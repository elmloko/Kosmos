<?php
	class PAFE_Vertical_Timeline extends \Elementor\Widget_Base {

	public function get_name() {
		return 'pafe-vertical-timeline';
	}

	public function get_title() {
		return __( 'PAFE Vertical Timeline', 'pafe' );
	}

	public function get_icon() {
		return 'eicon-bullet-list';
	}

	public function get_categories() {
		return [ 'pafe-free-widgets' ];
	}

	public function get_keywords() {
		return [ 'vertical', 'timeline' ];
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
			'pafe_vertical_timeline_custome_content_section',
			[
				'label' => __( 'Content', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,

			]
		); 
		$repeater = new \Elementor\Repeater(); 
		$repeater->add_control(
			'pafe_vertical_timeline_title', [
				'label' => __( 'Title', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Title' , 'pafe' ),
				'label_block' => true,
			]
		); 
		$repeater->add_control(
			'pafe_vertical_timeline_button_link', [
				'label' => __( 'Button Link', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,

			]
		);  
		$repeater->add_control(
			'pafe_vertical_timeline_button_text', [
				'label' => __( 'Button Text', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default'=> 'Read more',
			]
		); 
		$repeater->add_control(
			'pafe_vertical_timeline_image_or_icon',
			[
				'label' => __( 'Image or Icon', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'icon'  => __( 'Icon','pafe' ),
					'image' => __( 'Image','pafe' ),
				],
			]
		);
		$repeater->add_control(
			'pafe_vertical_timeline_date', [
				'label' => __( 'Date', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
			]
		); 
		$repeater->add_control(
			'pafe_vertical_timeline_image', [
				'label' => __( 'Choose Image', 'pafe' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'condition' => [
					'pafe_vertical_timeline_image_or_icon' => 'image'	
				]
			]
		); 
		$repeater->add_control(
			'pafe_vertical_timeline_icon', [
				'label' => __( 'Social Icons', 'pafe' ),
				'type' => \Elementor\Controls_Manager::ICON,
				'include' => [
					'fa fa-address-book',
					'fa fa-envelope-open',
					'fa fa-check',
					'fa fa-bell',
					'fa fa-cog',
					'fa fa-phone',
					'fa fa-star',
					'fa fa-user',
					'fa fa-car',
					'fa fa-truck',
					'fa fa-university',
					'fa fa-diamond',
					'fa fa-heart',
					'fa fa-gift',
					'fa fa-commenting-o',
					'fa fa-bolt',
					'fa fa-map-marker',	
					'fa fa-picture-o',
				],
				'default' => 'fa fa-address-book',
				'condition' => [
					'pafe_vertical_timeline_image_or_icon' => 'icon'	
				]
			]
		);
		$repeater->add_control(
			'pafe_vertical_timeline_content', [
				'label' => __( 'Content', 'pafe' ),
				'type' => \Elementor\Controls_Manager::WYSIWYG,
				'show_label' => false,
			]
		); 
		$this->add_control(
			'pafe_vertical_timeline',
			[
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(), 
				'title_field' => '{{{ pafe_vertical_timeline_title }}}',
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'pafe_vertical_timeline_list_section',
			[
				'label' => __( 'List', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'pafe_vertical_timeline_spacebetween',
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
					'{{WRAPPER}} .pafe-vertical-timeline__block:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};', 
				],
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'pafe_vertical_timeline_title_style_section',
			[
				'label' => __( 'Title', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .pafe-vertical-timeline-content__title',
				'scheme' =>\Elementor\Core\Schemes\Typography::TYPOGRAPHY_2,

			]
		);
		$this->add_control(
			'pafe_vertical_timeline_title_color', 
			[
				'label' => __( 'Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Core\Schemes\Color::get_type(),
					'value' => \Elementor\Core\Schemes\Color::COLOR_1,
				],
				'default' => '#000', 
				'selectors' => [
					'{{WRAPPER}} .pafe-vertical-timeline-content__title' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'pafe_vertical_timeline_date_section',
			[
				'label' => __( 'Date', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'date_typography',
				'selector' => '{{WRAPPER}} .pafe_vertical_timeline__date',
				'scheme' => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_2,
			]
		);
		$this->add_control(
			'pafe_vertical_timeline_date_color', 
			[
				'label' => __( 'Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Core\Schemes\Color::get_type(),
					'value' => \Elementor\Core\Schemes\Color::COLOR_1,
				],
				'default' => '#000', 
				'selectors' => [
					'{{WRAPPER}} .pafe_vertical_timeline__date' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_responsive_control(
			'pafe_vertical_timeline_date_margin',
			[
				'label' => __( 'Margin', 'pafe' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .pafe_vertical_timeline__date' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'pafe_vertical_timeline_content_style_section',
			[
				'label' => __( 'Content', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'content_typography',
				'selector' => '{{WRAPPER}} .pafe-vertical-timeline-content__content',
				'scheme' => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_2,
			]
		);
		$this->add_control(
			'pafe_vertical_timeline_content_color', 
			[
				'label' => __( 'Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Core\Schemes\Color::get_type(),
					'value' => \Elementor\Core\Schemes\Color::COLOR_1,
				],
				'default' => '#000', 
				'selectors' => [
					'{{WRAPPER}} .pafe-vertical-timeline-content__content' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'pafe_vertical_timeline_content_background_color', 
			[
				'label' => __( 'Background Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Core\Schemes\Color::get_type(),
					'value' => \Elementor\Core\Schemes\Color::COLOR_1,
				],
				'default' => '#F1F2F3', 
				'selectors' => [
					'{{WRAPPER}} .pafe-vertical-timeline-content' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .cd-timeline__block:nth-child(odd)>.cd-timeline__content:before ' => 'border-left-color: {{VALUE}}',
					'{{WRAPPER}} .cd-timeline__block:nth-child(even)>.cd-timeline__content:before' => 'border-right-color: {{VALUE}}',
					'{{WRAPPER}} .cd-timeline__content:before' => 'border-right-color: {{VALUE}}',
				],
			]
		); 
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'pafe_vertical_timeline_content_border',
				'label' => __( 'Border', 'pafe' ),
				'selector' => '{{WRAPPER}} .pafe-vertical-timeline-content',
			]
		);
		$this->add_responsive_control(
			'pafe_vertical_timeline_content_padding',
			[
				'label' => __( 'Padding', 'pafe' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .pafe-vertical-timeline-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'pafe_vertical_timeline_content_border_radius',
			[
				'label' => __( 'Border Radius', 'pafe' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .pafe-vertical-timeline-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'pafe_vertical_timeline_image_style_section',
			[
				'label' => __( 'Image', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'pafe_vertical_timeline_image_width',
			[
				'label' => __( 'Width', 'pafe' ),
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
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .pafe-vertical-timeline__image' => 'width: {{SIZE}}{{UNIT}};', 
				],
			]
		);
		$this->add_control(
			'pafe_vertical_timeline_image_background_color',
			[
				'label' => __( 'Background', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Core\Schemes\Color::get_type(),
					'value' => \Elementor\Core\Schemes\Color::COLOR_1,
				],
				'default' => '#3CCD94', 
				'selectors' => [
					'{{WRAPPER}} .pafe-vertical-timeline__img' => 'background-color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'pafe_vertical_timeline_image_box_shadow',
				'label' => __( 'Input Box Shadow', 'pafe' ),
				'selector' => '{{WRAPPER}} .pafe-vertical-timeline__img',
			]
		);

		$this->end_controls_section();
		$this->start_controls_section(
			'pafe_vertical_timeline_icon_style_section',
			[
				'label' => __( 'Icon', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'pafe_vertical_timeline_icon_width',
			[
				'label' => __( 'Size', 'pafe' ),
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
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .pafe-vertical-timeline__icon>i' => 'font-size: {{SIZE}}{{UNIT}};', 
				],
			]
		);
		$this->add_control(
			'pafe_vertical_timeline_icon_color',
			[
				'label' => __( 'Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Core\Schemes\Color::get_type(),
					'value' => \Elementor\Core\Schemes\Color::COLOR_1,
				],
				'default' => '#fff', 
				'selectors' => [
					'{{WRAPPER}} .pafe-vertical-timeline__icon>i' => 'color: {{VALUE}}',
				],
			]
		);
		
		$this->add_control(
			'pafe_vertical_timeline_icon_background_color',
			[
				'label' => __( 'Background', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Core\Schemes\Color::get_type(),
					'value' => \Elementor\Core\Schemes\Color::COLOR_1,
				],
				'default' => '#3CCD94', 
				'selectors' => [
					'{{WRAPPER}} .pafe-vertical-timeline__icon' => 'background-color: {{VALUE}}',       
				],
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'pafe_vertical_timeline_icon_box_shadow',
				'label' => __( 'Input Box Shadow', 'pafe' ),
				'selector' => '{{WRAPPER}} .pafe-vertical-timeline__icon',
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'pafe_vertical_timeline_vertical_line_style_section',
			[
				'label' => __( 'Vertical line', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'pafe_vertical_timeline_line_color',
			[
				'label' => __( 'Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Core\Schemes\Color::get_type(),
					'value' => \Elementor\Core\Schemes\Color::COLOR_1,
				],
				'default' => '#B2DFBC', 
				'selectors' => [
					'{{WRAPPER}} .cd-timeline__container:before' => 'background-color: {{VALUE}}',       
				],
			]
		);
		$this->end_controls_section();
		$this->start_controls_section (
			'pafe_vertical_timeline_button_style_section',
			[
				'label' => __( 'Button', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		$this->start_controls_tabs(
			'pafe_vertical_timeline_button_tabs'
		);

		$this->start_controls_tab( 
			'pafe_vertical_timeline_button_normal_tabs',
			[
				'label' => __( 'Normal', 'pafe' ),
			]
		);		
		
		$this->add_control(
			'pafe_vertical_timeline_button_color',
			[
				'label' => __( 'Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Core\Schemes\Color::get_type(), 
					'value' => \Elementor\Core\Schemes\Color::COLOR_1,
				],
				'default' => '#fff', 
				'selectors' => [
					'{{WRAPPER}} .pafe-vertical-timeline__readmore' => 'color: {{VALUE}}',       
				], 
			]
		);
		$this->add_control( 
			'pafe_vertical_timeline_button_background_color',
			[
				'label' => __( 'Background', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Core\Schemes\Color::get_type(), 
					'value' => \Elementor\Core\Schemes\Color::COLOR_1,
				],
				'default' => '#3CCD94',  
				'selectors' => [
					'{{WRAPPER}} .pafe-vertical-timeline__readmore' => 'background-color: {{VALUE}}',       
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab( 
			'pafe_vertical_timeline_button_hover_tabs',
			[
				'label' => __( 'Hover', 'pafe' ),
			]
		);
		$this->add_control(
			'pafe_vertical_timeline_button_hover_color',
			[
				'label' => __( 'Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Core\Schemes\Color::get_type(), 
					'value' => \Elementor\Core\Schemes\Color::COLOR_1,
				],
				'default' => '#fff', 
				'selectors' => [
					'{{WRAPPER}} .pafe-vertical-timeline__readmore:hover' => 'color: {{VALUE}}',       
				], 
			]
		);
		$this->add_control( 
			'pafe_vertical_timeline_button_hover_background_color',
			[
				'label' => __( 'Background', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Core\Schemes\Color::get_type(), 
					'value' => \Elementor\Core\Schemes\Color::COLOR_1,
				],
				'default' => '#3CCD94',  
				'selectors' => [
					'{{WRAPPER}} .pafe-vertical-timeline__readmore:hover' => 'background-color: {{VALUE}}',       
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'readmore_typography',
				'selector' => '{{WRAPPER}} .pafe-vertical-timeline__readmore',
				'scheme' => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_2,
			]
		);
		$this->add_responsive_control(
			'pafe_vertical_timeline_button_padding',
			[
				'label' => __( 'Button Padding', 'pafe' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .pafe-vertical-timeline__readmore' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'pafe_vertical_timeline_button_border_radius',
			[
				'label' => __( 'Border Radius', 'pafe' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .pafe-vertical-timeline__readmore' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			] 
		);
		$this->end_controls_section();
	}	
	protected function render() {
		$settings = $this->get_settings_for_display();
		?>
		<div class="cd-timeline js-cd-timeline pafe-vertical-timeline" data-pafe-vertical-timeline>
			<div class="cd-timeline__container">
		<?php 
			foreach ( $settings['pafe_vertical_timeline'] as $item) :
		?>
			<div class="cd-timeline__block pafe-vertical-timeline__block">
				<?php
					if (!empty($item['pafe_vertical_timeline_image']) || $item['pafe_vertical_timeline_image_or_icon'] == 'image') :
						if ( !empty($item['pafe_vertical_timeline_image']['url'] ) ) :
				?>
						<div class="cd-timeline__img cd-timeline__img--picture pafe-vertical-timeline__img">
							<img class="pafe-vertical-timeline__image"  src="<?php echo $item['pafe_vertical_timeline_image']['url']; ?>" alt="" data-pafe-vertical-timeline-image>
						</div>	
				<?php endif; endif;?>
				<?php
					if (!empty($item['pafe_vertical_timeline_icon']) || $item['pafe_vertical_timeline_image_or_icon'] == 'icon') :	
				?>		
						<div class="cd-timeline__img cd-timeline__img--picture pafe-vertical-timeline__icon">		
							<?php echo '<i class="' . $item['pafe_vertical_timeline_icon'] . '" aria-hidden="true"></i>';?>	
						</div>	
				<?php endif;?>		 
				<?php			
					if (!empty($item['pafe_vertical_timeline_content']) && !empty($item['pafe_vertical_timeline_title'])) :
				?>
					<div class="cd-timeline__content text-component pafe-vertical-timeline-content" data-pafe-vertical-timeline-content>
						<div class="pafe-vertical-timeline-content__title"><?php echo $item['pafe_vertical_timeline_title']; ?></div>
						<div class="color-contrast-medium pafe-vertical-timeline-content__content"><?php echo $item['pafe_vertical_timeline_content']; ?></div>
			
						<div class="flex justify-between items-center pafe-vertical-timeline__dates">
					<?php			
						if (!empty($item['pafe_vertical_timeline_date'])) :	 
					?>
					        <span class="cd-timeline__date pafe_vertical_timeline__date"><?php echo $item['pafe_vertical_timeline_date']; ?></span>
					<?php endif;?>        
					        <?php if (!empty($item['pafe_vertical_timeline_button_link']) && !empty($item['pafe_vertical_timeline_button_text'])) : ?>	
					        <div class="pafe_vertical_timeline__btn">
					        	<a href="<?php echo $item['pafe_vertical_timeline_button_link']; ?>" class="btn btn--subtle pafe-vertical-timeline__readmore"><?php echo $item['pafe_vertical_timeline_button_text']; ?></a>
					        </div>	
					        <?php endif;?>	
					    </div> 							
					</div> 
				<?php endif; ?>				
			</div>	  
		<?php endforeach;?>	
		</div>
		<?php
	}	
}	 
