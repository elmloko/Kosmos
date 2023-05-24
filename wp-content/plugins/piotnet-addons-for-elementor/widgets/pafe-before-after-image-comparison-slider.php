<?php

class PAFE_Before_After_Image_Comparison_Slider extends \Elementor\Widget_Base {

	public function get_name() {
		return 'pafe-before-after-image-comparison-slider';
	}

	public function get_title() {
		return __( 'PAFE Before After Image Comparison Slider', 'pafe' );
	}

	public function get_icon() {
		return 'eicon-slideshow';
	}

	public function get_categories() {
		return [ 'pafe-free-widgets' ];
	}

	public function get_keywords() {
		return [ 'comparison', 'before', 'after', 'slider' ];
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
			'pafe_before_after_image_comparison_slider_section',
			[
				'label' => __( 'Images', 'pafe' ),
			]
		);

		$this->add_control(
			'pafe_before_after_image_comparison_slider_image_before',
			[
				'label' => __( 'Before Image', 'elementor' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
			]
		);

		$this->add_control(
			'pafe_before_after_image_comparison_slider_image_after',
			[
				'label' => __( 'After Image', 'elementor' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'pafe_before_after_image_comparison_slider_section_options',
			[
				'label' => __( 'Options', 'pafe' ),
			]
		);

		$this->add_control(
			'pafe_before_after_image_comparison_slider_image_offset_percent',
			[
				'label' => __( 'How much of the before image is visible when the page loads (%)', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 50,
				],
			]
		);

		$this->add_control(
			'pafe_before_after_image_comparison_slider_image_orientation',
			[
				'label' => __( 'Orientation', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'horizontal' => 'Horizontal',
					'vertical' => 'Vertical',
				],
				'default' => 'horizontal',
			]
		);

		$this->add_control(
			'pafe_before_after_image_comparison_slider_image_before_label',
			[
				'label' => __( 'Before Label', 'elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => 'Before',
			]
		);

		$this->add_control(
			'pafe_before_after_image_comparison_slider_image_after_label',
			[
				'label' => __( 'After Label', 'elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => 'After',
			]
		);

		$this->add_control(
			'pafe_before_after_image_comparison_slider_image_overlay',
			[
				'label' => __( 'Overlay', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'pafe_before_after_image_comparison_slider_image_move_slider_on_hover',
			[
				'label' => __( 'Move slider on mouse hover', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$this->add_control(
			'pafe_before_after_image_comparison_slider_image_move_with_handle_only',
			[
				'label' => __( 'Move with handle only', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'pafe_before_after_image_comparison_slider_image_click_to_move',
			[
				'label' => __( 'Click to move', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {

		$settings = $this->get_settings_for_display();

		if ( !empty($settings['pafe_before_after_image_comparison_slider_image_before']) && !empty($settings['pafe_before_after_image_comparison_slider_image_after']) ) {

		?>	
			<div class="pafe-before-after-image-comparison-slider" data-pafe-before-after-image-comparison-slider data-pafe-before-after-image-offset-percent="<?php echo $settings['pafe_before_after_image_comparison_slider_image_offset_percent']['size'] / 100; ?>" data-pafe-before-after-image-orientation="<?php echo $settings['pafe_before_after_image_comparison_slider_image_orientation']; ?>" data-pafe-before-after-image-before-label="<?php echo $settings['pafe_before_after_image_comparison_slider_image_before_label']; ?>" data-pafe-before-after-image-after-label="<?php echo $settings['pafe_before_after_image_comparison_slider_image_after_label']; ?>" data-pafe-before-after-image-overlay="<?php echo $settings['pafe_before_after_image_comparison_slider_image_overlay']; ?>" data-pafe-before-after-image-move-slider-on-hover="<?php echo $settings['pafe_before_after_image_comparison_slider_image_move_slider_on_hover']; ?>" data-pafe-before-after-image-move-with-handle-only="<?php echo $settings['pafe_before_after_image_comparison_slider_image_move_with_handle_only']; ?>" data-pafe-before-after-image-click-to-move="<?php echo $settings['pafe_before_after_image_comparison_slider_image_click_to_move']; ?>">
	            <img class="pafe-before-after-image-comparison-slider__item pafe-before-after-image-comparison-slider__item--before" src="<?php echo $settings['pafe_before_after_image_comparison_slider_image_before']['url'] ?>">
	            <img class="pafe-before-after-image-comparison-slider__item pafe-before-after-image-comparison-slider__item--after" src="<?php echo $settings['pafe_before_after_image_comparison_slider_image_after']['url'] ?>">
      		</div>
        <?php

		}

	}
}