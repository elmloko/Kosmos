<?php
/**
 * Premium Blog.
 */

namespace PremiumAddons\Widgets;

use PremiumAddons\Includes\Controls\Premium_Image_Choose;

// Elementor Classes.
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;

// PremiumAddons Classes.
use PremiumAddons\Includes\Helper_Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

/**
 * Class Premium_Contactform
 */
class Premium_Contactform extends Widget_Base {

	/**
	 * Retrieve Widget Name.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_name() {
		return 'premium-contact-form';
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return __( 'Contact Form 7', 'premium-addons-for-elementor' );
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
		return 'pa-contact-form';
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
	 * Retrieve Widget Keywords.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget keywords.
	 */
	public function get_keywords() {
		return array( 'pa', 'premium', 'form7', 'contact' );
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
			'premium-addons',
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
	 * Register Contact Form 7 controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		$papro_activated = apply_filters( 'papro_activated', false );

		$this->start_controls_section(
			'premium_section_wpcf7_form',
			array(
				'label' => __( 'Contact Form', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'source',
			array(
				'label'       => __( 'Source', 'premium-addons-for-elementor' ),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'existing' => __( 'Existing Forms', 'premium-addons-for-elementor' ),
					'presets'  => apply_filters( 'pa_pro_label', __( 'Form Presets (Pro)', 'premium-addons-for-elementor' ) ),
				),
				'default'     => 'existing',
			)
		);

		$this->add_control(
			'premium_wpcf7_form',
			array(
				'label'       => __( 'Select Form', 'premium-addons-for-elementor' ),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT,
				'options'     => $this->get_wpcf_forms(),
				'condition'   => array(
					'source' => 'existing',
				),
			)
		);

		$this->add_control(
			'presets',
			array(
				'label'        => __( 'Select From Presets', 'premium-addons-for-elementor' ),
				'type'         => Premium_Image_Choose::TYPE,
				'default'      => 'preset1',
				'prefix_class' => 'premium-cf__',
				'options'      => array(
					'preset1' => array(
						'title'      => __( 'Preset 1', 'premium-addons-for-elementor' ),
						'imagesmall' => PREMIUM_ADDONS_URL . 'widgets/dep/form-presets/pa-cf7-preset1.svg',
						'width'      => '33%',
					),
					'preset2' => array(
						'title'      => __( 'Preset 2', 'premium-addons-for-elementor' ),
						'imagesmall' => PREMIUM_ADDONS_URL . 'widgets/dep/form-presets/pa-cf7-preset2.svg',
						'width'      => '33%',
					),
					'preset3' => array(
						'title'      => __( 'Preset 3', 'premium-addons-for-elementor' ),
						'imagesmall' => PREMIUM_ADDONS_URL . 'widgets/dep/form-presets/pa-cf7-preset3.svg',
						'width'      => '33%',
					),
					'preset4' => array(
						'title'      => __( 'Preset 4', 'premium-addons-for-elementor' ),
						'imagesmall' => PREMIUM_ADDONS_URL . 'widgets/dep/form-presets/pa-cf7-preset4.svg',
						'width'      => '33%',
					),
					'preset5' => array(
						'title'      => __( 'Preset 5', 'premium-addons-for-elementor' ),
						'imagesmall' => PREMIUM_ADDONS_URL . 'widgets/dep/form-presets/pa-cf7-preset5.svg',
						'width'      => '33%',
					),
					'preset6' => array(
						'title'      => __( 'Preset 6', 'premium-addons-for-elementor' ),
						'imagesmall' => PREMIUM_ADDONS_URL . 'widgets/dep/form-presets/pa-cf7-preset6.svg',
						'width'      => '33%',
					),
				),
				'condition'    => array(
					'source' => 'presets',
				),
			)
		);

		if ( $papro_activated ) {

			do_action( 'pa_cf_presets_options', $this );

		} else {

			$get_pro = Helper_Functions::get_campaign_link( 'https://premiumaddons.com/pro', 'editor-page', 'wp-editor', 'get-pro' );

			$this->add_control(
				'presets_notice',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => __( 'This option is available in Premium Addons Pro. ', 'premium-addons-for-elementor' ) . '<a href="' . esc_url( $get_pro ) . '" target="_blank">' . __( 'Upgrade now!', 'premium-addons-for-elementor' ) . '</a>',
					'content_classes' => 'papro-upgrade-notice',
					'condition'       => array(
						'source' => 'presets',
					),
				)
			);

		}

		$this->add_control(
			'form_title',
			array(
				'label' => __( 'Form Title', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'title_text',
			array(
				'label'       => __( 'Title', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
				'condition'   => array(
					'form_title' => 'yes',
				),
				'ai'          => array(
					'active' => false,
				),
			)
		);

		$this->add_control(
			'title_tag',
			array(
				'label'       => __( 'HTML Tag', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'h3',
				'options'     => array(
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'div'  => 'div',
					'span' => 'span',
					'p'    => 'p',
				),
				'label_block' => true,
				'condition'   => array(
					'form_title' => 'yes',
				),
			)
		);

		$this->add_control(
			'form_description',
			array(
				'label' => __( 'Form Description', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'description_text',
			array(
				'label'     => __( 'Description', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXTAREA,
				'dynamic'   => array(
					'active' => true,
				),
				'condition' => array(
					'form_description' => 'yes',
				),
			)
		);

		$this->add_control(
			'fields_effects',
			array(
				'label'        => __( 'Fields Focus Effect', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => '',
				'options'      => array(
					''               => __( 'None', 'premium-addons-for-elementor' ),
					'label'          => apply_filters( 'pa_pro_label', __( 'Label Position (Pro)', 'premium-addons-for-elementor' ) ),
					'label-letter'   => apply_filters( 'pa_pro_label', __( 'Label Letter Spacing (Pro)', 'premium-addons-for-elementor' ) ),
					'label-pos-back' => apply_filters( 'pa_pro_label', __( 'Label Position + Background (Pro)', 'premium-addons-for-elementor' ) ),
					'css-filters'    => apply_filters( 'pa_pro_label', __( 'Label CSS Filters (Pro)', 'premium-addons-for-elementor' ) ),
				),
				'prefix_class' => 'premium-cf-anim-',
				'render_type'  => 'template',
				'label_block'  => true,
			)
		);

		if ( $papro_activated ) {
			do_action( 'pa_cf_effects_options', $this );
		}

		$this->end_controls_section();

		$this->start_controls_section(
			'labels_style_section',
			array(
				'label' => __( 'Labels', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'label_display',
			array(
				'label'     => __( 'Display', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					''             => __( 'Default', 'premium-addons-for-elementor' ),
					'inline'       => __( 'Inline', 'premium-addons-for-elementor' ),
					'inline-block' => __( 'Inline Block', 'premium-addons-for-elementor' ),
				),
				'default'   => 'inline-block',
				'selectors' => array(
					'{{WRAPPER}} .premium-cf7-container .wpcf7-form label, {{WRAPPER}} .premium-cf7-container .wpcf7-form .wpcf7-quiz-label' => 'display: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'label_display_notice',
			array(
				'raw'             => __( 'You may need to change this if labels margin option is not working. Note that this will not change the layout.', 'premium-addons-for-elemeentor' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$this->add_control(
			'labels_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-cf7-container label' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'labels_typography',
				'selector'       => '{{WRAPPER}} .premium-cf7-container label',
				'fields_options' => array(
					'letter_spacing' => array(
						'size_units' => array( 'px' ),
					),
				),
			)
		);

		$this->add_control(
			'labels_background_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-cf7-container label' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'label_border',
				'selector' => '{{WRAPPER}} .premium-cf7-container label',
			)
		);

		$this->add_responsive_control(
			'label_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-cf7-container label' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'labels_text_shadow',
				'selector' => '{{WRAPPER}} .premium-cf7-container label',
			)
		);

		$this->add_responsive_control(
			'labels_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-cf7-container label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'labels_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-cf7-container label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_title_description',
			array(
				'label' => __( 'Title & Description', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'title_desc_align',
			array(
				'label'     => __( 'Alignment', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => __( 'Left', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'     => array(
						'title' => __( 'Center', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end'   => array(
						'title' => __( 'Right', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'center',
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .premium-cf-head' => 'align-items: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_heading',
			array(
				'label'     => __( 'Title', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => array(
					'form_title' => 'yes',
				),
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-cf7-title' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'form_title' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'title_typography',
				'selector'  => '{{WRAPPER}} .premium-cf7-title',
				'condition' => array(
					'form_title' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'      => 'title_shadow',
				'selector'  => '{{WRAPPER}} .premium-cf7-title',
				'condition' => array(
					'form_title' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'title_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-cf7-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'form_title' => 'yes',
				),
			)
		);

		$this->add_control(
			'description_heading',
			array(
				'label'     => __( 'Description', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => array(
					'form_description' => 'yes',
				),
			)
		);

		$this->add_control(
			'description_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-cf7-description' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'form_description' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'description_typography',
				'selector'  => '{{WRAPPER}} .premium-cf7-description',
				'condition' => array(
					'form_description' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'      => 'description_shadow',
				'selector'  => '{{WRAPPER}} .premium-cf7-description',
				'condition' => array(
					'form_description' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'description_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-cf7-description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'form_description' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_fields_styles',
			array(
				'label' => __( 'Input/Textarea', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'field_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-cf7-container .wpcf7-form-control.wpcf7-text, {{WRAPPER}} .premium-cf7-container .wpcf7-form-control.wpcf7-quiz, {{WRAPPER}} .premium-cf7-container .wpcf7-form-control.wpcf7-date, {{WRAPPER}} .premium-cf7-container .wpcf7-form-control.wpcf7-textarea' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'field_typography',
				'selector' => '{{WRAPPER}} .premium-cf7-container .wpcf7-form-control.wpcf7-text, {{WRAPPER}} .premium-cf7-container .wpcf7-form-control.wpcf7-quiz, {{WRAPPER}} .premium-cf7-container .wpcf7-form-control.wpcf7-quiz, {{WRAPPER}} .premium-cf7-container .wpcf7-form-control.wpcf7-textarea',
			)
		);

		$this->add_responsive_control(
			'text_indent',
			array(
				'label'      => __( 'Text Indent', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%', 'custom' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 60,
					),
					'%'  => array(
						'min' => 0,
						'max' => 30,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-cf7-container .wpcf7-form-control.wpcf7-text, {{WRAPPER}} .premium-cf7-container .wpcf7-form-control.wpcf7-quiz, {{WRAPPER}} .premium-cf7-container .wpcf7-form-control.wpcf7-textarea, {{WRAPPER}} .premium-cf7-container .wpcf7-form-control.wpcf7-date' => 'text-indent: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'input_width',
			array(
				'label'      => __( 'Input Width', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%', 'custom' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 1200,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-cf7-container .wpcf7-form-control.wpcf7-text, {{WRAPPER}} .premium-cf7-container .wpcf7-form-control.wpcf7-quiz, {{WRAPPER}} .premium-cf7-container .wpcf7-form-control.wpcf7-date' => 'width: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'input_height',
			array(
				'label'      => __( 'Input Height', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%', 'custom' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 1200,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-cf7-container .wpcf7-form-control.wpcf7-text, {{WRAPPER}} .premium-cf7-container .wpcf7-form-control.wpcf7-quiz, {{WRAPPER}} .premium-cf7-container .wpcf7-form-control.wpcf7-date' => 'height: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'textarea_width',
			array(
				'label'      => __( 'Textarea Width', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%', 'custom' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 1200,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-cf7-container .wpcf7-form-control.wpcf7-textarea' => 'width: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'textarea_height',
			array(
				'label'      => __( 'Textarea Height', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%', 'custom' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 1200,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-cf7-container .wpcf7-form-control.wpcf7-textarea' => 'height: {{SIZE}}{{UNIT}}',
				),
			)
		);

		// IMPORTANT: CHECK THIS
		$this->add_responsive_control(
			'input_spacing',
			array(
				'label'      => __( 'Bottom Spacing', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%', 'custom' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-cf7-container .wpcf7-form-control:not(.wpcf7-submit)' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'field_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-cf7-container .wpcf7-form-control.wpcf7-text, {{WRAPPER}} .premium-cf7-container .wpcf7-form-control.wpcf7-quiz, {{WRAPPER}} .premium-cf7-container .wpcf7-form-control.wpcf7-date, {{WRAPPER}} .premium-cf7-container .wpcf7-form-control.wpcf7-textarea' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'tabs_fields_style' );

		$this->start_controls_tab(
			'tab_fields_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'field_bg',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-cf7-container .wpcf7-form-control.wpcf7-text, {{WRAPPER}} .premium-cf7-container .wpcf7-form-control.wpcf7-quiz, {{WRAPPER}} .premium-cf7-container .wpcf7-form-control.wpcf7-date, {{WRAPPER}} .premium-cf7-container .wpcf7-form-control.wpcf7-textarea' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'field_border',
				'selector' => '{{WRAPPER}} .premium-cf7-container .wpcf7-form-control.wpcf7-text, {{WRAPPER}} .premium-cf7-container .wpcf7-form-control.wpcf7-quiz, {{WRAPPER}} .premium-cf7-container .wpcf7-form-control.wpcf7-date, {{WRAPPER}} .premium-cf7-container .wpcf7-form-control.wpcf7-textarea',
			)
		);

		$this->add_control(
			'field_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-cf7-container .wpcf7-form-control.wpcf7-text, {{WRAPPER}} .premium-cf7-container .wpcf7-form-control.wpcf7-quiz, {{WRAPPER}} .premium-cf7-container .wpcf7-form-control.wpcf7-date, {{WRAPPER}} .premium-cf7-container .wpcf7-form-control.wpcf7-textarea' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'field_box_shadow',
				'selector' => '{{WRAPPER}} .premium-cf7-container .wpcf7-form-control.wpcf7-text, {{WRAPPER}} .premium-cf7-container .wpcf7-form-control.wpcf7-quiz, {{WRAPPER}} .premium-cf7-container .wpcf7-form-control.wpcf7-textarea',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_fields_focus',
			array(
				'label' => __( 'Focus', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'field_bg_focus',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-cf7-container .wpcf7-form input:focus, {{WRAPPER}} .premium-cf7-container .wpcf7-form textarea:focus' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'input_border_focus',
				'selector' => '{{WRAPPER}} .premium-cf7-container .wpcf7-form input:focus, {{WRAPPER}} .premium-cf7-container .wpcf7-form textarea:focus',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'focus_box_shadow',
				'selector' => '{{WRAPPER}} .premium-cf7-container .wpcf7-form input:focus, {{WRAPPER}} .premium-cf7-container .wpcf7-form textarea:focus',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_select_style',
			array(
				'label' => __( 'Select', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'select_width',
			array(
				'label'      => __( 'Width', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%', 'custom' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 1200,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-cf7-container .wpcf7-form-control.wpcf7-select' => 'width: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'select_height',
			array(
				'label'      => __( 'Height', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%', 'custom' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 1200,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-cf7-container .wpcf7-form-control.wpcf7-select' => 'height: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'select_field_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-cf7-container .wpcf7-form-control.wpcf7-select' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'select_field_typography',
				'selector' => '{{WRAPPER}} .premium-cf7-container .wpcf7-form-control.wpcf7-select',
			)
		);

		$this->add_control(
			'select_field_bg',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-cf7-container .wpcf7-form-control.wpcf7-select' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'select_text_indent',
			array(
				'label'      => __( 'Text Indent', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%', 'custom' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 60,
					),
					'%'  => array(
						'min' => 0,
						'max' => 30,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-cf7-container .wpcf7-form-control.wpcf7-select' => 'text-indent: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'select_field_border',
				'selector' => '{{WRAPPER}} .premium-cf7-container .wpcf7-form-control.wpcf7-select',
			)
		);

		$this->add_control(
			'select_field_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-cf7-container .wpcf7-form-control.wpcf7-select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'selectfield_box_shadow',
				'selector' => '{{WRAPPER}} .premium-cf7-container .wpcf7-form-control.wpcf7-select',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_radio_checkbox_style',
			array(
				'label' => __( 'Radio/Checkbox', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'custom_select_style',
			array(
				'label'        => __( 'Custom Radio/Checkbox Style', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'premium-cf7-cselect-',
			)
		);

		$this->add_control(
			'radio_display',
			array(
				'label'     => __( 'Display', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					''       => __( 'Default', 'premium-addons-for-elementor' ),
					'row'    => __( 'Inline', 'premium-addons-for-elementor' ),
					'column' => __( 'Block', 'premium-addons-for-elementor' ),
				),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .premium-cf7-container .wpcf7-radio' => 'flex-direction: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'radio_checkbox_size',
			array(
				'label'      => __( 'Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'size' => '15',
					'unit' => 'px',
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 80,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-cf7-container input[type="checkbox"], {{WRAPPER}} .premium-cf7-container input[type="radio"]' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'custom_select_style' => 'yes',
				),
			)
		);

		$this->add_control(
			'radio_checkbox_text_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-cf7-container .wpcf7-list-item-label' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'radio_checkbox_typography',
				'selector' => '{{WRAPPER}} .premium-cf7-container .wpcf7-list-item-label',
			)
		);

		$this->add_control(
			'radio_checkbox_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-cf7-container input[type="checkbox"], {{WRAPPER}} .premium-cf7-container input[type="radio"]' => 'background: {{VALUE}}',
				),
				'condition' => array(
					'custom_select_style' => 'yes',
				),
			)
		);

		$this->add_control(
			'radio_checkbox_color_checked',
			array(
				'label'     => __( 'Checked Option Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-cf7-container input[type="checkbox"]:checked:before, {{WRAPPER}} .premium-cf7-container input[type="radio"]:checked:before' => 'background: {{VALUE}}',
				),
				'condition' => array(
					'custom_select_style' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'radio_checkbox_border_width',
			array(
				'label'     => __( 'Border Width', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => array(
					'{{WRAPPER}} .premium-cf7-container input[type="checkbox"], {{WRAPPER}} .premium-cf7-container input[type="radio"]' => 'border-width: {{SIZE}}{{UNIT}}',
				),
				'condition' => array(
					'custom_select_style' => 'yes',
				),
			)
		);

		$this->add_control(
			'radio_checkbox_border_color',
			array(
				'label'     => __( 'Border Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-cf7-container [type="checkbox"], {{WRAPPER}} .premium-cf7-container [type="radio"]' => 'border-color: {{VALUE}}',
				),
				'condition' => array(
					'custom_select_style' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'check_radio_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-cf7-container .wpcf7-list-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'custom_select_style' => 'yes',
				),
			)
		);

		$this->add_control(
			'checkbox_heading',
			array(
				'label'     => __( 'Checkboxes', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => array(
					'custom_select_style' => 'yes',
				),

			)
		);

		$this->add_control(
			'checkbox_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-cf7-container [type="checkbox"], {{WRAPPER}} .premium-cf7-container [type="checkbox"]:before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'custom_select_style' => 'yes',
				),
			)
		);

		$this->add_control(
			'radio_heading',
			array(
				'label'     => __( 'Radio Buttons', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => array(
					'custom_select_style' => 'yes',
				),
			)
		);

		$this->add_control(
			'radio_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-cf7-container input[type="radio"], {{WRAPPER}} .premium-cf7-container input[type="radio"]:before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'custom_select_style' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_placeholder_style',
			array(
				'label'     => __( 'Placeholder', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'fields_effects!' => 'label',
				),
			)
		);

		$this->add_control(
			'placeholder_switch',
			array(
				'label'        => __( 'Hide Placeholder', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'premium-cf7-placeholder-hide-',
			)
		);

		$this->add_control(
			'placeholder_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-cf7-container .wpcf7-form-control::placeholder' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'placeholder_switch!' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'typography_placeholder',
				'selector'  => '{{WRAPPER}} .premium-cf7-container .wpcf7-form-control::placeholder',
				'condition' => array(
					'placeholder_switch!' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_errors_style',
			array(
				'label' => __( 'Errors', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->start_controls_tabs( 'tabs_error_messages_style' );

		$this->start_controls_tab(
			'tab_error_messages_alert',
			array(
				'label' => __( 'Alert', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'error_alert_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-cf7-container .wpcf7-not-valid-tip' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'error_alert_typography',
				'selector' => '{{WRAPPER}} .premium-cf7-container .wpcf7-not-valid-tip',
			)
		);

		$this->add_control(
			'error_alert_bg_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-cf7-container .wpcf7-not-valid-tip' => 'background: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'error_alert_border',
				'selector' => '{{WRAPPER}} .premium-cf7-container .wpcf7-not-valid-tip',
			)
		);

		$this->add_responsive_control(
			'error_top_spacing',
			array(
				'label'      => __( 'Top Spacing', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-cf7-container .wpcf7-not-valid-tip' => 'margin-top: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'error_alert_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-cf7-container .wpcf7-not-valid-tip' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_error_messages_fields',
			array(
				'label' => __( 'Fields', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'error_field_bg_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .premium-cf7-container .wpcf7-not-valid' => 'background: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'error_field_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .premium-cf7-container .wpcf7-not-valid.wpcf7-text' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'error_field_border',
				'selector'  => '{{WRAPPER}} .premium-cf7-container .wpcf7-not-valid',
				'separator' => 'before',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_contact_form_submit_button_styles',
			array(
				'label' => __( 'Submit Button', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'button_full_width',
			array(
				'label'        => __( 'Full Width', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'prefix_class' => 'premium-cf7-button-fwidth-',
			)
		);

		$this->add_responsive_control(
			'button_align',
			array(
				'label'        => __( 'Alignment', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::CHOOSE,
				'default'      => 'left',
				'options'      => array(
					'left'   => array(
						'title' => __( 'Left', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'prefix_class' => 'premium-cf7-button-align-',
				'condition'    => array(
					'button_full_width!' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'button_width',
			array(
				'label'      => __( 'Width', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%', 'custom' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1200,
						'step' => 1,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-cf7-container input.wpcf7-submit' => 'width: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'button_full_width!' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'submit_button_typography',
				'selector' => '{{WRAPPER}} .premium-cf7-container input.wpcf7-submit',
			)
		);

		$this->start_controls_tabs( 'btn_style_tabs' );

		$this->start_controls_tab( 'normal', array( 'label' => __( 'Normal', 'premium-addons-for-elementor' ) ) );

		$this->add_control(
			'premium_elements_button_text_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-cf7-container input.wpcf7-submit' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_elements_button_background_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-cf7-container input.wpcf7-submit' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_elements_btn_border',
				'selector' => '{{WRAPPER}} .premium-cf7-container input.wpcf7-submit',
			)
		);

		$this->add_responsive_control(
			'premium_elements_btn_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-cf7-container input.wpcf7-submit' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'submit_text_shadow',
				'selector' => '{{WRAPPER}} .premium-cf7-container input.wpcf7-submit',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'button_box_shadow',
				'selector' => '{{WRAPPER}} .premium-cf7-container input.wpcf7-submit',
			)
		);

		$this->add_responsive_control(
			'submit_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-cf7-container input.wpcf7-submit' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'submit_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-cf7-container input.wpcf7-submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'premium_elements_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_elements_button_hover_text_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-cf7-container input.wpcf7-submit:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_elements_button_hover_background_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-cf7-container input.wpcf7-submit:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'btn_hover_border',
				'selector' => '{{WRAPPER}} .premium-cf7-container input.wpcf7-submit:hover',
			)
		);

		$this->add_responsive_control(
			'btn_hoverr_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-cf7-container input.wpcf7-submit:hover' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'submit_hover_text_shadow',
				'selector' => '{{WRAPPER}} .premium-cf7-container input.wpcf7-submit:hover',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'button_hover_box_shadow',
				'selector' => '{{WRAPPER}} .premium-cf7-container input.wpcf7-submit:hover',
			)
		);

		$this->add_responsive_control(
			'submit_hover_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-cf7-container input.wpcf7-submit:hover' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'submit_hover_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-cf7-container input.wpcf7-submit:hover' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_after_submit_style',
			array(
				'label' => __( 'After Submit Message', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'after_submit_typography',
				'selector' => '{{WRAPPER}} .premium-cf7-container .wpcf7-mail-sent-ng, {{WRAPPER}} .premium-cf7-container .wpcf7-mail-sent-ok, {{WRAPPER}} .premium-cf7-container .wpcf7-response-output',
			)
		);

		$this->add_control(
			'after_submit_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-cf7-container .wpcf7-mail-sent-ng, {{WRAPPER}} .premium-cf7-container .wpcf7-mail-sent-ok, {{WRAPPER}} .premium-cf7-container .wpcf7-response-output' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'after_submit_bg',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-cf7-container .wpcf7-mail-sent-ng, {{WRAPPER}} .premium-cf7-container .wpcf7-mail-sent-ok, {{WRAPPER}} .premium-cf7-container .wpcf7-response-output' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'after_submit_border',
				'selector' => '{{WRAPPER}} .premium-cf7-container .wpcf7-mail-sent-ng, {{WRAPPER}} .premium-cf7-container .wpcf7-mail-sent-ok, {{WRAPPER}} .premium-cf7-container .wpcf7-response-output',
			)
		);

		$this->add_responsive_control(
			'after_submit_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-cf7-container .wpcf7-mail-sent-ng, {{WRAPPER}} .premium-cf7-container .wpcf7-mail-sent-ok, {{WRAPPER}} .premium-cf7-container .wpcf7-response-output' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'after_submit_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-cf7-container .wpcf7-mail-sent-ng, {{WRAPPER}} .premium-cf7-container .wpcf7-mail-sent-ok, {{WRAPPER}} .premium-cf7-container .wpcf7-response-output' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'after_submit_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-cf7-container .wpcf7-mail-sent-ng, {{WRAPPER}} .premium-cf7-container .wpcf7-mail-sent-ok, {{WRAPPER}} .premium-cf7-container .wpcf7-response-output' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Get WPCF Forms
	 *
	 * @since 1.0.0
	 * @access public
	 */
	protected function get_wpcf_forms() {

		if ( ! class_exists( 'WPCF7_ContactForm' ) ) {
			return array();
		}

		$forms = \WPCF7_ContactForm::find(
			array(
				'orderby' => 'title',
				'order'   => 'ASC',
			)
		);

		if ( empty( $forms ) ) {
			return array();
		}

		$result = array();

		foreach ( $forms as $item ) {
			$key            = sprintf( '%1$s::%2$s', $item->id(), $item->title() );
			$result[ $key ] = $item->title();
		}

		return $result;
	}

	/**
	 * Render Contact Form 7 widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings();

		$papro_activated = apply_filters( 'papro_activated', false );

		$source = $settings['source'];

		if ( ! $papro_activated || version_compare( PREMIUM_PRO_ADDONS_VERSION, '2.9.6', '<' ) ) {

			if ( 'presets' === $source || '' !== $settings['fields_effects'] ) {

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

		$form_id = 'existing' === $source ? $settings['premium_wpcf7_form'] : $settings['form_id'];

		if ( ! empty( $form_id ) ) {

			if ( 'yes' === $settings['form_title'] ) {

				$this->add_inline_editing_attributes( 'title_text' );
				$this->add_render_attribute( 'title_text', 'class', 'premium-cf7-title' );

				$title_tag = Helper_Functions::validate_html_tag( $settings['title_tag'] );
			}

			if ( 'yes' === $settings['form_description'] ) {

				$this->add_inline_editing_attributes( 'description_text' );
				$this->add_render_attribute( 'description_text', 'class', 'premium-cf7-description' );

			}

			$this->add_render_attribute( 'container', 'class', 'premium-cf7-container' );

			?>

			<?php if ( 'yes' === $settings['form_title'] || 'yes' === $settings['form_description'] ) : ?>

				<div class="premium-cf-head">

					<?php if ( ! empty( $settings['title_text'] ) ) : ?>
						<<?php echo wp_kses_post( $title_tag ) . ' ' . wp_kses_post( $this->get_render_attribute_string( 'title_text' ) ); ?>>
							<?php echo wp_kses_post( $settings['title_text'] ); ?>
						</<?php echo wp_kses_post( $title_tag ); ?>>
					<?php endif; ?>

					<?php if ( ! empty( $settings['description_text'] ) ) : ?>
						<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'description_text' ) ); ?>>
							<?php echo wp_kses_post( $settings['description_text'] ); ?>
						</div>
					<?php endif; ?>

				</div>

			<?php endif; ?>

			<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'container' ) ); ?>>
				<?php echo do_shortcode( '[contact-form-7 id="' . $form_id . '" ]' ); ?>
			</div>

			<?php
		}

	}
}
