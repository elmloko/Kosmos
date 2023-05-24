<?php
	class PAFE_Countdown_Cart extends \Elementor\Widget_Base {
		public function get_name() {
			return 'pafe-countdown-cart';
		}

		public function get_title() {
			return __( 'PAFE Countdown Cart', 'pafe' );
		}
 
		public function get_icon() {  
			return 'eicon-countdown';
		} 

		public function get_categories() {
			return [ 'pafe-free-widgets' ];
		}

		public function get_keywords() {
			return [ 'countdown','cart' ];
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
				'pafe_countdown_cart_section',
				[
					'label' => __( 'Setting', 'pafe' ),
					'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
				]
			);

			$this->add_control(
				'pafe_countdown_cart_label_before',
				[
					'label' => __( 'Label Before', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					
				]
			);

			$this->add_control(
				'pafe_countdown_cart_label_after',
				[
					'label' => __( 'Label After', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					
				]
			);

			$this->add_control(
				'pafe_countdown_cart_id',
				[
					'label' => __( 'Product ID', 'pafe' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'default' => '',
				] 
			);

			$this->end_controls_section(); 

			$this->start_controls_section(
				'pafe_countdown_cart_style',
				[
					'label' => __( 'General', 'pafe' ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				]
			);

			$this->add_responsive_control(
				'pafe_countdown_cart_spacing',
				[
					'label' => __( 'Spacing', 'pafe' ),
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
						'size' => 10,
					],
					'selectors' => [
						'{{WRAPPER}} .pafe-countdown-stock__text' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'pafe_countdown_cart_padding',
				[
					'label' => __( 'Padding', 'pafe' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors' => [
						'{{WRAPPER}} .pafe-countdown-stock' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				] 
			);

			$this->add_responsive_control(
				'pafe_countdown_cart_margin', 
				[
					'label' => __( 'Margin', 'pafe' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors' => [
						'{{WRAPPER}} .pafe-countdown-stock' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				] 
			);

			$this->add_responsive_control(
	   			'pafe_countdown_cart_text_align',
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
						'{{WRAPPER}} .pafe-countdown-stock__text' => 'text-align: {{VALUE}};',
	 				],
	   			]
   			);

			$this->add_control(
				'pafe_countdown_cart_hide_availabitity',
				[
					'label' => __( 'Hide Availability Elementor', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label_on' => 'Yes',
					'label_off' => 'No',
					'default' => 'yes',
					'return_value' => 'yes',
					'selectors' => [
						'.stock' => 'display: none',
					],
				] 
			);

			$this->end_controls_section(); 

			$this->start_controls_section(
				'pafe_countdown_cart_label_before_style',
				[
					'label' => __( 'Label Before', 'pafe' ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				]
			);

			$this->add_control(
				'pafe_countdown_cart_label_before_color', 
				[  
					'label' => __( 'Color', 'pafe' ), 
					'type' => \Elementor\Controls_Manager::COLOR,
					'scheme' => [
						'type' => \Elementor\Core\Schemes\Color::get_type(),
						'value' => \Elementor\Core\Schemes\Color::COLOR_1,
					],
					'default' => '#000',  
					'selectors' => [
						'{{WRAPPER}} .pafe-countdown-stock__label-before' => 'color: {{VALUE}}',
					],
				]
			);	

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'label_before',
					'selector' => '{{WRAPPER}} .pafe-countdown-stock__label-before',
					'scheme' => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				]
			);

			$this->end_controls_section(); 

			$this->start_controls_section(
				'pafe_countdown_cart_label_after_style',
				[
					'label' => __( 'Label After', 'pafe' ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				]
			);

			$this->add_control(
				'pafe_countdown_cart_label_after_color', 
				[ 
					'label' => __( 'Color', 'pafe' ), 
					'type' => \Elementor\Controls_Manager::COLOR,
					'scheme' => [
						'type' => \Elementor\Core\Schemes\Color::get_type(),
						'value' => \Elementor\Core\Schemes\Color::COLOR_1,
					],
					'default' => '#000',  
					'selectors' => [
						'{{WRAPPER}} .pafe-countdown-stock__label-after' => 'color: {{VALUE}}',
					],
				]
			);	

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'label_after',
					'selector' => '{{WRAPPER}} .pafe-countdown-stock__label-after',
					'scheme' => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				]
			);

			$this->end_controls_section(); 

			$this->start_controls_section(
				'pafe_countdown_cart_number_style',
				[
					'label' => __( 'Number', 'pafe' ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				]
			);

			$this->add_control(
				'pafe_countdown_cart_number_color', 
				[  
					'label' => __( 'Color', 'pafe' ), 
					'type' => \Elementor\Controls_Manager::COLOR,
					'scheme' => [
						'type' => \Elementor\Core\Schemes\Color::get_type(),
						'value' => \Elementor\Core\Schemes\Color::COLOR_1,
					],
					'default' => '#000',  
					'selectors' => [
						'{{WRAPPER}} .pafe-countdown-stock__number' => 'color: {{VALUE}}',
					],
				]
			);	

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'number',
					'selector' => '{{WRAPPER}} .pafe-countdown-stock__number',
					'scheme' => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				]
			);

			$this->end_controls_section(); 	

			$this->start_controls_section(

				'pafe_countdown_cart_bar_style_section',
				[
					'label' => __( 'Progress Bar', 'pafe' ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				]
			);
						
			$this->add_responsive_control(
				'pafe_countdown_cart_bar_height',
				[
					'label' => __( 'Height', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 50, 
							'step' => 1,
						],
					],
					'default' => [
						'unit' => 'px',
						'size' => 5,
					],
					'selectors' => [
						'{{WRAPPER}} .pafe-countdown-progressbar' => 'height: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'pafe_countdown_cart_bar_border_radius',
				[
					'label' => __( 'Border Radius', 'pafe' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors' => [
						'{{WRAPPER}} .pafe-countdown-progressbar' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						'{{WRAPPER}} .pafe-countdown-progressbar__inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				] 
			);

			$this->add_responsive_control(
				'pafe_countdown_cart_bar_max_width',
				[
					'label' => __( 'Max Width', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 2000, 
							'step' => 1,
						],
					],
					'default' => [
						'unit' => 'px',
						'size' => 300,
					],
					'selectors' => [
						'{{WRAPPER}} .pafe-countdown-progressbar' => 'max-width: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .pafe-countdown-stock__text' => 'max-width: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->start_controls_tabs(
				'label_tabs'
			);

			$this->start_controls_tab( 
				'background_total_tabs',
				[
					'label' => __( 'Background Total', 'pafe' ),
				]
			);

			$this->add_control(
				'pafe_countdown_cart_bar_background_color', 
				[ 
					'label' => __( 'Background', 'pafe' ), 
					'type' => \Elementor\Controls_Manager::COLOR,
					'scheme' => [
						'type' => \Elementor\Core\Schemes\Color::get_type(),
						'value' => \Elementor\Core\Schemes\Color::COLOR_1,
					],
					'default' => '#ededed',  
					'selectors' => [
						'{{WRAPPER}} .pafe-countdown-progressbar' => 'background-color: {{VALUE}}',
					],
				]
			);	

			$this->end_controls_tab();

			$this->start_controls_tab( 
				'background_stock_tabs',
				[
					'label' => __( 'Background Stock', 'pafe' ),
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Background::get_type(),
				[ 
					'name' => 'background_bar',
					'label' => __( 'Background', 'pafe' ),
					'types' => [ 'classic', 'gradient' ],
					'selector' => '{{WRAPPER}} .pafe-countdown-progressbar__inner',
				]
			);

			$this->end_controls_tab();

			$this->end_controls_tabs();
			
			$this->end_controls_section(); 
		}	
		protected function render() { 

			$settings = $this->get_settings_for_display();
global $product;
			if (!empty($settings['pafe_countdown_cart_id'])) {
				$product_id = $settings['pafe_countdown_cart_id'];
				$product = wc_get_product($product_id);

			} else {
				$product = wc_get_product();
			}

            if (!empty($product)) {
                $units_sold = $product->get_total_sales();
                $units_quantity = $product->get_stock_quantity();
            }
   			$stock_percent = $units_quantity/($units_sold+$units_quantity)*100;

			?>	
			<div class="pafe-countdown-stock">
					<div class="pafe-countdown-stock__text">
						<?php if (!empty($settings['pafe_countdown_cart_label_before']) ) : ?>
							<span class="pafe-countdown-stock__label-before"> <?php echo $settings['pafe_countdown_cart_label_before']; ?>	</span>
						<?php endif; ?>

						<span class="pafe-countdown-stock__number"><?php if (!empty($product)){ echo $product->get_stock_quantity(); }?></span>
						<?php if (!empty($settings['pafe_countdown_cart_label_after']) ) : ?>
							<span class="pafe-countdown-stock__label-after"> <?php echo $settings['pafe_countdown_cart_label_after']; ?></span>
						<?php endif; ?>
					</div>
					<div class="pafe-countdown-progressbar">
						<div class="pafe-countdown-progressbar__inner" style="width: <?php echo $stock_percent . 'px'; ?>"></div>
					</div>
				</div>
			<?php
		}	
	}	  
