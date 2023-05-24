<?php
	class PAFE_Image_Accordion extends \Elementor\Widget_Base {
		public function get_name() {
			return 'pafe-image-accordion';
		}

		public function get_title() {
			return __( 'PAFE Image Accordion', 'pafe' );
		}

		public function get_icon() {
			return 'eicon-image-rollover';
		} 

		public function get_categories() {
			return [ 'pafe-free-widgets' ];
		}
 
		public function get_keywords() {
			return [ 'image', 'accordion' ];
		}

		public function get_script_depends() {
			return [ 
				'pafe-widget-free'
			];
		}

		public function get_style_depends() {
			return [ 
				'pafe-widget-style-free'
			];
		}

		protected function _register_controls() {
			$this->start_controls_section(
				'pafe_image_accordion_section',
				[
					'label' => __( 'Settings', 'pafe' ),
				]
			);
			
			$repeater = new \Elementor\Repeater();
			$repeater->add_control(
				'pafe_image_accordion_item_image', 
				[
					'label' => __( 'Choose Image', 'pafe' ),
					'type' => \Elementor\Controls_Manager::MEDIA,
				]
			); 
			$repeater->add_control(
				'pafe_image_accordion_item_title',
				[
					'label' => __( 'Title', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'dynamic' => [
						'active' => true,
					],
				]
			); 
			$repeater->add_control(
				'pafe_image_accordion_item_wysiwyg',
				[ 
					'label' => __( 'Description','pafe' ),
					'type' => \Elementor\Controls_Manager::WYSIWYG,
				]
			);
			$repeater->add_control(
				'pafe_image_accordion_item_link',
				[
					'label' => __( 'URL', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'dynamic' => [
						'active' => true,
					],
				]
			); 
			$repeater->add_control(
				'pafe_image_accordion_item_button_text', 
				[
					'label' => __( 'Button Text', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'default'=> 'Read more',
				]
			);
			$this->add_control(
				'pafe_image_accordion',
				[
					'type' => \Elementor\Controls_Manager::REPEATER,
					'show_label' => true,
					'fields' => $repeater->get_controls(),
					'title_field' => __('{{{pafe_image_accordion_item_title}}}'),				
				]
			); 
			$this->end_controls_section();
			$this->start_controls_section(
				'pafe_image_accordion_container_style_section',
				[
					'label' => __( 'Container', 'pafe' ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				]
			);
			$this->add_responsive_control(
				'pafe_image_accordion_container_height',
				[
					'label' => __( 'Height', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 1000,
							'step' => 1,
						],
					],
					'default' => [
						'unit' => 'px',
						'size' => 500,
					],
					'selectors' => [
						'{{WRAPPER}} .pafe-image-accordion' => 'height: {{SIZE}}{{UNIT}};',
					],
				]
			);	
			$this->add_responsive_control(
				'pafe_image_accordion_container_padding',
				[
					'label' => __( 'Padding', 'pafe' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors' => [
						'{{WRAPPER}} .pafe-image-accordion__item-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
				$this->add_responsive_control(
				'pafe_image_accordion_container_border_radius',
				[
					'label' => __( 'Border Radius', 'pafe' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors' => [
						'{{WRAPPER}} .pafe-image-accordion' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_responsive_control(
				'pafe_image_accordion_container_active',
				[
					'label' => __( 'Active Width', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 10,
							'step' => 1,
						], 
					], 
					'default' => [
						'size' => 10,
					],
					'selectors' => [
						'{{WRAPPER}} .pafe-image-accordion__item.active' => 'flex-grow: {{SIZE}};', 
					],
				]
			);
			$this->add_responsive_control(
				'pafe_image_accordion_containe_item_width',
				[
					'label' => __( 'Item Width', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 10,
							'step' => 1,
						], 
					], 
					'default' => [
						'size' => 3,
					],
					'selectors' => [
						'{{WRAPPER}} .pafe-image-accordion__item' => 'flex-grow: {{SIZE}};', 
					],
				]
			);
			$this->add_group_control(
				\Elementor\Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'pafe_image_accordion_container_box_shadow',
					'label' => __( 'Box Shadow', 'pafe' ),
					'selector' => '{{WRAPPER}} .pafe-image-accordion__item',
				]
			);
			$this->end_controls_section();
			$this->start_controls_section(
				'pafe_image_accordion_background_section',
				[
					'label' => __( 'Background', 'pafe' ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				]
			);
			$this->add_control(
				'pafe_image_accordion_background_type',
				[
					'label' => __( 'Type', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => 'classic',
					'options' => [
						'classic'  => __( 'Classic','pafe' ),
						'gradient' => __( 'Gradient','pafe' ),
					],
				]
			);
			$this->add_control( 
				'pafe_image_accordion_background_overlay',
				[
					'label' => __( 'Background', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'scheme' => [
						'type' => \Elementor\Core\Schemes\Color::get_type(), 
						'value' => \Elementor\Core\Schemes\Color::COLOR_1,
					],
					'default' => 'rgba(242,242,242,0.29)',  
					'selectors' => [
						'{{WRAPPER}} .pafe-image-accordion__item::after' => 'background-color: {{VALUE}}',       
					],
					'condition' => [
						'pafe_image_accordion_background_type' => 'classic'	
					]
				]
			);
			$this->add_control(
				'pafe_image_accordion_background_gradient_color',
				[
					'label' => _x( 'Color', 'Background Control', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'default' => 'rgba(242,242,242,0.57)',
					'title' => _x( 'Background Color', 'Background Control', 'pafe' ),
					'selectors' => [
						'{{WRAPPER}} .pafe-image-accordion__item::after' => 'background-color: {{VALUE}};',
					],
					'condition' => [
						'pafe_image_accordion_background_type' => 'gradient'	
					]
				]
			);
			$this->add_control(
				'pafe_image_accordion_background_gradient_color_stop',
				[
					'label' => _x( 'Location', 'Background Control', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ '%' ],
					'default' => [
						'unit' => '%',
						'size' => 0,
					],
					'render_type' => 'ui',
					'condition' => [
						'pafe_image_accordion_background_type' => 'gradient'	
					]
				]
			);
			$this->add_control(
				'pafe_image_accordion_background_gradient_color_b',
				[
					'label' => _x( 'Second Color', 'Background Control', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'default' => 'rgba(10,87,117,0.58)',
					'render_type' => 'ui',
					'condition' => [
						'pafe_image_accordion_background_type' => 'gradient'	
					]
				]
			);
			$this->add_control(
				'pafe_image_accordion_background_gradient_color_b_stop',
				[
					'label' => _x( 'Location', 'Background Control', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ '%' ],
					'default' => [
						'unit' => '%',
						'size' => 100,
					],
					'render_type' => 'ui',
					'condition' => [
						'pafe_image_accordion_background_type' => 'gradient'	
					]
				]
			);
			$this->add_control(
				'pafe_image_accordion_background_gradient_gradient_type',
				[
					'label' => _x( 'Type', 'Background Control', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'options' => [
						'linear' => _x( 'Linear', 'Background Control', 'pafe' ),
						'radial' => _x( 'Radial', 'Background Control', 'pafe' ),
					],
					'default' => 'linear',
					'render_type' => 'ui',
					'condition' => [
						'pafe_image_accordion_background_type' => 'gradient'	
					]
				]
			);
			$this->add_control(
				'pafe_image_accordion_background_gradient_angle',
				[
					'label' => _x( 'Angle', 'Background Control', 'pafe' ),
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
						'{{WRAPPER}} .pafe-image-accordion__item:after' => 'background-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{pafe_image_accordion_background_gradient_color.VALUE}} {{pafe_image_accordion_background_gradient_color_stop.SIZE}}{{pafe_image_accordion_background_gradient_color_stop.UNIT}}, {{pafe_image_accordion_background_gradient_color_b.VALUE}} {{pafe_image_accordion_background_gradient_color_b_stop.SIZE}}{{pafe_image_accordion_background_gradient_color_b_stop.UNIT}});',
					],
					'condition' => [
						'pafe_image_accordion_background_type' => 'gradient'	
					]
				]
			);
			$this->add_control(
				'pafe_image_accordion_background_gradient_position',
				[
					'label' => _x( 'Position', 'Background Control', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'options' => [
						'center center' => _x( 'Center Center', 'Background Control', 'pafe' ),
						'center left' => _x( 'Center Left', 'Background Control', 'pafe' ),
						'center right' => _x( 'Center Right', 'Background Control', 'pafe' ),
						'top center' => _x( 'Top Center', 'Background Control', 'pafe' ),
						'top left' => _x( 'Top Left', 'Background Control', 'pafe' ),
						'top right' => _x( 'Top Right', 'Background Control', 'elementor' ),
						'bottom center' => _x( 'Bottom Center', 'Background Control', 'pafe' ),
						'bottom left' => _x( 'Bottom Left', 'Background Control', 'pafe' ),
						'bottom right' => _x( 'Bottom Right', 'Background Control', 'pafe' ),
					],
					'default' => 'center center',
					'selectors' => [
						'{{WRAPPER}}.pafe-image-accordion__item:after' => 'background-color: transparent; background-image: radial-gradient(at {{VALUE}}, {{pafe_image_accordion_background_gradient_color.VALUE}} {{pafe_image_accordion_background_gradient_color_stop.SIZE}}{{pafe_image_accordion_background_gradient_color_stop.UNIT}}, {{pafe_image_accordion_background_gradient_color_b.VALUE}} {{pafe_image_accordion_background_gradient_color_b_stop.SIZE}}{{pafe_image_accordion_background_gradient_color_b_stop.UNIT}});',
					],
					'condition' => [
						'pafe_image_accordion_background_type' => 'gradient'	
					]
				]
			);
			$this->end_controls_section();
			$this->start_controls_section(
				'pafe_image_accordion_title_style_section',
				[
					'label' => __( 'Title', 'pafe' ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				]
			);
			$this->add_control(
				'pafe_image_accordion_title_color', 
				[
					'label' => __( 'Color', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'scheme' => [
						'type' => \Elementor\Core\Schemes\Color::get_type(),
						'value' => \Elementor\Core\Schemes\Color::COLOR_1,
					],
					'default' => '#fff', 
					'selectors' => [
						'{{WRAPPER}} .pafe-image-accordion__item-content__title-inner' => 'color: {{VALUE}}',
					],
				]
			); 
			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'title_typography',
					'selector' => '{{WRAPPER}} .pafe-image-accordion__item-content__title-inner',
					'scheme' => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_2,
				]
			);
			
			$this->add_responsive_control(
				'pafe_image_accordion_title_margin',
				[
					'label' => __( 'Space Between', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 100,
							'step' => 1,
						], 
					], 
					'default' => [
						'unit' => 'px',
						'size' => 15,
					],
					'selectors' => [
						'{{WRAPPER}} .pafe-image-accordion__item-content__title-inner' => 'margin-bottom: {{SIZE}}{{UNIT}};', 
					],
				]
			);
			$this->end_controls_section();
			$this->start_controls_section(
				'pafe_image_accordion_content_style_section',
				[
					'label' => __( 'Content', 'pafe' ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				] 
			);
			$this->add_control(
				'pafe_image_accordion_content_color', 
				[
					'label' => __( 'Color', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'scheme' => [
						'type' => \Elementor\Core\Schemes\Color::get_type(),
						'value' => \Elementor\Core\Schemes\Color::COLOR_1,
					],
					'default' => '#fff', 
					'selectors' => [
						'{{WRAPPER}} .pafe-image-accordion__item-content__text' => 'color: {{VALUE}}',
					],
				]
			); 

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'content_typography',
					'selector' => '{{WRAPPER}} .pafe-image-accordion__item-content__text',
					'scheme' => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_2,
				]
			);
			$this->add_responsive_control(
				'pafe_image_accordion_content_margin',
				[
					'label' => __( 'Space Between', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 400,
							'step' => 1,
						], 
					], 
					'default' => [
						'unit' => 'px',
						'size' => 15,
					],
					'selectors' => [
						'{{WRAPPER}} .pafe-image-accordion__item-content__text' => 'margin-bottom: {{SIZE}}{{UNIT}};', 
					],
				]
			);
			$this->end_controls_section();
			$this->start_controls_section(                    
				'pafe_image_accordion_button_style_section',
				[
					'label' => __( 'Button', 'pafe' ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				]
			);
			$this->start_controls_tabs(
				'pafe_image_accordion_button_tabs'
			);

			$this->start_controls_tab( 
				'pafe_image_accordion_button_normal_tabs',
				[
					'label' => __( 'Normal', 'pafe' ),
				]
			);		
			
			$this->add_control(
				'pafe_image_accordion_button_text_color',
				[
					'label' => __( 'Color', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'scheme' => [
						'type' => \Elementor\Core\Schemes\Color::get_type(), 
						'value' => \Elementor\Core\Schemes\Color::COLOR_1,
					],
					'default' => '#fff', 
					'selectors' => [
						'{{WRAPPER}} .pafe-image-accordion__item-content__link' => 'color: {{VALUE}}',       
					], 
				]
			);
			$this->add_control( 
				'pafe_image_accordion_button_background_color',
				[
					'label' => __( 'Background', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'scheme' => [
						'type' => \Elementor\Core\Schemes\Color::get_type(), 
						'value' => \Elementor\Core\Schemes\Color::COLOR_1,
					],
					'default' => '#3CCD94',  
					'selectors' => [
						'{{WRAPPER}} .pafe-image-accordion__item-content__link' => 'background-color: {{VALUE}}',       
					],
				]
			);
			$this->end_controls_tab();
			$this->start_controls_tab( 
				'pafe_image_accordion_button_hover_tabs',
				[
					'label' => __( 'Hover', 'pafe' ),
				]
			);
			$this->add_control(
				'pafe_image_accordion_button_text_hover_color',
				[
					'label' => __( 'Color', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'scheme' => [
						'type' => \Elementor\Core\Schemes\Color::get_type(), 
						'value' => \Elementor\Core\Schemes\Color::COLOR_1,
					],
					'default' => '#fff', 
					'selectors' => [
						'{{WRAPPER}} .pafe-image-accordion__item-content__link:hover' => 'color: {{VALUE}}',       
					], 
				]
			);
			$this->add_control( 
				'pafe_image_accordion_button_background_hover_color',
				[
					'label' => __( 'Background', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'scheme' => [
						'type' => \Elementor\Core\Schemes\Color::get_type(), 
						'value' => \Elementor\Core\Schemes\Color::COLOR_1,
					],
					'default' => '#3CCD94',  
					'selectors' => [
						'{{WRAPPER}} .pafe-image-accordion__item-content__link:hover' => 'background-color: {{VALUE}}',       
					],
				]
			);
			$this->end_controls_tab();
			$this->end_controls_tabs();
			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'readmore_typography',
					'selector' => '{{WRAPPER}} .pafe-image-accordion__item-content__link',
					'scheme' => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_2,
				]
			);
			$this->add_responsive_control(
				'pafe_image_accordion_button_padding',
				[
					'label' => __( 'Button Padding', 'pafe' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors' => [
						'{{WRAPPER}} .pafe-image-accordion__item-content__link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_responsive_control(
				'pafe_image_accordion_button_border_radius',
				[
					'label' => __( 'Border Radius', 'pafe' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors' => [
						'{{WRAPPER}} .pafe-image-accordion__item-content__link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				] 
			);
			$this->end_controls_section();
			$this->start_controls_section(                    
				'pafe_image_accordion_trigger_section',
				[
					'label' => __( 'Trigger', 'pafe' ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				]
			);
			$this->add_control(
				'pafe_image_accordion_trigger_type',
				[
					'label' => __( 'Type', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => 'click',
					'options' => [
						'click'  => __( 'Click','pafe' ),
						'hover' => __( 'Hover','pafe' ),
					],
				]
			);
			$this->end_controls_section();
	}
	protected function render() {
		$settings = $this->get_settings_for_display();
		?>
			<div class="pafe-image-accordion" data-pafe-image-accordion-list data-pafe-image-accordion-option='<?php echo $settings['pafe_image_accordion_trigger_type']; ?>'>
				<?php 
					$index = 0;
					foreach ( $settings['pafe_image_accordion'] as $item) :						
						$index ++; 
				?>					
					<div class="pafe-image-accordion__item<?php if($index == 1){echo " active";}?>" data-pafe-image-accordion-item >	
						<?php if (!empty($item['pafe_image_accordion_item_image'])) : ?>
							<img src="<?php echo $item['pafe_image_accordion_item_image']['url']; ?>" alt="">
						<?php endif; ?>							
						<div class="pafe-image-accordion__item-content" data-pafe-image-accordion-item-content>
							<?php if (!empty($item['pafe_image_accordion_item_title'])) : ?> 
							<div class="pafe-image-accordion__item-content__title">
								<h2 class="pafe-image-accordion__item-content__title-inner"><?php echo $item['pafe_image_accordion_item_title']; ?>
							</div>
							<?php endif; ?>								
							<div class="pafe-image-accordion__item-content__text">
								<?php if (!empty($item['pafe_image_accordion_item_wysiwyg'])) : ?>
									<?php echo $item['pafe_image_accordion_item_wysiwyg'];?>
								<?php endif; ?>
								<?php if (!empty($item['pafe_image_accordion_item_link']) && !empty($item['pafe_image_accordion_item_button_text'])) : ?>		
								<a class="pafe-image-accordion__item-content__link" href="<?php echo $item['pafe_image_accordion_item_link']; ?>"><?php echo $item['pafe_image_accordion_item_button_text']; ?></a>
								<?php endif; ?>	
							</div>								
						</div>							
					</div>				
				<?php endforeach;?>	
			</div> 
		<?php
	}	
}		
