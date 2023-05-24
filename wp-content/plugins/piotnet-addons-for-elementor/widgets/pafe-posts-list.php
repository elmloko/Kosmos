<?php
	class PAFE_Posts_List extends \Elementor\Widget_Base {
		public function get_name() {
			return 'pafe-posts-list';
		}

		public function get_title() {
			return __( 'PAFE Posts List', 'pafe' );
		}

		public function get_icon() { 
			return 'eicon-post-list';
		} 

		public function get_categories() {
			return [ 'pafe-free-widgets' ];
		}

		public function get_keywords() {
			return [ 'posts', 'list' ];
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
			$this->start_controls_section (
				'pafe_posts_list_section',
				[
					'label' => __( 'Query', 'pafe' ),
				] 
			);
			$post_types = get_post_types( [], 'objects' );
			$post_types_array = array( '' => 'None' );
			foreach ( $post_types as $post_type ) {
		        $post_types_array[$post_type->name] = $post_type->label;
		    }
			$post_types = get_post_types( [], 'objects' );
			$post_types_array = array();
			$taxonomy = array();
			foreach ( $post_types as $post_type ) {
		        $post_types_array[$post_type->name] = $post_type->label;
		        $taxonomy_of_post_type = get_object_taxonomies( $post_type->name, 'names' );
		        $post_type_name = $post_type->name;
		        if (!empty($taxonomy_of_post_type) && $post_type_name != 'nav_menu_item' && $post_type_name != 'elementor_library' && $post_type_name != 'elementor_font' ) {
		        	if ($post_type_name == 'post') {
		        		$taxonomy_of_post_type = array_diff( $taxonomy_of_post_type, ["post_format"] );
		        	}
		        	$taxonomy[$post_type_name] = $taxonomy_of_post_type;
		        }
		    }
		    $taxonomy_array = array();
		    foreach ($taxonomy as $key => $value) {
		    	foreach ($value as $key_item => $value_item) {
		    		$taxonomy_array[$value_item . '|' . $key] = $value_item . ' - ' . $key;
		    	}
		    }
		    $this->add_control(
				'pafe_posts_list_post_taxonomy',
				[
					'label' => __( 'Taxonomy', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'options' => $taxonomy_array,
					'default' => 'category|post',
				]
			);
			$this->add_control(
				'pafe_posts_list_post_per_pages',
				[
					'label' => __( 'Post per page', 'pafe' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'min' => 0,
					'max' => 100,
					'step' => 1,
					'default' => 10,
				]
			);	 
			$this->add_control(
				'pafe_posts_list_term_not_in', [
					'label' => __( 'Term Not In', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'description' => 'E.g: 1,4,5',
					'default' => '',
				]
			); 	
			$this->add_control(
				'pafe_section_layout_type',
				[
					'label' => __( 'Layout Type', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => 'layout_1',
					'options' => [
						'layout_1'  => __( 'Layout 1','pafe' ),
						'layout_2' => __( 'Layout 2','pafe' ),
						'layout_3' => __( 'Layout 3','pafe' ),
					],
				]
			);
			$this->end_controls_section();
			$this->start_controls_section(
				'pafe_posts_list_general_section',
				[
					'label' => __( 'General', 'pafe' ),
				] 
			); 
			$this->add_control(
				'pafe_posts_list_title', [
					'label' => __( 'Title Widget', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => 'Title Widget',
				]
			); 	
			$this->add_control(
				'pafe_posts_list_show_filter',
				[
					'label' => __( 'Show Filter', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'default' => 'yes',
					'label_on' => 'Yes',
					'label_off' => 'No',
					'return_value' => 'yes', 
				] 
			);
			$this->add_control(
				'pafe_posts_list_show_all',
				[
					'label' => __( 'Show All Button', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'default' => 'yes',
					'label_on' => 'Yes',
					'label_off' => 'No',
					'return_value' => 'yes',
				] 
			);
			$this->add_control(
				'pafe_posts_list_title_length',
				[
					'label' => __( 'Title Length', 'pafe' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'min' => 0,
					'max' => 30,
					'step' => 1,
					'default' => 9,
				]
			);	
			$this->add_control(
				'pafe_posts_list_general_excerpt',
				[
					'label' => __( 'Show Excerpt', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label_on' => 'Yes',
					'label_off' => 'No',
					'default' => '',
					'return_value' => 'yes',
				] 
			);
			$this->add_control(
				'pafe_posts_list_general_author',
				[
					'label' => __( 'Show Author', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label_on' => 'Yes',
					'label_off' => 'No',
					'default' => 'yes',
					'return_value' => 'yes',
				] 
			);
			$this->add_control(
				'pafe_posts_list_general_date',
				[
					'label' => __( 'Show Date', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'default' => 'yes',
					'label_on' => 'Yes',
					'label_off' => 'No',
					'return_value' => 'yes',
				] 
			);
			$this->end_controls_section();
			$this->start_controls_section(
				'pafe_posts_list_section_style_section',
				[
					'label' => __( 'Section', 'pafe' ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				]
			);
			$this->add_control(
				'pafe_section_background_type',
				[
					'label' => __( 'Background Type', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => 'classic',
					'separator' => 'before',
					'options' => [
						'classic'  => __( 'Classic','pafe' ),
						'gradient' => __( 'Gradient','pafe' ),
					],
				]
			);
			$this->add_control( 
				'pafe_section_background_overlay',
				[
					'label' => __( 'Background', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'scheme' => [
						'type' => \Elementor\Core\Schemes\Color::get_type(), 
						'value' => \Elementor\Core\Schemes\Color::COLOR_1,
					],
					'default' => '#fff',  
					'selectors' => [
						'{{WRAPPER}} .pafe-posts-list' => 'background-color: {{VALUE}}',       
					],
					'condition' => [
						'pafe_section_background_type' => 'classic'	
					]
				]
			);
			$this->add_control(
				'pafe_section_background_gradient_color',
				[
					'label' => _x( 'Color', 'Background Control', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'default' => 'rgba(242,242,242,0.57)',
					'title' => _x( 'Background Color', 'Background Control', 'pafe' ),
					'selectors' => [
						'{{WRAPPER}} .pafe-posts-list' => 'background-color: {{VALUE}};',
					],
					'condition' => [
						'pafe_section_background_type' => 'gradient'	
					]
				]
			);
			$this->add_control(
				'pafe_section_background_gradient_color_stop',
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
						'pafe_section_background_type' => 'gradient'	
					]
				]
			);
			$this->add_control(
				'pafe_section_background_gradient_color_b',
				[
					'label' => _x( 'Second Color', 'Background Control', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'default' => 'rgba(10,87,117,0.58)',
					'render_type' => 'ui',
					'condition' => [
						'pafe_section_background_type' => 'gradient'	
					]
				]
			);
			$this->add_control(
				'pafe_section_background_gradient_color_b_stop',
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
						'pafe_section_background_type' => 'gradient'	
					]
				]
			);
			$this->add_control(
				'pafe_section_background_gradient_type',
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
						'pafe_section_background_type' => 'gradient'	
					]
				]
			);
			$this->add_control(
				'pafe_section_background_gradient_angle',
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
						'{{WRAPPER}} .pafe-posts-list' => 'background-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{pafe_section_background_gradient_color.VALUE}} {{pafe_section_background_gradient_color_stop.SIZE}}{{pafe_section_background_gradient_color_stop.UNIT}}, {{pafe_section_background_gradient_color_b.VALUE}} {{pafe_section_background_gradient_color_b_stop.SIZE}}{{pafe_section_background_gradient_color_b_stop.UNIT}});',
					],
					'condition' => [
						'pafe_section_background_type' => 'gradient'	
					]
				]
			);
			$this->add_control( 
				'pafe_section_background_gradient_position',
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
						'{{WRAPPER}}.pafe-posts-list' => 'background-color: transparent; background-image: radial-gradient(at {{VALUE}}, {{pafe_section_background_gradient_color.VALUE}} {{pafe_section_background_gradient_color_stop.SIZE}}{{pafe_section_background_gradient_color_stop.UNIT}}, {{pafe_section_background_gradient_color_b.VALUE}} {{pafe_section_background_gradient_color_b_stop.SIZE}}{{pafe_section_background_gradient_color_b_stop.UNIT}});',
					],
					'separator' => 'after',
					'condition' => [
						'pafe_section_background_type' => 'gradient'	
					],
				]
			);
			$this->add_responsive_control(
				'pafe_posts_list_section_padding',
				[
					'label' => __( 'Padding', 'pafe' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'separator' => 'before',
					'selectors' => [
						'{{WRAPPER}} .pafe-posts-list' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				] 
			); 
			$this->add_responsive_control(
				'pafe_posts_list_space',
				[
					'label' => __( 'List Space', 'pafe' ),
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
						'size' => 20,
					],
					'selectors' => [
						'{{WRAPPER}} .pafe-card-right' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					],
				]
			);
			$this->end_controls_section();
			$this->start_controls_section(
				'pafe_posts_list_style_section',
				[
					'label' => __( 'Post list', 'pafe' ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				]
			);
			$this->add_responsive_control(
				'pafe_posts_list_padding',
				[
					'label' => __( 'Padding', 'pafe' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors' => [
						'{{WRAPPER}} .pafe-post-list__right' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_responsive_control(
				'pafe_posts_list_margin_item',
				[
					'label' => __( 'Margin Item', 'pafe' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'separator' => 'before',
					'size_units' => [ 'px', '%' ],
					'selectors' => [
						'{{WRAPPER}} .pafe-card-right' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_responsive_control(
				'pafe_posts_list_padding_item',
				[
					'label' => __( 'Padding Item', 'pafe' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors' => [
						'{{WRAPPER}} .pafe-card-right' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->end_controls_section();
			$this->start_controls_section(
				'pafe_posts_list_filter_style_section',
				[
					'label' => __( 'Filter', 'pafe' ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				]
			);
			$this->add_responsive_control(
				'pafe_posts_list_filter_space',
				[
					'label' => __( 'Space Bottom', 'pafe' ),
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
						'size' => 20,
					],
					'selectors' => [
						'{{WRAPPER}} .pafe-posts-list__filter' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					],
				]
			);
			$this->start_controls_tabs(
				'pafe_posts_list_filter_tabs'
			);

			$this->start_controls_tab( 
				'pafe_posts_list_filter_normal_tabs',
				[
					'label' => __( 'Normal', 'pafe' ),
				]
			);		
			$this->add_control(
				'pafe_posts_list_filter_background', 
				[ 
					'label' => __( 'Background', 'pafe' ), 
					'type' => \Elementor\Controls_Manager::COLOR,
					'scheme' => [
						'type' => \Elementor\Core\Schemes\Color::get_type(),
						'value' => \Elementor\Core\Schemes\Color::COLOR_1,
					], 
					'default' => '#fff',  
					'selectors' => [
						'{{WRAPPER}} .pafe-posts-list__filter-item' => 'background-color: {{VALUE}}',
					],
				]
			);	
			$this->add_control(
				'pafe_posts_list_filter_color', 
				[ 
					'label' => __( 'Color', 'pafe' ), 
					'type' => \Elementor\Controls_Manager::COLOR,
					'scheme' => [
						'type' => \Elementor\Core\Schemes\Color::get_type(),
						'value' => \Elementor\Core\Schemes\Color::COLOR_1,
					],
					'default' => '#0095EB',  
					'selectors' => [
						'{{WRAPPER}} .pafe-posts-list__filter-item' => 'color: {{VALUE}}',
					],
				]
			);	
			
			$this->end_controls_tab();
			$this->start_controls_tab( 
				'pafe_posts_list_filter_hover_tabs',
				[
					'label' => __( 'Hover', 'pafe' ),
				]
			);
			$this->add_control(
				'pafe_posts_list_filter_background_hover', 
				[ 
					'label' => __( 'Background', 'pafe' ), 
					'type' => \Elementor\Controls_Manager::COLOR,
					'scheme' => [
						'type' => \Elementor\Core\Schemes\Color::get_type(),
						'value' => \Elementor\Core\Schemes\Color::COLOR_1,
					], 
					'default' => '#0095EB',  
					'selectors' => [
						'{{WRAPPER}} .pafe-posts-list__filter-item:hover' => 'background-color: {{VALUE}}',
						'{{WRAPPER}} .actives' => 'background-color: {{VALUE}}',
					],
				]
			);	
			$this->add_control(
				'pafe_posts_list_filter_color_hover', 
				[ 
					'label' => __( 'Color', 'pafe' ), 
					'type' => \Elementor\Controls_Manager::COLOR,
					'scheme' => [
						'type' => \Elementor\Core\Schemes\Color::get_type(),
						'value' => \Elementor\Core\Schemes\Color::COLOR_1, 
					],
					'default' => '#fff',  
					'selectors' => [
						'{{WRAPPER}} .pafe-posts-list__filter-item:hover' => 'color: {{VALUE}}',
						'{{WRAPPER}} .actives' => 'color: {{VALUE}}',
					],
				]
			);
			$this->end_controls_tab();
			$this->end_controls_tabs();	
			$this->add_responsive_control(
				'pafe_posts_list_filter_padding',
				[
					'label' => __( 'Padding', 'pafe' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'separator' => 'before',
					'selectors' => [
						'{{WRAPPER}} .pafe-posts-list__filter-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_responsive_control(
				'pafe_posts_list_filter_margin',
				[
					'label' => __( 'Margin', 'pafe' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors' => [
						'{{WRAPPER}} .pafe-posts-list__filter-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_responsive_control(
				'pafe_posts_list_filter_border_radius',
				[
					'label' => __( 'Border Radius', 'pafe' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors' => [
						'{{WRAPPER}} .pafe-posts-list__filter-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				] 
			);
			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'filter_typography',
					'selector' => '{{WRAPPER}} .pafe-posts-list__filter-item',
					'scheme' => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				] 
			); 
			$this->add_responsive_control(
	   			'filter_align',
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
						'{{WRAPPER}} .pafe-posts-list__filter' => 'text-align: {{VALUE}};',
	 				],
	   			]
   			);
   			$this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				[
					'name' => 'pafe_posts_list_filters_border',
					'label' => __( 'Border', 'pafe' ),
					'selector' => '{{WRAPPER}} .pafe-posts-list__filter',
				]
			);
			$this->end_controls_section(); 
			$this->start_controls_section(
				'pafe_posts_list_title_widget_style_section',
				[
					'label' => __( 'Widget Title', 'pafe' ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				]
			);
			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'title_widget_typography',
					'selector' => '{{WRAPPER}} .pafe-posts-list__filter-title',
					'scheme' => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				] 
			); 
			$this->add_control(
				'pafe_posts_list_title_wiget_color',
				[
					'label' => __( 'Color', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'scheme' => [
						'type' => \Elementor\Core\Schemes\Color::get_type(),
						'value' => \Elementor\Core\Schemes\Color::COLOR_1,
					],
					'default' => '#42527B', 
					'selectors' => [
						'{{WRAPPER}} .pafe-posts-list__filter-title' => 'color: {{VALUE}}',
					],
				]
			);	
			$this->add_responsive_control(
	   			'title_widget_align',
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
						'{{WRAPPER}} .pafe-posts-list__filter-title' => 'text-align: {{VALUE}};',
	 				],
	   			]
   			);
			$this->end_controls_section(); 
			$this->start_controls_section(
				'pafe_posts_list_title_style_section',
				[
					'label' => __( 'Post Title', 'pafe' ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				]
			);
			
 			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'title_typography',
					'selector' => '{{WRAPPER}} .pafe-card-right__title',
					'scheme' => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				] 
			); 
			$this->add_control(
				'pafe_posts_list_title_color',
				[
					'label' => __( 'Color', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'scheme' => [
						'type' => \Elementor\Core\Schemes\Color::get_type(),
						'value' => \Elementor\Core\Schemes\Color::COLOR_1,
					],
					'default' => '#1b326e', 
					'selectors' => [
						'{{WRAPPER}} .pafe-card-right__title' => 'color: {{VALUE}}',
					],
				]
			);	
			$this->add_control(
				'pafe_posts_list_title_color_hover',
				[
					'label' => __( 'Hover', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'scheme' => [
						'type' => \Elementor\Core\Schemes\Color::get_type(),
						'value' => \Elementor\Core\Schemes\Color::COLOR_1,
					],
					'default' => '#0095EB', 
					'selectors' => [
						'{{WRAPPER}} .pafe-card-right__title:hover' => 'color: {{VALUE}}',
					],
				]
			);	
			$this->end_controls_section(); 
			$this->start_controls_section(
				'pafe_posts_list_text_style_section',
				[
					'label' => __( 'Posts Text', 'pafe' ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
					
				]
			);
			$this->add_control(
				'pafe_posts_list_text_color', 
				[ 
					'label' => __( 'Color', 'pafe' ), 
					'type' => \Elementor\Controls_Manager::COLOR,
					'scheme' => [
						'type' => \Elementor\Core\Schemes\Color::get_type(),
						'value' => \Elementor\Core\Schemes\Color::COLOR_1,
					],
					'default' => 'rgba(35,76,109,0.56)',  
					'selectors' => [
						'{{WRAPPER}} .pafe-card-right__description' => 'color: {{VALUE}}',
					],
				]
			);	
			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'text_typography',
					'selector' => '{{WRAPPER}} .pafe-card-right__description',
					'scheme' => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				] 
			); 
			$this->add_responsive_control(
				'pafe_posts_list_text_length',
				[
					'label' => __( 'Length', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 300,
							'step' => 1, 
						],
					],
					'default' => [
						'unit' => 'px',
						'size' => 60,
					],
					'selectors' => [
						'{{WRAPPER}} .pafe-card-right__description' => 'max-height: {{SIZE}}{{UNIT}};',
					],
				]
			);
			$this->end_controls_section(); 
			$this->start_controls_section(
				'pafe_posts_list_author_style_section',
				[
					'label' => __( 'Posts Author', 'pafe' ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'author_typography',
					'selector' => '{{WRAPPER}} .pafe-card-right__info,.pafe-card-left__info',
					'scheme' => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				]
			); 
			$this->add_control(
				'pafe_posts_list_author_color', 
				[ 
					'label' => __( 'Color', 'pafe' ), 
					'type' => \Elementor\Controls_Manager::COLOR,
					'scheme' => [
						'type' => \Elementor\Core\Schemes\Color::get_type(),
						'value' => \Elementor\Core\Schemes\Color::COLOR_1,
					],
					'default' => 'rgba(35,76,109,0.56)',  
					'selectors' => [
						'{{WRAPPER}} .pafe-card-right__info' => 'color: {{VALUE}}',
					],
				]
			);	
			$this->end_controls_section();  
			$this->start_controls_section(
				'pafe_posts_list_image_style_section',
				[
					'label' => __( 'Post Image', 'pafe' ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				]
			);
			
			$this->add_control(
				'pafe_posts_list_image_aspect_ratio',
				[
					'label' => __( 'Aspect Ratio', 'pafe' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'description' => 'Aspect Ratio = Height / Width * 100. E.g Width = 100, Height = 100 => Ratio = 1; Width = 100, Height = 50 => Ratio = 50',
					'default' => 65,
					'selectors' => [
						'{{WRAPPER}} .pafe-card-right__thumbnail::before' => 'content: ""; display: block; padding-top: {{VALUE}}%',
					],
				]
			);

			$this->end_controls_section();
			$this->start_controls_section(
				'pafe_featured_post_style_section',
				[
					'label' => __( 'Featured Post', 'pafe' ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				]
			);
			$this->add_responsive_control(
				'pafe_featured_post_height',
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
						'size' => 444,
					],
					'selectors' => [
						'{{WRAPPER}} .pafe-card-left__content' => 'height: {{SIZE}}{{UNIT}};', 
						'{{WRAPPER}} .pafe-card-left__inner' => 'height: {{SIZE}}{{UNIT}};', 
						'{{WRAPPER}} .pafe-card-left' => 'height: {{SIZE}}{{UNIT}};', 
					],
				]
			);
			$this->add_control(
				'pafe_featured_post_title_color',
				[
					'label' => __( 'Color', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'scheme' => [
						'type' => \Elementor\Core\Schemes\Color::get_type(),
						'value' => \Elementor\Core\Schemes\Color::COLOR_1,
					],
					'default' => '#fff', 
					'selectors' => [
						'{{WRAPPER}} .pafe-card-left__title' => 'color: {{VALUE}}',
						'{{WRAPPER}} .pafe-card-left__info' => 'color: {{VALUE}}',
					],
				]
			);	
			
			$this->add_control(
				'pafe_featured_post__title_color_hover',
				[
					'label' => __( 'Hover', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'scheme' => [
						'type' => \Elementor\Core\Schemes\Color::get_type(),
						'value' => \Elementor\Core\Schemes\Color::COLOR_1,
					],
					'default' => '#0095EB', 
					'selectors' => [
						'{{WRAPPER}} .pafe-card-left__title:hover' => 'color: {{VALUE}}',
					],
				]
			);	
			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'title_featured_typography',
					'selector' => '{{WRAPPER}} .pafe-card-left__title',
					'scheme' => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				]
			);
			$this->add_control(
				'pafe_featured_post_background_type',
				[
					'label' => __( 'Background Type', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => 'gradient',
					'separator' => 'before',
					'options' => [
						'classic'  => __( 'Classic','pafe' ),
						'gradient' => __( 'Gradient','pafe' ),
					],
				]
			);
			$this->add_control( 
				'pafe_featured_post_background_overlay',
				[
					'label' => __( 'Background Ovelay', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'scheme' => [
						'type' => \Elementor\Core\Schemes\Color::get_type(), 
						'value' => \Elementor\Core\Schemes\Color::COLOR_1,
					],
					'default' => '',  
					'selectors' => [
						'{{WRAPPER}} .pafe-card-left__content' => 'background-color: {{VALUE}}',       
					],
					'condition' => [
						'pafe_featured_post_background_type' => 'classic'	
					]
				]
			);
			$this->add_control(
				'pafe_featured_post_background_gradient_color',
				[
					'label' => _x( 'Color', 'Background Control', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'default' => 'rgba(242,242,242,0.57)',
					'title' => _x( 'Background Color', 'Background Control', 'pafe' ),
					'selectors' => [
						'{{WRAPPER}} .pafe-card-left__content' => 'background-color: {{VALUE}};',
					],
					'condition' => [
						'pafe_featured_post_background_type' => 'gradient'	
					]
				]
			);
			$this->add_control(
				'pafe_featured_post_background_gradient_color_stop',
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
						'pafe_featured_post_background_type' => 'gradient'	
					]
				]
			);
			$this->add_control(
				'pafe_featured_post_background_gradient_color_b',
				[
					'label' => _x( 'Second Color', 'Background Control', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'default' => '#07232eeb',
					'render_type' => 'ui',
					'condition' => [
						'pafe_featured_post_background_type' => 'gradient'	
					]
				]
			);
			$this->add_control(
				'pafe_featured_post_background_gradient_color_b_stop',
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
						'pafe_featured_post_background_type' => 'gradient'	
					]
				]
			);
			$this->add_control(
				'pafe_featured_post_background_gradient_gradient_type',
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
						'pafe_featured_post_background_type' => 'gradient'	
					]
				]
			);
			$this->add_control(
				'pafe_featured_post_background_gradient_angle',
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
						'{{WRAPPER}} .pafe-card-left__content' => 'background-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{pafe_featured_post_background_gradient_color.VALUE}} {{pafe_featured_post_background_gradient_color_stop.SIZE}}{{pafe_featured_post_background_gradient_color_stop.UNIT}}, {{pafe_featured_post_background_gradient_color_b.VALUE}} {{pafe_featured_post_background_gradient_color_b_stop.SIZE}}{{pafe_featured_post_background_gradient_color_b_stop.UNIT}});',
					],
					'condition' => [
						'pafe_featured_post_background_type' => 'gradient'	
					]
				]
			);
			$this->add_control(
				'pafe_featured_post_background_gradient_position',
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
						'{{WRAPPER}}.pafe-card-left__content' => 'background-color: transparent; background-image: radial-gradient(at {{VALUE}}, {{pafe_featured_post_background_gradient_color.VALUE}} {{pafe_featured_post_background_gradient_color_stop.SIZE}}{{pafe_featured_post_background_gradient_color_stop.UNIT}}, {{pafe_featured_post_background_gradient_color_b.VALUE}} {{pafe_featured_post_background_gradient_color_b_stop.SIZE}}{{pafe_featured_post_background_gradient_color_b_stop.UNIT}});',
					],
					'separator' => 'after',
					'condition' => [
						'pafe_featured_post_background_type' => 'gradient'	
					],
				]
			);
			$this->add_responsive_control(
				'pafe_featured_post_padding',
				[
					'label' => __( 'Padding', 'pafe' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors' => [
						'{{WRAPPER}} .pafe-post-list__left' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_control( 
				'pafe_featured_post_background_content',
				[
					'label' => __( 'Content Background', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'scheme' => [
						'type' => \Elementor\Core\Schemes\Color::get_type(), 
						'value' => \Elementor\Core\Schemes\Color::COLOR_1,
					],
					'separator' => 'before',
					'default' => '#00e8cc',  
					'selectors' => [
						'{{WRAPPER}} .pafe-card-left__content-layout_3' => 'background-color: {{VALUE}}',       
					],
					'condition' => [ 
						'pafe_section_layout_type' => 'layout_3',
					]
				]
			);
			$this->add_responsive_control(
				'pafe_featured_post_content_padding',
				[
					'label' => __( 'Content Padding', 'pafe' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors' => [
						'{{WRAPPER}} .pafe-card-left__content-layout_3' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' => [ 
						'pafe_section_layout_type' => 'layout_3',
					]
				]
			);
			$this->add_control(
				'pafe_featured_post_image_height',
				[
					'label' => __( 'Aspect Ratio', 'pafe' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'description' => 'This option has just worked on Layout 3',
					'default' => 60,
					'selectors' => [
						'{{WRAPPER}} .pafe-card-left__inner-layout_3::before' => 'content: ""; display: block; padding-top: {{VALUE}}%',
					],
				]
			);
			$this->end_controls_section();		
		}		 
		protected function render() {
			$settings = $this->get_settings_for_display();
			if ( $settings['pafe_posts_list_post_taxonomy'] ) {
				$taxonomy_post_type = explode('|',$settings['pafe_posts_list_post_taxonomy']); 
				$terms_array = array();						
				if ( !empty($settings['pafe_posts_list_post_taxonomy']) ) {
					$terms_array['taxonomy'] = $taxonomy_post_type[0];
					$terms_array['hide_empty'] = false;
				}
				if ( !empty($settings['pafe_posts_list_term_not_in']) ) {
					$not_in = explode(',',$settings['pafe_posts_list_term_not_in']); 
					$terms_array['exclude'] = $not_in;
				}
				$terms = get_terms($terms_array); 

				if ( !empty($settings['pafe_posts_list_title_length']) ) {	
					$titleLength = 	$settings['pafe_posts_list_title_length'];
				}	
				$options = [
					'taxonomy' => $taxonomy_post_type[0],
					'post_type' => $taxonomy_post_type[1],
					'post_per_pages' => $settings['pafe_posts_list_post_per_pages'], 
					'author' => $settings['pafe_posts_list_general_author'],
					'date' => $settings['pafe_posts_list_general_date'],
					'excerpt' => $settings['pafe_posts_list_general_excerpt'],
					'layout_type' => $settings['pafe_section_layout_type'],
				];
				$first_term_show = array();
				$first_term_show['post_type'] = $taxonomy_post_type[1];	
				if ( !empty($settings['pafe_posts_list_post_per_pages']) ) {			
			    	$first_term_show['posts_per_page'] = $settings['pafe_posts_list_post_per_pages'];
				}			
			}			
		?> 
			<div class="pafe-posts-list" data-pafe-posts-list data-pafe-posts-list-option='<?php echo json_encode( $options ); ?>'>
			<?php if ( !empty($settings['pafe_posts_list_show_filter']) == 'yes' ) : ?> 	
				<div class="pafe-posts-list__filter" >
					<div class="pafe-posts-list__filter-title">
						<?php 
							if ( !empty($settings['pafe_posts_list_title']) ) {			
						    	echo $settings['pafe_posts_list_title'];
							}
						?> 
					</div>
					<div class="pafe-posts-list__filter-list">	
						<?php if ($settings['pafe_posts_list_show_all'] == 'yes'): ?>	
						<span class="pafe-posts-list__filter-item" data-id="0" data-pafe-posts-list-filter-item>All</span>
						<?php endif; ?>
						<?php foreach ($terms as $term) :?>	
							<span class="pafe-posts-list__filter-item" data-id=<?php echo $term->term_id; ?> data-pafe-posts-list-filter-item><?php echo $term->name; ?></span>
						<?php endforeach;?>
					</div>
				</div> 
			<?php endif; ?>	
				<div class="pafe-posts-list__post" data-pafe-tab-content-post>
						<div class="<?php if ($settings['pafe_section_layout_type'] == 'layout_1') {
										echo 'pafe-posts-list-first-show';
									} elseif ($settings['pafe_section_layout_type'] == 'layout_2') {
										echo 'pafe-posts-list-first-show_layout-2';
									} elseif ($settings['pafe_section_layout_type'] == 'layout_3') {
										echo 'pafe-posts-list-first-show_layout-3';
									} ?>">
						<?php		 
							$first_query = new WP_Query($first_term_show);
							$index = 0;
							if ($first_query->have_posts()) : while($first_query->have_posts()) : $first_query->the_post();	
								$index ++;
						?>	
							<?php if ($index == 1): ?>
								<div class="pafe-post-list__left">
									<div class="pafe-card-left">
										<?php if ($settings['pafe_section_layout_type'] !== 'layout_3'): ?>
										<div class="pafe-card-left__inner" style="background-image: url('<?php echo get_the_post_thumbnail_url();?>');">
										<?php endif; ?>
										<?php if ($settings['pafe_section_layout_type'] == 'layout_3'): ?>
										<div class="pafe-card-left__inner-layout_3" style="background-image: url('<?php echo get_the_post_thumbnail_url();?>');"></div>	
										<?php endif; ?>
									 		<a class="<?php if ($settings['pafe_section_layout_type'] == 'layout_3') {
												echo 'pafe-card-left__content-layout_3';
											} else {
												echo 'pafe-card-left__content';
											}?>" href="<?php echo get_permalink(); ?>">	
												<div class="pafe-card-left__title"><?php echo wp_trim_words( get_the_title(), $titleLength ); ?></div>
												<div class="pafe-card-left__info">
													<?php if ($settings['pafe_posts_list_general_author'] == 'yes'): ?>
													<span class="pafe-card-left__author">
														<i class="fa fa-user" aria-hidden="true"></i><?php echo get_the_author(); ?>
													</span>
													<?php endif; ?>	
													<?php if ($settings['pafe_posts_list_general_date'] == 'yes'): ?>
													<span class="pafe-card-left__tag" style="padding-left: 10px;">
														<i class="far fa-clock"></i>  <?php echo get_the_date(); ?>
													</span>
													<?php endif; ?> 
												</div>	
											</a>
										<?php if ($settings['pafe_section_layout_type'] !== 'layout_3'): ?>	
										</div>  
										<?php endif; ?>
									</div>	
								</div>
							<?php endif; ?>	  
							<?php if ($index == 2) : ?>	 
								<div class="pafe-post-list__right">
							<?php endif; ?>
							<?php if ( $index >=2 ) : ?>	
									<div class="pafe-card-right"> 
										<div class="pafe-card-right__inner">
											<a class="pafe-card-right__thumbnail" href="<?php echo get_permalink(); ?>" style="background-image: url('<?php echo get_the_post_thumbnail_url();?>');">
											</a>
											<div class="pafe-card-right__content">			
												<a href="<?php echo get_permalink(); ?>"><div class="pafe-card-right__title"> <?php echo wp_trim_words( get_the_title(), $titleLength ); ?></div></a>
											<?php if ($settings['pafe_posts_list_general_excerpt'] == 'yes'): ?>
												<div class="pafe-card-right__description">
													<?php echo get_the_excerpt(); ?>
												</div> 
											<?php endif; ?>
												<div class="pafe-card-right__info">
												<?php if ($settings['pafe_posts_list_general_date'] == 'yes'): ?>
													<span class="pafe-card-right__tag">
														<i class="far fa-clock"></i> <?php echo get_the_date(); ?>
													</span>  
												<?php endif; ?>
												</div>
											</div>
										</div>
									</div> 
							<?php endif; ?>	
							<?php endwhile; endif; wp_reset_postdata(); ?> 
						</div>
					</div>
				</div>	
			</div>
		<?php	
	}	
}			
