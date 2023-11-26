<?php

class PAFE_Switch_Content extends \Elementor\Widget_Base {

	public function get_name() {
		return 'pafe-switch-content';
	}

	public function get_title() {
		return __( 'PAFE Switch Content', 'pafe' );
	}

	public function get_icon() {
		return 'fa fa-toggle-on';
	}

	public function get_categories() {
		return [ 'pafe-free-widgets' ];
	}

	public function get_keywords() {
		return [ 'switch', 'content' ];
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
			'pafe_switch_content_primary_section',
			[
				'label' => __( 'Section 1', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'pafe_switch_content_primary_label',
			[
				'label' => __( 'Title', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				
			]
		);

		$this->add_control(
			'pafe_switch_content_primary_content_type',
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

		$this->add_control(
			'pafe_switch_content_primary_content_wysiwyg',
			[ 
				'label' => __( 'Text Editor','pafe' ),
				'type' => \Elementor\Controls_Manager::WYSIWYG,
				'condition' => [
					'pafe_switch_content_primary_content_type' => 'content'	
				]
			]
		);

		$this->add_control(
			'pafe_switch_content_primary_content_image',
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
					'pafe_switch_content_primary_content_type' => 'image'	
				]
			]
		);

		$this->add_control(
			'pafe_switch_content_primary_content_saved_template',
			[
				'label' => __( 'Shortcode', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Shortcode', 'pafe' ),
				'condition' => [
					'pafe_switch_content_primary_content_type' => 'saved_template'	
				]
			]
		);

		$this->end_controls_section();
	/*Secondary section*/	
		$this->start_controls_section(
			'pafe_switch_content_secondary_section',
			[
				'label' => __( 'Section 2', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'pafe_switch_content_secondary_label',
			[
				'label' => __( 'Title', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
			]
		);

		$this->add_control(
			'pafe_switch_content_secondary_content_type',
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

		$this->add_control(
			'pafe_switch_content_secondary_content_wysiwyg',
			[ 
				'label' => __( 'Text Editor','pafe' ),
				'type' => \Elementor\Controls_Manager::WYSIWYG,
				'condition' => [
					'pafe_switch_content_secondary_content_type' => 'content'	
				]
			]
		);

		$this->add_control(
			'pafe_switch_content_secondary_content_image',
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
					'pafe_switch_content_secondary_content_type' => 'image'	
				]
			]
		);

		$this->add_control(
			'pafe_switch_content_secondary_content_saved_template',
			[
				'label' => __( 'Shortcode', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Shortcode', 'pafe' ),
				'condition' => [
					'pafe_switch_content_secondary_content_type' => 'saved_template'	
				]
			]
		);

		$this->end_controls_section();

		/* Style TAB */
		
		$this->start_controls_section(
			'pafe_switch_content_style_switch_button_section',
			[
				'label' => __( 'Switch Button', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'pafe_switch_content_style_switch_button_deactive_color',
			[
				'label' => __( 'Box Around Color 1', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
                ],
				'default' => '#ccc',
				'selectors' => [
					'{{WRAPPER}} .pafe-switch-content__button-slider' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'pafe_switch_content_style_switch_button_active_color',
			[
				'label' => __( 'Box Around Color 2', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
                ],
				'default' => '#2196F3',
				'selectors' => [
					'{{WRAPPER}} input:checked + .pafe-switch-content__button-slider' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'pafe_switch_content_style_switch_slider_color',
			[
				'label' => __( 'Switch Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
                ],
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .pafe-switch-content__button-slider:before' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'pafe_switch_content_style_switch_button_width',
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
					'size' => 15,
				],
				'selectors' => [
					'{{WRAPPER}} .pafe-switch-content__button-switch' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'pafe_switch_content_style_switch_button_spacing',
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
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .pafe-switch-content__button-switch' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'pafe_switch_content_style_switch_button_margin_bottom',
			[
				'label' => __( 'Margin Bottom', 'pafe' ),
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
					'{{WRAPPER}} .pafe-switch-content__button' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'pafe_switch_content_style_switch_button_border_radius',
			[
				'label' => __( 'Box Around Border Radius', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 100,
				],
				'selectors' => [
					'{{WRAPPER}} .pafe-switch-content__button-slider.round' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'pafe_switch_content_style_switch_button_slider_border_radius',
			[
				'label' => __( 'Switch Border Radius', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100, 
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 50,
				],
				'selectors' => [
					'{{WRAPPER}} .pafe-switch-content__button-slider.round:before' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'pafe_switch_content_label_section',
			[
				'label' => __( 'Title', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs(
			'label_tabs'
		);

		$this->start_controls_tab( 
			'label_primary_tabs',
			[
				'label' => __( 'Section 1', 'pafe' ),
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'label_typography',
				'selector' => '{{WRAPPER}} .pafe-switch-content-primary-label',
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_SECONDARY,
                ],
			]
		);

		$this->add_control(
			'pafe_switch_content_primary_label_color',
			[
				'label' => __( 'Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
                ],
				'default' => '#000', 
				'selectors' => [
					'{{WRAPPER}} .pafe-switch-content-primary-label' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 
			'label_secondary_tabs',
			[
				'label' => __( 'Section 2', 'pafe' ),
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'label_secondary_typography',
				'selector' => '{{WRAPPER}} .pafe-switch-content-secondary-label',
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_SECONDARY,
                ],
			]
		);
 
		$this->add_control(
			'pafe_switch_content_secondary_label_color',
			[
				'label' => __( 'Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
                ],
				'default' => '#000', 
				'selectors' => [
					'{{WRAPPER}} .pafe-switch-content-secondary-label' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'pafe_switch_content_section',
			[
				'label' => __( 'Content', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs(
			'content_tabs'
		);

		$this->start_controls_tab( 
			'content_primary_tabs',
			[
				'label' => __( 'Section 1', 'pafe' ),
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'content_typography',
				'selector' => '{{WRAPPER}} .pafe-switch_content-primary-content-wysiwyg',
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_TEXT,
                ],
			]
		);

		$this->add_control(
			'pafe_switch_content_primary_content_color',
			[
				'label' => __( 'Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
                ],
				'default' => '#000', 
				'selectors' => [
					'{{WRAPPER}} .pafe-switch_content-primary-content-wysiwyg' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 
			'content_secondary_tabs',
			[
				'label' => __( 'Section 2', 'pafe' ),
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'content_secondary_typography',
				'selector' => '{{WRAPPER}} .pafe-switch_content-secondary-content-wysiwyg',
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_TEXT,
                ],
			]
		);
 
		$this->add_control(
			'pafe_switch_content_secondary_content_color',
			[
				'label' => __( 'Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
                ],
				'default' => '#000', 
				'selectors' => [
					'{{WRAPPER}} .pafe-switch_content-secondary-content-wysiwyg' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();	
 
		$this->end_controls_section();
	}
	protected function render() {

		$settings = $this->get_settings_for_display();
		?>
	<div class="pafe-switch-content" data-pafe-switch-content> 
		<div class="pafe-switch-content__button" data-pafe-switch-content-button>
			<?php 
				if (!empty($settings['pafe_switch_content_primary_label'])) {
					?>
						<div class="pafe-switch-content-primary-label" data-pafe-switch-content-primary-label>
							<?php echo $settings['pafe_switch_content_primary_label']; ?>
						</div> 
					<?php
				}
			?>	
			<label class="pafe-switch-content__button-switch" data-pafe-switch-content-button-switch>
			    <input type="checkbox" class="pafe-switch-content__button-checkbox" data-pafe-switch-content-button-checkbox></input>
			    <span class="pafe-switch-content__button-slider round"></span>
			 </label> 
			 <?php 
				if (!empty($settings['pafe_switch_content_secondary_label'])) {
					?>
						<div class="pafe-switch-content-secondary-label" data-pafe-switch-content-secondary-label>
							<?php echo $settings['pafe_switch_content_secondary_label']; ?>
						</div> 
					<?php
				}
			?>	
		</div>
		<div class="pafe-switch-content__inner" data-pafe-switch-content>			
			<div class="pafe-switch-content-primary" data-pafe-switch-content-primary>		
				<?php 
					if (!empty($settings['pafe_switch_content_primary_content_wysiwyg']) && $settings['pafe_switch_content_primary_content_type' ] == 'content' ) {
						?>
							<div class="pafe-switch_content-primary-content-wysiwyg" data-pafe-switch-content-primary-content-wysiwyg>
								<?php echo $settings['pafe_switch_content_primary_content_wysiwyg']; ?>
							</div>
						<?php
					}
				?>
		
				<?php 
					if (!empty($settings['pafe_switch_content_primary_content_saved_template']) && $settings['pafe_switch_content_primary_content_type' ] == 'saved_template' ) {
						?>
							<div class="pafe-switch_content-primary-content__saved-template" data-pafe-switch-content-primary-content-saved-template>
								<?php echo do_shortcode($settings["pafe_switch_content_primary_content_saved_template"]); ?>
							</div>
						<?php
					}
				?>
		
				<?php
					if (!empty($settings['pafe_switch_content_primary_content_image'] ) && $settings['pafe_switch_content_primary_content_type' ] == 'image') {
						if ( !empty($settings['pafe_switch_content_primary_content_image']['url'] ) ) {
							?>
								<img class="pafe-switch-content-primary-content-image"  src="<?php echo $settings['pafe_switch_content_primary_content_image']['url']; ?>" alt="" data-pafe-switch-content-primary-image>
							<?php
						}		
					}
				?>
			</div>
			<!-- Content Secondary  -->
			<div class="pafe-switch-content-secondary" data-pafe-switch-content-secondary>				
				<?php 
					if (!empty($settings['pafe_switch_content_secondary_content_wysiwyg']) && $settings['pafe_switch_content_secondary_content_type' ] == 'content' ) {
						?>
							<div class="pafe-switch_content-secondary-content-wysiwyg" data-pafe-switch-content-secondary-content-wysiwyg>
								<?php echo $settings['pafe_switch_content_secondary_content_wysiwyg']; ?>
							</div>
						<?php
					}
				?>
		
				<?php 
					if (!empty($settings['pafe_switch_content_secondary_content_saved_template']) && $settings['pafe_switch_content_secondary_content_type' ] == 'saved_template' ) {
						?>
							<div class="pafe-switch_content-secondary-content__saved-template" data-pafe-switch-content-secondary-content-saved-template>
								<?php echo do_shortcode($settings["pafe_switch_content_secondary_content_saved_template"]); ?>
							</div>
						<?php
					}
				?>
		
				<?php
					if (!empty($settings['pafe_switch_content_secondary_content_image'] ) && $settings['pafe_switch_content_secondary_content_type' ] == 'image') {
						if ( !empty($settings['pafe_switch_content_secondary_content_image']['url'] ) ) {
							?>
								<img class="pafe-switch-content-secondary-content-image" src="<?php echo $settings['pafe_switch_content_secondary_content_image']['url']; ?>" alt="" data-pafe-switch-content-secondary-content-image>
							<?php
						}		
					}
				?>
			</div>
		</div>
	</div>		
		<?php
	}
    public function add_wpml_support() {
        add_filter( 'wpml_elementor_widgets_to_translate', [ $this, 'wpml_widgets_to_translate_filter' ] );
    }

    public function wpml_widgets_to_translate_filter( $widgets ) {
        $widgets[ $this->get_name() ] = [
            'conditions' => [ 'widgetType' => $this->get_name() ],
            'fields'     => [
                [
                    'field'       => 'pafe_switch_content_primary_label',
                    'label' => __( 'Title', 'pafe' ),
                    'editor_type' => 'LINE'
                ],
                [
                    'field'       => 'pafe_switch_content_secondary_label',
                    'label' => __( 'Title', 'pafe' ),
                    'editor_type' => 'LINE'
                ],
            ],
        ];

        return $widgets;
    }
}
