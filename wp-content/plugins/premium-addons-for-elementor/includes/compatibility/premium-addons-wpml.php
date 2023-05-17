<?php
/**
 * Premium Addons WPML.
 */

namespace PremiumAddons\Includes\Compatibility;

use PremiumAddons\Includes\Helper_Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No access of directly access.
}

if ( ! class_exists( 'Premium_Addons_Wpml' ) ) {

	/**
	 * Class Premium_Addons_Wpml.
	 */
	class Premium_Addons_Wpml {

		/**
		 * Class instance
		 *
		 * @var instance
		 */
		private static $instance = null;

		/**
		 * Constructor
		 */
		public function __construct() {

			if ( ! self::is_wpml_active() ) {
				return;
			}

			$this->includes();

			add_filter( 'wpml_elementor_widgets_to_translate', array( $this, 'translatable_widgets' ) );

		}


		/**
		 * Is WPML Active
		 *
		 * Check if WPML Multilingual CMS and WPML String Translation active
		 *
		 * @since 3.1.9
		 * @access private
		 *
		 * @return boolean is WPML String Translation
		 */
		public static function is_wpml_active() {

			$wpml = Helper_Functions::check_plugin_active( 'sitepress-multilingual-cms/sitepress.php' );

			$wpml_trans = Helper_Functions::check_plugin_active( 'wpml-string-translation/plugin.php' );

			return $wpml && $wpml_trans;

		}

		/**
		 *
		 * Includes
		 *
		 * Integrations class for widgets with complex controls.
		 *
		 * @since 3.1.9
		 */
		public function includes() {

			include_once 'widgets/carousel.php';
			include_once 'widgets/fancy-text.php';
			include_once 'widgets/grid.php';
			include_once 'widgets/maps.php';
			include_once 'widgets/pricing-table.php';
			include_once 'widgets/progress-bar.php';
			include_once 'widgets/vertical-scroll.php';
			include_once 'widgets/icon-list.php';
			include_once 'widgets/person.php';

		}

		/**
		 * Widgets to translate.
		 *
		 * @since 3.1.9
		 * @access public
		 *
		 * @param array $widgets Widget array.
		 *
		 * @return array
		 */
		public function translatable_widgets( $widgets ) {

			$widgets['premium-addon-banner'] = array(
				'conditions' => array( 'widgetType' => 'premium-addon-banner' ),
				'fields'     => array(
					array(
						'field'       => 'premium_banner_title',
						'type'        => __( 'Banner: Title', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_banner_description',
						'type'        => __( 'Banner: Description', 'premium-addons-for-elementor' ),
						'editor_type' => 'AREA',
					),
					array(
						'field'       => 'premium_banner_more_text',
						'type'        => __( 'Banner: Button Text', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
					'premium_banner_image_custom_link' => array(
						'field'       => 'url',
						'type'        => __( 'Banner: URL', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINK',
					),
					'premium_banner_link'              => array(
						'field'       => 'url',
						'type'        => __( 'Banner: Button URL', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINK',
					),
				),
			);

			$widgets['premium-addon-button'] = array(
				'conditions' => array( 'widgetType' => 'premium-addon-button' ),
				'fields'     => array(
					array(
						'field'       => 'premium_button_text',
						'type'        => __( 'Button: Text', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
					'premium_button_link' => array(
						'field'       => 'url',
						'type'        => __( 'Button: URL', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINK',
					),
				),
			);

			$widgets['premium-addon-blog'] = array(
				'conditions' => array( 'widgetType' => 'premium-addon-blog' ),
				'fields'     => array(
					array(
						'field'       => 'premium_blog_prev_text',
						'type'        => __( 'Blog: Previous Page String', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_blog_next_text',
						'type'        => __( 'Blog: Next Page String', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'empty_query_text',
						'type'        => __( 'Blog: Empty Query String', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),

				),
			);

			$widgets['premium-countdown-timer'] = array(
				'conditions' => array( 'widgetType' => 'premium-countdown-timer' ),
				'fields'     => array(
					array(
						'field'       => 'premium_countdown_expiry_text_',
						'type'        => __( 'Countdown: Expiration Message', 'premium-addons-for-elementor' ),
						'editor_type' => 'AREA',
					),
					array(
						'field'       => 'premium_countdown_separator_text',
						'type'        => __( 'Countdown: Digits Separator', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_countdown_day_singular',
						'type'        => __( 'Countdown: Day Singular', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_countdown_day_plural',
						'type'        => __( 'Countdown: Day Plural', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_countdown_week_singular',
						'type'        => __( 'Countdown: Week Singular', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_countdown_week_plural',
						'type'        => __( 'Countdown: Week Plural', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_countdown_month_singular',
						'type'        => __( 'Countdown: Month Singular', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_countdown_month_plural',
						'type'        => __( 'Countdown: Month Plural', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_countdown_year_singular',
						'type'        => __( 'Countdown: Year Singular', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_countdown_year_plural',
						'type'        => __( 'Countdown: Year Plural', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_countdown_hour_singular',
						'type'        => __( 'Countdown: Hour Singular', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_countdown_hour_plural',
						'type'        => __( 'Countdown: Hour Plural', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_countdown_minute_singular',
						'type'        => __( 'Countdown: Minute Singular', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_countdown_minute_plural',
						'type'        => __( 'Countdown: Minute Plural', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_countdown_second_singular',
						'type'        => __( 'Countdown: Second Singular', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_countdown_second_plural',
						'type'        => __( 'Countdown: Second Plural', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_countdown_expiry_redirection_',
						'type'        => __( 'Countdown: Direction URL', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
				),
			);

			$widgets['premium-counter'] = array(
				'conditions' => array( 'widgetType' => 'premium-counter' ),
				'fields'     => array(
					array(
						'field'       => 'premium_counter_title',
						'type'        => __( 'Counter: Title Text', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_counter_t_separator',
						'type'        => __( 'Counter: Thousands Separator', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_counter_preffix',
						'type'        => __( 'Counter: Prefix', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_counter_suffix',
						'type'        => __( 'Counter: Suffix', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
				),
			);

			$widgets['premium-addon-dual-header'] = array(
				'conditions' => array( 'widgetType' => 'premium-addon-dual-header' ),
				'fields'     => array(
					array(
						'field'       => 'premium_dual_header_first_header_text',
						'type'        => __( 'Dual Heading: First Heading', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_dual_header_second_header_text',
						'type'        => __( 'Dual Heading: Second Heading', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'background_text',
						'type'        => __( 'Dual Heading: Background Text', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
					'premium_dual_heading_link' => array(
						'field'       => 'url',
						'type'        => __( 'Advanced Heading: Heading URL', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINK',
					),
				),
			);

			$widgets['premium-carousel-widget'] = array(
				'conditions'        => array( 'widgetType' => 'premium-carousel-widget' ),
				'fields'            => array(),
				'integration-class' => 'PremiumAddons\Compatibility\WPML\Widgets\Carousel',
			);

			$widgets['premium-addon-fancy-text'] = array(
				'conditions'        => array( 'widgetType' => 'premium-addon-fancy-text' ),
				'fields'            => array(
					array(
						'field'       => 'premium_fancy_prefix_text',
						'type'        => __( 'Fancy Text: Prefix', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_fancy_suffix_text',
						'type'        => __( 'Fancy Text: Suffix', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_fancy_text_cursor_text',
						'type'        => __( 'Fancy Text: Cursor Text', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
				),
				'integration-class' => 'PremiumAddons\Compatibility\WPML\Widgets\FancyText',
			);

			$widgets['premium-img-gallery'] = array(
				'conditions'        => array( 'widgetType' => 'premium-img-gallery' ),
				'fields'            => array(
					array(
						'field'       => 'premium_gallery_load_more_text',
						'type'        => __( 'Grid: Load More Button', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
				),
				'integration-class' => 'PremiumAddons\Compatibility\WPML\Widgets\Grid',
			);

			$widgets['premium-icon-list'] = array(
				'conditions'        => array( 'widgetType' => 'premium-icon-list' ),
				'fields'            => array(),
				'integration-class' => 'PremiumAddons\Compatibility\WPML\Widgets\Icon_List',
			);

			$widgets['premium-addon-image-button'] = array(
				'conditions' => array( 'widgetType' => 'premium-addon-image-button' ),
				'fields'     => array(
					array(
						'field'       => 'premium_image_button_text',
						'type'        => __( 'Button: Text', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
					'premium_image_button_link' => array(
						'field'       => 'url',
						'type'        => __( 'Button: URL', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINK',
					),
				),
			);

			$widgets['premium-image-scroll'] = array(
				'conditions' => array( 'widgetType' => 'premium-image-scroll' ),
				'fields'     => array(
					array(
						'field'       => 'link_text',
						'type'        => __( 'Image Scroll: Link Title', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
					'link' => array(
						'field'       => 'url',
						'type'        => __( 'Image Scroll: URL', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINK',
					),
				),
			);

			$widgets['premium-addon-image-separator'] = array(
				'conditions' => array( 'widgetType' => 'premium-addon-image-separator' ),
				'fields'     => array(
					array(
						'field'       => 'premium_image_separator_image_link_text',
						'type'        => __( 'Image Separator: Link Title', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
					'link' => array(
						'field'       => 'premium_image_separator_image_link',
						'type'        => __( 'Image Separator: URL', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINK',
					),
				),
			);

			$widgets['premium-lottie'] = array(
				'conditions' => array( 'widgetType' => 'premium-lottie' ),
				'fields'     => array(
					'link' => array(
						'field'       => 'url',
						'type'        => __( 'Lottie : Link', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINK',
					),
				),
			);

			$widgets['premium-addon-maps'] = array(
				'conditions'        => array( 'widgetType' => 'premium-addon-maps' ),
				'fields'            => array(
					array(
						'field'       => 'premium_maps_center_lat',
						'type'        => __( 'Maps: Center Latitude', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_maps_center_long',
						'type'        => __( 'Maps: Center Longitude', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
				),
				'integration-class' => 'PremiumAddons\Compatibility\WPML\Widgets\Maps',
			);

			$widgets['premium-addon-modal-box'] = array(
				'conditions' => array( 'widgetType' => 'premium-addon-modal-box' ),
				'fields'     => array(
					array(
						'field'       => 'premium_modal_box_title',
						'type'        => __( 'Modal Box: Header Title', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_modal_box_content_temp',
						'type'        => __( 'Modal Box: Content Template ID', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_modal_box_content',
						'type'        => __( 'Modal Box: Content Text', 'premium-addons-for-elementor' ),
						'editor_type' => 'VISUAL',
					),
					array(
						'field'       => 'premium_modal_close_text',
						'type'        => __( 'Modal Box: Close Button', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_modal_box_button_text',
						'type'        => __( 'Modal Box: Trigger Button', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_modal_box_selector_text',
						'type'        => __( 'Modal Box: Trigger Text', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
				),
			);

			$widgets['premium-addon-person'] = array(
				'conditions'        => array( 'widgetType' => 'premium-addon-person' ),
				'fields'            => array(
					array(
						'field'       => 'premium_person_name',
						'type'        => __( 'Person: Name', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_person_title',
						'type'        => __( 'Person: Title', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_person_content',
						'type'        => __( 'Person: Description', 'premium-addons-for-elementor' ),
						'editor_type' => 'AREA',
					),
				),
				'integration-class' => 'PremiumAddons\Compatibility\WPML\Widgets\Person',
			);

			$widgets['premium-addon-pricing-table'] = array(
				'conditions'        => array( 'widgetType' => 'premium-addon-pricing-table' ),
				'fields'            => array(
					array(
						'field'       => 'premium_pricing_table_title_text',
						'type'        => __( 'Pricing Table: Title', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_pricing_table_slashed_price_value',
						'type'        => __( 'Pricing Table: Slashed Price', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_pricing_table_price_currency',
						'type'        => __( 'Pricing Table: Currency', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_pricing_table_price_value',
						'type'        => __( 'Pricing Table: Price Value', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_pricing_table_price_separator',
						'type'        => __( 'Pricing Table: Separator', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_pricing_table_price_duration',
						'type'        => __( 'Pricing Table: Duration', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_pricing_table_description_text',
						'type'        => __( 'Pricing Table: Description', 'premium-addons-for-elementor' ),
						'editor_type' => 'AREA',
					),
					array(
						'field'       => 'premium_pricing_table_button_text',
						'type'        => __( 'Pricing Table: Button Text', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_pricing_table_button_link',
						'type'        => __( 'Pricing Table: Button URL', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINK',
					),
					array(
						'field'       => 'premium_pricing_table_badge_text',
						'type'        => __( 'Pricing Table: Badge', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
				),
				'integration-class' => 'PremiumAddons\Compatibility\WPML\Widgets\Pricing_Table',
			);

			$widgets['premium-addon-progressbar'] = array(
				'conditions'        => array( 'widgetType' => 'premium-addon-progressbar' ),
				'fields'            => array(
					array(
						'field'       => 'premium_progressbar_left_label',
						'type'        => __( 'Progress Bar: Left Label', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
				),
				'integration-class' => 'PremiumAddons\Compatibility\WPML\Widgets\Progress_Bar',
			);

			$widgets['premium-addon-testimonials'] = array(
				'conditions' => array( 'widgetType' => 'premium-addon-testimonials' ),
				'fields'     => array(
					array(
						'field'       => 'premium_testimonial_person_name',
						'type'        => __( 'Testimonial: Name', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'separator_text',
						'type'        => __( 'Testimonial: Separator', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_testimonial_company_name',
						'type'        => __( 'Testimonial: Company', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_testimonial_company_link',
						'type'        => __( 'Testimonial: Company Link', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINK',
					),
					array(
						'field'       => 'premium_testimonial_content',
						'type'        => __( 'Testimonial: Content', 'premium-addons-for-elementor' ),
						'editor_type' => 'AREA',
					),
				),
			);

			$widgets['premium-addon-title'] = array(
				'conditions' => array( 'widgetType' => 'premium-addon-title' ),
				'fields'     => array(
					array(
						'field'       => 'premium_title_text',
						'type'        => __( 'Title: Text', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'background_text',
						'type'        => __( 'Title: Background Text', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
					'custom_link' => array(
						'field'       => 'url',
						'type'        => __( 'Title : Link', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINK',
					),
				),
			);

			$widgets['premium-addon-video-box'] = array(
				'conditions' => array( 'widgetType' => 'premium-addon-video-box' ),
				'fields'     => array(
					array(
						'field'       => 'premium_video_box_link',
						'type'        => __( 'Video Box: Link', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINK',
					),
					array(
						'field'       => 'premium_video_box_description_text',
						'type'        => __( 'Video Box: Description', 'premium-addons-for-elementor' ),
						'editor_type' => 'AREA',
					),
				),
			);

			$widgets['premium-vscroll'] = array(
				'conditions'        => array( 'widgetType' => 'premium-vscroll' ),
				'fields'            => array(
					array(
						'field'       => 'dots_tooltips',
						'type'        => __( 'Vertical Scroll: Tooltips', 'premium-addons-for-elementor' ),
						'editor_type' => 'LINE',
					),
				),
				'integration-class' => 'PremiumAddons\Compatibility\WPML\Widgets\Vertical_Scroll',
			);

			return $widgets;
		}

		/**
		 * Creates and returns an instance of the class
		 *
		 * @since 0.0.1
		 * @access public
		 *
		 * @return object
		 */
		public static function get_instance() {

			if ( ! isset( self::$instance ) ) {

				self::$instance = new self();

			}

			return self::$instance;
		}

	}

}
