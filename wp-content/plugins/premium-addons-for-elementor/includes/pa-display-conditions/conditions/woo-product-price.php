<?php
/**
 * Woocommerce Product Price Condition Handler.
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
 * Class Woo_Product_Price.
 */
class Woo_Product_Price extends Condition {

	/**
	 * Get Value Controls Options.
	 *
	 * @access public
	 * @since 4.7.2
	 *
	 * @return array  controls options.
	 */
	public function get_control_options() {
		return array(
			'label'       => __( 'Equal or Higher than', 'premium-addons-for-elementor' ),
			'type'        => Controls_Manager::NUMBER,
			'description' => __( 'Set the minimum price of the product to be checked.', 'premium-addons-for-elementor' ),
			'min'         => 0,
			'condition'   => array(
				'pa_condition_key' => 'woo_product_price',
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

		$product_id = get_queried_object_id();

		if ( ! $product_id ) {
			return true;
		}

		$type = get_post_type();

		if ( 'product' !== $type ) {
			return true;
		}

		$product = wc_get_product( $product_id );

		$product_price = $product->get_price();

		$condition_result = (int) $value <= $product_price ? true : false;

		return Helper_Functions::get_final_result( $condition_result, $operator );
	}

}
