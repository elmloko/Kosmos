<?php

class PAFE_Progress_Bar extends \Elementor\Widget_Base {

	public function get_name() {
		return 'pafe-progressbar';
	}

	public function get_title() {
		return __( 'PAFE Progress Bar', 'pafe' );
	}

	public function get_icon() {
		return 'fas fa-circle-notch';
	}

	public function get_categories() {
		return [ 'pafe-free-widgets' ];
	}

	public function get_keywords() {
		return [ 'progressbar' ];
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
	

/** Insert Content Section**/
	protected function _register_controls() {
		$this->start_controls_section(
			'pafe_progressbar',
			[
				'label' => __( 'Progress Bar Content', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

			// $this->add_control(
			// 	'pafe_progressbar_type',
			// 	[
			// 		'label' => __( 'Type', 'pafe' ),
			// 		'type' => \Elementor\Controls_Manager::SELECT,
			// 		'default' => 'circle',
			// 		'options' => [
			// 			'circle'  => __( 'Circle', 'pafe' ),
			// 			'line'  => __( 'Line', 'pafe' ),
			// 		],
			// 	]
			// );

			$this->add_control(
				'pafe_progressbar_percentage',
				[
					'label' => __( 'Percentage', 'pafe' ),
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
				
				]
			);

			$this->add_control(
				'show_label',
				[
					'label' => __( 'Show Label', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label_on' => __( 'Yes', 'pafe' ),
					'label_off' => __( 'No', 'pafe' ),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);
			
			$this->add_control(
				'pafe_progressbar_animation_duration',
				[
					'label' => __( 'Duration', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 8000,
							'step' => 100,
						],
					],
					'default' => [
						'unit' => 'px',
						'size' => 2000,
					],
				
				]
			);

		$this->end_controls_section();

/* Edit Content Style Section*/
		
		$this->start_controls_section(
			'pafe_progressbar_bar_style_section',
			[
				'label' => __( 'Progress Bar Style', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'pafe_progressbar_alignment',
			[
				'label' => __( 'Alignment', 'pafe' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'pafe' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'pafe' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'pafe' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'center',
				'toggle' => true,
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'progressbar_label_typography',
				'selector' => '{{WRAPPER}} .pafe-progressbar__label',
				'scheme' => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
			]
		);
		
		$this->add_control(
			'pafe_progressbar_label_color',
			[
				'label' => __( 'Label Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#27140d',
				'selectors' => [
					'{{WRAPPER}} .pafe-progressbar-percentage-content' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'pafe_progressbar_size',
			[
				'label' => __( 'Size', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 500,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 200,
				],
				'selectors' => [
					'{{WRAPPER}} .pafe-progressbar' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			
			]
		);

		$this->add_control(
			'pafe_progressbar_background_color',
			[
				'label' => __( 'Background color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Core\Schemes\Color::get_type(),
					'value' => \Elementor\Core\Schemes\Color::COLOR_1,
				],
				'default' => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}} .pafe-progressbar-circle-trail' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'pafe_progressbar_strokewidth',
			[
				'label' => __( 'Stroke Width', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 2,
						'max' => 15,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .pafe-progressbar-circle-stroke' => 'border-width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'pafe_progressbar_strokecolor',
				[
					'label' => __( 'Stroke Color', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'default' => '#3BB5ED',
					'selectors' => [
						'{{WRAPPER}} .pafe-progressbar-circle-stroke' => 'border-color: {{VALUE}}',
					],
				]
		);

		$this->add_control(
			'pafe_progressbar_trailwidth',
			[
				'label' => __( 'Trail Width', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 2,
						'max' => 15,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .pafe-progressbar-circle-trail' => 'border-width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'pafe_progressbar_trailcolor',
			[
				'label' => __( 'Trail Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#D6F0FD',
				'selectors' => [
					'{{WRAPPER}} .pafe-progressbar-circle-trail' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();

	}


	protected function render() {
		$settings = $this->get_settings_for_display();
		$options = [
			'value' => $settings['pafe_progressbar_percentage']['size'],
			'unit'	=> $settings['pafe_progressbar_percentage']['unit'],
			'duration' => $settings['pafe_progressbar_animation_duration']['size'],
			'size' => $settings['pafe_progressbar_size']['size'],
		];
		?>
		
		<div class="pafe-progressbar-container<?php if ($settings['pafe_progressbar_alignment']=="left") {echo ' left';} if ($settings['pafe_progressbar_alignment']=="center") {echo ' center';} if ($settings['pafe_progressbar_alignment']=="right") {echo ' right';} ?>">
			<div class="pafe-progressbar<?php if ($settings['show_label']!="yes") { echo ' pafe-progressbar--hidden-label';}?>" data-pafe-progressbar-option='<?php echo json_encode( $options ); ?>' >
				<div class="pafe-progressbar__inner" style="position: relative; ">
					<div class="pafe-progressbar-circle">
						<div class="pafe-progressbar__content">
							<div class="pafe-progressbar-border" <?php if ($settings['pafe_progressbar_percentage']['size'] > 50) { ?>style="clip-path: inset(0);" <?php } ?>>
								<div class="pafe-progressbar-circle-left pafe-progressbar-circle-stroke" style="transform: rotate(<?php echo ($settings['pafe_progressbar_percentage']['size']*18/5)?>deg);">
								</div>
								<?php if ($settings['pafe_progressbar_percentage']['size'] <= 50): ?>
									<div class="pafe-progressbar-circle-right pafe-progressbar-circle-stroke" style="visibility: hidden;">
									</div>
									<?php elseif ($settings['pafe_progressbar_percentage']['size'] > 50):?>
										<div class="pafe-progressbar-circle-right pafe-progressbar-circle-stroke" style="visibility: visible;">
										</div>
								<?php endif; ?>
							</div>
							<div class="pafe-progressbar-circle-trail">
							</div>
							<div class="pafe-progressbar__label">
								<div class="pafe-progressbar-percentage-content">
									<div class="pafe-progressbar-percentage-size"><?php echo$settings['pafe_progressbar_percentage']['size']?></div>
									<div class="pafe-progressbar-percentage-unit"><?php echo$settings['pafe_progressbar_percentage']['unit']?></div>
								</div>
							</div>
						</div>
					</div>				
				</div>
			</div>
		</div>

		<?php
	}

	
}
