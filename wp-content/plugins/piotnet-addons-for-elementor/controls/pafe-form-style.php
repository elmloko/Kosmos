<?php
require_once( __DIR__ . '/controls-manager.php' );

	class PAFE_Form_Style extends \Elementor\Widget_Base {

		public function __construct() {
			parent::__construct();
			$this->init_control(); 
		}

		public function get_name() {
			return 'pafe-form-style';
		}

		public function pafe_register_controls( $element, $args ) {

			$element->start_controls_section(
				'pafe_form_style_section',
				[
					'label' => __( 'PAFE Form Style', 'pafe' ),
					'tab' => PAFE_Controls_Manager_Free::TAB_PAFE,
				]
			);

			$element->add_control(
				'pafe_form_style',
				[ 
					'label' => __( 'Enable Form Style', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'default' => '',
					'label_on' => 'Yes',
					'label_off' => 'No',
					'return_value' => 'yes',
				] 
			);

			$element->add_responsive_control(
				'pafe_form_style_width',
				[ 
					'label' => __( 'Width', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px','%' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 1000,
							'step' => 1,
						],
						'%' => [
							'min' => 0,
							'max' => 100,
							'step' => 1,
						],
					],
					'default' => [
						'unit' => '%',
						'size' => 100,
					],
					'selectors' => [
						'{{WRAPPER}} .wpcf7 input:not([type="submit"]):not([type="checkbox"]):not([type="radio"])' => 'width: {{SIZE}}{{UNIT}} ',
						'{{WRAPPER}} .wpcf7-textarea' => 'width: {{SIZE}}{{UNIT}} ',
						'{{WRAPPER}} .wpcf7-select' => 'width: {{SIZE}}{{UNIT}} ',
					],
					'condition' => [ 
						'pafe_form_style' => 'yes',
					]
				]
			);

			$element->add_responsive_control(
	   			'pafe_form_style_align',
	    			[
	  				'label' => __( 'Alignment', 'elementor' ),
	 				'type' => \Elementor\Controls_Manager::CHOOSE,
	   				'options' => [
	  					'left' => [
	 						'title' => __( 'Left', 'elementor' ),
	  						'icon' => 'eicon-text-align-left',
	   					],
	     				'center' => [
	  						'title' => __( 'Center', 'elementor' ),
	   						'icon' => 'eicon-text-align-center',
	    					],
	    				'right' => [
	   						'title' => __( 'Right', 'elementor' ),
	   						'icon' => 'eicon-text-align-right',
	   					],
	   				],
					'selectors' => [
						'{{WRAPPER}} .wpcf7 p' => 'text-align: {{VALUE}};',
						'{{WRAPPER}} .wpcf7 label' => 'width: 100%; text-align:left; display:block;',
	 				],
	 				'condition' => [ 
						'pafe_form_style' => 'yes',
					]
	   			]
   			);

			$element->add_responsive_control( 
				'pafe_form_style_row_gap',
				[ 
					'label' => __( 'Row Gap', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px','%' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 100,
							'step' => 1,
						],
						'%' => [
							'min' => 0,
							'max' => 100, 
							'step' => 1,
						],
					],
					'default' => [
						'unit' => 'px',
						'size' => 20,
					],
					'selectors' => [
						'{{WRAPPER}} .wpcf7 input:not([type="submit"])' => 'margin-bottom: {{SIZE}}{{UNIT}} ',
						'{{WRAPPER}} .wpcf7-textarea' => 'margin-bottom: {{SIZE}}{{UNIT}} ',
						'{{WRAPPER}} .wpcf7-select' => 'margin-bottom: {{SIZE}}{{UNIT}} ',
					],
					'condition' => [ 
						'pafe_form_style' => 'yes',
					]
				]
			);

			$element->add_responsive_control(
				'pafe_form_style_padding',
				[
					'label' => __( 'Input Padding', 'pafe' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors' => [
						'{{WRAPPER}} .wpcf7 input:not([type="submit"]):not([type="checkbox"]):not([type="radio"])' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						'{{WRAPPER}} .wpcf7-textarea' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						'{{WRAPPER}} .wpcf7-select' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' => [ 
						'pafe_form_style' => 'yes',
					]
				]
			); 

			$element->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				[
					'name' => 'pafe_form_style_input_border',
					'label' => __( 'Border', 'pafe' ),
					'selector' => '{{WRAPPER}} .wpcf7 input:not([type="submit"]), {{WRAPPER}} .wpcf7-textarea, {{WRAPPER}} .wpcf7-select',
					'condition' => [ 
						'pafe_form_style' => 'yes',
					]
				]
				
			);

			$element->add_responsive_control(
				'pafe_form_style_input_border_radius',
				[
					'label' => __( 'Input Border Radius', 'pafe' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors' => [
						'{{WRAPPER}} .wpcf7 input:not([type="submit"])' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						'{{WRAPPER}} .wpcf7-textarea' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						'{{WRAPPER}} .wpcf7-select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' => [ 
						'pafe_form_style' => 'yes',
					]
				] 
			);

			$element->add_responsive_control(
				'pafe_form_style_area_padding',
				[
					'label' => __( 'Text Area Padding', 'pafe' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors' => [
						'{{WRAPPER}} .wpcf7-textarea' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' => [ 
						'pafe_form_style' => 'yes',
					]
				]
			);

			$element->add_control(
				'pafe_form_style_background_color',
				[
					'label' => __( 'Background Color', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'scheme' => [
						'type' => \Elementor\Core\Schemes\Color::get_type(),
						'value' => \Elementor\Core\Schemes\Color::COLOR_1,
					],
					'default' => '#f1f1f1', 
					'selectors' => [
						'{{WRAPPER}} .wpcf7 input:not([type="submit"])' => 'background-color: {{VALUE}}',
						'{{WRAPPER}} .wpcf7 select' => 'background-color: {{VALUE}}',
						'{{WRAPPER}} .wpcf7 textarea' => 'background-color: {{VALUE}}',
					],
					'condition' => [ 
						'pafe_form_style' => 'yes',
					]
				]
			);
			
			$element->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'label' => __( 'Label', 'pafe' ),
					'name' => 'pafe_form_style_label',
					'selector' => '{{WRAPPER}} .wpcf7 label',
					'scheme' => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_1, 
					'separator' => 'before',
					'condition' => [ 
						'pafe_form_style' => 'yes'
					]
				]
			);

			$element->add_control(
				'pafe_form_style_label_color',
				[
					'label' => __( 'Label Color', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'scheme' => [
						'type' => \Elementor\Core\Schemes\Color::get_type(),
						'value' => \Elementor\Core\Schemes\Color::COLOR_1,
					],
					'default' => '#000', 
					'selectors' => [
						'{{WRAPPER}} .wpcf7 label' => 'color: {{VALUE}}',
					],
					
					'condition' => [ 
						'pafe_form_style' => 'yes',
					]
				]
			);

			$element->add_responsive_control(
				'pafe_form_style_label_spacing',
				[ 
					'label' => __( 'Spacing', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px','%' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 100,
							'step' => 1,
						],
						'%' => [
							'min' => 0,
							'max' => 100, 
							'step' => 1,
						],
					],
					'default' => [
						'unit' => 'px',
						'size' => 0,
					],
					'separator' => 'after',
					'selectors' => [
						'{{WRAPPER}} .wpcf7 input:not([type="submit"])' => 'margin-top: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .wpcf7-textarea' => 'margin-top: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .wpcf7-select' => 'margin-top: {{SIZE}}{{UNIT}};',
					],
					'condition' => [ 
						'pafe_form_style' => 'yes',
					]
				]
			);

 			$element->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'label' => __( 'Placeholder', 'pafe' ),
					'name' => 'pafe_form_style_placeholder',
					'selector' => '{{WRAPPER}} ::placeholder',
					'scheme' => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_1, 
					'separator' => 'before',
					'condition' => [ 
						'pafe_form_style' => 'yes'
					]
				]
			);

			$element->add_control(
				'pafe_form_style_placeholder_color',
				[
					'label' => __( 'Placeholder Color', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'scheme' => [
						'type' => \Elementor\Core\Schemes\Color::get_type(),
						'value' => \Elementor\Core\Schemes\Color::COLOR_1,
					],
					'default' => '#7D7676', 
					'selectors' => [
						'{{WRAPPER}} ::placeholder' => 'color: {{VALUE}}',
					],
					'separator' => 'after',
					'condition' => [ 
						'pafe_form_style' => 'yes',
					]
				]
			);

			$element->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'label' => __( 'Button', 'pafe' ),
					'name' => 'pafe_form_style_button',
					'selector' => '{{WRAPPER}} .wpcf7-submit',
					'scheme' => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_1, 
					'separator' => 'before',
					'condition' => [ 
						'pafe_form_style' => 'yes'
					]
				]
			);

			$element->add_responsive_control(
				'pafe_form_style_button_width',
				[ 
					'label' => __( 'Button Width', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px','%' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 1000,
							'step' => 1,
						],
						'%' => [
							'min' => 0,
							'max' => 100,
							'step' => 1,
						],
					],
					'default' => [
						'unit' => '%',
						'size' => 100,
					],
					'selectors' => [
						'{{WRAPPER}} .wpcf7-submit' => 'width: {{SIZE}}{{UNIT}} ',
					],
					'condition' => [ 
						'pafe_form_style' => 'yes',
					]
				]
			);

			$element->add_responsive_control(
				'pafe_form_style_margin',
				[
					'label' => __( 'Button Margin', 'pafe' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors' => [
						'{{WRAPPER}} .wpcf7-submit' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' => [ 
						'pafe_form_style' => 'yes',
					]
				]
			);

			$element->start_controls_tabs(
				'hover_effect_tabs'
			);

			$element->start_controls_tab( 
				'normal_tabs',
				[
					'label' => __( 'Normal Button', 'pafe' ),
					'condition' => [ 
						'pafe_form_style' => 'yes',
					]
				]
			);	

			$element->add_control(
				'pafe_form_style_button_color_normal',
				[
					'label' => __( 'Button Color', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'scheme' => [
						'type' => \Elementor\Core\Schemes\Color::get_type(),
						'value' => \Elementor\Core\Schemes\Color::COLOR_1,
					],
					'default' => '#fff', 
					'selectors' => [
						'{{WRAPPER}} .wpcf7-submit' => 'color: {{VALUE}}',
					],
					'condition' => [ 
						'pafe_form_style' => 'yes',
					]
				]
			);

			$element->add_control(
				'pafe_form_style_background_color_normal',
				[
					'label' => __( 'Background Color', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'scheme' => [
						'type' => \Elementor\Core\Schemes\Color::get_type(),
						'value' => \Elementor\Core\Schemes\Color::COLOR_1,
					],
					'default' => '#6EC1E4', 
					'selectors' => [
						'{{WRAPPER}} .wpcf7-submit' => 'background-color: {{VALUE}}',
					],
					'condition' => [ 
						'pafe_form_style' => 'yes',
					]
				]
			);

			$element->end_controls_tab();
			$element->start_controls_tab( 
				'hover_tabs',
				[
					'label' => __( 'Hover Button', 'pafe' ),
					'condition' => [ 
						'pafe_form_style' => 'yes',
					]
				]
			);

			$element->add_control(
				'pafe_form_style_button_color_hover',
				[
					'label' => __( 'Button Color', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'scheme' => [
						'type' => \Elementor\Core\Schemes\Color::get_type(),
						'value' => \Elementor\Core\Schemes\Color::COLOR_1,
					],
					'default' => '#fff', 
					'selectors' => [
						'{{WRAPPER}} .wpcf7 input[type="submit"]:hover' => 'color: {{VALUE}}',
					],
					'condition' => [ 
						'pafe_form_style' => 'yes',
					]
				]
			);

			$element->add_control(
				'pafe_form_style_background_color_hover',
				[
					'label' => __( 'Background Color', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'scheme' => [
						'type' => \Elementor\Core\Schemes\Color::get_type(),
						'value' => \Elementor\Core\Schemes\Color::COLOR_1,
					],
					'default' => '#04A5E9', 
					'selectors' => [
						'{{WRAPPER}} .wpcf7 input[type="submit"]:hover' => 'background-color: {{VALUE}}',
					],
					'condition' => [ 
						'pafe_form_style' => 'yes',
					]
				]
			);

			$element->end_controls_tab();
			$element->end_controls_tabs();

			$element->add_responsive_control(
				'pafe_form_style_button_padding',
				[
					'label' => __( 'Button Padding', 'pafe' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors' => [
						'{{WRAPPER}} .wpcf7 input[type="submit"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' => [ 
						'pafe_form_style' => 'yes',
					]
				] 
			); 

			$element->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				[
					'name' => 'pafe_form_style_button_border',
					'label' => __( 'Button Border', 'pafe' ),
					'selector' => '{{WRAPPER}} .wpcf7 input[type="submit"]',
					'condition' => [ 
						'pafe_form_style' => 'yes',
					]
				]
				
			);

			$element->add_responsive_control(
				'pafe_form_style_button_border_radius',
				[
					'label' => __( 'Button Border Radius', 'pafe' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors' => [
						'{{WRAPPER}} .wpcf7 input[type="submit"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' => [ 
						'pafe_form_style' => 'yes',
					]
				] 
			);
   			
			$element->end_controls_section();
		} 	
		protected function init_control() {
			add_action( 'elementor/element/common/_section_background/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		}
	}
