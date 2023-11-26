<?php
require_once( __DIR__ . '/controls-manager.php' );

	class PAFE_Tooltip extends \Elementor\Widget_Base {

		public function __construct() {
			parent::__construct();
			$this->init_control();
		}

		public function get_name() {
			return 'pafe-tooltip';
		}

		public function pafe_register_controls( $element, $args ) {

			$element->start_controls_section(
				'pafe_tooltip_section',
				[
					'label' => __( 'PAFE Tooltip', 'pafe' ),
					'tab' => PAFE_Controls_Manager_Free::TAB_PAFE,
				]
			);

			$element->add_control(
				'pafe_tooltip',
				[
					'label' => __( 'Enable Tooltip', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'default' => '',
					'description' => __( 'This feature only works on the frontend.', 'pafe' ),
					'label_on' => 'Yes',
					'label_off' => 'No',
					'return_value' => 'yes',
				] 
			);
			$element->add_control(
				'pafe_tooltip_content_type',
				[
					'label' => __( 'Type', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => 'content',
					'options' => [
						'image'  => __( 'Image','pafe' ),
						'content' => __( 'Text Editor','pafe' ),
						'saved_template' => __( 'Elementor Template','pafe' ),
					],
					'condition' => [ 
						'pafe_tooltip' => 'yes',
					]
				]
			);

			$element->add_control(
				'pafe_tooltip_content_wysiwyg',
				[ 
					'label' => __( 'Text Editor','pafe' ),
					'type' => \Elementor\Controls_Manager::WYSIWYG,
					'condition' => [
						'pafe_tooltip_content_type' => 'content',
						'pafe_tooltip' => 'yes',	
					]
				] 
			);

			$element->add_control(
				'pafe_tooltip_content_saved_template',
				[
					'label' => __( 'Shortcode', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'placeholder' => __( 'Shortcode', 'pafe' ),
					'condition' => [
						'pafe_tooltip_content_type' => 'saved_template'	
					]
				]
			);

			$element->add_control(
				'pafe_tooltip_content_image',
				[
					'label' => __( 'Choose Image', 'pafe' ),
					'type' => \Elementor\Controls_Manager::MEDIA,
					'default' => [
						'url' => \Elementor\Utils::get_placeholder_image_src(),
					],
					'dynamic' => [
						'active' => true,
					],
					'condition' => [
						'pafe_tooltip_content_type' => 'image',
						'pafe_tooltip' => 'yes',	
					]
				]
			);
 
			$element->add_control(
				'pafe_tooltip_duration',
				[
					'label' => __( 'Duration (ms)', 'pafe' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'default' => '300',
					'condition' => [ 
						'pafe_tooltip' => 'yes',
					]
				] 
			);

			$element->add_control(
				'pafe_tooltip_distance',
				[
					'label' => __( 'Distance', 'pafe' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'size_units' => [ 'px' ],
					'default' => '5',
					'condition' => [ 
						'pafe_tooltip' => 'yes',
					]
				] 
			);

			$element->add_control(
				'pafe_tooltip_animation_type',
				[
					'label' => __( 'Animation Type', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => 'scale',
					'options' => [
						'scale'  => __( 'Scale','pafe' ),
						'fade' => __( 'Fade','pafe' ),
					],
					'condition' => [ 
						'pafe_tooltip' => 'yes',
					]
				] 
			);

			$element->add_control(
				'pafe_tooltip_placement',
				[
					'label' => __( 'Placement', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => 'top',
					'options' => [
						'top'  => __( 'Top','pafe' ),
						'right' => __( 'Right','pafe' ),
						'bottom' => __( 'Bottom','pafe' ),
						'left' => __( 'Left','pafe' ),
					],
					'condition' => [ 
						'pafe_tooltip' => 'yes',
					]
				] 
			);
 
			$element->add_control(
				'pafe_tooltip_content_color',
				[
					'label' => __( 'Content Color', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'global' => [
                        'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
                    ],
					'default' => '#fff', 
					'selectors' => [
						'{{WRAPPER}} [data-tippy-root] > .tippy-box' => 'color: {{VALUE}}',
					],
					'condition' => [ 
						'pafe_tooltip' => 'yes',  
						'pafe_tooltip_content_type' => 'content',
					]
				]
			);

			$element->add_control(
				'pafe_tooltip_custom_width',
				[
					'label' => __( 'Custom Width', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'default' => '',
					'label_on' => 'Yes',
					'label_off' => 'No',
					'return_value' => 'yes',
				] 
			);

			$element->add_responsive_control(
				'pafe_tooltip_content_width',
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
						'{{WRAPPER}} [data-tippy-root]' => 'width: {{SIZE}}{{UNIT}} !important; max-width: none !important;',
						'{{WRAPPER}} [data-tippy-root] > .tippy-box' => 'max-width: none !important;',
					],
					'condition' => [ 
						'pafe_tooltip' => 'yes',
						'pafe_tooltip_custom_width' => 'yes',
					]
				]
			);

			$element->add_control(
				'pafe_tooltip_background_color',
				[
					'label' => __( 'Background Color', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'global' => [
                        'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
                    ],
					'default' => '#000', 
					'selectors' => [
						'{{WRAPPER}} [data-tippy-root] > .tippy-box' => 'background-color: {{VALUE}}',
						'{{WRAPPER}} [data-tippy-root] > .tippy-box .tippy-arrow' => 'color: {{VALUE}}!important',
					],
					'condition' => [ 
						'pafe_tooltip' => 'yes',  
					]
				]
			);

			$element->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'pafe_tooltip_label_typography',
					'selector' => '{{WRAPPER}} [data-tippy-root] > .tippy-box',
					'global' => [
                        'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_ACCENT,
                    ],
					'condition' => [ 
						'pafe_tooltip' => 'yes', 
						'pafe_tooltip_content_type' => 'content', 
					]
				]
			);
			$element->end_controls_section();
		} 

		public function after_render_element($element) {
			$settings = $element->get_settings(); 	
			if ( !empty( $settings['pafe_tooltip'] ) ) {
				$tippy_options = [
					'duration' => absint( $settings['pafe_tooltip_duration'] ),
					'animation' => $settings['pafe_tooltip_animation_type'],
					'distance' => floatval( $settings['pafe_tooltip_distance'] ),
					'placement' => $settings['pafe_tooltip_placement'],
                    'allowHTML'=> true,
				];

				$content = '';
				if ($settings['pafe_tooltip_content_type'] == 'content') {
					$content = $settings['pafe_tooltip_content_wysiwyg'];
				} elseif ($settings['pafe_tooltip_content_type'] == 'image') {
					$content = '<img src="'. $settings['pafe_tooltip_content_image']['url'] . '"alt="">';
				} elseif ($settings['pafe_tooltip_content_type'] == 'saved_template') {
					$content = do_shortcode($settings['pafe_tooltip_content_saved_template']);
				}   
				$element->add_render_attribute( '_wrapper', [
					'data-tippy-content' => $content,
					'data-pafe-tippy-options' => json_encode( $tippy_options ),
				]);
			}
		}

		protected function init_control() {
			add_action( 'elementor/element/section/section_advanced/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
			add_action( 'elementor/element/container/section_layout/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
			add_action( 'elementor/element/column/section_advanced/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
			add_action( 'elementor/element/common/_section_background/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
			add_action( 'elementor/frontend/section/before_render', [ $this, 'after_render_element'], 10, 1 );
			add_action( 'elementor/frontend/column/before_render', [ $this, 'after_render_element'], 10, 1 );
			add_action( 'elementor/frontend/container/before_render', [ $this, 'after_render_element'], 10, 1 );
			add_action( 'elementor/frontend/widget/before_render', [ $this, 'after_render_element'], 10, 1 );
		}

	}	
