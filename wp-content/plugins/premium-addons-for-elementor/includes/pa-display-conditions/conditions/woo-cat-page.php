<?php
/**
 * Woocommerce Category Page Condition Handler.
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
 * Class Woo_Cat_Page.
 */
class Woo_Cat_Page extends Condition {

	/**
	 * Get Controls Options.
	 *
	 * @access public
	 * @since 4.7.2
	 *
	 * @return array|void  controls options
	 */
	public function get_control_options() {

		return array(
			'label'       => __( 'Value', 'premium-addons-for-elementor' ),
			'type'        => Controls_Manager::SELECT2,
			'default'     => array(),
			'label_block' => true,
			'options'     => Helper_Functions::get_woo_categories( 'id' ),
			'multiple'    => true,
			'condition'   => array(
				'pa_condition_key' => 'woo_cat_page',
			),
		);
	}

	/**
	 * Compare Condition Value.
	 *
	 * @access public
	 * @since 4.7.2
	 *
	 * @param array       $settings      element settings.
	 * @param string      $operator      condition operator.
	 * @param string      $value         condition value.
	 * @param string      $compare_val   compare value.
	 * @param string|bool $tz            time zone.
	 *
	 * @return bool|void
	 */
	public function compare_value( $settings, $operator, $value, $compare_val, $tz ) {

		$category = get_queried_object();

		$taxonomy = ! isset( $category->taxonomy ) || ! isset( $category->term_id ) || 'product_cat' !== $category->taxonomy;

		if ( $taxonomy ) {
			return true;
		}

		$cat_id = $category->term_id;

		$condition_result = false !== array_search( $cat_id, $value ) ? true : false;

		return Helper_Functions::get_final_result( $condition_result, $operator );
	}

}
