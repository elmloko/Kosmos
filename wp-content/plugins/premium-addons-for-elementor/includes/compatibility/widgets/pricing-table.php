<?php
/**
 * PA WPML Pricing Table.
 */

namespace PremiumAddons\Compatibility\WPML\Widgets;

use WPML_Elementor_Module_With_Items;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No access of directly access.
}

/**
 * Fancy Text
 *
 * Registers translatable widget with items.
 *
 * @since 3.1.9
 */
class Pricing_Table extends WPML_Elementor_Module_With_Items {

	/**
	 * Retrieve the field name.
	 *
	 * @since 3.1.9
	 * @return string
	 */
	public function get_items_field() {
		return 'premium_fancy_text_list_items';
	}

	/**
	 * Retrieve the fields inside the repeater.
	 *
	 * @since 3.1.9
	 *
	 * @return array
	 */
	public function get_fields() {
		return array(
			'premium_pricing_list_item_text',
			'premium_pricing_table_item_tooltip_text',
		);
	}

	/**
	 * Get the title for each repeater string
	 *
	 * @since 3.1.9
	 *
	 * @param string $field control ID.
	 *
	 * @return string
	 */
	protected function get_title( $field ) {

		switch ( $field ) {
			case 'premium_pricing_list_item_text':
				return esc_html__( 'Pricing Table: Item Text', 'premium-addons-for-elementor' );

			case 'premium_pricing_table_item_tooltip_text':
				return esc_html__( 'Pricing Table: Tooltip Text', 'premium-addons-for-elementor' );

			default:
				return '';
		}

	}

	/**
	 * Get `editor_type` for each repeater string
	 *
	 * @since 3.1.9
	 *
	 * @param string $field control ID.
	 *
	 * @return string
	 */
	protected function get_editor_type( $field ) {

		return 'LINE';

	}

}
