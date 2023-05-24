<?php

class PAFE_Image_Carousel_Multiple_Custom_Urls extends \Elementor\Widget_Base {

	public function __construct() {
		parent::__construct();
		$this->init_control();
	}

	public function get_name() {
		return 'pafe-image-carousel-multiple-custom-urls';
	}

	public function pafe_register_controls( $element, $section_id, $args ) {
		static $sections = [
			'section_image_carousel',
		];

		if ( ! in_array( $section_id, $sections ) ) {
			return;
		}

		$element->add_control(
			'pafe_image_carousel_multiple_custom_urls',
			[
				'label' => '',
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => __( 'PAFE Image Carousel Multiple Custom Urls', 'pafe' ),
			]
		);

		$element->add_control(
			'pafe_image_carousel_multiple_custom_urls_note',
			[
				'label' => '',
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => __( 'When adding/editing the Image Carousel element in Elementor, set Link to Custom URL. Then in the field that appears, enter each of your URLs, separated by commas.<br><br> Note that currently effect are not visible in edit/preview mode & can only be viewed on the frontend. ', 'pafe' ),
				'content_classes' => 'elementor-control-field-description pafe-image-carousel-multiple-custom-urls-description',
			]
		);
	}

	protected function init_control() {
		add_action( 'elementor/element/before_section_end', [ $this, 'pafe_register_controls' ], 10, 3 );
	}

}
