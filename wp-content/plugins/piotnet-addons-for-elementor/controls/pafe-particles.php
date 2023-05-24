<?php
require_once( __DIR__ . '/controls-manager.php' );

	class PAFE_Particles extends \Elementor\Widget_Base {

		public function __construct() {
			parent::__construct();
			$this->init_control();
		}

		public function get_name() {
			return 'pafe-particles';
		}

		public function pafe_register_controls( $element, $args ) {

			$element->start_controls_section(
				'pafe_particles_section',
				[
					'label' => __( 'PAFE Particles', 'pafe' ),
					'tab' => PAFE_Controls_Manager_Free::TAB_PAFE,
				]
			);

			$element->add_control(
				'pafe_particles_enable',
				[
					'label' => __( 'Enable Particles', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'default' => '',
					'description' => __( 'This feature only works on the frontend.', 'pafe' ),
					'label_on' => 'Yes',
					'label_off' => 'No',
					'return_value' => 'yes',
				] 

			);

			$element->add_control(
				'pafe_particles_quantity',
				[
					'label' => __( 'Particles Quantity', 'pafe' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'min' => 5,
					'max' => 2000,
					'step' => 5,
					'default' => 300,
					'condition' => [ 
						'pafe_particles_enable' => 'yes',  
					]
				]
			);

			$element->add_control(
				'pafe_particles_color',
				[
					'label' => __( 'Particles Color', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'default' => '#910a0a',
					'scheme' => [
						'type' => \Elementor\Core\Schemes\Color::get_type(),
						'value' => \Elementor\Core\Schemes\Color::COLOR_1,
					],
					'condition' => [ 
						'pafe_particles_enable' => 'yes',  
					]
				]
			);

			$element->add_control(
				'pafe_particles_shape',
				[
					'label' => __( 'Particles Shape', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => 'circle',
					'options' => [
						'circle'  => __( 'Circle', 'pafe' ),
						'edge'  => __( 'Edge', 'pafe' ),
						'triangle' => __( 'Triangle', 'pafe' ),
						'polygon' => __( 'Polygon', 'pafe' ),
						'star' => __( 'Star', 'pafe' ),
						'image' => __( 'Image', 'pafe' ),
					],
					'condition' => [ 
						'pafe_particles_enable' => 'yes',  
					]
				]
			);

			$element->add_control(
				'pafe_particles_image',
				[
					'label' => __( 'Choose Image', 'pafe' ),
					'type' => \Elementor\Controls_Manager::MEDIA,
					'default' => [
						'url' => \Elementor\Utils::get_placeholder_image_src(),
					],
					'condition' => [ 
						'pafe_particles_shape' => 'image',  
					]
				]
			);

			$element->add_control(
				'pafe_particles_size',
				[
					'label' => __( 'Particles Size', 'pafe' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'min' => 1,
					'max' => 100,
					'step' => 1,
					'default' => 5,
					'condition' => [ 
						'pafe_particles_enable' => 'yes',  
					]
				]
			);

			$element->add_control(
				'pafe_particles_opacity',
				[
					'label' => __( 'Particles Opacity', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 1,
							'step' => 0.01,
						],

					],
					'default' => [
						'unit' => 'px',
						'size' => 0.3,
					],
					'condition' => [ 
						'pafe_particles_enable' => 'yes',  
					]
				]
			);

			$element->add_control(
				'pafe_particles_speed',
				[
					'label' => __( 'Particles Speed', 'pafe' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'min' => 1,
					'max' => 30,
					'step' => 1,
					'default' => 6,
					'condition' => [ 
						'pafe_particles_enable' => 'yes',  
					]
				] 
			);

			$element->add_control(
				'pafe_particles_linked_color',
				[
					'label' => __( 'Linked Color', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'default' => '#910a0a',
					'scheme' => [
						'type' => \Elementor\Core\Schemes\Color::get_type(),
						'value' => \Elementor\Core\Schemes\Color::COLOR_1,
					],
					'condition' => [ 
						'pafe_particles_enable' => 'yes',  
					]
				]
			);


			$element->add_control(
				'pafe_particles_linked_opacity',
				[
					'label' => __( 'Linked Opacity', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 1,
							'step' => 0.01,
						],

					],
					'default' => [
						'unit' => 'px',
						'size' => 0.3,
					],
					'condition' => [ 
						'pafe_particles_enable' => 'yes',  
					]
				]
			);

		
			$element->add_control(
				'pafe_particles_hover_effect',
				[
					'label' => __( 'Hover Effect', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => '',
					'options' => [
						''  => __( 'None', 'pafe' ),
						'repulse'  => __( 'Repulse', 'pafe' ),
						'grab' => __( 'Grab', 'pafe' ),
						'bubble' => __( 'Bubble', 'pafe' ),
						'["grab", "bubble"]' => __( 'Grab Bubble', 'pafe' ),
					],
					'condition' => [ 
						'pafe_particles_enable' => 'yes',  
					]
				]
			);	

			$element->add_control(
				'pafe_particles_click_effect',
				[
					'label' => __( 'Click Effect', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => '',
					'options' => [
						''  => __( 'None', 'pafe' ),
						'repulse'  => __( 'Repulse', 'pafe' ),
						'push' => __( 'Push', 'pafe' ),
						'bubble' => __( 'Bubble', 'pafe' ),
						'remove' => __( 'Remove', 'pafe' ),
					],
					'condition' => [ 
						'pafe_particles_enable' => 'yes',  
					]
				]
		);

			$element->end_controls_section();
		} 

		public function after_render_element($element) {
			$settings = $element->get_settings(); 	
			if ( !empty( $settings['pafe_particles_enable'] ) ) {
				$particles_options = [
					'quantity' => $settings['pafe_particles_quantity'],
					'particles_color' => $settings['pafe_particles_color'],
					'linked_color' => $settings['pafe_particles_linked_color'],
					'hover_effect' => $settings['pafe_particles_hover_effect'],
					'click_effect' => $settings['pafe_particles_click_effect'],
					'particles_shape' => $settings['pafe_particles_shape'],
					'particles_size' => $settings['pafe_particles_size'],
					'particles_speed' => $settings['pafe_particles_speed'],
					'particles_image' => $settings['pafe_particles_image']['url'],
					'particles_opacity' => $settings['pafe_particles_opacity'],
					'linked_opacity' => $settings['pafe_particles_linked_opacity']['size'],

				];

				$element->add_render_attribute( '_wrapper', [
					'data-pafe-particles' => $element->get_id(),
					'data-pafe-particles-options'=> json_encode($particles_options),
				]);
			}
		}

		protected function init_control() {
			add_action( 'elementor/element/section/section_advanced/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
			add_action( 'elementor/element/container/section_layout/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
			add_action( 'elementor/frontend/section/before_render', [ $this, 'after_render_element'], 10, 1 );
			add_action( 'elementor/frontend/container/before_render', [ $this, 'after_render_element'], 10, 1 );
		}

	}	
