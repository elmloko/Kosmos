<?php
/**
 * Premium World Clock.
 */

namespace PremiumAddons\Widgets;

// Elementor Classes.
use Elementor\Widget_Base;
use Elementor\Icons_Manager;
use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

// PremiumAddons Classes.
use PremiumAddons\Includes\Helper_Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

/**
 * Class Premium_World_Clock.
 */
class Premium_World_Clock extends Widget_Base {

	/**
	 * Options
	 *
	 * @var options
	 */
	private $options = null;

	/**
	 * Retrieve Widget Name.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_name() {
		return 'premium-world-clock';
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return __( 'World Clock', 'premium-addons-for-elementor' );
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
		return 'pa-world-clock';
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
			'pa-world-clock',
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
			'pa-luxon',
			'premium-addons',
		);
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
	public function get_keywords() {
		return array( 'pa', 'premium', 'world', 'clock', 'timezone', 'time' );
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
	 * Register World Clock controls.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function register_controls() {

		$this->options = apply_filters(
			'pa_clock_options',
			array(
				'skins'          => array(
					'digital' => array(
						'label'   => __( 'Digital', 'premium-addons-for-elementor' ),
						'options' => array(
							'skin-2' => __( 'Layout 1', 'premium-addons-for-elementor' ),
							'skin-3' => __( 'Layout 2', 'premium-addons-for-elementor' ),
							'skin-4' => __( 'Layout 3 (Pro)', 'premium-addons-for-elementor' ),
						),
					),
					'analog'  => array(
						'label'   => __( 'Analog', 'premium-addons-for-elementor' ),
						'options' => array(
							'skin-1' => __( 'Style 1', 'premium-addons-for-elementor' ),
							'skin-5' => __( 'Style 2', 'premium-addons-for-elementor' ),
							'skin-6' => __( 'Style 3 (Pro)', 'premium-addons-for-elementor' ),
							'skin-7' => __( 'Style 4 (Pro)', 'premium-addons-for-elementor' ),
						),
					),
				),
				'skin_condition' => array( 'skin-4', 'skin-6', 'skin-7' ),
			)
		);

		$this->add_clock_controls();

		$this->add_additional_option_controls();

		$this->add_helpful_info_section();

		$this->add_units_style_controls();

		$this->add_clock_style_controls();

		$this->add_info_style_controls();

		$this->add_days_style_controls();

		$this->add_clock_num_style_controls();
	}

	/** Content Controls. */
	private function add_clock_controls() {

		$this->start_controls_section(
			'premium_world_clock_content',
			array(
				'label' => __( 'Clock', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'tz_type',
			array(
				'label'       => __( 'Timezone', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'local'  => __( 'Current Timezone', 'premium-addons-for-elementor' ),
					'custom' => __( 'Custom Timezone', 'premium-addons-for-elementor' ),
				),
				'default'     => 'local',
				'render_type' => 'template',
			)
		);

		$this->add_control(
			'custom_tz',
			array(
				'label'       => __( 'Zone Name', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'description' => 'Get your Time Zone from <a href="https://timezonedb.com/time-zones" target="_blank">Time Zones Of The World.</a>.',
				'dynamic'     => array( 'active' => true ),
				'condition'   => array(
					'tz_type' => 'custom',
				),
				'render_type' => 'template',
			)
		);

		$papro_activated = apply_filters( 'papro_activated', false );

		$this->add_control(
			'skin',
			array(
				'label'        => __( 'Layout', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SELECT,
				'prefix_class' => 'premium-world-clock__',
				'render_type'  => 'template',
				'groups'       => $this->options['skins'],
				'default'      => 'skin-2',
			)
		);

		if ( ! $papro_activated ) {

			$get_pro = Helper_Functions::get_campaign_link( 'https://premiumaddons.com/pro', 'editor-page', 'wp-editor', 'get-pro' );

			$this->add_control(
				'clock_notice',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => __( 'This option is available in Premium Addons Pro. ', 'premium-addons-for-elementor' ) . '<a href="' . esc_url( $get_pro ) . '" target="_blank">' . __( 'Upgrade now!', 'premium-addons-for-elementor' ) . '</a>',
					'content_classes' => 'papro-upgrade-notice',
					'condition'       => array(
						'skin' => $this->options['skin_condition'],
					),
				)
			);

		}

		$this->add_control(
			'clock_hands',
			array(
				'label'        => __( 'Clock Hands Style', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SELECT,
				'prefix_class' => 'premium-world-clock__',
				'options'      => array(
					'hand-0' => __( 'Style 1', 'premium-addons-for-elementor' ),
					'hand-1' => __( 'Style 2', 'premium-addons-for-elementor' ),
					'hand-2' => __( 'Style 3', 'premium-addons-for-elementor' ),
					'hand-3' => __( 'Style 4', 'premium-addons-for-elementor' ),
					'hand-4' => __( 'Style 5', 'premium-addons-for-elementor' ),
					'hand-5' => __( 'Style 6', 'premium-addons-for-elementor' ),
				),
				'default'      => 'hand-0',
				'condition'    => array(
					'skin' => array( 'skin-1', 'skin-5', 'skin-6', 'skin-7' ),
				),
			)
		);

		$this->add_control(
			'time_format',
			array(
				'label'       => __( 'Time Format', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'hh' => __( '12 Hours', 'premium-addons-for-elementor' ),
					'HH' => __( '24 Hours', 'premium-addons-for-elementor' ),
				),
				'default'     => 'HH',
				'render_type' => 'template',
				'condition'   => array(
					'skin' => array( 'skin-2', 'skin-3', 'skin-4' ),
				),
			)
		);

		$this->add_control(
			'show_time',
			array(
				'label'   => __( 'Show Clock', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'show_indicators',
			array(
				'label'     => __( 'Show Clock Faces', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => array(
					'show_time'       => 'yes',
					'show_clock_num!' => 'yes',
					'skin'            => array( 'skin-1', 'skin-5', 'skin-6', 'skin-7' ),
				),
			)
		);

		$this->add_control(
			'show_clock_num',
			array(
				'label'     => __( 'Show Clock Numbers', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'show_time' => 'yes',
					'skin'      => array( 'skin-1', 'skin-5', 'skin-6' ),
				),
			)
		);

		$this->add_responsive_control(
			'clock_num_spacing',
			array(
				'label'      => __( 'Clock Number Spacing', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-world-clock__clock-numbers' => 'width: calc( 100% - {{SIZE}}% ); height: calc( 100% - {{SIZE}}% );',
				),
				'condition'  => array(
					'show_time'      => 'yes',
					'show_clock_num' => 'yes',
					'skin'           => array( 'skin-1', 'skin-5', 'skin-6' ),
				),
			)
		);

		$this->add_control(
			'show_seconds',
			array(
				'label'     => __( 'Show Seconds', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'show_time' => 'yes',
					'skin'      => array( 'skin-2', 'skin-3', 'skin-4' ),
				),
			)
		);

		$this->add_control(
			'show_meridiem',
			array(
				'label'      => __( 'Show Meridiem', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SWITCHER,
				'default'    => 'yes',
				'conditions' => array(
					'relation' => 'and',
					'terms'    => array(
						array(
							'name'     => 'show_time',
							'operator' => '===',
							'value'    => 'yes',
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'     => 'skin',
									'operator' => 'in',
									'value'    => array( 'skin-1', 'skin-5', 'skin-6', 'skin-7' ),
								),
								array(
									'relation' => 'and',
									'terms'    => array(
										array(
											'name'     => 'skin',
											'operator' => '!in',
											'value'    => array( 'skin-1', 'skin-5', 'skin-6', 'skin-7' ),
										),
										array(
											'name'     => 'time_format',
											'operator' => '===',
											'value'    => 'hh',
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
			'meridiem_type',
			array(
				'label'       => __( 'Show As:', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'render_type' => 'template',
				'options'     => array(
					'text' => __( 'Text', 'premium-addons-for-elementor' ),
					'icon' => __( 'Icon', 'premium-addons-for-elementor' ),
				),
				'default'     => 'text',
				'condition'   => array(
					'time_format' => 'hh',
					'show_time'   => 'yes',
					'skin'        => array( 'skin-2', 'skin-3', 'skin-4' ),
				),
			)
		);

		$this->add_control(
			'show_timezone_offset',
			array(
				'label'       => __( 'Show GMT Offset', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Shows the number of hours and minutes that the time differs from Greenwich Mean Time', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'offset_format',
			array(
				'label'       => __( 'Offset Format', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'Z'  => __( '+5HRS', 'premium-addons-for-elementor' ),
					'ZZ' => __( '+05:00', 'premium-addons-for-elementor' ),
				),
				'default'     => 'ZZ',
				'render_type' => 'template',
				'condition'   => array(
					'show_timezone_offset' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'clock_size',
			array(
				'label'      => __( 'Clock Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}}:not(.premium-world-clock__skin-1):not(.premium-world-clock__skin-4) .premium-world-clock__time-wrapper,
                    {{WRAPPER}}.premium-world-clock__skin-1 .premium-world-clock__circle,
                    {{WRAPPER}}.premium-world-clock__skin-5 .premium-world-clock__circle,
                    {{WRAPPER}}.premium-world-clock__skin-6 .premium-world-clock__circle,
                    {{WRAPPER}}.premium-world-clock__skin-7 .premium-world-clock__circle'  => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.premium-world-clock__skin-4 .premium-world-clock__time-wrapper,
                    {{WRAPPER}}.premium-world-clock__skin-4 .premium-world-clock__date-wrapper' => 'width: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'skin!'     => array( 'skin-3' ),
					'show_time' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'time_spacing',
			array(
				'label'      => __( 'Units Spacing', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'condition'  => array(
					'skin'      => array( 'skin-2', 'skin-3', 'skin-4' ),
					'show_time' => 'yes',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-world-clock__time-wrapper'  => 'gap: {{SIZE}}px;',
				),
			)
		);

		$this->add_responsive_control(
			'meridiem_hor',
			array(
				'label'      => __( 'Meridiem Horizontal Position', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => -100,
						'max' => 300,
					),
				),
				'conditions' => array(
					'terms' => array(
						array(
							'name'     => 'show_time',
							'operator' => '===',
							'value'    => 'yes',
						),
						array(
							'name'     => 'show_meridiem',
							'operator' => '===',
							'value'    => 'yes',
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'     => 'skin',
									'operator' => 'in',
									'value'    => array( 'skin-1', 'skin-5', 'skin-6', 'skin-7' ),
								),
								array(
									'terms' => array(
										array(
											'name'     => 'skin',
											'operator' => '!in',
											'value'    => array( 'skin-1', 'skin-5', 'skin-6', 'skin-7' ),
										),
										array(
											'name'     => 'time_format',
											'operator' => '===',
											'value'    => 'hh',
										),
									),
								),
							),
						),
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-world-clock__meridiem'  => 'left: {{SIZE}}px;',
				),
			)
		);

		$this->add_responsive_control(
			'meridiem_ver',
			array(
				'label'      => __( 'Meridiem Vertical Position', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => -100,
						'max' => 300,
					),
				),
				'conditions' => array(
					'terms' => array(
						array(
							'name'     => 'show_time',
							'operator' => '===',
							'value'    => 'yes',
						),
						array(
							'name'     => 'show_meridiem',
							'operator' => '===',
							'value'    => 'yes',
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'     => 'skin',
									'operator' => 'in',
									'value'    => array( 'skin-1', 'skin-5', 'skin-6', 'skin-7' ),
								),
								array(
									'terms' => array(
										array(
											'name'     => 'skin',
											'operator' => '!in',
											'value'    => array( 'skin-1', 'skin-5', 'skin-6', 'skin-7' ),
										),
										array(
											'name'     => 'time_format',
											'operator' => '===',
											'value'    => 'hh',
										),
									),
								),
							),
						),
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-world-clock__meridiem'  => 'top: {{SIZE}}px;',
				),
			)
		);

		$this->end_controls_section();
	}

	private function add_additional_option_controls() {

		$info_conditions = array(
			'relation' => 'or',
			'terms'    => array(
				array(
					'name'     => 'clock_title',
					'operator' => '!==',
					'value'    => '',
				),
				array(
					'name'  => 'date',
					'value' => 'yes',
				),
				array(
					'name'  => 'show_timezone_offset',
					'value' => 'yes',
				),
			),
		);

		$info_display_conditions = array(
			'relation' => 'or',
			'terms'    => array(
				array(
					'terms' => array(
						array(
							'name'     => 'clock_title',
							'operator' => '!==',
							'value'    => '',
						),
						array(
							'name'  => 'date',
							'value' => 'yes',
						),
					),
				),
				array(
					'terms' => array(
						array(
							'name'     => 'clock_title',
							'operator' => '!==',
							'value'    => '',
						),
						array(
							'name'  => 'show_timezone_offset',
							'value' => 'yes',
						),
					),
				),
				array(
					'terms' => array(
						array(
							'name'  => 'show_timezone_offset',
							'value' => 'yes',
						),
						array(
							'name'  => 'date',
							'value' => 'yes',
						),
					),
				),
			),
		);

		$this->start_controls_section(
			'additional_options_section',
			array(
				'label'     => __( 'Additional Settings', 'premium-addons-for-elementor' ),
				'condition' => array(
					'skin!' => $this->options['skin_condition'],
				),
			)
		);

		$this->add_control(
			'clock_title',
			array(
				'label'       => __( 'Title', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
			)
		);

		$this->add_control(
			'date',
			array(
				'label'   => __( 'Show Date', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'days_num',
			array(
				'label'       => __( 'Days Before/After', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'description' => __( 'Number of days to show before/after the current day.', 'premium-addons-for-elementor' ),
				'options'     => array(
					'1' => __( '1 Day', 'premium-addons-for-elementor' ),
					'2' => __( '2 Days', 'premium-addons-for-elementor' ),
					'3' => __( '3 Days', 'premium-addons-for-elementor' ),
				),
				'default'     => '3',
				'render_type' => 'template',
				'condition'   => array(
					'date' => 'yes',
					'skin' => 'skin-3',
				),
			)
		);

		$this->add_control(
			'date_format',
			array(
				'label'       => __( 'Date Format', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'render_type' => 'template',
				'options'     => array(
					'D'    => __( '8/6/2023', 'premium-addons-for-elementor' ),
					'DD'   => __( 'Aug 6, 2023', 'premium-addons-for-elementor' ),
					'DDD'  => __( 'August 6, 2023', 'premium-addons-for-elementor' ),
					'DDDD' => __( 'Wednesday, August 6, 2023', 'premium-addons-for-elementor' ),
				),
				'default'     => 'DDD',
				'condition'   => array(
					'date'  => 'yes',
					'skin!' => array( 'skin-3', 'skin-4' ),
				),
			)
		);

		$this->add_control(
			'date_format_digital',
			array(
				'label'       => __( 'Date Format', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'render_type' => 'template',
				'options'     => array(
					'dn|m|d' => __( 'Day Number | Month | Day', 'premium-addons-for-elementor' ),
					'm|dn|d' => __( 'Month | Day Number | Day', 'premium-addons-for-elementor' ),
					'custom' => __( 'custom', 'premium-addons-for-elementor' ),
				),
				'default'     => 'm|dn|d',
				'condition'   => array(
					'date' => 'yes',
					'skin' => 'skin-4',
				),
			)
		);

		$this->add_control(
			'custom_date_format',
			array(
				'label'       => __( 'Custom Date Format', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'description' => 'Use this option to re-arrange the date segments separated by |, EX: d|m|dn, d = day name, m = month name, dn = day number',
				'render_type' => 'template',
				'condition'   => array(
					'skin'                => 'skin-4',
					'date'                => 'yes',
					'date_format_digital' => 'custom',
				),
			)
		);

		$this->add_responsive_control(
			'unit_display',
			array(
				'label'        => __( 'Units Display', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::CHOOSE,
				'prefix_class' => 'premium-world-clock__unit-',
				'toggle'       => false,
				'separator'    => 'before',
				'options'      => array(
					'row'    => array(
						'title' => __( 'Inline', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-navigation-horizontal',
					),
					'column' => array(
						'title' => __( 'Block', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-navigation-vertical',
					),
				),
				'default'      => 'row',
				'condition'    => array(
					'skin'      => 'skin-2',
					'show_time' => 'yes',
				),
				'selectors'    => array(
					'{{WRAPPER}} .premium-world-clock__time-wrapper' => 'flex-direction: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'layout',
			array(
				'label'        => __( 'Display', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::CHOOSE,
				'prefix_class' => 'premium-world-clock__',
				'toggle'       => false,
				'separator'    => 'before',
				'options'      => array(
					'row'    => array(
						'title' => __( 'Inline', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-navigation-horizontal',
					),
					'column' => array(
						'title' => __( 'Block', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-navigation-vertical',
					),
				),
				'default'      => 'column',
				'conditions'   => array(
					'terms' => array(
						array(
							'name'  => 'show_time',
							'value' => 'yes',
						),
						array(
							'name'     => 'skin',
							'operator' => '!==',
							'value'    => 'skin-3',
						),
						$info_conditions,
					),
				),
				'selectors'    => array(
					'{{WRAPPER}} .premium-world-clock__clock-wrapper' => 'flex-direction: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'info_layout',
			array(
				'label'        => __( 'Info Display', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::CHOOSE,
				'prefix_class' => 'premium-world-clock__info-',
				'toggle'       => false,
				'options'      => array(
					'row'    => array(
						'title' => __( 'Inline', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-navigation-horizontal',
					),
					'column' => array(
						'title' => __( 'Block', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-navigation-vertical',
					),
				),
				'default'      => 'column',
				'conditions'   => array(
					'terms' => array(
						array(
							'terms' => array(
								array(
									'name'     => 'skin',
									'operator' => '!in',
									'value'    => array( 'skin-3', 'skin-4' ),
								),
								$info_display_conditions,
							),
						),
					),
				),
				'selectors'    => array(
					'{{WRAPPER}} .premium-world-clock__additonal-info' => 'flex-direction: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'info_layout_digital',
			array(
				'label'        => __( 'Info Display', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::CHOOSE,
				'prefix_class' => 'premium-world-clock__info-',
				'toggle'       => false,
				'options'      => array(
					'row'    => array(
						'title' => __( 'Inline', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-navigation-horizontal',
					),
					'column' => array(
						'title' => __( 'Block', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-navigation-vertical',
					),
				),
				'default'      => 'column',
				'conditions'   => array(
					'terms' => array(
						array(
							'name'     => 'skin',
							'operator' => 'in',
							'value'    => array( 'skin-3', 'skin-4' ),
						),
						array(
							'name'     => 'clock_title',
							'operator' => '!==',
							'value'    => '',
						),
						array(
							'name'  => 'show_timezone_offset',
							'value' => 'yes',
						),
					),
				),
				'selectors'    => array(
					'{{WRAPPER}} .premium-world-clock__additonal-info' => 'flex-direction: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'info_order',
			array(
				'label'      => __( 'Clock Info Order', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::CHOOSE,
				'toggle'     => false,
				'options'    => array(
					'0' => array(
						'title' => __( 'Start', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-order-start',
					),
					'2' => array(
						'title' => __( 'End', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-order-end',
					),
				),
				'default'    => '0',
				'conditions' => array(
					'terms' => array(
						array(
							'name'     => 'show_time',
							'operator' => '===',
							'value'    => 'yes',
						),
						$info_conditions,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-world-clock__additonal-info' => 'order: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'title_order',
			array(
				'label'      => __( 'Title Order', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::CHOOSE,
				'toggle'     => false,
				'options'    => array(
					'0' => array(
						'title' => __( 'Start', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-order-start',
					),
					'2' => array(
						'title' => __( 'End', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-order-end',
					),
				),
				'default'    => '0',
				'conditions' => array(
					'terms' => array(
						array(
							'name'     => 'skin',
							'operator' => '!in',
							'value'    => array( 'skin-3', 'skin-4' ),
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'terms' => array(
										array(
											'name'     => 'clock_title',
											'operator' => '!==',
											'value'    => '',
										),
										array(
											'name'  => 'date',
											'value' => 'yes',
										),
									),
								),
								array(
									'terms' => array(
										array(
											'name'     => 'clock_title',
											'operator' => '!==',
											'value'    => '',
										),
										array(
											'name'  => 'show_timezone_offset',
											'value' => 'yes',
										),
									),
								),
							),
						),
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-world-clock__clock-title' => 'order: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'clock_alignment',
			array(
				'label'     => __( 'Clock Alignment', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => __( 'Start', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-order-start',
					),
					'center'     => array(
						'title' => __( 'Center', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-center',
					),
					'flex-end'   => array(
						'title' => __( 'End', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-order-end',
					),
				),
				'default'   => 'center',
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}}:not(.premium-world-clock__column) .premium-world-clock__clock-wrapper' => 'justify-content: {{VALUE}};',
					'{{WRAPPER}}.premium-world-clock__column .premium-world-clock__clock-wrapper' => 'align-items: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'clock_ver_align',
			array(
				'label'      => __( 'Clock Vertical Alignment', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::CHOOSE,
				'options'    => array(
					'flex-start' => array(
						'title' => __( 'Top', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-arrow-up',
					),
					'center'     => array(
						'title' => __( 'Center', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-justify',
					),
					'flex-end'   => array(
						'title' => __( 'Bottom', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-arrow-down',
					),
				),
				'default'    => 'center',
				'toggle'     => false,
				'conditions' => array(
					'relation' => 'and',
					'terms'    => array(
						$info_conditions,
						array(
							'name'  => 'layout',
							'value' => 'row',
						),
						array(
							'name'  => 'show_time',
							'value' => 'yes',
						),
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-world-clock__clock-wrapper' => 'align-items: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'info_txt_align',
			array(
				'label'      => __( 'Info Alignment', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::CHOOSE,
				'options'    => array(
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
				'toggle'     => false,
				'default'    => 'center',
				'conditions' => array(
					'relation' => 'and',
					'terms'    => array(
						array(
							'name'     => 'skin',
							'operator' => '!in',
							'value'    => array( 'skin-3', 'skin-4' ),
						),
						array(
							'name'     => 'info_layout',
							'operator' => '===',
							'value'    => 'column',
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'terms' => array(
										array(
											'name'     => 'clock_title',
											'operator' => '!==',
											'value'    => '',
										),
										array(
											'name'  => 'date',
											'value' => 'yes',
										),
									),
								),
								array(
									'terms' => array(
										array(
											'name'     => 'clock_title',
											'operator' => '!==',
											'value'    => '',
										),
										array(
											'name'  => 'show_timezone_offset',
											'value' => 'yes',
										),
									),
								),
								array(
									'terms' => array(
										array(
											'name'  => 'show_timezone_offset',
											'value' => 'yes',
										),
										array(
											'name'  => 'date',
											'value' => 'yes',
										),
									),
								),
							),
						),
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-world-clock__additonal-info' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'info_txt_align_digital',
			array(
				'label'      => __( 'Info Alignment', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::CHOOSE,
				'options'    => array(
					'flex-start' => array(
						'title' => __( 'Start', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'     => array(
						'title' => __( 'Center', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end'   => array(
						'title' => __( 'End', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'toggle'     => false,
				'default'    => 'center',
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'relation' => 'and',
							'terms'    => array(
								array(
									'name'     => 'skin',
									'operator' => '===',
									'value'    => 'skin-4',
								),
								array(
									'name'     => 'info_layout',
									'operator' => '===',
									'value'    => 'column',
								),
								array(
									'name'     => 'clock_title',
									'operator' => '!==',
									'value'    => '',
								),
								array(
									'name'  => 'show_timezone_offset',
									'value' => 'yes',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms'    => array(
								array(
									'name'     => 'skin',
									'operator' => '===',
									'value'    => 'skin-3',
								),
								array(
									'relation' => 'or',
									'terms'    => array(
										array(
											'name'     => 'clock_title',
											'operator' => '!==',
											'value'    => '',
										),
										array(
											'name'  => 'show_timezone_offset',
											'value' => 'yes',
										),
									),
								),
							),
						),
					),
				),
				'selectors'  => array(
					'{{WRAPPER}}.premium-world-clock__info-column .premium-world-clock__additonal-info' => 'align-items: {{VALUE}};',
					'{{WRAPPER}}:not(.premium-world-clock__info-column) .premium-world-clock__additonal-info' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'days_align',
			array(
				'label'     => __( 'Days Alignment', 'premium-addons-for-elementor' ),
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
				'toggle'    => false,
				'default'   => 'left',
				'condition' => array(
					'skin' => 'skin-3',
					'date' => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-world-clock__days-wrapper' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'clock_container_spacing',
			array(
				'label'      => __( 'Spacing', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'separator'  => 'before',
				'conditions' => array(
					'terms' => array(
						array(
							'name'  => 'show_time',
							'value' => 'yes',
						),
						array(
							'name'     => 'skin',
							'operator' => '!==',
							'value'    => 'skin-4',
						),
						$info_conditions,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}}:not(.premium-world-clock__skin-3) .premium-world-clock__clock-wrapper'  => 'gap: {{SIZE}}px;',
					'{{WRAPPER}}.premium-world-clock__skin-3 .premium-world-clock__clock-wrapper'  => 'gap: unset; column-gap: {{SIZE}}px;',
				),
			)
		);

		$this->add_responsive_control(
			'info_spacing',
			array(
				'label'      => __( 'Info Spacing', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'conditions' => array(
					'relation' => 'and',
					'terms'    => array(
						array(
							'name'     => 'skin',
							'operator' => '!in',
							'value'    => array( 'skin-3', 'skin-4' ),
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'terms' => array(
										array(
											'name'     => 'clock_title',
											'operator' => '!==',
											'value'    => '',
										),
										array(
											'name'  => 'date',
											'value' => 'yes',
										),
									),
								),
								array(
									'terms' => array(
										array(
											'name'     => 'clock_title',
											'operator' => '!==',
											'value'    => '',
										),
										array(
											'name'  => 'show_timezone_offset',
											'value' => 'yes',
										),
									),
								),
								array(
									'terms' => array(
										array(
											'name'  => 'show_timezone_offset',
											'value' => 'yes',
										),
										array(
											'name'  => 'date',
											'value' => 'yes',
										),
									),
								),
							),
						),
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-world-clock__additonal-info'  => 'gap: {{SIZE}}px;',
				),
			)
		);

		$this->add_control(
			'equal_width',
			array(
				'label'       => __( 'Equal Units Width', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Applies equal width on the time units', 'premium-addons-for-elementor' ),
				'separator'   => 'before',
				'condition'   => array(
					'skin' => array( 'skin-2', 'skin-3', 'skin-4' ),
				),
			)
		);

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
			'https://premiumaddons.com/docs/elementor-world-clock-widget/' => __( 'Getting started »', 'premium-addons-for-elementor' ),
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

	/** Style Controls. */
	private function add_clock_style_controls() {

		$this->start_controls_section(
			'clock_style_section',
			array(
				'label'     => __( 'Clock', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_time' => 'yes',
					'skin!'     => $this->options['skin_condition'],
				),
			)
		);

		$this->add_control(
			'clock_face_fill',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-world-clock__circle > svg, {{WRAPPER}} .premium-world-clock__circle > svg *' => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'skin' => array( 'skin-5', 'skin-6', 'skin-7' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'clock_box_shadow',
				'selector' => '{{WRAPPER}}:not(.premium-world-clock__skin-1):not(.premium-world-clock__skin-5):not(.premium-world-clock__skin-6):not(.premium-world-clock__skin-7) .premium-world-clock__time-wrapper,
                {{WRAPPER}}.premium-world-clock__skin-1 .premium-world-clock__circle,
                {{WRAPPER}}.premium-world-clock__skin-5 .premium-world-clock__circle,
                {{WRAPPER}}.premium-world-clock__skin-6 .premium-world-clock__circle,
                {{WRAPPER}}.premium-world-clock__skin-7 .premium-world-clock__circle',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'clock_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}}:not(.premium-world-clock__skin-1):not(.premium-world-clock__skin-5):not(.premium-world-clock__skin-6):not(.premium-world-clock__skin-7) .premium-world-clock__time-wrapper,
                {{WRAPPER}}.premium-world-clock__skin-1 .premium-world-clock__circle,
                {{WRAPPER}}.premium-world-clock__skin-5 .premium-world-clock__circle,
                {{WRAPPER}}.premium-world-clock__skin-6 .premium-world-clock__circle,
                {{WRAPPER}}.premium-world-clock__skin-7 .premium-world-clock__circle',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'clock_border',
				'selector' => '{{WRAPPER}}:not(.premium-world-clock__skin-1):not(.premium-world-clock__skin-5):not(.premium-world-clock__skin-6):not(.premium-world-clock__skin-7) .premium-world-clock__time-wrapper,
                {{WRAPPER}}.premium-world-clock__skin-1 .premium-world-clock__circle,
                {{WRAPPER}}.premium-world-clock__skin-5 .premium-world-clock__circle,
                {{WRAPPER}}.premium-world-clock__skin-6 .premium-world-clock__circle,
                {{WRAPPER}}.premium-world-clock__skin-7 .premium-world-clock__circle',
			)
		);

		$this->add_control(
			'time_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-world-clock__time-wrapper' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'skin' => array( 'skin-2', 'skin-3', 'skin-4' ),
				),
			)
		);

		$this->add_responsive_control(
			'time_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-world-clock__time-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'skin' => array( 'skin-2', 'skin-3', 'skin-4' ),
				),
			)
		);

		$this->end_controls_section();
	}

	private function add_info_style_controls() {

		$info_conditions = array(
			'relation' => 'or',
			'terms'    => array(
				array(
					'name'     => 'clock_title',
					'operator' => '!==',
					'value'    => '',
				),
				array(
					'name'  => 'date',
					'value' => 'yes',
				),
				array(
					'name'  => 'show_timezone_offset',
					'value' => 'yes',
				),
			),
		);

		$this->start_controls_section(
			'clock_info_style_section',
			array(
				'label'      => __( 'Clock Info', 'premium-addons-for-elementor' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => $info_conditions,
			)
		);

		$this->add_control(
			'title_heading',
			array(
				'label'     => esc_html__( 'Title', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => array(
					'clock_title!' => '',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'clock_title_typo',
				'selector'  => '{{WRAPPER}} .premium-world-clock__clock-title',
				'condition' => array(
					'clock_title!' => '',
				),
			)
		);

		$this->add_control(
			'clock_title_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-world-clock__clock-title' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'clock_title!' => '',
				),
			)
		);

		$this->add_control(
			'date_heading',
			array(
				'label'     => esc_html__( 'Date', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'date' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'date_typo',
				'selector'  => '{{WRAPPER}} .premium-world-clock__date,
                 {{WRAPPER}} .premium-world-clock__month, {{WRAPPER}} .premium-world-clock__day, {{WRAPPER}} .premium-world-clock__date-segment',
				'condition' => array(
					'date' => 'yes',
				),
			)
		);

		$this->add_control(
			'date_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-world-clock__date,
                    {{WRAPPER}} .premium-world-clock__month-wrapper,
                    {{WRAPPER}} .premium-world-clock__day-wrapper,
                    {{WRAPPER}} .premium-world-clock__date-segment' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'date' => 'yes',
				),
			)
		);

		$this->add_control(
			'date_border_color',
			array(
				'label'     => __( 'Border Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-world-clock__date-segment' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'date' => 'yes',
					'skin' => 'skin-4',
				),
			)
		);

		$this->add_responsive_control(
			'date_border_width',
			array(
				'label'      => __( 'Border Thickness', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-world-clock__date-segment' => 'border-width: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'date' => 'yes',
					'skin' => 'skin-4',
				),
			)
		);

		$this->add_control(
			'date_cont_heading',
			array(
				'label'     => esc_html__( 'Date Container', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'date' => 'yes',
					'skin' => 'skin-4',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'      => 'date_bg',
				'types'     => array( 'classic', 'gradient' ),
				'selector'  => '{{WRAPPER}} .premium-world-clock__date-wrapper',
				'condition' => array(
					'date' => 'yes',
					'skin' => 'skin-4',
				),
			)
		);

		$this->add_control(
			'date_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-world-clock__date-wrapper' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'date' => 'yes',
					'skin' => 'skin-4',
				),
			)
		);

		$this->add_responsive_control(
			'date_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-world-clock__date-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'date' => 'yes',
					'skin' => 'skin-4',
				),
			)
		);

		$this->add_control(
			'offset_heading',
			array(
				'label'     => esc_html__( 'GMT Offset', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'show_timezone_offset' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'offset_typo',
				'selector'  => '{{WRAPPER}} .premium-world-clock__gmt-offset',
				'condition' => array(
					'show_timezone_offset' => 'yes',
				),
			)
		);

		$this->add_control(
			'offset_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-world-clock__gmt-offset' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'show_timezone_offset' => 'yes',
				),
			)
		);

		$this->add_control(
			'info_cont_heading',
			array(
				'label'     => esc_html__( 'Info Container', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'clock_info_bg',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}}:not(.premium-world-clock__skin-3):not(.premium-world-clock__skin-4) .premium-world-clock__additonal-info',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'clock_info_shadow',
				'selector' => '{{WRAPPER}}:not(.premium-world-clock__skin-3):not(.premium-world-clock__skin-4) .premium-world-clock__additonal-info',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'clock_info_border',
				'selector' => '{{WRAPPER}}:not(.premium-world-clock__skin-3):not(.premium-world-clock__skin-4) .premium-world-clock__additonal-info',
			)
		);

		$this->add_control(
			'clock_info_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}}:not(.premium-world-clock__skin-3):not(.premium-world-clock__skin-4) .premium-world-clock__additonal-info' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'clock_info_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}}:not(.premium-world-clock__skin-3):not(.premium-world-clock__skin-4) .premium-world-clock__additonal-info' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'clock_info_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}}:not(.premium-world-clock__skin-3):not(.premium-world-clock__skin-4) .premium-world-clock__additonal-info' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function add_units_style_controls() {

		$mer_conditions = array(
			'relation' => 'and',
			'terms'    => array(
				array(
					'name'     => 'show_time',
					'operator' => '===',
					'value'    => 'yes',
				),
				array(
					'name'     => 'show_meridiem',
					'operator' => '===',
					'value'    => 'yes',
				),
				array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'skin',
							'operator' => '!in',
							'value'    => array( 'skin-2', 'skin-3', 'skin-4' ),
						),
						array(
							'relation' => 'and',
							'terms'    => array(
								array(
									'name'     => 'skin',
									'operator' => 'in',
									'value'    => array( 'skin-2', 'skin-3', 'skin-4' ),
								),
								array(
									'name'     => 'time_format',
									'operator' => '===',
									'value'    => 'hh',
								),
							),
						),
					),
				),
			),
		);

		$this->start_controls_section(
			'time_style_section',
			array(
				'label'     => __( 'Time Units', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_time' => 'yes',
					'skin!'     => $this->options['skin_condition'],
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'time_typo',
				'label'     => 'Units Typography',
				'selector'  => '{{WRAPPER}} .premium-world-clock__time-wrapper *:not(.premium-world-clock__meridiem)',
				'condition' => array(
					'skin' => array( 'skin-2', 'skin-3', 'skin-4' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'      => 'time_text_shadow',
				'selector'  => '{{WRAPPER}} .premium-world-clock__time-wrapper *',
				'condition' => array(
					'skin' => array( 'skin-2', 'skin-3', 'skin-4' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'       => 'meridiem_typo',
				'label'      => 'Meridiem Typography',
				'selector'   => '{{WRAPPER}} .premium-world-clock__meridiem',
				'conditions' => $mer_conditions,
			)
		);

		$this->add_control(
			'meridiem_size',
			array(
				'label'      => __( 'Meridiem Icon Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-world-clock__meridiem > svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'show_meridiem' => 'yes',
					'time_format'   => 'hh',
					'skin'          => array( 'skin-2', 'skin-3', 'skin-4' ),
					'meridiem_type' => 'icon',
				),
			)
		);

		$this->add_responsive_control(
			'time_skew',
			array(
				'label'      => __( 'Skew (deg)', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'deg' ),
				'default'    => array(
					'size' => '-10',
					'unit' => 'deg',
				),
				'range'      => array(
					'deg' => array(
						'min' => -50,
						'max' => 50,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-world-clock__hand, {{WRAPPER}} .premium-world-clock__separator' => 'transform: skew({{SIZE}}deg);',
				),
				'condition'  => array(
					'skin' => array( 'skin-2', 'skin-3', 'skin-4' ),
				),
			)
		);

		$this->start_controls_tabs( 'clock_units_tabs' );

		$this->start_controls_tab(
			'hours_tab',
			array(
				'label' => __( 'Hours', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'hours_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}.premium-world-clock__hand-0 .premium-world-clock__hours::before,
                    {{WRAPPER}}.premium-world-clock__hand-1 .premium-world-clock__hours::before,
                    {{WRAPPER}}.premium-world-clock__hand-2 .premium-world-clock__hours::before,
                    {{WRAPPER}}.premium-world-clock__hand-3 .premium-world-clock__hours::before,
					{{WRAPPER}}.premium-world-clock__hand-4 .premium-world-clock__hours::before,
					{{WRAPPER}}.premium-world-clock__hand-5 .premium-world-clock__hours::before' => 'background-color: {{VALUE}};',
					'{{WRAPPER}}:not(.premium-world-clock__skin-1):not(.premium-world-clock__skin-5):not(.premium-world-clock__skin-6):not(.premium-world-clock__skin-7) .premium-world-clock__hours' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'      => 'hours_bg',
				'types'     => array( 'classic', 'gradient' ),
				'selector'  => '{{WRAPPER}} .premium-world-clock__hours',
				'condition' => array(
					'skin' => array( 'skin-2', 'skin-3', 'skin-4' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'hours_border',
				'selector'  => '{{WRAPPER}} .premium-world-clock__hours',
				'condition' => array(
					'skin' => array( 'skin-2', 'skin-3', 'skin-4' ),
				),
			)
		);

		$this->add_control(
			'hours_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-world-clock__hours' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'skin' => array( 'skin-2', 'skin-3', 'skin-4' ),
				),
			)
		);

		$this->add_responsive_control(
			'hours_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-world-clock__hours' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'skin' => array( 'skin-2', 'skin-3', 'skin-4' ),
				),
			)
		);

		$this->add_responsive_control(
			'hours_thickness',
			array(
				'label'      => __( 'Thickness', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 10,
					),
				),
				'condition'  => array(
					'skin!' => array( 'skin-2', 'skin-3', 'skin-4' ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-world-clock__hours::before'  => 'width: {{SIZE}}px;',
				),
			)
		);

		$this->add_responsive_control(
			'hours_len',
			array(
				'label'      => __( 'Length', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 500,
					),
				),
				'condition'  => array(
					'skin!' => array( 'skin-2', 'skin-3', 'skin-4' ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-world-clock__hours'  => 'width: {{SIZE}}px; height: {{SIZE}}px;',
				),
			)
		);

		$this->add_responsive_control(
			'hours_pos',
			array(
				'label'      => __( 'Position', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%' ),
				'condition'  => array(
					'skin!' => array( 'skin-2', 'skin-3', 'skin-4' ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-world-clock__hours::before'  => 'height: calc( 50% + {{SIZE}}% );',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'min_tab',
			array(
				'label' => __( 'Minutes', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'min_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}.premium-world-clock__hand-0 .premium-world-clock__minutes::before,
                    {{WRAPPER}}.premium-world-clock__hand-1 .premium-world-clock__minutes::before,
                    {{WRAPPER}}.premium-world-clock__hand-2 .premium-world-clock__minutes::before,
                    {{WRAPPER}}.premium-world-clock__hand-3 .premium-world-clock__minutes::before,
					{{WRAPPER}}.premium-world-clock__hand-4 .premium-world-clock__minutes::before,
					{{WRAPPER}}.premium-world-clock__hand-5 .premium-world-clock__minutes::before' => 'background-color: {{VALUE}};',
					'{{WRAPPER}}:not(.premium-world-clock__skin-1):not(.premium-world-clock__skin-5):not(.premium-world-clock__skin-6):not(.premium-world-clock__skin-7) .premium-world-clock__minutes' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'      => 'min_bg',
				'types'     => array( 'classic', 'gradient' ),
				'selector'  => '{{WRAPPER}} .premium-world-clock__minutes',
				'condition' => array(
					'skin' => array( 'skin-2', 'skin-3', 'skin-4' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'min_border',
				'selector'  => '{{WRAPPER}} .premium-world-clock__minutes',
				'condition' => array(
					'skin' => array( 'skin-2', 'skin-3', 'skin-4' ),
				),
			)
		);

		$this->add_control(
			'min_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-world-clock__minutes' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'skin' => array( 'skin-2', 'skin-3', 'skin-4' ),
				),
			)
		);

		$this->add_responsive_control(
			'min_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-world-clock__minutes' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'skin' => array( 'skin-2', 'skin-3', 'skin-4' ),
				),
			)
		);

		$this->add_responsive_control(
			'min_thickness',
			array(
				'label'      => __( 'Thickness', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 10,
					),
				),
				'condition'  => array(
					'skin!' => array( 'skin-2', 'skin-3', 'skin-4' ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-world-clock__minutes::before'  => 'width: {{SIZE}}px;',
				),
			)
		);

		$this->add_responsive_control(
			'min_len',
			array(
				'label'      => __( 'Length', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 500,
					),
				),
				'condition'  => array(
					'skin!' => array( 'skin-2', 'skin-3', 'skin-4' ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-world-clock__minutes'  => 'width: {{SIZE}}px; height: {{SIZE}}px;',
				),
			)
		);

		$this->add_responsive_control(
			'min_pos',
			array(
				'label'      => __( 'Position', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%' ),
				'condition'  => array(
					'skin!' => array( 'skin-2', 'skin-3', 'skin-4' ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-world-clock__minutes::before'  => 'height: calc( 50% + {{SIZE}}% );',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'sec_tab',
			array(
				'label' => __( 'Seconds', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'sec_typo',
				'selector'  => '{{WRAPPER}} .premium-world-clock__seconds',
				'condition' => array(
					'skin' => 'skin-3',
				),
			)
		);

		$this->add_control(
			'sec_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-world-clock__seconds::before' => 'background-color: {{VALUE}};',
					'{{WRAPPER}}:not(.premium-world-clock__skin-1):not(.premium-world-clock__skin-5):not(.premium-world-clock__skin-6):not(.premium-world-clock__skin-7) .premium-world-clock__seconds,
                    {{WRAPPER}}.premium-world-clock__skin-3 .premium-world-clock__sec-wrapper *' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'      => 'sec_bg',
				'types'     => array( 'classic', 'gradient' ),
				'selector'  => '{{WRAPPER}}:not(.premium-world-clock__skin-3) .premium-world-clock__seconds,
                 {{WRAPPER}}.premium-world-clock__skin-3 .premium-world-clock__sec-wrapper',
				'condition' => array(
					'skin' => array( 'skin-2', 'skin-3', 'skin-4' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'sec_border',
				'selector'  => '{{WRAPPER}}:not(.premium-world-clock__skin-3) .premium-world-clock__seconds,
                {{WRAPPER}}.premium-world-clock__skin-3 .premium-world-clock__sec-wrapper',
				'condition' => array(
					'skin' => array( 'skin-2', 'skin-3', 'skin-4' ),
				),
			)
		);

		$this->add_control(
			'sec_border_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}}:not(.premium-world-clock__skin-3) .premium-world-clock__seconds,
                    {{WRAPPER}}.premium-world-clock__skin-3 .premium-world-clock__sec-wrapper' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'skin' => array( 'skin-2', 'skin-3', 'skin-4' ),
				),
			)
		);

		$this->add_responsive_control(
			'sec_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}}:not(.premium-world-clock__skin-3) .premium-world-clock__seconds,
                    {{WRAPPER}}.premium-world-clock__skin-3 .premium-world-clock__sec-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'skin' => array( 'skin-2', 'skin-3', 'skin-4' ),
				),
			)
		);

		$this->add_responsive_control(
			'sec_thickness',
			array(
				'label'      => __( 'Thickness', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 10,
					),
				),
				'condition'  => array(
					'skin!' => array( 'skin-2', 'skin-3', 'skin-4' ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-world-clock__seconds::before'  => 'width: {{SIZE}}px;',
				),
			)
		);

		$this->add_responsive_control(
			'sec_len',
			array(
				'label'      => __( 'Length', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 500,
					),
				),
				'condition'  => array(
					'skin!' => array( 'skin-2', 'skin-3', 'skin-4' ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-world-clock__seconds'  => 'width: {{SIZE}}px; height: {{SIZE}}px;',
				),
			)
		);

		$this->add_responsive_control(
			'sec_pos',
			array(
				'label'      => __( 'Position', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%' ),
				'condition'  => array(
					'skin!' => array( 'skin-2', 'skin-3', 'skin-4' ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-world-clock__seconds::before'  => 'height: calc( 50% + {{SIZE}}% );',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'mer_tab',
			array(
				'label'      => __( 'Meridiem', 'premium-addons-for-elementor' ),
				'conditions' => $mer_conditions,
			)
		);

		$this->add_control(
			'mer_color',
			array(
				'label'      => __( 'Color', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::COLOR,
				'selectors'  => array(
					'{{WRAPPER}} .premium-world-clock__meridiem' => 'color: {{VALUE}};',
					'{{WRAPPER}} .premium-world-clock__meridiem *' => 'fill: {{VALUE}};',
				),
				'conditions' => $mer_conditions,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'       => 'mer_bg',
				'types'      => array( 'classic', 'gradient' ),
				'selector'   => '{{WRAPPER}} .premium-world-clock__meridiem',
				'conditions' => $mer_conditions,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'       => 'mer_border',
				'selector'   => '{{WRAPPER}} .premium-world-clock__meridiem',
				'conditions' => $mer_conditions,
			)
		);

		$this->add_control(
			'mer_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-world-clock__meridiem' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'conditions' => $mer_conditions,
			)
		);

		$this->add_responsive_control(
			'mer_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-world-clock__meridiem' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'conditions' => $mer_conditions,
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'faces_tab',
			array(
				'label'     => __( 'Faces', 'premium-addons-for-elementor' ),
				'condition' => array(
					'show_indicators' => 'yes',
					'skin'            => array( 'skin-1', 'skin-5', 'skin-6', 'skin-7' ),
				),
			)
		);

		$this->add_control(
			'clock_face_color',
			array(
				'label'     => __( 'Clock Face Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-world-clock__face' => 'background-color: {{VALUE}};',
					'{{WRAPPER}}:not(.premium-world-clock__skin-1) .premium-world-clock__circle > svg, {{WRAPPER}}:not(.premium-world-clock__skin-1) .premium-world-clock__circle > svg *' => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'show_indicators' => 'yes',
					'skin'            => array( 'skin-1', 'skin-5', 'skin-6', 'skin-7' ),
				),
			)
		);

		$this->add_responsive_control(
			'clock_face_thickness',
			array(
				'label'      => __( 'Clock Faces Thickness', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 10,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-world-clock__face'  => 'height: {{SIZE}}px;',
				),
				'condition'  => array(
					'show_indicators' => 'yes',
					'skin'            => array( 'skin-1' ),
				),
			)
		);

		$this->add_responsive_control(
			'clock_face_len',
			array(
				'label'      => __( 'Clock Faces Length', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-world-clock__face'  => 'width: {{SIZE}}px;',
				),
				'condition'  => array(
					'show_indicators' => 'yes',
					'skin'            => array( 'skin-1' ),
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'separator_color',
			array(
				'label'     => __( 'Separator Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .premium-world-clock__separator' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'skin' => array( 'skin-2', 'skin-3', 'skin-4' ),
				),
			)
		);

		$this->add_control(
			'clock_rounder',
			array(
				'label'     => esc_html__( 'Rounder', 'premium-addons-for-elementor' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
				'condition' => array(
					'skin!' => array( 'skin-2', 'skin-3', 'skin-4' ),
				),
			)
		);

		$this->add_responsive_control(
			'rounder_size',
			array(
				'label'      => __( 'Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 10,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-world-clock__rounder'  => 'width: {{SIZE}}px; height: {{SIZE}}px;',
				),
				'condition'  => array(
					'skin!' => array( 'skin-2', 'skin-3', 'skin-4' ),
				),
			)
		);

		$this->add_control(
			'rounder_bg',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-world-clock__rounder' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'skin!' => array( 'skin-2', 'skin-3', 'skin-4' ),
				),
			)
		);

		$this->add_control(
			'rounder_border_color',
			array(
				'label'     => __( 'Border Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-world-clock__rounder' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'skin!' => array( 'skin-2', 'skin-3', 'skin-4' ),
				),
			)
		);

		$this->end_controls_section();
	}

	private function add_days_style_controls() {

		$this->start_controls_section(
			'days_style_section',
			array(
				'label'     => __( 'Days', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'date' => 'yes',
					'skin' => 'skin-3',
				),
			)
		);

		$this->start_controls_tabs( 'days_tabs' );

		$this->start_controls_tab(
			'normal_tab',
			array(
				'label' => __( 'Normal', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'day_typo',
				'selector' => '{{WRAPPER}} .premium-world-clock__day-name',
			)
		);

		$this->add_control(
			'days_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-world-clock__day-name' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'days_bg',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-world-clock__day-name',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'days_border',
				'selector' => '{{WRAPPER}} .premium-world-clock__day-name',
			)
		);

		$this->add_control(
			'days_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-world-clock__day-name' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'days_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-world-clock__day-name' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'active_tab',
			array(
				'label' => __( 'Active', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'day_typo_act',
				'selector' => '{{WRAPPER}} .premium-world-clock__day-name.current-day',
			)
		);

		$this->add_control(
			'days_color_act',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-world-clock__day-name.current-day' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'days_bg_act',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-world-clock__day-name.current-day',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'days_border_act',
				'selector' => '{{WRAPPER}} .premium-world-clock__day-name.current-day',
			)
		);

		$this->add_control(
			'days_rad_act',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-world-clock__day-name.current-day' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'days_padding_act',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-world-clock__day-name.current-day' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'days_container',
			array(
				'label'     => esc_html__( 'Container', 'premium-addons-for-elementor' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_responsive_control(
			'days_spacing',
			array(
				'label'      => __( 'Spacing', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-world-clock__days-wrapper' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'days_cont_bg',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-world-clock__days-wrapper',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'days_cont_shadow',
				'selector' => '{{WRAPPER}} .premium-world-clock__days-wrapper',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'days_cont_border',
				'selector' => '{{WRAPPER}} .premium-world-clock__days-wrapper',
			)
		);

		$this->add_control(
			'days_cont_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-world-clock__days-wrapper' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'days_cont_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-world-clock__days-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'days_cont_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-world-clock__days-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function add_clock_num_style_controls() {

		$this->start_controls_section(
			'clock_num_style_section',
			array(
				'label'     => __( 'Clock Numbers', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_time'      => 'yes',
					'show_clock_num' => 'yes',
					'skin'           => array( 'skin-1', 'skin-5', 'skin-6' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'clock_num_typo',
				'selector' => '{{WRAPPER}} .premium-world-clock__clock-number',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'clock_num_shadow',
				'selector' => '{{WRAPPER}} .premium-world-clock__clock-number',
			)
		);

		$this->add_control(
			'clock_num_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-world-clock__clock-number' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'clock_num_bg',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-world-clock__clock-number',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'clock_num_border',
				'selector' => '{{WRAPPER}} .premium-world-clock__clock-number',
			)
		);

		$this->add_control(
			'clock_num_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-world-clock__clock-number' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'clock_num_size',
			array(
				'label'      => __( 'Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-world-clock__clock-number' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}
	/**
	 * Render title widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		$papro_activated = apply_filters( 'papro_activated', false );

		if ( ! $papro_activated && in_array( $settings['skin'], array( 'skin-4', 'skin-6', 'skin-7' ), true ) ) {
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

		$time_zone = 'custom' === $settings['tz_type'] ? $settings['custom_tz'] : $settings['tz_type'];

		$clock_title = $settings['clock_title'];

		$skin = $settings['skin'];

		$show_timezone = 'yes' === $settings['show_timezone_offset'] ? true : false;

		$show_time = 'yes' === $settings['show_time'] ? true : false;

		$analog_skins = array( 'skin-1', 'skin-5', 'skin-6', 'skin-7' );

		$show_seconds = ! in_array( $skin, $analog_skins, true ) && 'yes' === $settings['show_seconds'] ? true : false;

		$show_meridiem = 'yes' === $settings['show_meridiem'] ? true : false;

		$show_clock_numbers = 'yes' === $settings['show_clock_num'] ? true : false;

		$show_indicators = 'yes' === $settings['show_indicators'] && 'skin-1' === $skin && ! $show_clock_numbers ? true : false;

		$show_date = 'yes' === $settings['date'] ? true : false;

		$clock_setting = array(
			'timezone'     => $time_zone,
			'format'       => in_array( $skin, $analog_skins, true ) ? 'hh' : $settings['time_format'],
			'skin'         => $skin,
			'showSeconds'  => $show_seconds,
			'showMeridiem' => $show_meridiem,
			'meridiemType' => $settings['meridiem_type'],
			'date'         => $show_date,
			'gmtOffset'    => $show_timezone,
			'showClockNum' => $show_clock_numbers,
			'equalWidth'   => 'yes' === $settings['equal_width'] && in_array( $skin, array( 'skin-2', 'skin-3', 'skin-4' ), true ) ? true : false,
		);

		if ( $show_date ) {

			if ( 'skin-4' === $skin ) {

				$digital_date_format         = 'custom' === $settings['date_format_digital'] ? explode( '|', $settings['custom_date_format'] ) : explode( '|', $settings['date_format_digital'] );
				$clock_setting['dateFormat'] = $digital_date_format;

			} elseif ( 'skin-3' === $skin ) {

				$clock_setting['daysNum'] = $settings['days_num'];
			} else {
				$clock_setting['dateFormat'] = $settings['date_format'];
			}
		}

		if ( $show_timezone ) {
			$clock_setting['offsetFormat'] = $settings['offset_format'];
		}

		$this->add_render_attribute(
			'pa-clock-wrapper',
			array(
				'class'         => array( 'premium-world-clock__clock-wrapper', 'premium-addons__v-hidden' ),
				'data-settings' => wp_json_encode( $clock_setting ),
			)
		);

		?>
			<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'pa-clock-wrapper' ) ); ?>>
				<?php if ( in_array( $skin, array( 'skin-1', 'skin-5', 'skin-6', 'skin-7' ), true ) ) { ?>
					<?php if ( $show_time ) : ?>
						<div class="premium-world-clock__time-wrapper">
							<div class="premium-world-clock__circle">
								<?php
								if ( 'skin-1' !== $skin ) {
									$this->get_clock_face( $skin );
								}
								?>

								<?php if ( $show_indicators ) { ?>
									<span class="premium-world-clock__twelve premium-world-clock__face"></span>
									<span class="premium-world-clock__three premium-world-clock__face"></span>
									<span class="premium-world-clock__six premium-world-clock__face"></span>
									<span class="premium-world-clock__nine premium-world-clock__face"></span>
								<?php } ?>

								<?php if ( $show_clock_numbers ) { ?>

									<div class="premium-world-clock__clock-numbers">
										<span class="premium-world-clock__clock-number">3</span>
										<span class="premium-world-clock__clock-number">4</span>
										<span class="premium-world-clock__clock-number">5</span>
										<span class="premium-world-clock__clock-number">6</span>
										<span class="premium-world-clock__clock-number">7</span>
										<span class="premium-world-clock__clock-number">8</span>
										<span class="premium-world-clock__clock-number">9</span>
										<span class="premium-world-clock__clock-number">10</span>
										<span class="premium-world-clock__clock-number">11</span>
										<span class="premium-world-clock__clock-number">12</span>
										<span class="premium-world-clock__clock-number">1</span>
										<span class="premium-world-clock__clock-number">2</span>
									</div>
								<?php } ?>

								<div class="premium-world-clock__rounder"></div>
								<div class="premium-world-clock__hours premium-world-clock__hand"></div>
								<div class="premium-world-clock__minutes premium-world-clock__hand"></div>
								<div class="premium-world-clock__seconds premium-world-clock__hand"></div>
								<?php
								if ( $show_meridiem ) {
									?>
										 <span class="premium-world-clock__meridiem">AM</span>
										 <?php
								}
								?>
							</div>
						</div>
					<?php endif; ?>

				<?php } elseif ( 'skin-2' === $skin ) { ?>

					<?php if ( $show_time ) : ?>

						<div class="premium-world-clock__time-wrapper">
							<span class="premium-world-clock__hours premium-world-clock__hand"></span>
							<span class="premium-world-clock__separator">:</span>
							<span class="premium-world-clock__minutes premium-world-clock__hand"></span>

							<?php if ( $show_seconds ) : ?>
								<span class="premium-world-clock__separator">:</span>
								<span class="premium-world-clock__seconds premium-world-clock__hand"></span>
							<?php endif; ?>

							<span class="premium-world-clock__meridiem"></span>
						</div>

					<?php endif; ?>

				<?php } elseif ( 'skin-3' === $skin ) { ?>
					<?php if ( $show_date ) { ?>
						<div class='premium-world-clock__days-wrapper'></div>
					<?php } ?>
					<?php if ( $show_time ) : ?>
					<div class='premium-world-clock__time-wrapper'>
						<span class='premium-world-clock__hours premium-world-clock__hand'></span>
						<span class='premium-world-clock__separator'>:</span>
						<span class='premium-world-clock__minutes premium-world-clock__hand'></span>
						<span class='premium-world-clock__meridiem'></span>
					</div>
					<?php endif; ?>
					<div class='premium-world-clock__date-wrapper'>
						<?php if ( $show_seconds ) : ?>
							<div class="premium-world-clock__sec-wrapper">
								<span class='premium-world-clock__seconds premium-world-clock__hand'></span>
								<span class='premium-world-clock__symbol premium-world-clock__second-symbol'>S</span>
							</div>
						<?php endif; ?>

						<?php if ( $show_date ) { ?>
							<div class="premium-world-clock__month-wrapper">
								<span class='premium-world-clock__date-segment premium-world-clock__month'></span>
								<span class='premium-world-clock__symbol'>M</span>
							</div>
							<div class="premium-world-clock__day-wrapper">
								<span class='premium-world-clock__date-segment premium-world-clock__day'></span>
								<span class='premium-world-clock__symbol'>D</span>
							</div>
						<?php } ?>
					</div>
				<?php } elseif ( 'skin-4' === $skin ) { ?>
					<?php if ( $show_time ) : ?>
						<div class="premium-world-clock__time-wrapper">
							<span class="premium-world-clock__hours premium-world-clock__hand"></span>
							<span class="premium-world-clock__separator">:</span>
							<span class="premium-world-clock__minutes premium-world-clock__hand"></span>
							<?php if ( $show_seconds ) : ?>
								<span class="premium-world-clock__separator">:</span>
								<span class="premium-world-clock__seconds premium-world-clock__hand"></span>
							<?php endif; ?>
							<span class="premium-world-clock__meridiem"></span>
						</div>
					<?php endif; ?>

					<?php if ( $show_date ) { ?>
						<div class="premium-world-clock__date-wrapper"></div>
					<?php } ?>

				<?php } ?>

				<?php if ( ! empty( $clock_title ) || $show_date || $show_timezone ) : ?>
					<div class='premium-world-clock__additonal-info'>

						<?php if ( ! empty( $clock_title ) ) : ?>
							<span class='premium-world-clock__clock-title'>
								<?php echo esc_html( $clock_title ); ?>
							</span>
						<?php endif; ?>

						<?php if ( $show_date && ! in_array( $skin, array( 'skin-3', 'skin-4' ), true ) ) : ?>
							<span class='premium-world-clock__date'></span>
						<?php endif; ?>

						<?php if ( $show_timezone ) : ?>
							<span class='premium-world-clock__gmt-offset'></span>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		<?php

	}

	/**
	 * Get Clock Face.
	 *
	 * @access private
	 * @since 2.8.23
	 *
	 * @param string $skin clock skin.
	 */
	private function get_clock_face( $skin ) {

		$clock_faces = array(
			'skin-5' => '<svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" width="125.62" height="125.62" viewBox="0 0 125.62 125.62"><defs><style>.premium-clock-svg{fill:#333;}</style></defs><g id="Face_1" data-name="Face 1"><path class="premium-clock-svg" d="M87.64,14.11l-1,4.58.61.14,1-4.59C88.05,14.19,87.84,14.14,87.64,14.11Zm-6.43-1-.5,4.67.62.07.49-4.68Zm12.73,2.64-1.45,4.46.59.2L94.53,16ZM100,18.07l-1.91,4.28.57.26,1.91-4.29Z" transform="translate(-12.19 -12.19)"/><path class="premium-clock-svg" d="M75,12.19A62.81,62.81,0,1,0,137.81,75,62.87,62.87,0,0,0,75,12.19Zm61.88,69-4.66-.49c0,.2-.05.41-.07.62l4.67.49c-.22,2-.52,3.9-.92,5.81l-4.58-1c0,.2-.1.4-.14.61l4.59,1q-.63,2.89-1.53,5.69l-4.45-1.45c-.06.2-.13.39-.2.59l4.46,1.45q-.93,2.82-2.11,5.5l-4.28-1.91c-.08.19-.17.38-.26.57l4.29,1.9h0q-1.09,2.4-2.38,4.69l-6.21-3.59c-.31.55-.62,1.09-.94,1.62l6.21,3.59q-1.35,2.27-2.9,4.39l-3.77-2.75c-.12.17-.24.35-.37.51l3.78,2.74a58.25,58.25,0,0,1-3.71,4.57l-3.46-3.12c-.14.16-.27.32-.41.47l3.45,3.12a58,58,0,0,1-4.15,4.15l-3.12-3.45-.47.41,3.12,3.46q-2.19,2-4.56,3.71l-2.75-3.77-.51.36,2.75,3.78q-2.13,1.55-4.39,2.89l-3.59-6.2c-.53.32-1.08.63-1.62.93l3.58,6.22q-2.3,1.29-4.69,2.37l-1.91-4.28-.56.26,1.9,4.28q-2.69,1.19-5.5,2.11l-1.44-4.46-.59.2,1.44,4.45q-2.79.9-5.68,1.53l-1-4.59-.6.14,1,4.58c-1.91.4-3.85.7-5.82.92l-.49-4.67-.62.07.49,4.66q-2.59.27-5.25.3V130L75,130l-.93,0v7.2c-1.77,0-3.52-.12-5.25-.29l.49-4.67-.62-.07-.49,4.67a58.77,58.77,0,0,1-5.82-.92l1-4.58-.61-.14-1,4.59c-1.93-.42-3.82-.92-5.68-1.52l1.44-4.46-.59-.2-1.45,4.46A58.65,58.65,0,0,1,50,131.94l1.91-4.28c-.2-.08-.38-.17-.57-.26l-1.91,4.29q-2.4-1.09-4.69-2.38l3.58-6.21c-.54-.31-1.09-.62-1.62-.94l-3.58,6.21c-1.51-.9-3-1.87-4.4-2.89l2.75-3.78-.51-.37-2.74,3.78a58.25,58.25,0,0,1-4.57-3.71l3.12-3.46-.47-.41L33.19,121A58.59,58.59,0,0,1,29,116.82l3.46-3.11c-.14-.16-.27-.32-.41-.47l-3.46,3.12a58.25,58.25,0,0,1-3.71-4.57l3.77-2.74-.36-.51-3.78,2.74q-1.55-2.13-2.9-4.39l6.21-3.58c-.32-.53-.63-1.08-.93-1.62l-6.22,3.58q-1.29-2.3-2.37-4.69l4.28-1.91c-.08-.19-.17-.38-.26-.57L18.06,100q-1.18-2.69-2.11-5.5l4.46-1.44c-.06-.2-.13-.4-.2-.6l-4.45,1.45q-.9-2.79-1.53-5.69l4.59-1c0-.2-.09-.41-.14-.61l-4.58,1c-.4-1.91-.7-3.85-.92-5.81l4.67-.49c0-.21-.05-.42-.07-.62l-4.66.49c-.18-1.73-.28-3.49-.3-5.26H20c0-.31,0-.62,0-.93s0-.63,0-.94h-7.2q0-2.66.3-5.25l4.66.49c0-.21,0-.42.07-.62l-4.67-.49a58.79,58.79,0,0,1,.93-5.82l4.57,1,.15-.6-4.6-1q.63-2.9,1.53-5.69l4.45,1.45.21-.59L16,55.48q.93-2.82,2.12-5.5l4.28,1.91c.08-.19.17-.38.26-.57l-4.29-1.91q1.09-2.4,2.38-4.69l6.21,3.59c.31-.55.61-1.09.94-1.62L21.64,43.1q1.35-2.26,2.89-4.39l3.78,2.75.37-.51L24.9,38.21a58.25,58.25,0,0,1,3.71-4.57l3.46,3.12.42-.47L29,33.17A56.3,56.3,0,0,1,33.19,29l3.11,3.47.47-.42L33.65,28.6a58.25,58.25,0,0,1,4.57-3.71L41,28.67l.51-.37-2.74-3.78c1.41-1,2.88-2,4.38-2.89l3.59,6.21q.79-.48,1.62-.93l-3.59-6.22q2.3-1.29,4.69-2.38l1.91,4.29.57-.26L50,18.06A58.65,58.65,0,0,1,55.49,16l1.45,4.46.59-.2-1.45-4.45q2.79-.9,5.68-1.53l1,4.59.61-.14-1-4.58c1.91-.4,3.85-.7,5.82-.92l.49,4.67.62-.07-.49-4.66c1.73-.18,3.49-.28,5.26-.3V20L75,20l.94,0v-7.2c1.78,0,3.53.12,5.27.3l.61.06q3,.33,5.82.93c.2,0,.41.08.61.13a57.38,57.38,0,0,1,5.69,1.52l.59.19q2.81.93,5.49,2.12l.57.25q2.4,1.09,4.69,2.38l-3.59,6.21c.55.31,1.09.61,1.63.94l3.58-6.21c1.51.9,3,1.86,4.39,2.9l-2.75,3.77.51.36,2.74-3.77a58.25,58.25,0,0,1,4.57,3.71l-3.12,3.46.47.41L116.82,29A58.31,58.31,0,0,1,121,33.18l-3.46,3.12.42.47,3.46-3.12c1.31,1.46,2.54,3,3.71,4.56L121.33,41c.13.16.24.34.36.51l3.78-2.75q1.55,2.13,2.9,4.39l-6.21,3.58c.32.54.63,1.08.93,1.63l6.22-3.59q1.29,2.3,2.38,4.69h0l-4.29,1.9.26.57,4.28-1.9q1.19,2.67,2.11,5.48l-4.46,1.46c.06.19.14.39.2.59l4.45-1.45q.9,2.79,1.53,5.69l-4.59,1c0,.2.1.4.14.61l4.58-1c.4,1.91.7,3.85.92,5.81l-4.67.49c0,.2,0,.41.07.62l4.66-.5c.18,1.73.28,3.49.3,5.26H130c0,.31,0,.63,0,.94s0,.62,0,.93h7.2C137.16,77.7,137.06,79.46,136.88,81.19Z" transform="translate(-12.19 -12.19)"/></g></svg>',
			'skin-6' => '<svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" width="125.62" height="125.62" viewBox="0 0 125.62 125.62"><defs><style>.premium-clock-svg{fill:#333;}</style></defs><g id="Face_2" data-name="Face 2"><path class="premium-clock-svg" d="M75,12.19A62.81,62.81,0,1,0,137.81,75,62.89,62.89,0,0,0,75,12.19Zm62.18,61.87H132.8c0-1.62-.12-3.21-.28-4.79l4.36-.47C137.06,70.53,137.16,72.29,137.18,74.06Zm-.36-5.88-4.36.47q-.3-2.73-.85-5.37l4.28-.91C136.3,64.28,136.6,66.22,136.82,68.18Zm-1.06-6.42-4.28.91a53.82,53.82,0,0,0-1.4-5.24l4.16-1.35A57.19,57.19,0,0,1,135.76,61.76Zm-1.71-6.28-4.16,1.36c-.57-1.73-1.23-3.42-2-5.07l4-1.78Q133.13,52.68,134.05,55.48Zm-2.37-6.07-4,1.78c-.66-1.46-1.39-2.89-2.17-4.28l3.79-2.19Q130.59,47,131.68,49.41Zm-3.32-6.31-3.79,2.19q-1.23-2.07-2.65-4l3.54-2.57C126.5,40.13,127.46,41.59,128.36,43.1Zm-3.26-4.9-3.54,2.58q-1.61-2.19-3.42-4.22l3.25-2.92Q123.36,35.83,125.1,38.2Zm-4.13-5-3.24,2.92c-1.23-1.34-2.5-2.61-3.84-3.83L116.82,29A58,58,0,0,1,121,33.17Zm-4.62-4.57-2.93,3.24q-2-1.8-4.21-3.41l2.57-3.54A58.25,58.25,0,0,1,116.35,28.6Zm-5.07-4.08-2.58,3.54c-1.29-.94-2.63-1.82-4-2.64l2.18-3.79C108.39,22.53,109.86,23.5,111.28,24.52Zm-10.7-6.21c1.59.73,3.16,1.52,4.68,2.38l-2.19,3.79Q101,23.3,98.79,22.31l1.78-4Zm-.57-.25-1.79,4c-1.65-.73-3.33-1.38-5.06-1.95L94.51,16Q97.33,16.88,100,18.06Zm-6.09-2.31-1.36,4.17a53.82,53.82,0,0,0-5.24-1.4l.91-4.29Q91.13,14.86,93.92,15.75Zm-7.21,2.63c-1.76-.36-3.55-.65-5.37-.84l.46-4.36a58.77,58.77,0,0,1,5.82.92ZM75.92,12.82c1.78,0,3.53.12,5.27.3l-.47,4.35q-2.37-.24-4.8-.27Zm-1.88,0V17.2c-1.62,0-3.21.12-4.79.28l-.46-4.36C70.52,13,72.27,12.84,74,12.82Zm-5.87.37.46,4.36a52.91,52.91,0,0,0-5.36.84l-.91-4.28Q65.22,13.51,68.17,13.19Zm-6.42,1.05.91,4.28a53.82,53.82,0,0,0-5.24,1.4l-1.36-4.16A57.38,57.38,0,0,1,61.75,14.24ZM55.47,16l1.36,4.17q-2.59.86-5.07,1.95l-1.78-4Q52.67,16.89,55.47,16Zm-6.06,2.37,1.78,4c-1.46.66-2.89,1.39-4.28,2.17L44.72,20.7Q47,19.41,49.41,18.32ZM43.1,21.64l2.19,3.79q-2.07,1.23-4,2.64l-2.57-3.54Q40.84,23,43.1,21.64ZM38.2,24.9l2.57,3.54Q38.58,30,36.56,31.85l-2.92-3.24Q35.83,26.65,38.2,24.9Zm-5,4.12,2.92,3.25q-2,1.83-3.84,3.84L29,33.19A58.59,58.59,0,0,1,33.18,29ZM28.6,33.65l3.24,2.92q-1.78,2-3.41,4.21l-3.54-2.56A58.25,58.25,0,0,1,28.6,33.65Zm-4.07,5.07,3.53,2.57c-.93,1.29-1.82,2.63-2.64,4l-3.79-2.19Q23,40.85,24.53,38.72Zm-3.84,6,3.8,2.19Q23.31,49,22.32,51.2l-4-1.78Q19.4,47,20.69,44.73ZM18.06,50l4,1.78c-.72,1.65-1.38,3.34-1.95,5.07L16,55.49Q16.88,52.67,18.06,50Zm-2.3,6.09,4.16,1.35a53.82,53.82,0,0,0-1.4,5.24l-4.29-.91Q14.86,58.86,15.76,56.08ZM14.1,62.37l4.29.91q-.55,2.64-.85,5.36l-4.36-.46C13.4,66.22,13.7,64.28,14.1,62.37Zm-1,6.43,4.36.46c-.16,1.58-.26,3.18-.28,4.8H12.82C12.84,72.29,12.94,70.53,13.12,68.8Zm-.3,7.13H17.2c0,1.62.12,3.21.27,4.79l-4.36.46C12.94,79.45,12.84,77.7,12.82,75.93Zm.36,5.88,4.36-.47c.19,1.82.48,3.61.84,5.37l-4.28.91C13.7,85.71,13.4,83.77,13.18,81.81Zm1.05,6.42,4.29-.91a53.82,53.82,0,0,0,1.4,5.24l-4.17,1.35C15.15,92.05,14.65,90.16,14.23,88.23ZM16,94.51l4.16-1.36q.86,2.59,1.95,5.07l-4,1.78A58.65,58.65,0,0,1,16,94.51Zm2.36,6.06,4-1.78q1,2.19,2.17,4.28l-3.79,2.2Q19.4,103,18.31,100.57Zm3.32,6.31,3.79-2.18c.82,1.38,1.71,2.72,2.64,4l-3.54,2.56C23.49,109.85,22.52,108.39,21.63,106.88Zm3.26,4.9,3.54-2.57q1.61,2.19,3.41,4.22l-3.24,2.92A58.25,58.25,0,0,1,24.89,111.78Zm4.12,5,3.25-2.92c1.22,1.34,2.49,2.61,3.83,3.84L33.17,121A56.3,56.3,0,0,1,29,116.81Zm4.63,4.58,2.92-3.25q2,1.81,4.21,3.42l-2.56,3.54A58.25,58.25,0,0,1,33.64,121.39Zm5.06,4.07,2.58-3.53q1.94,1.41,4,2.64l-2.19,3.79Q40.84,127,38.7,125.46Zm6,3.84,2.19-3.79c1.39.78,2.82,1.51,4.28,2.17l-1.78,4Q47,130.58,44.72,129.3ZM50,131.93l1.78-4c1.65.73,3.33,1.38,5.06,1.95l-1.35,4.17Q52.67,133.12,50,131.93Zm6.09,2.31,1.35-4.16a54,54,0,0,0,5.25,1.4l-.92,4.28A57.19,57.19,0,0,1,56.07,134.24Zm12.11,2.58q-3-.33-5.82-.93l.92-4.28q2.64.55,5.36.85Zm5.86.36c-1.77,0-3.51-.12-5.24-.3l.46-4.36c1.57.16,3.17.26,4.78.28ZM75,127.5l-.94,0v4.71A56.77,56.77,0,0,1,47.22,125l2.33-4c-.54-.3-1.09-.61-1.62-.93l-2.33,4A57.7,57.7,0,0,1,26,104.39l4-2.33c-.32-.54-.63-1.08-.94-1.63l-4,2.33a56.69,56.69,0,0,1-7.2-26.83H22.5c0-.31,0-.62,0-.93s0-.63,0-.94H17.82A56.8,56.8,0,0,1,25,47.23l4,2.33c.3-.55.61-1.09.93-1.63l-4-2.32A57.7,57.7,0,0,1,45.6,26l2.33,4q.8-.48,1.62-.93l-2.33-4A56.77,56.77,0,0,1,74,17.82v4.7l.94,0,.94,0v-4.7A56.73,56.73,0,0,1,102.76,25l-2.34,4.06c.55.3,1.09.61,1.63.93L104.39,26A57.58,57.58,0,0,1,124,45.6L120,47.94c.33.54.64,1.08.94,1.63L125,47.22a56.71,56.71,0,0,1,7.21,26.84h-4.72c0,.31,0,.62,0,.94s0,.62,0,.93h4.72A56.63,56.63,0,0,1,125,102.78l-4.06-2.35c-.3.54-.61,1.09-.93,1.62L124,104.4a57.67,57.67,0,0,1-19.66,19.65L102,120c-.53.32-1.08.63-1.62.93l2.34,4.06a56.73,56.73,0,0,1-26.84,7.2v-4.71Zm.94,9.68V132.8c1.62,0,3.21-.11,4.79-.27l.46,4.36C79.44,137.06,77.69,137.16,75.92,137.18Zm5.88-.36-.47-4.36c1.82-.19,3.61-.48,5.37-.84l.91,4.28Q84.75,136.5,81.8,136.82Zm6.42-1-.91-4.28c1.78-.39,3.54-.86,5.25-1.41l1.35,4.17A57.38,57.38,0,0,1,88.22,135.77Zm6.28-1.71-1.35-4.17q2.59-.85,5.07-1.95l1.78,4Q97.32,133.13,94.5,134.06Zm6.07-2.37-1.78-4q2.19-1,4.28-2.17l2.19,3.79Q103,130.6,100.57,131.69Zm6.31-3.32-2.19-3.79q2.07-1.23,4-2.64l2.57,3.54C109.85,126.51,108.39,127.48,106.88,128.37Zm4.9-3.26-2.57-3.54q2.19-1.6,4.21-3.41l2.93,3.24A58.25,58.25,0,0,1,111.78,125.11Zm5-4.12-2.92-3.25c1.34-1.22,2.61-2.49,3.84-3.83l3.24,2.92A56.3,56.3,0,0,1,116.81,121Zm4.58-4.63-3.24-2.93q1.8-2,3.41-4.21l3.54,2.57A58.25,58.25,0,0,1,121.39,116.36Zm4.08-5.07-3.54-2.58c.94-1.29,1.82-2.63,2.64-4l3.79,2.18C127.47,108.4,126.5,109.87,125.47,111.29Zm3.83-6-3.79-2.19c.79-1.4,1.51-2.83,2.18-4.29l4,1.77C131,102.17,130.17,103.75,129.3,105.28Zm2.64-5.28-4-1.78c.73-1.65,1.38-3.33,1.95-5.06l4.16,1.36Q133.12,97.33,131.94,100Zm2.3-6.08-4.16-1.35a54,54,0,0,0,1.4-5.25l4.29.92Q135.14,91.14,134.24,93.92Zm1.66-6.3-4.28-.91q.54-2.64.84-5.36l4.36.46C136.6,83.77,136.3,85.71,135.9,87.62Zm1-6.43-4.36-.46c.16-1.58.26-3.18.28-4.8h4.38C137.16,77.7,137.06,79.46,136.88,81.19Z" transform="translate(-12.19 -12.19)"/></g></svg>',
			'skin-7' => '<svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" width="125.62" height="125.62" viewBox="0 0 125.62 125.62"><defs><style>.premium-clock-svg{fill:#333;}</style></defs><g id="Face_3" data-name="Face 3"><path class="premium-clock-svg" d="M75,12.19A62.81,62.81,0,1,0,137.81,75,62.89,62.89,0,0,0,75,12.19Zm62.18,61.87H132.8c0-1.62-.12-3.21-.28-4.79l4.36-.47C137.06,70.53,137.16,72.29,137.18,74.06Zm-.36-5.88-4.36.47q-.3-2.73-.85-5.37l4.28-.91C136.3,64.28,136.6,66.22,136.82,68.18Zm-1.06-6.42-4.28.91a53.82,53.82,0,0,0-1.4-5.24l4.16-1.35A57.19,57.19,0,0,1,135.76,61.76Zm-1.71-6.28-4.16,1.36c-.57-1.73-1.23-3.42-2-5.07l4-1.78Q133.13,52.68,134.05,55.48Zm-2.37-6.07-4,1.78c-.66-1.46-1.39-2.89-2.17-4.28l3.79-2.19Q130.59,47,131.68,49.41Zm-3.32-6.31-3.79,2.19q-1.23-2.07-2.65-4l3.54-2.57C126.5,40.13,127.46,41.59,128.36,43.1Zm-3.26-4.9-3.54,2.58q-1.61-2.19-3.42-4.22l3.25-2.92Q123.36,35.83,125.1,38.2Zm-4.13-5-3.24,2.92c-1.23-1.34-2.5-2.61-3.84-3.83L116.82,29A58,58,0,0,1,121,33.17Zm-4.62-4.57-2.93,3.24q-2-1.8-4.21-3.41l2.57-3.54A58.25,58.25,0,0,1,116.35,28.6Zm-5.07-4.08-2.58,3.54c-1.29-.94-2.63-1.82-4-2.64l2.18-3.79C108.39,22.53,109.86,23.5,111.28,24.52Zm-10.7-6.21c1.59.73,3.16,1.52,4.68,2.38l-2.19,3.79Q101,23.3,98.79,22.31l1.78-4Zm-.57-.25-1.79,4c-1.65-.73-3.33-1.38-5.06-1.95L94.51,16Q97.33,16.88,100,18.06Zm-6.09-2.31-1.36,4.17a53.82,53.82,0,0,0-5.24-1.4l.91-4.29Q91.13,14.86,93.92,15.75Zm-7.21,2.63c-1.76-.36-3.55-.65-5.37-.84l.46-4.36a58.77,58.77,0,0,1,5.82.92ZM75.92,12.82c1.78,0,3.53.12,5.27.3l-.47,4.35q-2.37-.24-4.8-.27Zm-1.88,0V17.2c-1.62,0-3.21.12-4.79.28l-.46-4.36C70.52,13,72.27,12.84,74,12.82Zm-5.87.37.46,4.36a52.91,52.91,0,0,0-5.36.84l-.91-4.28Q65.22,13.51,68.17,13.19Zm-6.42,1.05.91,4.28a53.82,53.82,0,0,0-5.24,1.4l-1.36-4.16A57.38,57.38,0,0,1,61.75,14.24ZM55.47,16l1.36,4.17q-2.59.86-5.07,1.95l-1.78-4Q52.67,16.89,55.47,16Zm-6.06,2.37,1.78,4c-1.46.66-2.89,1.39-4.28,2.17L44.72,20.7Q47,19.41,49.41,18.32ZM43.1,21.64l2.19,3.79q-2.07,1.23-4,2.64l-2.57-3.54Q40.84,23,43.1,21.64ZM38.2,24.9l2.57,3.54Q38.58,30,36.56,31.85l-2.92-3.24Q35.83,26.65,38.2,24.9Zm-5,4.12,2.92,3.25q-2,1.83-3.84,3.84L29,33.19A58.59,58.59,0,0,1,33.18,29ZM28.6,33.65l3.24,2.92q-1.78,2-3.41,4.21l-3.54-2.56A58.25,58.25,0,0,1,28.6,33.65Zm-4.07,5.07,3.53,2.57c-.93,1.29-1.82,2.63-2.64,4l-3.79-2.19Q23,40.85,24.53,38.72Zm-3.84,6,3.8,2.19Q23.31,49,22.32,51.2l-4-1.78Q19.4,47,20.69,44.73ZM18.06,50l4,1.78c-.72,1.65-1.38,3.34-1.95,5.07L16,55.49Q16.88,52.67,18.06,50Zm-2.3,6.09,4.16,1.35a53.82,53.82,0,0,0-1.4,5.24l-4.29-.91Q14.86,58.86,15.76,56.08ZM14.1,62.37l4.29.91q-.55,2.64-.85,5.36l-4.36-.46C13.4,66.22,13.7,64.28,14.1,62.37Zm-1,6.43,4.36.46c-.16,1.58-.26,3.18-.28,4.8H12.82C12.84,72.29,12.94,70.53,13.12,68.8Zm-.3,7.13H17.2c0,1.62.12,3.21.27,4.79l-4.36.46C12.94,79.45,12.84,77.7,12.82,75.93Zm.36,5.88,4.36-.47c.19,1.82.48,3.61.84,5.37l-4.28.91C13.7,85.71,13.4,83.77,13.18,81.81Zm1.05,6.42,4.29-.91a53.82,53.82,0,0,0,1.4,5.24l-4.17,1.35C15.15,92.05,14.65,90.16,14.23,88.23ZM16,94.51l4.16-1.36q.86,2.59,1.95,5.07l-4,1.78A58.65,58.65,0,0,1,16,94.51Zm2.36,6.06,4-1.78q1,2.19,2.17,4.28l-3.79,2.2Q19.4,103,18.31,100.57Zm3.32,6.31,3.79-2.18c.82,1.38,1.71,2.72,2.64,4l-3.54,2.56C23.49,109.85,22.52,108.39,21.63,106.88Zm3.26,4.9,3.54-2.57q1.61,2.19,3.41,4.22l-3.24,2.92A58.25,58.25,0,0,1,24.89,111.78Zm4.12,5,3.25-2.92c1.22,1.34,2.49,2.61,3.83,3.84L33.17,121A56.3,56.3,0,0,1,29,116.81Zm4.63,4.58,2.92-3.25q2,1.81,4.21,3.42l-2.56,3.54A58.25,58.25,0,0,1,33.64,121.39Zm5.06,4.07,2.58-3.53q1.94,1.41,4,2.64l-2.19,3.79Q40.84,127,38.7,125.46Zm6,3.84,2.19-3.79c1.39.78,2.82,1.51,4.28,2.17l-1.78,4Q47,130.58,44.72,129.3ZM50,131.93l1.78-4c1.65.73,3.33,1.38,5.06,1.95l-1.35,4.17Q52.67,133.12,50,131.93Zm6.09,2.31,1.35-4.16a54,54,0,0,0,5.25,1.4l-.92,4.28A57.19,57.19,0,0,1,56.07,134.24Zm12.11,2.58q-3-.33-5.82-.93l.92-4.28q2.64.55,5.36.85Zm5.86.36c-1.77,0-3.51-.12-5.24-.3l.46-4.36c1.57.16,3.17.26,4.78.28ZM75,127.5l-.94,0v4.71A56.77,56.77,0,0,1,47.22,125l2.33-4c-.54-.3-1.09-.61-1.62-.93l-2.33,4A57.7,57.7,0,0,1,26,104.39l4-2.33c-.32-.54-.63-1.08-.94-1.63l-4,2.33a56.69,56.69,0,0,1-7.2-26.83H22.5c0-.31,0-.62,0-.93s0-.63,0-.94H17.82A56.8,56.8,0,0,1,25,47.23l4,2.33c.3-.55.61-1.09.93-1.63l-4-2.32A57.7,57.7,0,0,1,45.6,26l2.33,4q.8-.48,1.62-.93l-2.33-4A56.77,56.77,0,0,1,74,17.82v4.7l.94,0,.94,0v-4.7A56.73,56.73,0,0,1,102.76,25l-2.34,4.06c.55.3,1.09.61,1.63.93L104.39,26A57.58,57.58,0,0,1,124,45.6L120,47.94c.33.54.64,1.08.94,1.63L125,47.22a56.71,56.71,0,0,1,7.21,26.84h-4.72c0,.31,0,.62,0,.94s0,.62,0,.93h4.72A56.63,56.63,0,0,1,125,102.78l-4.06-2.35c-.3.54-.61,1.09-.93,1.62L124,104.4a57.67,57.67,0,0,1-19.66,19.65L102,120c-.53.32-1.08.63-1.62.93l2.34,4.06a56.73,56.73,0,0,1-26.84,7.2v-4.71Zm.94,9.68V132.8c1.62,0,3.21-.11,4.79-.27l.46,4.36C79.44,137.06,77.69,137.16,75.92,137.18Zm5.88-.36-.47-4.36c1.82-.19,3.61-.48,5.37-.84l.91,4.28Q84.75,136.5,81.8,136.82Zm6.42-1-.91-4.28c1.78-.39,3.54-.86,5.25-1.41l1.35,4.17A57.38,57.38,0,0,1,88.22,135.77Zm6.28-1.71-1.35-4.17q2.59-.85,5.07-1.95l1.78,4Q97.32,133.13,94.5,134.06Zm6.07-2.37-1.78-4q2.19-1,4.28-2.17l2.19,3.79Q103,130.6,100.57,131.69Zm6.31-3.32-2.19-3.79q2.07-1.23,4-2.64l2.57,3.54C109.85,126.51,108.39,127.48,106.88,128.37Zm4.9-3.26-2.57-3.54q2.19-1.6,4.21-3.41l2.93,3.24A58.25,58.25,0,0,1,111.78,125.11Zm5-4.12-2.92-3.25c1.34-1.22,2.61-2.49,3.84-3.83l3.24,2.92A56.3,56.3,0,0,1,116.81,121Zm4.58-4.63-3.24-2.93q1.8-2,3.41-4.21l3.54,2.57A58.25,58.25,0,0,1,121.39,116.36Zm4.08-5.07-3.54-2.58c.94-1.29,1.82-2.63,2.64-4l3.79,2.18C127.47,108.4,126.5,109.87,125.47,111.29Zm3.83-6-3.79-2.19c.79-1.4,1.51-2.83,2.18-4.29l4,1.77C131,102.17,130.17,103.75,129.3,105.28Zm2.64-5.28-4-1.78c.73-1.65,1.38-3.33,1.95-5.06l4.16,1.36Q133.12,97.33,131.94,100Zm2.3-6.08-4.16-1.35a54,54,0,0,0,1.4-5.25l4.29.92Q135.14,91.14,134.24,93.92Zm1.66-6.3-4.28-.91q.54-2.64.84-5.36l4.36.46C136.6,83.77,136.3,85.71,135.9,87.62Zm1-6.43-4.36-.46c.16-1.58.26-3.18.28-4.8h4.38C137.16,77.7,137.06,79.46,136.88,81.19Z" transform="translate(-12.19 -12.19)"/><path class="premium-clock-svg" d="M75.55,111.15A36.45,36.45,0,1,1,112,74.71,36.49,36.49,0,0,1,75.55,111.15Zm0-72.29A35.85,35.85,0,1,0,111.4,74.71,35.89,35.89,0,0,0,75.55,38.86Z" transform="translate(-12.19 -12.19)"/><path class="premium-clock-svg" d="M109.55,50.35a2.77,2.77,0,0,0,.89.85l5.67-3.27a3.27,3.27,0,0,0-.33-1.25l.45-.19L118,49.6l-.45.18a2.74,2.74,0,0,0-.88-.85L111,52.2a3.46,3.46,0,0,0,.33,1.25l-.45.19-1.79-3.11ZM112,54.57a2.85,2.85,0,0,0,.88.85l5.67-3.28a3.27,3.27,0,0,0-.33-1.25l.45-.19,1.8,3.11L120,54a2.85,2.85,0,0,0-.88-.85l-5.67,3.27a3.37,3.37,0,0,0,.33,1.26l-.45.19-1.8-3.11ZM95,37.82a2.75,2.75,0,0,0,1.19.29l3.27-5.67a3.46,3.46,0,0,0-.91-.92l.29-.39,3.11,1.8-.3.38A2.78,2.78,0,0,0,100.5,33l-3.27,5.67a3.19,3.19,0,0,0,.91.92l-.3.39-3.11-1.8Zm22.25,30.29a2.65,2.65,0,0,0,.34,1.17h6.54a3.1,3.1,0,0,0,.34-1.24l.49.06v3.59l-.49-.08a2.65,2.65,0,0,0-.34-1.17h-6.54a3.1,3.1,0,0,0-.34,1.25l-.49-.06V68Zm0,4.86a2.69,2.69,0,0,0,.34,1.18h6.54a3.19,3.19,0,0,0,.34-1.25L125,73v3.59l-.49-.07a2.69,2.69,0,0,0-.34-1.18h-6.54a3.19,3.19,0,0,0-.34,1.25l-.49-.06V72.9Zm0,4.87a2.65,2.65,0,0,0,.34,1.17h6.54a3.1,3.1,0,0,0,.34-1.25l.49.07v3.59l-.49-.08a2.65,2.65,0,0,0-.34-1.17h-6.54a3.1,3.1,0,0,0-.34,1.25l-.49-.07V77.76Zm-2.67,12.68a2.75,2.75,0,0,0-.29,1.19L120,95a3.46,3.46,0,0,0,.92-.91l.39.29-1.8,3.11-.38-.3a2.78,2.78,0,0,0,.29-1.19l-5.67-3.28a3.5,3.5,0,0,0-.92.92l-.39-.3,1.8-3.11ZM118,98.44a2.68,2.68,0,0,0,.78-.68l.39.29-1.73,3-.38-.31a2.75,2.75,0,0,0,.29-1.19l-6.06-1.07,4,4.71a3.5,3.5,0,0,0,.9-.9l.39.3L115,105.34l-.38-.3a2.49,2.49,0,0,0,.24-1.11l-5-6.09.39-.67Zm-15.39,16.71a2.54,2.54,0,0,0,1-.21l.19.45-3,1.73-.18-.45a3,3,0,0,0,.85-.89l-4.71-4,1.08,6.07a3.31,3.31,0,0,0,1.22-.33l.19.45-2.75,1.59-.18-.45a2.49,2.49,0,0,0,.77-.84l-1.29-7.77.68-.39Zm-21.4,8.19a2.63,2.63,0,0,0,1,.33l-.06.48H78.69l.07-.48a2.83,2.83,0,0,0,1.18-.34l-2.1-5.79-2.1,5.8a3.4,3.4,0,0,0,1.23.33l-.07.48H73.73l.07-.48a2.45,2.45,0,0,0,1.08-.34L77.66,116h.77Zm-8.17-6.9a2.78,2.78,0,0,0-1.18.34v6.55a3.27,3.27,0,0,0,1.25.34l-.06.48H69.47l.07-.48a2.83,2.83,0,0,0,1.18-.34v-6.55a3.37,3.37,0,0,0-1.25-.34l.06-.48h3.59ZM58.2,120.82a2.56,2.56,0,0,0,.69.77l-.3.39-3-1.72.3-.39a3,3,0,0,0,1.19.3l1.08-6.06-4.72,4a3.31,3.31,0,0,0,.9.89l-.3.39-2.75-1.58.31-.39a2.46,2.46,0,0,0,1.1.25l6.09-5,.68.39Zm-3.63-10.07a2.78,2.78,0,0,0-1.19-.29l-3.28,5.67a3.36,3.36,0,0,0,.92.92l-.3.39-3.11-1.79.31-.39a2.91,2.91,0,0,0,1.19.3l3.27-5.68a3.28,3.28,0,0,0-.91-.91l.29-.39,3.11,1.79Zm-4.22-2.43a2.74,2.74,0,0,0-1.18-.29l-3.28,5.67a3.46,3.46,0,0,0,.91.92l-.29.39-3.11-1.8.3-.38a2.82,2.82,0,0,0,1.19.29l3.28-5.67a3.46,3.46,0,0,0-.91-.92l.29-.39,3.11,1.8Zm-11.53-.23a2.85,2.85,0,0,0,.21,1l-.45.19-1.73-3,.46-.18a3,3,0,0,0,.88.85l4-4.71-6.07,1.08a3.75,3.75,0,0,0,.33,1.22l-.45.19L34.37,102l.46-.18a2.55,2.55,0,0,0,.83.77l7.78-1.28.39.67Zm1.89-10.53a2.64,2.64,0,0,0-.88-.85L34.16,100a3.27,3.27,0,0,0,.33,1.25l-.45.19-1.8-3.11.46-.18a2.74,2.74,0,0,0,.88.85l5.67-3.27a3.27,3.27,0,0,0-.33-1.25l.45-.19,1.8,3.11Zm-2.43-4.22a2.85,2.85,0,0,0-.88-.85l-5.67,3.28A3.27,3.27,0,0,0,32.06,97l-.45.19-1.8-3.11.46-.18a2.85,2.85,0,0,0,.88.85l5.67-3.28a3.37,3.37,0,0,0-.33-1.25l.45-.19,1.8,3.11Zm-2.43-4.21a3,3,0,0,0-.88-.85l-5.68,3.27a3.57,3.57,0,0,0,.33,1.26l-.45.19-1.79-3.11.46-.18a2.85,2.85,0,0,0,.88.85l5.67-3.28A3.27,3.27,0,0,0,34.06,86l.45-.19L36.3,89Zm-2-8.4a2.87,2.87,0,0,0-.34-1.18H26.93a3.57,3.57,0,0,0-.34,1.25l-.48-.06V77.15l.48.08a2.78,2.78,0,0,0,.34,1.17h6.55a3.27,3.27,0,0,0,.34-1.25l.49.06V80.8Zm0-4.23a1.7,1.7,0,0,0-.65-1.42l-3-2.22L27,75.16c-.28.21-.38.46-.44,1.17l-.48-.06V72.62l.48.07a1.71,1.71,0,0,0,.35,1l2.33-1.67-2.34-1.76a2.28,2.28,0,0,0-.34,1l-.48-.06v-3l.48.07a1.33,1.33,0,0,0,.49,1.13l2.79,2.1,3.32-2.4A1.71,1.71,0,0,0,33.82,68l.49.06v3.69l-.49-.07c0-.57-.08-.91-.36-1.13l-2.63,1.9,2.66,2c.23-.15.33-.53.33-1.22l.49.06v3.3Zm3.61-19.07a1.65,1.65,0,0,0,.14-1.55l-1.44-3.4-3.91.4c-.35,0-.56.21-1,.79l-.4-.29,1.83-3.16.38.3a1.76,1.76,0,0,0-.21,1.07l2.86-.29-1.15-2.69a2.28,2.28,0,0,0-.81.72L33.37,49l1.47-2.55.39.3A1.36,1.36,0,0,0,35.08,48l1.37,3.21,4.08-.41a1.76,1.76,0,0,0,1.16-.76l.39.29-1.84,3.2-.38-.31c.28-.49.38-.83.24-1.16l-3.23.34,1.31,3.07c.28,0,.55-.29.89-.89l.39.29-1.65,2.86ZM49.07,42a1.66,1.66,0,0,0,.9-1.28l.45-3.66-3.59-1.61c-.32-.14-.59-.1-1.23.2l-.19-.45,3.16-1.82.18.45a1.72,1.72,0,0,0-.71.82l2.61,1.18.35-2.9a2.2,2.2,0,0,0-1.06.22l-.19-.45,2.55-1.47.18.45c-.49.34-.69.6-.73,1l-.42,3.46,3.74,1.68a1.69,1.69,0,0,0,1.38-.08l.2.46L53.45,40l-.18-.46c.5-.28.75-.53.8-.88l-3-1.32-.41,3.31c.25.13.63,0,1.23-.32l.19.45-2.86,1.65ZM57,37.41a2.64,2.64,0,0,0,.85-.88l-3.27-5.67a3.27,3.27,0,0,0-1.25.33l-.19-.45,3.1-1.8.18.46a2.71,2.71,0,0,0-.84.88L58.86,36a3.27,3.27,0,0,0,1.25-.33l.19.46-3.11,1.79ZM66.5,33a1.69,1.69,0,0,0,1.42-.66l2.22-2.95-2.31-3.19c-.2-.28-.46-.37-1.16-.43l.06-.49h3.65l-.07.49a1.64,1.64,0,0,0-1,.35L71,28.43l1.76-2.34a2.1,2.1,0,0,0-1-.34l.06-.49h3l-.08.49a1.36,1.36,0,0,0-1.13.48L71.39,29l2.4,3.32A1.73,1.73,0,0,0,75,33l-.06.48H71.28l.07-.48c.57,0,.91-.09,1.13-.37L70.57,30l-2,2.67c.16.23.53.33,1.23.33l-.07.48H66.43Zm9.17,0a3,3,0,0,0,1.18-.34V26.09a3.19,3.19,0,0,0-1.25-.34l.06-.49h3.59l-.08.49a2.65,2.65,0,0,0-1.17.34v6.55a3.46,3.46,0,0,0,1.25.34l-.06.48H75.6Zm4.86,0a2.94,2.94,0,0,0,1.18-.34V26.09a3.1,3.1,0,0,0-1.25-.34l.06-.49h3.59l-.07.49a2.73,2.73,0,0,0-1.18.34v6.55a3.57,3.57,0,0,0,1.25.34l-.06.48H80.46Z" transform="translate(-12.19 -12.19)"/></g></svg>',
		);

		echo $clock_faces[ $skin ]; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

}
