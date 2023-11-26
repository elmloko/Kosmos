<?php
/**
 * Premium Google Maps.
 */

namespace PremiumAddons\Widgets;

// Elementor Classes.
use Elementor\Icons_Manager;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;

// PremiumAddons Classes.
use PremiumAddons\Admin\Includes\Admin_Helper;
use PremiumAddons\Includes\Helper_Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

/**
 * Class Premium_Maps
 */
class Premium_Maps extends Widget_Base {

	/**
	 * Retrieve Widget Name.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_name() {
		return 'premium-addon-maps';
	}

	/**
	 * Widget preview refresh button.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function is_reload_preview_required() {
		return true;
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return __( 'Google Maps', 'premium-addons-for-elementor' );
	}

	/**
	 * Retrieve Widget Icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string widget icon.
	 */
	public function get_icon() {
		return 'pa-maps';
	}

	/**
	 * Retrieve Widget Categories.
	 *
	 * @since 1.5.1
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( 'premium-elements' );
	}

	/**
	 * Retrieve Widget Dependent CSS.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array CSS style handles.
	 */
	public function get_style_depends() {

		$icons_css = apply_filters( 'papro_activated', false ) ? array( 'elementor-icons' ) : array();

		return array_merge(
			$icons_css,
			array(
				'premium-addons',
			)
		);
	}

	/**
	 * Retrieve Widget Dependent JS.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array JS script handles.
	 */
	public function get_script_depends() {
		return array(
			'elementor-waypoints',
			'pa-maps-cluster',
			'pa-maps',
			'pa-maps-api',
		);
	}

	/**
	 * Retrieve Widget Keywords.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget keywords.
	 */
	public function get_keywords() {
		return array( 'pa', 'premium', 'marker', 'pin', 'tooltip', 'location' );
	}

	/**
	 * Retrieve Widget Support URL.
	 *
	 * @access public
	 *
	 * @return string support URL.
	 */
	public function get_custom_help_url() {
		return 'https://premiumaddons.com/support/';
	}

	/**
	 * Register Google Maps controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		$this->start_controls_section(
			'premium_maps_map_settings',
			array(
				'label' => __( 'Center Location', 'premium-addons-for-elementor' ),
			)
		);

		$settings = Admin_Helper::get_integrations_settings();

		if ( empty( $settings['premium-map-api'] ) || '1' == $settings['premium-map-api'] ) { // phpcs:ignore WordPress.PHP.StrictComparisons
			$this->add_control(
				'premium_maps_api_url',
				array(
					'raw'             => 'Premium Maps widget requires an API key. Get your API key from <a target="_blank" href="https://premiumaddons.com/docs/getting-your-api-key-for-google-reviews/">here</a> and add it to Premium Addons admin page. Go to Dashboard -> Premium Addons for Elementor -> Integrations tab',
					'type'            => Controls_Manager::RAW_HTML,
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				)
			);
		}

		$this->add_control(
			'premium_map_ip_location',
			array(
				'label'        => __( 'Get User Location', 'premium-addons-for-elementor' ),
				'description'  => __( 'Get center location from visitor\'s location', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
			)
		);

		$this->add_control(
			'premium_map_location_finder',
			array(
				'label'     => __( 'Latitude & Longitude Finder', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'premium_map_ip_location!' => 'true',
				),
			)
		);

		$this->add_control(
			'premium_map_notice',
			array(
				'label'       => __( 'Find Latitude & Longitude', 'elementor' ),
				'type'        => Controls_Manager::RAW_HTML,
				'raw'         => '<form onsubmit="getAddress(this);" action="javascript:void(0);"><input type="text" id="premium-map-get-address" class="premium-map-get-address" style="margin-top:10px; margin-bottom:10px;"><input type="submit" value="Search" class="elementor-button elementor-button-default" onclick="getAddress(this)"></form>',
				'label_block' => true,
				'condition'   => array(
					'premium_map_location_finder' => 'yes',
					'premium_map_ip_location!'    => 'true',
				),
			)
		);

		$this->add_control(
			'premium_maps_center_lat',
			array(
				'label'       => __( 'Center Latitude', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'description' => __( 'Center latitude and longitude are required to identify your location', 'premium-addons-for-elementor' ),
				'default'     => '59.3347981',
				'label_block' => true,
				'condition'   => array(
					'premium_map_ip_location!' => 'true',
				),
			)
		);

		$this->add_control(
			'premium_maps_center_long',
			array(
				'label'       => __( 'Center Longitude', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'description' => __( 'Center latitude and longitude are required to identify your location', 'premium-addons-for-elementor' ),
				'default'     => '18.0601028',
				'label_block' => true,
				'condition'   => array(
					'premium_map_ip_location!' => 'true',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_maps_map_pins_settings',
			array(
				'label' => __( 'Markers', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_maps_markers_width',
			array(
				'label' => __( 'Max Width', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::NUMBER,
				'title' => __( 'Set the Maximum width for markers description box', 'premium-addons-for-elementor' ),
			)
		);

		$repeater = new REPEATER();

		$repeater->start_controls_tabs( 'marker_tabs' );

		$repeater->start_controls_tab(
			'marker_content_tab',
			array(
				'label' => esc_html__( 'Content', 'elementor-pro' ),
			)
		);

		$repeater->add_control(
			'premium_map_pin_location_finder',
			array(
				'label' => __( 'Location Finder', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$repeater->add_control(
			'premium_map_pin_notice',
			array(
				'label'       => __( 'Find Location', 'elementor' ),
				'type'        => Controls_Manager::RAW_HTML,
				'raw'         => '<form onsubmit="getPinAddress(this);" action="javascript:void(0);"><input type="text" id="premium-map-get-address" class="premium-map-get-address" style="margin-top:10px; margin-bottom:10px;"><input type="submit" value="Search" class="elementor-button elementor-button-default" onclick="getPinAddress(this)"></form>',
				'label_block' => true,
				'condition'   => array(
					'premium_map_pin_location_finder' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'map_latitude',
			array(
				'label'       => __( 'Latitude', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'description' => 'Click <a href="https://www.latlong.net/" target="_blank">here</a> to get your location coordinates',
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'map_longitude',
			array(
				'name'        => 'map_longitude',
				'label'       => __( 'Longitude', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'description' => 'Click <a href="https://www.latlong.net/" target="_blank">here</a> to get your location coordinates',
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'pin_title',
			array(
				'label'       => __( 'Location Title', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'pin_desc',
			array(
				'label'       => __( 'Description', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::WYSIWYG,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'advanced_view',
			array(
				'label' => __( 'Advanced Info', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$get_pro = Helper_Functions::get_campaign_link( 'https://premiumaddons.com/pro', 'editor-page', 'wp-editor', 'get-pro' );

		$papro_activated = apply_filters( 'papro_activated', false );

		if ( ! $papro_activated ) {
			$repeater->add_control(
				'marker_notice',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => __( 'Advanced Marker option is available in Premium Addons Pro.', 'premium-addons-for-elementor' ) . '<a href="' . esc_url( $get_pro ) . '" target="_blank">' . __( 'Upgrade now!', 'premium-addons-for-elementor' ) . '</a>',
					'content_classes' => 'papro-upgrade-notice',
					'condition'       => array(
						'advanced_view' => 'yes',
					),
				)
			);
		}

		do_action( 'pa_maps_marker_controls', $repeater );

		$repeater->add_control(
			'open_by_default',
			array(
				'label'      => __( 'Opened By Default', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SWITCHER,
				'separator'  => 'before',
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'pin_title',
							'operator' => '!==',
							'value'    => '',
						),
						array(
							'name'     => 'pin_desc',
							'operator' => '!==',
							'value'    => '',
						),
					),
				),
			)
		);

		$repeater->add_control(
			'custom_id',
			array(
				'label'       => __( 'Custom ID', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Use this with Premium Carousel widget ', 'premium-addons-for-elementor' ) . '<a href="https://premiumaddons.com/docs/how-to-use-elementor-widgets-to-navigate-through-carousel-widget-slides/" target="_blank">Custom Navigation option</a>',
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
			)
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'marker_style_tab',
			array(
				'label' => esc_html__( 'Style', 'elementor-pro' ),
			)
		);

		$repeater->add_control(
			'pin_icon',
			array(
				'label' => __( 'Custom Icon', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);

		$repeater->add_control(
			'pin_icon_size',
			array(
				'label'      => __( 'Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 200,
					),
					'em' => array(
						'min' => 1,
						'max' => 20,
					),
				),
			)
		);

		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		$this->add_control(
			'premium_maps_map_pins',
			array(
				'label'       => __( 'Map Pins', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::REPEATER,
				'default'     => array(
					'map_latitude'  => '59.3347981',
					'map_longitude' => '18.0601028',
					'pin_title'     => __( 'Barbeque Steakhouse & Bar', 'premium-addons-for-elementor' ),
					'pin_desc'      => __( 'Add an optional description to your map pin', 'premium-addons-for-elementor' ),
					'pin_address'   => 'Kungsgatan 54, 111 35 Stockholm, Sweden',
					'pin_website'   => 'https://bbqsteakhouse.se/',
					'pin_phone'     => '+468100026',
					'pin_hours'     => '10AM-11PM',
				),
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ pin_title }}}',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_maps_controls_section',
			array(
				'label' => __( 'Controls', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_maps_map_type',
			array(
				'label'   => __( 'Map Type', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'roadmap'   => __( 'Road Map', 'premium-addons-for-elementor' ),
					'satellite' => __( 'Satellite', 'premium-addons-for-elementor' ),
					'terrain'   => __( 'Terrain', 'premium-addons-for-elementor' ),
					'hybrid'    => __( 'Hybrid', 'premium-addons-for-elementor' ),
				),
				'default' => 'roadmap',
			)
		);

		$this->add_responsive_control(
			'premium_maps_map_height',
			array(
				'label'      => __( 'Height', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh' ),
				'default'    => array(
					'size' => 500,
					'unit' => 'px',
				),
				'range'      => array(
					'px' => array(
						'min' => 80,
						'max' => 1400,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium_maps_map_height' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'premium_maps_map_zoom',
			array(
				'label'   => __( 'Zoom', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => array(
					'size' => 12,
				),
				'range'   => array(
					'px' => array(
						'min' => 0,
						'max' => 22,
					),
				),
			)
		);

		$this->add_control(
			'disable_drag',
			array(
				'label' => __( 'Disable Map Drag', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'premium_maps_map_option_map_type_control',
			array(
				'label' => __( 'Map Type Controls', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'premium_maps_map_option_zoom_controls',
			array(
				'label' => __( 'Zoom Controls', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'premium_maps_map_option_streeview',
			array(
				'label' => __( 'Street View Control', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'premium_maps_map_option_fullscreen_control',
			array(
				'label' => __( 'Fullscreen Control', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'premium_maps_map_option_mapscroll',
			array(
				'label' => __( 'Scroll Wheel Zoom', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'premium_maps_marker_open',
			array(
				'label' => __( 'Info Container Always Opened', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'premium_maps_marker_hover_open',
			array(
				'label' => __( 'Open Info Container on Hover', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'premium_maps_marker_mouse_out',
			array(
				'label'     => __( 'Close Info Container on Mouse Out', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'premium_maps_marker_hover_open' => 'yes',
				),
			)
		);

		if ( $settings['premium-map-cluster'] ) {
			$this->add_control(
				'premium_maps_map_option_cluster',
				array(
					'label' => __( 'Marker Clustering', 'premium-addons-for-elementor' ),
					'type'  => Controls_Manager::SWITCHER,
				)
			);

			$this->add_control(
				'cluster_icon',
				array(
					'label'     => __( 'Cluster Icon', 'premium-addons-for-elementor' ),
					'type'      => Controls_Manager::MEDIA,
					'condition' => array(
						'premium_maps_map_option_cluster' => 'yes',
					),
				)
			);

		}

		$this->add_control(
			'load_on_visible',
			array(
				'label'        => __( 'Load Map On Scroll', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'description'  => __( 'This option will load the map while scrolling to improve page loading speed', 'premium-addons-for-elementor' ),
				'return_value' => 'true',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_maps_custom_styling_section',
			array(
				'label' => __( 'Map Style', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_maps_custom_styling',
			array(
				'label'       => __( 'JSON Code', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXTAREA,
				'description' => 'Get your custom styling from <a href="https://snazzymaps.com/" target="_blank">here</a>',
				'label_block' => true,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_pa_docs',
			array(
				'label' => __( 'Helpful Documentations', 'premium-addons-for-elementor' ),
			)
		);

		$doc1_url = Helper_Functions::get_campaign_link( 'https://premiumaddons.com/docs/google-maps-widget-tutorial', 'editor-page', 'wp-editor', 'get-support' );

		$this->add_control(
			'doc_1',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf( '<a href="%s" target="_blank">%s</a>', $doc1_url, __( 'Getting started »', 'premium-addons-for-elementor' ) ),
				'content_classes' => 'editor-pa-doc',
			)
		);

		$doc2_url = Helper_Functions::get_campaign_link( 'https://premiumaddons.com/docs/getting-your-api-key-for-google-reviews', 'editor-page', 'wp-editor', 'get-support' );

		$this->add_control(
			'doc_2',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf( '<a href="%s" target="_blank">%s</a>', $doc2_url, __( 'Getting your API key »', 'premium-addons-for-elementor' ) ),
				'content_classes' => 'editor-pa-doc',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_maps_box_style',
			array(
				'label' => __( 'Map', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'map_border',
				'selector' => '{{WRAPPER}} .premium-maps-container',
			)
		);

		$this->add_control(
			'premium_maps_box_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-maps-container,{{WRAPPER}} .premium_maps_map_height' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'label'    => __( 'Shadow', 'premium-addons-for-elementor' ),
				'name'     => 'premium_maps_box_shadow',
				'selector' => '{{WRAPPER}} .premium-maps-container',
			)
		);

		$this->add_responsive_control(
			'premium_maps_box_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-maps-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'premium_maps_box_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-maps-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'marker_window',
			array(
				'label' => __( 'Marker Info', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'marker_window_width',
			array(
				'label'      => __( 'Minimum Width', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 300,
						'max' => 1000,
					),
					'em' => array(
						'min' => 20,
						'max' => 50,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-maps-info-container' => 'min-width: {{SIZE}}{{UNIT}} !important',
				),
			)
		);

		$this->add_control(
			'marker_window_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fff',
				'selectors' => array(
					'{{WRAPPER}} .gm-style-iw, {{WRAPPER}} .premium-maps-location-info, {{WRAPPER}} .gm-style .gm-style-iw-tc::after' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'marker_window_border',
				'selector' => '{{WRAPPER}} .gm-style-iw',
			)
		);

		$this->add_control(
			'marker_window_border_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .gm-style-iw' => 'border-radius: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'marker_window_shadow',
				'selector' => '{{WRAPPER}} .gm-style-iw',
			)
		);

		$this->add_responsive_control(
			'marker_window_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-maps-info-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'title_heading',
			array(
				'label'     => __( 'Location Title', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'premium_maps_pin_title_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-maps-info-title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-maps-title-wrap' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .maps-skin1 .premium-maps-location-direction' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'pin_title_typography',
				'selector' => '{{WRAPPER}} .premium-maps-info-title',
			)
		);

		$this->add_responsive_control(
			'premium_maps_pin_title_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-maps-title-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_maps_pin_title_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-maps-title-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_maps_pin_title_align',
			array(
				'label'     => __( 'Alignment', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => __( 'Left', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'center',
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .premium-maps-title-wrap' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'description_heading',
			array(
				'label'     => __( 'Description', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'premium_maps_pin_text_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-maps-info-desc, {{WRAPPER}} .premium-maps-info-desc a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'pin_text_typo',
				'selector' => '{{WRAPPER}} .premium-maps-info-desc',
			)
		);

		$this->add_responsive_control(
			'premium_maps_pin_text_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-maps-info-desc' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_maps_pin_text_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-maps-info-desc' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_maps_pin_description_align',
			array(
				'label'     => __( 'Alignment', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => __( 'Left', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'center',
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .premium-maps-info-desc' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		if ( $papro_activated ) {

			$this->start_controls_section(
				'advanced_pins_style',
				array(
					'label' => __( 'Advanced Info', 'premium-addons-for-elementor' ),
					'tab'   => Controls_Manager::TAB_STYLE,
				)
			);

			$this->add_control(
				'info_icons_color',
				array(
					'label'     => __( 'Icons Color', 'premium-addons-for-elementor' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .maps-info-item i' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'info_text_color',
				array(
					'label'     => __( 'Text Color', 'premium-addons-for-elementor' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .maps-info-item p, {{WRAPPER}} .maps-info-item a' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'info_text_typography',
					'selector' => '{{WRAPPER}} .maps-info-item p',
				)
			);

			$this->add_control(
				'skin1_heading',
				array(
					'label'     => __( 'Get Directions Icon (Skin 1 only)', 'premium-addons-for-elementor' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_control(
				'directions_icon_color',
				array(
					'label'     => __( 'Icon Color', 'premium-addons-for-elementor' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .maps-skin1 .eicon-share-arrow' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'skin2_heading',
				array(
					'label'     => __( 'Get Directions Link (Skin 2, 3 only)', 'premium-addons-for-elementor' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_control(
				'directions_link_color',
				array(
					'label'     => __( 'Text Color', 'premium-addons-for-elementor' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .advanced-pin:not(.maps-skin1) .premium-maps-location-direction' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'directions_link_typography',
					'selector' => '{{WRAPPER}} .advanced-pin:not(.maps-skin1) .premium-maps-location-direction',
				)
			);

			$this->end_controls_section();

		}

	}

	/**
	 * Render Google Maps widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$papro_activated = apply_filters( 'papro_activated', false );

		$settings = $this->get_settings_for_display();

		$map_pins = $settings['premium_maps_map_pins'];

		$street_view = 'yes' === $settings['premium_maps_map_option_streeview'];

		$scroll_wheel = 'yes' === $settings['premium_maps_map_option_mapscroll'];

		$full_screen = 'yes' === $settings['premium_maps_map_option_fullscreen_control'];

		$zoom_control = 'yes' === $settings['premium_maps_map_option_zoom_controls'];

		$type_control = 'yes' === $settings['premium_maps_map_option_map_type_control'];

		$automatic_open = 'yes' === $settings['premium_maps_marker_open'];

		$hover_open = 'yes' === $settings['premium_maps_marker_hover_open'];

		$hover_close = 'yes' === $settings['premium_maps_marker_mouse_out'];

		$marker_cluster = false;
		$cluster_icon   = '';

		$cluster_enabled = Admin_Helper::get_integrations_settings()['premium-map-cluster'];

		if ( $cluster_enabled ) {
			$marker_cluster = 'yes' === $settings['premium_maps_map_option_cluster'];

			if ( $marker_cluster ) {
				$cluster_icon = $settings['cluster_icon']['url'];
			}
		}

		$centerlat = ! empty( $settings['premium_maps_center_lat'] ) ? $settings['premium_maps_center_lat'] : 18.591212;

		$centerlong = ! empty( $settings['premium_maps_center_long'] ) ? $settings['premium_maps_center_long'] : 73.741261;

		$marker_width = ! empty( $settings['premium_maps_markers_width'] ) ? $settings['premium_maps_markers_width'] : 1000;

		$ip_location = $settings['premium_map_ip_location'];

		if ( 'true' === $ip_location ) {

			require_once PREMIUM_ADDONS_PATH . 'widgets/dep/urlopen.php';

			if ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {

				$http_x_headers = sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) );

				if ( is_array( $http_x_headers ) ) {
					$http_x_headers = explode( ',', filter_var_array( $http_x_headers ) );
				}

				$_SERVER['REMOTE_ADDR'] = $http_x_headers[0];
			}

			$ip_address = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';

			$env = unserialize( rplg_urlopen( "http://www.geoplugin.net/php.gp?ip=$ip_address" )['data'] );

			$centerlat = isset( $env['geoplugin_latitude'] ) ? $env['geoplugin_latitude'] : $centerlat;

			$centerlong = isset( $env['geoplugin_longitude'] ) ? $env['geoplugin_longitude'] : $centerlong;

		}

		$map_settings = array(
			'zoom'              => $settings['premium_maps_map_zoom']['size'],
			'maptype'           => $settings['premium_maps_map_type'],
			'streetViewControl' => $street_view,
			'centerlat'         => $centerlat,
			'centerlong'        => $centerlong,
			'scrollwheel'       => $scroll_wheel,
			'fullScreen'        => $full_screen,
			'zoomControl'       => $zoom_control,
			'typeControl'       => $type_control,
			'automaticOpen'     => $automatic_open,
			'hoverOpen'         => $hover_open,
			'hoverClose'        => $hover_close,
			'cluster'           => $marker_cluster,
			'cluster_icon'      => $cluster_icon,
			'drag'              => $settings['disable_drag'],
			'loadScroll'        => $settings['load_on_visible'],
		);

		$this->add_render_attribute(
			'style_wrapper',
			array(
				'class'         => 'premium_maps_map_height',
				'data-settings' => wp_json_encode( $map_settings ),
				'data-style'    => $settings['premium_maps_custom_styling'],
			)
		);

		?>

	<div class="premium-maps-container" id="premium-maps-container">

		<?php if ( count( $map_pins ) ) { ?>

			<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'style_wrapper' ) ); ?>>
				<?php
				foreach ( $map_pins as $index => $pin ) {

					$key = 'map_marker_' . $index;

					$pin_longitude = $pin['map_longitude'];
					$pin_latitude  = $pin['map_latitude'];

					$this->add_render_attribute(
						$key,
						array(
							'class'          => array( 'premium-pin', 'elementor-invisible' ),
							'data-lng'       => $pin_longitude,
							'data-lat'       => $pin_latitude,
							'data-icon'      => $pin['pin_icon']['url'],
							'data-icon-size' => $pin['pin_icon_size']['size'],
							'data-max-width' => $marker_width,
							'data-activated' => 'yes' === $pin['open_by_default'],
						)
					);

					if ( ! empty( $pin['custom_id'] ) ) {
						$this->add_render_attribute( $key, 'data-id', esc_attr( $pin['custom_id'] ) );
					}

					$info_key = 'marker_info_' . $index;

					$this->add_render_attribute( $info_key, 'class', 'premium-maps-info-container' );

					if ( $papro_activated && 'yes' === $pin['advanced_view'] ) {
						$this->add_render_attribute(
							$info_key,
							'class',
							array(
								'advanced-pin',
								'maps-' . $pin['marker_skin'],
							)
						);

						$this->render_advanced_pin_view( $pin, $key, $info_key );

					} else {

						$this->render_classic_pin_view( $pin, $key );
					}

					?>

					<?php
				}
				?>

			</div>

		<?php } ?>

	</div>

		<?php
	}

	/**
	 * Render Classic Pin View
	 *
	 * Renders the HTML markup of the classic view.
	 *
	 * @since 4.9.47
	 * @access protected
	 *
	 * @param object $pin pin object.
	 * @param string $key pin key.
	 */
	protected function render_classic_pin_view( $pin, $key ) {

		?>

			<div <?php echo wp_kses_post( $this->get_render_attribute_string( $key ) ); ?>>
				<?php if ( ! empty( $pin['pin_title'] ) || ! empty( $pin['pin_desc'] ) ) : ?>
					<div class='premium-maps-info-container'>

						<div class='premium-maps-title-wrap'>
							<p class='premium-maps-info-title'><?php echo wp_kses_post( $pin['pin_title'] ); ?></p>
						</div>

						<div class='premium-maps-info-desc'>
							<?php echo $this->parse_text_editor( $pin['pin_desc'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>

					</div>
				<?php endif; ?>
			</div>

		<?php

	}

	/**
	 * Render Advanced Pin View
	 *
	 * Renders the HTML markup of the advanced view.
	 *
	 * @since 4.9.47
	 * @access protected
	 *
	 * @param object $pin pin object.
	 * @param string $key pin key.
	 * @param string $info_key pin info key.
	 */
	protected function render_advanced_pin_view( $pin, $key, $info_key ) {

		$pin_longitude = $pin['map_longitude'];
		$pin_latitude  = $pin['map_latitude'];

		$direction_link = sprintf( 'https://www.google.com/maps/dir/?api=1&destination=%s,%s', $pin_latitude, $pin_longitude );

		?>

			<div <?php echo wp_kses_post( $this->get_render_attribute_string( $key ) ); ?>>
				<?php if ( ! empty( $pin['pin_title'] ) || ! empty( $pin['pin_desc'] ) ) : ?>
					<div <?php echo wp_kses_post( $this->get_render_attribute_string( $info_key ) ); ?>>

						<div class='premium-maps-info-close'>
							<i class='eicon-close' aria-hidden='true'></i>
						</div>

						<?php if ( 'skin3' === $pin['marker_skin'] ) : ?>
						<div class='premium-maps-skin3-wrap'>
						<?php endif; ?>

						<div class='premium-maps-info-img'>
							<img src='<?php echo esc_attr( $pin['pin_img']['url'] ); ?>' alt='<?php echo esc_attr( $pin['pin_img']['alt'] ); ?>'>
						</div>


						<div class='premium-maps-title-wrap'>
							<p class='premium-maps-info-title'><?php echo wp_kses_post( $pin['pin_title'] ); ?></p>

							<?php if ( in_array( $pin['marker_skin'], array( 'skin1', 'skin3' ) ) ) : ?>
								<?php if ( 'skin1' === $pin['marker_skin'] ) : ?>
								<div class='premium-maps-location-directions'>
								<?php endif; ?>
									<a class='premium-maps-location-direction' title='<?php echo esc_attr( __( 'Directions', 'premium-addons-for-elementor' ) ); ?>' ref='nofollow' target='_blank' href='<?php echo esc_url( $direction_link ); ?>'>
										<i class='eicon-share-arrow' aria-hidden='true'></i>
										<span><?php echo wp_kses_post( __( 'Get Directions', 'premium-addons-for-elementor' ) ); ?></span>
									</a>
								<?php if ( 'skin1' === $pin['marker_skin'] ) : ?>
								</div>
								<?php endif; ?>
							<?php endif; ?>

						</div>

						<?php if ( 'skin3' === $pin['marker_skin'] ) : ?>
						</div>
						<?php endif; ?>

						<div class='premium-maps-location-info'>

							<?php if ( ! empty( $pin['pin_address'] ) ) : ?>
								<div class='premium-maps-info-location maps-info-item'>
									<i class='eicon-map-pin' aria-hidden='true'></i>
									<p><?php echo wp_kses_post( $pin['pin_address'] ); ?></p>
								</div>
							<?php endif; ?>

							<?php if ( ! empty( $pin['pin_website'] ) ) : ?>
								<div class='premium-maps-info-website maps-info-item'>
									<i class='eicon-globe' aria-hidden='true'></i>
									<p>
										<a href='<?php echo esc_attr( $pin['pin_website'] ); ?>' target='_blank' rel='nofollow'>
											<?php echo wp_kses_post( $pin['pin_website'] ); ?>
										</a>
									</p>
								</div>
							<?php endif; ?>

							<?php if ( ! empty( $pin['pin_phone'] ) ) : ?>
								<div class='premium-maps-info-number maps-info-item'>
									<i class='eicon-headphones' aria-hidden='true'></i>
									<p>
										<a href='tel:<?php echo esc_attr( $pin['pin_phone'] ); ?>' target='_blank' rel='nofollow'>
											<?php echo wp_kses_post( $pin['pin_phone'] ); ?>
										</a>
									</p>
								</div>
							<?php endif; ?>

							<?php if ( ! empty( $pin['pin_hours'] ) ) : ?>
								<div class='premium-maps-info-hours maps-info-item'>
									<i class='eicon-clock-o' aria-hidden='true'></i>
									<p><?php echo wp_kses_post( $pin['pin_hours'] ); ?></p>
								</div>
							<?php endif; ?>

							<div class='premium-maps-info-desc'>
								<?php echo $this->parse_text_editor( $pin['pin_desc'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</div>

							<?php if ( 'skin2' === $pin['marker_skin'] ) : ?>
								<a class='premium-maps-location-direction' title='<?php echo esc_attr( __( 'Directions', 'premium-addons-for-elementor' ) ); ?>' ref='nofollow' target='_blank' href='<?php echo esc_url( $direction_link ); ?>'>
									<?php echo wp_kses_post( __( 'Get Directions', 'premium-addons-for-elementor' ) ); ?>
								</a>
							<?php endif; ?>

						</div>

					</div>
				<?php endif; ?>
			</div>

		<?php

	}
}
