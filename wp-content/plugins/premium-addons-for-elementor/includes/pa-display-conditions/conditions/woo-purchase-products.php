<?php
/**
 * Woocommerce Purchase Products Condition Handler.
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
 * Class Woo_Purchase_Products.
 */
class Woo_Purchase_Products extends Condition {

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
				'pa_condition_key' => 'woo_purchase_products',
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

		$order_item_id = array();

		$args = array(
			'numberposts' => -1,
			'meta_key'    => '_customer_user',
			'meta_value'  => get_current_user_id(),
			'post_type'   => wc_get_order_types(),
			'post_status' => array_keys( wc_get_is_paid_statuses() ),
		);
		
		$order_item_id = array();

		$orders = wc_get_orders( $args );

		foreach ( $orders as $order ) {

			$order = wc_get_order( $order->ID );

			$order_items = $order->get_items();

			foreach ( $order_items as $order_item ) {
				
				$product_id = $order_item->get_product_id();

				$order_item_id[] = $product_id;
			}

		}

		$condition_result = ! empty( array_intersect( (array) $compare_val, $order_item_id ) ) ? true : false;
		
		return Helper_Functions::get_final_result( $condition_result, $operator );
	}

}