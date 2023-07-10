<?php
/**
 * Premium Media Grid.
 */

namespace PremiumAddons\Widgets;

// Elementor Classes.
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Widget_Base;
use Elementor\Utils;
use Elementor\Embed;
use Elementor\Control_Media;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Image_Size;

// PremiumAddons Classes.
use PremiumAddons\Includes\Helper_Functions;
use PremiumAddons\Includes\Premium_Template_Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Premium_Grid
 */
class Premium_Grid extends Widget_Base {

	/**
	 * Template Instance
	 *
	 * @var template_instance
	 */
	protected $template_instance;

	/**
	 * Check for Self Hosted Videos
	 *
	 * @var is_self_hosted
	 */
	private static $check_self_hosted = false;

	/**
	 * Retrieve Widget Name.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_name() {
		return 'premium-img-gallery';
	}

	/**
	 * Get Elementor Helper Instance.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function getTemplateInstance() {
		return $this->template_instance = Premium_Template_Tags::getInstance();
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return __( 'Media Grid', 'premium-addons-for-elementor' );
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
		return 'pa-grid-icon';
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
			'pa-prettyphoto',
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
			'prettyPhoto-js',
			'isotope-js',
			'premium-addons',
		);
	}

	/**
	 * Widget preview refresh button.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function is_reload_preview_required() {
		return true;
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
		return array( 'pa', 'premium', 'layout', 'gallery', 'image', 'video', 'portfolio', 'visual', 'masonry', 'youtube', 'vimeo' );
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
	 * Register Media Grid controls.
	 *
	 * @since 2.1.0
	 * @access protected
	 */
	protected function register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		$this->start_controls_section(
			'premium_gallery_general',
			array(
				'label' => __( 'Layout', 'premium-addons-for-elementor' ),

			)
		);

		$this->add_control(
			'premium_gallery_img_size_select',
			array(
				'label'   => __( 'Grid Layout', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'fitRows' => __( 'Even', 'premium-addons-for-elementor' ),
					'masonry' => __( 'Masonry', 'premium-addons-for-elementor' ),
					'metro'   => __( 'Metro', 'premium-addons-for-elementor' ),
				),
				'default' => 'fitRows',
			)
		);

		$this->add_responsive_control(
			'pemium_gallery_even_img_height',
			array(
				'label'       => __( 'Height', 'premium-addons-for-elementor' ),
				'label_block' => true,
				'size_units'  => array( 'px', 'em', 'vh' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => array(
					'px' => array(
						'min' => 1,
						'max' => 500,
					),
					'em' => array(
						'min' => 1,
						'max' => 50,
					),
				),
				'render_type' => 'template',
				'condition'   => array(
					'premium_gallery_img_size_select' => 'fitRows',
				),
				'selectors'   => array(
					'{{WRAPPER}} .pa-gallery-img-container img' => 'height: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'premium_gallery_images_fit',
			array(
				'label'     => __( 'Images Fit', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'fill'  => __( 'Fill', 'premium-addons-for-elementor' ),
					'cover' => __( 'Cover', 'premium-addons-for-elementor' ),
				),
				'default'   => 'fill',
				'selectors' => array(
					'{{WRAPPER}} .pa-gallery-img-container img'  => 'object-fit: {{VALUE}}',
				),
				'condition' => array(
					'premium_gallery_img_size_select' => array( 'metro', 'fitRows' ),
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
			'premium_gallery_column_number',
			array(
				'label'           => __( 'Columns', 'premium-addons-for-elementor' ),
				'label_block'     => true,
				'type'            => Controls_Manager::SELECT,
				'desktop_default' => '50%',
				'tablet_default'  => '100%',
				'mobile_default'  => '100%',
				'options'         => array(
					'100%'    => __( '1 Column', 'premium-addons-for-elementor' ),
					'50%'     => __( '2 Columns', 'premium-addons-for-elementor' ),
					'33.330%' => __( '3 Columns', 'premium-addons-for-elementor' ),
					'25%'     => __( '4 Columns', 'premium-addons-for-elementor' ),
					'20%'     => __( '5 Columns', 'premium-addons-for-elementor' ),
					'16.66%'  => __( '6 Columns', 'premium-addons-for-elementor' ),
					'8.33%'   => __( '12 Columns', 'premium-addons-for-elementor' ),
				),
				'condition'       => array(
					'premium_gallery_img_size_select!' => 'metro',
				),
				'selectors'       => array(
					'{{WRAPPER}} .premium-img-gallery-masonry div.premium-gallery-item, {{WRAPPER}} .premium-img-gallery-fitRows div.premium-gallery-item' => 'width: {{VALUE}};',
				),
				'render_type'     => 'template',
			)
		);

		$this->add_control(
			'premium_gallery_load_more',
			array(
				'label'       => __( 'Load More Button', 'premium-addons-for-elementor' ),
				'description' => __( 'Requires number of images larger than 6', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'condition'   => array(
					'premium_gallery_shuffle_onload!' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_gallery_load_more_text',
			array(
				'label'     => __( 'Button Text', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Load More', 'premium-addons-for-elementor' ),
				'dynamic'   => array( 'active' => true ),
				'condition' => array(
					'premium_gallery_load_more' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_gallery_load_minimum',
			array(
				'label'       => __( 'Initial Number of Images', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => __( 'Set the minimum number of images before showing load more button', 'premium-addons-for-elementor' ),
				'default'     => 6,
				'condition'   => array(
					'premium_gallery_load_more' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_gallery_load_click_number',
			array(
				'label'       => __( 'Images to Show', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => __( 'Set the minimum number of images to show with each click', 'premium-addons-for-elementor' ),
				'default'     => 6,
				'condition'   => array(
					'premium_gallery_load_more' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'premium_gallery_load_more_align',
			array(
				'label'     => __( 'Button Alignment', 'premium-addons-for-elementor' ),
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
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}} .premium-gallery-load-more' => 'text-align: {{VALUE}};',
				),
				'condition' => array(
					'premium_gallery_load_more' => 'yes',
				),
			)
		);

		$this->add_control(
			'gradient_layer',
			array(
				'label'       => __( 'Gradient Layer', 'premium-addons-for-elementor' ),
				'description' => __( 'Adds a gradient color on top of the grid container', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'condition'   => array(
					'premium_gallery_load_more' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'      => 'gradient_color',
				'types'     => array( 'gradient' ),
				'selector'  => '{{WRAPPER}} .premium-gallery-gradient-layer',
				'condition' => array(
					'premium_gallery_load_more' => 'yes',
					'gradient_layer'            => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'gradient_height',
			array(
				'label'       => __( 'Height', 'premium-addons-for-elementor' ),
				'label_block' => true,
				'size_units'  => array( 'px', 'em', '%', 'custom' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => array(
					'px' => array(
						'min' => 1,
						'max' => 300,
					),
					'em' => array(
						'min' => 1,
						'max' => 30,
					),
				),
				'condition'   => array(
					'premium_gallery_load_more' => 'yes',
					'gradient_layer'            => 'yes',
				),
				'selectors'   => array(
					'{{WRAPPER}} .premium-gallery-gradient-layer' => 'height: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'gradient_position',
			array(
				'label'       => __( 'Position', 'premium-addons-for-elementor' ),
				'label_block' => true,
				'size_units'  => array( 'px', 'em', '%', 'custom' ),
				'type'        => Controls_Manager::SLIDER,
				'condition'   => array(
					'premium_gallery_load_more' => 'yes',
					'gradient_layer'            => 'yes',
				),
				'selectors'   => array(
					'{{WRAPPER}} .premium-gallery-gradient-layer' => 'bottom: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_gallery_cats',
			array(
				'label' => __( 'Categories', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_gallery_filter',
			array(
				'label'   => __( 'Filter Tabs', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$condition = array( 'premium_gallery_filter' => 'yes' );

		$this->add_control(
			'premium_gallery_first_cat_switcher',
			array(
				'label'     => __( 'First Category', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => $condition,
			)
		);

		$this->add_control(
			'premium_gallery_first_cat_label',
			array(
				'label'     => __( 'First Category Label', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'All', 'premium-addons-for-elementor' ),
				'dynamic'   => array( 'active' => true ),
				'condition' => array_merge(
					array(
						'premium_gallery_first_cat_switcher' => 'yes',
					),
					$condition
				),
			)
		);

		$repeater = new REPEATER();

		$repeater->add_control(
			'premium_gallery_img_cat',
			array(
				'label'   => __( 'Category', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'premium_gallery_img_cat_rotation',
			array(
				'label'       => __( 'Rotation Degrees', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => __( 'Set rotation value in degrees', 'premium-addons-for-elementor' ),
				'min'         => -180,
				'max'         => 180,
				'selectors'   => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'transform: rotate({{VALUE}}deg);',
				),
			)
		);

		$this->add_control(
			'premium_gallery_cats_content',
			array(
				'label'       => __( 'Categories', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::REPEATER,
				'default'     => array(
					array(
						'premium_gallery_img_cat' => 'Category 1',
					),
					array(
						'premium_gallery_img_cat' => 'Category 2',
					),
				),
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ premium_gallery_img_cat }}}',
				'condition'   => $condition,
			)
		);

		$this->add_control(
			'premium_gallery_active_cat',
			array(
				'label'     => __( 'Active Category Index', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 0,
				'min'       => 0,
				'condition' => $condition,

			)
		);

		$this->add_control(
			'active_cat_notice',
			array(
				'raw'             => __( 'Please note categories are zero indexed, so if you need the first category to be active, you need to set the value to 0', 'premium-addons-for-elementor' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => $condition,
			)
		);

		$this->add_control(
			'premium_gallery_shuffle',
			array(
				'label'     => __( 'Shuffle Images on Filter Click', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array_merge(
					array(
						'premium_gallery_filter' => 'yes',
					),
					$condition
				),
			)
		);

		$this->add_responsive_control(
			'premium_gallery_filters_align',
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
					'{{WRAPPER}} .premium-img-gallery-filter' => 'justify-content: {{VALUE}}',
				),
				'condition' => $condition,
			)
		);

		$this->add_control(
			'url_flag',
			array(
				'label'       => __( 'URL Flag', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'cat',
				'description' => __( 'This is used to link categories from different pages. For example: premiumaddons.com/grid-widget-for-elementor-page-builder?cat=2', 'premium-addons-for-elementor' ),
				'label_block' => true,
				'condition'   => $condition,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_gallery_content',
			array(
				'label' => __( 'Images/Videos', 'premium-addons-for-elementor' ),
			)
		);

		$img_repeater = new REPEATER();

		$img_repeater->add_control(
			'premium_gallery_img',
			array(
				'label'   => __( 'Upload Image', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => array( 'active' => true ),
				'default' => array(
					'url' => Utils::get_placeholder_image_src(),
				),
			)
		);

		$img_repeater->add_responsive_control(
			'premium_gallery_image_cell',
			array(
				'label'              => __( 'Width', 'premium-addons-for-elementor' ),
				'description'        => __( 'Works only when layout set to Metro', 'premium-addons-for-elementor' ),
				'label_block'        => true,
				'default'            => array(
					'unit' => 'px',
					'size' => 4,
				),
				'type'               => Controls_Manager::SLIDER,
				'range'              => array(
					'px' => array(
						'min' => 1,
						'max' => 12,
					),
				),
				'render_type'        => 'template',
				'frontend_available' => true,
			)
		);

		$img_repeater->add_responsive_control(
			'premium_gallery_image_vcell',
			array(
				'label'              => __( 'Height', 'premium-addons-for-elementor' ),
				'description'        => __( 'Works only when layout set to \'Metro\'', 'premium-addons-for-elementor' ),
				'label_block'        => true,
				'type'               => Controls_Manager::SLIDER,
				'default'            => array(
					'unit' => 'px',
					'size' => 4,
				),
				'range'              => array(
					'px' => array(
						'min' => 1,
						'max' => 12,
					),
				),
				'render_type'        => 'template',
				'frontend_available' => true,
			)
		);

		$img_repeater->add_control(
			'premium_gallery_video',
			array(
				'label'        => __( 'Video', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
			)
		);

		$img_repeater->add_control(
			'premium_gallery_video_type',
			array(
				'label'       => __( 'Type', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'youtube' => __( 'YouTube', 'premium-addons-for-elementor' ),
					'vimeo'   => __( 'Vimeo', 'premium-addons-for-elementor' ),
					'hosted'  => __( 'Self Hosted', 'premium-addons-for-elementor' ),
				),
				'label_block' => true,
				'default'     => 'youtube',
				'condition'   => array(
					'premium_gallery_video' => 'true',
				),
			)
		);

		$img_repeater->add_control(
			'premium_gallery_video_url',
			array(
				'label'       => __( 'Video URL', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => array(
					'active'     => true,
					'categories' => array(
						TagsModule::POST_META_CATEGORY,
						TagsModule::URL_CATEGORY,
					),
				),
				'condition'   => array(
					'premium_gallery_video'       => 'true',
					'premium_gallery_video_type!' => 'hosted',
				),
			)
		);

		$img_repeater->add_control(
			'premium_gallery_video_self',
			array(
				'label'      => __( 'Select Video', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::MEDIA,
				'dynamic'    => array(
					'active'     => true,
					'categories' => array(
						TagsModule::POST_META_CATEGORY,
						TagsModule::URL_CATEGORY,
					),
				),
				'media_type' => 'video',
				'condition'  => array(
					'premium_gallery_video'      => 'true',
					'premium_gallery_video_type' => 'hosted',
				),
			)
		);

		$img_repeater->add_control(
			'premium_gallery_video_self_url',
			array(
				'label'       => __( 'Remote Video URL', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array(
					'active' => true,
				),
				'label_block' => true,
				'condition'   => array(
					'premium_gallery_video'      => 'true',
					'premium_gallery_video_type' => 'hosted',
				),
			)
		);

		$img_repeater->add_control(
			'premium_gallery_video_controls',
			array(
				'label'        => __( 'Controls', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'default'      => 'true',
				'condition'    => array(
					'premium_gallery_video' => 'true',
				),
			)
		);

		$img_repeater->add_control(
			'premium_gallery_video_mute',
			array(
				'label'        => __( 'Mute', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'condition'    => array(
					'premium_gallery_video' => 'true',
				),
			)
		);

		$img_repeater->add_control(
			'premium_gallery_video_loop',
			array(
				'label'        => __( 'Loop', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'condition'    => array(
					'premium_gallery_video' => 'true',
				),
			)
		);

		$img_repeater->add_control(
			'download_button',
			array(
				'label'     => __( 'Download Button', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'premium_gallery_video'      => 'true',
					'premium_gallery_video_type' => 'hosted',
				),
			)
		);

		$img_repeater->add_control(
			'privacy_mode',
			array(
				'label'       => __( 'Privacy Mode', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'When turned on, YouTube won\'t store information about visitors on your website unless they play the video.', 'premium-addons-for-elementor' ),
				'condition'   => array(
					'premium_gallery_video'      => 'true',
					'premium_gallery_video_type' => 'youtube',
				),
			)
		);

		$img_repeater->add_control(
			'premmium_gallery_img_info',
			array(
				'label' => __( 'Get Title/Description From Media Library', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$img_repeater->add_control(
			'premium_gallery_img_name',
			array(
				'label'       => __( 'Title', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
				'condition'   => array(
					'premmium_gallery_img_info!' => 'yes',
				),
			)
		);

		$img_repeater->add_control(
			'premium_gallery_img_desc',
			array(
				'label'       => __( 'Description', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXTAREA,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
				'condition'   => array(
					'premmium_gallery_img_info!' => 'yes',
				),
			)
		);

		$img_repeater->add_control(
			'premium_gallery_img_category',
			array(
				'label'       => __( 'Category', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'To assign for multiple categories, separate by a comma \',\'', 'premium-addons-for-elementor' ),
				'dynamic'     => array( 'active' => true ),
			)
		);

		$img_repeater->add_control(
			'premium_gallery_img_link_type',
			array(
				'label'       => __( 'Link Type', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'url'  => __( 'URL', 'premium-addons-for-elementor' ),
					'link' => __( 'Existing Page', 'premium-addons-for-elementor' ),
				),
				'default'     => 'url',
				'label_block' => true,
				'condition'   => array(
					'premium_gallery_video!' => 'true',
				),
			)
		);

		$img_repeater->add_control(
			'premium_gallery_img_link',
			array(
				'label'       => __( 'Link', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => array( 'active' => true ),
				'placeholder' => 'https://premiumaddons.com/',
				'label_block' => true,
				'condition'   => array(
					'premium_gallery_img_link_type' => 'url',
					'premium_gallery_video!'        => 'true',
				),
			)
		);

		$img_repeater->add_control(
			'premium_gallery_img_existing',
			array(
				'label'       => __( 'Existing Page', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => $this->getTemplateInstance()->get_all_posts(),
				'condition'   => array(
					'premium_gallery_img_link_type' => 'link',
				),
				'multiple'    => false,
				'separator'   => 'after',
				'label_block' => true,
				'condition'   => array(
					'premium_gallery_img_link_type' => 'link',
					'premium_gallery_video!'        => 'true',
				),
			)
		);

		$img_repeater->add_control(
			'premium_gallery_link_whole',
			array(
				'label'     => __( 'Whole Image Link', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'premium_gallery_video!' => 'true',
				),
			)
		);

		$img_repeater->add_control(
			'premium_gallery_lightbox_whole',
			array(
				'label'     => __( 'Whole Image Lightbox', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'premium_gallery_video!' => 'true',
				),
			)
		);

		$this->add_control(
			'premium_gallery_img_content',
			array(
				'label'              => __( 'Images', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::REPEATER,
				'default'            => array(
					array(
						'premium_gallery_img_name'     => 'Image #1',
						'premium_gallery_img_category' => 'Category 1',
					),
					array(
						'premium_gallery_img_name'     => 'Image #2',
						'premium_gallery_img_category' => 'Category 2',
					),
				),
				'frontend_available' => true,
				'fields'             => $img_repeater->get_controls(),
				'title_field'        => '{{{ "" !== premium_gallery_img_name ? premium_gallery_img_name : "Image" }}} - {{{ "" !== premium_gallery_img_category ? premium_gallery_img_category : "No Categories" }}}',
			)
		);

		$this->add_control(
			'premium_gallery_shuffle_onload',
			array(
				'label'     => __( 'Shuffle Images on Page Load', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'premium_gallery_load_more!' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_gallery_yt_thumbnail_size',
			array(
				'label'       => __( 'Youtube Videos Thumbnail Size', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'maxresdefault' => __( 'Maximum Resolution', 'premium-addons-for-elementor' ),
					'hqdefault'     => __( 'High Quality', 'premium-addons-for-elementor' ),
					'mqdefault'     => __( 'Medium Quality', 'premium-addons-for-elementor' ),
					'sddefault'     => __( 'Standard Quality', 'premium-addons-for-elementor' ),
				),
				'default'     => 'maxresdefault',
				'label_block' => true,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_gallery_grid_settings',
			array(
				'label' => __( 'Display Options', 'premium-addons-for-elementor' ),

			)
		);

		$this->add_responsive_control(
			'premium_gallery_gap',
			array(
				'label'      => __( 'Image Gap', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 200,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-gallery-item' => 'padding: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'premium_gallery_img_style',
			array(
				'label'       => __( 'Skin', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'description' => __( 'Choose a layout style for the gallery', 'premium-addons-for-elementor' ),
				'options'     => array(
					'default' => __( 'Style 1', 'premium-addons-for-elementor' ),
					'style1'  => __( 'Style 2', 'premium-addons-for-elementor' ),
					'style2'  => __( 'Style 3', 'premium-addons-for-elementor' ),
					'style3'  => __( 'Style 4', 'premium-addons-for-elementor' ),
				),
				'default'     => 'default',
				'separator'   => 'before',
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_grid_style_notice',
			array(
				'raw'             => __( 'Style 4 works only with Even / Masonry Layout', 'premium-addons-for-elementor' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => array(
					'premium_gallery_img_style'       => 'style3',
					'premium_gallery_img_size_select' => 'metro',
				),
			)
		);

		$this->add_responsive_control(
			'premium_gallery_style1_border_border',
			array(
				'label'       => __( 'Height', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 700,
					),
				),
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}} .pa-gallery-img.style1 .premium-gallery-caption' => 'bottom: {{SIZE}}px;',
				),
				'condition'   => array(
					'premium_gallery_img_style' => 'style1',
				),
			)
		);

		$this->add_control(
			'premium_gallery_img_effect',
			array(
				'label'       => __( 'Hover Effect', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'description' => __( 'Choose a hover effect for the image', 'premium-addons-for-elementor' ),
				'options'     => array(
					'none'    => __( 'None', 'premium-addons-for-elementor' ),
					'zoomin'  => __( 'Zoom In', 'premium-addons-for-elementor' ),
					'zoomout' => __( 'Zoom Out', 'premium-addons-for-elementor' ),
					'scale'   => __( 'Scale', 'premium-addons-for-elementor' ),
					'gray'    => __( 'Grayscale', 'premium-addons-for-elementor' ),
					'blur'    => __( 'Blur', 'premium-addons-for-elementor' ),
					'bright'  => __( 'Bright', 'premium-addons-for-elementor' ),
					'sepia'   => __( 'Sepia', 'premium-addons-for-elementor' ),
					'trans'   => __( 'Translate', 'premium-addons-for-elementor' ),
				),
				'default'     => 'zoomin',
				'label_block' => true,
				'separator'   => 'after',
			)
		);

		$this->add_control(
			'premium_gallery_links_icon',
			array(
				'label'   => __( 'Links Icon', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'library' => 'fa-solid',
					'value'   => 'fas fa-link',
				),
			)
		);

		$this->add_control(
			'premium_gallery_videos_heading',
			array(
				'label'     => __( 'Videos', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'premium_gallery_video_icon',
			array(
				'label'        => __( 'Always Show Play Icon', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'default'      => 'true',
				'condition'    => array(
					'premium_gallery_img_style!' => 'style2',
				),

			)
		);

		$this->add_control(
			'premium_gallery_videos_icon',
			array(
				'label'   => __( 'Videos Play Icon', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'library' => 'fa-solid',
					'value'   => 'fas fa-play',
				),
			)
		);

		$this->add_control(
			'premium_gallery_rtl_mode',
			array(
				'label'       => __( 'RTL Mode', 'premium-addons-for-elementor' ),
				'description' => __( 'This option moves the origin of the grid to the right side. Useful for RTL direction sites', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'separator'   => 'before',
			)
		);

		$this->add_responsive_control(
			'premium_gallery_content_align',
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
				'default'   => 'center',
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .premium-gallery-caption' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_gallery_lightbox_section',
			array(
				'label' => __( 'Lightbox', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_gallery_light_box',
			array(
				'label'     => __( 'Lightbox', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'premium_gallery_lightbox_type',
			array(
				'label'     => __( 'Lightbox Style', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'default',
				'options'   => array(
					'default' => __( 'PrettyPhoto', 'premium-addons-for-elementor' ),
					'yes'     => __( 'Elementor', 'premium-addons-for-elementor' ),
					'no'      => __( 'Other Lightbox Plugin', 'premium-addons-for-elementor' ),
				),
				'condition' => array(
					'premium_gallery_light_box' => 'yes',
				),
			)
		);

		$this->add_control(
			'lightbox_show_title',
			array(
				'label'     => __( 'Show Image Title', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => array(
					'premium_gallery_light_box'     => 'yes',
					'premium_gallery_lightbox_type' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_gallery_lightbox_theme',
			array(
				'label'     => __( 'Lightbox Theme', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'pp_default'    => __( 'Default', 'premium-addons-for-elementor' ),
					'light_rounded' => __( 'Light Rounded', 'premium-addons-for-elementor' ),
					'dark_rounded'  => __( 'Dark Rounded', 'premium-addons-for-elementor' ),
					'light_square'  => __( 'Light Square', 'premium-addons-for-elementor' ),
					'dark_square'   => __( 'Dark Square', 'premium-addons-for-elementor' ),
					'facebook'      => __( 'Facebook', 'premium-addons-for-elementor' ),
				),
				'default'   => 'pp_default',
				'condition' => array(
					'premium_gallery_light_box'     => 'yes',
					'premium_gallery_lightbox_type' => 'default',
				),
			)
		);

		$this->add_control(
			'premium_gallery_overlay_gallery',
			array(
				'label'     => __( 'Overlay Gallery Images', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'premium_gallery_light_box'     => 'yes',
					'premium_gallery_lightbox_type' => 'default',
				),
			)
		);

		$this->add_control(
			'premium_gallery_lightbox_icon',
			array(
				'label'     => __( 'Lightbox Icon', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'library' => 'fa-solid',
					'value'   => 'fas fa-search',
				),
				'condition' => array(
					'premium_gallery_light_box' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_gallery_responsive_section',
			array(
				'label' => __( 'Responsive', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_gallery_responsive_switcher',
			array(
				'label'       => __( 'Responsive Controls', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'If the content text is not suiting well on specific screen sizes, you may enable this option which will hide the description text.', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_gallery_min_range',
			array(
				'label'       => __( 'Minimum Size', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => __( 'Note: minimum size for extra small screens is 1px.', 'premium-addons-for-elementor' ),
				'default'     => 1,
				'condition'   => array(
					'premium_gallery_responsive_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_gallery_max_range',
			array(
				'label'       => __( 'Maximum Size', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => __( 'Note: maximum size for extra small screens is 767px.', 'premium-addons-for-elementor' ),
				'default'     => 767,
				'condition'   => array(
					'premium_gallery_responsive_switcher' => 'yes',
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

		$docs = array(
			'https://premiumaddons.com/docs/grid-widget-tutorial' => __( 'Getting started »', 'premium-addons-for-elementor' ),
			'https://premiumaddons.com/docs/how-to-assign-an-image-to-multiple-categories' => __( 'How to assign a grid item to multiple categories »', 'premium-addons-for-elementor' ),
			'https://premiumaddons.com/docs/how-to-link-categories-in-elementor-gallery-widget/' => __( 'How To Link Categories In Media Grid Widget »', 'premium-addons-for-elementor' ),
			'https://premiumaddons.com/docs/how-to-open-a-popup-lightbox-through-a-grid-image' => __( 'How to open an Elementor popup/lightbox using a grid item »', 'premium-addons-for-elementor' ),
			'https://premiumaddons.com/docs/how-to-solve-media-grid-and-tabs-widgets-conflict/'  => __( 'How to Solve Media Grid and Tabs Widgets Conflict »', 'premium-addons-for-elementor' ),
		);

		$doc_index = 1;
		foreach ( $docs as $url => $title ) {

			$doc_url = Helper_Functions::get_campaign_link( $url, 'editor-page', 'wp-editor', 'get-support' );

			$this->add_control(
				'doc_' . $doc_index,
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => sprintf( '<a href="%s" target="_blank">%s</a>', $doc_url, $title ),
					'content_classes' => 'editor-pa-doc',
				)
			);

			$doc_index++;

		}

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_gallery_general_style',
			array(
				'label' => __( 'General', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'premium_gallery_general_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-img-gallery',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_gallery_general_border',
				'selector' => '{{WRAPPER}} .premium-img-gallery',
			)
		);

		$this->add_control(
			'premium_gallery_general_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-img-gallery' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'premium_gallery_general_box_shadow',
				'selector' => '{{WRAPPER}} .premium-img-gallery',
			)
		);

		$this->add_responsive_control(
			'premium_gallery_general_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-img-gallery' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'premium_gallery_general_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-img-gallery' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_gallery_img_style_section',
			array(
				'label' => __( 'Image', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'premium_gallery_icons_style_overlay',
			array(
				'label'     => __( 'Hover Overlay Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pa-gallery-img:not(.style2):hover .pa-gallery-icons-wrapper, {{WRAPPER}} .pa-gallery-img .pa-gallery-icons-caption-container, {{WRAPPER}} .pa-gallery-img:hover .pa-gallery-icons-caption-container' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_gallery_img_border',
				'selector' => '{{WRAPPER}} .pa-gallery-img-container',
			)
		);

		$this->add_control(
			'premium_gallery_img_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .pa-gallery-img-container' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'label'     => __( 'Shadow', 'premium-addons-for-elementor' ),
				'name'      => 'premium_gallery_img_box_shadow',
				'selector'  => '{{WRAPPER}} .pa-gallery-img-container',
				'condition' => array(
					'premium_gallery_img_style!' => 'style1',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'     => 'css_filters',
				'selector' => '{{WRAPPER}} .pa-gallery-img-container img',
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'label'    => __( 'Hover CSS Filters', 'premium-addons-for-elementor' ),
				'name'     => 'hover_css_filters',
				'selector' => '{{WRAPPER}} .premium-gallery-item:hover img',
			)
		);

		$this->add_responsive_control(
			'premium_gallery_img_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pa-gallery-img-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'premium_gallery_img_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pa-gallery-img-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_gallery_content_style',
			array(
				'label' => __( 'Title / Description', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'premium_gallery_title_heading',
			array(
				'label' => __( 'Title', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'premium_gallery_title_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-gallery-img-name, {{WRAPPER}} .premium-gallery-img-name a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'premium_gallery_title_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-gallery-img-name, {{WRAPPER}} .premium-gallery-img-name a',
			)
		);

		$this->add_responsive_control(
			'title_spacing',
			array(
				'label'     => __( 'Spacing', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => array(
					'{{WRAPPER}} .premium-gallery-img-name'  => 'margin-bottom: {{SIZE}}px',
				),
			)
		);

		$this->add_control(
			'premium_gallery_description_heading',
			array(
				'label'     => __( 'Description', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'premium_gallery_description_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-gallery-img-desc, {{WRAPPER}} .premium-gallery-img-desc a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'premium_gallery_description_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-gallery-img-desc, {{WRAPPER}} .premium-gallery-img-desc a',
			)
		);

		$this->add_control(
			'container_heading',
			array(
				'label' => __( 'Container', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'      => 'premium_gallery_content_background',
				'types'     => array( 'classic', 'gradient' ),
				'selector'  => '{{WRAPPER}} .premium-gallery-caption',
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_gallery_content_border',
				'selector' => '{{WRAPPER}} .premium-gallery-caption',
			)
		);

		$this->add_control(
			'premium_gallery_content_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-gallery-caption' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'label'    => __( 'Shadow', 'premium-addons-for-elementor' ),
				'name'     => 'premium_gallery_content_shadow',
				'selector' => '{{WRAPPER}} .premium-gallery-caption',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'premium_gallery_content_box_shadow',
				'selector' => '{{WRAPPER}} .premium-gallery-caption',
			)
		);

		$this->add_responsive_control(
			'premium_gallery_content_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-gallery-caption' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'premium_gallery_content_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-gallery-caption' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_gallery_icons_style',
			array(
				'label' => __( 'Icons', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'premium_gallery_style1_icons_position',
			array(
				'label'       => __( 'Position', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', '%', 'em', 'custom' ),
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 300,
					),
				),
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}} .pa-gallery-img:not(.style2) .pa-gallery-icons-inner-container' => 'top: {{SIZE}}{{UNIT}};',
				),
				'condition'   => array(
					'premium_gallery_img_style!' => 'style2',
				),
			)
		);

		$this->add_responsive_control(
			'premium_gallery_icons_size',
			array(
				'label'       => __( 'Size', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', 'em' ),
				'range'       => array(
					'px' => array(
						'min' => 1,
						'max' => 50,
					),
				),
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}} .pa-gallery-icons-inner-container i, {{WRAPPER}} .pa-gallery-icons-caption-cell i' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .pa-gallery-icons-inner-container svg, {{WRAPPER}} .pa-gallery-icons-caption-cell svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->start_controls_tabs( 'premium_gallery_icons_style_tabs' );

		$this->start_controls_tab(
			'premium_gallery_icons_style_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_gallery_icons_style_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .pa-gallery-magnific-image i, {{WRAPPER}} .pa-gallery-img-link i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .pa-gallery-magnific-image svg, {{WRAPPER}} .pa-gallery-img-link svg' => 'fill: {{VALUE}}; color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'premium_gallery_icons_style_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .pa-gallery-magnific-image span, {{WRAPPER}} .pa-gallery-img-link span' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_gallery_icons_style_border',
				'selector' => '{{WRAPPER}} .pa-gallery-magnific-image span, {{WRAPPER}} .pa-gallery-img-link span',
			)
		);

		$this->add_control(
			'premium_gallery_icons_style_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pa-gallery-magnific-image span, {{WRAPPER}} .pa-gallery-img-link span' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'label'    => __( 'Shadow', 'premium-addons-for-elementor' ),
				'name'     => 'premium_gallery_icons_style_shadow',
				'selector' => '{{WRAPPER}} .pa-gallery-magnific-image span, {{WRAPPER}} .pa-gallery-img-link span',
			)
		);

		$this->add_responsive_control(
			'premium_gallery_icons_style_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pa-gallery-magnific-image span, {{WRAPPER}} .pa-gallery-img-link span' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_gallery_icons_style_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pa-gallery-magnific-image span, {{WRAPPER}} .pa-gallery-img-link span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'premium_gallery_icons_style_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_gallery_icons_style_color_hover',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .pa-gallery-magnific-image:hover i, {{WRAPPER}} .pa-gallery-img-link:hover i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pa-gallery-magnific-image:hover svg, {{WRAPPER}} .pa-gallery-img-link:hover svg' => 'fill: {{VALUE}}; color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'premium_gallery_icons_style_background_hover',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .pa-gallery-magnific-image:hover span, {{WRAPPER}} .pa-gallery-img-link:hover span' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_gallery_icons_style_border_hover',
				'selector' => '{{WRAPPER}} .pa-gallery-magnific-image:hover span, {{WRAPPER}} .pa-gallery-img-link:hover span',
			)
		);

		$this->add_control(
			'premium_gallery_icons_style_border_radius_hover',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pa-gallery-magnific-image:hover span, {{WRAPPER}} .pa-gallery-img-link:hover span' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'label'    => __( 'Shadow', 'premium-addons-for-elementor' ),
				'name'     => 'premium_gallery_icons_style_shadow_hover',
				'selector' => '{{WRAPPER}} {{WRAPPER}} .pa-gallery-magnific-image:hover span, {{WRAPPER}} .pa-gallery-img-link:hover span',
			)
		);

		$this->add_responsive_control(
			'premium_gallery_icons_style_margin_hover',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pa-gallery-magnific-image:hover span, {{WRAPPER}} .pa-gallery-img-link:hover span' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_gallery_icons_style_padding_hover',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pa-gallery-magnific-image:hover span, {{WRAPPER}} .pa-gallery-img-link:hover span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_gallery_filter_style',
			array(
				'label'     => __( 'Filter', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_gallery_filter' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'premium_gallery_filter_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-gallery-cats-container li a.category',
			)
		);

		$this->start_controls_tabs( 'premium_gallery_filters' );

		$this->start_controls_tab(
			'premium_gallery_filters_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_gallery_filter_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-gallery-cats-container li a.category' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_gallery_background_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-gallery-cats-container li a.category' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_gallery_filter_border',
				'selector' => '{{WRAPPER}} .premium-gallery-cats-container li a.category',
			)
		);

		$this->add_control(
			'premium_gallery_filter_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-gallery-cats-container li a.category'  => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'filter_adv_radius!' => 'yes',
				),
			)
		);

		$this->add_control(
			'filter_adv_radius',
			array(
				'label'       => __( 'Advanced Border Radius', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Apply custom radius values. Get the radius value from ', 'premium-addons-for-elementor' ) . '<a href="https://9elements.github.io/fancy-border-radius/" target="_blank">here</a>',
			)
		);

		$this->add_control(
			'filter_adv_radius_value',
			array(
				'label'     => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'selectors' => array(
					'{{WRAPPER}} .premium-gallery-cats-container li a.category' => 'border-radius: {{VALUE}};',
				),
				'condition' => array(
					'filter_adv_radius' => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'premium_gallery_filters_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_gallery_filter_hover_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-gallery-cats-container li a:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_gallery_background_hover_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-gallery-cats-container li a:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_gallery_filter_border_hover',
				'selector' => '{{WRAPPER}} .premium-gallery-cats-container li a.category:hover',
			)
		);

		$this->add_control(
			'premium_gallery_filter_border_radius_hover',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-gallery-cats-container li a.category:hover'  => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'filter_hover_adv_radius!' => 'yes',
				),
			)
		);

		$this->add_control(
			'filter_hover_adv_radius',
			array(
				'label'       => __( 'Advanced Border Radius', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Apply custom radius values. Get the radius value from ', 'premium-addons-for-elementor' ) . '<a href="https://9elements.github.io/fancy-border-radius/" target="_blank">here</a>',
			)
		);

		$this->add_control(
			'filter_hover_adv_radius_value',
			array(
				'label'     => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'selectors' => array(
					'{{WRAPPER}} .premium-gallery-cats-container li a.category:hover' => 'border-radius: {{VALUE}};',
				),
				'condition' => array(
					'filter_hover_adv_radius' => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'premium_gallery_filters_active',
			array(
				'label' => __( 'Active', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_gallery_filter_active_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-gallery-cats-container li a.active' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_gallery_background_active_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-gallery-cats-container li a.active' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_gallery_filter_border_active',
				'selector' => '{{WRAPPER}} .premium-gallery-cats-container li a.active',
			)
		);

		$this->add_control(
			'premium_gallery_filter_border_radius_active',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-gallery-cats-container li a.active'  => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'filter_active_adv_radius!' => 'yes',
				),
			)
		);

		$this->add_control(
			'filter_active_adv_radius',
			array(
				'label'       => __( 'Advanced Border Radius', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Apply custom radius values. Get the radius value from ', 'premium-addons-for-elementor' ) . '<a href="https://9elements.github.io/fancy-border-radius/" target="_blank">here</a>',
			)
		);

		$this->add_control(
			'filter_active_adv_radius_value',
			array(
				'label'     => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'selectors' => array(
					'{{WRAPPER}} .premium-gallery-cats-container li a.active' => 'border-radius: {{VALUE}};',
				),
				'condition' => array(
					'filter_active_adv_radius' => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'premium_gallery_filter_shadow',
				'selector' => '{{WRAPPER}} .premium-gallery-cats-container li a.category',
			)
		);

		$this->add_responsive_control(
			'premium_gallery_filter_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-gallery-cats-container li a.category' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_gallery_filter_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-gallery-cats-container li a.category' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_gallery_button_style_settings',
			array(
				'label'     => __( 'Load More Button', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_gallery_load_more' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'premium_gallery_button_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-gallery-load-more-btn',
			)
		);

		$this->start_controls_tabs( 'premium_gallery_button_style_tabs' );

		$this->start_controls_tab(
			'premium_gallery_button_style_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_gallery_button_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-gallery-load-more-btn'  => 'color: {{VALUE}};',
					'{{WRAPPER}} .premium-gallery-load-more-btn .premium-loader'  => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_gallery_button_spin_color',
			array(
				'label'     => __( 'Spinner Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-gallery-load-more-btn .premium-loader'  => 'border-top-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'premium_gallery_button_text_shadow',
				'selector' => '{{WRAPPER}} .premium-gallery-load-more-btn',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'premium_gallery_button_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-gallery-load-more-btn',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_gallery_button_border',
				'selector' => '{{WRAPPER}} .premium-gallery-load-more-btn',
			)
		);

		$this->add_control(
			'premium_gallery_button_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-gallery-load-more-btn' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'premium_gallery_button_box_shadow',
				'selector' => '{{WRAPPER}} .premium-gallery-load-more-btn',
			)
		);

		$this->add_responsive_control(
			'premium_gallery_button_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-gallery-load-more' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_gallery_button_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-gallery-load-more-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'premium_gallery_button_style_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_gallery_button_hover_color',
			array(
				'label'     => __( 'Text Hover Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-gallery-load-more-btn:hover'  => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'premium_gallery_button_text_shadow_hover',
				'selector' => '{{WRAPPER}} .premium-gallery-load-more-btn:hover',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'premium_gallery_button_background_hover',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-gallery-load-more-btn:hover',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_gallery_button_border_hover',
				'selector' => '{{WRAPPER}} .premium-gallery-load-more-btn:hover',
			)
		);

		$this->add_control(
			'button_border_radius_hover',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-gallery-load-more-btn:hover' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'premium_gallery_button_shadow_hover',
				'selector' => '{{WRAPPER}} .premium-gallery-load-more-btn:hover',
			)
		);

		$this->add_responsive_control(
			'button_margin_hover',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-gallery-load-more-btn:hover' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_gallery_button_padding_hover',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-gallery-load-more-btn:hover' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_lightbox_style',
			array(
				'label'     => __( 'Lightbox', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_gallery_lightbox_type' => 'yes',
				),
			)
		);

		$this->add_control(
			'lightbox_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'#elementor-lightbox-slideshow-{{ID}}' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'lightbox_ui_color',
			array(
				'label'     => __( 'UI Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'#elementor-lightbox-slideshow-{{ID}} .dialog-lightbox-close-button, #elementor-lightbox-slideshow-{{ID}} .elementor-swiper-button' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'lightbox_ui_hover_color',
			array(
				'label'     => __( 'UI Hover Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'#elementor-lightbox-slideshow-{{ID}} .dialog-lightbox-close-button:hover, #elementor-lightbox-slideshow-{{ID}} .elementor-swiper-button:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->update_controls();

	}

	/**
	 * Filter Cats
	 *
	 * Formats Category to be inserted in class attribute.
	 *
	 * @since 2.1.0
	 * @access public
	 *
	 * @param string $string category slug.
	 *
	 * @return string $cat_filtered slug filtered.
	 */
	public function filter_cats( $string ) {

		$cat_filtered = trim( $string );

		$cat_filtered = mb_strtolower( $cat_filtered );

		if ( strpos( $cat_filtered, 'class' ) || strpos( $cat_filtered, 'src' ) ) {
			$cat_filtered = substr( $cat_filtered, strpos( $cat_filtered, '"' ) + 1 );
			$cat_filtered = strtok( $cat_filtered, '"' );
			$cat_filtered = preg_replace( '/[http:.]/', '', $cat_filtered );
			$cat_filtered = str_replace( '/', '', $cat_filtered );
		}

		$cat_filtered = str_replace( ', ', ',', $cat_filtered );
		$cat_filtered = preg_replace( '/[\s_&@!#%]/', '-', $cat_filtered );
		$cat_filtered = str_replace( ',', ' ', $cat_filtered );

		return $cat_filtered;
	}

	/**
	 * Render Filter Tabs on the frontend
	 *
	 * @since 2.1.0
	 * @access protected
	 *
	 * @param string  $first Class for the first category.
	 * @param integer $active_index active category index.
	 */
	protected function render_filter_tabs( $first, $active_index ) {

		$settings = $this->get_settings_for_display();

		?>

		<div class="premium-img-gallery-filter">
			<ul class="premium-gallery-cats-container">
				<?php if ( 'yes' === $settings['premium_gallery_first_cat_switcher'] ) : ?>
					<li>
						<a href="javascript:;" class="category <?php echo esc_attr( $first ); ?>" data-filter="*">
							<?php echo wp_kses_post( $settings['premium_gallery_first_cat_label'] ); ?>
						</a>
					</li>
					<?php
				endif;
				foreach ( $settings['premium_gallery_cats_content'] as $index => $category ) {
					if ( ! empty( $category['premium_gallery_img_cat'] ) ) {
						$cat_filtered = $this->filter_cats( $category['premium_gallery_img_cat'] );

						$key = 'premium_grid_category_' . $index;

						if ( $active_index === $index ) {
							$this->add_render_attribute( $key, 'class', 'active' );
						}

						$this->add_render_attribute(
							$key,
							'class',
							array(
								'category',
								'elementor-repeater-item-' . $category['_id'],
							)
						);

						$slug = sprintf( '.%s', $cat_filtered );

						$this->add_render_attribute( $key, 'data-filter', $slug );
						?>
						<li>
							<a href="javascript:;" <?php echo wp_kses_post( $this->get_render_attribute_string( $key ) ); ?>>
								<?php echo wp_kses_post( $category['premium_gallery_img_cat'] ); ?>
							</a>
						</li>
						<?php
					}
				}
				?>
			</ul>
		</div>

		<?php
	}

	/**
	 * Render Grid output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 2.1.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		$filter = $settings['premium_gallery_filter'];

		$skin = $settings['premium_gallery_img_style'];

		$layout = $settings['premium_gallery_img_size_select'];

		$lightbox = $settings['premium_gallery_light_box'];

		$lightbox_type = $settings['premium_gallery_lightbox_type'];

		$show_play = $settings['premium_gallery_video_icon'];

		if ( 'yes' === $settings['premium_gallery_responsive_switcher'] ) {
			$min_size = $settings['premium_gallery_min_range'] . 'px';
			$max_size = $settings['premium_gallery_max_range'] . 'px';
		}

		$category = '*';

		if ( 'yes' === $filter ) {

			if ( ! empty( $settings['premium_gallery_active_cat'] ) || 0 === $settings['premium_gallery_active_cat'] ) {

				if ( 'yes' !== $settings['premium_gallery_first_cat_switcher'] ) {
					$active_index     = $settings['premium_gallery_active_cat'];
					$active_category  = $settings['premium_gallery_cats_content'][ $active_index ]['premium_gallery_img_cat'];
					$category         = '.' . $this->filter_cats( $active_category );
					$active_cat_index = $settings['premium_gallery_active_cat'];

				} else {
					$active_cat_index = $settings['premium_gallery_active_cat'] - 1;
				}
			} else {
				$active_cat_index = 'yes' === $settings['premium_gallery_first_cat_switcher'] ? -1 : 0;
			}

			$is_all_active = ( 0 > $active_cat_index ) ? 'active' : '';

		}

		if ( 'original' === $layout ) {
			$layout = 'masonry';
		} elseif ( 'one_size' === $layout ) {
			$layout = 'fitRows';
		}

		$ltr_mode = 'yes' === $settings['premium_gallery_rtl_mode'] ? false : true;

		$shuffle = 'yes' === $settings['premium_gallery_shuffle'] ? true : false;

		$shuffle_onload = 'yes' === $settings['premium_gallery_shuffle_onload'] ? 'random' : 'original-order';

		$grid_settings = array(
			'img_size'   => $layout,
			'filter'     => $filter,
			'theme'      => $settings['premium_gallery_lightbox_theme'],
			'active_cat' => $category,
			'ltr_mode'   => $ltr_mode,
			'shuffle'    => $shuffle,
			'sort_by'    => $shuffle_onload,
			'skin'       => $skin,
		);

		if ( 'yes' === $filter ) {
			$grid_settings['flag'] = ! empty( $settings['url_flag'] ) ? $settings['url_flag'] : 'cat';
		}

		$load_more = 'yes' === $settings['premium_gallery_load_more'] ? true : false;

		if ( $load_more ) {
			$minimum      = ! empty( $settings['premium_gallery_load_minimum'] ) ? $settings['premium_gallery_load_minimum'] : 6;
			$click_number = ! empty( $settings['premium_gallery_load_click_number'] ) ? $settings['premium_gallery_load_click_number'] : 6;

			$grid_settings = array_merge(
				$grid_settings,
				array(
					'load_more'    => $load_more,
					'minimum'      => $minimum,
					'click_images' => $click_number,
				)
			);
		}

		if ( 'yes' === $lightbox ) {
			$grid_settings = array_merge(
				$grid_settings,
				array(
					'light_box'     => $lightbox,
					'lightbox_type' => $lightbox_type,
					'overlay'       => 'yes' === $settings['premium_gallery_overlay_gallery'] ? true : false,
				)
			);
		} else {
			$this->add_render_attribute(
				'grid',
				array(
					'class' => array(
						'premium-img-gallery-no-lightbox',
					),
				)
			);
		}

		$this->add_render_attribute(
			'grid',
			array(
				'id'    => 'premium-img-gallery-' . esc_attr( $this->get_id() ),
				'class' => array(
					'elementor-invisible',
					'premium-img-gallery',
					'premium-img-gallery-' . $layout,
					$settings['premium_gallery_img_effect'],
				),
			)
		);

		if ( $show_play ) {
			$this->add_render_attribute(
				'grid',
				array(
					'class' => array(
						'premium-gallery-icon-show',
					),
				)
			);
		}

		$this->add_render_attribute(
			'gallery_container',
			array(
				'class'         => 'premium-gallery-container',
				'data-settings' => wp_json_encode( $grid_settings ),
			)
		);

		$this->add_render_attribute(
			'image_container',
			'class',
			array(
				'pa-gallery-img-container',
			)
		);

		?>

	<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'grid' ) ); ?>>
		<?php
		if ( 'yes' === $filter ) :
			$this->render_filter_tabs( $is_all_active, $active_cat_index );
		endif;
		?>

		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'gallery_container' ) ); ?>>

			<?php if ( 'metro' === $layout ) : ?>
				<div class="grid-sizer"></div>
				<?php
			endif;

			foreach ( $settings['premium_gallery_img_content'] as $index => $image ) :

				$key = 'gallery_item_' . $index;

				$image_id = apply_filters( 'wpml_object_id', $image['premium_gallery_img']['id'], 'elementor_library', true );

				$image_by_id = get_post( $image_id );

				$alt = '';
				if ( isset( $image_by_id->post_title ) ) {
					$alt = apply_filters( 'pa_grid_image_alt', get_post( $image_id )->post_title );
				}

				$this->add_render_attribute(
					$key,
					array(
						'class' => array(
							'premium-gallery-item',
							'elementor-repeater-item-' . $image['_id'],
							$this->filter_cats( $image['premium_gallery_img_category'] ),
						),
					)
				);

				if ( $load_more && $index > $minimum - 1 ) {

					$this->add_render_attribute( $key, 'class', 'premium-gallery-item-hidden' );

				}

				if ( $image['premium_gallery_video'] ) {
					$this->add_render_attribute( $key, 'class', 'premium-gallery-video-item' );
				}
				?>
				<div <?php echo wp_kses_post( $this->get_render_attribute_string( $key ) ); ?>>
				<div class="pa-gallery-img <?php echo esc_attr( $skin ); ?>" onclick="">
					<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'image_container' ) ); ?>>
						<?php
							$video_data = $this->render_grid_item( $image, $index );

							$image['video_link']  = $video_data['link'];
							$image['video_thumb'] = $video_data['thumbnail'];
						if ( 'style3' === $skin ) :
							?>
							<div class="pa-gallery-icons-wrapper">
								<div class="pa-gallery-icons-inner-container">
									<?php $this->render_icons( $image, $index, $alt ); ?>
								</div>
							</div>
						<?php endif; ?>
					</div>
					<?php
					if ( 'style2' !== $skin ) :
						if ( 'default' === $skin || 'style1' === $skin ) :
							?>
							<div class="pa-gallery-icons-wrapper">
								<div class="pa-gallery-icons-inner-container">
									<?php $this->render_icons( $image, $index, $alt ); ?>
								</div>
							</div>
							<?php
						endif;
						$this->render_image_caption( $image );
					else :
						?>
						<div class="pa-gallery-icons-caption-container">
							<div class="pa-gallery-icons-caption-cell">
								<?php
									$this->render_icons( $image, $index, $alt );
									$this->render_image_caption( $image );
								?>
							</div>
						</div>
						<?php
					endif;
					if ( $image['premium_gallery_video'] ) :
						?>
							</div>
						</div>
						<?php
						continue;
					endif;
					if ( 'yes' === $image['premium_gallery_link_whole'] ) {

						$link_key = 'image_link_' . $index;

						$this->add_render_attribute(
							$link_key,
							array(
								'class' => 'pa-gallery-whole-link',
							)
						);

						if ( 'url' === $image['premium_gallery_img_link_type'] ) {
							$icon_link = $image['premium_gallery_img_link'];
						} else {
							$icon_link = get_permalink( $image['premium_gallery_img_existing'] );
						}

						if ( ! empty( $icon_link ) ) {

							if ( 'url' === $image['premium_gallery_img_link_type'] ) {
								$this->add_link_attributes( $link_key, $icon_link );

							} else {
								$this->add_render_attribute( $link_key, 'href', $icon_link );
							}

							?>
							<a <?php echo wp_kses_post( $this->get_render_attribute_string( $link_key ) ); ?>>
								<span>
									<?php echo wp_kses_post( $image['premium_gallery_img_name'] ); ?>
								</span>
							</a>
							<?php
						}
					} elseif ( 'yes' === $lightbox ) {

						if ( 'yes' === $image['premium_gallery_lightbox_whole'] ) {

							$lightbox_key = 'image_lightbox_' . $index;

							$this->add_render_attribute(
								$lightbox_key,
								array(
									'class' => 'pa-gallery-whole-link',
									'href'  => $image['premium_gallery_img']['url'],
								)
							);

							if ( 'default' !== $lightbox_type ) {

								$this->add_render_attribute(
									$lightbox_key,
									array(
										'data-elementor-open-lightbox'      => $lightbox_type,
										'data-elementor-lightbox-slideshow' => count( $settings['premium_gallery_img_content'] ) > 1 ? $this->get_id() : false,
									)
								);

								if ( 'yes' === $settings['lightbox_show_title'] ) {

									$this->add_render_attribute( $lightbox_key, 'data-elementor-lightbox-title', $alt );

								}
							} else {

								$rel = sprintf( 'prettyPhoto[premium-grid-%s]', $this->get_id() );

								$this->add_render_attribute(
									$lightbox_key,
									array(
										'data-rel' => $rel,
									)
								);
							}

							?>

								<a <?php echo wp_kses_post( $this->get_render_attribute_string( $lightbox_key ) ); ?>></a>

							<?php
						}
					}
					?>
				</div>
			</div>
			<?php endforeach; ?>

			<?php if ( 'yes' === $settings['gradient_layer'] ) : ?>
				<div class="premium-gallery-gradient-layer"></div>
			<?php endif; ?>
		</div>

		<?php if ( 'yes' === $settings['premium_gallery_load_more'] ) : ?>
			<div class="premium-gallery-load-more premium-gallery-btn-hidden">
				<button class="premium-gallery-load-more-btn">
					<?php echo wp_kses_post( $settings['premium_gallery_load_more_text'] ); ?>
					<div class="premium-loader"></div>
				</button>
			</div>
		<?php endif; ?>

	</div>

		<?php
		if ( \Elementor\Plugin::instance()->editor->is_edit_mode() ) {

			if ( 'metro' !== $settings['premium_gallery_img_size_select'] ) {
				$this->render_editor_script();
			}
		}
		?>

		<?php if ( 'yes' === $settings['premium_gallery_responsive_switcher'] ) : ?>
		<style>
			@media( min-width: <?php echo esc_attr( $min_size ); ?> ) and ( max-width:<?php echo esc_attr( $max_size ); ?> ) {
				#premium-img-gallery-<?php echo esc_attr( $this->get_id() ); ?> .premium-gallery-caption {
					display: none;
				}
			}
		</style>
	<?php endif; ?>

		<?php
	}

	/**
	 * Render Grid Image
	 *
	 * Written in PHP and used to generate the final HTML for image.
	 *
	 * @since 3.6.4
	 * @access protected
	 *
	 * @param array   $item image repeater item.
	 * @param integer $index item index.
	 */
	protected function render_grid_item( $item, $index ) {

		$settings = $this->get_settings();

		$image_src = $item['premium_gallery_img']['url'];
		$image_id  = attachment_url_to_postid( $image_src );

		$is_video = $item['premium_gallery_video'];

		$key = 'image_' . $index;

		if ( ! empty( $item['premium_gallery_img']['url'] ) ) {
			$settings['image_data'] = Helper_Functions::get_image_data( $image_id, $item['premium_gallery_img']['url'], $settings['thumbnail_size'] );
			$image_html             = Group_Control_Image_Size::get_attachment_image_html( $settings, 'thumbnail', 'image_data' );
		}

		if ( $is_video ) {

			$type = $item['premium_gallery_video_type'];

			if ( 'hosted' !== $type ) {
				$embed_params = $this->get_embed_params( $item );
				$link         = Embed::get_embed_url( $item['premium_gallery_video_url'], $embed_params );

				if ( 'youtube' === $type && 'yes' === $item['privacy_mode'] ) {
					$link = str_replace( '.com', '-nocookie.com', $link );
				}

				if ( empty( $image_html ) ) {

					$video_props = Embed::get_video_properties( $link );
					$id          = $video_props['video_id'];
					$type        = $video_props['provider'];
					$size        = '';

					if ( 'youtube' === $type ) {
						$size = $settings['premium_gallery_yt_thumbnail_size'];
					}
					$image_src = Helper_Functions::get_video_thumbnail( $id, $type, $size );

					$image_html = '<img src="' . esc_url( $image_src ) . '">';
				}
			} else {
				self::$check_self_hosted = true;
				$video_params            = $this->get_hosted_params( $item );
			}
		}

		if ( $is_video ) {
			?>
			<div class="premium-gallery-video-wrap" data-type="<?php echo esc_attr( $item['premium_gallery_video_type'] ); ?>">
				<?php if ( 'hosted' !== $item['premium_gallery_video_type'] ) : ?>
					<div class="premium-gallery-iframe-wrap" data-src="<?php echo esc_url( $link ); ?>"></div>
					<?php
				else :
					$link = empty( $item['premium_gallery_video_self_url'] ) ? $item['premium_gallery_video_self']['url'] : $item['premium_gallery_video_self_url'];
					?>
					<video src="<?php echo esc_url( $link ); ?>" <?php echo wp_kses_post( Utils::render_html_attributes( $video_params ) ); ?>></video>
				<?php endif; ?>
			</div>
		<?php } ?>
		<?php echo wp_kses_post( $image_html ); ?>
		<?php

		return array(
			'link'      => ( isset( $link ) && ! empty( $link ) ) ? $link : false,
			'thumbnail' => $image_src,
		);
	}

	/**
	 * Render Icons
	 *
	 * Render Lightbox and URL Icons HTML
	 *
	 * @since 3.6.4
	 * @access protected
	 *
	 * @param array   $item grid image repeater item.
	 * @param integer $index item index.
	 * @param string  $alt image alternative text.
	 */
	protected function render_icons( $item, $index, $alt ) {

		$settings = $this->get_settings_for_display();

		$lightbox_key = 'image_lightbox_' . $index;

		$link_key = 'image_link_' . $index;

		$href = $item['premium_gallery_img']['url'];

		$lightbox = $settings['premium_gallery_light_box'];

		$lightbox_type = $settings['premium_gallery_lightbox_type'];

		$is_video = $item['premium_gallery_video'];

		$id = $this->get_id();

		if ( $is_video ) {

			$type = $item['premium_gallery_video_type'];

			$this->add_render_attribute(
				$lightbox_key,
				array(
					'class'    => array(
						'pa-gallery-lightbox-wrap',
						'pa-gallery-magnific-image',
						'pa-gallery-video-icon',
					),
					'tabindex' => 0,
				)
			);

			if ( 'yes' === $lightbox ) {

				$lightbox_options = array(
					'privacy' => 'yes',
				);

				if ( 'default' !== $lightbox_type ) {

					if ( 1 === count( $settings['premium_gallery_img_content'] ) || self::$check_self_hosted ) {
						$lightbox_options = array(
							'type'         => 'video',
							'videoType'    => $item['premium_gallery_video_type'],
							'url'          => $item['video_link'],
							'modalOptions' => array(
								'id'               => 'elementor-lightbox-' . $id,
								'videoAspectRatio' => '169',
							),
						);

						if ( 'hosted' === $type ) {
							$lightbox_options['videoParams'] = $this->get_hosted_params( $item );
						}
					}

					$this->add_render_attribute(
						$lightbox_key,
						array(
							'data-elementor-open-lightbox' => 'yes',
							'data-elementor-lightbox'      => wp_json_encode( $lightbox_options ),
							'data-elementor-lightbox-video' => $item['video_link'],
						)
					);

					// Make sure videos slideshow is enabled only when there are no self hosted videos
					// Self hosted videos causes issue with slideshow
					if ( ! self::$check_self_hosted ) {
						$this->add_render_attribute( $lightbox_key, 'data-elementor-lightbox-slideshow', count( $settings['premium_gallery_img_content'] ) > 1 ? $this->get_id() : false );
					}
				} else {

					$rel  = sprintf( 'prettyPhoto[premium-grid-%s]', $this->get_id() );
					$link = ( 'hosted' === $type ) ? $item['video_link'] : $item['video_link'] . '&iframe=true';

					$this->add_render_attribute(
						$lightbox_key,
						array(
							'href'     => $link,
							'data-rel' => $rel,
						)
					);
				}
			}

			?>
			<div>
				<a <?php echo wp_kses_post( $this->get_render_attribute_string( $lightbox_key ) ); ?>>
					<span>
						<?php
						Icons_Manager::render_icon( $settings['premium_gallery_videos_icon'], array( 'aria-hidden' => 'true' ) );
						?>
					</span>
				</a>
			</div>

			<?php
			return;
		}

		if ( 'yes' === $lightbox ) {

			if ( 'yes' !== $item['premium_gallery_lightbox_whole'] ) {

				$this->add_render_attribute(
					$lightbox_key,
					array(
						'class' => 'pa-gallery-magnific-image',
						'href'  => $href,
					)
				);

				if ( 'default' !== $lightbox_type ) {

					$this->add_render_attribute(
						$lightbox_key,
						array(
							'data-elementor-open-lightbox' => $lightbox_type,
							'data-elementor-lightbox-slideshow' => count( $settings['premium_gallery_img_content'] ) > 1 ? $id : false,
						)
					);

					if ( 'yes' === $settings['lightbox_show_title'] ) {

						$this->add_render_attribute( $lightbox_key, 'data-elementor-lightbox-title', $alt );

					}
				} else {

					$rel = sprintf( 'prettyPhoto[premium-grid-%s]', $this->get_id() );

					$this->add_render_attribute(
						$lightbox_key,
						array(
							'data-rel' => $rel,
						)
					);

				}

				?>

					<a <?php echo wp_kses_post( $this->get_render_attribute_string( $lightbox_key ) ); ?>>
						<span>
							<?php
							Icons_Manager::render_icon( $settings['premium_gallery_lightbox_icon'], array( 'aria-hidden' => 'true' ) );
							?>
						</span>
					</a>
				<?php
			}
		}

		if ( ! empty( $item['premium_gallery_img_link']['url'] ) || ! empty( $item['premium_gallery_img_existing'] ) ) {

			if ( 'yes' !== $item['premium_gallery_link_whole'] ) {

				$this->add_render_attribute(
					$link_key,
					array(
						'class' => 'pa-gallery-img-link',
					)
				);

				if ( 'url' === $item['premium_gallery_img_link_type'] ) {
					$icon_link = $item['premium_gallery_img_link'];
				} else {
					$icon_link = get_permalink( $item['premium_gallery_img_existing'] );
				}

				if ( ! empty( $icon_link ) ) {

					if ( 'url' === $item['premium_gallery_img_link_type'] ) {
						$this->add_link_attributes( $link_key, $icon_link );
					} else {
						$this->add_render_attribute( $link_key, 'href', $icon_link );
					}

					?>
					<a <?php echo wp_kses_post( $this->get_render_attribute_string( $link_key ) ); ?>>
						<span>
							<?php
							Icons_Manager::render_icon( $settings['premium_gallery_links_icon'], array( 'aria-hidden' => 'true' ) );
							?>
						</span>
					</a>
					<?php
				}
			}
		}
	}

	/**
	 * Render Image Caption
	 *
	 * Written in PHP to render the final HTML for image title and description
	 *
	 * @since 3.6.4
	 * @access protected
	 *
	 * @param array $item image repeater item.
	 */
	protected function render_image_caption( $item ) {

		$media_lib = $item['premmium_gallery_img_info'];

		if ( 'yes' === $media_lib ) {
			$title = Control_Media::get_image_title( $item['premium_gallery_img'] );

			$description = get_post( $item['premium_gallery_img']['id'] )->post_content;
		} else {
			$title = $item['premium_gallery_img_name'];

			$description = $item['premium_gallery_img_desc'];
		}

		if ( ! empty( $title ) || ! empty( $description ) ) :
			?>
			<div class="premium-gallery-caption">

				<?php if ( ! empty( $title ) ) : ?>
					<span class="premium-gallery-img-name"><?php echo wp_kses_post( $title ); ?></span>
					<?php
				endif;

				if ( ! empty( $description ) ) :
					?>
					<p class="premium-gallery-img-desc"><?php echo wp_kses_post( $description ); ?></p>
				<?php endif; ?>

			</div>
			<?php
		endif;
	}

	/**
	 * Get Hosted Videos Parameters
	 *
	 * @since 3.7.0
	 * @access private
	 *
	 * @param array $item image repeater item.
	 */
	private function get_hosted_params( $item ) {

		$video_params = array();

		if ( $item['premium_gallery_video_controls'] ) {
			$video_params['controls'] = '';
		}

		if ( $item['premium_gallery_video_mute'] ) {
			$video_params['muted'] = 'muted';
		}

		if ( $item['premium_gallery_video_loop'] ) {
			$video_params['loop'] = '';
		}

		if ( 'yes' !== $item['download_button'] ) {
			$video_params['controlsList'] = 'nodownload';
		}

		$video_params['preload'] = 'none';

		return $video_params;
	}

	/**
	 * Get embeded videos parameters
	 *
	 * @since 3.7.0
	 * @access private
	 *
	 * @param array $item image repeater item.
	 */
	private function get_embed_params( $item ) {

		$video_params = array();

		$props = Embed::get_video_properties( $item['premium_gallery_video_url'] );

		$video_params['loop'] = $item['premium_gallery_video_loop'] ? '1' : '0';

		$video_params['autoplay'] = '1';

		$video_params['controls'] = $item['premium_gallery_video_controls'] ? '1' : '0';

		$key = 'youtube' === $item['premium_gallery_video_type'] ? 'mute' : 'muted';

		// $video_params['playlist'] = $props['video_id'];

		$video_params[ $key ] = $item['premium_gallery_video_mute'] ? '1' : '0';

		if ( 'vimeo' === $item['premium_gallery_video_type'] ) {
			$video_params['autopause'] = '0';
		}

		return $video_params;
	}

	/**
	 * Update Controls
	 *
	 * @since 3.8.8
	 * @access private
	 */
	private function update_controls() {

		$this->update_responsive_control(
			'premium_gallery_img_border_radius',
			array(
				'type'      => Controls_Manager::DIMENSIONS,
				'selectors' => array(
					'{{WRAPPER}} .pa-gallery-img-container, {{WRAPPER}} .pa-gallery-img:not(.style2) .pa-gallery-icons-wrapper, {{WRAPPER}} .pa-gallery-img.style2 .pa-gallery-icons-caption-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),

			)
		);

		$this->update_responsive_control(
			'premium_gallery_content_border_radius',
			array(
				'type'      => Controls_Manager::DIMENSIONS,
				'selectors' => array(
					'{{WRAPPER}} .premium-gallery-caption' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),

			)
		);

	}

	/**
	 * Render Editor Masonry Script.
	 *
	 * @since 3.12.3
	 * @access protected
	 */
	protected function render_editor_script() {

		?>
		<script type="text/javascript">
			jQuery( document ).ready( function( $ ) {

				$( '.premium-gallery-container' ).each( function() {

					var $node_id 	= '<?php echo esc_attr( $this->get_id() ); ?>',
						scope 		= $( '[data-id="' + $node_id + '"]' ),
						settings    = $(this).data("settings"),
						selector 	= $(this);

					if ( selector.closest( scope ).length < 1 ) {
						return;
					}

					var masonryArgs = {
						// set itemSelector so .grid-sizer is not used in layout
						filter 			: settings.active_cat,
						itemSelector	: '.premium-gallery-item',
						percentPosition : true,
						layoutMode		: settings.img_size,
					};

					var $isotopeObj = {};

					selector.imagesLoaded( function() {

						$isotopeObj = selector.isotope( masonryArgs );

						selector.find('.premium-gallery-item').resize( function() {
							$isotopeObj.isotope( 'layout' );
						});
					});

				});
			});
		</script>
		<?php
	}
}
