<?php
/**
 * Premium Persons.
 */

namespace PremiumAddons\Widgets;

// Elementor Classes.
use Elementor\Widget_Base;
use Elementor\Utils;
use Elementor\Control_Media;
use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Repeater;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Border;

// PremiumAddons Classes.
use PremiumAddons\Includes\Helper_Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

/**
 * Class Premium_Person
 */
class Premium_Person extends Widget_Base {

	/**
	 * Retrieve Widget Name.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_name() {
		return 'premium-addon-person';
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return __( 'Team Members', 'premium-addons-for-elementor' );
	}

	/**
	 * Retrieve Widget Icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string widget icon.
	 */
	public function get_icon() {
		return 'pa-person';
	}

	/**
	 * Retrieve Widget Dependent CSS.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array CSS style handles.
	 */
	public function get_style_depends() {
		return array(
			'pa-slick',
			'premium-addons',
		);
	}

	/**
	 * Retrieve Widget Dependent JS.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array JS script handles.
	 */
	public function get_script_depends() {
		return array(
			'imagesloaded',
			'pa-slick',
			'premium-addons',
		);
	}

	/**
	 * Retrieve Widget Categories.
	 *
	 * @since 1.5.1
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( 'premium-elements' );
	}

	/**
	 * Retrieve Widget Keywords.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget keywords.
	 */
	public function get_keywords() {
		return array( 'pa', 'premium', 'person', 'carousel', 'slider', 'group' );
	}

	/**
	 * Retrieve Widget Support URL.
	 *
	 * @access public
	 *
	 * @return string support URL.
	 */
	public function get_custom_help_url() {
		return 'https://premiumaddons.com/support/';
	}

	/**
	 * Register Persons controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		$this->start_controls_section(
			'premium_person_general_settings',
			array(
				'label' => __( 'General Settings', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'multiple',
			array(
				'label'       => __( 'Multiple Member', 'premium-addons-for-elementor' ),
				'description' => __( 'Enable this option if you need to add multiple persons', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'premium_person_style',
			array(
				'label'       => __( 'Style', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'style1',
				'options'     => array(
					'style1' => __( 'Style 1', 'premium-addons-for-elementor' ),
					'style2' => __( 'Style 2', 'premium-addons-for-elementor' ),
					'style3' => __( 'Style 3', 'premium-addons-for-elementor' ),
				),
				'label_block' => true,
				'render_type' => 'template',
			)
		);

		$this->add_control(
			'title_rotate',
			array(
				'label'        => __( 'Title Rotate', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SELECT,
				'options'      => array(
					'cw'  => __( '90 Degrees', 'premium-addons-for-elementor' ),
					'ccw' => __( '-90 Degrees', 'premium-addons-for-elementor' ),
				),
				'default'      => 'cw',
				'prefix_class' => 'premium-persons-title-',
				'label_block'  => true,
				'condition'    => array(
					'premium_person_style' => 'style3',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'    => 'thumbnail',
				'default' => 'full',
			)
		);

		$this->add_responsive_control(
			'premium_person_image_width',
			array(
				'label'       => __( 'Width', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'description' => __( 'Enter image width in (PX, EM, %), default is 100%', 'premium-addons-for-elementor' ),
				'size_units'  => array( 'px', 'em', '%', 'custom' ),
				'range'       => array(
					'px' => array(
						'min' => 1,
						'max' => 800,
					),
					'em' => array(
						'min' => 1,
						'max' => 50,
					),
				),
				'default'     => array(
					'unit' => '%',
					'size' => '100',
				),
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}} .premium-persons-container' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_person_align',
			array(
				'label'     => __( 'Alignment', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => __( 'Left', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'     => array(
						'title' => __( 'Center', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end'   => array(
						'title' => __( 'Right', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-container' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_person_hover_image_effect',
			array(
				'label'       => __( 'Hover Effect', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'none'      => __( 'None', 'premium-addons-for-elementor' ),
					'zoomin'    => __( 'Zoom In', 'premium-addons-for-elementor' ),
					'zoomout'   => __( 'Zoom Out', 'premium-addons-for-elementor' ),
					'scale'     => __( 'Scale', 'premium-addons-for-elementor' ),
					'grayscale' => __( 'Grayscale', 'premium-addons-for-elementor' ),
					'blur'      => __( 'Blur', 'premium-addons-for-elementor' ),
					'bright'    => __( 'Bright', 'premium-addons-for-elementor' ),
					'sepia'     => __( 'Sepia', 'premium-addons-for-elementor' ),
					'trans'     => __( 'Translate', 'premium-addons-for-elementor' ),
				),
				'default'     => 'zoomin',
				'label_block' => true,
			)
		);

		$this->add_responsive_control(
			'premium_person_text_align',
			array(
				'label'     => __( 'Content Alignment', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => __( 'Left', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'left',
				'selectors' => array(
					'{{WRAPPER}} .premium-person-info' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_person_name_heading',
			array(
				'label'       => __( 'Name Tag', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'h2',
				'options'     => array(
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'div'  => 'div',
					'span' => 'span',
					'p'    => 'p',
				),
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_person_title_heading',
			array(
				'label'       => __( 'Title Tag', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'h4',
				'options'     => array(
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'div'  => 'div',
					'span' => 'span',
					'p'    => 'p',
				),
				'label_block' => true,
			)
		);

		$this->add_responsive_control(
			'persons_per_row',
			array(
				'label'              => __( 'Members/Row', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::SELECT,
				'options'            => array(
					'100%'    => __( '1 Column', 'premium-addons-for-elementor' ),
					'50%'     => __( '2 Columns', 'premium-addons-for-elementor' ),
					'33.33%'  => __( '3 Columns', 'premium-addons-for-elementor' ),
					'25%'     => __( '4 Columns', 'premium-addons-for-elementor' ),
					'20%'     => __( '5 Columns', 'premium-addons-for-elementor' ),
					'16.667%' => __( '6 Columns', 'premium-addons-for-elementor' ),
				),
				'default'            => '33.33%',
				'tablet_default'     => '100%',
				'mobile_default'     => '100%',
				'render_type'        => 'template',
				'selectors'          => array(
					'{{WRAPPER}} .premium-person-container' => 'width: {{VALUE}}',
				),
				'condition'          => array(
					'multiple' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$this->add_responsive_control(
			'spacing',
			array(
				'label'      => __( 'Spacing', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'default'    => array(
					'top'    => 5,
					'right'  => 5,
					'bottom' => 5,
					'left'   => 5,
				),
				'condition'  => array(
					'multiple' => 'yes',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-person-container' => 'padding: 0 {{RIGHT}}{{UNIT}} 0 {{LEFT}}{{UNIT}}; margin: {{TOP}}{{UNIT}} 0 {{BOTTOM}}{{UNIT}} 0',
					' {{WRAPPER}} .premium-person-style1 .premium-person-info' => 'left: {{LEFT}}{{UNIT}}; right: {{RIGHT}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'multiple_equal_height',
			array(
				'label'       => __( 'Equal Height', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'default'     => 'yes',
				'description' => __( 'This option searches for the image with the largest height and applies that height to the other images', 'premium-addons-for-elementor' ),
				'condition'   => array(
					'multiple'            => 'yes',
					'custom_height[size]' => '',
				),
			)
		);

		$this->add_responsive_control(
			'custom_height',
			array(
				'label'      => __( 'Custom Height', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'custom' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 500,
					),
					'em' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'condition'  => array(
					'multiple' => 'yes',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-person-image-container img' => 'height: {{SIZE}}{{UNIT}} !important;',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_person_settings',
			array(
				'label'     => __( 'Single Member Settings', 'premium-addons-for-elementor' ),
				'condition' => array(
					'multiple!' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_person_image',
			array(
				'label'       => __( 'Image', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::MEDIA,
				'dynamic'     => array( 'active' => true ),
				'default'     => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_person_name',
			array(
				'label'       => __( 'Name', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'default'     => 'John Frank',
				'separator'   => 'before',
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_person_title',
			array(
				'label'       => __( 'Title', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'default'     => __( 'Developer', 'premium-addons-for-elementor' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_person_content',
			array(
				'label'   => __( 'Description', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::WYSIWYG,
				'dynamic' => array( 'active' => true ),
				'default' => __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_person_social_enable',
			array(
				'label'     => __( 'Enable Social Icons', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'premium_person_facebook',
			array(
				'label'       => __( 'Facebook', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'default'     => '#',
				'label_block' => true,
				'condition'   => array(
					'premium_person_social_enable' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_person_twitter',
			array(
				'label'       => __( 'Twitter', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'default'     => '#',
				'label_block' => true,
				'condition'   => array(
					'premium_person_social_enable' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_person_linkedin',
			array(
				'label'       => __( 'LinkedIn', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
				'condition'   => array(
					'premium_person_social_enable' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_person_google',
			array(
				'label'       => __( 'Google+', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
				'condition'   => array(
					'premium_person_social_enable' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_person_youtube',
			array(
				'label'       => __( 'YouTube', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
				'condition'   => array(
					'premium_person_social_enable' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_person_instagram',
			array(
				'label'       => __( 'Instagram', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'default'     => '#',
				'label_block' => true,
				'condition'   => array(
					'premium_person_social_enable' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_person_skype',
			array(
				'label'       => __( 'Skype', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
				'condition'   => array(
					'premium_person_social_enable' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_person_pinterest',
			array(
				'label'       => __( 'Pinterest', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
				'condition'   => array(
					'premium_person_social_enable' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_person_dribbble',
			array(
				'label'       => __( 'Dribbble', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'default'     => '#',
				'label_block' => true,
				'condition'   => array(
					'premium_person_social_enable' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_person_behance',
			array(
				'label'       => __( 'Behance', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
				'condition'   => array(
					'premium_person_social_enable' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_person_whatsapp',
			array(
				'label'       => __( 'WhatsApp', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
				'condition'   => array(
					'premium_person_social_enable' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_person_telegram',
			array(
				'label'       => __( 'Telegram', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
				'condition'   => array(
					'premium_person_social_enable' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_person_mail',
			array(
				'label'       => __( 'Email Address', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
				'condition'   => array(
					'premium_person_social_enable' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_person_site',
			array(
				'label'       => __( 'Website', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
				'condition'   => array(
					'premium_person_social_enable' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_person_number',
			array(
				'label'       => __( 'Phone Number', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'description' => __( 'Example: tel: +012 345 678 910', 'premium-addons-for-elementor' ),
				'label_block' => true,
				'condition'   => array(
					'premium_person_social_enable' => 'yes',
				),
			)
		);

		$this->add_control(
			'phone_notice',
			array(
				'raw'             => __( 'Please note that Phone Number icon will show only on mobile devices.', 'premium-addons-for-elementor' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => array(
					'premium_person_social_enable' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'multiple_settings',
			array(
				'label'     => __( 'Multiple Members Settings', 'premium-addons-for-elementor' ),
				'condition' => array(
					'multiple' => 'yes',
				),
			)
		);

		$repeater = new REPEATER();

		$repeater->add_control(
			'multiple_image',
			array(
				'label'   => __( 'Image', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => array( 'active' => true ),
				'default' => array(
					'url' => Utils::get_placeholder_image_src(),
				),
			)
		);

		$repeater->add_control(
			'multiple_name',
			array(
				'label'       => __( 'Name', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'default'     => 'John Frank',
				'separator'   => 'before',
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'multiple_title',
			array(
				'label'       => __( 'Title', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'default'     => __( 'Developer', 'premium-addons-for-elementor' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'multiple_description',
			array(
				'label'   => __( 'Description', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::WYSIWYG,
				'dynamic' => array( 'active' => true ),
				'default' => __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit', 'premium-addons-for-elementor' ),
			)
		);

		$repeater->add_control(
			'multiple_social_enable',
			array(
				'label'     => __( 'Enable Social Icons', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before',
			)
		);

		$repeater->add_control(
			'multiple_facebook',
			array(
				'label'       => __( 'Facebook', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'default'     => '#',
				'label_block' => true,
				'condition'   => array(
					'multiple_social_enable' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'multiple_twitter',
			array(
				'label'       => __( 'Twitter', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'default'     => '#',
				'label_block' => true,
				'condition'   => array(
					'multiple_social_enable' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'multiple_linkedin',
			array(
				'label'       => __( 'LinkedIn', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
				'condition'   => array(
					'multiple_social_enable' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'multiple_google',
			array(
				'label'       => __( 'Google+', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
				'condition'   => array(
					'multiple_social_enable' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'multiple_youtube',
			array(
				'label'       => __( 'YouTube', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
				'condition'   => array(
					'multiple_social_enable' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'multiple_instagram',
			array(
				'label'       => __( 'Instagram', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'default'     => '#',
				'label_block' => true,
				'condition'   => array(
					'multiple_social_enable' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'multiple_skype',
			array(
				'label'       => __( 'Skype', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
				'condition'   => array(
					'multiple_social_enable' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'multiple_pinterest',
			array(
				'label'       => __( 'Pinterest', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
				'condition'   => array(
					'multiple_social_enable' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'multiple_dribbble',
			array(
				'label'       => __( 'Dribbble', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'default'     => '#',
				'label_block' => true,
				'condition'   => array(
					'multiple_social_enable' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'multiple_behance',
			array(
				'label'       => __( 'Behance', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
				'condition'   => array(
					'multiple_social_enable' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'multiple_whatsapp',
			array(
				'label'       => __( 'WhatsApp', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
				'condition'   => array(
					'multiple_social_enable' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'multiple_telegram',
			array(
				'label'       => __( 'Telegram', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
				'condition'   => array(
					'multiple_social_enable' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'multiple_mail',
			array(
				'label'       => __( 'Email Address', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
				'condition'   => array(
					'multiple_social_enable' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'multiple_site',
			array(
				'label'       => __( 'Website', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
				'condition'   => array(
					'multiple_social_enable' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'multiple_number',
			array(
				'label'       => __( 'Phone Number', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'description' => __( 'Example: tel: +012 345 678 910', 'premium-addons-for-elementor' ),
				'label_block' => true,
				'condition'   => array(
					'multiple_social_enable' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'phone_notice',
			array(
				'raw'             => __( 'Please note that Phone Number icon will show only on mobile devices.', 'premium-addons-for-elementor' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => array(
					'multiple_social_enable' => 'yes',
				),
			)
		);

		$this->add_control(
			'multiple_persons',
			array(
				'label'         => __( 'Members', 'premium-addons-for-elementor' ),
				'type'          => Controls_Manager::REPEATER,
				'default'       => array(
					array(
						'multiple_name' => 'John Frank',
					),
					array(
						'multiple_name' => 'John Frank',
					),
					array(
						'multiple_name' => 'John Frank',
					),
				),
				'fields'        => $repeater->get_controls(),
				'title_field'   => '{{{multiple_name}}} - {{{multiple_title}}}',
				'prevent_empty' => false,
			)
		);

		$this->add_control(
			'carousel',
			array(
				'label'              => __( 'Carousel', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'carousel_play',
			array(
				'label'              => __( 'Auto Play', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'condition'          => array(
					'carousel' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'carousel_autoplay_speed',
			array(
				'label'              => __( 'Autoplay Speed', 'premium-addons-for-elementor' ),
				'description'        => __( 'Autoplay Speed means at which time the next slide should come. Set a value in milliseconds (ms)', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::NUMBER,
				'default'            => 5000,
				'condition'          => array(
					'carousel'      => 'yes',
					'carousel_play' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$this->add_responsive_control(
			'carousel_arrows_pos',
			array(
				'label'      => __( 'Arrows Position', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => -100,
						'max' => 100,
					),
					'em' => array(
						'min' => -10,
						'max' => 10,
					),
				),
				'condition'  => array(
					'carousel' => 'yes',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-persons-container a.carousel-arrow.carousel-next' => 'right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .premium-persons-container a.carousel-arrow.carousel-prev' => 'left: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_pa_docs',
			array(
				'label' => __( 'Helpful Documentations', 'premium-addons-for-elementor' ),
			)
		);

		$doc1_url = Helper_Functions::get_campaign_link( 'https://premiumaddons.com/docs/persons-widget-tutorial/', 'editor-page', 'wp-editor', 'get-support' );
		$doc2_url = Helper_Functions::get_campaign_link( 'https://premiumaddons.com/docs/why-im-not-able-to-see-elementor-font-awesome-5-icons-in-premium-add-ons', 'editor-page', 'wp-editor', 'get-support' );

		$this->add_control(
			'doc_1',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf( '<a href="%s" target="_blank">%s</a>', $doc1_url, __( 'Getting started »', 'premium-addons-for-elementor' ) ),
				'content_classes' => 'editor-pa-doc',
			)
		);

		$this->add_control(
			'doc_2',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf( '<a href="%s" target="_blank">%s</a>', $doc2_url, __( 'I\'m not able to see the social icons in the widget »', 'premium-addons-for-elementor' ) ),
				'content_classes' => 'editor-pa-doc',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_person_image_style',
			array(
				'label' => __( 'Image', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'image_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-person-image-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'premium_person_style' => 'style2',
				),
			)
		);

		$this->add_control(
			'image_adv_radius',
			array(
				'label'       => __( 'Advanced Border Radius', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Apply custom radius values. Get the radius value from ', 'premium-addons-for-elementor' ) . '<a href="https://9elements.github.io/fancy-border-radius/" target="_blank">here</a>',
				'condition'   => array(
					'premium_person_style' => 'style2',
				),
			)
		);

		$this->add_control(
			'image_adv_radius_value',
			array(
				'label'     => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'selectors' => array(
					'{{WRAPPER}} .premium-person-image-container' => 'border-radius: {{VALUE}};',
				),
				'condition' => array(
					'premium_person_style' => 'style2',
					'image_adv_radius'     => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'     => 'css_filters',
				'selector' => '{{WRAPPER}} .premium-person-container img',
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'     => 'hover_css_filters',
				'label'    => __( 'Hover CSS Filters', 'premium-addons-for-elementor' ),
				'selector' => '{{WRAPPER}} .premium-person-container:hover img',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'premium_person_shadow',
				'selector'  => '{{WRAPPER}} .premium-person-social',
				'condition' => array(
					'premium_person_style' => 'style2',
				),
			)
		);

		$this->add_control(
			'blend_mode',
			array(
				'label'     => __( 'Blend Mode', 'elementor' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					''            => __( 'Normal', 'elementor' ),
					'multiply'    => 'Multiply',
					'screen'      => 'Screen',
					'overlay'     => 'Overlay',
					'darken'      => 'Darken',
					'lighten'     => 'Lighten',
					'color-dodge' => 'Color Dodge',
					'saturation'  => 'Saturation',
					'color'       => 'Color',
					'luminosity'  => 'Luminosity',
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .premium-person-image-container img' => 'mix-blend-mode: {{VALUE}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_person_name_style',
			array(
				'label' => __( 'Name', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'premium_person_name_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-person-name' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'name_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-person-name',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'name_shadow',
				'selector' => '{{WRAPPER}} .premium-person-name',
			)
		);

		$this->add_responsive_control(
			'name_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-person-name' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_person_title_style',
			array(
				'label' => __( 'Job Title', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'premium_person_title_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-person-title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-person-title',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'title_shadow',
				'selector' => '{{WRAPPER}} .premium-person-title',
			)
		);

		$this->add_responsive_control(
			'title_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-person-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'title_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-person-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_person_description_style',
			array(
				'label' => __( 'Description', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'premium_person_description_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-person-content' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'description_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-person-content',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'description_shadow',
				'selector' => '{{WRAPPER}} .premium-person-content',
			)
		);

		$this->add_responsive_control(
			'description_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-person-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_person_social_icon_style',
			array(
				'label'     => __( 'Social Icons', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_person_social_enable' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'premium_person_social_size',
			array(
				'label'       => __( 'Size', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', 'em', '%' ),
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}} .premium-person-list-item i' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'premium_person_social_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-person-list-item i'  => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_person_social_hover_color',
			array(
				'label'     => __( 'Hover Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-person-list-item:hover i'  => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'premium_person_social_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-person-list-item a'  => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'premium_person_social_default_colors',
			array(
				'label'        => __( 'Brands Default Colors', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'premium-person-defaults-',
			)
		);

		$this->add_control(
			'premium_person_social_hover_background',
			array(
				'label'     => __( 'Hover Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} li.premium-person-list-item:hover a'  => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'premium_person_social_default_colors!'   => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_person_social_border',
				'selector' => '{{WRAPPER}} .premium-person-list-item a',
			)
		);

		$this->add_responsive_control(
			'premium_person_social_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-person-list-item a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
				'condition'  => array(
					'social_adv_radius!' => 'yes',
				),
			)
		);

		$this->add_control(
			'social_adv_radius',
			array(
				'label'       => __( 'Advanced Border Radius', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Apply custom radius values. Get the radius value from ', 'premium-addons-for-elementor' ) . '<a href="https://9elements.github.io/fancy-border-radius/" target="_blank">here</a>',
			)
		);

		$this->add_control(
			'social_adv_radius_value',
			array(
				'label'     => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'selectors' => array(
					'{{WRAPPER}} .premium-person-list-item a' => 'border-radius: {{VALUE}};',
				),
				'condition' => array(
					'social_adv_radius' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'premium_person_social_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-person-list-item a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_person_social_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-person-list-item a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_person_general_style',
			array(
				'label' => __( 'Content', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'premium_person_content_background_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(245,245,245,0.97)',
				'selectors' => array(
					'{{WRAPPER}} .premium-person-info' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_person_border_bottom_width',
			array(
				'label'       => __( 'Bottom Offset', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', 'em', '%' ),
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 700,
					),
				),
				'default'     => array(
					'size' => 20,
					'unit' => 'px',
				),
				'label_block' => true,
				'condition'   => array(
					'premium_person_style' => 'style1',
				),
				'selectors'   => array(
					'{{WRAPPER}} .premium-person-info' => 'bottom: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'premium_person_content_speed',
			array(
				'label'     => __( 'Transition Duration (sec)', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 5,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-person-info, {{WRAPPER}} .premium-person-image-container img'   => 'transition-duration: {{SIZE}}s',
				),
			)
		);

		$this->add_responsive_control(
			'premium_person_content_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-person-info-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'carousel_style',
			array(
				'label'     => __( 'Carousel', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'carousel' => 'yes',
				),
			)
		);

		$this->add_control(
			'arrow_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-persons-container .slick-arrow' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'arrow_hover_color',
			array(
				'label'     => __( 'Hover Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-persons-container .slick-arrow:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'arrow_size',
			array(
				'label'      => __( 'Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-persons-container .slick-arrow i' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'arrow_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-persons-container .slick-arrow' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'arrow_hover_background',
			array(
				'label'     => __( 'Background Hover Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-persons-container .slick-arrow:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'arrow_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-persons-container .slick-arrow' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'arrow_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-persons-container .slick-arrow' => 'padding: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Render Persons widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		$image_effect = $settings['premium_person_hover_image_effect'];

		$image_html = '';
		if ( ! empty( $settings['premium_person_image']['url'] ) ) {
			$image_html = Group_Control_Image_Size::get_attachment_image_html( $settings, 'thumbnail', 'premium_person_image' );
		}

		$this->add_render_attribute(
			'persons_container',
			'class',
			array(
				'premium-persons-container',
				'premium-person-' . $settings['premium_person_style'],
			)
		);

		$this->add_render_attribute(
			'person_container',
			'class',
			array(
				'premium-person-container',
				'premium-person-' . $image_effect . '-effect',
			)
		);

		if ( 'yes' === $settings['multiple'] ) {
			$persons = $settings['multiple_persons'];
			$this->add_render_attribute( 'persons_container', 'class', 'multiple-persons' );
			$this->add_render_attribute( 'persons_container', 'data-persons-equal', $settings['multiple_equal_height'] );
		}

		$carousel = 'yes' === $settings['carousel'] ? true : false;

		if ( $carousel ) {

			$this->add_render_attribute( 'persons_container', 'data-carousel', $carousel );

			$speed = ! empty( $settings['carousel_autoplay_speed'] ) ? $settings['carousel_autoplay_speed'] : 5000;

			$this->add_render_attribute(
				'persons_container',
				array(
					'data-rtl' => is_rtl(),
				)
			);

		}

		?>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'persons_container' ) ); ?>>
			<?php if ( 'yes' !== $settings['multiple'] ) : ?>
			<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'person_container' ) ); ?>>
				<div class="premium-person-image-container">
					<?php echo wp_kses_post( $image_html ); ?>
					<?php if ( 'style2' === $settings['premium_person_style'] && 'yes' === $settings['premium_person_social_enable'] ) : ?>
						<div class="premium-person-social">
							<?php $this->get_social_icons(); ?>
						</div>
					<?php endif; ?>
				</div>
				<div class="premium-person-info">
					<?php $this->render_person_info(); ?>
				</div>
			</div>
				<?php
			else :
				foreach ( $persons as $index => $person ) {

					$person_image_html = '';
					if ( ! empty( $person['multiple_image']['url'] ) ) {

						$image_src = $person['multiple_image']['url'];
						$image_id  = attachment_url_to_postid( $image_src );

						$settings['image_data'] = Helper_Functions::get_image_data( $image_id, $person['multiple_image']['url'], $settings['thumbnail_size'] );
						$person_image_html      = Group_Control_Image_Size::get_attachment_image_html( $settings, 'thumbnail', 'image_data' );
					}
					?>
					<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'person_container' ) ); ?>>
						<div class="premium-person-image-container">
							<?php echo wp_kses_post( $person_image_html ); ?>
							<?php if ( 'style2' === $settings['premium_person_style'] && 'yes' === $person['multiple_social_enable'] ) : ?>
								<div class="premium-person-social">
									<?php $this->get_social_icons( $person ); ?>
								</div>
							<?php endif; ?>
						</div>
						<div class="premium-person-info">
							<?php $this->render_person_info( $person, $index ); ?>
						</div>
					</div>
					<?php
				}
			endif;
			?>
		</div>
		<?php
	}

	/**
	 * Get Social Icons
	 *
	 * Render person social icons list
	 *
	 * @since 3.8.4
	 * @access protected
	 *
	 * @param object $person current person.
	 */
	private function get_social_icons( $person = '' ) {

		$settings = $this->get_settings_for_display();

		$social_sites = array(
			'facebook'  => 'fa fa-facebook-f',
			'twitter'   => 'fa fa-twitter',
			'linkedin'  => 'fa fa-linkedin',
			'google'    => 'fa fa-google-plus',
			'youtube'   => 'fa fa-youtube',
			'instagram' => 'fa fa-instagram',
			'skype'     => 'fa fa-skype',
			'pinterest' => 'fa fa-pinterest',
			'dribbble'  => 'fa fa-dribbble',
			'behance'   => 'fa fa-behance',
			'whatsapp'  => 'fa fa-whatsapp',
			'telegram'  => 'fa fa-telegram',
			'mail'      => 'fa fa-envelope',
			'site'      => 'fa fa-link',
			'number'    => 'fa fa-phone',
		);

		echo '<ul class="premium-person-social-list">';

		do_action( 'pa_before_member_icon' );

		foreach ( $social_sites as $site => $icon ) {

			if ( ! \Elementor\Plugin::instance()->editor->is_edit_mode() ) {
				if ( 'number' === $site && ! wp_is_mobile() ) {
					continue;
				}
			}

			$value = ( '' === $person ) ? $settings[ 'premium_person_' . $site ] : $person[ 'multiple_' . $site ];

			if ( ! empty( $value ) ) {
				$icon_class = sprintf( 'elementor-icon premium-person-list-item premium-person-%s', $site );
				?>
				<li class="<?php echo esc_attr( $icon_class ); ?>">
					<a href="<?php echo esc_url( $value ); ?>" target="_blank">
						<i class="<?php echo esc_attr( $icon ); ?>"></i>
					</a>
				</li>
				<?php
			}
		}

		do_action( 'pa_after_member_icon' );

		echo '</ul>';

	}

	/**
	 * Render Person Info
	 *
	 * @since 3.12.0
	 * @access protected
	 *
	 * @param object  $person current person.
	 * @param integer $index person index.
	 */
	protected function render_person_info( $person = '', $index = '' ) {

		$settings = $this->get_settings_for_display();

		$this->add_inline_editing_attributes( 'premium_person_name', 'advanced' );
		$this->add_render_attribute( 'premium_person_name', 'class', 'premium-person-name' );

		$this->add_inline_editing_attributes( 'premium_person_title', 'advanced' );
		$this->add_render_attribute( 'premium_person_title', 'class', 'premium-person-title' );

		$this->add_inline_editing_attributes( 'premium_person_content', 'advanced' );
		$this->add_render_attribute( 'premium_person_content', 'class', 'premium-person-content' );

		$name_heading = Helper_Functions::validate_html_tag( $settings['premium_person_name_heading'] );

		$title_heading = Helper_Functions::validate_html_tag( $settings['premium_person_title_heading'] );

		$skin = $settings['premium_person_style'];

		if ( empty( $person ) ) :
			?>
			<div class="premium-person-info-container">
				<?php if ( 'style3' !== $skin && ! empty( $settings['premium_person_name'] ) ) : ?>
					<<?php echo wp_kses_post( $name_heading . ' ' . $this->get_render_attribute_string( 'premium_person_name' ) ); ?>>
						<?php echo wp_kses_post( $settings['premium_person_name'] ); ?>
					</<?php echo wp_kses_post( $name_heading ); ?>>
					<?php
				endif;

				if ( 'style3' === $skin ) :
					?>
					<div class="premium-person-title-desc-wrap">
					<?php
				endif;
				if ( ! empty( $settings['premium_person_title'] ) ) :
					?>
						<<?php echo wp_kses_post( $title_heading . ' ' . $this->get_render_attribute_string( 'premium_person_title' ) ); ?>>
							<span><?php echo wp_kses_post( $settings['premium_person_title'] ); ?></span>
						</<?php echo wp_kses_post( $title_heading ); ?>>
					<?php
					endif;

				if ( ! empty( $settings['premium_person_content'] ) ) :
					?>
						<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'premium_person_content' ) ); ?>>
							<?php echo $this->parse_text_editor( $settings['premium_person_content'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
					<?php
					endif;
				if ( 'style3' === $skin ) :
					?>
					</div>
					<?php
				endif;

				if ( 'style3' === $skin ) :
					?>
					<div class="premium-person-name-icons-wrap">
						<?php if ( ! empty( $settings['premium_person_name'] ) ) : ?>
							<<?php echo wp_kses_post( $name_heading ); ?> class="premium-person-name"><span <?php echo wp_kses_post( $this->get_render_attribute_string( 'premium_person_name' ) ); ?>><?php echo wp_kses_post( $settings['premium_person_name'] ); ?></span></<?php echo wp_kses_post( $name_heading ); ?>>
							<?php
						endif;
						if ( 'yes' === $settings['premium_person_social_enable'] ) :
							$this->get_social_icons();
						endif;
						?>
					</div>
					<?php
				endif;

				if ( 'style1' === $settings['premium_person_style'] && 'yes' === $settings['premium_person_social_enable'] ) :
					$this->get_social_icons();
				endif;
				?>
			</div>
			<?php
		else :

			$name_setting_key  = $this->get_repeater_setting_key( 'multiple_name', 'multiple_persons', $index );
			$title_setting_key = $this->get_repeater_setting_key( 'multiple_title', 'multiple_persons', $index );
			$desc_setting_key  = $this->get_repeater_setting_key( 'multiple_description', 'multiple_persons', $index );

			$this->add_inline_editing_attributes( $name_setting_key, 'advanced' );
			$this->add_render_attribute( $name_setting_key, 'class', 'premium-person-name' );

			$this->add_inline_editing_attributes( $title_setting_key, 'advanced' );
			$this->add_render_attribute( $title_setting_key, 'class', 'premium-person-title' );

			$this->add_inline_editing_attributes( $desc_setting_key, 'advanced' );
			$this->add_render_attribute( $desc_setting_key, 'class', 'premium-person-content' );

			?>
			<div class="premium-person-info-container">
				<?php if ( 'style3' !== $skin && ! empty( $person['multiple_name'] ) ) : ?>
					<<?php echo wp_kses_post( $name_heading . ' ' . $this->get_render_attribute_string( $name_setting_key ) ); ?>>
						<?php echo wp_kses_post( $person['multiple_name'] ); ?>
					</<?php echo wp_kses_post( $name_heading ); ?>>
					<?php
				endif;

				if ( 'style3' === $skin ) :
					?>
					<div class="premium-person-title-desc-wrap">
					<?php
				endif;
				if ( ! empty( $person['multiple_title'] ) ) :
					?>
						<<?php echo wp_kses_post( $title_heading . ' ' . $this->get_render_attribute_string( $title_setting_key ) ); ?>>
							<span><?php echo wp_kses_post( $person['multiple_title'] ); ?></span>
						</<?php echo wp_kses_post( $title_heading ); ?>>
					<?php
					endif;

				if ( ! empty( $person['multiple_description'] ) ) :
					?>
						<div <?php echo wp_kses_post( $this->get_render_attribute_string( $desc_setting_key ) ); ?>>
							<?php echo $this->parse_text_editor( $person['multiple_description'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
					<?php
					endif;
				if ( 'style3' === $skin ) :
					?>
					</div>
					<?php
				endif;

				if ( 'style3' === $skin ) :
					?>
					<div class="premium-person-name-icons-wrap">
						<?php if ( ! empty( $person['multiple_name'] ) ) : ?>
							<<?php echo wp_kses_post( $name_heading ); ?> class="premium-person-name">
								<span <?php echo wp_kses_post( $this->get_render_attribute_string( $name_setting_key ) ); ?>>
									<?php echo wp_kses_post( $person['multiple_name'] ); ?>
								</span>
							</<?php echo wp_kses_post( $name_heading ); ?>>
							<?php
						endif;
						if ( 'yes' === $person['multiple_social_enable'] ) :
							$this->get_social_icons( $person );
						endif;
						?>
					</div>
					<?php
				endif;

				if ( 'style1' === $settings['premium_person_style'] && 'yes' === $person['multiple_social_enable'] ) :
					$this->get_social_icons( $person );
				endif;
				?>
			</div>
			<?php
		endif;

	}


	/**
	 * Render Persons widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function content_template() {
		?>
		<#

		view.addInlineEditingAttributes( 'premium_person_name', 'advanced' );
		view.addRenderAttribute( 'premium_person_name', 'class', 'premium-person-name' );

		view.addInlineEditingAttributes( 'premium_person_title', 'advanced' );
		view.addRenderAttribute( 'premium_person_title', 'class', 'premium-person-title' );

		view.addInlineEditingAttributes( 'premium_person_content', 'advanced' );
		view.addRenderAttribute( 'premium_person_content', 'class', 'premium-person-content' );

		var nameHeading = elementor.helpers.validateHTMLTag( settings.premium_person_name_heading ),

		titleHeading = elementor.helpers.validateHTMLTag( settings.premium_person_title_heading ),

		imageEffect = 'premium-person-' + settings.premium_person_hover_image_effect + '-effect' ;

		skin        = settings.premium_person_style;

		view.addRenderAttribute( 'persons_container', 'class', [ 'premium-persons-container', 'premium-person-' + skin ] );

		view.addRenderAttribute('person_container', 'class', [ 'premium-person-container', imageEffect ] );

		var imageHtml = '';
		if ( settings.premium_person_image.url ) {
			var image = {
				id: settings.premium_person_image.id,
				url: settings.premium_person_image.url,
				size: settings.thumbnail_size,
				dimension: settings.thumbnail_custom_dimension,
				model: view.getEditModel()
			};

			var image_url = elementor.imagesManager.getImageUrl( image );

			imageHtml = '<img src="' + image_url + '"/>';

		}

		if ( settings.multiple ) {
			var persons = settings.multiple_persons;
			view.addRenderAttribute( 'persons_container', 'class', 'multiple-persons' );
			view.addRenderAttribute( 'persons_container', 'data-persons-equal', settings.multiple_equal_height );
		}

		var carousel = 'yes' === settings.carousel ? true : false;

		if( carousel ) {

			view.addRenderAttribute('persons_container', {
				'data-carousel': carousel,
			});

		}


		function getSocialIcons( person = null ) {

			var personSettings,
				socialIcons;

			if( null === person ) {
				personSettings = settings;
				socialIcons = {
					facebook: settings.premium_person_facebook,
					twitter:  settings.premium_person_twitter,
					linkedin:  settings.premium_person_linkedin,
					google:  settings.premium_person_google,
					youtube: settings.premium_person_youtube,
					instagram: settings.premium_person_instagram,
					skype: settings.premium_person_skype,
					pinterest: settings.premium_person_pinterest,
					dribbble: settings.premium_person_dribbble,
					behance: settings.premium_person_behance,
					whatsapp: settings.premium_person_whatsapp,
					telegram: settings.premium_person_telegram,
					mail: settings.premium_person_mail,
					site: settings.premium_person_site,
					number: settings.premium_person_number
				};
			} else {
				personSettings = person;
				socialIcons = {
					facebook: person.multiple_facebook,
					twitter:  person.multiple_twitter,
					linkedin:  person.multiple_linkedin,
					google:  person.multiple_google,
					youtube: person.multiple_youtube,
					instagram: person.multiple_instagram,
					skype: person.multiple_skype,
					pinterest: person.multiple_pinterest,
					dribbble: person.multiple_dribbble,
					behance: person.multiple_behance,
					whatsapp: person.multiple_whatsapp,
					telegram: person.multiple_telegram,
					mail: person.multiple_mail,
					site: person.multiple_site,
					number: person.multiple_number
				};
			}

			#>
			<ul class="premium-person-social-list">
				<# if( '' != socialIcons.facebook ) { #>
					<li class="elementor-icon premium-person-list-item premium-person-facebook"><a href="{{ socialIcons.facebook }}" target="_blank"><i class="fa fa-facebook-f"></i></a></li>
				<# } #>

				<# if( '' != socialIcons.twitter ) { #>
					<li class="elementor-icon premium-person-list-item premium-person-twitter"><a href="{{ socialIcons.twitter }}" target="_blank"><i class="fa fa-twitter"></i></a></li>
				<# } #>

				<# if( '' != socialIcons.linkedin ) { #>
					<li class="elementor-icon premium-person-list-item premium-person-linkedin"><a href="{{ socialIcons.linkedin }}" target="_blank"><i class="fa fa-linkedin"></i></a></li>
				<# } #>

				<# if( '' != socialIcons.google ) { #>
					<li class="elementor-icon premium-person-list-item premium-person-google"><a href="{{ socialIcons.google }}" target="_blank"><i class="fa fa-google-plus"></i></a></li>
				<# } #>

				<# if( '' != socialIcons.youtube ) { #>
					<li class="elementor-icon premium-person-list-item premium-person-youtube"><a href="{{ socialIcons.youtube }}" target="_blank"><i class="fa fa-youtube"></i></a></li>
				<# } #>

				<# if( '' != socialIcons.instagram ) { #>
					<li class="elementor-icon premium-person-list-item premium-person-instagram"><a href="{{ socialIcons.instagram }}" target="_blank"><i class="fa fa-instagram"></i></a></li>
				<# } #>

				<# if( '' != socialIcons.skype ) { #>
					<li class="elementor-icon premium-person-list-item premium-person-skype"><a href="{{ socialIcons.skype }}" target="_blank"><i class="fa fa-skype"></i></a></li>
				<# } #>

				<# if( '' != socialIcons.pinterest ) { #>
					<li class="elementor-icon premium-person-list-item premium-person-pinterest"><a href="{{ socialIcons.pinterest }}" target="_blank"><i class="fa fa-pinterest"></i></a></li>
				<# } #>

				<# if( '' != socialIcons.dribbble ) { #>
					<li class="elementor-icon premium-person-list-item premium-person-dribbble"><a href="{{ socialIcons.dribbble }}" target="_blank"><i class="fa fa-dribbble"></i></a></li>
				<# } #>

				<# if( '' != socialIcons.behance ) { #>
					<li class="elementor-icon premium-person-list-item premium-person-behance"><a href="{{ socialIcons.behance }}" target="_blank"><i class="fa fa-behance"></i></a></li>
				<# } #>

				<# if( '' != socialIcons.whatsapp ) { #>
					<li class="elementor-icon premium-person-list-item premium-person-whatsapp"><a href="{{ socialIcons.whatsapp }}" target="_blank"><i class="fa fa-whatsapp"></i></a></li>
				<# } #>

				<# if( '' != socialIcons.telegram ) { #>
					<li class="elementor-icon premium-person-list-item premium-person-telegram"><a href="{{ socialIcons.mail }}" target="_blank"><i class="fa fa-telegram"></i></a></li>
				<# } #>

				<# if( '' != socialIcons.mail ) { #>
					<li class="elementor-icon premium-person-list-item premium-person-mail"><a href="{{ socialIcons.mail }}" target="_blank"><i class="fa fa-envelope"></i></a></li>
				<# } #>

				<# if( '' != socialIcons.site ) { #>
					<li class="elementor-icon premium-person-list-item premium-person-site"><a href="{{ socialIcons.site }}" target="_blank"><i class="fa fa-link"></i></a></li>
				<# } #>

				<# if( '' != socialIcons.number ) { #>
					<li class="elementor-icon premium-person-list-item premium-person-number"><a href="{{ socialIcons.number }}" target="_blank"><i class="fa fa-phone"></i></a></li>
				<# } #>

			</ul>
			<# }
		#>

		<div {{{ view.getRenderAttributeString('persons_container') }}}>
			<# if( 'yes' !== settings.multiple ) { #>
			<div {{{ view.getRenderAttributeString('person_container') }}}>
				<div class="premium-person-image-container">
					{{{imageHtml}}}
					<# if ( 'style2' === settings.premium_person_style && 'yes' === settings.premium_person_social_enable ) { #>
						<div class="premium-person-social">
							<# getSocialIcons(); #>
						</div>
					<# } #>
				</div>
				<div class="premium-person-info">
					<div class="premium-person-info-container">
						<# if( 'style3' !== skin && '' != settings.premium_person_name ) { #>
							<{{{nameHeading}}} {{{ view.getRenderAttributeString('premium_person_name') }}}>
									{{{ settings.premium_person_name }}}
							</{{{nameHeading}}}>
						<# }

						if( 'style3' === skin ) { #>
							<div class="premium-person-title-desc-wrap">
						<# }
							if( '' != settings.premium_person_title ) { #>
								<{{{titleHeading}}} {{{ view.getRenderAttributeString('premium_person_title') }}}>
										<span>{{{ settings.premium_person_title }}}</span>
								</{{{titleHeading}}}>
							<# }
							if( '' != settings.premium_person_content ) { #>
								<div {{{ view.getRenderAttributeString('premium_person_content') }}}>
									{{{ settings.premium_person_content }}}
								</div>
							<# }
						if( 'style3' === skin ) { #>
							</div>
						<# }

						if( 'style3' === skin ) { #>
							<div class="premium-person-name-icons-wrap">
							<# if( '' != settings.premium_person_name ) { #>
								<{{{nameHeading}}} {{{ view.getRenderAttributeString('premium_person_name') }}}>
									{{{ settings.premium_person_name }}}
								</{{{nameHeading}}}>
							<# }
							if( 'yes' === settings.premium_person_social_enable ) {
								getSocialIcons();
							} #>
							</div>
						<# }

						if ( 'style1' === settings.premium_person_style && 'yes' === settings.premium_person_social_enable ) {
							getSocialIcons();
						} #>
					</div>
				</div>
			</div>
			<# } else {
				_.each( persons, function( person, index ) {
					var nameSettingKey = view.getRepeaterSettingKey( 'multiple_name', 'multiple_persons', index ),
						titleSettingKey = view.getRepeaterSettingKey( 'multiple_title', 'multiple_persons', index ),
						descSettingKey = view.getRepeaterSettingKey( 'multiple_description', 'multiple_persons', index );


					view.addInlineEditingAttributes( nameSettingKey, 'advanced' );
					view.addRenderAttribute( nameSettingKey, 'class', 'premium-person-name' );

					view.addInlineEditingAttributes( titleSettingKey, 'advanced' );
					view.addRenderAttribute( titleSettingKey, 'class', 'premium-person-title' );

					view.addInlineEditingAttributes( descSettingKey, 'advanced' );
					view.addRenderAttribute( descSettingKey, 'class', 'premium-person-content' );

					var personImageHtml = '';
					if ( person.multiple_image.url ) {
						var personImage = {
							id: person.multiple_image.id,
							url: person.multiple_image.url,
							size: settings.thumbnail_size,
							dimension: settings.thumbnail_custom_dimension,
							model: view.getEditModel()
						};

						var personImageUrl = elementor.imagesManager.getImageUrl( personImage );

						personImageHtml = '<img src="' + personImageUrl + '"/>';

					}
				#>
					<div {{{ view.getRenderAttributeString('person_container') }}}>
						<div class="premium-person-image-container">
								{{{personImageHtml}}}
							<# if ( 'style2' === settings.premium_person_style && 'yes' === person.multiple_social_enable ) { #>
							<div class="premium-person-social">
								<# getSocialIcons( person ); #>
							</div>
						<# } #>
						</div>
						<div class="premium-person-info">
							<div class="premium-person-info-container">
								<# if( 'style3' !== skin && '' != person.multiple_name ) { #>
									<{{{nameHeading}}} {{{ view.getRenderAttributeString( nameSettingKey ) }}}>
										{{{ person.multiple_name }}}
									</{{{nameHeading}}}>
								<# }

								if( 'style3' === skin ) { #>
									<div class="premium-person-title-desc-wrap">
								<# }
									if( '' != person.multiple_title  ) { #>
										<{{{titleHeading}}} {{{ view.getRenderAttributeString( titleSettingKey ) }}}>
											<span>{{{ person.multiple_title }}}</span>
										</{{{titleHeading}}}>
									<# }
									if( '' != person.multiple_description ) { #>
										<div {{{ view.getRenderAttributeString( descSettingKey ) }}}>
											{{{ person.multiple_description }}}
										</div>
									<# }
								if( 'style3' === skin ) { #>
									</div>
								<# }

								if( 'style3' === skin ) { #>
									<div class="premium-person-name-icons-wrap">
									<# if( '' != settings.premium_person_name ) { #>
										<{{{nameHeading}}} {{{ view.getRenderAttributeString( nameSettingKey ) }}}>
											{{{ person.multiple_name }}}
										</{{{nameHeading}}}>
									<# }
									if( 'yes' === person.multiple_social_enable ) {
										getSocialIcons( person );
									} #>
									</div>
								<# }

								if ( 'style1' === settings.premium_person_style && 'yes' === person.multiple_social_enable ) {
									getSocialIcons( person );
								} #>
							</div>
						</div>
					</div>
				<# });
			} #>

		</div>
		<?php
	}
}
