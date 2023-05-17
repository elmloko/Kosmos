<?php
/**
 * Premium Blog.
 */

namespace PremiumAddons\Widgets;

// Elementor Classes.
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;

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

		$this->start_controls_section(
			'premium_section_wpcf7_form',
			array(
				'label' => __( 'Contact Form', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_wpcf7_form',
			array(
				'label'       => __( 'Select Your Contact Form', 'premium-addons-for-elementor' ),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT,
				'options'     => $this->get_wpcf_forms(),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_wpcf7_fields',
			array(
				'label' => __( 'Fields', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_wpcf7_fields_heading',
			array(
				'label' => __( 'Width', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_responsive_control(
			'premium_elements_input_width',
			array(
				'label'      => __( 'Input Field', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 1200,
					),
					'em' => array(
						'min' => 1,
						'max' => 80,
					),
				),
				'default'    => array(
					'size' => 100,
					'unit' => '%',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-cf7-container input.wpcf7-text, {{WRAPPER}} .premium-cf7-container .wpcf7-file' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_elements_textarea_width',
			array(
				'label'      => __( 'Text Area', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 1200,
					),
					'em' => array(
						'min' => 1,
						'max' => 80,
					),
				),
				'default'    => array(
					'size' => 100,
					'unit' => '%',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-cf7-container textarea.wpcf7-textarea' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'premium_wpcf7_fields_height_heading',
			array(
				'label' => __( 'Height', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_responsive_control(
			'premium_elements_input_height',
			array(
				'label'      => __( 'Input Field', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 500,
					),
					'em' => array(
						'min' => 1,
						'max' => 40,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-cf7-container input.wpcf7-text' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_elements_textarea_height',
			array(
				'label'      => __( 'Text Area', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 1200,
					),
					'em' => array(
						'min' => 1,
						'max' => 80,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-cf7-container textarea.wpcf7-textarea' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_wpcf7_button',
			array(
				'label' => __( 'Button', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_responsive_control(
			'premium_elements_button_width',
			array(
				'label'      => __( 'Width', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 1200,
					),
					'em' => array(
						'min' => 1,
						'max' => 80,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-cf7-container input.wpcf7-submit' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_elements_button_height',
			array(
				'label'      => __( 'Height', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 500,
					),
					'em' => array(
						'min' => 1,
						'max' => 40,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-cf7-container input.wpcf7-submit' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_contact_form_styles',
			array(
				'label' => __( 'Form', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'premium_elements_input_background',
			array(
				'label'     => __( 'Input Field Background', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-cf7-container input, {{WRAPPER}} .premium-cf7-container textarea' => 'background: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'premium_elements_input_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-cf7-container input, {{WRAPPER}} .premium-cf7-container textarea' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_elements_input_border',
				'selector' => '{{WRAPPER}} .premium-cf7-container input, {{WRAPPER}} .premium-cf7-container textarea, {{WRAPPER}} .premium-cf7-container select',
			)
		);

		$this->add_responsive_control(
			'premium_elements_input_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-cf7-container input, {{WRAPPER}} .premium-cf7-container textarea, {{WRAPPER}} .premium-cf7-container select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_elements_input_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-cf7-container input, {{WRAPPER}} .premium-cf7-container textarea, {{WRAPPER}} .premium-cf7-container select' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'premium_elements_input_focus',
			array(
				'label'     => __( 'Focus Border Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-cf7-container input.wpcf7-text:focus, {{WRAPPER}} .premium-cf7-container textarea.wpcf7-textarea:focus , {{WRAPPER}} .premium-cf7-container .wpcf7-file:focus' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_elements_input_focus_border_animation',
			array(
				'label'        => __( 'Focus Border Animation', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'premium-contact-form-anim-',
			)
		);

		$this->add_control(
			'premium_elements_input_focus_border_color',
			array(
				'label'     => __( 'Focus Line Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'premium_elements_input_focus_border_animation' => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}}.premium-contact-form-anim-yes .wpcf7-span.is-focused::after' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'input_button_shadow',
				'selector' => '{{WRAPPER}} .premium-cf7-container input.wpcf7-text, {{WRAPPER}} .premium-cf7-container textarea.wpcf7-textarea, {{WRAPPER}} .premium-cf7-container .wpcf7-file',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_contact_form_typography',
			array(
				'label' => __( 'Labels', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'premium_elements_heading_default',
			array(
				'type'  => Controls_Manager::HEADING,
				'label' => __( 'Default Typography', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_elements_contact_form_color',
			array(
				'label'     => __( 'Default Font Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-cf7-container, {{WRAPPER}} .premium-cf7-container label' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'premium_elements_contact_form_default_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-cf7-container',
			)
		);

		$this->add_control(
			'premium_elements_heading_input',
			array(
				'type'      => Controls_Manager::HEADING,
				'label'     => __( 'Input Typography', 'premium-addons-for-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'premium_elements_contact_form_field_color',
			array(
				'label'     => __( 'Input Text Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-cf7-container select.wpcf7-select, {{WRAPPER}} .premium-cf7-container input.wpcf7-text, {{WRAPPER}} .premium-cf7-container textarea.wpcf7-textarea' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'select_items_color',
			array(
				'label'     => __( 'Select Field Items Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-cf7-container select.wpcf7-select option' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'premium_elements_contact_form_field_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-cf7-container select.wpcf7-select, {{WRAPPER}} .premium-cf7-container input.wpcf7-text, {{WRAPPER}} .premium-cf7-container textarea.wpcf7-textarea',
			)
		);

		$this->add_control(
			'premium_elements_contact_form_placeholder_color',
			array(
				'label'     => __( 'Placeholder Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-cf7-container ::-webkit-input-placeholder' => 'color: {{VALUE}};',
					'{{WRAPPER}} .premium-cf7-container ::-moz-placeholder' => 'color: {{VALUE}};',
					'{{WRAPPER}} .premium-cf7-container ::-ms-input-placeholder' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_contact_form_submit_button_styles',
			array(
				'label' => __( 'Button', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'section_title_premium_btn_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-cf7-container input.wpcf7-submit',
			)
		);

		$this->add_responsive_control(
			'section_title_premium_btn_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-cf7-container input.wpcf7-submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'btn_style_tabs' );

		$this->start_controls_tab( 'normal', array( 'label' => __( 'Normal', 'premium-addons-for-elementor' ) ) );

		$this->add_control(
			'premium_elements_button_text_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-for-elementor' ),
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
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
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
				'label'     => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-cf7-container input.wpcf7-submit' => 'border-radius: {{SIZE}}px;',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'button_box_shadow',
				'selector' => '{{WRAPPER}} .premium-cf7-container input.wpcf7-submit',
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
				'label'     => __( 'Text Color', 'premium-addons-for-elementor' ),
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

		$this->add_control(
			'premium_elements_button_hover_border_color',
			array(
				'label'     => __( 'Border Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-cf7-container input.wpcf7-submit:hover' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'button_hover_box_shadow',
				'selector' => '{{WRAPPER}} .premium-cf7-container input.wpcf7-submit:hover',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

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

		if ( ! empty( $settings['premium_wpcf7_form'] ) ) {

			$this->add_render_attribute( 'container', 'class', 'premium-cf7-container' );

			?>

			<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'container' ) ); ?>>
				<?php echo do_shortcode( '[contact-form-7 id="' . $settings['premium_wpcf7_form'] . '" ]' ); ?>
			</div>

			<?php
		}

	}
}
