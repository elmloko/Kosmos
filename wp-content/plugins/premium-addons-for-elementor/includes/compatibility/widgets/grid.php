<?php
/**
 * PA WPML Media Grid.
 */

namespace PremiumAddons\Compatibility\WPML\Widgets;

use WPML_Elementor_Module_With_Items;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No access of directly access.
}

/**
 * Grid
 *
 * Registers translatable widget with items.
 *
 * @since 3.1.9
 */
class Grid extends WPML_Elementor_Module_With_Items {

	/**
	 * Retrieve the field name.
	 *
	 * @since 3.1.9
	 * @return string
	 */
	public function get_items_field() {
		return 'premium_gallery_img_content';
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
			'premium_gallery_img_name',
			'premium_gallery_img_desc',
			'premium_gallery_video_url',
			'premium_gallery_video_self_url',
			'premium_gallery_img_link' => array( 'url' ),
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
			case 'premium_gallery_img_name':
				return esc_html__( 'Grid: Image Name', 'premium-addons-for-elementor' );

			case 'premium_gallery_img_desc':
				return esc_html__( 'Grid: Image Description', 'premium-addons-for-elementor' );

			case 'url':
				return esc_html__( 'Grid: Image Link', 'premium-addons-for-elementor' );

			case 'premium_gallery_video_url':
			case 'premium_gallery_video_self_url':
				return esc_html__( 'Grid: Video URL', 'premium-addons-for-elementor' );

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
			case 'premium_gallery_img_name':
			case 'premium_gallery_video_url':
			case 'premium_gallery_video_self_url':
				return 'LINE';

			case 'premium_gallery_img_desc':
				return 'AREA';

			case 'url':
				return 'LINK';

			default:
				return '';
		}
	}

}
