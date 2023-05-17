<?php
/**
 * Premium Fancy Text.
 */

namespace PremiumAddons\Widgets;

// Elementor Classes.
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;

// PremiumAddons Classes.
use PremiumAddons\Includes\Helper_Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

/**
 * Class Premium_Fancytext
 */
class Premium_Fancytext extends Widget_Base {


	/**
	 * Retrieve Widget Name.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function get_name() {
		return 'premium-addon-fancy-text';
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function get_title() {
		return __( 'Fancy Text', 'premium-addons-for-elementor' );
	}

	/**
	 * Retrieve Widget Icon.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string widget icon.
	 */
	public function get_icon() {
		return 'pa-fancy-text';
	}

	/**
	 * Retrieve Widget Dependent CSS.
	 *
	 * @since  1.0.0
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
	 * @since  1.0.0
	 * @access public
	 *
	 * @return array JS script handles.
	 */
	public function get_script_depends() {
		return array(
			'pa-typed',
			'pa-vticker',
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
		return array( 'pa', 'premium', 'typing', 'slide', 'headline', 'heading', 'animation' );
	}

	/**
	 * Retrieve Widget Categories.
	 *
	 * @since  1.5.1
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
	 * Register Testimonials controls.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		$this->start_controls_section(
			'premium_fancy_text_content',
			array(
				'label' => __( 'Fancy Text', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_fancy_prefix_text',
			array(
				'label'       => __( 'Prefix', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'default'     => __( 'This is', 'premium-addons-for-elementor' ),
				'description' => __( 'Text before Fancy text', 'premium-addons-for-elementor' ),
				'label_block' => true,
			)
		);

		$repeater = new REPEATER();

		$repeater->add_control(
			'premium_text_strings_text_field',
			array(
				'label'       => __( 'Fancy String', 'premium-addons-for-elementor' ),
				'dynamic'     => array( 'active' => true ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_fancy_text_strings',
			array(
				'label'       => __( 'Fancy Text', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::REPEATER,
				'default'     => array(
					array(
						'premium_text_strings_text_field' => __( 'Designer', 'premium-addons-for-elementor' ),
					),
					array(
						'premium_text_strings_text_field' => __( 'Developer', 'premium-addons-for-elementor' ),
					),
					array(
						'premium_text_strings_text_field' => __( 'Awesome', 'premium-addons-for-elementor' ),
					),
				),
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ premium_text_strings_text_field }}}',
			)
		);

		$this->add_control(
			'premium_fancy_suffix_text',
			array(
				'label'       => __( 'Suffix', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'default'     => __( 'Text', 'premium-addons-for-elementor' ),
				'description' => __( 'Text after Fancy text', 'premium-addons-for-elementor' ),
				'label_block' => true,
			)
		);

		$this->add_responsive_control(
			'premium_fancy_text_align',
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
					'{{WRAPPER}} .premium-fancy-text-wrapper' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_fancy_additional_settings',
			array(
				'label' => __( 'Additional Settings', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_fancy_text_effect',
			array(
				'label'       => __( 'Effect', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'typing'    => __( 'Typing', 'premium-addons-for-elementor' ),
					'slide'     => __( 'Slide Up', 'premium-addons-for-elementor' ),
					'zoomout'   => __( 'Zoom Out', 'premium-addons-for-elementor' ),
					'rotate'    => __( 'Rotate', 'premium-addons-for-elementor' ),
					'auto-fade' => __( 'Auto Fade', 'premium-addons-for-elementor' ),
					'custom'    => __( 'Custom', 'premium-addons-for-elementor' ),
				),
				'default'     => 'typing',
				'render_type' => 'template',
				'label_block' => true,
			)
		);

		$this->add_control(
			'custom_animation',
			array(
				'label'       => __( 'Animations', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::ANIMATION,
				'render_type' => 'template',
				'default'     => 'fadeIn',
				'condition'   => array(
					'premium_fancy_text_effect' => 'custom',
				),
			)
		);

		$this->add_control(
			'premium_fancy_text_type_speed',
			array(
				'label'       => __( 'Type Speed', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 30,
				'description' => __( 'Set typing effect speed in milliseconds.', 'premium-addons-for-elementor' ),
				'condition'   => array(
					'premium_fancy_text_effect' => 'typing',
				),
			)
		);

		$this->add_control(
			'premium_fancy_text_zoom_speed',
			array(
				'label'       => __( 'Animation Speed', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'render_type' => 'template',
				'description' => __( 'Set animation speed in milliseconds. Default value is 1000', 'premium-addons-for-elementor' ),
				'condition'   => array(
					'premium_fancy_text_effect!' => array( 'typing', 'slide' ),
				),
				'selectors'   => array(
					'{{WRAPPER}} .premium-fancy-text-wrapper:not(.typing):not(.slide) .premium-fancy-list-items'   => 'animation-duration: {{VALUE}}ms',
				),
			)
		);

		$this->add_control(
			'premium_fancy_text_zoom_delay',
			array(
				'label'       => __( 'Animation Delay', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => __( 'Set animation delay in milliseconds.Default value is 2500', 'premium-addons-for-elementor' ),
				'condition'   => array(
					'premium_fancy_text_effect!' => array( 'typing', 'slide', 'auto-fade' ),
				),
			)
		);

		$this->add_control(
			'loop_count',
			array(
				'label'     => __( 'Loop Count', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'condition' => array(
					'premium_fancy_text_effect!' => array( 'typing', 'slide', 'auto-fade' ),
				),
			)
		);

		$this->add_control(
			'premium_fancy_text_back_speed',
			array(
				'label'       => __( 'Back Speed', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 30,
				'description' => __( 'Set a speed for backspace effect in milliseconds.', 'premium-addons-for-elementor' ),
				'condition'   => array(
					'premium_fancy_text_effect' => 'typing',
				),
			)
		);

		$this->add_control(
			'premium_fancy_text_start_delay',
			array(
				'label'       => __( 'Start Delay', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 30,
				'description' => __( 'If you set it on 5000 milliseconds, the first word/string will appear after 5 seconds.', 'premium-addons-for-elementor' ),
				'condition'   => array(
					'premium_fancy_text_effect' => 'typing',
				),
			)
		);

		$this->add_control(
			'premium_fancy_text_back_delay',
			array(
				'label'       => __( 'Back Delay', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 30,
				'description' => __( 'If you set it on 5000 milliseconds, the word/string will remain visible for 5 seconds before backspace effect.', 'premium-addons-for-elementor' ),
				'condition'   => array(
					'premium_fancy_text_effect' => 'typing',
				),
			)
		);

		$this->add_control(
			'premium_fancy_text_type_loop',
			array(
				'label'     => __( 'Loop', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => array(
					'premium_fancy_text_effect' => 'typing',
				),
			)
		);

		$this->add_control(
			'premium_fancy_text_show_cursor',
			array(
				'label'     => __( 'Show Cursor', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => array(
					'premium_fancy_text_effect' => 'typing',
				),
			)
		);

		$this->add_control(
			'premium_fancy_text_cursor_text',
			array(
				'label'     => __( 'Cursor Mark', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'default'   => '|',
				'condition' => array(
					'premium_fancy_text_effect'      => 'typing',
					'premium_fancy_text_show_cursor' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_slide_up_speed',
			array(
				'label'       => __( 'Animation Speed', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 200,
				'description' => __( 'Set a duration value in milliseconds for slide up effect.', 'premium-addons-for-elementor' ),
				'condition'   => array(
					'premium_fancy_text_effect' => 'slide',
				),
			)
		);

		$this->add_control(
			'premium_slide_up_pause_time',
			array(
				'label'       => __( 'Pause Time (Milliseconds)', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 3000,
				'description' => __( 'How long should the word/string stay visible? Set a value in milliseconds.', 'premium-addons-for-elementor' ),
				'condition'   => array(
					'premium_fancy_text_effect' => 'slide',
				),
			)
		);

		$this->add_control(
			'premium_slide_up_shown_items',
			array(
				'label'       => __( 'Show Items', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 1,
				'description' => __( 'How many items should be visible at a time?', 'premium-addons-for-elementor' ),
				'condition'   => array(
					'premium_fancy_text_effect' => 'slide',
				),
			)
		);

		$this->add_control(
			'premium_slide_up_hover_pause',
			array(
				'label'       => __( 'Pause on Hover', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'If you enabled this option, the slide will be paused when mouseover.', 'premium-addons-for-elementor' ),
				'condition'   => array(
					'premium_fancy_text_effect' => 'slide',
				),
			)
		);

		$this->add_control(
			'loading_bar',
			array(
				'label'     => __( 'Loading Bar', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'premium_fancy_text_effect!' => 'typing',
				),
			)
		);

		$this->add_responsive_control(
			'premium_fancy_slide_align',
			array(
				'label'     => __( 'Fancy Text Alignment', 'premium-addons-for-elementor' ),
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
					'{{WRAPPER}} .premium-fancy-list-items' => 'text-align: {{VALUE}}',
				),
				'condition' => array(
					'premium_fancy_text_effect' => 'slide',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_pa_docs',
			array(
				'label' => __( 'Helpful Documentations', 'premium-addons-for-elementor' ),
			)
		);

		$title = __( 'Getting started »', 'premium-addons-for-elementor' );

		$doc_url = Helper_Functions::get_campaign_link( 'https://premiumaddons.com/docs/fancy-text-widget-tutorial/', 'editor-page', 'wp-editor', 'get-support' );

		$this->add_control(
			'doc_1',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf( '<a href="%s" target="_blank">%s</a>', $doc_url, $title ),
				'content_classes' => 'editor-pa-doc',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_fancy_text_style_tab',
			array(
				'label' => __( 'Fancy Text', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'premium_fancy_text_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-fancy-text' => 'color: {{VALUE}};',
					'{{WRAPPER}} .premium-fancy-svg-text .premium-fancy-list-items' => 'fill : {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'fancy_text_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-fancy-text-wrapper:not(.auto-fade) .premium-fancy-text, {{WRAPPER}} .premium-fancy-text svg g > text',
			)
		);

		$this->add_control(
			'premium_fancy_text_background_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-fancy-text' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'      => 'text_shadow',
				'selector'  => '{{WRAPPER}} .premium-fancy-text',
				'condition' => array(
					'premium_fancy_text_effect!' => 'auto-fade',
				),
			)
		);

		$this->add_control(
			'autofade_shadow',
			array(
				'label'     => esc_html__( 'Text Shadow', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::POPOVER_TOGGLE,
				'condition' => array(
					'premium_fancy_text_effect' => 'auto-fade',
				),
			)
		);

		$this->add_control(
			'stroke_text_color',
			array(
				'label'     => __( 'Stroke Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'premium_fancy_text_effect!' => 'auto-fade',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-fancy-text' => '-webkit-text-stroke-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'stroke_width',
			array(
				'label'     => __( 'Stroke Width', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => array(
					'{{WRAPPER}} .premium-fancy-text' => '-webkit-text-stroke-width: {{SIZE}}px',
				),
				'condition' => array(
					'premium_fancy_text_effect!' => 'auto-fade',
				),
			)
		);

		$this->start_popover();

		$this->add_control(
			'autofade_shadow_color',
			array(
				'label'       => __( 'Color', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::COLOR,
				'default'     => 'rgba(0, 0, 0, 0.3)',
				'render_type' => 'ui',
			)
		);

		$this->add_responsive_control(
			'autofade_shadow_blur',
			array(
				'label'       => __( 'Blur', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'default'     => array(
					'size' => 10,
				),
				'render_type' => 'ui',
			)
		);

		$this->add_responsive_control(
			'autofade_shadow_hor',
			array(
				'label'       => __( 'Horizontal', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => array(
					'px' => array(
						'min'  => -100,
						'max'  => 100,
						'step' => 10,
					),
				),
				'default'     => array(
					'size' => 0,
				),
				'render_type' => 'ui',
			)
		);

		$this->add_responsive_control(
			'autofade_shadow_ver',
			array(
				'label'       => __( 'Vertical', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => array(
					'px' => array(
						'min'  => -100,
						'max'  => 100,
						'step' => 10,
					),
				),
				'default'     => array(
					'size' => 0,
				),
				'condition'   => array(
					'autofade_shadow' => 'yes',
				),
				'render_type' => 'ui',
				'selectors'   => array(
					'{{WRAPPER}} .premium-fancy-svg-text' => 'filter:drop-shadow( {{autofade_shadow_hor.SIZE}}px {{autofade_shadow_ver.SIZE}}px {{autofade_shadow_blur.SIZE}}px {{autofade_shadow_color.VALUE}} )',
				),
			)
		);

		$this->end_popover();

		$this->add_responsive_control(
			'premium_fancy_autofade_width',
			array(
				'label'     => __( 'Width', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 500,
						'step' => 10,
					),
				),
				'condition' => array(
					'premium_fancy_text_effect' => 'auto-fade',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-fancy-text' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_fancy_autofade_v_align',
			array(
				'label'     => __( 'Vertical Alignment', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'text-top'    => array(
						'title' => __( 'Top', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-arrow-up',
					),
					'middle'      => array(
						'title' => __( 'Center', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-justify',
					),
					'text-bottom' => array(
						'title' => __( 'Bottom', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-arrow-down',
					),
				),
				'default'   => 'middle',
				'toggle'    => false,
				'condition' => array(
					'premium_fancy_text_effect' => 'auto-fade',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-fancy-text' => 'vertical-align: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_fancy_cursor_text_style_tab',
			array(
				'label'     => __( 'Cursor Text', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_fancy_text_cursor_text!' => '',
					'premium_fancy_text_effect'       => 'typing',
				),
			)
		);

		$this->add_control(
			'premium_fancy_text_cursor_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .typed-cursor' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'fancy_text_cursor_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .typed-cursor',
			)
		);

		$this->add_control(
			'premium_fancy_text_cursor_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .typed-cursor' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_prefix_suffix_style_tab',
			array(
				'label' => __( 'Prefix & Suffix', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'premium_prefix_suffix_text_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-prefix-text, {{WRAPPER}} .premium-suffix-text' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'prefix_suffix_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-prefix-text, {{WRAPPER}} .premium-suffix-text',
			)
		);

		$this->add_control(
			'premium_prefix_suffix_text_background_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-prefix-text, {{WRAPPER}} .premium-suffix-text' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'loading_bar_style',
			array(
				'label'     => __( 'Loading Bar', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'loading_bar'                => 'yes',
					'premium_fancy_text_effect!' => 'typing',
				),
			)
		);

		$this->add_control(
			'loading_bar_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .loading .premium-loading-bar' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'loading_bar_height',
			array(
				'label'     => __( 'Height', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 10,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .loading .premium-loading-bar' => 'height: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render Fancy Text widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		$effect = $settings['premium_fancy_text_effect'];

		$loading_bar = 'yes' === $settings['loading_bar'] ? 'loading' : '';

		$pause = '';

		if ( 'typing' === $effect ) {

			$show_cursor = ( ! empty( $settings['premium_fancy_text_show_cursor'] ) ) ? true : false;

			$cursor_text = addslashes( $settings['premium_fancy_text_cursor_text'] );

			$loop = ! empty( $settings['premium_fancy_text_type_loop'] ) ? true : false;

			$strings = array();

			foreach ( $settings['premium_fancy_text_strings'] as $item ) {
				if ( ! empty( $item['premium_text_strings_text_field'] ) ) {
					array_push( $strings, str_replace( '\'', '&#39;', $item['premium_text_strings_text_field'] ) );
				}
			}

			$fancytext_settings = array(
				'effect'     => $effect,
				'strings'    => $strings,
				'typeSpeed'  => $settings['premium_fancy_text_type_speed'],
				'backSpeed'  => $settings['premium_fancy_text_back_speed'],
				'startDelay' => $settings['premium_fancy_text_start_delay'],
				'backDelay'  => $settings['premium_fancy_text_back_delay'],
				'showCursor' => $show_cursor,
				'cursorChar' => $cursor_text,
				'loop'       => $loop,
			);
		} elseif ( 'slide' === $effect ) {

			$this->add_render_attribute( 'prefix', 'class', 'premium-fancy-text-span-align' );
			$this->add_render_attribute( 'suffix', 'class', 'premium-fancy-text-span-align' );

			$mouse_pause        = 'yes' === $settings['premium_slide_up_hover_pause'] ? true : false;
			$pause              = $mouse_pause ? 'pause' : '';
			$fancytext_settings = array(
				'effect'     => $effect,
				'speed'      => $settings['premium_slide_up_speed'],
				'showItems'  => $settings['premium_slide_up_shown_items'],
				'pause'      => $settings['premium_slide_up_pause_time'],
				'mousePause' => $mouse_pause,
			);
		} elseif ( 'auto-fade' === $effect ) {

			$fancytext_settings = array(
				'duration' => ( '' === $settings['premium_fancy_text_zoom_speed'] || 0 === $settings['premium_fancy_text_zoom_speed'] ) ? 9000 : $settings['premium_fancy_text_zoom_speed'],
				'effect'   => $effect,
			);

			$this->add_render_attribute( 'autofade_behavior', 'operator', 'atop' );

		} else {

			$fancytext_settings = array(
				'effect' => $effect,
				'delay'  => $settings['premium_fancy_text_zoom_delay'],
				'count'  => $settings['loop_count'],
			);

			if ( 'custom' === $effect ) {
				$fancytext_settings['animation'] = $settings['custom_animation'];
			}
		}

		$fancytext_settings['loading'] = $loading_bar;

		$this->add_render_attribute(
			'wrapper',
			array(
				'class'         => array( 'premium-fancy-text-wrapper', $effect, $loading_bar, $pause ),
				'data-settings' => wp_json_encode( $fancytext_settings ),
			)
		);

		?>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'wrapper' ) ); ?>>
			<?php if ( ! empty( $settings['premium_fancy_prefix_text'] ) ) : ?>
				<span class="premium-prefix-text">
					<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'prefix' ) ); ?>><?php echo wp_kses( ( $settings['premium_fancy_prefix_text'] ), true ); ?></span>
				</span>
			<?php endif; ?>

		<?php if ( 'typing' === $effect ) : ?>
			<span id="<?php echo esc_attr( 'premium-fancy-text-' . $this->get_id() ); ?>" class="premium-fancy-text"></span>
		<?php elseif ( 'auto-fade' === $effect ) : ?>
			<span class="premium-fancy-text">
				<svg class="premium-fancy-svg-text" viewBox="0 -200 1500 400" width="100%" height="100%" preserveAspectRatio="xMidYMin slice" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1">
					<defs>
						<filter id="wrap">
							<feGaussianBlur in="SourceGraphic" stdDeviation="10" result="blur" />
							<feColorMatrix in="blur" mode="matrix" values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 20 -5" result="wrap"/>
							<feComposite in="SourceGraphic" in2="wrap" <?php echo wp_kses_post( $this->get_render_attribute_string( 'autofade_behavior' ) ); ?> />
						</filter>
					</defs>
					<g filter="url(#wrap)">
					<?php
					foreach ( $settings['premium_fancy_text_strings'] as $index => $item ) :
						if ( ! empty( $item['premium_text_strings_text_field'] ) ) :
							$this->add_render_attribute(
								'text_' . $item['_id'],
								array(
									'class' => 'premium-fancy-list-items',
									'x'     => '0',
									'y'     => '60',
								)
							);
							?>
								<text <?php echo wp_kses_post( $this->get_render_attribute_string( 'text_' . $item['_id'] ) ); ?>>
									<?php echo wp_kses_post( $item['premium_text_strings_text_field'] ); ?>
								</text>
							<?php
						endif;
					endforeach;
					?>
					</g>
				</svg>
			</span>
		<?php else : ?>
			<div class="premium-fancy-text" style='display: inline-block; text-align: center'>
				<ul class="premium-fancy-text-items-wrapper">
			<?php
			foreach ( $settings['premium_fancy_text_strings'] as $index => $item ) :
				if ( ! empty( $item['premium_text_strings_text_field'] ) ) :
					$this->add_render_attribute( 'text_' . $item['_id'], 'class', 'premium-fancy-list-items' );

					if ( ( 'typing' !== $effect && 'slide' !== $effect ) && 0 !== $index ) {
						$this->add_render_attribute( 'text_' . $item['_id'], 'class', 'premium-fancy-item-hidden' );
					} else {
						$this->add_render_attribute( 'text_' . $item['_id'], 'class', 'premium-fancy-item-visible' );
					}

					?>
						<li <?php echo wp_kses_post( $this->get_render_attribute_string( 'text_' . $item['_id'] ) ); ?>>
							<?php echo wp_kses_post( $item['premium_text_strings_text_field'] ); ?>
						</li>
					<?php
				endif;
			endforeach;
			?>
				</ul>
			</div>
		<?php endif; ?>
		<?php if ( ! empty( $settings['premium_fancy_suffix_text'] ) ) : ?>
			<span class="premium-suffix-text">
				<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'suffix' ) ); ?>><?php echo wp_kses( ( $settings['premium_fancy_suffix_text'] ), true ); ?></span>
			</span>
		<?php endif; ?>
	</div>
		<?php
	}

	/**
	 * Render Fancy Text widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function content_template() {
		?>
		<#
			view.addInlineEditingAttributes('prefix');
			view.addInlineEditingAttributes('suffix');

			var effect = settings.premium_fancy_text_effect;

			var loadingBar = 'yes' === settings.loading_bar ? 'loading' : '';

			var fancyTextSettings = {
				'loading' : loadingBar,
			};

			fancyTextSettings.effect = effect;

			var pause =  '';

		if( 'typing' === effect ) {

			var cursorText          = settings.premium_fancy_text_cursor_text,
				cursorTextEscaped   = cursorText.replace(/'/g, "\\'"),
				showCursor  = settings.premium_fancy_text_show_cursor ? true : false,
				loop        = settings.premium_fancy_text_type_loop ? true : false,
				strings     = [];

			_.each( settings.premium_fancy_text_strings, function( item ) {
				if ( '' !== item.premium_text_strings_text_field ) {

					var fancyString = item.premium_text_strings_text_field;

					strings.push( fancyString );
				}
			});

			fancyTextSettings.strings    = strings,
			fancyTextSettings.typeSpeed  = settings.premium_fancy_text_type_speed,
			fancyTextSettings.backSpeed  = settings.premium_fancy_text_back_speed,
			fancyTextSettings.startDelay = settings.premium_fancy_text_start_delay,
			fancyTextSettings.backDelay  = settings.premium_fancy_text_back_delay,
			fancyTextSettings.showCursor = showCursor,
			fancyTextSettings.cursorChar = cursorTextEscaped,
			fancyTextSettings.loop       = loop;

		} else if ( 'slide' === effect ) {

			view.addRenderAttribute( 'prefix', 'class', 'premium-fancy-text-span-align' );
			view.addRenderAttribute( 'suffix', 'class', 'premium-fancy-text-span-align' );

			var mausePause = 'yes' === settings.premium_slide_up_hover_pause ? true : false;

			pause        = mausePause ? 'pause' : '';

			fancyTextSettings.speed         = settings.premium_slide_up_speed,
			fancyTextSettings.showItems     = settings.premium_slide_up_shown_items,
			fancyTextSettings.pause         = settings.premium_slide_up_pause_time,
			fancyTextSettings.mousePause    = mausePause;

		} else if ( 'auto-fade' === effect ) {
			fancyTextSettings.duration = ( '' === settings.premium_fancy_text_zoom_speed || 0 === settings.premium_fancy_text_zoom_speed) ? 9000 : settings.premium_fancy_text_zoom_speed;
			fancyTextSettings.effect = effect;

			view.addRenderAttribute('autoFadeBehavior', 'operator', 'atop');
		} else {

			fancyTextSettings.delay         = settings.premium_fancy_text_zoom_delay;
			fancyTextSettings.count         = settings.loop_count;

			if( 'custom' === effect ) {
				fancyTextSettings.animation = settings.custom_animation;
			}
		}
			view.addRenderAttribute( 'container', {
			'class': [ 'premium-fancy-text-wrapper', effect, loadingBar, pause ],
			'data-settings' : JSON.stringify( fancyTextSettings )
			});
		#>
			<div {{{ view.getRenderAttributeString('container') }}}>
				<span class="premium-prefix-text"><span {{{ view.getRenderAttributeString('prefix') }}}>{{{ settings.premium_fancy_prefix_text }}}</span></span>

			<# if ( 'typing' === effect ) { #>
				<span id="premium-fancy-text-{{{view.getID()}}}" class="premium-fancy-text"></span>
			<# } else if ( 'auto-fade' === effect ) { #>
				<span class="premium-fancy-text" >
				<svg class="premium-fancy-svg-text" viewBox="0 -200 1500 400" width="100%" height="100%" preserveAspectRatio="xMidYMin slice" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" >
				<defs>
					<filter id="wrap">
						<feGaussianBlur in="SourceGraphic" stdDeviation="10" result="blur" />
						<feColorMatrix in="blur" mode="matrix" values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 20 -5" result="wrap"/>
						<feComposite in="SourceGraphic" in2="wrap" {{{ view.getRenderAttributeString('autoFadeBehavior') }}} />
					</filter>
				</defs>
				<g filter="url(#wrap)">
				<# _.each ( settings.premium_fancy_text_strings, function ( item, index ) {
					if ( '' !== item.premium_text_strings_text_field ) {
						view.addRenderAttribute(
							'text_' + item['_id'],
							{
								'class' : 'premium-fancy-list-items',
								'x'     : '0',
								'y'     : '60',
							}
						);
						#>
						<text {{{ view.getRenderAttributeString('text_' + item._id ) }}}>{{{ item.premium_text_strings_text_field }}}</text>
						<# } }); #>
				</g>
				</svg>
			</span>
			<# } else { #>
				<div class="premium-fancy-text" style=' display: inline-block; text-align: center;'>
					<ul class="premium-fancy-text-items-wrapper">
						<# _.each ( settings.premium_fancy_text_strings, function ( item, index ) {
							if ( '' !== item.premium_text_strings_text_field ) {
								view.addRenderAttribute( 'text_' + item._id, 'class', 'premium-fancy-list-items' );
							if( ( 'typing' !== effect && 'slide' !== effect ) && 0 !== index ) {
								view.addRenderAttribute( 'text_' + item._id, 'class', 'premium-fancy-item-hidden' );
							} else {
								view.addRenderAttribute( 'text_' + item._id, 'class', 'premium-fancy-item-visible' );
							} #>
								<li {{{ view.getRenderAttributeString('text_' + item._id ) }}}>{{{ item.premium_text_strings_text_field }}}</li>
						<# } }); #>
					</ul>
				</div>
			<# } #>
				<span class="premium-suffix-text"><span {{{ view.getRenderAttributeString('suffix') }}}>{{{ settings.premium_fancy_suffix_text }}}</span></span>
			</div>
		<?php
	}

}
