<?php
/**
 * PA WPML Icon List.
 */

namespace PremiumAddons\Compatibility\WPML\Widgets;

use WPML_Elementor_Module_With_Items;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No access of directly access.
}

/**
 * Icon List.
 *
 * Registers translatable widget with items.
 *
 * @since 3.1.9
 */
class Icon_List extends WPML_Elementor_Module_With_Items {

	/**
	 * Retrieve the field name.
	 *
	 * @since 3.1.9
	 * @return string
	 */
	public function get_items_field() {
		return 'list';
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
			'list_title',
			'link_title',
			'badge_title',
			'list_text_icon',
			'link' => array( 'url' ),
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
			case 'list_title':
				return esc_html__( 'List: Item Title', 'premium-addons-for-elementor' );

			case 'list_text_icon':
				return esc_html__( 'List: Item Icon', 'premium-addons-for-elementor' );

			case 'url':
				return esc_html__( 'List: Item Link', 'premium-addons-for-elementor' );

			case 'link_title':
				return esc_html__( 'List: Item Link Title', 'premium-addons-for-elementor' );

			case 'badge_title':
				return esc_html__( 'List: Item Badge', 'premium-addons-for-elementor' );

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

		switch ( $field ) {
			case 'list_title':
			case 'link_title':
			case 'badge_title':
			case 'list_text_icon':
				return 'LINE';

			case 'url':
				return 'LINK';

			default:
				return '';
		}
	}

}
