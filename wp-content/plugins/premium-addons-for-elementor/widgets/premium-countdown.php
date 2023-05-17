<?php
/**
 * Premium Countdown.
 */

namespace PremiumAddons\Widgets;

// Elementor Classes.
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;

// PHP Classes.
use \Datetime;
use \DateTimeZone;

// PremiumAddons Classes.
use PremiumAddons\Includes\Helper_Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Premium_Countdown
 */
class Premium_Countdown extends Widget_Base {

	/**
	 * Retrieve Widget Name.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_name() {
		return 'premium-countdown-timer';
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return __( 'Countdown', 'premium-addons-for-elementor' );
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
		return 'pa-countdown';
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
		return array(
			'pa-countdown',
			'pa-tweenmax',
			'premium-addons',
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
		return array( 'pa', 'premium', 'counter', 'time', 'event' );
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
	 * Register Countdown controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		$this->start_controls_section(
			'premium_countdown_global_settings',
			array(
				'label' => __( 'Countdown', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_countdown_style',
			array(
				'label'   => __( 'Style', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'd-u-s' => __( 'Inline', 'premium-addons-for-elementor' ),
					'd-u-u' => __( 'Block', 'premium-addons-for-elementor' ),
				),
				'default' => 'd-u-u',
			)
		);

		$this->add_control(
			'premium_countdown_type',
			array(
				'label'   => __( 'Type', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'fixed'     => __( 'Fixed Timer', 'premium-addons-for-elementor' ),
					'evergreen' => __( 'Evergreen Timer', 'premium-addons-for-elementor' ),
				),
				'default' => 'fixed',
			)
		);

		$this->add_control(
			'premium_countdown_date_time',
			array(
				'label'          => __( 'Due Date', 'premium-addons-for-elementor' ),
				'description'    => __( 'Date format is (yyyy/mm/dd). Time format is (hh:mm:ss). Example: 2020-01-01 09:30.', 'premium-addons-for-elementor' ),
				'type'           => Controls_Manager::DATE_TIME,
				'picker_options' => array(
					'format' => 'Ym/d H:m:s',
				),
				'default'        => gmdate( 'Y/m/d H:m:s', strtotime( '+ 1 Day' ) ),
				'dynamic'        => array(
					'active' => true,
				),
				'condition'      => array(
					'premium_countdown_type' => 'fixed',
				),
			)
		);

		$this->add_control(
			'premium_countdown_eve_days',
			array(
				'label'       => __( 'Days', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => '0',
				'dynamic'     => array( 'active' => true ),
				'render_type' => 'template',
				'default'     => 2,
				'condition'   => array(
					'premium_countdown_type' => 'evergreen',
				),
			)
		);

		$this->add_control(
			'premium_countdown_eve_hours',
			array(
				'label'       => __( 'Hours', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => '0',
				'max'         => '23',
				'dynamic'     => array( 'active' => true ),
				'render_type' => 'template',
				'default'     => 3,
				'condition'   => array(
					'premium_countdown_type' => 'evergreen',
				),
			)
		);

		$this->add_control(
			'premium_countdown_eve_min',
			array(
				'label'       => __( 'Minutes', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => '0',
				'max'         => '59',
				'dynamic'     => array( 'active' => true ),
				'render_type' => 'template',
				'default'     => 0,
				'condition'   => array(
					'premium_countdown_type' => 'evergreen',
				),
			)
		);

		$this->add_control(
			'premium_countdown_eve_reset',
			array(
				'label'     => __( 'Reset', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'premium_countdown_type' => 'evergreen',
				),
			)
		);

		$this->add_control(
			'premium_countdown_eve_reset_hours',
			array(
				'label'       => __( 'Hours', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => '0',
				'dynamic'     => array( 'active' => true ),
				'render_type' => 'template',
				'default'     => 24,
				'condition'   => array(
					'premium_countdown_type'      => 'evergreen',
					'premium_countdown_eve_reset' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_countdown_eve_reset_min',
			array(
				'label'       => __( 'Minutes', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => '0',
				'max'         => '59',
				'dynamic'     => array( 'active' => true ),
				'render_type' => 'template',
				'default'     => 0,
				'condition'   => array(
					'premium_countdown_type'      => 'evergreen',
					'premium_countdown_eve_reset' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_countdown_s_u_time',
			array(
				'label'       => __( 'Time Zone', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'wp-time'   => __( 'WordPress Default', 'premium-addons-for-elementor' ),
					'user-time' => __( 'User Local Time', 'premium-addons-for-elementor' ),
				),
				'default'     => 'wp-time',
				'description' => __( 'This will set the current time of the option that you will choose.', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_countdown_units',
			array(
				'label'       => __( 'Time Units', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT2,
				'description' => __( 'Select the time units that you want to display in countdown timer.', 'premium-addons-for-elementor' ),
				'options'     => array(
					'Y' => __( 'Years', 'premium-addons-for-elementor' ),
					'O' => __( 'Month', 'premium-addons-for-elementor' ),
					'W' => __( 'Week', 'premium-addons-for-elementor' ),
					'D' => __( 'Day', 'premium-addons-for-elementor' ),
					'H' => __( 'Hours', 'premium-addons-for-elementor' ),
					'M' => __( 'Minutes', 'premium-addons-for-elementor' ),
					'S' => __( 'Second', 'premium-addons-for-elementor' ),
				),
				'default'     => array( 'O', 'D', 'H', 'M', 'S' ),
				'multiple'    => true,
				'separator'   => 'after',
			)
		);

		$this->add_control(
			'premium_countdown_flip',
			array(
				'label'       => __( 'Flipping Effect', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'render_type' => 'template',
			)
		);

		$this->add_control(
			'premium_countdown_separator',
			array(
				'label'       => __( 'Digits Separator', 'premium-addons-for-elementor' ),
				'description' => __( 'Enable or disable digits separator', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'condition'   => array(
					'premium_countdown_style' => 'd-u-u',
				),
			)
		);

		$this->add_control(
			'premium_countdown_separator_text',
			array(
				'label'     => __( 'Separator Text', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'condition' => array(
					'premium_countdown_style'     => 'd-u-u',
					'premium_countdown_separator' => 'yes',
				),
				'default'   => ':',
			)
		);

		$this->add_responsive_control(
			'premium_countdown_align',
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
				'toggle'    => false,
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}} .premium-countdown' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_countdown_on_expire_settings',
			array(
				'label' => __( 'Expire', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_countdown_expire_text_url',
			array(
				'label'       => __( 'Expire Type', 'premium-addons-for-elementor' ),
				'label_block' => false,
				'type'        => Controls_Manager::SELECT,
				'description' => __( 'Choose whether if you want to set a message or a redirect link or leave it as digits', 'premium-addons-for-elementor' ),
				'options'     => array(
					'default' => __( 'Default', 'premium-addons-for-elementor' ),
					'text'    => __( 'Message', 'premium-addons-for-elementor' ),
					'url'     => __( 'Redirection Link', 'premium-addons-for-elementor' ),
				),
				'default'     => 'text',
			)
		);

		$this->add_control(
			'default_type_notice',
			array(
				'raw'             => __( 'Default option will show the expiration message as <b>Digits [00:00:00]</b>.', 'premium-addons-for-elementor' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => array(
					'premium_countdown_expire_text_url' => 'default',
				),
			)
		);

		$this->add_control(
			'premium_countdown_expiry_text_',
			array(
				'label'     => __( 'On expiry Text', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::WYSIWYG,
				'dynamic'   => array( 'active' => true ),
				'default'   => __( 'Countdown Expired!', 'prmeium_elementor' ),
				'condition' => array(
					'premium_countdown_expire_text_url' => 'text',
				),
			)
		);

		$this->add_control(
			'premium_countdown_expiry_redirection_',
			array(
				'label'     => __( 'Redirect To', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array(
					'active'     => true,
					'categories' => array(
						TagsModule::POST_META_CATEGORY,
						TagsModule::URL_CATEGORY,
					),
				),
				'condition' => array(
					'premium_countdown_expire_text_url' => 'url',
				),
				'default'   => get_permalink( 1 ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_countdown_transaltion',
			array(
				'label' => __( 'Strings Translation', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_countdown_day_singular',
			array(
				'label'   => __( 'Day (Singular)', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
				'default' => 'Day',
			)
		);

		$this->add_control(
			'premium_countdown_day_plural',
			array(
				'label'   => __( 'Day (Plural)', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
				'default' => 'Days',
			)
		);

		$this->add_control(
			'premium_countdown_week_singular',
			array(
				'label'   => __( 'Week (Singular)', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
				'default' => 'Week',
			)
		);

		$this->add_control(
			'premium_countdown_week_plural',
			array(
				'label'   => __( 'Weeks (Plural)', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
				'default' => 'Weeks',
			)
		);

		$this->add_control(
			'premium_countdown_month_singular',
			array(
				'label'   => __( 'Month (Singular)', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
				'default' => 'Month',
			)
		);

		$this->add_control(
			'premium_countdown_month_plural',
			array(
				'label'   => __( 'Months (Plural)', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
				'default' => 'Months',
			)
		);

		$this->add_control(
			'premium_countdown_year_singular',
			array(
				'label'   => __( 'Year (Singular)', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
				'default' => 'Year',
			)
		);

		$this->add_control(
			'premium_countdown_year_plural',
			array(
				'label'   => __( 'Years (Plural)', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
				'default' => 'Years',
			)
		);

		$this->add_control(
			'premium_countdown_hour_singular',
			array(
				'label'   => __( 'Hour (Singular)', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
				'default' => 'Hour',
			)
		);

		$this->add_control(
			'premium_countdown_hour_plural',
			array(
				'label'   => __( 'Hours (Plural)', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
				'default' => 'Hours',
			)
		);

		$this->add_control(
			'premium_countdown_minute_singular',
			array(
				'label'   => __( 'Minute (Singular)', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
				'default' => 'Minute',
			)
		);

		$this->add_control(
			'premium_countdown_minute_plural',
			array(
				'label'   => __( 'Minutes (Plural)', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
				'default' => 'Minutes',
			)
		);

		$this->add_control(
			'premium_countdown_second_singular',
			array(
				'label'   => __( 'Second (Singular)', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
				'default' => 'Second',
			)
		);

		$this->add_control(
			'premium_countdown_second_plural',
			array(
				'label'   => __( 'Seconds (Plural)', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
				'default' => 'Seconds',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_pa_docs',
			array(
				'label' => __( 'Helpful Documentations', 'premium-addons-for-elementor' ),
			)
		);

		$doc1_url = Helper_Functions::get_campaign_link( 'https://premiumaddons.com/docs/countdown-widget-tutorial/', 'editor-page', 'wp-editor', 'get-support' );

		$this->add_control(
			'doc_1',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf( '<a href="%s" target="_blank">%s</a>', $doc1_url, __( 'Gettings started »', 'premium-addons-for-elementor' ) ),
				'content_classes' => 'editor-pa-doc',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_countdown_typhography',
			array(
				'label' => __( 'Digits', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'premium_countdown_digit_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .countdown .countdown-section .countdown-amount, {{WRAPPER}} .premium-countdown-flip .premium-countdown-figure span' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'premium_countdown_digit_typo',
				'global'    => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'selector'  => '{{WRAPPER}} .countdown .countdown-section .countdown-amount, {{WRAPPER}} .premium-countdown-flip .premium-countdown-figure span',
				'separator' => 'after',
			)
		);

		$this->add_control(
			'premium_countdown_timer_digit_bg_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .countdown .countdown-section .countdown-amount,
					 {{WRAPPER}} .premium-countdown-flip .premium-countdown-figure,
					 {{WRAPPER}} .premium-countdown-flip .premium-countdown-figure .top,
					 {{WRAPPER}} .premium-countdown-flip .premium-countdown-figure .top-back,
					 {{WRAPPER}} .premium-countdown-flip .premium-countdown-figure .bottom-back ' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'premium_countdown_units_shadow',
				'selector' => '{{WRAPPER}} .countdown .countdown-amount, {{WRAPPER}} .premium-countdown-figure',
			)
		);

		$this->add_responsive_control(
			'premium_countdown_digit_bg_size',
			array(
				'label'     => __( 'Background Size', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 1,
						'max' => 1000,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .countdown .countdown-section .countdown-amount' => 'padding: {{SIZE}}px;',
					'{{WRAPPER}} .premium-countdown-figure' => 'width: {{SIZE}}px;',
				),

			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_countdown_digits_border',
				'selector' => '{{WRAPPER}} .countdown .countdown-section .countdown-amount, {{WRAPPER}} .premium-countdown-figure',
			)
		);

		$this->add_control(
			'premium_countdown_digit_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .countdown .countdown-section .countdown-amount' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'premium_countdown_flip!' => 'yes',
					'digit_adv_radius!'       => 'yes',
				),
			)
		);

		$this->add_control(
			'digit_adv_radius',
			array(
				'label'       => __( 'Advanced Border Radius', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Apply custom radius values. Get the radius value from ', 'premium-addons-for-elementor' ) . '<a href="https://9elements.github.io/fancy-border-radius/" target="_blank">here</a>',
			)
		);

		$this->add_control(
			'digit_adv_radius_value',
			array(
				'label'     => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'selectors' => array(
					'{{WRAPPER}} .countdown .countdown-section .countdown-amount' => 'border-radius: {{VALUE}};',
				),
				'condition' => array(
					'digit_adv_radius' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_countdown_unit_style',
			array(
				'label' => __( 'Units', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'premium_countdown_unit_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .countdown .countdown-section .countdown-period, {{WRAPPER}} .premium-countdown-label' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'premium_countdown_unit_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'selector' => '{{WRAPPER}} .countdown .countdown-section .countdown-period, {{WRAPPER}} .premium-countdown-label',
			)
		);

		$this->add_control(
			'premium_countdown_unit_backcolor',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .countdown .countdown-section .countdown-period, {{WRAPPER}} .premium-countdown-label' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_countdown_separator_width',
			array(
				'label'      => __( 'Spacing in Between', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => array(
					'size' => 40,
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 200,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .countdown .countdown-section, {{WRAPPER}} .premium-countdown-block' => 'margin-right: calc( {{SIZE}}{{UNIT}} / 2 ); margin-left: calc( {{SIZE}}{{UNIT}} / 2 );',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'premium_countdown_style',
							'operator' => '===',
							'value'    => 'd-u-s',
						),
						array(
							'relation' => 'and',
							'terms'    => array(
								array(
									'name'     => 'premium_countdown_style',
									'operator' => '===',
									'value'    => 'd-u-u',
								),
								array(
									'name'     => 'premium_countdown_separator',
									'operator' => '!==',
									'value'    => 'yes',
								),
							),
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'units_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .countdown-section .countdown-period, {{WRAPPER}} .premium-countdown-label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
				'condition'  => array(
					'premium_countdown_style' => 'd-u-s',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_countdown_separator_style',
			array(
				'label'     => __( 'Separator', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_countdown_style'     => 'd-u-u',
					'premium_countdown_separator' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'premium_countdown_separator_size',
			array(
				'label'     => __( 'Size', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .countdown_separator' => 'font-size: {{SIZE}}px;',
				),
			)
		);

		$this->add_control(
			'premium_countdown_separator_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .countdown_separator' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_countdown_separator_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .countdown_separator' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_countdown_exp_message',
			array(
				'label'     => __( 'Expiration Message', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_countdown_expire_text_url' => 'text',
				),
			)
		);

		$this->add_control(
			'premium_countdown_message_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-countdown-exp-message' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'premium_countdown_message_bg_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-countdown-exp-message' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'premium_countdown_message_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'selector' => '{{WRAPPER}} .premium-countdown-exp-message',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_countdown_message_border',
				'selector' => '{{WRAPPER}} .premium-countdown-exp-message',
			)
		);

		$this->add_control(
			'premium_countdown_message_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-countdown-exp-message' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'premium_countdown_message_shadow',
				'selector' => '{{WRAPPER}} .premium-countdown-exp-message',
			)
		);

		$this->add_responsive_control(
			'premium_countdown_message_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-countdown-exp-message' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_countdown_message_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-countdown-exp-message' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Handles Evergreen Counter
	 *
	 * @param array $settings widget settings.
	 *
	 * @since 4.3.9
	 * @access protected
	 *
	 * @return object evergreen to-run-time.
	 */
	protected function get_evergreen_time( $settings ) {

		if ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$http_x_headers = explode( ',', sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) );

			$_SERVER['REMOTE_ADDR'] = $http_x_headers[0];
		}

		$ip_address = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';

		$ip_address = ( '::1' === $ip_address ) ? '127.0.0.1' : $ip_address;

		$id = $this->get_id();

		$eve_days  = empty( $settings['premium_countdown_eve_days'] ) ? 0 : $settings['premium_countdown_eve_days'] * 24 * 60 * 60;
		$eve_hours = empty( $settings['premium_countdown_eve_hours'] ) ? 0 : $settings['premium_countdown_eve_hours'] * 60 * 60;
		$eve_min   = empty( $settings['premium_countdown_eve_min'] ) ? 0 : $settings['premium_countdown_eve_min'] * 60;

		$eve_interval = $eve_days + $eve_hours + $eve_min;

		$counter_key = 'premium_countdown_evergreen_' . $id;

		$evergreen_user = 'premium_evergreen_user_' . $ip_address;

		add_option( $counter_key, array() );

		$local_data = get_option( $counter_key, 'Null' );

		$local_due_date = isset( $local_data[ $evergreen_user ]['due_date'] ) ? $local_data[ $evergreen_user ]['due_date'] : 'Null';

		$local_interval = isset( $local_data[ $evergreen_user ]['interval'] ) ? $local_data[ $evergreen_user ]['interval'] : 'Null';

		if ( 'Null' === $local_due_date && 'Null' === $local_interval ) {
			return $this->handle_evergreen_counter( $counter_key, $evergreen_user, $eve_interval );
		}

		if ( 'Null' !== $local_due_date && intval( $local_interval ) !== $eve_interval ) {
			return $this->handle_evergreen_counter( $counter_key, $evergreen_user, $eve_interval );
		}

		if ( strtotime( $local_due_date->format( 'Y-m-d H:i:s' ) ) > 0 && intval( $local_interval ) === $eve_interval ) {
			return $local_due_date;
		}

	}

	/**
	 * Set/update Evergreen user Local Data.
	 *
	 * @param string $counter_key  evergreen/widget key.
	 * @param string $evergreen_user  evergreen user Key.
	 * @param number $eve_interval evergreen interval.
	 *
	 * @since 4.3.9
	 * @access protected
	 *
	 * @return object $end_time
	 */
	protected function handle_evergreen_counter( $counter_key, $evergreen_user, $eve_interval ) {

		$end_time = new DateTime( 'GMT' );

		$end_time->setTime( $end_time->format( 'H' ) + 2, $end_time->format( 'i' ), $end_time->format( 's' ) + $eve_interval );

		$local_data = get_option( $counter_key, 'Null' );

		$local_data[ $evergreen_user ]['due_date'] = $end_time;
		$local_data[ $evergreen_user ]['interval'] = $eve_interval;

		update_option( $counter_key, $local_data );

		return $end_time;
	}


	/**
	 * Render Countdown widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		$id = $this->get_id();

		$timer_type = $settings['premium_countdown_type'];

		$reset = '';

		$is_edit_mode = \Elementor\Plugin::$instance->editor->is_edit_mode();

		if ( 'evergreen' === $timer_type && 'yes' === $settings['premium_countdown_eve_reset'] ) {

			$transient_name = 'premium_evergreen_reset_' . $id;

			if ( false === get_transient( $transient_name ) ) {

				delete_option( 'premium_countdown_evergreen_' . $id );

				$reset = true;

				$reset_hours = empty( $settings['premium_countdown_eve_reset_hours'] ) ? 0 : $settings['premium_countdown_eve_reset_hours'] * HOUR_IN_SECONDS;
				$reset_min   = empty( $settings['premium_countdown_eve_reset_min'] ) ? 0 : $settings['premium_countdown_eve_reset_min'] * MINUTE_IN_SECONDS;

				$expire_time = $reset_hours + $reset_min;

				if ( ! $is_edit_mode && 0 !== $expire_time ) {
					set_transient( $transient_name, 'DEFAULT', $expire_time );
				}
			}
		}

		$target_date = 'evergreen' === $timer_type ? $this->get_evergreen_time( $settings ) : str_replace( '-', '/', $settings['premium_countdown_date_time'] );

		$formats = $settings['premium_countdown_units'];
		$format  = implode( '', $formats );
		$time    = str_replace( '-', '/', current_time( 'mysql' ) );

		$sent_time = '';

		if ( 'wp-time' === $settings['premium_countdown_s_u_time'] ) {
			$sent_time = $time;
		}

		// Singular labels set up.
		$y     = ! empty( $settings['premium_countdown_year_singular'] ) ? $settings['premium_countdown_year_singular'] : 'Year';
		$m     = ! empty( $settings['premium_countdown_month_singular'] ) ? $settings['premium_countdown_month_singular'] : 'Month';
		$w     = ! empty( $settings['premium_countdown_week_singular'] ) ? $settings['premium_countdown_week_singular'] : 'Week';
		$d     = ! empty( $settings['premium_countdown_day_singular'] ) ? $settings['premium_countdown_day_singular'] : 'Day';
		$h     = ! empty( $settings['premium_countdown_hour_singular'] ) ? $settings['premium_countdown_hour_singular'] : 'Hour';
		$mi    = ! empty( $settings['premium_countdown_minute_singular'] ) ? $settings['premium_countdown_minute_singular'] : 'Minute';
		$s     = ! empty( $settings['premium_countdown_second_singular'] ) ? $settings['premium_countdown_second_singular'] : 'Second';
		$label = $y . ',' . $m . ',' . $w . ',' . $d . ',' . $h . ',' . $mi . ',' . $s;

		// Plural labels set up.
		$ys      = ! empty( $settings['premium_countdown_year_plural'] ) ? $settings['premium_countdown_year_plural'] : 'Years';
		$ms      = ! empty( $settings['premium_countdown_month_plural'] ) ? $settings['premium_countdown_month_plural'] : 'Months';
		$ws      = ! empty( $settings['premium_countdown_week_plural'] ) ? $settings['premium_countdown_week_plural'] : 'Weeks';
		$ds      = ! empty( $settings['premium_countdown_day_plural'] ) ? $settings['premium_countdown_day_plural'] : 'Days';
		$hs      = ! empty( $settings['premium_countdown_hour_plural'] ) ? $settings['premium_countdown_hour_plural'] : 'Hours';
		$mis     = ! empty( $settings['premium_countdown_minute_plural'] ) ? $settings['premium_countdown_minute_plural'] : 'Minutes';
		$ss      = ! empty( $settings['premium_countdown_second_plural'] ) ? $settings['premium_countdown_second_plural'] : 'Seconds';
		$labels1 = $ys . ',' . $ms . ',' . $ws . ',' . $ds . ',' . $hs . ',' . $mis . ',' . $ss;

		$pcdt_style = 'd-u-s' === $settings['premium_countdown_style'] ? ' side' : ' down';

		$event = 'digit';
		$text  = '';
		if ( 'text' === $settings['premium_countdown_expire_text_url'] ) {
			$event = 'onExpiry';
			$text  = '<div class="premium-countdown-exp-message">' . $this->parse_text_editor( $settings['premium_countdown_expiry_text_'] ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} elseif ( 'url' === $settings['premium_countdown_expire_text_url'] ) {
			$redirect = ! empty( $settings['premium_countdown_expiry_redirection_'] ) ? esc_url( $settings['premium_countdown_expiry_redirection_'] ) : '';
			$event    = 'expiryUrl';
			$text     = $redirect;
		}

		$separator_text = ! empty( $settings['premium_countdown_separator_text'] ) ? $settings['premium_countdown_separator_text'] : '';

		$countdown_settings = array(
			'label1'     => $label,
			'label2'     => $labels1,
			'until'      => $target_date,
			'format'     => $format,
			'event'      => $event,
			'text'       => $text,
			'serverSync' => $sent_time,
			'separator'  => $separator_text,
			'timerType'  => $timer_type,
			'reset'      => $reset,
		);

		$flipped = 'yes' === $settings['premium_countdown_flip'] ? 'premium-countdown-flip' : '';

		$this->add_render_attribute(
			'container',
			array(
				'id'            => 'countDownContiner-' . esc_attr( $this->get_id() ),
				'class'         => array( 'premium-countdown', 'premium-countdown-separator-' . esc_attr( $settings['premium_countdown_separator'] ) ),
				'data-settings' => wp_json_encode( $countdown_settings ),
			)
		);

		$this->add_render_attribute(
			'inner_counter',
			array(
				'id'    => 'countdown-' . esc_attr( $this->get_id() ),
				'class' => array( 'premium-countdown-init', 'countdown' . esc_attr( $pcdt_style ), $flipped ),
			)
		);

		?>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'container' ) ); ?>>
			<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'inner_counter' ) ); ?>></div>
		</div>
		<?php
	}
}
