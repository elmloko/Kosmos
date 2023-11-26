<?php
/**
 * Premium Pinterest Feed Handler.
 */

use PremiumAddons\Includes\Helper_Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PINTEREST_API_URL', 'https://api.pinterest.com/v5/' );


/**
 * Get Pinterset Data
 *
 * @param string $id         widget id.
 * @param array  $settings    widget settings.
 */
function get_pinterest_data( $id, $settings, $endpoint ) {

	$original_endpoint = $endpoint;

	$filter_id = $settings['match_id'];

	$token = $settings['access_token'];

	$transient_name = sprintf( 'papro_pinterest_feed_%s_%s', $id, substr( $token, -8 ) );

	$response = get_transient( $transient_name );

	$is_edit_mode = \Elementor\Plugin::$instance->editor->is_edit_mode();

	if ( $is_edit_mode || false === $response ) {

		$limit = $settings['no_of_posts'];

		if ( 'pins/' === $endpoint && empty( $filter_id ) && 1 === count( $settings['board_id'] ) ) {

			$board_id = $settings['board_id'][0];
			$endpoint = 'boards/' . $board_id . '/' . $endpoint;

		}

		if ( 'boards/' === $endpoint && 1 === count( $settings['board_id'] ) ) {
			$endpoint .= $settings['board_id'][0];
		}

		$url = PINTEREST_API_URL . $endpoint . $filter_id;

		if ( empty( $filter_id ) && ! empty( $limit ) ) {
			$url .= '?page_size=' . $limit;
		}

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
			return;
		}

		$response = wp_remote_retrieve_body( $response );

		$response = json_decode( $response, true );

		$transient = $settings['reload'];

		$expire_time = Helper_Functions::transient_expire( $transient );

		set_transient( $transient_name, $response, $expire_time );
	}

	if ( 'pins/' === $original_endpoint ) {
		$items = ! empty( $filter_id ) ? array( $response ) : $response['items'];
	} else {
		$items = 1 === count( $settings['board_id'] ) ? array( $response ) : $response['items'];
	}

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

function get_profile_data( $id, $settings ) {

	$token = $settings['access_token'];

	$transient_name = sprintf( 'papro_pinterest_profile_%s_%s', $id, substr( $token, -8 ) );

	$response = get_transient( $transient_name );

	if ( false === $response ) {

		$url = PINTEREST_API_URL . 'user_account';

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
			return;
		}

		$response = wp_remote_retrieve_body( $response );

		$response = json_decode( $response, true );

		$transient = $settings['reload'];

		$expire_time = Helper_Functions::transient_expire( $transient );

		set_transient( $transient_name, $response, $expire_time );
	}

	return $response;
}

function get_board_pins( $widget_id, $settings, $board_id ) {

	$token = $settings['access_token'];

	$transient_name = sprintf( 'papro_pinterest_board_%s_%s', $board_id, substr( $token, -8 ) );

	$response = get_transient( $transient_name );

	$is_edit_mode = \Elementor\Plugin::$instance->editor->is_edit_mode();

	if ( $is_edit_mode || false === $response ) {

        $limit = $settings['pins_per_board'];

		$url = PINTEREST_API_URL . 'boards/' . $board_id . '/pins';

        if ( ! empty( $limit ) ) {
			$url .= '?page_size=' . $limit;
		}

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
			return;
		}

		$response = wp_remote_retrieve_body( $response );

		$response = json_decode( $response, true );

		$transient = $settings['reload'];

		$expire_time = Helper_Functions::transient_expire( $transient );

		set_transient( $transient_name, $response, $expire_time );
	}

    $detect = new \PA_Mobile_Detect();

    if ( $detect->isTablet() && ! empty( $settings['pins_per_board_tablet'] ) ) {
        $items = array_slice( $items, 0, $settings['pins_per_board_tablet'] );
    } elseif ( $detect->isMobile() && ! empty( $settings['pins_per_board_mobile'] ) ) {
        $items = array_slice( $items, 0, $settings['pins_per_board_mobile'] );
    }

	return $response['items'];
}

