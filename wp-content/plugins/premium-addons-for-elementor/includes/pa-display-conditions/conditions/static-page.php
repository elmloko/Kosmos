<?php
/**
 * Returning User Condition Handler.
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
 * Class Static_Page
 */
class Static_Page extends Condition {

	/**
	 * Get Controls Options.
	 *
	 * @access public
	 * @since 4.9.22
	 *
	 * @return array|void  controls options
	 */
	public function get_control_options() {

		return array(
			'type'        => Controls_Manager::SELECT,
			'options'     => array(
				'home'   => __( 'Homepage', 'premium-addons-for-elementor' ),
				'static' => __( 'Front Page', 'premium-addons-for-elementor' ),
				'blog'   => __( 'Blog', 'premium-addons-for-elementor' ),
				'404'    => __( '404 Page', 'premium-addons-for-elementor' ),
			),
			'default'     => 'home',
			'label_block' => true,
			'condition'   => array(
				'pa_condition_key' => 'static_page',
			),
		);
	}

	/**
	 * Compare Condition Value.
	 *
	 * @access public
	 * @since 4.9.22
	 *
	 * @param array       $settings      element settings.
	 * @param string      $operator      condition operator.
	 * @param string      $value         condition value.
	 * @param string      $compare_val   condition value.
	 * @param string|bool $tz        time zone.

	 * @return bool|void
	 */
	public function compare_value( $settings, $operator, $value, $compare_val, $tz ) {

		switch ( $value ) {
			case 'home':
				$condition_result = is_front_page() && is_home();
				break;

			case 'static':
				$condition_result = is_front_page() && ! is_home();
				break;

			case 'static':
				$condition_result = ! is_front_page() && is_home();
				break;

			default:
				$condition_result = is_404();
				break;
		}

		return Helper_Functions::get_final_result( $condition_result, $operator );
	}

}
