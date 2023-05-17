<?php
/**
 * Operating System Condition Handler.
 */

namespace PremiumAddons\Includes\PA_Display_Conditions\Conditions;

// Elementor Classes.
use Elementor\Controls_Manager;

// PA Classes.
use PremiumAddons\Includes\Helper_Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Operating System
 */
class Operating_System extends Condition {

	/**
	 * Get Controls Options.
	 *
	 * @access public
	 * @since 4.7.0
	 *
	 * @return array|void  controls options
	 */
	public function get_control_options() {

		return array(
			'label'       => __( 'Value', 'premium-addons-for-elementor' ),
			'type'        => Controls_Manager::SELECT2,
			'default'     => array( 'windows' ),
			'label_block' => true,
			'options'     => array(
				'windows'    => __( 'Windows', 'premium-addons-for-elementor' ),
				'mac_os'     => __( 'Mac OS', 'premium-addons-for-elementor' ),
				'linux'      => __( 'Linux', 'premium-addons-for-elementor' ),
				'iphone'     => __( 'iPhone', 'premium-addons-for-elementor' ),
				'android'    => __( 'Android', 'premium-addons-for-elementor' ),
				'blackberry' => __( 'BlackBerry', 'premium-addons-for-elementor' ),
				'open_bsd'   => __( 'OpenBSD', 'premium-addons-for-elementor' ),
				'sun_os'     => __( 'SunOS', 'premium-addons-for-elementor' ),
				'qnx'        => __( 'QNX', 'premium-addons-for-elementor' ),
				'beos'       => __( 'BeOS', 'premium-addons-for-elementor' ),
				'os2'        => __( 'OS/2', 'premium-addons-for-elementor' ),
			),
			'multiple'    => true,
			'condition'   => array(
				'pa_condition_key' => 'operating_system',
			),
		);
	}

	/**
	 * Compare Condition Value.
	 *
	 * @access public
	 * @since 4.7.0
	 *
	 * @param array       $settings       element settings.
	 * @param string      $operator       condition operator.
	 * @param string      $value          condition value.
	 * @param string      $compare_val    compare value.
	 * @param string|bool $tz        time zone.
	 *
	 * @return bool|void
	 */
	public function compare_value( $settings, $operator, $value, $compare_val, $tz ) {

		$user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';

		$os_list = array(
			'windows'    => '(Win16)|(Windows 95)|(Win95)|(Windows_95)|(Windows 98)|(Win98)|(Windows NT 5.0)|(Windows 2000)|(Windows NT 5.1)|(Windows XP)|(Windows NT 5.2)|(Windows NT 6.0)|(Windows Vista)|(Windows NT 6.1)|(Windows 7)|(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)|(Windows ME)',
			'mac_os'     => '(Mac_PowerPC)|(Macintosh)|(mac os x)',
			'linux'      => '(Linux)|(X11)',
			'iphone'     => 'iPhone',
			'android'    => '(Android)',
			'blackberry' => 'BlackBerry',
			'open_bsd'   => 'OpenBSD',
			'sun_os'     => 'SunOS',
			'qnx'        => 'QNX',
			'beos'       => 'BeOS',
		);

		$current_os = array();

		foreach ( $os_list as $key => $key_val ) {

			$match = preg_match( '/' . $key_val . '/i', $user_agent );

			if ( $match ) {
				array_push( $current_os, $key );

				// We need to remove mac_os if iPhone is the current OS, and Linux if Android is the current OS
				if ( 'iphone' === $key ) {
					array_shift( $current_os );
				} elseif ( 'android' === $key ) {
					array_shift( $current_os );
				}
			}
		}

		$result = ! empty( array_intersect( $value, $current_os ) ) ? true : false;

		return Helper_Functions::get_final_result( $result, $operator );

	}

}
