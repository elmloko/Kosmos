<?php
/**
 * Woocommerce category Condition Handler.
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
 * Class Woo_Category.
 */
class Woo_Category extends Condition {

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
			'default'     => array(),
			'label_block' => true,
			'options'     => Helper_Functions::get_woo_categories( 'id' ),
			'multiple'    => true,
			'condition'   => array(
				'pa_condition_key' => 'woo_category',
			),
		);
	}

	/**
	 * Get Value Controls Options.
	 *
	 * @access public
	 * @since 4.7.0
	 *
	 * @return array  controls options.
	 */
	public function add_value_control() {

		return array(
			'label'       => __( 'Status', 'premium-addons-for-elementor' ),
			'type'        => Controls_Manager::SELECT,
			'default'     => 'in-cart',
			'label_block' => true,
			'options'     => array(
				'in-cart'   => __( 'In Cart', 'premium-addons-for-elementor' ),
				'purchased' => __( 'Purchased', 'premium-addons-for-elementor' ),
			),
			'condition'   => array(
				'pa_condition_key' => 'woo_category',
			),
		);

	}

	/**
	 * Compare Condition Value.
	 *
	 * @access public
	 * @since 4.7.0
	 *
	 * @param array       $settings      element settings.
	 * @param string      $operator      condition operator.
	 * @param string      $compare_val   compare value.
	 * @param string      $value         condition value.
	 * @param string|bool $tz            time zone.
	 *
	 * @return bool|void
	 */
	public function compare_value( $settings, $operator, $compare_val, $value, $tz ) {

		$cart = WC()->cart;

		$product_cats = array();

		if ( 'in-cart' === $value ) {

			if ( $cart->is_empty() ) {
				return false;
			}

			foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {

				$product = $cart_item['data'];

				if ( $product->is_type( 'variation' ) ) {
					$product = wc_get_product( $product->get_parent_id() );
				}

				$product_cats = array_merge( $product_cats, $product->get_category_ids() );
			}
		} else {

			$args = array(
				'numberposts' => -1,
				'meta_key'    => '_customer_user',
				'meta_value'  => get_current_user_id(),
				'post_type'   => wc_get_order_types(),
				'post_status' => array_keys( wc_get_is_paid_statuses() ),
			);

			$customer_orders = get_posts( $args );

			$product_ids = array();

			foreach ( $customer_orders as $order ) {

				$order = wc_get_order( $order->ID );
				$items = $order->get_items();
				foreach ( $items as $item ) {
					$product_id    = $item->get_product_id();
					$product_ids[] = $product_id;
				}
			}

			foreach ( $product_ids as $id ) {
				$product = wc_get_product( $id );

				if ( $product->is_type( 'variation' ) ) {
					$product = wc_get_product( $product->get_parent_id() );
				}

				$product_cats = array_merge( $product_cats, $product->get_category_ids() );
			}
		}

		$condition_result = ! empty( array_intersect( (array) $compare_val, $product_cats ) ) ? true : false;

		return Helper_Functions::get_final_result( $condition_result, $operator );
	}

}
