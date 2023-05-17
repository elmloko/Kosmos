<?php
/**
 * Page Condition Handler.
 */

namespace PremiumAddons\Includes\PA_Display_Conditions\Conditions;

// Elementor Classes.
use Elementor\Controls_Manager;

// PA Classes.
use PremiumAddons\Includes\Helper_Functions;
use PremiumAddons\Includes\Premium_Template_Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Page
 */
class Page extends Condition {

	/**
	 * Get Controls Options.
	 *
	 * @access public
	 * @since 4.7.0
	 *
	 * @return array|void  controls options
	 */
	public function get_control_options() {
		$posts = Premium_Template_Tags::get_default_posts_list( 'page' );

		return array(
			'label'       => __( 'Value', 'premium-addons-for-elementor' ),
			'type'        => Controls_Manager::SELECT2,
			'default'     => array(),
			'label_block' => true,
			'options'     => $posts,
			'multiple'    => true,
			'condition'   => array(
				'pa_condition_key' => 'page',
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

		$current_id = get_the_ID();

		if ( is_array( $value ) && ! empty( $value ) ) {
			foreach ( $value as $index => $page_id ) {

				if ( intval( $page_id ) === $current_id ) {

					if ( 'is' === $operator ) {

						return Helper_Functions::get_final_result( true, $operator );
					}
				}
			}
		}

		return false;

	}

}
