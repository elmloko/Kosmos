<?php

use PremiumAddons\Includes\Helper_Functions;

// Premium Addons Pro Classes
use PremiumAddonsPro\Includes\White_Label\Helper;

?>

<table class="widefat" cellspacing="0">
	<thead>
		<tr>
			<th colspan="2"><b><?php esc_html_e( 'WordPress Environment', 'premium-addons-for-elementor' ); ?></b></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><?php esc_html_e( 'Home URL', 'premium-addons-for-elementor' ); ?>:</td>
			<td><?php form_option( 'home' ); ?></td>
		</tr>
		<tr>
			<td><?php esc_html_e( 'Site URL', 'premium-addons-for-elementor' ); ?>:</td>
			<td><?php form_option( 'siteurl' ); ?></td>
		</tr>
		<tr>
			<td><?php esc_html_e( 'WP Version', 'premium-addons-for-elementor' ); ?>:</td>
			<td><?php bloginfo( 'version' ); ?></td>
		</tr>
		<tr>
			<td><?php esc_html_e( 'WP Multisite', 'premium-addons-for-elementor' ); ?>:</td>
			<td>
			<?php
			if ( is_multisite() ) {
				echo '&#10004;';
			} else {
				echo '&ndash;';
			}
			?>
				</td>
		</tr>
		<tr>
			<td><?php esc_html_e( 'WP Memory Limit', 'premium-addons-for-elementor' ); ?>:</td>
			<td>
			<?php
			 // echo "kok";
				$memory_limit = wp_convert_hr_to_bytes( WP_MEMORY_LIMIT );
			if ( $memory_limit < 67108864 ) {
				echo '<mark>' . sprintf( __( '%1$s - We recommend setting wp memory at least 64MB.</mark> See: <a href="%2$s" target="_blank">Increasing WP Memory Limit</a>', 'premium-addons-for-elementor' ), esc_html( size_format( $memory_limit ) ), 'https://premiumaddons.com/docs/im-getting-a-blank-page-on-elementor-after-activating-premium-add-ons/' );
			} else {
				echo esc_html( size_format( $memory_limit ) );
			}
			?>
				</td>
		</tr>
		<tr>
			<td><?php esc_html_e( 'WP Path', 'premium-addons-for-elementor' ); ?>:</td>
			<td><?php echo ABSPATH; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
		</tr>


		<tr>
			<td><?php esc_html_e( 'WP Debug Mode', 'premium-addons-for-elementor' ); ?>:</td>
			<td>
			<?php
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				echo '&#10004;';
			} else {
				echo '&ndash;';
			}
			?>
				</td>
		</tr>
		<tr>
			<td><?php esc_html_e( 'Language', 'premium-addons-for-elementor' ); ?>:</td>
			<td><?php echo esc_html( get_locale() ); ?></td>
		</tr>
	</tbody>
</table>
<br>
<table class="widefat" cellspacing="0">
	<thead>
		<tr>
			<th colspan="2" data-export-label="Server Environment"><b><?php esc_html_e( 'Server Environment', 'premium-addons-for-elementor' ); ?></b></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><?php esc_html_e( 'Server Info', 'premium-addons-for-elementor' ); ?>:</td>
			<td>
			<?php
					$server_sw = isset( $_SERVER['SERVER_SOFTWARE'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) ) : '';
					echo esc_html( $server_sw );
			?>
			</td>
		</tr>
		<tr>
			<td><?php esc_html_e( 'PHP Version', 'premium-addons-for-elementor' ); ?>:</td>
			<td>
			<?php
				// Check if phpversion function exists
			if ( function_exists( 'phpversion' ) ) {
				$php_version = phpversion();

				echo esc_html( $php_version );
			} else {
				esc_html_e( "Couldn't determine PHP version because phpversion() doesn't exist.", 'premium-addons-for-elementor' );
			}
			?>
				</td>
		</tr>
		<?php if ( function_exists( 'ini_get' ) ) : ?>
			<tr>
				<td><?php esc_html_e( 'PHP Memory Limit', 'premium-addons-for-elementor' ); ?>:</td>
				<td><?php echo esc_html( size_format( wp_convert_hr_to_bytes( ini_get( 'memory_limit' ) ) ) ); ?></td>
			</tr>
			<tr>
				<td><?php esc_html_e( 'PHP Post Max Size', 'premium-addons-for-elementor' ); ?>:</td>
				<td><?php echo esc_html( size_format( wp_convert_hr_to_bytes( ini_get( 'post_max_size' ) ) ) ); ?></td>
			</tr>
			<tr>
				<td ><?php esc_html_e( 'PHP Time Limit', 'premium-addons-for-elementor' ); ?>:</td>
				<td>
				<?php
				$time_limit = ini_get( 'max_execution_time' );
				if ( $time_limit < 120 && $time_limit != 0 ) {
					echo '<mark>' . sprintf( __( '%s - We recommend setting max execution time at least 300.</mark> See: <a href="%2$s" target="_blank">Increasing WP Time Limit</a>', 'premium-addons-for-elementor' ), esc_html( $time_limit ), 'https://premiumaddons.com/docs/im-getting-a-blank-page-on-elementor-after-activating-premium-add-ons/' );
				} else {
					echo esc_html( $time_limit );
				}
				?>
				</td>
			</tr>
			<tr>
				<td><?php esc_html_e( 'PHP Max Input Vars', 'premium-addons-for-elementor' ); ?>:</td>
				<td><?php echo esc_html( ini_get( 'max_input_vars' ) ); ?></td>
			</tr>
			<tr>
				<td ><?php esc_html_e( 'SUHOSIN Installed', 'premium-addons-for-elementor' ); ?>:</td>
				<td><?php echo extension_loaded( 'suhosin' ) ? '&#10004;' : '&ndash;'; ?></td>
			</tr>
		<?php endif; ?>
		<tr>
			<td><?php esc_html_e( 'MySQL Version', 'premium-addons-for-elementor' ); ?>:</td>
			<td>
				<?php
				/** @global wpdb $wpdb */
				global $wpdb;
				echo esc_html( $wpdb->db_version() );
				?>
			</td>
		</tr>
		<tr>
			<td><?php esc_html_e( 'Max Upload Size', 'premium-addons-for-elementor' ); ?>:</td>
		<td><?php echo esc_html( size_format( wp_max_upload_size() ) ); ?></td>
		</tr>
		</tbody>
</table>
<br>
<table class="widefat" cellspacing="0">
	<thead>
		<tr>
			<th colspan="2" data-export-label="PHP Extensions"><b><?php esc_html_e( 'PHP Extensions', 'premium-addons-for-elementor' ); ?></b></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><?php esc_html_e( 'cURL', 'premium-addons-for-elementor' ); ?>:</td>
			<td><?php echo ( function_exists( 'curl_init' ) ? 'Supported' : 'Not Supported' ); ?></td>
		</tr>
		<tr>
			<td><?php esc_html_e( 'fsockopen', 'premium-addons-for-elementor' ); ?>:</td>
			<td><?php echo ( function_exists( 'fsockopen' ) ? 'Supported' : 'Not Supported' ); ?></td>
		</tr>
		<tr>
			<td><?php esc_html_e( 'SOAP Client', 'premium-addons-for-elementor' ); ?>:</td>
			<td><?php echo ( class_exists( 'SoapClient' ) ? 'Installed' : 'Not Installed' ); ?></td>
		</tr>
		<tr>
			<td><?php esc_html_e( 'Suhosin', 'premium-addons-for-elementor' ); ?>:</td>
			<td><?php echo ( extension_loaded( 'suhosin' ) ? 'Installed' : 'Not Installed' ); ?></td>
		</tr>
	</tbody>
</table>
<br>
<table class="widefat" cellspacing="0">
	<thead>
		<tr>
			<th colspan="2"><b><?php esc_html_e( 'Active Plugins', 'premium-addons-for-elementor' ); ?> (<?php echo count( (array) get_option( 'active_plugins' ) ); ?>)</b></th>
		</tr>
	</thead>
	<tbody>
		<?php

		$active_plugins = (array) get_option( 'active_plugins', array() );

		if ( is_multisite() ) {
			$network_activated_plugins = array_keys( get_site_option( 'active_sitewide_plugins', array() ) );
			$active_plugins            = array_merge( $active_plugins, $network_activated_plugins );
		}

		foreach ( $active_plugins as $plugin ) {

			$plugin_data    = @get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );
			$dirname        = dirname( $plugin );
			$version_string = '';
			$network_string = '';

			if ( ! empty( $plugin_data['Name'] ) ) {

				// link the plugin name to the plugin url if available
				$plugin_name = esc_html( $plugin_data['Name'] );

				if ( 'Premium Addons for Elementor' === $plugin_name ) {
					$plugin_name = Helper_Functions::name();
					$author      = Helper_Functions::author();
					if ( 'Leap13' !== $author ) {
						$plugin_data['Author'] = Helper_Functions::author();
					}
				} elseif ( 'Premium Addons PRO' === $plugin_name ) {
					$plugin_name = Helper::name_pro();
					$author      = Helper::author_pro();
					if ( 'Leap13' !== $author ) {
						$plugin_data['Author'] = Helper::author_pro();
					}
				}

				if ( ! empty( $plugin_data['PluginURI'] ) ) {
					$plugin_name = '<a href="' . esc_url( $plugin_data['PluginURI'] ) . '" title="' . esc_attr__( 'Visit plugin homepage', 'premium-addons-for-elementor' ) . '" target="_blank">' . $plugin_name . '</a>';
				}
				?>
				<tr>
					<td><?php echo wp_kses_post( $plugin_name ); ?></td>
					<td><?php echo sprintf( esc_html_x( 'by %s', 'by author', 'premium-addons-for-elementor' ), wp_kses_post( $plugin_data['Author'] ) ) . ' &ndash; ' . esc_html( $plugin_data['Version'] ) . wp_kses_post( $version_string ) . wp_kses_post( $network_string ); ?></td>
				</tr>
				<?php
			}
		}
		?>
	</tbody>
</table>
