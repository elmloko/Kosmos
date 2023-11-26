<?php
/**
 * Premium TikTok Feed Handler.
 */

use PremiumAddons\Includes\Helper_Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TIKTOK_API_URL', 'https://open.tiktokapis.com/v2/' );

/**
 * Get TikTok Data
 *
 * @param string $id         widget id.
 * @param array  $settings    widget settings.
 */
function get_tiktok_data( $id, $settings ) {

	$token = $settings['access_token'];

	$transient_name = sprintf( 'papro_tiktok_feed_%s_%s', $id, substr( $token, -8 ) );

	$response = get_transient( $transient_name );

	$is_edit_mode = \Elementor\Plugin::$instance->editor->is_edit_mode();

	if ( $is_edit_mode || false === $response ) {

		$filter_id = $settings['match_id'];

		$fields = '?fields=id,create_time,cover_image_url,share_url,video_description,duration,height,width,title,embed_html,embed_link,like_count,comment_count,share_count,view_count';

		$args = array(
			'headers' => array(
				'Authorization' => 'Bearer ' . $token,
				'Content-Type'  => 'application/json',
			),
		);

		if ( ! empty( $filter_id ) ) {

			$filters  = explode( ',', $filter_id );
			$endpoint = 'video/query/';

			$filters = array(
				'filters' => array(
					'video_ids' => $filters,
				),
			);

			$args['body'] = json_encode( $filters );

		} else {
			$endpoint = 'video/list/';
			$limit    = $settings['no_of_posts'];

			if ( ! empty( $limit ) ) {
				$limit = array(
					'max_count' => $limit,
				);

				$args['body'] = json_encode( $limit );
			}
		}

		$url = TIKTOK_API_URL . $endpoint . $fields;

		sleep( 2 );

		$response = wp_remote_post(
			$url,
			$args
		);

		if ( is_wp_error( $response ) ) {
			return;
		}

		$response = wp_remote_retrieve_body( $response );

		$response = json_decode( $response, true );

		if ( 'ok' !== $response['error']['code'] ) {
			?>
			<div class="premium-error-notice">
				<?php echo esc_html( __( 'Something went wrong: Code ', 'premium-addons-pro' ) ) . $response['error']['code'] . ' => ' . $response['error']['message']; ?>
			</div>
			<?php
			return;
		}

		$transient = $settings['reload'];

		$expire_time = Helper_Functions::transient_expire( $transient );

		set_transient( $transient_name, $response, $expire_time );
	}

	$items = $response['data']['videos'];

	if ( empty( $filter_id ) ) {

		$detect = new \PA_Mobile_Detect();

		if ( $detect->isTablet() && ! empty( $settings['no_of_posts_tablet'] ) ) {
			$items = array_slice( $items, 0, $settings['no_of_posts_tablet'] );
		} elseif ( $detect->isMobile() && ! empty( $settings['no_of_posts_mobile'] ) ) {
			$items = array_slice( $items, 0, $settings['no_of_posts_mobile'] );
		}
	}

	return $items;
}

/**
 * Get Profile Data.
 *
 * @param string $id         widget id.
 * @param array  $settings    widget settings.
 */
function get_tiktok_profile_data( $id, $settings ) {

	$token = $settings['access_token'];

	$transient_name = sprintf( 'papro_tiktok_profile_%s_%s', $id, substr( $token, -8 ) );

	$response = get_transient( $transient_name );

	if ( false === $response ) {

		$url = TIKTOK_API_URL . 'user/info/?fields=avatar_url,display_name,bio_description,profile_deep_link,is_verified,follower_count,following_count,likes_count';

		sleep( 2 );

		$response = wp_remote_get(
			$url,
			array(
				'headers' => array(
					'Authorization' => 'Bearer ' . $token,
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return 'error';
		}

		$response = wp_remote_retrieve_body( $response );

		$response = json_decode( $response, true );

		if ( 'ok' !== $response['error']['code'] ) {
			?>
			<div class="premium-error-notice">
				<?php echo esc_html( __( 'Something went wrong: Code ', 'premium-addons-pro' ) ) . $response['error']['code'] . ' => ' . $response['error']['message']; ?>
			</div>
			<?php
			return 'error';
		}

		$transient = $settings['reload'];

		$expire_time = Helper_Functions::transient_expire( $transient );

		set_transient( $transient_name, $response, $expire_time );
	}

	return $response['data']['user'];
}

/**
 * Refresh TikTok Token.
 *
 * @param string $token old token.
 */
function refresh_tiktok_token( $token ) {

	$api_url = 'https://appfb.premiumaddons.com/wp-json/fbapp/v2/reftiktok/';

	$response = wp_remote_get(
		$api_url . $token,
		array(
			'timeout'   => 60,
			'sslverify' => true,
		)
	);

	$response = wp_remote_retrieve_body( $response );

	$response = json_decode( $response, true );

	set_transient( 'pa_tiktok_token_' . $token, $response, 23 * HOUR_IN_SECONDS );

	return $response;
}


/**
 * Refresh TikTok Token.
 *
 * @param string $token old token.
 */
function get_tiktok_videos_urls( $settings ) {

	$token = $settings['access_token'];

	$transient_name = sprintf( 'papro_tiktok_urls_%s', substr( $token, -8 ) );

	$response = get_transient( $transient_name );

	// $is_edit_mode = \Elementor\Plugin::$instance->editor->is_edit_mode();

	if ( false === $response ) {

		$api_url = 'https://appfb.premiumaddons.com/wp-json/fbapp/v2/tiktokvideos/';

		$response = wp_remote_get(
			$api_url . $token,
			array(
				'timeout'   => 60,
				'sslverify' => true,
			)
		);

		$response = wp_remote_retrieve_body( $response );

		$response = json_decode( $response, true );

		$transient = $settings['reload'];

		set_transient( $transient_name, $response, 4 * HOUR_IN_SECONDS );

	}

	return $response;
}
