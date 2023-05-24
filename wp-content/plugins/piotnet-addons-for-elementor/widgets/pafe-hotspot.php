<?php

class PAFE_Hotspot extends \Elementor\Widget_Base {

	public function get_name() {
		return 'pafe-hotspot';
	}

	public function get_title() {
		return __( 'PAFE Hotspot', 'pafe' );
	}

	public function get_icon() {
		return 'eicon-hotspot';
	}

	public function get_categories() {
		return [ 'pafe-free-widgets' ];
	}

	public function get_keywords() {
		return [ 'hotspot', 'image' ];
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
	

	/** Image Control Section**/
	protected function _register_controls() {
		$this->start_controls_section(
				'pafe_hotspot_image_section',
				[
					'label' => __( 'Image', 'pafe' ),
					'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
				]
			);

				$this->add_control(
					'pafe_hotspot_image_upload',
					[
						'label' => __( 'Choose Image', 'pafe' ),
						'type' => \Elementor\Controls_Manager::MEDIA,
						'default' => [
							'url' => \Elementor\Utils::get_placeholder_image_src(),
						],
					]
				);
				
				$this->add_group_control(
					\Elementor\Group_Control_Image_Size::get_type(),
					[
						'name' => 'pafe_hotspot_thumbnail', 
						'exclude' => [ 'custom' ],
						'include' => [],
						'default' => 'full',
					]
				);

		$this->end_controls_section();

		$this->start_controls_section(
			'pafe_hotspot_marker_section',
				[
					'label' => __( 'Marker', 'pafe' ),
					'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
				]
			);

				$repeater = new \Elementor\Repeater();

				$repeater->start_controls_tabs(
					'icon_tooltip_tabs'
				);

				$repeater->start_controls_tab( 
					'pafe_hotspot_marker_icon',
					[
						'label' => __( 'Icon', 'pafe' ),
					]
				);

					$repeater->add_control(
						'pafe_hotspot_marker_title',
							[
								'label' => __( 'Title', 'pafe' ),
								'type' => \Elementor\Controls_Manager::TEXT,
								'default' => 'Marker',
							]
					);

					$repeater->add_control(
						'pafe_hotspot_marker_type', [
							'label' => __( 'Type', 'pafe' ),
							'type' => \Elementor\Controls_Manager::SELECT,
							'default' => 'icon',
							'options' => [
								'icon'  => __( 'Icon', 'pafe' ),
								'text' => __( 'Text', 'pafe' ),
								'image' => __( 'Image', 'pafe' ),
							],
						]
					);

						$repeater->add_control(
							'pafe_hotspot_marker_type_icon',
								[
									'label' => __( 'Icon', 'pafe' ),
									'type' => \Elementor\Controls_Manager::ICONS,
									'default' => [
										'value' => 'fas fa-star',
										'library' => 'solid',
									],
									'condition' => [ 
										'pafe_hotspot_marker_type' => 'icon',
									]
								]
						);

						$repeater->add_control(
							'pafe_hotspot_marker_type_icon_view',
							[
								'label' => __( 'View', 'pafe' ),
								'type' => \Elementor\Controls_Manager::SELECT,
								'default' => 'default',
								'options' => [
									'default' => __( 'Default', 'pafe' ),
									'stacked' => __( 'Stacked', 'pafe' ),
									'framed' => __( 'Framed', 'pafe' ),
								],
								'condition' => [ 
									'pafe_hotspot_marker_type' => 'icon',
								]
							]
						);

						$repeater->add_control(
							'pafe_hotspot_marker_type_icon_view_primary_color',
							[
								'label' => __( 'Primary Color', 'pafe' ),
								'type' => \Elementor\Controls_Manager::COLOR,
								'default' => '#6ec1e4',
								'selectors' => [
									'{{WRAPPER}} {{CURRENT_ITEM}} .pafe-hotspot-icon-view-stacked' => 'background: {{VALUE}};',
									'{{WRAPPER}} {{CURRENT_ITEM}} .pafe-hotspot-icon-view-framed' => 'background: {{VALUE}};',
									'{{WRAPPER}} {{CURRENT_ITEM}} .pafe-hotspot-icon-view-default' => 'color: {{VALUE}};',
								],
								'scheme' => [
									'type' => \Elementor\Core\Schemes\Color::get_type(),
									'value' => \Elementor\Core\Schemes\Color::COLOR_1,
								],
								'condition' => [ 
									'pafe_hotspot_marker_type' => 'icon',
								]
							]
						);

						$repeater->add_control(
							'pafe_hotspot_marker_type_icon_view_secondary_color',
							[
								'label' => __( 'Secondary Color', 'pafe' ),
								'type' => \Elementor\Controls_Manager::COLOR,
								'default' => '#fff',
								'condition' => [
									'pafe_hotspot_marker_type_icon_view!' => 'default',
									'pafe_hotspot_marker_type' => 'icon',
								],
								'selectors' => [
									'{{WRAPPER}} {{CURRENT_ITEM}} .pafe-hotspot-icon-view-stacked i' => 'color: {{VALUE}};',
									'{{WRAPPER}} {{CURRENT_ITEM}} .pafe-hotspot-icon-view-framed i' => 'color: {{VALUE}};',
									'{{WRAPPER}} {{CURRENT_ITEM}} .pafe-hotspot-icon-view-framed' => 'border-color: {{VALUE}};',
								],
								'scheme' => [
									'type' => \Elementor\Core\Schemes\Color::get_type(),
									'value' => \Elementor\Core\Schemes\Color::COLOR_1,
								],
							]
						);

						$repeater->add_responsive_control(
							'pafe_hotspot_marker_type_icon_size',
							[
								'label' => __( 'Icon Size', 'pafe' ),
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
									'size' => 28,
								],
								'selectors' => [
									'{{WRAPPER}} {{CURRENT_ITEM}} .pafe-hotspot__marker-icon-icon' => 'font-size: {{SIZE}}{{UNIT}};',
								],
								'condition' => [ 
									'pafe_hotspot_marker_type' => 'icon',
								]

							]
						);

						$repeater->add_control(
							'pafe_hotspot_marker_type_icon_framed_border',
							[
								'label' => __( 'Frame Border', 'pafe' ),
								'type' => \Elementor\Controls_Manager::DIMENSIONS,
								'size_units' => [ 'px', '%', 'em' ],
								'selectors' => [
									'{{WRAPPER}} {{CURRENT_ITEM}} .pafe-hotspot-icon-view-framed' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
								],
								'condition' => [ 
									'pafe_hotspot_marker_type_icon_view' => 'framed',
								]
							]
						);

						$repeater->add_control(
							'pafe_hotspot_marker_type_image',
								[
									'label' => __( 'Marker Image', 'pafe' ),
									'type' => \Elementor\Controls_Manager::MEDIA,
									'default' => [
										'url' => \Elementor\Utils::get_placeholder_image_src(),
									],
									'condition' => [ 
										'pafe_hotspot_marker_type' => 'image',
									]
								]
						);


						$repeater->add_responsive_control(
							'pafe_hotspot_marker_type_image_size',
							[
								'label' => __( 'Marker Image Size', 'pafe' ),
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
									'size' => 50,
								],
								'selectors' => [
									'{{WRAPPER}} {{CURRENT_ITEM}} .pafe-hopspot__marker-icon-image' => 'width: {{SIZE}}{{UNIT}};',
								],
								'condition' => [ 
									'pafe_hotspot_marker_type' => 'image',
								]

							]
						);


						$repeater->add_control(
							'pafe_hotspot_marker_type_text',
								[
									'label' => __( 'Marker Text', 'pafe' ),
									'type' => \Elementor\Controls_Manager::TEXT,
									'placeholder' => __( '', 'pafe' ),
									'default' => 'Marker',
									'condition' => [
										'pafe_hotspot_marker_type' => 'text',	
									]
								]
						);


						$repeater->add_group_control(
							\Elementor\Group_Control_Typography::get_type(),
							[
								'name' => 'pafe_hotspot_marker_type_text_style',
								'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .pafe-hopspot__marker-icon-text',
								'scheme' => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_2,
								'condition' => [
										'pafe_hotspot_marker_type' => 'text',	
									]
							]
						);

						$repeater->add_control(
							'pafe_hotspot_marker_type_text_color',
							[
								'label' => __( 'Color', 'pafe' ),
								'type' => \Elementor\Controls_Manager::COLOR,
								'default' => '#000', 
								'default' => '#000', 
								'selectors' => [
									'{{WRAPPER}} {{CURRENT_ITEM}} .pafe-hopspot__marker-icon-text' => 'color: {{VALUE}}',
								],
								'condition' => [
										'pafe_hotspot_marker_type' => 'text',	
									]
							]
						);


						$repeater->add_control(
							'pafe_hotspot_marker_opacity',
							[
								'label' => __( 'Opacity', 'pafe' ),
								'type' => \Elementor\Controls_Manager::SLIDER,
								'size_units' => [ 'px' ],
								'range' => [
									'px' => [
										'min' => 0,
										'max' => 1,
										'step' => 0.1,
									],

								],
								'default' => [
									'unit' => 'px',
									'size' => 1,
								],
								'selectors' => [
									'{{WRAPPER}} {{CURRENT_ITEM}} .pafe-hotspot__marker-icon-icon' => 'opacity: {{SIZE}};',
								],
								'condition' => [
									'pafe_hotspot_marker_type' => 'icon',	
								]
							]
						);		


						$repeater->add_responsive_control(
							'pafe_hotspot_marker_position_horizontal',
							[
								'label' => __( 'Horizontal Position', 'pafe' ),
								'type' => \Elementor\Controls_Manager::SLIDER,
								'size_units' => [ '%' ],
								'range' => [
									'%' => [
										'min' => 0,
										'max' => 100,
										'step' => 1,
									],

								],
								'default' => [
									'unit' => '%',
									'size' => 50,
								],
								'selectors' => [
									'{{WRAPPER}} {{CURRENT_ITEM}}' => 'left: {{SIZE}}%;',
								],

							]
						);				

						$repeater->add_responsive_control(
							'pafe_hotspot_marker_position_vertical',
							[
								'label' => __( 'Vertical Position', 'pafe' ),
								'type' => \Elementor\Controls_Manager::SLIDER,
								'size_units' => [ '%' ],
								'range' => [
									'%' => [
										'min' => 0,
										'max' => 100,
										'step' => 1,
									],

								],
								'default' => [
									'unit' => '%',
									'size' => 50,
								],
								'selectors' => [
									'{{WRAPPER}} {{CURRENT_ITEM}}' => 'top: {{SIZE}}%;',
								],

							]
						);
					$repeater->end_controls_tab();
					$repeater->start_controls_tab( 
						'pafe_hotspot_marker_tooltip',
							[
								'label' => __( 'Tooltip', 'pafe' ),
								'description' => __( 'This feature only works on the frontend.', 'pafe' ),
							]
						);

						$repeater->add_control(
							'pafe_hotspot_tooltip_content_type',
							[
								'label' => __( 'Type', 'pafe' ),
								'type' => \Elementor\Controls_Manager::SELECT,
								'default' => 'content',
								'options' => [
									'image'  => __( 'Image','pafe' ),
									'content' => __( 'Text Editor','pafe' ),
									'saved_template' => __( 'Elementor Template','pafe' ),
								],
								
							]
						);

						$repeater->add_control(
							'pafe_hotspot_tooltip_content_wysiwyg',
							[ 
								'label' => __( 'Text Editor','pafe' ),
								'type' => \Elementor\Controls_Manager::WYSIWYG,
								'default' => '<p>description of this hotspot</p>',
								'condition' => [
									'pafe_hotspot_tooltip_content_type' => 'content',
								]
							] 
						);

						$repeater->add_control(
							'pafe_hotspot_tooltip_content_saved_template',
							[
								'label' => __( 'Shortcode', 'pafe' ),
								'type' => \Elementor\Controls_Manager::TEXT,
								'placeholder' => __( 'Shortcode', 'pafe' ),
								'condition' => [
									'pafe_hotspot_tooltip_content_type' => 'saved_template'	
								]
							]
						);

						$repeater->add_control(
							'pafe_hotspot_tooltip_content_image',
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
									'pafe_hotspot_tooltip_content_type' => 'image',
								]
							]
						);
			 
						$repeater->add_control(
							'pafe_hotspot_tooltip_duration',
							[
								'label' => __( 'Duration (ms)', 'pafe' ),
								'type' => \Elementor\Controls_Manager::NUMBER,
								'default' => '300',
								
							] 
						);

						$repeater->add_control(
							'pafe_hotspot_tooltip_distance',
							[
								'label' => __( 'Distance', 'pafe' ),
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
									'size' => 5,
								],
								'selectors' => [
									'{{WRAPPER}} {{CURRENT_ITEM}} .pafe-hotspot__tooltip-top' => 'margin-bottom: {{SIZE}}{{UNIT}};',
									'{{WRAPPER}} {{CURRENT_ITEM}} .pafe-hotspot__tooltip-bottom' => 'margin-top: {{SIZE}}{{UNIT}};',
									'{{WRAPPER}} {{CURRENT_ITEM}} .pafe-hotspot__tooltip-left' => 'margin-right: {{SIZE}}{{UNIT}};',
									'{{WRAPPER}} {{CURRENT_ITEM}} .pafe-hotspot__tooltip-right' => 'margin-left: {{SIZE}}{{UNIT}};',
								],
							]	
						);

						$repeater->add_control(
							'pafe_hotspot_tooltip_placement',
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
								
							] 
						);


						$repeater->add_control(
							'pafe_hotspot_tooltip_trigger',
							[
								'label' => __( 'Trigger Event', 'pafe' ),
								'type' => \Elementor\Controls_Manager::SELECT,
								'default' => 'click',
								'options' => [
									'hover'  => __( 'Hover','pafe' ),
									'click' => __( 'Click','pafe' ),
								],
								
							]
						);

			 
						$repeater->add_control(
							'pafe_hotspot_tooltip_content_color',
							[
								'label' => __( 'Content Color', 'pafe' ),
								'type' => \Elementor\Controls_Manager::COLOR,
								'scheme' => [
									'type' => \Elementor\Core\Schemes\Color::get_type(),
									'value' => \Elementor\Core\Schemes\Color::COLOR_1,
								],
								'default' => '#6ec1e4',  
								'selectors' => [
									'{{WRAPPER}} {{CURRENT_ITEM}} .pafe-hotspot__tooltip p' => 'color: {{VALUE}}',
								],
								'condition' => [ 
									'pafe_hotspot_tooltip_content_type' => 'content',
								]
							]
						);

						$repeater->add_responsive_control(
							'pafe_hotspot_tooltip_content_width',
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
									'unit' => 'px',
									'size' => 195,
								],
								'selectors' => [
									'{{WRAPPER}} {{CURRENT_ITEM}} .pafe-hotspot__tooltip' => 'width: {{SIZE}}{{UNIT}} !important; max-width: none !important;',
								],
							]
						);

						$repeater->add_control(
							'pafe_hotspot_tooltip_background_color',
							[
								'label' => __( 'Background Color', 'pafe' ),
								'type' => \Elementor\Controls_Manager::COLOR,
								'scheme' => [
									'type' => \Elementor\Core\Schemes\Color::get_type(),
									'value' => \Elementor\Core\Schemes\Color::COLOR_1,
								],
								'default' => '#fff', 
								'selectors' => [
									'{{WRAPPER}} {{CURRENT_ITEM}} .pafe-hotspot__tooltip' => 'background-color: {{VALUE}}',
									'{{WRAPPER}} {{CURRENT_ITEM}} .pafe-hotspot__tooltip.pafe-hotspot__tooltip-top::after' => 'border-top-color: {{VALUE}}',
									'{{WRAPPER}} {{CURRENT_ITEM}} .pafe-hotspot__tooltip.pafe-hotspot__tooltip-bottom::after' => 'border-bottom-color: {{VALUE}}',
									'{{WRAPPER}} {{CURRENT_ITEM}} .pafe-hotspot__tooltip.pafe-hotspot__tooltip-left::after' => 'border-left-color: {{VALUE}}',
									'{{WRAPPER}} {{CURRENT_ITEM}} .pafe-hotspot__tooltip.pafe-hotspot__tooltip-right::after' => 'border-right-color: {{VALUE}}',
								],

							]
						);

						$repeater->add_group_control(
							\Elementor\Group_Control_Typography::get_type(),
							[
								'name' => 'pafe_hotspot_tooltip_label_typography',
								'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .pafe-hotspot__tooltip p',
								'scheme' => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_4,
								'condition' => [ 
									'pafe_hotspot_tooltip_content_type' => 'content', 
								]
							]
						);

					$repeater->end_controls_tab();
					$repeater->end_controls_tabs();

				$this->add_control(
					'pafe_hotspot_list',
					[
						'label' => __( '', 'pafe' ),
						'type' => \Elementor\Controls_Manager::REPEATER,
						'fields' => $repeater->get_controls(),
						'default' => [
							[
								'pafe_hotspot_marker_title' => __( 'Marker Name', 'pafe' ),
								'pafe_hotspot_marker_tooltip_content' => __( 'Item content. Click the edit button to change this text.', 'pafe' ),
							],
						],
						'title_field' => '{{{ pafe_hotspot_marker_title }}}',
					]
				);

			$this->end_controls_section();

		}

	/** Marker Control Section**/


	protected function render() {
		$settings = $this->get_settings_for_display();

		echo \Elementor\Group_Control_Image_Size::get_attachment_image_html( $settings, 'pafe_hotspot_thumbnail', 'pafe_hotspot_image_upload' );
		?>

		<?php if ( $settings['pafe_hotspot_list'] ) {
			echo '<div>';
			foreach (  $settings['pafe_hotspot_list'] as $item ) { ?>
				<div class="pafe-hotspot__marker-wrapper elementor-repeater-item-<?php echo esc_attr( $item['_id'] ); ?>" data-pafe-hotspot-tippy-content='<?php echo $content;?>' data-pafe-hotspot-tippy-option='<?php echo  json_encode( $tippy_options );?>' >
					<div class="pafe-hotspot__marker-icon" data-pafe-hotspot-trigger='<?php echo $item['pafe_hotspot_tooltip_trigger'];?>'>
						<?php if ($item['pafe_hotspot_marker_type'] == 'icon') {?>
							 <div class="pafe-hotspot__marker-icon-icon">

								<?php if ($item['pafe_hotspot_marker_type_icon_view'] == 'stacked') { echo
									'<div class="pafe-hotspot-icon-view-stacked">';
										 \Elementor\Icons_Manager::render_icon( $item['pafe_hotspot_marker_type_icon'], [ 'aria-hidden' => 'true' ] ); 
									 echo '</div>';	
								}
									  elseif ($item['pafe_hotspot_marker_type_icon_view'] == 'default') { echo
									'<div class="pafe-hotspot-icon-view-default">';
										 \Elementor\Icons_Manager::render_icon( $item['pafe_hotspot_marker_type_icon'], [ 'aria-hidden' => 'true' ] ); 
									echo '</div>';	
								}
									  elseif ($item['pafe_hotspot_marker_type_icon_view'] == 'framed') { echo
									'<div class="pafe-hotspot-icon-view-framed">';
										 \Elementor\Icons_Manager::render_icon( $item['pafe_hotspot_marker_type_icon'], [ 'aria-hidden' => 'true' ] ); 
									echo '</div>';
								}
							?>
							 </div>
							<?php } elseif ($item['pafe_hotspot_marker_type'] == 'image') {?>
							 <div class="pafe-hopspot__marker-icon-image" > <?php echo '<img src="' . $item['pafe_hotspot_marker_type_image']['url'] . '">';?></div>
							<?php } elseif ($item['pafe_hotspot_marker_type'] == 'text') {?>
							 <div class="pafe-hopspot__marker-icon-text"> <?php echo $item['pafe_hotspot_marker_type_text']; ?></div><?php
							}
							 ?>
					  	<div class="pafe-hotspot__tooltip pafe-hotspot__tooltip-<?php echo $item['pafe_hotspot_tooltip_placement'];?>" style='transition: all <?php echo $item['pafe_hotspot_tooltip_duration']?>ms;'>
					  			<?php 
					  				if ($item['pafe_hotspot_tooltip_content_type'] == 'content') { echo $item['pafe_hotspot_tooltip_content_wysiwyg'];}
					  				elseif ($item['pafe_hotspot_tooltip_content_type'] == 'image') { echo '<img src="' . $item['pafe_hotspot_tooltip_content_image']['url'] . '">';}
					  				elseif ($item['pafe_hotspot_tooltip_content_type'] == 'saved_template') { echo $item['pafe_hotspot_tooltip_content_saved_template'];};
					  			 ?>
					  	</div>
				  	</div>
				</div>	
 			<?php }
			echo '</div>';
		}
	}

} 
