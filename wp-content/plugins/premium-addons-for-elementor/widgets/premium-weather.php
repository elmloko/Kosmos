<?php
/**
 * Premium Weather.
 */

namespace PremiumAddons\Widgets;

// Elementor Classes.
use Elementor\Utils;
use Elementor\Widget_Base;
use Elementor\Icons_Manager;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;

// PremiumAddons Classes.
use PremiumAddons\Includes\Helper_Functions;
use PremiumAddons\Admin\Includes\Admin_Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

/**
 * Class Premium_Weather.
 */
class Premium_Weather extends Widget_Base {

	/**
	 * Options
	 *
	 * @var options
	 */
	private $options = null;

	/**
	 * Settings
	 *
	 * @var settings
	 */
	public $settings = null;

	/**
	 * Check Icon Draw Option.
	 *
	 * @since 4.9.26
	 * @access public
	 */
	public function check_icon_draw() {
		$is_enabled = Admin_Helper::check_svg_draw( 'premium-weather' );
		return $is_enabled;
	}

	/**
	 * Retrieve Widget Name.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_name() {
		return 'premium-weather';
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return __( 'Weather', 'premium-addons-for-elementor' );
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
		return 'pa-weather';
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
		return array( 'pa', 'premium', 'magazine', 'news', 'weather', 'forecast' );
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
	 * Widget preview refresh button.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function is_reload_preview_required() {
		return true;
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

		return array(
			'font-awesome-5-all',
			'pa-slick',
			'premium-addons',
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

		$draw_scripts = $this->check_icon_draw() ? array(
			'pa-fontawesome-all',
			'pa-tweenmax',
			'pa-motionpath',
		) : array();

		return array_merge(
			$draw_scripts,
			array(
				'pa-slick',
				'pa-slimscroll',
				'lottie-js',
				'premium-addons',
			)
		);
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
	 * Register Smart Post Listing controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {

		$this->options = apply_filters(
			'pa_weather_options',
			array(
				'source'                 => array(
					'name'   => __( 'City Name', 'premium-addons-for-elementor' ),
					'coords' => __( 'City Coordinates (Pro)', 'premium-addons-for-elementor' ),
				),
				'layouts'                => array(
					'layout-1' => __( 'Layout 1', 'premium-addons-for-elementor' ),
					'layout-2' => __( 'Layout 2', 'premium-addons-for-elementor' ),
					'layout-3' => __( 'Layout 3 (Pro)', 'premium-addons-for-elementor' ),
				),
				'source_condition'       => array( 'coords' ),
				'dailyf_condition'       => array(
					'enable_forecast' => 'yes',
				),
				'custom_icons_condition' => array(
					'enable_custom_icon' => 'yes',
				),
			)
		);

		$this->register_content_tab_controls();
		$this->register_style_tab_controls();
	}

	/**
	 * Adds content tab controls.
	 *
	 * @access private
	 * @since 4.9.37
	 */
	private function register_content_tab_controls() {

		$this->add_general_section_controls();
		$this->add_display_options_section();
		$this->add_hourly_forecast_section();
		$this->add_daily_forecast_section();
		$this->add_custom_icons_section();
		$this->add_helpful_info_section();

	}

	/**
	 * Adds style tab controls.
	 *
	 * @access private
	 * @since 4.9.37
	 */
	private function register_style_tab_controls() {

		$this->add_city_style();
		$this->add_current_weather_style();
		$this->add_hourly_style();
		$this->add_forecast_style();
		$this->add_tabs_style();
		$this->add_navigation_style();
		$this->add_outer_container_style();
	}

	/**
	 * Adds General controls.
	 *
	 * @access private
	 * @since 4.9.37
	 */
	private function add_general_section_controls() {

		$papro_activated = apply_filters( 'papro_activated', false );

		$this->start_controls_section(
			'pa_weather_general_section',
			array(
				'label' => __( 'General Settings', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'api_key',
			array(
				'label'       => __( 'API Key', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'description' => 'Get your OpenWeatherMap API Key by signing up <a href="https://openweathermap.org/" target="_blank">here</a>',
			)
		);

		$this->add_control(
			'location_type',
			array(
				'label'       => __( 'Location', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'render_type' => 'template',
				'label_block' => true,
				'options'     => array(
					'current' => __( 'Current Location', 'premium-addons-for-elementor' ),
					'custom'  => __( 'Custom Location', 'premium-addons-for-elementor' ),
				),
				'default'     => 'custom',
			)
		);

		$this->add_control(
			'custom_location_type',
			array(
				'label'       => __( 'Get By:', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'render_type' => 'template',
				'label_block' => true,
				'options'     => $this->options['source'],
				'default'     => 'name',
				'condition'   => array(
					'location_type' => 'custom',
				),
			)
		);

		$this->add_control(
			'city_name',
			array(
				'label'       => __( 'City Name', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
				'default'     => 'London',
				'condition'   => array(
					'location_type'        => 'custom',
					'custom_location_type' => 'name',
				),
			)
		);

		if ( ! $papro_activated ) {

			$get_pro = Helper_Functions::get_campaign_link( 'https://premiumaddons.com/pro', 'editor-page', 'wp-editor', 'get-pro' );

			$this->add_control(
				'weather_notice',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => __( 'This option is available in Premium Addons Pro. ', 'premium-addons-for-elementor' ) . '<a href="' . esc_url( $get_pro ) . '" target="_blank">' . __( 'Upgrade now!', 'premium-addons-for-elementor' ) . '</a>',
					'content_classes' => 'papro-upgrade-notice',
					'condition'       => array(
						'custom_location_type' => $this->options['source_condition'],
					),
				)
			);

		} else {
			do_action( 'pa_weather_source_controls', $this );
		}

		$this->add_control(
			'unit',
			array(
				'label'       => __( 'Unit', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'render_type' => 'template',
				'label_block' => true,
				'options'     => array(
					'metric'   => __( 'Metric', 'premium-addons-for-elementor' ),
					'imperial' => __( 'Imperial', 'premium-addons-for-elementor' ),
				),
				'default'     => 'metric',
			)
		);

		$this->add_control(
			'lang',
			array(
				'label'       => __( 'Language', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'description' => __( 'Please note that this affects only the city name and weather description', 'premium-addons-for-elementor' ),
				'render_type' => 'template',
				'label_block' => true,
				'options'     => array(
					'af'     => __( 'Afrikaans', 'premium-addons-for-elementor' ),
					'al'     => __( 'Albanian', 'premium-addons-for-elementor' ),
					'ar'     => __( 'Arabic', 'premium-addons-for-elementor' ),
					'az'     => __( 'Azerbaijani', 'premium-addons-for-elementor' ),
					'bg'     => __( 'Bulgarian', 'premium-addons-for-elementor' ),
					'ca'     => __( 'Catalan', 'premium-addons-for-elementor' ),
					'cz'     => __( 'Czech', 'premium-addons-for-elementor' ),
					'da'     => __( 'Danish', 'premium-addons-for-elementor' ),
					'de'     => __( 'German', 'premium-addons-for-elementor' ),
					'el'     => __( 'Greek', 'premium-addons-for-elementor' ),
					'en'     => __( 'English', 'premium-addons-for-elementor' ),
					'eu'     => __( 'Basque', 'premium-addons-for-elementor' ),
					'fa'     => __( 'Persian (Farsi)', 'premium-addons-for-elementor' ),
					'fi'     => __( 'Finnish', 'premium-addons-for-elementor' ),
					'fr'     => __( 'French', 'premium-addons-for-elementor' ),
					'gl'     => __( 'Galician', 'premium-addons-for-elementor' ),
					'he'     => __( 'Hebrew', 'premium-addons-for-elementor' ),
					'hi'     => __( 'Hindi', 'premium-addons-for-elementor' ),
					'hr'     => __( 'Croatian', 'premium-addons-for-elementor' ),
					'hu'     => __( 'Hungarian', 'premium-addons-for-elementor' ),
					'id'     => __( 'Indonesian', 'premium-addons-for-elementor' ),
					'it'     => __( 'Italian', 'premium-addons-for-elementor' ),
					'ja'     => __( 'Japanese', 'premium-addons-for-elementor' ),
					'kr'     => __( 'Korean', 'premium-addons-for-elementor' ),
					'la'     => __( 'Latvian', 'premium-addons-for-elementor' ),
					'lt'     => __( 'Lithuanian', 'premium-addons-for-elementor' ),
					'mk'     => __( 'Macedonian', 'premium-addons-for-elementor' ),
					'no'     => __( 'Norwegian', 'premium-addons-for-elementor' ),
					'nl'     => __( 'Dutch', 'premium-addons-for-elementor' ),
					'pl'     => __( 'Polish', 'premium-addons-for-elementor' ),
					'pt'     => __( 'Portuguese', 'premium-addons-for-elementor' ),
					'pt'     => __( 'br Português Brasil', 'premium-addons-for-elementor' ),
					'ro'     => __( 'Romanian', 'premium-addons-for-elementor' ),
					'ru'     => __( 'Russian', 'premium-addons-for-elementor' ),
					'sv, se' => __( 'Swedish', 'premium-addons-for-elementor' ),
					'sk'     => __( 'Slovak', 'premium-addons-for-elementor' ),
					'sl'     => __( 'Slovenian', 'premium-addons-for-elementor' ),
					'sp, es' => __( 'Spanish', 'premium-addons-for-elementor' ),
					'sr'     => __( 'Serbian', 'premium-addons-for-elementor' ),
					'th'     => __( 'Thai', 'premium-addons-for-elementor' ),
					'tr'     => __( 'Turkish', 'premium-addons-for-elementor' ),
					'ua, uk' => __( 'Ukrainian', 'premium-addons-for-elementor' ),
					'vi'     => __( 'Vietnamese', 'premium-addons-for-elementor' ),
					'zh_cn'  => __( 'cn Chinese Simplified', 'premium-addons-for-elementor' ),
					'zh_tw'  => __( 'Chinese Traditional', 'premium-addons-for-elementor' ),
					'zu'     => __( 'Zulu', 'premium-addons-for-elementor' ),
				),
				'default'     => 'en',
			)
		);

		$this->add_control(
			'reload',
			array(
				'label'     => __( 'Reload Data Once Every', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'separator' => 'before',
				'options'   => array(
					1  => __( 'Hour', 'premium-addons-for-elementor' ),
					3  => __( '3 Hours', 'premium-addons-for-elementor' ),
					6  => __( '6 Hours', 'premium-addons-for-elementor' ),
					12 => __( '12 Hours', 'premium-addons-for-elementor' ),
					24 => __( 'Day', 'premium-addons-for-elementor' ),
				),
				'default'   => 6,
				'condition' => array(
					'location_type' => 'custom',
				),
			)
		);

		$this->end_controls_section();
	}

	private function add_display_options_section() {

		$this->start_controls_section(
			'pa_weather_display_section',
			array(
				'label' => __( 'Current Weather', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'layout',
			array(
				'label'        => __( 'Choose Layout', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SELECT,
				'prefix_class' => 'premium-weather__',
				'render_type'  => 'template',
				'label_block'  => true,
				'options'      => $this->options['layouts'],
				'default'      => 'layout-1',
			)
		);

		$this->add_control(
			'show_temp_icon',
			array(
				'label'     => __( 'Show Temperature Icon', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'premium-addons-for-elementor' ),
				'label_off' => __( 'Hide', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'current_weather_heading',
			array(
				'label'     => esc_html__( 'Current Weather', 'premium-addons-for-elementor' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'show_current_weather',
			array(
				'label'     => __( 'Current Weather', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'premium-addons-for-elementor' ),
				'label_off' => __( 'Hide', 'premium-addons-for-elementor' ),
				'default'   => 'yes',
			)
		);

		$this->add_control(
			'show_curr_weather_desc',
			array(
				'label'     => __( 'Weather State', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'premium-addons-for-elementor' ),
				'label_off' => __( 'Hide', 'premium-addons-for-elementor' ),
				'default'   => 'yes',
				'condition' => array(
					'show_current_weather' => 'yes',
				),
			)
		);

		$this->add_control(
			'show_city',
			array(
				'label'     => __( 'Title', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'premium-addons-for-elementor' ),
				'label_off' => __( 'Hide', 'premium-addons-for-elementor' ),
				'default'   => 'yes',
				'condition' => array(
					'show_current_weather' => 'yes',
				),
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => __( 'Title', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Use this option to add a title of your choice, and use the {{city_name}} placeholder to add your city name.', 'premium-addons-for-elementor' ),
				'label_block' => true,
				'default'     => '{{city_name}}',
				'condition'   => array(
					'show_city'            => 'yes',
					'show_current_weather' => 'yes',
				),
			)
		);

		$this->add_control(
			'display_options_heading',
			array(
				'label'     => esc_html__( 'Display Options', 'premium-addons-for-elementor' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
				'condition' => array(
					'show_current_weather' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'current_weather_display',
			array(
				'label'        => __( 'Current Weather Display', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::CHOOSE,
				'prefix_class' => 'premium-cw%s-',
				'options'      => array(
					'inline' => array(
						'title' => __( 'Inline', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-ellipsis-h',
					),
					'block'  => array(
						'title' => __( 'Block', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-ellipsis-v',
					),
				),
				'default'      => 'block',
				'toggle'       => false,
				'condition'    => array(
					'layout'               => 'layout-1',
					'show_current_weather' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'current_align',
			array(
				'label'     => __( 'Current Weather Alignment', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => __( 'Left', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-left',
					),
					'center'     => array(
						'title' => __( 'Center', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-center',
					),
					'flex-end'   => array(
						'title' => __( 'Right', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'default'   => 'center',
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .premium-weather__basic-weather' => 'justify-content: {{VALUE}}',
				),
				'condition' => array(
					'layout'               => 'layout-1',
					'show_current_weather' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'city_align',
			array(
				'label'       => __( 'Title Alignment', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::CHOOSE,
				'description' => __( 'Note: this options works only if the "Current Weather Alignment" is set to <b>block</b>', 'premium-addons-for-elementor' ),
				'options'     => array(
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
				'default'     => 'center',
				'toggle'      => false,
				'conditions'  => array(
					'terms' => array(
						array(
							'name'  => 'show_current_weather',
							'value' => 'yes',
						),
						array(
							'name'  => 'show_city',
							'value' => 'yes',
						),
						array(
							'name'     => 'layout',
							'operator' => '!==',
							'value'    => 'layout-3',
						),
					),
				),
				'selectors'   => array(
					'{{WRAPPER}} .premium-weather__city-wrapper' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'cur_weather_sec_display',
			array(
				'label'        => __( 'Container Display', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::CHOOSE,
				'prefix_class' => 'premium-cw-sec%s-',
				'options'      => array(
					'inline' => array(
						'title' => __( 'Inline', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-ellipsis-h',
					),
					'block'  => array(
						'title' => __( 'Block', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-ellipsis-v',
					),
				),
				'default'      => 'block',
				'toggle'       => false,
				'condition'    => array(
					'layout'               => 'layout-1',
					'show_current_weather' => 'yes',
					'show_extra_info'      => 'yes',
				),
				'selectors'    => array(
					'{{WRAPPER}} .premium-weather__current-weather' => '{{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'cur_weather_sec_align',
			array(
				'label'       => __( 'Container Alignment', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => true,
				'options'     => array(
					'flex-start'    => array(
						'title' => __( 'Left', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-left',
					),
					'center'        => array(
						'title' => __( 'Center', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-center',
					),
					'flex-end'      => array(
						'title' => __( 'Right', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-right',
					),
					'space-between' => array(
						'title' => __( 'Strech', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-stretch',
					),
				),
				'default'     => 'space-between',
				'toggle'      => false,
				'selectors'   => array(
					'{{WRAPPER}} .premium-weather__current-weather' => 'justify-content: {{VALUE}}',
				),
				'condition'   => array(
					'layout'                  => 'layout-1',
					'show_current_weather'    => 'yes',
					'show_extra_info'         => 'yes',
					'cur_weather_sec_display' => 'inline',
				),
			)
		);

		$this->add_control(
			'cur_weather_sec_order',
			array(
				'label'     => __( 'Extra Weather Order', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'toggle'    => false,
				'options'   => array(
					'0' => array(
						'title' => __( 'First', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-order-start',
					),
					'2' => array(
						'title' => __( 'Last', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-order-end',
					),
				),
				'default'   => '0',
				'condition' => array(
					'layout'                  => 'layout-1',
					'show_current_weather'    => 'yes',
					'show_extra_info'         => 'yes',
					'cur_weather_sec_display' => 'inline',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-weather__current-weather .premium-weather__extra-weather' => 'order: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'cur_weather_sec_spacing',
			array(
				'label'      => __( 'Section Spacing', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'condition'  => array(
					'layout'                  => 'layout-1',
					'show_current_weather'    => 'yes',
					'show_extra_info'         => 'yes',
					'cur_weather_sec_display' => 'inline',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__current-weather' => 'column-gap: {{SIZE}}px',
				),
			)
		);

		$this->add_control(
			'show_extra_info',
			array(
				'label'     => __( 'Additional Weather Info', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before',
				'condition' => array(
					'show_current_weather' => 'yes',
				),
			)
		);

		$this->add_control(
			'pa_extra_weather',
			array(
				'label'       => __( 'Weather Data', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => array(
					'wind'     => __( 'Wind Speed', 'premium-addons-for-elementor' ),
					'pressure' => __( 'Pressure', 'premium-addons-for-elementor' ),
					'humidity' => __( 'Humidity', 'premium-addons-for-elementor' ),
					'rain'     => __( 'Rain', 'premium-addons-for-elementor' ),
					'snow'     => __( 'Snow', 'premium-addons-for-elementor' ),
				),
				'default'     => array( 'wind', 'pressure', 'humidity' ),
				'multiple'    => true,
				'label_block' => true,
				'description' => __( 'Please note that if you do not see some of the parameters displayed, it means that these weather phenomena have just not happened for the time of measurement for the city or location chosen', 'premium-addons-for-elementor' ),
				'condition'   => array(
					'show_extra_info'      => 'yes',
					'show_current_weather' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'extra_weather_display',
			array(
				'label'     => __( 'Display', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'row'    => array(
						'title' => __( 'Inline', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-ellipsis-h',
					),
					'column' => array(
						'title' => __( 'Block', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-ellipsis-v',
					),
				),
				'default'   => 'column',
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}}.premium-weather__layout-2:not(.premium-weather__hourly-yes) .premium-weather__extra-weather' => 'flex-direction: {{VALUE}}',
				),
				'condition' => array(
					'show_extra_info'      => 'yes',
					'enable_hourly!'       => 'yes',
					'show_current_weather' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'extra_weather_alignment',
			array(
				'label'     => __( 'ًWeather Alignment', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => __( 'Left', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-left',
					),
					'center'     => array(
						'title' => __( 'Center', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-center',
					),
					'flex-end'   => array(
						'title' => __( 'Right', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'default'   => 'center',
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}}.premium-weather__layout-1 .premium-weather__extra-weather,{{WRAPPER}}.premium-weather__layout-2:not(.premium-weather__hourly-yes) .premium-weather__extra-outer-wrapper' => 'justify-content: {{VALUE}}',
				),
				'condition' => array(
					'show_extra_info'      => 'yes',
					'layout!'              => 'layout-3',
					'show_current_weather' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'extra_weather_icon_size',
			array(
				'label'      => __( 'Icon Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__extra-weather i'  => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .premium-weather__extra-weather svg'  => 'width: {{SIZE}}{{UNIT}}; height:{{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'show_extra_info'      => 'yes',
					'show_current_weather' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'extra_weather_icon_spacing',
			array(
				'label'      => __( 'Icon Spacing', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__extra-weather > div'  => 'gap: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'show_extra_info'      => 'yes',
					'show_current_weather' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'extra_weather_spacing',
			array(
				'label'      => __( 'Spacing Between', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__extra-weather'  => 'gap: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'show_extra_info'      => 'yes',
					'show_current_weather' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	private function add_daily_forecast_section() {

		$papro_activated = apply_filters( 'papro_activated', false );

		$this->start_controls_section(
			'pa_daily_forecast_section',
			array(
				'label' => __( 'Daily Forecast', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'enable_forecast',
			array(
				'label'       => __( 'Daily Forecast', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'label_on'    => __( 'Show', 'premium-addons-for-elementor' ),
				'label_off'   => __( 'Hide', 'premium-addons-for-elementor' ),
				'description' => __( 'This option allows you to add daily forecast up to 7 days', 'premium-addons-for-elementor' ),
				'default'     => 'yes',
			)
		);

		if ( ! $papro_activated ) {

			$get_pro = Helper_Functions::get_campaign_link( 'https://premiumaddons.com/pro', 'editor-page', 'wp-editor', 'get-pro' );

			$this->add_control(
				'weather_notice2',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => __( 'This option is available in Premium Addons Pro. ', 'premium-addons-for-elementor' ) . '<a href="' . esc_url( $get_pro ) . '" target="_blank">' . __( 'Upgrade now!', 'premium-addons-for-elementor' ) . '</a>',
					'content_classes' => 'papro-upgrade-notice',
					'condition'       => $this->options['dailyf_condition'],
				)
			);

		} else {
			do_action( 'pa_weather_daily_forecast_controls', $this );
		}

		$this->end_controls_section();
	}

	private function add_hourly_forecast_section() {

		$this->start_controls_section(
			'pa_hourly_forecast_section',
			array(
				'label' => __( 'Hourly Forecast', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'enable_hourly',
			array(
				'label'        => __( 'Hourly Forecast', 'premium-addons-for-elementor' ),
				'prefix_class' => 'premium-weather__hourly-',
				'render_type'  => 'template',
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'premium-addons-for-elementor' ),
				'label_off'    => __( 'Hide', 'premium-addons-for-elementor' ),
				'description'  => __( 'This option allows you to add hourly weather condition.', 'premium-addons-for-elementor' ),
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'hourly_layout',
			array(
				'label'        => __( 'Choose Layout', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SELECT,
				'prefix_class' => 'premium-hours-',
				'render_type'  => 'template',
				'label_block'  => true,
				'options'      => array(
					'default'  => __( 'Layout 1', 'premium-addons-for-elementor' ),
					'vertical' => __( 'Layout 2', 'premium-addons-for-elementor' ),
				),
				'default'      => 'default',
				'condition'    => array(
					'enable_hourly' => 'yes',
					'layout!'       => 'layout-2',
				),
			)
		);

		$this->add_control(
			'hourly_max',
			array(
				'label'       => __( 'Max Number of Hours', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => __( 'Set a maximum number of hours to display', 'premium-addons-for-elementor' ),
				'default'     => 12,
				'max'         => 24,
				'min'         => 1,
				'condition'   => array(
					'enable_hourly' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'slides_to_show',
			array(
				'label'     => __( 'Hours To Show', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'devices'   => array( 'desktop', 'tablet', 'mobile' ),
				'default'   => 8,
				'max'       => 24,
				'min'       => 1,
				'condition' => array(
					'enable_hourly' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'slides_to_scroll',
			array(
				'label'     => __( 'Slides To Scroll', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'devices'   => array( 'desktop', 'tablet', 'mobile' ),
				'default'   => 2,
				'max'       => 24,
				'condition' => array(
					'enable_hourly' => 'yes',
					'hourly_layout' => 'default',
				),
			)
		);

		$this->add_control(
			'show_arrows_on_hover',
			array(
				'label'        => __( 'Display Arrows On Hover', 'premium-addons-for-elementor' ),
				'prefix_class' => 'premium-weather-hidden-arrows-',
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'condition'    => array(
					'enable_hourly' => 'yes',
				),
			)
		);

		$this->add_control(
			'hourly_ele_min_width',
			array(
				'label'      => __( 'Element Minimum Width', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}}.premium-hours-vertical .premium-weather__hourly-data > *'  => 'min-width: {{SIZE}}px;',
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
				),
				'condition'  => array(
					'enable_hourly' => 'yes',
					'hourly_layout' => 'vertical',
				),
			)
		);

		$this->add_control(
			'hourly_weather_data',
			array(
				'label'       => __( 'Weather Data', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT2,
				'label_block' => true,
				'options'     => array(
					'temp'      => __( 'Temperature', 'premium-addons-for-elementor' ),
					'desc'      => __( 'Description', 'premium-addons-for-elementor' ),
					'desc_icon' => __( 'Weather Icon', 'premium-addons-for-elementor' ),
					'wind'      => __( 'Wind Speed', 'premium-addons-for-elementor' ),
					'pressure'  => __( 'Pressure', 'premium-addons-for-elementor' ),
					'humidity'  => __( 'Humidity', 'premium-addons-for-elementor' ),
				),
				'default'     => array( 'desc_icon', 'temp', 'pressure', 'humidity', 'wind' ),
				'multiple'    => true,
				'condition'   => array(
					'enable_hourly' => 'yes',
					'hourly_layout' => 'vertical',
					'layout!'       => 'layout-2',
				),
			)
		);

		$this->end_controls_section();
	}

	private function add_custom_icons_section() {

		$papro_activated = apply_filters( 'papro_activated', false );

		$this->start_controls_section(
			'pa_custom_icon_section',
			array(
				'label' => __( 'Custom Icons', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'enable_custom_icon',
			array(
				'label' => __( 'Use Custom Icons', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		if ( ! $papro_activated ) {

			$get_pro = Helper_Functions::get_campaign_link( 'https://premiumaddons.com/pro', 'editor-page', 'wp-editor', 'get-pro' );

			$this->add_control(
				'weather_notice3',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => __( 'This option is available in Premium Addons Pro. ', 'premium-addons-for-elementor' ) . '<a href="' . esc_url( $get_pro ) . '" target="_blank">' . __( 'Upgrade now!', 'premium-addons-for-elementor' ) . '</a>',
					'content_classes' => 'papro-upgrade-notice',
					'condition'       => $this->options['custom_icons_condition'],
				)
			);

		} else {
			do_action( 'pa_weather_custom_icons_controls', $this );

			$draw_icon = $this->check_icon_draw();

			$this->add_control(
				'draw_svg',
				array(
					'label'       => __( 'Draw Icon', 'premium-addons-pro' ),
					'type'        => Controls_Manager::SWITCHER,
					'classes'     => $draw_icon ? '' : 'editor-pa-control-disabled',
					'description' => __( 'Use this option to draw your Font Awesome Custom Icons.', 'premium-addons-pro' ),
					'condition'   => array(
						'enable_custom_icon' => 'yes',
						'icons_source'       => 'custom',
					),
				)
			);

			if ( $draw_icon ) {

				$this->add_control(
					'path_width',
					array(
						'label'     => __( 'Path Thickness', 'premium-addons-for-elementor' ),
						'type'      => Controls_Manager::SLIDER,
						'range'     => array(
							'px' => array(
								'min'  => 0,
								'max'  => 50,
								'step' => 0.1,
							),
						),
						'condition' => array(
							'draw_svg'           => 'yes',
							'enable_custom_icon' => 'yes',
							'icons_source'       => 'custom',
						),
						'selectors' => array(
							'{{WRAPPER}} .premium-drawable-icon *' => 'stroke-width: {{SIZE}};',
						),
					)
				);

				$this->add_control(
					'stroke_color',
					array(
						'label'     => __( 'Stroke Color', 'premium-addons-for-elementor' ),
						'type'      => Controls_Manager::COLOR,
						'global'    => array(
							'default' => Global_Colors::COLOR_ACCENT,
						),
						'condition' => array(
							'draw_svg'           => 'yes',
							'enable_custom_icon' => 'yes',
							'icons_source'       => 'custom',
						),
						'selectors' => array(
							'{{WRAPPER}} .premium-drawable-icon *' => 'stroke: {{VALUE}};',
						),
					)
				);

				$this->add_control(
					'svg_color',
					array(
						'label'     => __( 'After Draw Fill Color', 'premium-addons-for-elementor' ),
						'type'      => Controls_Manager::COLOR,
						'global'    => false,
						'separator' => 'after',
						'condition' => array(
							'draw_svg'           => 'yes',
							'enable_custom_icon' => 'yes',
							'icons_source'       => 'custom',
						),
					)
				);
			} else {
				Helper_Functions::get_draw_svg_notice(
					$this,
					'weather',
					array(
						'enable_custom_icon' => 'yes',
						'icons_source'       => 'custom',
					)
				);
			}
		}

		$this->end_controls_section();
	}

	private function add_helpful_info_section() {

		$this->start_controls_section(
			'section_pa_docs',
			array(
				'label' => __( 'Helpful Documentations', 'premium-addons-for-elementor' ),
			)
		);

		$docs = array(
			'https://premiumaddons.com/docs/elementor-weather-widget/' => __( 'Getting started »', 'premium-addons-for-elementor' ),
		);

		$doc_index = 1;
		foreach ( $docs as $url => $title ) {

			$doc_url = Helper_Functions::get_campaign_link( $url, 'editor-page', 'wp-editor', 'get-support' );

			$this->add_control(
				'doc_' . $doc_index,
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => sprintf( '<a href="%s" target="_blank">%s</a>', $doc_url, $title ),
					'content_classes' => 'editor-pa-doc',
				)
			);

			$doc_index++;

		}

		$this->end_controls_section();

	}

	/** Style Controls.*/

	/**
	 * Adds posts title style controls.
	 *
	 * @access private
	 * @since 4.9.37
	 */
	private function add_outer_container_style() {

		$this->start_controls_section(
			'pa_weather_outer',
			array(
				'label' => __( 'Container', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'outer_shadow',
				'selector' => '{{WRAPPER}} .premium-weather__outer-wrapper',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'outer_bg',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-weather__outer-wrapper',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'outer_border',
				'selector' => '{{WRAPPER}} .premium-weather__outer-wrapper',
			)
		);

		$this->add_control(
			'outer_border_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__outer-wrapper'  => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};;',
				),
			)
		);

		$this->add_responsive_control(
			'outer_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__outer-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function add_city_style() {

		$this->start_controls_section(
			'pa_weather_city_style',
			array(
				'label'     => __( 'Title', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_city'            => 'yes',
					'title!'               => '',
					'show_current_weather' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'city_name',
				'selector' => '{{WRAPPER}} .premium-weather__city-name',
			)
		);

		$this->add_control(
			'city_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-weather__city-name' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'city_shadow',
				'selector' => '{{WRAPPER}} .premium-weather__city-name',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'city_bg',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-weather__city-name',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'city_border',
				'selector' => '{{WRAPPER}} .premium-weather__city-name',
			)
		);

		$this->add_control(
			'city_border_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__city-name'  => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'city_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__city-name' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'city_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__city-name' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function add_current_weather_style() {

		$this->start_controls_section(
			'pa_weather_current',
			array(
				'label'     => __( 'Current Weather', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_current_weather' => 'yes',
				),
			)
		);

		$this->start_controls_tabs( 'current_weather_tabs' );

		$this->start_controls_tab(
			'pa_current_tab',
			array(
				'label' => __( 'Current', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'pa_current_temp',
			array(
				'label' => esc_html__( 'Temperature', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'temp',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{WRAPPER}}:not(.premium-weather__layout-3) .premium-weather__basic-weather .premium-weather__temp-wrapper *,
                            {{WRAPPER}}.premium-weather__layout-3 .premium-weather__extra-outer-wrapper .premium-weather__temp-wrapper *' => 'font-family:"{{VALUE}}", Sans-serif;',
						),
					),
				),
				'selector'       => '{{WRAPPER}}:not(.premium-weather__layout-3) .premium-weather__basic-weather .premium-weather__temp-wrapper .premium-weather__temp-val,
                {{WRAPPER}}.premium-weather__layout-3 .premium-weather__extra-outer-wrapper .premium-weather__temp-wrapper .premium-weather__temp-val',
			)
		);

		$this->add_control(
			'temp_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}}:not(.premium-weather__layout-3) .premium-weather__basic-weather .premium-weather__temp-wrapper, {{WRAPPER}}.premium-weather__layout-3 .premium-weather__extra-outer-wrapper .premium-weather__temp-wrapper' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'current_temp_icon_color',
			array(
				'label'     => __( 'Icon Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}:not(.premium-weather__layout-3) .premium-weather__basic-weather .premium-weather__temp-wrapper > svg *,
                    {{WRAPPER}}.premium-weather__layout-3 .premium-weather__extra-outer-wrapper .premium-weather__temp-wrapper > svg *' => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'show_temp_icon' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'current_temp_icon_size',
			array(
				'label'      => __( 'Icon Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}}:not(.premium-weather__layout-3) .premium-weather__basic-weather .premium-weather__temp-wrapper > svg,
                    {{WRAPPER}}.premium-weather__layout-3 .premium-weather__extra-outer-wrapper .premium-weather__temp-wrapper > svg'  => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'show_temp_icon' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'current_temp_margin',
			array(
				'label'      => __( 'Icon Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}}:not(.premium-weather__layout-3) .premium-weather__basic-weather .premium-weather__temp-wrapper > svg,
                    {{WRAPPER}}.premium-weather__layout-3 .premium-weather__extra-outer-wrapper .premium-weather__temp-wrapper > svg' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'show_temp_icon' => 'yes',
				),
			)
		);

		$this->add_control(
			'pa_current_temp_unit',
			array(
				'label'     => esc_html__( 'Temperature Unit', 'premium-addons-for-elementor' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_responsive_control(
			'temp_unit_typo',
			array(
				'label'      => __( 'Font Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__temp-unit'  => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'temp_unit_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__temp-unit' => 'top: {{TOP}}{{UNIT}}; right: {{RIGHT}}{{UNIT}}; bottom: {{BOTTOM}}{{UNIT}}; left:{{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'pa_current_desc',
			array(
				'label'     => esc_html__( 'Description', 'premium-addons-for-elementor' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'desc_typo',
				'selector' => '{{WRAPPER}} .premium-weather__desc',
			)
		);

		$this->add_control(
			'desc_color',
			array(
				'label'     => __( 'Description Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-weather__desc' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'desc_feels_typo',
				'label'    => __( 'Feels Like Typography', 'premium-addons-for-elementor' ),
				'selector' => '{{WRAPPER}} .premium-weather__feels-like',
			)
		);

		$this->add_control(
			'desc_feels_color',
			array(
				'label'     => __( 'Feels Like Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-weather__feels-like' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'temp_icon_color',
			array(
				'label'     => __( 'Icon Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-weather__basic-weather .premium-weather__icon-wrapper > svg,
					{{WRAPPER}} .premium-weather__basic-weather .premium-weather__icon-wrapper > svg *' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .premium-weather__basic-weather .premium-weather__icon-wrapper' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'temp_icon_size',
			array(
				'label'      => __( 'Icon Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
				),
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__basic-weather .premium-weather__icon-wrapper > svg,
					{{WRAPPER}} .premium-weather__basic-weather .premium-weather__icon-wrapper .premium-lottie-animation,
					{{WRAPPER}} .premium-weather__basic-weather .premium-weather__icon-wrapper img'  => 'width: {{SIZE}}{{UNIT}}; height:{{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .premium-weather__basic-weather .premium-weather__icon-wrapper i' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$papro_activated = apply_filters( 'papro_activated', false );

		if ( $papro_activated ) {
			$this->add_control(
				'temp_icon_stroke',
				array(
					'label'     => __( 'Stroke Color', 'premium-addons-for-elementor' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .premium-weather__basic-weather .premium-weather__icon-wrapper .premium-lottie-animation svg *' => 'stroke: {{VALUE}} !important',
					),
					'condition' => array(
						'enable_custom_icon' => 'yes',
						'icons_source'       => 'default',
						'lottie_type'        => 'outlined',
					),
				)
			);

			$this->add_responsive_control(
				'temp_stork_width',
				array(
					'label'      => __( 'Stroke Width', 'premium-addons-for-elementor' ),
					'type'       => Controls_Manager::SLIDER,
					'range'      => array(
						'px' => array(
							'min' => 0,
							'max' => 1000,
						),
					),
					'size_units' => array( 'px' ),
					'selectors'  => array(
						'{{WRAPPER}} .premium-weather__basic-weather .premium-weather__icon-wrapper .premium-lottie-animation svg *' => 'stroke-width: {{SIZE}}{{UNIT}} !important;',
					),
					'condition'  => array(
						'enable_custom_icon' => 'yes',
						'icons_source'       => 'default',
						'lottie_type'        => 'outlined',
					),
				)
			);

		}

		$this->add_responsive_control(
			'temp_icon_margin',
			array(
				'label'      => __( 'Icon Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__basic-weather .premium-weather__icon-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'desc_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'separator'  => 'before',
				'selectors'  => array(
					'{{WRAPPER}}:not(.premium-weather__layout-3) .premium-weather__desc-wrapper, {{WRAPPER}}.premium-weather__layout-3 .premium-weather__feels-like' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pa_extra_tab',
			array(
				'label'     => __( 'Extra Weather', 'premium-addons-for-elementor' ),
				'condition' => array(
					'show_extra_info' => 'yes',
				),
			)
		);

		$this->add_extra_weather_style();

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'pa_current_conatainer',
			array(
				'label'     => esc_html__( 'Container', 'premium-addons-for-elementor' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'current_shadow',
				'selector' => '{{WRAPPER}} .premium-weather__current-weather',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'current_bg',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-weather__current-weather',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'current_border',
				'selector' => '{{WRAPPER}} .premium-weather__current-weather',
			)
		);

		$this->add_control(
			'current_border_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__current-weather'  => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'current_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__current-weather' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'current_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__current-weather' => 'Margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function add_extra_weather_style() {
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'extra_weather_name',
				'selector' => '{{WRAPPER}} .premium-weather__extra-weather span',
			)
		);

		$this->add_control(
			'extra_weather_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-weather__extra-weather span' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'extra_weather_icon_color',
			array(
				'label'     => __( 'Icons Colors', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-weather__extra-weather i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .premium-weather__extra-weather svg, {{WRAPPER}} .premium-weather__extra-weather svg path' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'extra_weather_bg',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-weather__extra-weather > div',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'extra_weather_border',
				'selector' => '{{WRAPPER}} .premium-weather__extra-weather > div',
			)
		);

		$this->add_control(
			'extra_weather_border_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__extra-weather > div'  => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'extra_weather_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__extra-weather > div' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'extra_weather_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__extra-weather > div' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
	}

	private function add_forecast_style() {

		$this->start_controls_section(
			'pa_weather_forecast_style',
			array(
				'label'      => __( 'Daily Forecast', 'premium-addons-for-elementor' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'terms' => array(
						array(
							'name'  => 'enable_forecast',
							'value' => 'yes',
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'     => 'forecast_tabs',
									'operator' => '!==',
									'value'    => 'yes',
								),
								array(
									'terms' => array(
										array(
											'name'  => 'forecast_tabs',
											'value' => 'yes',
										),
										array(
											'name'     => 'forecast_days',
											'operator' => 'in',
											'value'    => array( '1', '6', '7', '8' ),
										),
									),
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'pa_forecast_date',
			array(
				'label'     => esc_html__( 'Date', 'premium-addons-for-elementor' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'forecast_date',
				'selector' => '{{WRAPPER}} .premium-weather__forecast-item-date',
			)
		);

		$this->add_control(
			'forecast_date_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-weather__forecast-item-date' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'forecast_date_margin',
			array(
				'label'      => __( 'Bottom Spacing', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__forecast-item-date'  => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'layout' => 'layout-1',
				),
			)
		);

		$this->add_control(
			'pa_forecast_temp_max',
			array(
				'label'     => esc_html__( 'Max Temperatrue', 'premium-addons-for-elementor' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'pa_forecast_temp_max_typo',
				'selector' => '{{WRAPPER}} .premium-weather__forecast-item .premium-weather__temp-max',
			)
		);

		$this->add_control(
			'pa_forecast_temp_max_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-weather__forecast-item .premium-weather__temp-max' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'max_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__forecast-item .premium-weather__temp-max' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'pa_forecast_temp_min',
			array(
				'label'     => esc_html__( 'Min Temepratrue', 'premium-addons-for-elementor' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'pa_forecast_temp_min_typo',
				'selector' => '{{WRAPPER}} .premium-weather__forecast-item .premium-weather__temp-min',
			)
		);

		$this->add_control(
			'pa_forecast_temp_min_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-weather__forecast-item .premium-weather__temp-min' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'paa_forecast_temp_icon_color',
			array(
				'label'     => __( 'Icon Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .premium-weather__forecast-item-data > span > svg,
					{{WRAPPER}} .premium-weather__forecast-item-data > span > svg *' => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'show_temp_icon' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'pa_forecast_temp_icon_size',
			array(
				'label'      => __( 'Icon Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__forecast-item-data > span > svg'  => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'show_temp_icon' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'pa_forecast_temp_margin',
			array(
				'label'      => __( 'Icon Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__forecast-item-data > span > svg' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'show_temp_icon' => 'yes',
				),
			)
		);

		$this->add_control(
			'forecast_icon',
			array(
				'label'     => esc_html__( 'Weather Icon', 'premium-addons-for-elementor' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
				'condition' => array(
					'show_forecast_icon' => 'yes',
				),
			)
		);

		$this->add_control(
			'forecast_icon_color',
			array(
				'label'     => __( 'Icons Colors', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-weather__forecast-item .premium-weather__icon-wrapper > svg,
					 {{WRAPPER}} .premium-weather__forecast-item .premium-weather__icon-wrapper > svg *' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .premium-weather__forecast-item .premium-weather__icon-wrapper' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'show_forecast_icon' => 'yes',
				),
			)
		);

		$papro_activated = apply_filters( 'papro_activated', false );

		if ( $papro_activated ) {
			$this->add_control(
				'forecast_icon_stroke',
				array(
					'label'     => __( 'Stroke Color', 'premium-addons-for-elementor' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .premium-weather__forecast-item .premium-weather__icon-wrapper .premium-lottie-animation svg *' => 'stroke: {{VALUE}} !important',
					),
					'condition' => array(
						'enable_custom_icon' => 'yes',
						'icons_source'       => 'default',
						'lottie_type'        => 'outlined',
					),
				)
			);

			$this->add_responsive_control(
				'forecast_stork_width',
				array(
					'label'      => __( 'Stroke Width', 'premium-addons-for-elementor' ),
					'type'       => Controls_Manager::SLIDER,
					'range'      => array(
						'px' => array(
							'min' => 0,
							'max' => 1000,
						),
					),
					'size_units' => array( 'px' ),
					'selectors'  => array(
						'{{WRAPPER}} .premium-weather__forecast-item .premium-weather__icon-wrapper .premium-lottie-animation svg *' => 'stroke-width: {{SIZE}}{{UNIT}} !important;',
					),
					'condition'  => array(
						'enable_custom_icon' => 'yes',
						'icons_source'       => 'default',
						'lottie_type'        => 'outlined',
					),
				)
			);

		}

		$this->add_responsive_control(
			'forecast_icon_size',
			array(
				'label'      => __( 'Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__forecast-item .premium-weather__icon-wrapper > svg,
					{{WRAPPER}} .premium-weather__forecast-item .premium-weather__icon-wrapper .premium-lottie-animation,
					{{WRAPPER}} .premium-weather__forecast-item .premium-weather__icon-wrapper img'  => 'width:{{SIZE}}{{UNIT}}; height:{{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .premium-weather__forecast-item .premium-weather__icon-wrapper i' => 'font-size:{{SIZE}}px',
				),
				'condition'  => array(
					'show_forecast_icon' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'icon_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__forecast-item .premium-weather__icon-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'layout'             => 'layout-1',
					'show_forecast_icon' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'icon_b_spacing',
			array(
				'label'      => __( 'Bottom Spacing', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__forecast-item .premium-weather__icon-wrapper'  => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'layout'             => 'layout-3',
					'show_forecast_icon' => 'yes',
				),
			)
		);

		$this->add_control(
			'pa_forecast_cont',
			array(
				'label'     => esc_html__( 'Item Container', 'premium-addons-for-elementor' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'pa_forecast_cont_shadow',
				'selector' => '{{WRAPPER}} .premium-weather__forecast-item',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'forecast_bg',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-weather__forecast-item',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'forecast_border',
				'selector' => '{{WRAPPER}} .premium-weather__forecast-item',
			)
		);

		$this->add_control(
			'forecast_border_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__forecast-item'  => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'forecast_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__forecast-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'forecast_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__forecast-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'pa_forecast_cont_outer',
			array(
				'label'     => esc_html__( 'Outer Container', 'premium-addons-for-elementor' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'pa_forecast_cont_outer_shadow',
				'selector' => '{{WRAPPER}} .premium-weather__forecast',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'outer_forecast_bg',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-weather__forecast',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'forecast__outer_border',
				'selector' => '{{WRAPPER}} .premium-weather__forecast',
			)
		);

		$this->add_control(
			'forecast_border_outer_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__forecast'  => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'forecast_outer_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__forecast' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'forecast_outer_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__forecast' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function add_hourly_style() {

		$this->start_controls_section(
			'pa_weather_hourly_style',
			array(
				'label'     => __( 'Hourly Forecast', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'enable_hourly' => 'yes',
				),
			)
		);

		$this->add_control(
			'pa_hourly_date',
			array(
				'label'     => esc_html__( 'Hours', 'premium-addons-for-elementor' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'hourly_date',
				'selector' => '{{WRAPPER}} .premium-weather__hourly-item-date',
			)
		);

		$this->add_control(
			'hourly_date_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-weather__hourly-item-date' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'hourly_date_margin',
			array(
				'label'      => __( 'Bottom Spacing', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__hourly-item-date'  => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'hourly_layout' => 'default',
				),
			)
		);

		$this->add_control(
			'pa_hourly_temp_max',
			array(
				'label'     => esc_html__( 'Weather Details', 'premium-addons-for-elementor' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'pa_hourly_temp_max_typo',
				'selector' => '{{WRAPPER}} .premium-weather__hourly-item .premium-weather__hourly-data > *:not(.premium-weather__icon-wrapper) > span,
				{{WRAPPER}}:not(.premium-hours-vertical) .premium-weather__hourly-item .premium-weather__temp-wrapper > span',
			)
		);

		$this->add_control(
			'pa_hourly_temp_max_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-weather__hourly-item .premium-weather__hourly-data > *:not(.premium-weather__icon-wrapper) > span,
					 {{WRAPPER}}:not(.premium-hours-vertical) .premium-weather__hourly-item .premium-weather__temp-wrapper > span' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pa_hourly_temp_icon_color',
			array(
				'label'     => __( 'Icon Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .premium-weather__hourly-item .premium-weather__hourly-data > *:not(.premium-weather__icon-wrapper) svg,
					 {{WRAPPER}}:not(.premium-hours-vertical) .premium-weather__hourly-item .premium-weather__temp-wrapper svg,
					{{WRAPPER}} .premium-weather__hourly-item .premium-weather__hourly-data > *:not(.premium-weather__icon-wrapper) svg *,
					 {{WRAPPER}}:not(.premium-hours-vertical) .premium-weather__hourly-item .premium-weather__temp-wrapper svg *' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .premium-weather__hourly-item .premium-weather__hourly-data i' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'pa_hourly_temp_icon_size',
			array(
				'label'      => __( 'Icon Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__hourly-item .premium-weather__hourly-data > *:not(.premium-weather__icon-wrapper) svg, {{WRAPPER}}:not(.premium-hours-vertical) .premium-weather__hourly-item .premium-weather__temp-wrapper svg'  => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .premium-weather__hourly-item .premium-weather__hourly-data i'  => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pa_hourly_temp_margin',
			array(
				'label'      => __( 'Icon Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__hourly-item .premium-weather__hourly-data > *:not(.premium-weather__icon-wrapper) svg, {{WRAPPER}} .premium-weather__hourly-item .premium-weather__hourly-data i, {{WRAPPER}}:not(.premium-hours-vertical) .premium-weather__hourly-item .premium-weather__temp-wrapper svg' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'hourly_icon',
			array(
				'label'     => esc_html__( 'Weather Icon', 'premium-addons-for-elementor' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'hourly_icon_color',
			array(
				'label'     => __( 'Icons Colors', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-weather__hourly-item .premium-weather__icon-wrapper > svg,
					 {{WRAPPER}} .premium-weather__hourly-item .premium-weather__icon-wrapper > svg *' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .premium-weather__hourly-item .premium-weather__icon-wrapper' => 'color: {{VALUE}}',
				),
			)
		);

		$papro_activated = apply_filters( 'papro_activated', false );

		if ( $papro_activated ) {
			$this->add_control(
				'hourly_icon_stroke',
				array(
					'label'     => __( 'Stroke Color', 'premium-addons-for-elementor' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .premium-weather__hourly-item .premium-weather__icon-wrapper .premium-lottie-animation svg *' => 'stroke: {{VALUE}} !important',
					),
					'condition' => array(
						'enable_custom_icon' => 'yes',
						'icons_source'       => 'default',
						'lottie_type'        => 'outlined',
					),
				)
			);

			$this->add_responsive_control(
				'hourly_stork_width',
				array(
					'label'      => __( 'Stroke Width', 'premium-addons-for-elementor' ),
					'type'       => Controls_Manager::SLIDER,
					'range'      => array(
						'px' => array(
							'min' => 0,
							'max' => 1000,
						),
					),
					'size_units' => array( 'px' ),
					'selectors'  => array(
						'{{WRAPPER}} .premium-weather__hourly-item .premium-weather__icon-wrapper .premium-lottie-animation svg *' => 'stroke-width: {{SIZE}}{{UNIT}} !important;',
					),
					'condition'  => array(
						'enable_custom_icon' => 'yes',
						'icons_source'       => 'default',
						'lottie_type'        => 'outlined',
					),
				)
			);
		}

		$this->add_responsive_control(
			'hourly_icon_size',
			array(
				'label'      => __( 'Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
				),
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__hourly-item .premium-weather__icon-wrapper > svg,
					{{WRAPPER}} .premium-weather__hourly-item .premium-weather__icon-wrapper .premium-lottie-animation,
					{{WRAPPER}} .premium-weather__hourly-item .premium-weather__icon-wrapper img'  => 'width:{{SIZE}}{{UNIT}}; height:{{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .premium-weather__hourly-item .premium-weather__icon-wrapper i' => 'font-size: {{SIZE}}px',
				),
			)
		);

		$this->add_responsive_control(
			'hourly_icon_margin',
			array(
				'label'      => __( 'Bottom Spacing', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__hourly-item .premium-weather__icon-wrapper'  => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'hourly_layout' => 'default',
				),
			)
		);

		$this->add_control(
			'pa_hourly_cont',
			array(
				'label'     => esc_html__( 'Item Container', 'premium-addons-for-elementor' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'pa_hourly_cont_shadow',
				'selector' => '{{WRAPPER}} .premium-weather__hourly-item, {{WRAPPER}}.premium-weather__layout-2 .premium-weather__extra-weather',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'hourly_bg',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-weather__hourly-item, {{WRAPPER}}.premium-weather__layout-2 .premium-weather__extra-weather',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'hourly_border',
				'selector' => '{{WRAPPER}} .premium-weather__hourly-item, {{WRAPPER}}.premium-weather__layout-2 .premium-weather__extra-weather',
			)
		);

		$this->add_control(
			'hourly_border_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__hourly-item, {{WRAPPER}}.premium-weather__layout-2 .premium-weather__extra-weather'  => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'hourly_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__hourly-item, {{WRAPPER}}.premium-weather__layout-2 .premium-weather__extra-weather' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'hourly_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__hourly-item, {{WRAPPER}}.premium-weather__layout-2 .premium-weather__extra-weather' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'pa_hourly_cont_outer',
			array(
				'label'     => esc_html__( 'Outer Container', 'premium-addons-for-elementor' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'pa_hourly_cont_outer_shadow',
				'selector' => '{{WRAPPER}}:not(.premium-weather__layout-2) .premium-weather__hourly-forecast-wrapper, {{WRAPPER}}.premium-weather__layout-2 .premium-weather__extra-outer-wrapper',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'outer_hourly_bg',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}}:not(.premium-weather__layout-2) .premium-weather__hourly-forecast-wrapper, {{WRAPPER}}.premium-weather__layout-2 .premium-weather__extra-outer-wrapper',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'hourly__outer_border',
				'selector' => '{{WRAPPER}}:not(.premium-weather__layout-2) .premium-weather__hourly-forecast-wrapper, {{WRAPPER}}.premium-weather__layout-2 .premium-weather__extra-outer-wrapper',
			)
		);

		$this->add_control(
			'hourly_border_outer_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}}:not(.premium-weather__layout-2) .premium-weather__hourly-forecast-wrapper, {{WRAPPER}}.premium-weather__layout-2 .premium-weather__extra-outer-wrapper'  => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'hourly_outer_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}}:not(.premium-weather__layout-2) .premium-weather__hourly-forecast-wrapper, {{WRAPPER}}.premium-weather__layout-2 .premium-weather__extra-outer-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'hourly_outer_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}}:not(.premium-weather__layout-2) .premium-weather__hourly-forecast-wrapper, {{WRAPPER}}.premium-weather__layout-2 .premium-weather__extra-outer-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function add_navigation_style() {

		$this->start_controls_section(
			'pa_nav_style',
			array(
				'label'      => __( 'Carousel Arrows', 'premium-addons-for-elementor' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'enable_hourly',
							'value' => 'yes',
						),
						array(
							'terms' => array(
								array(
									'name'  => 'enable_forecast',
									'value' => 'yes',
								),
								array(
									'name'  => 'forecast_carousel_sw',
									'value' => 'yes',
								),
								array(
									'name'     => 'forecast_layouts',
									'operator' => '!==',
									'value'    => 'style-4',
								),
							),
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'nav_icon_size',
			array(
				'label'      => __( 'Icon Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .carousel-arrow i'   => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .carousel-arrow svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'nav_arrow_height',
			array(
				'label'      => __( 'Arrow Height', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .carousel-arrow' => 'height: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'layout',
							'value' => 'layout-2',
						),
						array(
							'terms' => array(
								array(
									'name'     => 'layout',
									'operator' => '!==',
									'value'    => 'layout-2',
								),
								array(
									'name'  => 'hourly_layout',
									'value' => 'default',
								),
							),
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'nav_arrow_pos',
			array(
				'label'      => __( 'Vertical Position', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .carousel-arrow' => 'top: {{SIZE}}px;',
				),
				'condition'  => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'layout',
							'value' => 'layout-2',
						),
						array(
							'terms' => array(
								array(
									'name'     => 'layout',
									'operator' => '!==',
									'value'    => 'layout-2',
								),
								array(
									'name'  => 'hourly_layout',
									'value' => 'default',
								),
							),
						),
					),
				),
			)
		);

		$this->start_controls_tabs( 'nav_colors' );

		$this->start_controls_tab(
			'pa_nav_nomral',
			array(
				'label' => __( 'Normal', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'pa_hourly_arrows_heading',
			array(
				'label'     => esc_html__( 'Hourly Forecast Arrows', 'premium-addons-for-elementor' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'pa_nav_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#171717',
				'selectors' => array(
					'{{WRAPPER}} .premium-weather__extra-outer-wrapper .carousel-arrow, {{WRAPPER}} .premium-weather__hourly-forecast-wrapper .carousel-arrow' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pa_nav_bg',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#F0F0F0',
				'selectors' => array(
					'{{WRAPPER}} .premium-weather__extra-outer-wrapper .carousel-arrow, {{WRAPPER}} .premium-weather__hourly-forecast-wrapper .carousel-arrow' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pa_nav_border',
				'selector' => '{{WRAPPER}} .premium-weather__extra-outer-wrapper .carousel-arrow, {{WRAPPER}} .premium-weather__hourly-forecast-wrapper .carousel-arrow',
			)
		);

		$this->add_control(
			'pa_nav_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__extra-outer-wrapper .carousel-prev.carousel-arrow, {{WRAPPER}} .premium-weather__hourly-forecast-wrapper .carousel-prev.carousel-arrow' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .premium-weather__extra-outer-wrapper .carousel-next.carousel-arrow, {{WRAPPER}} .premium-weather__hourly-forecast-wrapper .carousel-next.carousel-arrow ' => 'border-radius: {{RIGHT}}{{UNIT}} {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'pa_daily_arrows_heading',
			array(
				'label'     => esc_html__( 'Daily Forecast Arrows', 'premium-addons-for-elementor' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'pa_daily_nav_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#171717',
				'selectors' => array(
					'{{WRAPPER}} .premium-weather__forecast .carousel-arrow' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pa_daily_nav_bg',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#F0F0F0',
				'selectors' => array(
					'{{WRAPPER}} .premium-weather__forecast .carousel-arrow' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pa_daily_nav_border',
				'selector' => '{{WRAPPER}} .premium-weather__forecast .carousel-arrow',
			)
		);

		$this->add_control(
			'pa_daily_nav_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__forecast .carousel-prev.carousel-arrow' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .premium-weather__forecast .carousel-next.carousel-arrow' => 'border-radius: {{RIGHT}}{{UNIT}} {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pa_nav_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-for-elementor' ),

			)
		);

		$this->add_control(
			'pa_arrows_heading_hov',
			array(
				'label'     => esc_html__( 'Hourly Forecast Arrows', 'premium-addons-for-elementor' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'pa_nav_color_hov',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-weather__extra-outer-wrapper .carousel-arrow:hover, {{WRAPPER}} .premium-weather__hourly-forecast-wrapper .carousel-arrow:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pa_nav_bg_hov',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-weather__extra-outer-wrapper .carousel-arrow:hover, {{WRAPPER}} .premium-weather__hourly-forecast-wrapper .carousel-arrow:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pa_nav_border_hov',
				'selector' => '{{WRAPPER}} .premium-weather__extra-outer-wrapper .carousel-arrow:hover, {{WRAPPER}} .premium-weather__hourly-forecast-wrapper .carousel-arrow:hover',
			)
		);

		$this->add_control(
			'pa_nav_border_radius_hov',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__extra-outer-wrapper .carousel-prev.carousel-arrow:hover, {{WRAPPER}} .premium-weather__hourly-forecast-wrapper .carousel-prev.carousel-arrow:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .premium-weather__extra-outer-wrapper .carousel-next.carousel-arrow:hover, {{WRAPPER}} .premium-weather__hourly-forecast-wrapper .carousel-next.carousel-arrow:hover' => 'border-radius: {{RIGHT}}{{UNIT}} {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'pa_daily_arrows_heading_hov',
			array(
				'label'     => esc_html__( 'Daily Forecast Arrows', 'premium-addons-for-elementor' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'pa_daily_nav_color_hov',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-weather__forecast .carousel-arrow:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pa_daily_nav_bg_hov',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-weather__forecast .carousel-arrow:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pa_daily_nav_border_hov',
				'selector' => '{{WRAPPER}} .premium-weather__forecast .carousel-arrow:hover',
			)
		);

		$this->add_control(
			'pa_daily_nav_border_radius_hov',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__forecast .carousel-prev.carousel-arrow:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .premium-weather__forecast .carousel-next.carousel-arrow:hover' => 'border-radius: {{RIGHT}}{{UNIT}} {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'pa_nav_padding',
			array(
				'label'      => __( 'Hourly Arrows Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'separator'  => 'before',
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__extra-outer-wrapper .carousel-arrow, {{WRAPPER}} .premium-weather__hourly-forecast-wrapper .carousel-arrow' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pa_daily_nav_padding',
			array(
				'label'      => __( 'Daily Arrows Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'separator'  => 'before',
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__forecast .carousel-arrow' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function add_tabs_style() {

		$this->start_controls_section(
			'pa_weather_tabs_style_section',
			array(
				'label'     => __( 'Forecast Tabs', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'enable_forecast' => 'yes',
					'forecast_tabs'   => 'yes',
					'forecast_days!'  => array( '1', '6', '7', '8' ),
				),
			)
		);

		$this->add_control(
			'pa_weather_tabs',
			array(
				'label'     => esc_html__( 'Tabs', 'premium-addons-for-elementor' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'pa_weather_tabs_typo',
				'selector' => '{{WRAPPER}} .premium-weather__tab-header',
			)
		);

		$this->start_controls_tabs( 'pa_weather_tabs_style' );

		$this->start_controls_tab(
			'pa_weather_tabs_style_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'pa_weather_tabs_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-weather__tab-header' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'pa_weather_tabs_bg',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-weather__tab-header',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pa_weather_tabs_border',
				'selector' => '{{WRAPPER}} .premium-weather__tab-header',
			)
		);

		$this->add_control(
			'pa_weather_tabs_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__tab-header' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pa_weather_tabs_style_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'pa_weather_tabs_color_hov',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-weather__tab-header:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'pa_weather_tabs_bg_hov',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-weather__tab-header:hover',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pa_weather_tabs_border_hov',
				'selector' => '{{WRAPPER}} .premium-weather__tab-header:hover',
			)
		);

		$this->add_control(
			'pa_weather_tabs_border_radius_hov',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__tab-header:hover' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pa_weather_tabs_style_active',
			array(
				'label' => __( 'Active', 'premium-addons-for-elementor' ),
			)
		);
		$this->add_control(
			'pa_weather_tabs_color_active',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-weather__tab-header.current' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'pa_weather_tabs_bg_active',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-weather__tab-header.current',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pa_weather_tabs_border_active',
				'selector' => '{{WRAPPER}} .premium-weather__tab-header.current',
			)
		);

		$this->add_control(
			'pa_weather_tabs_border_radius_active',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__tab-header.current' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'pa_weather_tabs_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'separator'  => 'before',
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__tab-header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pa_weather_tabs_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__tab-header' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'pa_weather_tabs_icons',
			array(
				'label'     => esc_html__( 'Icons Row', 'premium-addons-for-elementor' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'pa_weather_tabs_icon_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-weather__weather-indicator i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .premium-weather__weather-indicator svg,
                    {{WRAPPER}} .premium-weather__weather-indicator > svg *' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'pa_weather_tabs_icon_size',
			array(
				'label'     => __( 'Icon Size', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => array(
					'{{WRAPPER}} .premium-weather__weather-indicator i' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .premium-weather__weather-indicator svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'pa_weather_tabs_icon_bg',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-weather__weather-indicators',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pa_weather_tabs_icon_border',
				'selector' => '{{WRAPPER}} .premium-weather__weather-indicators',
			)
		);

		$this->add_responsive_control(
			'pa_weather_tabs_icon_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__weather-indicators' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pa_weather_tabs_icon_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__weather-indicators' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'pa_weather_tabs_date',
			array(
				'label'     => esc_html__( 'Hours', 'premium-addons-for-elementor' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'tabs_date',
				'selector' => '{{WRAPPER}} .premium-weather__hourly-item-date',
			)
		);

		$this->add_control(
			'tabs_date_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-weather__hourly-item-date' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pa_weather_tabs_details',
			array(
				'label'     => esc_html__( 'Weather Details', 'premium-addons-for-elementor' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'pa_weather_tabs_details_typo',
				'selector' => '{{WRAPPER}} .premium-weather__tab-content .premium-weather__hourly-item > *:not(.premium-weather__hourly-item-date):not(.premium-weather__icon-wrapper)',
			)
		);

		$this->add_control(
			'pa_weather_tabs_details_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-weather__tab-content .premium-weather__hourly-item > *:not(.premium-weather__hourly-item-date):not(.premium-weather__icon-wrapper)' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'pa_weather_tabs_details_bg',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-weather__hourly-item',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pa_weather_tabs_details_border',
				'selector' => '{{WRAPPER}} .premium-weather__hourly-item',
			)
		);

		$this->add_control(
			'pa_weather_tabs_details_border_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__hourly-item'  => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pa_weather_tabs_details_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__hourly-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pa_weather_tabs_details_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__hourly-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'tabs_forecast_icon',
			array(
				'label'     => esc_html__( 'Weather Icon', 'premium-addons-for-elementor' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'tabs_forecast_icon_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-weather__tab-content .premium-weather__icon-wrapper svg,
					 {{WRAPPER}} .premium-weather__tab-content .premium-weather__icon-wrapper svg *' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .premium-weather__tab-content .premium-weather__icon-wrapper' => 'color: {{VALUE}};',
				),
			)
		);

		$papro_activated = apply_filters( 'papro_activated', false );

		if ( $papro_activated ) {
			$this->add_control(
				'tabs_forecast_icon_stroke',
				array(
					'label'     => __( 'Stroke Color', 'premium-addons-for-elementor' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .premium-weather__tab-content .premium-weather__icon-wrapper svg *' => 'stroke: {{VALUE}} !important',
					),
					'condition' => array(
						'enable_custom_icon' => 'yes',
						'icons_source'       => 'default',
						'lottie_type'        => 'outlined',
					),
				)
			);

			$this->add_responsive_control(
				'tabs_forecast_icon_stroke_width',
				array(
					'label'      => __( 'Stroke Width', 'premium-addons-for-elementor' ),
					'type'       => Controls_Manager::SLIDER,
					'range'      => array(
						'px' => array(
							'min' => 0,
							'max' => 1000,
						),
					),
					'size_units' => array( 'px' ),
					'selectors'  => array(
						'{{WRAPPER}} .premium-weather__tab-content .premium-weather__icon-wrapper svg *' => 'stroke-width: {{SIZE}}{{UNIT}} !important;',
					),
					'condition'  => array(
						'enable_custom_icon' => 'yes',
						'icons_source'       => 'default',
						'lottie_type'        => 'outlined',
					),
				)
			);
		}

		$this->add_responsive_control(
			'tabs_forecast_icon_size',
			array(
				'label'      => __( 'Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__tab-content .premium-weather__icon-wrapper > svg,
					{{WRAPPER}} .premium-weather__tab-content .premium-weather__icon-wrapper .premium-lottie-animation,
					{{WRAPPER}} .premium-weather__tab-content .premium-weather__icon-wrapper img'  => 'width:{{SIZE}}{{UNIT}}; height:{{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .premium-weather__tab-content .premium-weather__icon-wrapper i' => 'font-size:{{SIZE}}px',
				),
			)
		);

		$this->add_control(
			'pa_weather_tabs_notice',
			array(
				'label'     => esc_html__( 'Notice', 'premium-addons-for-elementor' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'notice_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-weather__forecast-item-date' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pa_weather_tabs_cont',
			array(
				'label'     => esc_html__( 'Container', 'premium-addons-for-elementor' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'pa_weather_tabs_cont_shadow',
				'selector' => '{{WRAPPER}} .premium-weather__forecast-tabs-wrapper',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'pa_weather_tabs_cont_bg',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-weather__forecast-tabs-wrapper',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pa_weather_tabs_cont_border',
				'selector' => '{{WRAPPER}} .premium-weather__forecast-tabs-wrapper',
			)
		);

		$this->add_control(
			'pa_weather_tabs_cont_border_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__forecast-tabs-wrapper'  => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pa_weather_tabs_cont_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__forecast-tabs-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pa_weather_tabs_cont_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__forecast-tabs-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render weather widget output on the frontend.
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings();

		$papro_activated = apply_filters( 'papro_activated', false );

		if ( ! $papro_activated ) {

			$settings['forecast_carousel_sw'] = false;
			$settings['forecast_days']        = false;
			$settings['forecast_tabs']        = false;

			if ( 'yes' === $settings['enable_custom_icon'] || 'yes' === $settings['enable_forecast'] || 'layout-3' === $settings['layout'] || 'coords' === $settings['custom_location_type'] ) {

				?>
				<div class="premium-error-notice">
					<?php
						$message = __( 'This option is available in <b>Premium Addons Pro</b>.', 'premium-addons-for-elementor' );
						echo wp_kses_post( $message );
					?>
				</div>
				<?php
				return false;

			}
		}

		$this->settings = $settings;

		$id = $this->get_id();

		$api_key = $settings['api_key'];

		if ( empty( $api_key ) ) {
			$notice = __( 'Please enter a valid API key.', 'premium-addons-for-elementor' );

			?>
				<div class="premium-error-notice">
					<?php echo wp_kses_post( $notice ); ?>
				</div>
			<?php
			return;
		}

		$is_edit_mode = \Elementor\Plugin::$instance->editor->is_edit_mode();

		$location_type = $settings['location_type'];

		$forecast = 'yes' === $settings['enable_forecast'] ? true : false;

		$hourly_forecast = 'yes' === $settings['enable_hourly'] ? true : false;

		$api_settings = array(
			'api_key'       => $api_key,
			'location_type' => $location_type,
			'unit'          => $settings['unit'],
			'lang'          => $settings['lang'],
			'forecast'      => $forecast,
			'hourly'        => $hourly_forecast,
		);

		if ( $forecast ) {
			$api_settings['forecast_tabs'] = 'yes' === $settings['forecast_tabs'] && ! in_array( $settings['forecast_days'], array( '1', '6', '7', '8' ), true ) ? true : false;
		}

		if ( 'custom' === $location_type ) {

			$custom_loc_type = $settings['custom_location_type'];

			$api_settings['custom_location_type'] = $custom_loc_type;

			if ( 'name' === $custom_loc_type ) {

				$city_name = $settings['city_name'];

				if ( empty( $city_name ) ) {
					$notice = __( 'Please Enter a Valid City Name.', 'premium-addons-for-elementor' );

					?>
						<div class="premium-error-notice">
							<?php echo wp_kses_post( $notice ); ?>
						</div>
					<?php
					return;
				}

				$api_settings['city_name'] = $city_name;

			} else {

				$lat  = $settings['lat_coord'];
				$long = $settings['long_coord'];

				if ( empty( $lat ) || empty( $long ) ) {
					$notice = __( 'Please Enter Valid Latitude & Longitude Coordinates.', 'premium-addons-for-elementor' );

					?>
						<div class="premium-error-notice">
							<?php echo wp_kses_post( $notice ); ?>
						</div>
					<?php
					return;
				}

				$api_settings['lat']  = $lat;
				$api_settings['long'] = $long;
			}

			$transient_name = sprintf( 'pa_weather_%s_%s', $api_key, $id );

			if ( $is_edit_mode ) {
				$weather_data = false;
			} else {
				$weather_data = get_transient( $transient_name );
			}

			if ( ! $weather_data ) {

				$api_handler = new Pa_Weather_Handler( $api_settings );

				$weather_data = $api_handler::get_weather_data();

				// if ( isset( $weather_data['status'] ) && ! $weather_data['status'] ) {
				if ( isset( $weather_data['cod'] ) ) {

					$notice = __( 'Something Went Wrong, Please make sure you\'ve entered valid data, CODE:', 'premium-addons-for-elementor' ) . $weather_data['cod'];

					?>
						<div class="premium-error-notice">
							<?php echo wp_kses_post( $notice ); ?>
						</div>
					<?php
					return;
				}

				$expire_time = HOUR_IN_SECONDS * $settings['reload'];

				set_transient( $transient_name, $weather_data, $expire_time );
			}
		} else {

			$api_handler = new Pa_Weather_Handler( $api_settings );

			$weather_data = $api_handler::get_weather_data();

			// if ( isset( $weather_data['status'] ) && ! $weather_data['status'] ) {
			if ( isset( $weather_data['cod'] ) ) {

				$notice = __( 'Something Went Wrong, Please make sure you\'ve entered valid data, CODE:', 'premium-addons-for-elementor' ) . $weather_data['cod'];

				?>
					<div class="premium-error-notice">
						<?php echo wp_kses_post( $notice ); ?>
					</div>
				<?php
				return;
			}
		}

		$this->render_weather_layout( $weather_data, $settings );
	}

	/**
	 * Render Weather Layout.
	 *
	 * @access private
	 * @since 2.8.23
	 *
	 * @param array $weather_data  weather data.
	 * @param array $settings widget settings.
	 */
	private function render_weather_layout( $weather_data, $settings ) {

		$layout                 = $settings['layout'];
		$show_temp_icon         = 'yes' === $settings['show_temp_icon'] ? true : false;
		$show_current_weather   = 'yes' === $settings['show_current_weather'] ? true : false;
		$current                = $weather_data['current'];
		$forecast               = 'yes' === $settings['enable_forecast'] ? true : false;
		$forecast_icon          = $forecast && 'yes' === $settings['show_forecast_icon'] ? true : false;
		$hourly_forecast        = 'yes' === $settings['enable_hourly'] ? $weather_data['hourly'] : false;
		$daily_carousel         = 'yes' === $settings['forecast_carousel_sw'] ? true : false;
		$show_city              = 'yes' === $settings['show_city'] ? true : false;
		$title                  = $show_city && ! empty( $settings['title'] ) ? $settings['title'] : false;
		$extra_weather          = 'yes' === $settings['show_extra_info'] && is_array( $settings['pa_extra_weather'] ) ? $settings['pa_extra_weather'] : array();
		$slick_settings         = array();
		$temp_unit              = 'metric' === $settings['unit'] ? '&deg;C' : '&deg;F';
		$height                 = false !== $forecast && 'layout-2' === $settings['layout'] && ! empty( $settings['height']['size'] ) ? $settings['height']['size'] . 'px' : false;
		$tabs_mode              = ! in_array( $settings['forecast_days'], array( '1', '6', '7', '8' ), true ) && 'yes' === $settings['forecast_tabs'] ? true : false;
		$show_curr_weather_desc = 'yes' === $settings['show_curr_weather_desc'] ? true : false;

		if ( $hourly_forecast ) {
			$slick_settings = array(
				'layout'               => $layout,
				'hourlyLayout'         => $settings['hourly_layout'],
				'slidesToScroll'       => $settings['slides_to_scroll'],
				'slidesToScrollTab'    => isset( $settings['slides_to_scroll_tablet'] ) ? $settings['slides_to_scroll_tablet'] : 1,
				'slidesToScrollMobile' => isset( $settings['slides_to_scroll_mobile'] ) ? $settings['slides_to_scroll_mobile'] : 1,
				'slidesToShow'         => empty( $settings['slides_to_show'] ) ? 4 : $settings['slides_to_show'],
				'slidesToShowTab'      => isset( $settings['slides_to_show_tablet'] ) ? $settings['slides_to_show_tablet'] : 1,
				'slidesToShowMobile'   => isset( $settings['slides_to_show_mobile'] ) ? $settings['slides_to_show_mobile'] : 1,
			);
		}

		$is_edit_mode = \Elementor\Plugin::$instance->editor->is_edit_mode();
		$hidden_style = $is_edit_mode ? '' : 'visibility:hidden; opacity:0;';

		$this->add_render_attribute(
			'outer_wrapper',
			array(
				'class'                    => 'premium-weather__outer-wrapper',
				'data-pa-weather-settings' => wp_json_encode( $slick_settings ),
				'data-pa-height'           => $height,
				'style'                    => $hidden_style,
			)
		);

		if ( $daily_carousel ) {

			$daily_settings = array(
				'slidesToShow'       => empty( $settings['daily_slides_to_show'] ) ? 4 : $settings['daily_slides_to_show'],
				'slidesToShowTab'    => $settings['daily_slides_to_show_tablet'],
				'slidesToShowMobile' => $settings['daily_slides_to_show_mobile'],
			);

			$this->add_render_attribute( 'outer_wrapper', 'data-pa-daily-settings', wp_json_encode( $daily_settings ) );
		}

		?>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'outer_wrapper' ) ); ?>>
		<?php
		if ( 'layout-1' === $layout ) {
			?>
				<?php if ( $show_current_weather ) : ?>
				<div class="premium-weather__current-weather">
					<div class="premium-weather__basic-weather">
						<?php if ( false !== $title ) { ?>
							<div class="premium-weather__city-wrapper">
								<span class="premium-weather__city-name"> <?php echo esc_html( str_replace( '{{city_name}}', $weather_data['city_name'], $title ) ); ?></span>
							</div>
						<?php } ?>
						<div class="premium-weather__icon-wrapper" title="<?php echo esc_attr( $current['weather'][0]['description'] ); ?>">
							<?php $this->render_weather_icon( $current['weather'][0]['icon'] ); ?>
						</div>
						<div class="premium-weather__temp-wrapper">
							<?php if ( $show_temp_icon ) : ?>
								<svg id="Weather_Icons" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs><style>.premium-weather-cls-1,.premium-weather-cls-2{fill:#333;}.premium-weather-cls-2{fill-rule:evenodd;}</style></defs><g id="Temperature"><path class="premium-weather-cls-1" d="m13.5,17c0,.83-.68,1.5-1.5,1.5s-1.5-.67-1.5-1.5c0-.55.3-1.05.77-1.31.2-.11.3-.34.24-.56-.01-.04-.01-.08-.01-.13V5c0-.27.22-.5.5-.5s.5.23.5.5v10c0,.05-.01.09-.02.13-.06.22.04.45.24.56.48.26.78.76.78,1.31Z"/><path class="premium-weather-cls-1" d="m15.5,12.76v-7.76c0-1.93-1.57-3.5-3.5-3.5s-3.5,1.57-3.5,3.5v7.76c-1.26,1.04-2,2.58-2,4.24,0,3.04,2.46,5.5,5.5,5.5s5.5-2.46,5.5-5.5c0-1.66-.75-3.2-2-4.24Zm-3.5,6.74c-1.39,0-2.5-1.12-2.5-2.5,0-.8.38-1.53,1-2V5c0-.82.67-1.5,1.5-1.5s1.5.68,1.5,1.5v10c.62.47,1,1.2,1,2,0,1.38-1.12,2.5-2.5,2.5Z"/><path class="premium-weather-cls-2" d="m13.5,17c0,.83-.68,1.5-1.5,1.5s-1.5-.67-1.5-1.5c0-.55.3-1.05.77-1.31.2-.11.3-.34.24-.56-.01-.04-.01-.08-.01-.13V5c0-.27.22-.5.5-.5s.5.23.5.5v10c0,.05-.01.09-.02.13-.06.22.04.45.24.56.48.26.78.76.78,1.31Z"/></g></svg>
							<?php endif; ?>
							<span class="premium-weather__temp-val"><?php echo esc_html( round( $current['temp'], 0 ) ); ?></span>
							<span class="premium-weather__temp-unit"><?php echo $temp_unit; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						</div>
						<?php if ( $show_curr_weather_desc ) : ?>
						<div class="premium-weather__desc-wrapper">
							<div class="premium-weather__desc"><?php echo esc_html( $current['weather'][0]['description'] ); ?></div>
							<div class="premium-weather__feels-like"> Feels Like: <?php echo esc_html( $current['feels_like'] ) . $temp_unit; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
						</div>
						<?php endif; ?>
					</div>

					<?php if ( count( $extra_weather ) ) { ?>
						<div class="premium-weather__extra-weather">
							<?php $this->render_extra_weather( $extra_weather, $current ); ?>
						</div>
					<?php } ?>
				</div>
				<?php endif; ?>

				<?php if ( false !== $hourly_forecast && count( $settings['hourly_weather_data'] ) ) { ?>
					<div class="premium-weather__hourly-forecast-wrapper">
						<?php $this->render_hourly_forecast( $hourly_forecast, $settings ); ?>
					</div>
				<?php } ?>
				<?php
				if ( false !== $forecast ) {
					if ( $tabs_mode ) {
						$this->render_forecast_tabs( $weather_data['tabs_data'] );
					} else {
						$this->render_forecast_days( $weather_data['daily'], $settings['forecast_days'], $forecast_icon, $show_temp_icon );
					}
				}
				?>
			<?php
		} elseif ( 'layout-2' === $layout ) {

			?>
				<?php if ( false !== $title ) { ?>
					<div class="premium-weather__city-wrapper">
						<span class="premium-weather__city-name"> <?php echo esc_html( str_replace( '{{city_name}}', $weather_data['city_name'], $title ) ); ?></span>
					</div>
				<?php } ?>

				<div class="premium-weather__current-weather">
					<?php if ( $show_current_weather ) : ?>
						<div class="premium-weather__basic-weather">
							<div class="premium-weather__temp-wrapper">
								<?php if ( $show_temp_icon ) : ?>
									<svg id="Weather_Icons" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs><style>.premium-weather-cls-1,.premium-weather-cls-2{fill:#333;}.premium-weather-cls-2{fill-rule:evenodd;}</style></defs><g id="Temperature"><path class="premium-weather-cls-1" d="m13.5,17c0,.83-.68,1.5-1.5,1.5s-1.5-.67-1.5-1.5c0-.55.3-1.05.77-1.31.2-.11.3-.34.24-.56-.01-.04-.01-.08-.01-.13V5c0-.27.22-.5.5-.5s.5.23.5.5v10c0,.05-.01.09-.02.13-.06.22.04.45.24.56.48.26.78.76.78,1.31Z"/><path class="premium-weather-cls-1" d="m15.5,12.76v-7.76c0-1.93-1.57-3.5-3.5-3.5s-3.5,1.57-3.5,3.5v7.76c-1.26,1.04-2,2.58-2,4.24,0,3.04,2.46,5.5,5.5,5.5s5.5-2.46,5.5-5.5c0-1.66-.75-3.2-2-4.24Zm-3.5,6.74c-1.39,0-2.5-1.12-2.5-2.5,0-.8.38-1.53,1-2V5c0-.82.67-1.5,1.5-1.5s1.5.68,1.5,1.5v10c.62.47,1,1.2,1,2,0,1.38-1.12,2.5-2.5,2.5Z"/><path class="premium-weather-cls-2" d="m13.5,17c0,.83-.68,1.5-1.5,1.5s-1.5-.67-1.5-1.5c0-.55.3-1.05.77-1.31.2-.11.3-.34.24-.56-.01-.04-.01-.08-.01-.13V5c0-.27.22-.5.5-.5s.5.23.5.5v10c0,.05-.01.09-.02.13-.06.22.04.45.24.56.48.26.78.76.78,1.31Z"/></g></svg>
								<?php endif; ?>
								<span class="premium-weather__temp-val"><?php echo esc_html( round( $current['temp'], 0 ) ); ?></span>
								<span class="premium-weather__temp-unit"><?php echo $temp_unit; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							</div>
							<div class="premium-weather__desc-wrapper">
								<div class="premium-weather__icon-wrapper" title="<?php echo esc_attr( $current['weather'][0]['description'] ); ?>">
									<?php $this->render_weather_icon( $current['weather'][0]['icon'] ); ?>
								</div>
								<?php if ( $show_curr_weather_desc ) : ?>
									<div class="premium-weather__desc"><?php echo esc_html( $current['weather'][0]['description'] ); ?></div>
									<div class="premium-weather__feels-like"> Feels Like: <?php echo esc_html( $current['feels_like'] ) . $temp_unit; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
								<?php endif; ?>
							</div>
						</div>
					<?php endif; ?>
					<?php if ( ( $show_current_weather && count( $extra_weather ) ) || ( false !== $hourly_forecast && count( $settings['hourly_weather_data'] ) ) ) : ?>
					<div class="premium-weather__extra-outer-wrapper" <?php // echo $hourly_forecast_css; ?>>
						<?php if ( $show_current_weather && count( $extra_weather ) ) { ?>
							<div class="premium-weather__extra-weather">
								<?php $this->render_extra_weather( $extra_weather, $current ); ?>
							</div>
							<?php
						}

						if ( false !== $hourly_forecast && count( $settings['hourly_weather_data'] ) ) {
							$this->render_hourly_forecast( $hourly_forecast, $settings );
						}
						?>
					</div>
					<?php endif; ?>
				</div>
				<?php
				if ( false !== $forecast ) {
					if ( $tabs_mode ) {
						$this->render_forecast_tabs( $weather_data['tabs_data'] );
					} else {
						$this->render_forecast_days( $weather_data['daily'], $settings['forecast_days'], $forecast_icon, $show_temp_icon );
					}
				}
				?>
			<?php
		} elseif ( 'layout-3' === $layout ) {
			?>

			<?php if ( $show_current_weather ) : ?>
				<div class="premium-weather__current-weather">
					<div class="premium-weather__basic-weather">
						<div class="premium-weather__icon-wrapper" title="<?php echo esc_attr( $current['weather'][0]['description'] ); ?>">
							<?php $this->render_weather_icon( $current['weather'][0]['icon'] ); ?>
						</div>

						<?php if ( false !== $title ) { ?>
							<div class="premium-weather__city-wrapper">
								<span class="premium-weather__city-name"> <?php echo esc_html( str_replace( '{{city_name}}', $weather_data['city_name'], $title ) ); ?></span>
							</div>
						<?php } ?>
						<?php if ( $show_curr_weather_desc ) : ?>
							<div class="premium-weather__desc"><?php echo esc_html( $current['weather'][0]['description'] ); ?></div>
							<div class="premium-weather__feels-like"> Feels Like: <?php echo esc_html( $current['feels_like'] ) . $temp_unit; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
						<?php endif; ?>
					</div>

					<div class="premium-weather__extra-outer-wrapper">
						<div class="premium-weather__temp-wrapper">
							<?php if ( $show_temp_icon ) : ?>
								<svg id="Weather_Icons" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs><style>.premium-weather-cls-1,.premium-weather-cls-2{fill:#333;}.premium-weather-cls-2{fill-rule:evenodd;}</style></defs><g id="Temperature"><path class="premium-weather-cls-1" d="m13.5,17c0,.83-.68,1.5-1.5,1.5s-1.5-.67-1.5-1.5c0-.55.3-1.05.77-1.31.2-.11.3-.34.24-.56-.01-.04-.01-.08-.01-.13V5c0-.27.22-.5.5-.5s.5.23.5.5v10c0,.05-.01.09-.02.13-.06.22.04.45.24.56.48.26.78.76.78,1.31Z"/><path class="premium-weather-cls-1" d="m15.5,12.76v-7.76c0-1.93-1.57-3.5-3.5-3.5s-3.5,1.57-3.5,3.5v7.76c-1.26,1.04-2,2.58-2,4.24,0,3.04,2.46,5.5,5.5,5.5s5.5-2.46,5.5-5.5c0-1.66-.75-3.2-2-4.24Zm-3.5,6.74c-1.39,0-2.5-1.12-2.5-2.5,0-.8.38-1.53,1-2V5c0-.82.67-1.5,1.5-1.5s1.5.68,1.5,1.5v10c.62.47,1,1.2,1,2,0,1.38-1.12,2.5-2.5,2.5Z"/><path class="premium-weather-cls-2" d="m13.5,17c0,.83-.68,1.5-1.5,1.5s-1.5-.67-1.5-1.5c0-.55.3-1.05.77-1.31.2-.11.3-.34.24-.56-.01-.04-.01-.08-.01-.13V5c0-.27.22-.5.5-.5s.5.23.5.5v10c0,.05-.01.09-.02.13-.06.22.04.45.24.56.48.26.78.76.78,1.31Z"/></g></svg>
							<?php endif; ?>
							<span class="premium-weather__temp-val"><?php echo esc_html( round( $current['temp'], 0 ) ); ?></span>
							<span class="premium-weather__temp-unit"><?php echo $temp_unit; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						</div>
						<?php if ( count( $extra_weather ) ) { ?>
							<div class="premium-weather__extra-weather">
								<?php $this->render_extra_weather( $extra_weather, $current ); ?>
							</div>
						<?php } ?>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( false !== $hourly_forecast && count( $settings['hourly_weather_data'] ) ) { ?>
				<div class="premium-weather__hourly-forecast-wrapper">
					<?php $this->render_hourly_forecast( $hourly_forecast, $settings ); ?>
				</div>

			<?php } ?>
			<?php
			if ( false !== $forecast ) {
				if ( $tabs_mode ) {
					$this->render_forecast_tabs( $weather_data['tabs_data'] );
				} else {
					$this->render_forecast_days( $weather_data['daily'], $settings['forecast_days'], $forecast_icon, $show_temp_icon );
				}
			}
			?>
			<?php
		}
		?>
		</div>
		<?php
	}

	/**
	 * Render Forecast Days.
	 *
	 * @access private
	 * @since 2.8.23
	 *
	 * @param array  $data  forecast data.
	 * @param int    $days_num  number of days to display.
	 * @param string $layout  wideget layout.
	 */
	private function render_forecast_days( $data, $days_num, $forecast_icon, $show_temp_icon ) {

		$layout = $this->settings['forecast_layouts'];
		?>
		<div class="premium-weather__forecast">
			<?php
			for ( $i = 0; $i < $days_num; $i++ ) {

				$item         = $data[ $i ];
				$weather_desc = $item['weather'][0]['description'];
				$date         = 'style-3' === $layout ? date( 'l', $item['dt'] ) : date( 'l, d', $item['dt'] );

				if ( 0 === $i ) {
					$date = 'Today, ' . date( 'd', $item['dt'] );
				}

				if ( 1 === $i ) {
					$date = 'Tomorrow, ' . date( 'd', $item['dt'] );
				}
				?>
				<div class="premium-weather__forecast-item">
					<span class="premium-weather__forecast-item-date"><?php echo esc_html( $date ); ?></span>
					<?php if ( 'style-4' !== $layout ) { ?>
					<div class="premium-weather__forecast-item-data">
						<?php if ( $forecast_icon ) : ?>
						<div class="premium-weather__icon-wrapper" title="<?php echo esc_attr( $weather_desc ); ?>">
							<?php $this->render_weather_icon( $item['weather'][0]['icon'] ); ?>
						</div>
						<?php endif; ?>
						<div class="premium-weather__temp-wrapper">
							<span class="premium-weather__temp-max"><?php echo esc_html( round( $item['temp']['max'], 0 ) ) . '&#176;'; ?></span>
							<span class="premium-weather__temp-min"><?php echo esc_html( round( $item['temp']['min'], 0 ) ) . '&#176;'; ?></span>
						</div>
					</div>
					<?php } else { ?>
						<div class="premium-weather__forecast-item-data">
							<?php if ( $forecast_icon ) : ?>
							<div class="premium-weather__icon-wrapper" title="<?php echo esc_attr( $weather_desc ); ?>">
								<?php $this->render_weather_icon( $item['weather'][0]['icon'] ); ?>
							</div>
							<?php endif; ?>
							<span class="premium-weather__temp-max">
								<?php if ( $show_temp_icon ) : ?>
									<svg id="Weather_Icons" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs><style>.premium-weather-cls-1,.premium-weather-cls-2{fill:#333;}.premium-weather-cls-2{fill-rule:evenodd;}</style></defs><g id="Temperature"><path class="premium-weather-cls-1" d="m13.5,17c0,.83-.68,1.5-1.5,1.5s-1.5-.67-1.5-1.5c0-.55.3-1.05.77-1.31.2-.11.3-.34.24-.56-.01-.04-.01-.08-.01-.13V5c0-.27.22-.5.5-.5s.5.23.5.5v10c0,.05-.01.09-.02.13-.06.22.04.45.24.56.48.26.78.76.78,1.31Z"/><path class="premium-weather-cls-1" d="m15.5,12.76v-7.76c0-1.93-1.57-3.5-3.5-3.5s-3.5,1.57-3.5,3.5v7.76c-1.26,1.04-2,2.58-2,4.24,0,3.04,2.46,5.5,5.5,5.5s5.5-2.46,5.5-5.5c0-1.66-.75-3.2-2-4.24Zm-3.5,6.74c-1.39,0-2.5-1.12-2.5-2.5,0-.8.38-1.53,1-2V5c0-.82.67-1.5,1.5-1.5s1.5.68,1.5,1.5v10c.62.47,1,1.2,1,2,0,1.38-1.12,2.5-2.5,2.5Z"/><path class="premium-weather-cls-2" d="m13.5,17c0,.83-.68,1.5-1.5,1.5s-1.5-.67-1.5-1.5c0-.55.3-1.05.77-1.31.2-.11.3-.34.24-.56-.01-.04-.01-.08-.01-.13V5c0-.27.22-.5.5-.5s.5.23.5.5v10c0,.05-.01.09-.02.13-.06.22.04.45.24.56.48.26.78.76.78,1.31Z"/></g></svg>
								<?php endif; ?>
								<?php echo esc_html( round( $item['temp']['max'], 0 ) ) . '&#176;'; ?>
							</span>

							<span class="premium-weather__temp-min">
								<?php if ( $show_temp_icon ) : ?>
									<svg id="Weather_Icons" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs><style>.premium-weather-cls-1,.premium-weather-cls-2{fill:#333;}.premium-weather-cls-2{fill-rule:evenodd;}</style></defs><g id="Temperature"><path class="premium-weather-cls-1" d="m13.5,17c0,.83-.68,1.5-1.5,1.5s-1.5-.67-1.5-1.5c0-.55.3-1.05.77-1.31.2-.11.3-.34.24-.56-.01-.04-.01-.08-.01-.13V5c0-.27.22-.5.5-.5s.5.23.5.5v10c0,.05-.01.09-.02.13-.06.22.04.45.24.56.48.26.78.76.78,1.31Z"/><path class="premium-weather-cls-1" d="m15.5,12.76v-7.76c0-1.93-1.57-3.5-3.5-3.5s-3.5,1.57-3.5,3.5v7.76c-1.26,1.04-2,2.58-2,4.24,0,3.04,2.46,5.5,5.5,5.5s5.5-2.46,5.5-5.5c0-1.66-.75-3.2-2-4.24Zm-3.5,6.74c-1.39,0-2.5-1.12-2.5-2.5,0-.8.38-1.53,1-2V5c0-.82.67-1.5,1.5-1.5s1.5.68,1.5,1.5v10c.62.47,1,1.2,1,2,0,1.38-1.12,2.5-2.5,2.5Z"/><path class="premium-weather-cls-2" d="m13.5,17c0,.83-.68,1.5-1.5,1.5s-1.5-.67-1.5-1.5c0-.55.3-1.05.77-1.31.2-.11.3-.34.24-.56-.01-.04-.01-.08-.01-.13V5c0-.27.22-.5.5-.5s.5.23.5.5v10c0,.05-.01.09-.02.13-.06.22.04.45.24.56.48.26.78.76.78,1.31Z"/></g></svg>
								<?php endif; ?>
								<?php echo esc_html( round( $item['temp']['min'], 0 ) ) . '&#176;'; ?>
							</span>
						</div>
					<?php } ?>
				</div>
			<?php } ?>
		</div>
		<?php
	}

	/**
	 * Render Extra Weather.
	 * Renders extra weather info ( wind speed, humidity, pressure, rain, and snow).
	 *
	 * @access private
	 * @since 2.8.23
	 *
	 * @param array $extra_weather  weather info to display.
	 * @param array $current  current day's weather data.
	 */
	private function render_extra_weather( $extra_weather, $current, $tabs = false ) {

		if ( in_array( 'wind', $extra_weather, true ) && isset( $current['wind_speed'] ) ) {
			$unit       = $this->settings['unit'];
			$wind_speed = 'metric' === $unit ? round( $current['wind_speed'] * 3.6, 0 ) . ' Kmph' : round( $current['wind_speed'], 0 ) . ' mph';
			?>
				<div class="premium-weather__wind-wrapper">
					<?php if ( ! $tabs ) : ?>
						<i class="fas fa-wind"></i>
					<?php endif; ?>
					<span class="premium-weather__wind" title="Wind Speed"><?php echo esc_html( $wind_speed ); ?></span>
				</div>
		<?php } ?>

		<?php
		if ( $tabs && in_array( 'wind_dir', $extra_weather, true ) && isset( $current['wind_speed'] ) ) {
			?>
			<span class="premium-weather__wind-dir" title="Wind Direction"><?php echo esc_html( $current['wind_dir'] ) . 'deg'; ?></span>
		<?php } ?>

		<?php if ( in_array( 'humidity', $extra_weather, true ) && isset( $current['humidity'] ) ) : ?>
		<div class="premium-weather__humidity-wrapper">
			<?php if ( ! $tabs ) : ?>
				<svg id="Weather_Icons" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs><style>.premium-weather-cls-1{fill:#333;}</style></defs><path id="Humidity" class="premium-weather-cls-1" d="m19.5,15.28c0,3.99-3.37,7.22-7.5,7.22s-7.5-3.23-7.5-7.22c0-2.63,2.36-7.11,7.09-13.57.2-.27.61-.27.81,0,4.73,6.46,7.1,10.94,7.1,13.57Z"/></svg>
			<?php endif; ?>
			<span class="premium-weather__humidity" title="Humidity"><?php echo esc_html( $current['humidity'] ) . '%'; ?></span>
		</div>
		<?php endif; ?>

		<?php if ( in_array( 'pressure', $extra_weather, true ) && isset( $current['pressure'] ) ) : ?>
		<div class="premium-weather__pressure-wrapper">
			<?php if ( ! $tabs ) : ?>
				<svg id="Weather_Icons" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs><style>.premium-weather-cls-1{fill:#333;}</style></defs><path id="Weather_Pressure" class="premium-weather-cls-1" d="m12,1.5C6.2,1.5,1.5,6.2,1.5,12s4.7,10.5,10.5,10.5,10.5-4.7,10.5-10.5S17.8,1.5,12,1.5Zm0,1.75c.48,0,.87.39.87.88s-.39.87-.87.87-.88-.39-.88-.87.39-.88.88-.88Zm-4.05,1.06c.34-.04.68.12.86.43.24.42.1.95-.32,1.2s-.95.1-1.19-.32c-.24-.42-.1-.95.32-1.19.1-.06.22-.1.33-.11Zm8.09,0c.11.01.23.05.33.11.42.24.56.78.32,1.19-.24.42-.78.56-1.19.32s-.56-.78-.32-1.2c.18-.31.53-.47.86-.43Zm-10.76,2.88c.11.01.22.05.33.11.42.24.56.78.32,1.19s-.78.56-1.2.32c-.42-.24-.56-.78-.32-1.19.18-.31.53-.47.87-.43Zm13.43.03c.34-.04.68.12.86.43.24.42.1.95-.32,1.19l-5.51,3.16c0,.96-.78,1.74-1.75,1.74-.32,0-.61-.09-.87-.24l-1.91,1.1c-.14.08-.29.12-.44.12-.3,0-.6-.16-.76-.44-.24-.42-.09-.95.32-1.19l1.91-1.09c0-.96.79-1.74,1.75-1.74.32,0,.61.09.87.24l5.51-3.16c.1-.06.22-.1.33-.11Zm-14.59,3.91c.48,0,.87.39.87.88s-.39.87-.87.87-.88-.39-.88-.87.39-.88.88-.88Zm15.75,0c.48,0,.88.39.88.88s-.39.87-.88.87-.88-.39-.88-.87.39-.88.88-.88Zm-14.8,3.94c.34-.04.69.12.87.43.24.42.1.95-.32,1.19-.42.24-.95.1-1.19-.32s-.1-.95.32-1.19c.1-.06.22-.1.33-.11Zm13.86,0c.11.01.23.05.33.11.42.24.56.78.32,1.19-.24.42-.78.56-1.19.32-.42-.24-.56-.78-.32-1.19.18-.31.53-.47.87-.43Z"/></svg>
			<?php endif; ?>
			<span class="premium-weather__pressure" title="Pressure"><?php echo esc_html( $current['pressure'] ) . ' hpa'; ?></span>
		</div>
		<?php endif; ?>

		<?php if ( in_array( 'rain', $extra_weather, true ) && isset( $current['rain'] ) ) : ?>
		<div class="premium-weather__rain-wrapper">
			<i class="fas fa-cloud-rain"></i>
			<span class="premium-weather__rain" title="Rain"> <?php echo esc_html( $current['rain'] ) . 'mmph'; ?></span>
		</div>
		<?php endif; ?>

		<?php if ( in_array( 'snow', $extra_weather, true ) && isset( $current['snow'] ) ) : ?>
		<div class="premium-weather__snow-wrapper">
			<i class="far fa-snowflake"></i>
			<span class="premium-weather__snow" title="Snow"><?php echo esc_html( $current['snow'] ) . 'mmph'; ?></span>
		</div>
		<?php endif; ?>
		<?php
	}

	/**
	 * Render Hourly Forecast.
	 *
	 * @access private
	 * @since 2.8.23
	 *
	 * @param array $data  hourly forecast data.
	 */
	private function render_hourly_forecast( $data ) {
		$settings        = $this->settings;
		$limit           = $settings['hourly_max'];
		$show_temp_icon  = 'yes' === $settings['show_temp_icon'] ? true : false;
		$vertical_layout = 'layout-2' !== $settings['layout'] && 'vertical' === $settings['hourly_layout'] ? true : false;

		if ( $vertical_layout ) {
			$weather_conditions = $settings['hourly_weather_data'];

			$show_desc_icon = in_array( 'desc_icon', $weather_conditions, true ) ? true : false;
			$show_desc      = in_array( 'desc', $weather_conditions, true ) ? true : false;
			$show_temp      = in_array( 'temp', $weather_conditions, true ) ? true : false;
		}

		for ( $i = 0; $i < $limit; $i++ ) {
			$item         = $data[ $i ];
			$weather_desc = $item['weather'][0]['description'];

			?>
			<div class="premium-weather__hourly-item">
				<span class="premium-weather__hourly-item-date"><?php echo esc_html( date( 'g A', $item['dt'] ) ); ?></span>
				<?php if ( ! $vertical_layout ) : ?>
					<div class="premium-weather__icon-wrapper" title="<?php echo esc_attr( $weather_desc ); ?>">
						<?php $this->render_weather_icon( $item['weather'][0]['icon'] ); ?>
					</div>
				<?php endif; ?>

				<?php if ( ! $vertical_layout ) { ?>
					<div class="premium-weather__temp-wrapper">
						<?php if ( $show_temp_icon ) : ?>
							<svg id="Weather_Icons" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs><style>.premium-weather-cls-1,.premium-weather-cls-2{fill:#333;}.premium-weather-cls-2{fill-rule:evenodd;}</style></defs><g id="Temperature"><path class="premium-weather-cls-1" d="m13.5,17c0,.83-.68,1.5-1.5,1.5s-1.5-.67-1.5-1.5c0-.55.3-1.05.77-1.31.2-.11.3-.34.24-.56-.01-.04-.01-.08-.01-.13V5c0-.27.22-.5.5-.5s.5.23.5.5v10c0,.05-.01.09-.02.13-.06.22.04.45.24.56.48.26.78.76.78,1.31Z"/><path class="premium-weather-cls-1" d="m15.5,12.76v-7.76c0-1.93-1.57-3.5-3.5-3.5s-3.5,1.57-3.5,3.5v7.76c-1.26,1.04-2,2.58-2,4.24,0,3.04,2.46,5.5,5.5,5.5s5.5-2.46,5.5-5.5c0-1.66-.75-3.2-2-4.24Zm-3.5,6.74c-1.39,0-2.5-1.12-2.5-2.5,0-.8.38-1.53,1-2V5c0-.82.67-1.5,1.5-1.5s1.5.68,1.5,1.5v10c.62.47,1,1.2,1,2,0,1.38-1.12,2.5-2.5,2.5Z"/><path class="premium-weather-cls-2" d="m13.5,17c0,.83-.68,1.5-1.5,1.5s-1.5-.67-1.5-1.5c0-.55.3-1.05.77-1.31.2-.11.3-.34.24-.56-.01-.04-.01-.08-.01-.13V5c0-.27.22-.5.5-.5s.5.23.5.5v10c0,.05-.01.09-.02.13-.06.22.04.45.24.56.48.26.78.76.78,1.31Z"/></g></svg>
						<?php endif; ?>
						<span class="premium-weather__temp">
							<?php
								echo esc_html( round( $item['temp'], 0 ) ) . '&#176;';
							?>
						</span>
					</div>
				<?php } else { ?>
					<div class="premium-weather__hourly-data">

						<?php if ( $show_desc_icon ) : ?>
							<div class="premium-weather__icon-wrapper" title="<?php echo esc_attr( $weather_desc ); ?>">
								<?php $this->render_weather_icon( $item['weather'][0]['icon'] ); ?>
							</div>
						<?php endif; ?>

						<?php if ( $show_temp ) : ?>
							<div class="premium-weather__temp-wrapper">
								<?php if ( $show_temp_icon ) : ?>
									<svg id="Weather_Icons" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs><style>.premium-weather-cls-1,.premium-weather-cls-2{fill:#333;}.premium-weather-cls-2{fill-rule:evenodd;}</style></defs><g id="Temperature"><path class="premium-weather-cls-1" d="m13.5,17c0,.83-.68,1.5-1.5,1.5s-1.5-.67-1.5-1.5c0-.55.3-1.05.77-1.31.2-.11.3-.34.24-.56-.01-.04-.01-.08-.01-.13V5c0-.27.22-.5.5-.5s.5.23.5.5v10c0,.05-.01.09-.02.13-.06.22.04.45.24.56.48.26.78.76.78,1.31Z"/><path class="premium-weather-cls-1" d="m15.5,12.76v-7.76c0-1.93-1.57-3.5-3.5-3.5s-3.5,1.57-3.5,3.5v7.76c-1.26,1.04-2,2.58-2,4.24,0,3.04,2.46,5.5,5.5,5.5s5.5-2.46,5.5-5.5c0-1.66-.75-3.2-2-4.24Zm-3.5,6.74c-1.39,0-2.5-1.12-2.5-2.5,0-.8.38-1.53,1-2V5c0-.82.67-1.5,1.5-1.5s1.5.68,1.5,1.5v10c.62.47,1,1.2,1,2,0,1.38-1.12,2.5-2.5,2.5Z"/><path class="premium-weather-cls-2" d="m13.5,17c0,.83-.68,1.5-1.5,1.5s-1.5-.67-1.5-1.5c0-.55.3-1.05.77-1.31.2-.11.3-.34.24-.56-.01-.04-.01-.08-.01-.13V5c0-.27.22-.5.5-.5s.5.23.5.5v10c0,.05-.01.09-.02.13-.06.22.04.45.24.56.48.26.78.76.78,1.31Z"/></g></svg>
								<?php endif; ?>
								<span class="premium-weather__temp">
									<?php
										echo esc_html( round( $item['temp'], 0 ) ) . '&#176;';
									?>
								</span>
							</div>
						<?php endif; ?>

						<?php $this->render_extra_weather( $weather_conditions, $item ); ?>
						<?php if ( $show_desc ) : ?>
							<span class="premium-weather__hourly-desc"> <?php echo esc_html( $item['weather'][0]['description'] ); ?></span>
						<?php endif; ?>
					</div>
				<?php } ?>

			</div>
			<?php
		}
	}

	/**
	 * Get Weather Icon Code.
	 *
	 * @access private
	 * @since 2.8.23
	 *
	 * @param string $code  icon code.
	 * @param bool   $is_video_code   true if the code for the video background.
	 *
	 * @return string
	 */
	private function get_weather_icon_code( $code, $is_video_code = false ) {

		$dual_icons = array( '01d', '01n', '02d', '02n', '10d', '10n' );

		$code = in_array( $code, $dual_icons, true ) ? $code : substr( $code, 0, -1 );

		if ( $is_video_code && in_array( $code, array( '09', '10d', '10n' ), true ) ) {
			$code = 'rain';
		}

		return $code;
	}

	/**
	 * Render Weather Icon.
	 *
	 * @access private
	 * @since 2.8.23
	 *
	 * @param string $code  weather icon code.
	 */
	private function render_weather_icon( $code ) {

		$settings = $this->settings;

		$code = $this->get_weather_icon_code( $code );

		$enable_custom_icons = 'yes' === $settings['enable_custom_icon'] ? true : false;

		$custom_icons = $enable_custom_icons ? $this->get_custom_icons() : array();

		$default_icons = array(
			'01d' => '<svg id="Weather_Icons" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs><style>.premium-weather-cls-1{fill:#333;}</style></defs><g id="Clear_Sky"><circle class="premium-weather-cls-1" cx="12" cy="12" r="5.5"/><path class="premium-weather-cls-1" d="m21.76,12.74h-1.95c-.98,0-.98-1.47,0-1.47h1.95c.98,0,.98,1.47,0,1.47Z"/><path class="premium-weather-cls-1" d="m19.39,5.62l-1.38,1.38c-.29.29-.75.29-1.04,0-.29-.29-.29-.75,0-1.04l1.38-1.38c.29-.28.75-.28,1.04,0,.28.29.28.75,0,1.04Z"/><path class="premium-weather-cls-1" d="m12.74,2.24v1.95c0,.4-.33.73-.73.73s-.74-.33-.74-.73v-1.95c0-.41.33-.74.74-.74s.73.33.73.74Z"/><path class="premium-weather-cls-1" d="m5.96,7.03l-1.38-1.38c-.32-.31-.29-.75,0-1.04s.72-.31,1.03,0l1.38,1.38c.69.69-.34,1.73-1.03,1.04Z"/><path class="premium-weather-cls-1" d="m4.19,12.74h-1.95c-.98,0-.98-1.47,0-1.47h1.95c.98,0,.98,1.47,0,1.47Z"/><path class="premium-weather-cls-1" d="m7.02,18.04l-1.38,1.38c-.31.31-.75.29-1.04,0s-.31-.72,0-1.03l1.38-1.38c.32-.31.75-.29,1.04,0,.29.28.31.72,0,1.03Z"/><path class="premium-weather-cls-1" d="m12.74,19.82v1.95c0,.98-1.47.98-1.47,0v-1.95c0-.98,1.47-.98,1.47,0Z"/><path class="premium-weather-cls-1" d="m19.43,19.4c-.29.28-.73.31-1.04,0l-1.38-1.39c-.31-.31-.29-.75,0-1.03.28-.29.72-.31,1.03,0l1.39,1.38c.31.31.28.75,0,1.04Z"/></g></svg>',

			'01n' => '<svg id="Weather_Icons" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs><style>.premium-weather-cls-1{fill:#333;}</style></defs><path id="Clear_Sky_Night" class="premium-weather-cls-1" d="m21.93,17.23c-1.89,3.24-5.4,5.27-9.26,5.27-5.89,0-10.67-4.7-10.67-10.51S6.37,1.87,11.95,1.5c.4-.02.67.41.46.76-.83,1.42-1.28,3.04-1.28,4.73,0,5.25,4.33,9.51,9.68,9.51.22,0,.44,0,.65-.02.4-.03.67.4.47.75Z"/></svg>',

			'02d' => '<svg id="Weather_Icons" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs><style>.premium-weather-cls-1,.premium-weather-cls-2{fill:#333;}.premium-weather-cls-2{fill-rule:evenodd;}</style></defs><g id="Few_Clouds"><path class="premium-weather-cls-1" d="m23,15.23c0,2.09-1.74,3.77-3.87,3.77h-10.59c-1.4,0-2.54-1.14-2.54-2.54s1.13-2.54,2.52-2.55c.05-2.72,2.33-4.91,5.13-4.91,1.27,0,2.45.45,3.35,1.22.46.38.84.84,1.14,1.36.32-.08.65-.12.99-.12,2.13,0,3.87,1.68,3.87,3.77Z"/><path class="premium-weather-cls-2" d="m11.85,7.56l-.83.83c-.19.19-.5.19-.69,0-.19-.19-.19-.5,0-.69l.83-.83c.19-.19.5-.19.69,0s.19.5,0,.69Z"/><path class="premium-weather-cls-2" d="m8,5.49v1.01c0,.28-.23.5-.5.5s-.5-.22-.5-.5v-1.01c0-.27.22-.49.5-.49s.5.22.5.49Z"/><path class="premium-weather-cls-2" d="m3.7,8.41l-.84-.83c-.46-.47.24-1.16.7-.7l.83.83c.46.46-.23,1.16-.69.7Z"/><path class="premium-weather-cls-2" d="m3,11.5c0,.28-.23.5-.5.5h-1.01c-.29,0-.46-.19-.49-.41v-.18c.03-.22.2-.41.49-.41h1.01c.27,0,.5.23.5.5Z"/><path class="premium-weather-cls-2" d="m4.41,15.02l-.84.83c-.46.46-1.15-.23-.69-.69l.83-.83c.46-.46,1.16.23.7.69Z"/><path class="premium-weather-cls-2" d="m10.01,9.18c-1.26.91-2.15,2.28-2.41,3.86-.77.21-1.44.68-1.91,1.31-1.05-.59-1.75-1.71-1.75-2.99,0-1.89,1.53-3.42,3.43-3.42,1.05,0,2.01.47,2.64,1.24Z"/></g></svg>',

			'02n' => '<svg id="Weather_Icons" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs><style>.premium-weather-cls-1{fill:#333;}</style></defs><g id="Few_Clouds_Night"><path class="premium-weather-cls-1" d="m18.27,19.5h-10.22c-1.41,0-2.55-1.14-2.55-2.55s1.13-2.54,2.53-2.55c.05-2.72,2.33-4.91,5.13-4.91,1.89,0,3.6,1.01,4.49,2.58.32-.08.65-.12.98-.12,2.13,0,3.87,1.69,3.87,3.77s-1.74,3.77-3.87,3.77c-.07,0-.13,0-.2,0-.05,0-.11,0-.16,0h0Z"/><path class="premium-weather-cls-1" d="m4.6,16.13c-1.27-.49-2.35-1.42-3.03-2.64-.19-.35.07-.77.47-.74.11,0,.22.01.33.01,2.68,0,4.86-2.24,4.86-5.01,0-.89-.23-1.75-.65-2.5-.19-.35.07-.77.47-.74,2.41.16,4.42,1.82,5.16,4.07-2.61.4-4.68,2.4-5.1,4.96-1.24.34-2.21,1.34-2.51,2.59h0Z"/></g></svg>',

			'04'  => '<svg id="Weather_Icons" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs><style>.premium-weather-cls-1{fill:#333;}</style></defs><g id="Broken_Clouds"><path class="premium-weather-cls-1" d="m21,11.77c-.76-.49-1.68-.77-2.66-.77-.25,0-.49.02-.74.06-.99-1.28-2.5-2.06-4.13-2.06-2.49,0-4.59,1.79-5.14,4.21-1.36.5-2.32,1.79-2.32,3.29,0,.17.02.34.04.5h-.73c-1.83,0-3.32-1.45-3.32-3.25s1.49-3.25,3.32-3.25h.09c.23-3.07,2.63-5.5,5.57-5.5,2.16,0,4.08,1.33,5.01,3.35.21-.03.42-.05.64-.05,2.15,0,3.95,1.49,4.37,3.47Z"/><path class="premium-weather-cls-1" d="m22,15.5c0,1.95-1.68,3.5-3.74,3.5-.17,0-.34-.01-.51-.03-.13.02-.27.03-.41.03h-7.84c-1.39,0-2.5-1.12-2.5-2.5,0-1.27.94-2.32,2.17-2.48.23-2.25,2.04-4.02,4.25-4.02,1.52,0,2.9.85,3.66,2.18.38-.11.78-.18,1.18-.18,2.06,0,3.74,1.56,3.74,3.5Z"/></g></svg>',

			'03'  => '<svg id="Weather_Icons" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs><style>.premium-weather-cls-1{fill:#333;}</style></defs><path id="Scattered_Clouds" class="premium-weather-cls-1" d="m17.78,18.5c-.11,0-.22,0-.33-.01-.1.01-.19.01-.29.01H4.72c-1.78,0-3.22-1.44-3.22-3.22s1.4-3.19,3.15-3.23v-.05c0-3.58,2.82-6.5,6.29-6.5,2.37,0,4.49,1.36,5.56,3.46.42-.12.85-.18,1.28-.18,2.61,0,4.72,2.18,4.72,4.86s-2.11,4.86-4.72,4.86Z"/></svg>',

			'09'  => '<svg id="Weather_Icons" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs><style>.premium-weather-cls-1{fill:#333;}</style></defs><g id="Shower_Rain"><path class="premium-weather-cls-1" d="m22.5,10.14c0,.66-.13,1.29-.36,1.86-.71,1.76-2.39,3-4.36,3H4.55c-1.62,0-2.94-1.33-3.04-3-.01-.07-.01-.15-.01-.22,0-1.78,1.36-3.23,3.05-3.23h.1v-.05c0-3.58,2.82-6.5,6.29-6.5,2.37,0,4.48,1.36,5.56,3.46.42-.12.85-.18,1.28-.18,2.61,0,4.72,2.18,4.72,4.86Z"/><path class="premium-weather-cls-1" d="m18.44,17.73l-1.13,2.27-.87,1.73c-.12.24-.42.34-.67.22s-.35-.42-.22-.67l.64-1.28,1.36-2.72c.12-.25.42-.35.67-.22.25.12.35.42.22.67Z"/><path class="premium-weather-cls-1" d="m14.44,17.73l-1,2c-.09.17-.26.27-.45.27-.07,0-.15-.02-.22-.05-.25-.12-.35-.42-.22-.67l1-2c.12-.25.42-.35.67-.22.25.12.35.42.22.67Z"/><path class="premium-weather-cls-1" d="m10.44,17.73l-1.13,2.27-.87,1.73c-.12.24-.42.34-.67.22s-.35-.42-.22-.67l.64-1.28,1.36-2.72c.12-.25.42-.35.67-.22.25.12.35.42.22.67Z"/><path class="premium-weather-cls-1" d="m6.44,17.73l-1,2c-.09.17-.26.27-.45.27-.07,0-.15-.02-.22-.05-.25-.12-.35-.42-.22-.67l1-2c.12-.25.42-.35.67-.22.25.12.35.42.22.67Z"/></g></svg>',

			'11'  => '<svg id="Weather_Icons" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs><style>.premium-weather-cls-1{fill:#333;}</style></defs><g id="Thunderstorm"><path class="premium-weather-cls-1" d="m22.5,10.64c0,2.06-1.24,3.82-3,4.52-.53.22-1.11.34-1.72.34-.12,0-.25,0-.29-.01-.11.01-.22.01-.33.01h-1.69c.15-.82-.3-1.68-1.13-2.06l-1.04-.46.4-3.82c.16-1.52-1.79-2.27-2.7-1.05l-4.13,5.6c-.42.56-.47,1.23-.24,1.79h-1.91c-.86,0-1.64-.34-2.22-.89-.62-.58-1-1.41-1-2.33s.38-1.75,1-2.34c.56-.53,1.31-.87,2.15-.89v-.05c0-2.31,1.18-4.35,2.95-5.5.97-.64,2.12-1,3.34-1s2.38.36,3.36,1c.92.6,1.69,1.44,2.2,2.46.42-.12.85-.18,1.28-.18.61,0,1.19.12,1.72.34,1.76.7,3,2.46,3,4.52Z"/><path class="premium-weather-cls-1" d="m14.32,15.7l-3.54,4.8-.59.8c-.3.41-.95.16-.9-.35l.05-.45.42-4.08-1.69-.76c-.54-.25-.76-.87-.4-1.35l4.14-5.6c.3-.41.95-.16.89.35l-.47,4.53,1.69.76c.55.25.76.87.4,1.35Z"/></g></svg>',

			'13'  => '<svg id="Weather_Icons" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs><style>.premium-weather-cls-1{fill:#333;}</style></defs><path id="Snow" class="premium-weather-cls-1" d="m21.97,12.81c.12.4-.11.83-.51.96l-3.5,1.05,2.66,1.66c.87.54.05,1.85-.82,1.3l-2.31-1.44,1.18,3.63c.13.4-.09.84-.49.97-.4.13-.84-.09-.97-.49l-1.75-5.38-2.69-1.68v3.42l3.88,3.88c.3.3.3.79,0,1.09s-.79.3-1.09,0l-2.79-2.79v2.24c0,.43-.34.77-.76.77s-.77-.34-.77-.77v-2.24l-2.79,2.79c-.3.3-.79.3-1.09,0s-.3-.79,0-1.09l3.88-3.88v-3.42l-2.7,1.69-1.97,5.39c-.14.4-.58.61-.98.46-.4-.14-.61-.59-.46-.98l1.29-3.55-2.21,1.38c-.39.25-.84.12-1.06-.24-.23-.36-.15-.82.24-1.06l2.19-1.37-2.88-.29c-.42-.04-.73-.42-.69-.84.04-.42.42-.73.84-.69l4.86.49,2.84-1.78-2.76-1.72-4.79,1.43c-.41.13-.84-.1-.96-.51-.12-.41.11-.84.51-.96l3.5-1.05-2.66-1.66c-.87-.55-.05-1.85.82-1.31l2.31,1.45-1.18-3.63c-.13-.41.09-.84.49-.97.41-.13.84.09.97.49l1.75,5.38,2.69,1.68v-3.43l-3.88-3.87c-.3-.3-.3-.79,0-1.09s.79-.3,1.09,0l2.79,2.79v-2.25c0-.42.34-.77.77-.77s.76.35.76.77v2.25l2.79-2.79c.3-.3.79-.3,1.09,0s.3.79,0,1.09l-3.88,3.87v3.43l2.69-1.68,1.75-5.38c.13-.4.57-.62.97-.49.4.13.62.56.49.97l-1.18,3.63,2.31-1.45c.87-.54,1.69.76.82,1.31l-2.66,1.66,3.5,1.05c.4.12.63.55.51.96-.12.41-.55.64-.95.51l-4.8-1.43-2.76,1.72,2.76,1.73,4.8-1.44c.4-.12.83.11.95.52Z"/></svg>',

			'50'  => '<svg id="Weather_Icons" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs><style>.premium-weather-cls-1{fill:#333;}</style></defs><g id="Mist"><path class="premium-weather-cls-1" d="m4.93,9.56c0,.41.33.73.73.73h12.67c1.21,0,2.19.99,2.19,2.2s-.98,2.2-2.19,2.2h-8.77c-.4,0-.73.33-.73.73s.33.73.73.73h6.82c1.21,0,2.19.99,2.19,2.2s-.98,2.2-2.19,2.2h-4.87c-.41,0-.73.33-.73.73v.49c0,.41-.33.73-.73.73s-.73-.32-.73-.73v-.49c0-1.21.98-2.2,2.19-2.2h4.87c.4,0,.73-.32.73-.73s-.33-.73-.73-.73h-6.82c-1.21,0-2.19-.98-2.19-2.2s.98-2.2,2.19-2.2h8.77c.4,0,.73-.32.73-.73s-.33-.73-.73-.73H5.66c-1.21,0-2.19-.98-2.19-2.2s.98-2.2,2.19-2.2h13.64c.41,0,.73-.32.73-.73s-.32-.73-.73-.73H4.69c-1.21,0-2.19-.99-2.19-2.2s.98-2.2,2.19-2.2h12.18c.4,0,.73.33.73.74s-.33.73-.73.73H4.69c-.41,0-.73.33-.73.73s.32.73.73.73h14.61c1.21,0,2.2.99,2.2,2.2s-.99,2.2-2.2,2.2H5.66c-.4,0-.73.33-.73.73Z"/></g></svg>',

			'10d' => '<svg id="Weather_Icons" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs><style>.premium-weather-cls-1,.premium-weather-cls-2{fill:#333;}.premium-weather-cls-2{fill-rule:evenodd;}</style></defs><g id="Rain"><path class="premium-weather-cls-2" d="m10.74,6.99c-1.89.79-3.3,2.49-3.64,4.55-.29.08-.57.2-.83.35-1.09-.58-1.83-1.72-1.83-3.03,0-1.89,1.53-3.42,3.43-3.42,1.19,0,2.25.61,2.87,1.55Z"/><path class="premium-weather-cls-2" d="m12.35,5.06l-.83.83c-.19.19-.5.19-.69,0-.19-.19-.19-.5,0-.69l.83-.83c.19-.19.5-.19.69,0s.19.5,0,.69Z"/><path class="premium-weather-cls-2" d="m8.5,2.99v1.01c0,.28-.23.5-.5.5s-.5-.22-.5-.5v-1.01c0-.27.22-.49.5-.49s.5.22.5.49Z"/><path class="premium-weather-cls-2" d="m4.2,5.91l-.84-.83c-.46-.47.24-1.16.7-.7l.83.83c.46.46-.23,1.16-.69.7Z"/><path class="premium-weather-cls-2" d="m3.5,9c0,.28-.23.5-.5.5h-1.01c-.66,0-.66-1,0-1h1.01c.27,0,.5.23.5.5Z"/><path class="premium-weather-cls-2" d="m4.91,12.52l-.84.83c-.11.11-.23.15-.34.15-.38,0-.7-.49-.35-.84l.83-.83c.46-.46,1.16.23.7.69Z"/><path class="premium-weather-cls-1" d="m22.5,13.73c0,2.09-1.74,3.77-3.87,3.77h-10.59c-1.4,0-2.54-1.14-2.54-2.54s1.13-2.54,2.52-2.55c.05-2.72,2.33-4.91,5.13-4.91,1.9,0,3.6,1.01,4.49,2.58.32-.08.65-.12.99-.12,2.13,0,3.87,1.68,3.87,3.77Z"/><path class="premium-weather-cls-1" d="m19.44,19.23l-1,2c-.12.24-.42.34-.67.22-.25-.12-.35-.42-.22-.67l1-2c.12-.25.42-.35.67-.22.25.12.35.42.22.67Z"/><path class="premium-weather-cls-1" d="m14.44,19.23l-1,2c-.12.24-.42.34-.67.22-.25-.12-.35-.42-.22-.67l1-2c.08-.18.26-.28.44-.28.08,0,.16.02.23.06.25.12.35.42.22.67Z"/><path class="premium-weather-cls-1" d="m9.44,19.23l-1,2c-.12.24-.42.34-.67.22-.25-.12-.35-.42-.22-.67l1-2c.08-.18.26-.28.44-.28.08,0,.16.02.23.06.25.12.35.42.22.67Z"/></g></svg>',

			'10n' => '<svg id="Weather_Icons" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs><style>.premium-weather-cls-1{fill:#333;}</style></defs><g id="Rain_Night"><path class="premium-weather-cls-1" d="m4.6,13.13c-1.27-.49-2.35-1.42-3.03-2.64-.19-.35.07-.77.47-.74.11,0,.22.01.33.01,2.68,0,4.86-2.24,4.86-5.01,0-.89-.23-1.75-.65-2.5-.19-.35.07-.77.47-.74,2.41.16,4.42,1.82,5.16,4.07-2.61.4-4.68,2.4-5.1,4.96-1.24.34-2.21,1.34-2.51,2.59h0Zm4.96,4.65c.12-.25.42-.35.67-.22s.35.42.22.67l-1,2c-.12.25-.42.35-.67.22s-.35-.42-.22-.67l1-2h0Zm3,0c.12-.25.42-.35.67-.22s.35.42.22.67l-2,4c-.12.25-.42.35-.67.22s-.35-.42-.22-.67l2-4h0Zm3,0c.12-.25.42-.35.67-.22s.35.42.22.67l-1,2c-.12.25-.42.35-.67.22s-.35-.42-.22-.67l1-2h0Zm3,0c.12-.25.42-.35.67-.22s.35.42.22.67l-2,4c-.12.25-.42.35-.67.22s-.35-.42-.22-.67l2-4h0Z"/><path class="premium-weather-cls-1" d="m18.63,8.95c2.13,0,3.87,1.69,3.87,3.77s-1.74,3.77-3.87,3.77h-10.59c-1.41,0-2.55-1.14-2.55-2.55s1.13-2.54,2.53-2.55c.05-2.72,2.33-4.91,5.13-4.91,1.89,0,3.6,1.01,4.49,2.58.32-.08.65-.12.98-.12h0Z"/></g></svg>',
		);

		if ( isset( $custom_icons[ $code ] ) ) {
			$icon_source = $settings['icons_source'];

			if ( 'default' === $icon_source ) {
				?>
					<div class="premium-lottie-animation" data-lottie-url="<?php echo esc_url( $custom_icons[ $code ] ); ?>" data-lottie-loop="true" data-lottie-reverse="false"></div>
				<?php
			} else {
				$this->render_custom_icon( $custom_icons[ $code ], $code );
			}
		} else {
			echo $default_icons[ $code ]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	/**
	 * Render Custom Icon.
	 *
	 * @access private
	 * @since 2.8.23
	 *
	 * @param array $icon_data icon data.
	 */
	private function render_custom_icon( $icon, $code ) {

		$settings  = $this->settings;
		$draw_fill = $settings['svg_color'];
		$icon_type = $icon['type'];
		$icon      = $icon['icon'];

		if ( 'icon' === $icon_type ) {
			$is_svg = 'svg' === $icon['library'] ? true : false;

			if ( $is_svg ) {
				Icons_Manager::render_icon(
					$icon,
					array(
						'aria-hidden' => 'true',
					)
				);
			} else {
				if ( 'yes' !== $settings['draw_svg'] ) {
					Icons_Manager::render_icon(
						$icon,
						array(
							'aria-hidden' => 'true',
						)
					);
				} else {
					?>
				<i class='premium-drawable-icon premium-svg-drawer <?php echo esc_attr( $icon['value'] ); ?>' data-svg-loop='false' data-svg-fill='<?php echo esc_attr( $draw_fill ); ?>' data-svg-sync='yes' data-svg-frames='5' data-svg-point='0' aria-hidden='hidden'></i>
					<?php
				}
			}
		} elseif ( 'image' === $icon_type ) {

			if ( ! empty( $icon['img']['url'] ) ) {
				$img_src = wp_get_attachment_image_src( $icon['img']['id'], $icon['size'] );
				?>
			<img src="<?php echo esc_url( $img_src[0] ); ?>" alt="<?php echo esc_attr( $icon['img']['alt'] ); ?>">
				<?php
			}
		} else {
			?>
				<div class="premium-lottie-animation" data-lottie-url="<?php echo esc_url( $icon['url'] ); ?>" data-lottie-loop="<?php echo esc_attr( $icon['loop'] ); ?>" data-lottie-reverse="<?php echo esc_attr( $icon['reverse'] ); ?>"></div>
			<?php
		}
	}

	/**
	 * Get Custom Icons.
	 *
	 * @access private
	 * @since 2.8.23
	 *
	 * @return array
	 */
	private function get_custom_icons() {

		$settings = $this->settings;

		$icon_source = $settings['icons_source'];

		if ( 'default' === $icon_source ) {

			$lottie_type = $settings['lottie_type'];

			$lottie_url = 'https://premiumtemplates.io/wp-content/uploads/premium-weather/' . $lottie_type;

			$custom_icons = array(
				'01d' => $lottie_url . '/01d.json',

				'01n' => $lottie_url . '/01n.json',

				'02d' => $lottie_url . '/02d.json',

				'02n' => $lottie_url . '/02n.json',

				'04'  => $lottie_url . '/04.json',

				'03'  => $lottie_url . '/03.json',

				'09'  => $lottie_url . '/09.json',

				'11'  => $lottie_url . '/11.json',

				'13'  => $lottie_url . '/13.json',

				'50'  => $lottie_url . '/50.json',

				'10d' => $lottie_url . '/10d.json',

				'10n' => $lottie_url . '/10n.json',
			);
		} else {
			$icons = $settings['custom_icons'];

			$icon_source = $settings['icons_source'];

			if ( ! count( $icons ) ) {
				return array();
			}

			$dual_icons = array( '01', '02', '10' );

			$condition_codes = array(
				'Clear Sky'        => '01',
				'Few Clouds'       => '02',
				'Scattered Clouds' => '03',
				'Broken Clouds'    => '04',
				'Shower Rain'      => '09',
				'Rain'             => '10',
				'Thunderstorm'     => '11',
				'Snow'             => '13',
				'Mist'             => '50',
			);

			$custom_icons = array();

			foreach ( $icons as $icon ) {

				$icon_code    = $condition_codes[ $icon['weather_desc'] ];
				$is_dual_icon = in_array( $icon_code, $dual_icons, true ) ? true : false;

				$icon_type = $icon['pa_icon_type'];

				if ( 'icon' === $icon_type ) {
					$custom_icon = $icon['pa_custom_icon'];

					if ( $is_dual_icon ) {
						$night_custom_icon = empty( $icon['pa_custom_icon_night'] ) ? $custom_icon : $icon['pa_custom_icon_night'];
					}
				} elseif ( 'image' === $icon_type ) {
					$custom_icon = array(
						'img'  => $icon['pa_weather_img'],
						'size' => $icon['image_size'],
					);

					if ( $is_dual_icon ) {
						$night_custom_icon = array(
							'img'  => $icon['pa_weather_img'],
							'size' => $icon['image_size'],
						);

						if ( ! empty( $icon['pa_weather_img_night']['url'] ) ) {
							$night_custom_icon['img'] = $icon['pa_weather_img_night'];
						}
					}
				} else {

					$source = $icon['lottie_source'];

					$lottie_url  = $icon['pa_lottie_url'];
					$custom_icon = array(
						'id'      => $icon['_id'],
						'url'     => $lottie_url,
						'loop'    => $icon['pa_lottie_loop'],
						'reverse' => $icon['pa_lottie_reverse'],
					);

					if ( $is_dual_icon ) {
						$night_custom_icon = array(
							'id'      => $icon['_id'],
							'loop'    => $icon['pa_lottie_loop'],
							'reverse' => $icon['pa_lottie_reverse'],
						);

						$night_custom_icon['url'] = ! empty( $icon['pa_lottie_url_night'] ) ? $icon['pa_lottie_url_night'] : $lottie_url;
					}
				}

				if ( $is_dual_icon ) {

					$custom_icons[ $icon_code . 'n' ] = array(
						'type' => $icon_type,
						'icon' => $night_custom_icon,
					);

					$icon_code .= 'd';
				}

				$custom_icons[ $icon_code ] = array(
					'type' => $icon_type,
					'icon' => $custom_icon,
				);
			}
		}

		return $custom_icons;
	}

	/**
	 * Render Forecast Tabs.
	 *
	 * @access private
	 * @since 2.8.23
	 *
	 * @param array $forecast_data forcast data up to 5 days.
	 */
	private function render_forecast_tabs( $forecast_data ) {
		?>
		<div class="premium-weather__forecast-tabs-wrapper">
			<?php
				$this->render_forecast_tabs_headers( array_keys( $forecast_data ) );
				$this->render_forecast_tabs_content( $forecast_data );
			?>
		</div>
		<?php
	}

	private function render_forecast_tabs_headers( $headers ) {

		$settings = $this->settings;

		$limit = $settings['forecast_days'];

		$date_format = $settings['date_format'];

		$headers = empty( $settings['forecast_dates'] ) ? $headers : $this->extract_forecast_dates( $settings['forecast_dates'] );
		?>
		<ul class="premium-weather__tabs-headers">
		<?php
		for ( $i = 0; $i < $limit; $i++ ) {
			if ( isset( $headers[ $i ] ) ) {
				$date = date( $date_format, strtotime( $headers[ $i ] ) );
				?>
					<li class='premium-weather__tab-header <?php echo $i === 0 ? ' current' : ''; ?>' data-content-id="#premium-tab-content-<?php echo esc_attr( $i ); ?>" aria-label='<?php echo esc_attr__( $date, 'premium-addons-for-elementor' ); ?>' title='<?php echo esc_attr__( $date, 'premium-addons-for-elementor' ); ?>'> <?php echo esc_html( $date ); ?></li>
					<?php
			}
		}
		?>
		</ul>
		<?php
	}

	private function render_forecast_tabs_content( $forecast_data ) {

		$settings = $this->settings;

		$limit = $settings['forecast_days'];

		$headers = empty( $settings['forecast_dates'] ) ? array_keys( $forecast_data ) : $this->extract_forecast_dates( $settings['forecast_dates'] );
		$i       = 0;

		$weather_conditions = $settings['tabs_weather_data'];

		$conditions_arr = array(
			'desc_icon'  => in_array( 'desc_icon', $weather_conditions, true ) ? true : false,
			'temp'       => in_array( 'temp', $weather_conditions, true ) ? true : false,
			'wind'       => in_array( 'wind', $weather_conditions, true ) ? true : false,
			'wind_dir'   => in_array( 'wind_dir', $weather_conditions, true ) ? true : false,
			'humidity'   => in_array( 'humidity', $weather_conditions, true ) ? true : false,
			'pressure'   => in_array( 'pressure', $weather_conditions, true ) ? true : false,
			'desc'       => in_array( 'desc', $weather_conditions, true ) ? true : false,
			'feels_like' => in_array( 'feels_like', $weather_conditions, true ) ? true : false,
		);

		?>
		<div class="premium-weather__tabs-content-wrapper">
			<div class="premium-weather__weather-indicators">
				<span class='premium-weather__weather-indicator' title="Hours" aria-label="hours">
					<svg id="Weather_Icons" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs><style>.premium-weather-cls-1{fill:#333;fill-rule:evenodd;}</style></defs><g id="Time"><path class="premium-weather-cls-1" d="m21.68,7.65c-.07-.88-.22-1.6-.56-2.26-.55-1.08-1.43-1.96-2.51-2.51-.66-.34-1.38-.49-2.26-.56-.87-.07-1.94-.07-3.32-.07h-2.06c-1.38,0-2.45,0-3.32.07-.88.07-1.6.22-2.26.56-1.08.55-1.96,1.43-2.51,2.51-.34.66-.49,1.38-.56,2.26-.07.87-.07,1.94-.07,3.32v2.06c0,1.38,0,2.45.07,3.32.07.88.22,1.6.56,2.26.55,1.08,1.43,1.96,2.51,2.51.66.34,1.38.49,2.26.56.87.07,1.94.07,3.32.07h2.06c1.38,0,2.45,0,3.32-.07.88-.07,1.6-.22,2.26-.56,1.08-.55,1.96-1.43,2.51-2.51.34-.66.49-1.38.56-2.26.07-.87.07-1.94.07-3.32v-2.06c0-1.38,0-2.45-.07-3.32Zm-4.47,9.56c-.2.19-.45.29-.71.29s-.51-.1-.71-.29l-4.5-4.5c-.18-.19-.29-.44-.29-.71v-5c0-.55.45-1,1-1s1,.45,1,1v4.59l4.21,4.2c.39.39.39,1.03,0,1.42Z"/></g></svg>
				</span>
				<?php if ( $conditions_arr['desc_icon'] ) : ?>
					<span class="premium-weather__weather-indicator empty"></span>
				<?php endif; ?>

				<?php if ( $conditions_arr['temp'] ) : ?>
					<span class="premium-weather__weather-indicator" title="Temperature" aria-label="temperature">
						<svg id="Weather_Icons" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs><style>.premium-weather-cls-1,.premium-weather-cls-2{fill:#333;}.premium-weather-cls-2{fill-rule:evenodd;}</style></defs><g id="Temperature"><path class="premium-weather-cls-1" d="m13.5,17c0,.83-.68,1.5-1.5,1.5s-1.5-.67-1.5-1.5c0-.55.3-1.05.77-1.31.2-.11.3-.34.24-.56-.01-.04-.01-.08-.01-.13V5c0-.27.22-.5.5-.5s.5.23.5.5v10c0,.05-.01.09-.02.13-.06.22.04.45.24.56.48.26.78.76.78,1.31Z"/><path class="premium-weather-cls-1" d="m15.5,12.76v-7.76c0-1.93-1.57-3.5-3.5-3.5s-3.5,1.57-3.5,3.5v7.76c-1.26,1.04-2,2.58-2,4.24,0,3.04,2.46,5.5,5.5,5.5s5.5-2.46,5.5-5.5c0-1.66-.75-3.2-2-4.24Zm-3.5,6.74c-1.39,0-2.5-1.12-2.5-2.5,0-.8.38-1.53,1-2V5c0-.82.67-1.5,1.5-1.5s1.5.68,1.5,1.5v10c.62.47,1,1.2,1,2,0,1.38-1.12,2.5-2.5,2.5Z"/><path class="premium-weather-cls-2" d="m13.5,17c0,.83-.68,1.5-1.5,1.5s-1.5-.67-1.5-1.5c0-.55.3-1.05.77-1.31.2-.11.3-.34.24-.56-.01-.04-.01-.08-.01-.13V5c0-.27.22-.5.5-.5s.5.23.5.5v10c0,.05-.01.09-.02.13-.06.22.04.45.24.56.48.26.78.76.78,1.31Z"/></g></svg>
					</span>
				<?php endif; ?>

				<?php if ( $conditions_arr['feels_like'] ) : ?>
					<span class="premium-weather__weather-indicator" title="Feels Like" aria-label="feels like">
						<svg id="Weather_Icons" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs><style>.premium-weather-cls-1,.premium-weather-cls-2{fill:#333;}.premium-weather-cls-2{fill-rule:evenodd;}</style></defs><g id="Feels_Like"><path class="premium-weather-cls-2" d="m19,7c0,2-2,4-4,4,2,0,4,2,4,4,0-2,2-4,4-4-2,0-4-2-4-4Zm-7,8c0,2-2,4-4,4,2,0,4,2,4,4,0-2,2-4,4-4-2,0-4-2-4-4Z"/><path class="premium-weather-cls-1" d="m1,8c3.5,0,7-3.5,7-7,0,3.5,3.5,7,7,7-3.5,0-7,3.5-7,7,0-3.5-3.5-7-7-7Z"/></g></svg>
					</span>
				<?php endif; ?>

				<?php if ( $conditions_arr['wind'] ) : ?>
					<span class="premium-weather__weather-indicator" title="Wind Speed" aria-label="wind speed"><i class="fas fa-wind" aria-hidden='true'></i></span>
				<?php endif; ?>

				<?php if ( $conditions_arr['wind_dir'] ) : ?>
					<span class="premium-weather__weather-indicator" title="Wind Direction" aria-label="wind direction">
						<svg id="Weather_Icons" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs><style>.premium-weather-cls-1{fill:#333;fill-rule:evenodd;}</style></defs><g id="Wind_Direction"><path class="premium-weather-cls-1" d="m22,6.71l.71-.71-.71-.71-4-4c-.39-.39-1.02-.39-1.41,0-.39.39-.39,1.02,0,1.41l2.29,2.29h-2.97c-.87,0-1.52,0-2.12.19-.53.17-1.02.45-1.44.82-.47.42-.8.99-1.24,1.74l-.07.12-4.22,7.24c-.54.93-.71,1.19-.91,1.38-.21.19-.45.33-.72.41-.26.09-.58.1-1.65.1h-1.24c-.55,0-1,.45-1,1s.45,1,1,1h1.38s0,0,0,0c.87,0,1.52,0,2.12-.19.53-.17,1.02-.45,1.44-.83.47-.42.8-.99,1.24-1.74l.07-.12,4.22-7.24c.54-.93.71-1.19.91-1.38.21-.19.45-.33.72-.41.26-.09.58-.1,1.65-.1h2.83l-2.29,2.29c-.39.39-.39,1.02,0,1.41.39.39,1.02.39,1.41,0l4-4Z"/><path class="premium-weather-cls-1" d="m7.25,9.62l-.5-.86-.44-.75v-.02c-.37-.61-1.02-.98-1.73-.99h-.02s-2.26,0-2.26,0c-.55,0-1-.45-1-1s.45-1,1-1h2.29c1.41,0,2.71.75,3.43,1.97l.02.03.44.75.5.86-1.73,1.01Z"/><path class="premium-weather-cls-1" d="m22,17.29l.71.71-.71.71-4,4c-.39.39-1.02.39-1.41,0-.39-.39-.39-1.02,0-1.41l2.29-2.29h-3.88c-1.41,0-2.71-.75-3.43-1.97v-.03s-.45-.75-.45-.75l-.5-.86,1.73-1.01.5.86.44.75v.02c.37.61,1.02.98,1.73.99h3.87l-2.29-2.29c-.39-.39-.39-1.02,0-1.41.39-.39,1.02-.39,1.41,0l4,4"/></g></svg>
					</span>
				<?php endif; ?>

				<?php if ( $conditions_arr['humidity'] ) : ?>
					<span class="premium-weather__weather-indicator" title="Humidity" aria-label="humidity">
						<svg id="Weather_Icons" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs><style>.premium-weather-cls-1{fill:#333;}</style></defs><path id="Humidity" class="premium-weather-cls-1" d="m19.5,15.28c0,3.99-3.37,7.22-7.5,7.22s-7.5-3.23-7.5-7.22c0-2.63,2.36-7.11,7.09-13.57.2-.27.61-.27.81,0,4.73,6.46,7.1,10.94,7.1,13.57Z"/></svg>
					</span>
				<?php endif; ?>

				<?php if ( $conditions_arr['pressure'] ) : ?>
					<span class="premium-weather__weather-indicator" title="Pressure" aria-label="pressure">
						<svg id="Weather_Icons" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs><style>.premium-weather-cls-1{fill:#333;}</style></defs><path id="Weather_Pressure" class="premium-weather-cls-1" d="m12,1.5C6.2,1.5,1.5,6.2,1.5,12s4.7,10.5,10.5,10.5,10.5-4.7,10.5-10.5S17.8,1.5,12,1.5Zm0,1.75c.48,0,.87.39.87.88s-.39.87-.87.87-.88-.39-.88-.87.39-.88.88-.88Zm-4.05,1.06c.34-.04.68.12.86.43.24.42.1.95-.32,1.2s-.95.1-1.19-.32c-.24-.42-.1-.95.32-1.19.1-.06.22-.1.33-.11Zm8.09,0c.11.01.23.05.33.11.42.24.56.78.32,1.19-.24.42-.78.56-1.19.32s-.56-.78-.32-1.2c.18-.31.53-.47.86-.43Zm-10.76,2.88c.11.01.22.05.33.11.42.24.56.78.32,1.19s-.78.56-1.2.32c-.42-.24-.56-.78-.32-1.19.18-.31.53-.47.87-.43Zm13.43.03c.34-.04.68.12.86.43.24.42.1.95-.32,1.19l-5.51,3.16c0,.96-.78,1.74-1.75,1.74-.32,0-.61-.09-.87-.24l-1.91,1.1c-.14.08-.29.12-.44.12-.3,0-.6-.16-.76-.44-.24-.42-.09-.95.32-1.19l1.91-1.09c0-.96.79-1.74,1.75-1.74.32,0,.61.09.87.24l5.51-3.16c.1-.06.22-.1.33-.11Zm-14.59,3.91c.48,0,.87.39.87.88s-.39.87-.87.87-.88-.39-.88-.87.39-.88.88-.88Zm15.75,0c.48,0,.88.39.88.88s-.39.87-.88.87-.88-.39-.88-.87.39-.88.88-.88Zm-14.8,3.94c.34-.04.69.12.87.43.24.42.1.95-.32,1.19-.42.24-.95.1-1.19-.32s-.1-.95.32-1.19c.1-.06.22-.1.33-.11Zm13.86,0c.11.01.23.05.33.11.42.24.56.78.32,1.19-.24.42-.78.56-1.19.32-.42-.24-.56-.78-.32-1.19.18-.31.53-.47.87-.43Z"/></svg>
					</span>
				<?php endif; ?>

				<?php if ( $conditions_arr['desc'] ) : ?>
					<span class="premium-weather__weather-indicator" title="Weather Description" aria-label="weather description">
						<svg id="Weather_Icons" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs><style>.premium-weather-cls-1,.premium-weather-cls-2{fill:#333;}.premium-weather-cls-2{fill-rule:evenodd;}</style></defs><g id="Description"><path class="premium-weather-cls-2" d="m22,9.79c0,2.2-1.13,3.58-2.22,4.37-.53.38-1.06.63-1.46.79-.2.07-.36.13-.49.16-.06.02-.11.03-.14.04-.02,0-.03,0-.04,0,0,0-.01,0-.01.01h-.03l-.1.03H6.49s-.03-.01-.03-.01c-.01-.01-.02-.01-.04-.01-.04-.01-.09-.01-.15-.02-.12-.02-.29-.06-.49-.11-.39-.1-.92-.28-1.47-.58-1.11-.62-2.31-1.81-2.31-3.87s1.19-3.28,2.29-3.93c.5-.3.99-.49,1.37-.6.4-2.6,2.73-4.28,5.14-4.74,2.58-.49,5.63.32,7.28,3.11.08.03.17.06.27.11.39.16.92.43,1.45.83,1.08.82,2.2,2.22,2.2,4.41Z"/><path class="premium-weather-cls-1" d="m15.6,22.8h-6c-.55,0-1-.45-1-1s.45-1,1-1h6c.55,0,1,.45,1,1s-.45,1-1,1Zm3.6-3.6h-3.6c-.55,0-1-.45-1-1s.45-1,1-1h3.6c.55,0,1,.45,1,1s-.45,1-1,1Zm-8.4,0h-6c-.55,0-1-.45-1-1s.45-1,1-1h6c.55,0,1,.45,1,1s-.45,1-1,1Z"/></g></svg>
					</span>
				<?php endif; ?>
			</div>
		<?php
		foreach ( $headers as $date ) {
			?>
				<div id ='premium-tab-content-<?php echo esc_attr( $i ); ?>' class='premium-weather__tab-content <?php echo $i === 0 ? ' current' : ''; ?>'>
				<?php
				if ( isset( $forecast_data[ $date ] ) ) {
					$this->render_tabs_hourly_forecast( $forecast_data[ $date ], $conditions_arr );
				} else {
					?>
						<span class="premium-weather__expire-notice"> <?php echo esc_html( $settings['date_notice'] ); ?></span>
					<?php
				}
				?>
				</div>
				<?php
				$i++;
		}
		?>
		</div>
		<?php
	}

	/**
	 * Extracts Stock Symbols from a string separated by ",".
	 *
	 * @access public
	 * @since 2.8.23
	 *
	 * @param string $symbol_str symbols strings.
	 *
	 * @return array
	 */
	private function extract_forecast_dates( $dates_str ) {

		$dates = explode( ',', $dates_str );

		$dates = array_slice( $dates, 0, intval( $this->settings['forecast_days'] ) );

		return $dates;
	}

	private function render_tabs_hourly_forecast( $data, $conditions_arr ) {

		$settings           = $this->settings;
		$limit              = $settings['tabs_hourly_max'];
		$weather_conditions = $settings['tabs_weather_data'];

		for ( $i = 0; $i < $limit; $i++ ) {
			$item = isset( $data[ $i ] ) ? $data[ $i ] : false;

			if ( $item ) {
				$weather_desc = $item['weather'][0]['description'];

				?>
				<div class="premium-weather__hourly-item">
					<span class="premium-weather__hourly-item-date"><?php echo esc_html( date( 'h:i A', $item['dt'] ) ); ?></span>

					<?php if ( $conditions_arr['desc_icon'] ) : ?>
						<div class="premium-weather__icon-wrapper" title="<?php echo esc_attr( $weather_desc ); ?>">
							<?php $this->render_weather_icon( $item['weather'][0]['icon'] ); ?>
						</div>
					<?php endif; ?>

					<?php if ( $conditions_arr['temp'] ) : ?>
						<span class="premium-weather__temp">
							<?php
								echo esc_html( round( $item['main']['temp'], 0 ) ) . '&#176;';
							?>
						</span>
					<?php endif; ?>

					<?php if ( $conditions_arr['feels_like'] ) : ?>
						<span class="premium-weather__temp">
							<?php
								echo esc_html( round( $item['main']['temp'], 0 ) ) . '&#176;';
							?>
						</span>
					<?php endif; ?>

					<?php
						$item['main']['wind_speed'] = $item['wind']['speed'];
						$item['main']['wind_dir']   = $item['wind']['deg'];
						$this->render_extra_weather( $weather_conditions, $item['main'], true );
					?>

					<?php if ( $conditions_arr['desc'] ) : ?>
						<div class="premium-weather__hourly-desc"> <?php echo esc_html( $item['weather'][0]['description'] ); ?></div>
					<?php endif; ?>
				</div>
				<?php
			}
		}
	}

}
