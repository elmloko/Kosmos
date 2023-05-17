<?php
/**
 * PA WPML Premium Person.
 */

namespace PremiumAddons\Compatibility\WPML\Widgets;

use WPML_Elementor_Module_With_Items;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No access of directly access.
}

/**
 * Person
 *
 * Registers translatable widget with items.
 *
 * @since 3.1.9
 */
class Person extends WPML_Elementor_Module_With_Items {

	/**
	 * Retrieve the field name.
	 *
	 * @since 3.1.9
	 * @return string
	 */
	public function get_items_field() {
		return 'multiple_persons';
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
			'multiple_name',
			'multiple_title',
			'multiple_description',
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
			case 'multiple_name':
				return esc_html__( 'Multiple Persons: Person Name', 'premium-addons-for-elementor' );

			case 'multiple_title':
				return esc_html__( 'Multiple Persons: Person Title', 'premium-addons-for-elementor' );

			case 'multiple_description':
				return esc_html__( 'Multiple Persons: Person Description', 'premium-addons-for-elementor' );

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
			case 'multiple_name':
			case 'multiple_title':
				return 'LINE';

			case 'multiple_description':
				return 'AREA';

			default:
				return '';
		}
	}

}
