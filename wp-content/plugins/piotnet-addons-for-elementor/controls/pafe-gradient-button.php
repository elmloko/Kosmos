<?php
class PAFE_Gradient_Button extends \Elementor\Widget_Base {

	public function __construct() {
		parent::__construct();
		$this->init_control();
	}

	public function get_name() {
		return 'pafe-gradient-text';
	}

	public function pafe_register_controls( $element, $args ) {

		$element->start_controls_section(
			'pafe_gradient_button_section',
			[
				'label' => __( 'PAFE Gradient Button', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$element->add_control(
			'pafe_gradient_button',
			[
				'label' => __( 'Enable Gradient Button', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);

		$element->start_controls_tabs( 'pafe_gradient_button_style' );

		$element->start_controls_tab(
			'pafe_gradient_button_normal',
			[
				'label' => __( 'Normal', 'elementor' ),
			]
		);

		$element->add_control(
			'pafe_gradient_button_color',
			[
				'label' => _x( 'Color', 'Background Control', 'elementor' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#670093',
				'title' => _x( 'Background Color', 'Background Control', 'elementor' ),
				'selectors' => [
					'{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'pafe_gradient_button' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_gradient_button_color_stop',
			[
				'label' => _x( 'Location', 'Background Control', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'default' => [
					'unit' => '%',
					'size' => 0,
				],
				'render_type' => 'ui',
				'condition' => [
					'pafe_gradient_button' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_gradient_button_color_b',
			[
				'label' => _x( 'Second Color', 'Background Control', 'elementor' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#f2295b',
				'render_type' => 'ui',
				'condition' => [
					'pafe_gradient_button' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_gradient_button_color_b_stop',
			[
				'label' => _x( 'Location', 'Background Control', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'default' => [
					'unit' => '%',
					'size' => 100,
				],
				'render_type' => 'ui',
				'condition' => [
					'pafe_gradient_button' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_gradient_button_gradient_type',
			[
				'label' => _x( 'Type', 'Background Control', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'linear' => _x( 'Linear', 'Background Control', 'elementor' ),
					'radial' => _x( 'Radial', 'Background Control', 'elementor' ),
				],
				'default' => 'linear',
				'render_type' => 'ui',
				'condition' => [
					'pafe_gradient_button' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_gradient_button_gradient_angle',
			[
				'label' => _x( 'Angle', 'Background Control', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'deg' ],
				'default' => [
					'unit' => 'deg',
					'size' => 180,
				],
				'range' => [
					'deg' => [
						'step' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button' => 'background-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{pafe_gradient_button_color.VALUE}} {{pafe_gradient_button_color_stop.SIZE}}{{pafe_gradient_button_color_stop.UNIT}}, {{pafe_gradient_button_color_b.VALUE}} {{pafe_gradient_button_color_b_stop.SIZE}}{{pafe_gradient_button_color_b_stop.UNIT}});',
				],
				'condition' => [
					'pafe_gradient_button' => 'yes',
					'pafe_gradient_button_gradient_type' => 'linear',
				],
			]
		);

		$element->add_control(
			'pafe_gradient_button_gradient_position',
			[
				'label' => _x( 'Position', 'Background Control', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'center center' => _x( 'Center Center', 'Background Control', 'elementor' ),
					'center left' => _x( 'Center Left', 'Background Control', 'elementor' ),
					'center right' => _x( 'Center Right', 'Background Control', 'elementor' ),
					'top center' => _x( 'Top Center', 'Background Control', 'elementor' ),
					'top left' => _x( 'Top Left', 'Background Control', 'elementor' ),
					'top right' => _x( 'Top Right', 'Background Control', 'elementor' ),
					'bottom center' => _x( 'Bottom Center', 'Background Control', 'elementor' ),
					'bottom left' => _x( 'Bottom Left', 'Background Control', 'elementor' ),
					'bottom right' => _x( 'Bottom Right', 'Background Control', 'elementor' ),
				],
				'default' => 'center center',
				'selectors' => [
					'{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button' => 'background-color: transparent; background-image: radial-gradient(at {{VALUE}}, {{pafe_gradient_button_color.VALUE}} {{pafe_gradient_button_color_stop.SIZE}}{{pafe_gradient_button_color_stop.UNIT}}, {{pafe_gradient_button_color_b.VALUE}} {{pafe_gradient_button_color_b_stop.SIZE}}{{pafe_gradient_button_color_b_stop.UNIT}});',
				],
				'condition' => [
					'pafe_gradient_button' => 'yes',
					'pafe_gradient_button_gradient_type' => 'radial',
				],
			]
		);

		$element->end_controls_tab();

		$element->start_controls_tab(
			'pafe_gradient_button_hover',
			[
				'label' => __( 'Hover', 'elementor' ),
			]
		);

		$element->add_control(
			'pafe_gradient_button_hover_color',
			[
				'label' => _x( 'Color', 'Background Control', 'elementor' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				'title' => _x( 'Background Color', 'Background Control', 'elementor' ),
				'selectors' => [
					'{{WRAPPER}} a.elementor-button:hover, {{WRAPPER}} .elementor-button:hover' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'pafe_gradient_button' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_gradient_button_hover_color_stop',
			[
				'label' => _x( 'Location', 'Background Control', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'default' => [
					'unit' => '%',
					'size' => 0,
				],
				'render_type' => 'ui',
				'condition' => [
					'pafe_gradient_button' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_gradient_button_hover_color_b',
			[
				'label' => _x( 'Second Color', 'Background Control', 'elementor' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#f2295b',
				'render_type' => 'ui',
				'condition' => [
					'pafe_gradient_button' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_gradient_button_hover_color_b_stop',
			[
				'label' => _x( 'Location', 'Background Control', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'default' => [
					'unit' => '%',
					'size' => 100,
				],
				'render_type' => 'ui',
				'condition' => [
					'pafe_gradient_button' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_gradient_button_hover_gradient_type',
			[
				'label' => _x( 'Type', 'Background Control', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'linear' => _x( 'Linear', 'Background Control', 'elementor' ),
					'radial' => _x( 'Radial', 'Background Control', 'elementor' ),
				],
				'default' => 'linear',
				'render_type' => 'ui',
				'condition' => [
					'pafe_gradient_button' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_gradient_button_hover_gradient_angle',
			[
				'label' => _x( 'Angle', 'Background Control', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'deg' ],
				'default' => [
					'unit' => 'deg',
					'size' => 180,
				],
				'range' => [
					'deg' => [
						'step' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} a.elementor-button:hover, {{WRAPPER}} .elementor-button:hover' => 'background-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{pafe_gradient_button_hover_color.VALUE}} {{pafe_gradient_button_hover_color_stop.SIZE}}{{pafe_gradient_button_hover_color_stop.UNIT}}, {{pafe_gradient_button_hover_color_b.VALUE}} {{pafe_gradient_button_hover_color_b_stop.SIZE}}{{pafe_gradient_button_hover_color_b_stop.UNIT}});',
				],
				'condition' => [
					'pafe_gradient_button' => 'yes',
					'pafe_gradient_button_hover_gradient_type' => 'linear',
				],
			]
		);

		$element->add_control(
			'pafe_gradient_button_hover_gradient_position',
			[
				'label' => _x( 'Position', 'Background Control', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'center center' => _x( 'Center Center', 'Background Control', 'elementor' ),
					'center left' => _x( 'Center Left', 'Background Control', 'elementor' ),
					'center right' => _x( 'Center Right', 'Background Control', 'elementor' ),
					'top center' => _x( 'Top Center', 'Background Control', 'elementor' ),
					'top left' => _x( 'Top Left', 'Background Control', 'elementor' ),
					'top right' => _x( 'Top Right', 'Background Control', 'elementor' ),
					'bottom center' => _x( 'Bottom Center', 'Background Control', 'elementor' ),
					'bottom left' => _x( 'Bottom Left', 'Background Control', 'elementor' ),
					'bottom right' => _x( 'Bottom Right', 'Background Control', 'elementor' ),
				],
				'default' => 'center center',
				'selectors' => [
					'{{WRAPPER}} a.elementor-button:hover, {{WRAPPER}} .elementor-button:hover' => 'background-color: transparent; background-image: radial-gradient(at {{VALUE}}, {{pafe_gradient_button_hover_color.VALUE}} {{pafe_gradient_button_hover_color_stop.SIZE}}{{pafe_gradient_button_hover_color_stop.UNIT}}, {{pafe_gradient_button_hover_color_b.VALUE}} {{pafe_gradient_button_hover_color_b_stop.SIZE}}{{pafe_gradient_button_hover_color_b_stop.UNIT}});',
				],
				'condition' => [
					'pafe_gradient_button' => 'yes',
					'pafe_gradient_button_hover_gradient_type' => 'radial',
				],
			]
		);

		$element->end_controls_tab();
		$element->end_controls_tabs();

		$element->end_controls_section();

	}

	protected function init_control() {
		add_action( 'elementor/element/button/section_style/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/form/section_button_style/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/pafe-form-builder-submit/section_messages_style/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
	}

}
