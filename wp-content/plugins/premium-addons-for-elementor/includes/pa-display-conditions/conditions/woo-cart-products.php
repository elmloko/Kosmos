<?php
/**
 * Woocommerce Products in Cart Condition Handler.
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
 * Class Woo_Cart_Products.
 */
class Woo_Cart_Products extends Condition {

	/**
	 * Get Value Controls Options.
	 *
	 * @access public
	 * @since 4.9.4
	 *
	 * @return array  controls options.
	 */
	public function get_control_options() {
		$products = Premium_Template_Tags::get_default_posts_list( 'product' );

		return array(
			'label'       => __( 'Value', 'premium-addons-for-elementor' ),
			'type'        => Controls_Manager::SELECT2,
			'default'     => array(),
			'label_block' => true,
			'options'     => $products,
			'multiple'    => true,
			'condition'   => array(
				'pa_condition_key' => 'woo_cart_products',
			),
		);

	}

	/**
	 * Compare Condition Value.
	 *
	 * @access public
	 * @since 4.9.4
	 *
	 * @param array       $settings      element settings.
	 * @param string      $operator      condition operator.
	 * @param string      $value         condition value.
	 * @param string      $compare_val   compare value.
	 * @param string|bool $tz            time zone.
	 *
	 * @return bool|void
	 */
	public function compare_value( $settings, $operator, $compare_val, $value, $tz ) {

		$cart = WC()->cart;

		$products_ids = array();

		if ( $cart->is_empty() ) {
			return false;
		}

		foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {

			$product = $cart_item['data'];

			if ( $product->is_type( 'variation' ) ) {
				$product = wc_get_product( $product->get_parent_id() );
			}

			array_push( $products_ids, $product->get_id() );
		}

		$condition_result = ! empty( array_intersect( (array) $compare_val, $products_ids ) ) ? true : false;

		return Helper_Functions::get_final_result( $condition_result, $operator );
	}

}
