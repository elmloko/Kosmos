<?php
	class PAFE_Sales_Pop extends \Elementor\Widget_Base {
		public function get_name() {
			return 'pafe-sales-pop';
		}

		public function get_title() {
			return __( 'PAFE Sales Pop', 'pafe' );
		}
 
		public function get_icon() {  
			return 'eicon-testimonial';
		} 

		public function get_categories() {
			return [ 'pafe-free-widgets' ];
		}

		public function get_keywords() {
			return [ 'pop','sales' ];
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
				'pafe_sales_pop_section',
				[
					'label' => __( 'Settings', 'pafe' ),
				]
			);
			$this->add_control(
				'pafe_sales_pop_time',
				[
					'label' => __( 'Time (s)', 'pafe' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'default' => '2',
				]
			); 
			$repeater = new \Elementor\Repeater();
			$repeater->add_control(
				'pafe_sales_pop_image', 
				[
					'label' => __( 'Choose Image', 'pafe' ),
					'type' => \Elementor\Controls_Manager::MEDIA,
				]
			); 
			$repeater->add_control(
				'pafe_sales_pop_item_title',
				[
					'label' => __( 'Title', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'dynamic' => [
						'active' => true,
					],
				]
			); 
			$repeater->add_control(
				'pafe_sales_pop_item_time',
				[
					'label' => __( 'Time', 'pafe' ),
					'description' => __( 'E.g: 10 hours ago', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'dynamic' => [
						'active' => true,
					],
				]
			); 
			$repeater->add_control(
				'pafe_sales_pop_item_wysiwyg',
				[ 
					'label' => __( 'Description','pafe' ),
					'type' => \Elementor\Controls_Manager::WYSIWYG,
				]
			);
			$this->add_control(
				'pafe_sales_pop_repeater',
				[
					'type' => \Elementor\Controls_Manager::REPEATER,
					'show_label' => true,
					'fields' => $repeater->get_controls(),
					'title_field' => __('{{{pafe_sales_pop_item_title}}}'),				
				]
			); 
			$this->end_controls_section();
			$this->start_controls_section(
				'pafe_sales_pop_random_section',
				[
					'label' => __( 'Random', 'pafe' ),
				]
			);
			$this->add_control(
				'pafe_sales_pop_random',
				[
					'label' => __( 'Enable Random', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'default' => 'label_on',
					'label_on' => 'Yes',
					'label_off' => 'No',
					'return_value' => 'yes',
				] 
			);
			$this->add_control(
				'pafe_sales_pop_min_time',
				[
					'label' => __( 'Min Time (s)', 'pafe' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'default' => '1',
					'condition' => [
						'pafe_sales_pop_random' => 'yes',
					],
				]
			); 
			$this->add_control(
				'pafe_sales_pop_max_time',
				[
					'label' => __( 'Max Time (s)', 'pafe' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'default' => '5',
					'condition' => [
						'pafe_sales_pop_random' => 'yes',
					],
				] 
			);
			$this->end_controls_section();
			$this->start_controls_section(
				'pafe_sales_pop_section_style_section',
				[
					'label' => __( 'Item', 'pafe' ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				]
			);
			$this->add_group_control(
	            \Elementor\Group_Control_Background::get_type(),
	            [
	                'name' => 'pafe_sales_pop_background',
	                'selector' => '{{WRAPPER}} .pafe-sales-pop-item',
	                'label' => __( 'Background', 'pafe' ),
	            ]
	        );
	        $this->add_control(
				'pafe_sales_pop_space',
				[
					'label' => __( 'Spacing', 'pafe' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'default' => '15',
					'selectors' => [
						'{{WRAPPER}} .pafe-sales-pop-item__title' => 'margin-bottom: {{VALUE}}px',
					],
				]
			);
			$this->add_responsive_control(
				'pafe_sales_pop_item_width',
				[
					'label' => __( 'Width', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px','%' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 500,
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
						'size' => 365,
					],
					'selectors' => [
						'{{WRAPPER}} .pafe-sales-pop-item' => 'width: {{SIZE}}{{UNIT}};',
					],
				]
			);
			$this->add_responsive_control(
				'pafe_sales_pop_border_radius',
				[
					'label' => __( 'Border Radius', 'pafe' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors' => [
						'{{WRAPPER}} .pafe-sales-pop-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				] 
			);
			$this->add_responsive_control( 
				'pafe_sales_pop_padding',
				[
					'label' => __( 'Padding', 'pafe' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors' => [
						'{{WRAPPER}} .pafe-sales-pop-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				[
					'name' => 'pafe_sales_pop_border',
					'label' => __( 'Border', 'pafe' ),
					'selector' => '{{WRAPPER}} .pafe-sales-pop-item',
				]
			);
			$this->add_group_control(
				\Elementor\Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'pafe_sales_pop_box_shadow',
					'label' => __( 'Box Shadow', 'pafe' ),
					'selector' => '{{WRAPPER}} .pafe-sales-pop-item',
				]
			);
			$this->add_control(
				'pafe_sales_pop_zindex',
				[
					'label' => __( 'Z-index', 'pafe' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'default' => '99',
					'selectors' => [
						'{{WRAPPER}} .pafe-sales-pop' => 'z-index: {{VALUE}}',
					],
				] 
			);

			$this->end_controls_section();
			$this->start_controls_section(
				'pafe_sales_pop_position_section',
				[
					'label' => __( 'Position', 'pafe' ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				]
			);
			$this->add_control(
				'pafe_sales_pop_position_top',
				[
					'label' => __( 'Top', 'pafe' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} .pafe-sales-pop' => 'top: {{VALUE}}px',
					],
				]
			);
			$this->add_control(
				'pafe_sales_pop_position_bottom',
				[
					'label' => __( 'Bottom', 'pafe' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'default' => '15',
					'selectors' => [
						'{{WRAPPER}} .pafe-sales-pop' => 'bottom: {{VALUE}}px',
					],
				]
			);
			$this->add_control(
				'pafe_sales_pop_position_left',
				[
					'label' => __( 'Left', 'pafe' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'default' => '15',
					'selectors' => [
						'{{WRAPPER}} .pafe-sales-pop' => 'left: {{VALUE}}px',
					],
				]
			);
			$this->add_control(
				'pafe_sales_pop_position_right',
				[
					'label' => __( 'Right', 'pafe' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} .pafe-sales-pop' => 'right: {{VALUE}}px',
					],
				]
			);
			$this->end_controls_section();
			$this->start_controls_section(
				'pafe_sales_pop_time_style',
				[
					'label' => __( 'Time', 'pafe' ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				]
			);
			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'sales_pop_time_typography',
					'selector' => '{{WRAPPER}} .pafe-sales-pop-item__time',
					'scheme' => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				]
			); 
			$this->add_control(
				'pafe_sales_pop_time_color', 
				[ 
					'label' => __( 'Color', 'pafe' ), 
					'type' => \Elementor\Controls_Manager::COLOR, 
					'scheme' => [
						'type' => \Elementor\Core\Schemes\Color::get_type(),
						'value' => \Elementor\Core\Schemes\Color::COLOR_1,
					],
					'default' => '#000',  
					'selectors' => [
						'{{WRAPPER}} .pafe-sales-pop-item__time' => 'color: {{VALUE}}',
					],
				]
			);

			$this->end_controls_section();
			$this->start_controls_section(
				'pafe_sales_pop_title_style_section',
				[
					'label' => __( 'Title', 'pafe' ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				]
			);
			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'sales_pop_title_typography',
					'selector' => '{{WRAPPER}} .pafe-sales-pop-item__title',
					'scheme' => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				]
			); 
			$this->add_control(
				'pafe_sales_pop_color', 
				[ 
					'label' => __( 'Color', 'pafe' ), 
					'type' => \Elementor\Controls_Manager::COLOR, 
					'scheme' => [
						'type' => \Elementor\Core\Schemes\Color::get_type(),
						'value' => \Elementor\Core\Schemes\Color::COLOR_1,
					],
					'default' => '#000',  
					'selectors' => [
						'{{WRAPPER}} .pafe-sales-pop-item__title' => 'color: {{VALUE}}',
					],
				]
			);	
			$this->end_controls_section();
			$this->start_controls_section(
				'pafe_sales_pop_description_style_section',
				[
					'label' => __( 'Description', 'pafe' ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'sales_pop_description_typography',
					'selector' => '{{WRAPPER}} .pafe-sales-pop-item__description',
					'scheme' => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				]
			); 
			$this->add_control(
				'pafe_sales_pop_description_color', 
				[ 
					'label' => __( 'Color', 'pafe' ), 
					'type' => \Elementor\Controls_Manager::COLOR, 
					'scheme' => [
						'type' => \Elementor\Core\Schemes\Color::get_type(),
						'value' => \Elementor\Core\Schemes\Color::COLOR_1,
					],
					'default' => '#000',  
					'selectors' => [
						'{{WRAPPER}} .pafe-sales-pop-item__description' => 'color: {{VALUE}}',
					],
				]
			);	

			$this->end_controls_section();
			$this->start_controls_section(
				'pafe_sales_pop_close_button_style_section',
				[
					'label' => __( 'Close Button', 'pafe' ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				]
			);
			$this->add_control(
				'pafe_sales_pop_close_button_color', 
				[ 
					'label' => __( 'Color', 'pafe' ), 
					'type' => \Elementor\Controls_Manager::COLOR, 
					'scheme' => [
						'type' => \Elementor\Core\Schemes\Color::get_type(),
						'value' => \Elementor\Core\Schemes\Color::COLOR_1,
					],
					'default' => '#55586c',  
					'selectors' => [
						'{{WRAPPER}} .pafe-sales-pop__close' => 'color: {{VALUE}}',
					],
				]
			);
			$this->add_control(
				'pafe_sales_pop_close_button_size',
				[
					'label' => __( 'Size', 'pafe' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'default' => '15',
					'selectors' => [
						'{{WRAPPER}} .pafe-sales-pop__close' => 'font-size: {{VALUE}}px',
					],
				]
			);

			$this->add_control(
				'pafe_sales_pop_close_button_position_top',
				[
					'label' => __( 'Top', 'pafe' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'default' => '5',
					'selectors' => [
						'{{WRAPPER}} .pafe-sales-pop__close' => 'top: {{VALUE}}px',
					],
				]
			);
			$this->add_control(
				'pafe_sales_pop_close_button_position_bottom',
				[
					'label' => __( 'Bottom', 'pafe' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} .pafe-sales-pop__close' => 'bottom: {{VALUE}}px',
					],
				]
			);
			$this->add_control(
				'pafe_sales_pop_close_button_position_left',
				[
					'label' => __( 'Left', 'pafe' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} .pafe-sales-pop__close' => 'left: {{VALUE}}px',
					],
				]
			);
			$this->add_control( 
				'pafe_sales_pop_close_button_position_right',
				[
					'label' => __( 'Right', 'pafe' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'default' => '7',
					'selectors' => [
						'{{WRAPPER}} .pafe-sales-pop__close' => 'right: {{VALUE}}px',
					],
				]
			);

			$this->end_controls_section();
			$this->start_controls_section(
				'pafe_sales_pop_image_style_section',
				[
					'label' => __( 'Image', 'pafe' ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				]
			);
			$this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				[
					'name' => 'pafe_sales_pop_image_border',
					'label' => __( 'Border', 'pafe' ),
					'selector' => '{{WRAPPER}} .pafe-sales-pop-item__image',
				]
			);
			$this->add_responsive_control(
				'pafe_sales_pop_image_border_radius',
				[
					'label' => __( 'Image Border Radius', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 200,
							'step' => 1,
						],
					],
					'default' => [
						'unit' => 'px',
						'size' => 3,
					],
					'selectors' => [
						'{{WRAPPER}} .pafe-sales-pop-item__img>img' => 'border-radius: {{SIZE}}{{UNIT}};',
					],
				]
			);
			$this->end_controls_section();
		}	
		protected function render() {
		$settings = $this->get_settings_for_display(); 
		if ($settings['pafe_sales_pop_random']) {
			$option = [
				'times'=> $settings['pafe_sales_pop_time'],
				'min_time'=> $settings['pafe_sales_pop_min_time'], 
				'max_time'=> $settings['pafe_sales_pop_max_time'],
				'random' => $settings['pafe_sales_pop_random'],
			];
		}
		?>
		<div class="pafe-sales-pop">
			<div class="pafe-sales-pop-list" data-pafe-sales-pop-list data-pafe-sales-pop-option='<?php echo json_encode( $option ); ?>'>
				
				<?php 
					$index = 0; 
					foreach ( $settings['pafe_sales_pop_repeater'] as $item) :		
						$index ++; 
				?>
					<div class="pafe-sales-pop-item" data-pafe-sales-pop-item="<?php echo $index; ?>">
						<div class="pafe-sales-pop__close" data-pafe-sales-pop-close><i class="fa fa-times" aria-hidden="true"></i></div>
						<div class="pafe-sales-pop-item__inner">
						<?php if (!empty($item['pafe_sales_pop_image']['url'])) : ?>
							<div class="pafe-sales-pop-item__img" ><img src="<?php echo $item['pafe_sales_pop_image']['url'];?>" alt="">
							</div>   
						<?php endif; ?>
							<div class="pafe-sales-pop-item_content">
							<?php if (!empty($item['pafe_sales_pop_item_title'])) : ?> 
								<div class="pafe-sales-pop-item__title">
									<?php echo $item['pafe_sales_pop_item_title']; ?>  
								</div>
							<?php endif; ?>
							<?php if (!empty($item['pafe_sales_pop_item_wysiwyg'])) : ?> 
								<div class="pafe-sales-pop-item__description">
									<?php echo $item['pafe_sales_pop_item_wysiwyg']; ?>
								</div> 
							<?php endif; ?>
							<?php if (!empty($item['pafe_sales_pop_item_time'])) : ?> 
								<div class="pafe-sales-pop-item__time">
									<?php echo $item['pafe_sales_pop_item_time']; ?>
								</div>  
							<?php endif; ?>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php 
		}
	}	
