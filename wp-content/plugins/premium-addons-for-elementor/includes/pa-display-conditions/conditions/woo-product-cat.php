<?php
/**
 * Woocommerce Product category Condition Handler.
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
 * Class Woo_Product_Cat.
 */
class Woo_Product_Cat extends Condition {

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
				'pa_condition_key' => 'woo_product_cat',
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

		if ( class_exists( 'Hide_Categories_Products_WC' ) ) {
			global $Hide_Categories_Products_WC;
			remove_filter( 'get_the_terms', array( $Hide_Categories_Products_WC, 'hide_hidden_categories_single_product' ), 11 );
		}

		$product_id = get_queried_object_id();

		if ( ! $product_id ) {
			return true;
		}

		$type = get_post_type();

		if ( 'product' !== $type ) {
			return true;
		}

		$product = wc_get_product( $product_id );

		$product_cats = $product->get_category_ids();

		$condition_result = ! empty( array_intersect( (array) $value, $product_cats ) ) ? true : false;

		return Helper_Functions::get_final_result( $condition_result, $operator );
	}

}
