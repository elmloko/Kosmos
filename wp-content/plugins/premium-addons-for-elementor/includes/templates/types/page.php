<?php

namespace PremiumAddons\Includes\Templates\Types;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Premium_Structure_Page' ) ) {

	/**
	 * Define Premium_Structure_Page class
	 */
	class Premium_Structure_Page extends Premium_Structure_Base {

		public function get_id() {
			return 'premium_page';
		}

		public function get_single_label() {
			return __( 'Page', 'premium-addons-for-elementor' );
		}

		public function get_plural_label() {
			return __( 'Pages', 'premium-addons-for-elementor' );
		}

		public function get_sources() {
			return array( 'premium-api' );
		}

		public function get_document_type() {
			return array(
				'class' => 'Premium_Page_Document',
				'file'  => PREMIUM_ADDONS_PATH . 'includes/templates/documents/page.php',
			);
		}

		/**
		 * Library settings for current structure
		 *
		 * @return void
		 */
		public function library_settings() {

			return array(
				'show_title'    => false,
				'show_keywords' => false,
			);

		}

	}

}
