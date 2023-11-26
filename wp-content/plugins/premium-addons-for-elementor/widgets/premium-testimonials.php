<?php
/**
 * Premium Testimonials.
 */

namespace PremiumAddons\Widgets;

// Elementor Classes.
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Widget_Base;
use Elementor\Utils;
use Elementor\Control_Media;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;

// PremiumAddons Classes.
use PremiumAddons\Includes\Helper_Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

/**
 * Class Premium_Testimonials
 */
class Premium_Testimonials extends Widget_Base {

	/**
	 * Retrieve Widget Name.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_name() {
		return 'premium-addon-testimonials';
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return __( 'Testimonial', 'premium-addons-for-elementor' );
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
		return 'pa-testimonials';
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
			'font-awesome-5-all',
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
		return array( 'pa', 'premium', 'quote', 'appreciate', 'rating', 'review', 'recommendation' );
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
	 * Register Testimonials controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

        $papro_activated = apply_filters( 'papro_activated', false );

		$this->start_controls_section(
			'testimonial_section',
			array(
				'label' => __( 'Testimonial', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'multiple',
			array(
				'label'       => __( 'Multiple Testimonials', 'premium-addons-for-elementor' ),
				'description' => __( 'Enable this option if you need to add multiple testimonials', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
			)
		);

		$this->start_controls_tabs(
			'author_info_tabs',
			array(
				'condition' => array(
					'multiple!' => 'yes',
				),
			)
		);

		$this->start_controls_tab(
			'author_info_tab_author',
			array(
				'label' => __( 'Author', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_testimonial_person_image',
			array(
				'label'      => __( 'Image', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::MEDIA,
				'dynamic'    => array( 'active' => true ),
				'default'    => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'show_label' => true,
			)
		);

		$this->add_control(
			'premium_testimonial_person_name',
			array(
				'label'       => __( 'Name', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'Joseph L.Mabie',
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'author_info_tab_content',
			array(
				'label' => __( 'Content', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'heading',
			array(
				'label'       => __( 'Heading', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Leave empty if not needed.', 'premium-addons-for-elementor' ),
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_testimonial_company_name',
			array(
				'label'       => __( 'Job', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'default'     => 'Influencer',
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_testimonial_company_link_switcher',
			array(
				'label'   => __( 'Link', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'premium_testimonial_company_link',
			array(
				'label'       => __( 'Link', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::URL,
				'default'     => array(
					'is_external' => true,
				),
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
				'condition'   => array(
					'premium_testimonial_company_link_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'rating',
			array(
				'label'       => __( 'Rating Score', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
                'min'         => 0,
				'max'         => 5,
				'description' => __( 'Leave empty if not needed.', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_testimonial_content',
			array(
				'label'       => __( 'Content', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::WYSIWYG,
				'dynamic'     => array( 'active' => true ),
				'default'     => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida. Risus commodo viverra maecenas accumsan lacus vel facilisis.',
				'label_block' => true,
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$repeater = new REPEATER();

		$repeater->add_control(
			'person_image',
			array(
				'label'      => __( 'Image', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::MEDIA,
				'dynamic'    => array( 'active' => true ),
				'default'    => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'show_label' => true,
			)
		);

		$repeater->add_control(
			'heading',
			array(
				'label'       => __( 'Heading', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Leave empty if not needed.', 'premium-addons-for-elementor' ),
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'person_name',
			array(
				'label'       => __( 'Name', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'default'     => 'Joseph L.Mabie',
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'company_name',
			array(
				'label'       => __( 'Job', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'default'     => 'Influencer',
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'link_switcher',
			array(
				'label' => __( 'Link', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$repeater->add_control(
			'link',
			array(
				'label'       => __( 'Link', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::URL,
				'default'     => array(
					'is_external' => true,
				),
				'label_block' => true,
				'condition'   => array(
					'link_switcher' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'rating',
			array(
				'label'       => __( 'Rating Score', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => __( 'Leave empty if not needed.', 'premium-addons-for-elementor' ),
                'min'         => 0,
				'max'         => 5,
			)
		);

		$repeater->add_control(
			'content',
			array(
				'label'       => __( 'Content', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::WYSIWYG,
				'dynamic'     => array( 'active' => true ),
				'default'     => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida. Risus commodo viverra maecenas accumsan lacus vel facilisis.',
				'label_block' => true,
			)
		);

		$this->add_control(
			'multiple_testimonials',
			array(
				'label'       => __( 'Testimonials', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::REPEATER,
				'default'     => array(
					array(
						'person_name'  => 'Joseph L.Mabie',
						'company_name' => 'Influencer',
						'heading'      => 'Great Support Team',
					),
					array(
						'person_name'  => 'Debra Campbell',
						'company_name' => 'Web Developer',
						'heading'      => 'Very Powerful',
					),
					array(
						'person_name'  => 'Joanne Ellis',
						'company_name' => 'Content Creator',
						'heading'      => 'Excellent Service',
					),
				),
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{person_name}}}',
				'condition'   => array(
					'multiple' => 'yes',
				),
			)
		);

		$this->add_control(
			'carousel',
			array(
				'label'              => __( 'Carousel', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'condition'          => array(
					'multiple' => 'yes',
					'skin!'    => 'skin4',
				),
			)
		);

		$this->add_control(
			'carousel_play',
			array(
				'label'              => __( 'Auto Play', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'conditions'         => array(
					'terms' => array(
						array(
							'name'  => 'multiple',
							'value' => 'yes',
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'terms' => array(
										array(
											'name'     => 'skin',
											'operator' => '!==',
											'value'    => 'skin4',
										),
										array(
											'name'     => 'carousel',
											'operator' => '===',
											'value'    => 'yes',
										),
									),
								),
								array(
									'terms' => array(
										array(
											'name'     => 'skin',
											'operator' => '===',
											'value'    => 'skin4',
										),
									),
								),

							),
						),
					),
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'speed',
			array(
				'label'              => __( 'Autoplay Speed', 'premium-addons-for-elementor' ),
				'description'        => __( 'Autoplay Speed means at which time the next slide should come. Set a value in milliseconds (ms)', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::NUMBER,
				'default'            => 5000,
				'conditions'         => array(
					'terms' => array(
						array(
							'name'  => 'multiple',
							'value' => 'yes',
						),
                        array(
							'name'  => 'carousel_play',
							'value' => 'yes',
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'terms' => array(
										array(
											'name'     => 'skin',
											'operator' => '!==',
											'value'    => 'skin4',
										),
										array(
											'name'     => 'carousel',
											'operator' => '===',
											'value'    => 'yes',
										),
									),
								),
								array(
									'terms' => array(
										array(
											'name'     => 'skin',
											'operator' => '===',
											'value'    => 'skin4',
										),
									),
								),

							),
						),
					),
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
					'multiple' => 'yes',
					'carousel' => 'yes',
					'skin!'    => 'skin4',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-testimonial-box a.carousel-arrow.carousel-next' => 'right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .premium-testimonial-box a.carousel-arrow.carousel-prev' => 'left: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'display_option_section',
			array(
				'label' => __( 'Display Options', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'skin',
			array(
				'label'              => __( 'Choose Skin', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => 'skin1',
				'options'            => array(
					'skin1' => apply_filters( 'pa_pro_label', __( 'Skin 1', 'premium-addons-for-elementor' ) ),
					'skin2' => apply_filters( 'pa_pro_label', __( 'Skin 2 (Pro)', 'premium-addons-for-elementor' ) ),
					'skin3' => apply_filters( 'pa_pro_label', __( 'Skin 3 (Pro)', 'premium-addons-for-elementor' ) ),
					'skin4' => apply_filters( 'pa_pro_label', __( 'Skin 4 (Pro)', 'premium-addons-for-elementor' ) ),
				),
				'prefix_class'       => 'premium-testimonial__',
				'label_block'        => true,
				'render_type'        => 'template',
				'frontend_available' => true,
			)
		);

        if ( ! $papro_activated ) {

			$get_pro = Helper_Functions::get_campaign_link( 'https://premiumaddons.com/pro', 'editor-page', 'wp-editor', 'get-pro' );

			$this->add_control(
				'pro_skins_notice',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => __( 'This option is available in Premium Addons Pro. ', 'premium-addons-for-elementor' ) . '<a href="' . esc_url( $get_pro ) . '" target="_blank">' . __( 'Upgrade now!', 'premium-addons-for-elementor' ) . '</a>',
					'content_classes' => 'papro-upgrade-notice',
					'condition'       => array(
						'skin!' => 'skin1',
					),
				)
			);

		}

		$this->add_control(
			'skin_notice',
			array(
				'raw'             => __( 'This skin can be used with four or more testimonials.', 'premium-addons-for-elementor' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => array(
					'multiple' => 'yes',
					'skin'     => 'skin4',
				),
			)
		);

		$this->add_control(
			'show_image',
			array(
				'label'     => __( 'Show Author Image', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => array(
					'skin!' => 'skin4',
				),
			)
		);

		$this->add_control(
			'img_position',
			array(
				'label'              => __( 'Image Position', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::SELECT,
				'options'            => array(
					'relative' => __( 'Relative', 'premium-addons-for-elementor' ),
					'absolute' => __( 'Absolute', 'premium-addons-for-elementor' ),
				),
				'default'            => 'relative',
				'label_block'        => true,
				'frontend_available' => true,
				'condition'          => array(
					'show_image' => 'yes',
					'skin'       => 'skin1',
				),
			)
		);

		$this->add_control(
			'img_place',
			array(
				'label'        => __( 'Select Position', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => array(
					'top'    => array(
						'title' => __( 'Top', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-flex eicon-justify-start-h premium-rotate-icon',
					),
					'bottom' => array(
						'title' => __( 'Bottom', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-flex eicon-justify-end-h premium-rotate-icon',
					),
				),
				'default'      => 'top',
				'prefix_class' => 'premium-testimonial__img-',
				'label_block'  => true,
				'condition'    => array(
					'show_image'   => 'yes',
					'skin'         => 'skin1',
					'img_position' => 'absolute',
				),
			)
		);

        if ( $papro_activated ) {

            do_action( 'pa_testimonials_skins_options', $this );

        }



		$this->add_control(
			'premium_testimonial_person_name_size',
			array(
				'label'       => __( 'Name HTML Tag', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
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
				'default'     => 'h3',
				'separator'   => 'before',
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_testimonial_company_name_size',
			array(
				'label'       => __( 'Job HTML Tag', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
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
				'default'     => 'h4',
				'label_block' => true,
			)
		);

		$this->add_control(
			'icon_style',
			array(
				'label'       => __( 'Quotation Skin', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'rounded' => __( 'Rounded', 'premium-addons-for-elementor' ),
					'sharp'   => __( 'Sharp', 'premium-addons-for-elementor' ),
				),
				'default'     => 'rounded',
				'label_block' => true,
			)
		);

		$this->add_responsive_control(
			'testimonials_per_row',
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
					'{{WRAPPER}} .premium-testimonial-container' => 'width: {{VALUE}}',
				),
				'separator'          => 'before',
				'condition'          => array(
					'multiple' => 'yes',
					'skin!'    => 'skin4',
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
					'{{WRAPPER}} .premium-testimonial-container' => 'padding: 0 {{RIGHT}}{{UNIT}} 0 {{LEFT}}{{UNIT}}; margin: {{TOP}}{{UNIT}} 0 {{BOTTOM}}{{UNIT}} 0',
				),
			)
		);

        $this->add_control(
			'spacing_notice',
			array(
				'raw'             => __( 'Note, you may need to give a top/bottom spacing if you are not seeing the whole quotation icon or the author image.', 'premium-addons-for-elementor' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
			)
		);

        $this->add_control(
			'equal_height',
			array(
				'label'       => __( 'Equal Height', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'This option searches for the testimonial with the largest height and applies that height to the other testimonials', 'premium-addons-for-elementor' ),
                'prefix_class'=> 'premium-testimonial__equal-',
				'condition'  => array(
					'multiple' => 'yes',
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

		$doc_url = Helper_Functions::get_campaign_link( 'https://premiumaddons.com/docs/why-im-not-able-to-see-elementor-font-awesome-5-icons-in-premium-add-ons', 'editor-page', 'wp-editor', 'get-support' );
		$title   = __( 'I\'m not able to see Font Awesome icons in the widget »', 'premium-addons-for-elementor' );

		$this->add_control(
			'doc_1',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf( '<a href="%s" target="_blank">%s</a>', $doc_url, $title ),
				'content_classes' => 'editor-pa-doc',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_testimonial_image_style',
			array(
				'label'     => __( 'Author Image', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_image' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_testimonial_img_size',
			array(
				'label'      => __( 'Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 100,
				),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 150,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-testimonial-img-wrapper' => 'width: {{SIZE}}{{UNIT}}; height:{{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .premium-testimonial__carousel' => 'width: calc( 3 * {{SIZE}}{{UNIT}} )',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'img_border',
				'selector' => '{{WRAPPER}} .premium-testimonial-img-wrapper',
			)
		);

		$this->add_control(
			'active_border',
			array(
				'label'     => __( 'Active Border Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .slick-center .premium-testimonial-img-wrapper' => 'border-color: {{VALUE}} !important',
				),
				'condition' => array(
					'multiple' => 'yes',
					'skin'     => 'skin4',
				),
			)
		);

		$this->add_control(
			'img_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-testimonial-img-wrapper' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

        $this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'     => 'css_filters',
				'selector' => '{{WRAPPER}} .premium-testimonial-img-wrapper',
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'     => 'hover_css_filters',
				'label'    => __( 'Hover CSS Filters', 'premium-addons-for-elementor' ),
				'selector' => '{{WRAPPER}} .premium-testimonial-container:hover .premium-testimonial-img-wrapper',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'heading_style_section',
			array(
				'label' => __( 'Heading', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'heading_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-testimonial-heading' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'heading_typography',
				'selector' => '{{WRAPPER}} .premium-testimonial-heading',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'heading_shadow',
				'selector' => '{{WRAPPER}} .premium-testimonial-heading',
			)
		);

		$this->add_responsive_control(
			'heading_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-testimonial-heading' => 'margin: {{top}}{{UNIT}} {{right}}{{UNIT}} {{bottom}}{{UNIT}} {{left}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'person_style_section',
			array(
				'label' => __( 'Author', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'premium_testimonial_person_name_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-testimonial-person-name' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'author_name_typography',
				'selector' => '{{WRAPPER}} .premium-testimonial-person-name',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'author_name_shadow',
				'selector' => '{{WRAPPER}} .premium-testimonial-person-name',
			)
		);

		$this->add_responsive_control(
			'name_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-testimonial-person-name' => 'margin: {{top}}{{UNIT}} {{right}}{{UNIT}} {{bottom}}{{UNIT}} {{left}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'company_style_section',
			array(
				'label' => __( 'Job', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'job_align',
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
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .premium-testimonial-author-info' => 'align-items: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'premium_testimonial_company_name_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-testimonial-company-link' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'company_name_typography',
				'selector' => '{{WRAPPER}} .premium-testimonial-company-link',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'company_name_shadow',
				'selector' => '{{WRAPPER}} .premium-testimonial-company-link',
			)
		);

		$this->add_responsive_control(
			'job_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-testimonial-job' => 'margin: {{top}}{{UNIT}} {{right}}{{UNIT}} {{bottom}}{{UNIT}} {{left}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'rating_style_section',
			array(
				'label' => __( 'Rating Score', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'star_size',
			array(
				'label'   => __( 'Star Size', 'premium-addons-pro' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 50,
				'default' => 15,
			)
		);

		$this->add_control(
			'fill',
			array(
				'label'   => __( 'Star Color', 'premium-addons-pro' ),
				'type'    => Controls_Manager::COLOR,
				'global'  => false,
				'default' => '#ffab40',
			)
		);

		$this->add_control(
			'empty',
			array(
				'label'  => __( 'Empty Star Color', 'premium-addons-pro' ),
				'type'   => Controls_Manager::COLOR,
				'global' => false,
			)
		);

		$this->add_responsive_control(
			'rating_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-testimonial__rating-wrapper' => 'margin: {{top}}{{UNIT}} {{right}}{{UNIT}} {{bottom}}{{UNIT}} {{left}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'content_style_section',
			array(
				'label' => __( 'Content', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'premium_testimonial_content_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-testimonial-text-wrapper' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'content_typography',
				'selector' => '{{WRAPPER}} .premium-testimonial-text-wrapper',
			)
		);

		$this->add_responsive_control(
			'premium_testimonial_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-testimonial-text-wrapper' => 'margin: {{top}}{{UNIT}} {{right}}{{UNIT}} {{bottom}}{{UNIT}} {{left}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'quotes_style_section',
			array(
				'label' => __( 'Quotation Icon', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'premium_testimonial_quote_icon_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#58BFCA',
				'selectors' => array(
					'{{WRAPPER}} .premium-testimonial-quote'   => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_testimonial_quotes_size',
			array(
				'label'      => __( 'Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 5,
						'max' => 250,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-testimonial-upper-quote svg, {{WRAPPER}} .premium-testimonial-lower-quote svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'premium_testimonial_upper_quote_position',
			array(
				'label'      => __( 'Top Icon Position', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				// 'default'    => array(
				// 'top'  => 0,
				// 'left' => 12,
				// 'unit' => 'px',
				// ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-testimonial-upper-quote' => 'top: {{TOP}}{{UNIT}}; left:{{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'skin!' => 'skin3',
				),
			)
		);

		$this->add_responsive_control(
			'premium_testimonial_lower_quote_position',
			array(
				'label'      => __( 'Bottom Icon Position', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				// 'default'    => array(
				// 'bottom' => 3,
				// 'right'  => 12,
				// 'unit'   => 'px',
				// ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-testimonial-lower-quote' => 'right: {{RIGHT}}{{UNIT}}; bottom: {{BOTTOM}}{{UNIT}};',
				),
				'condition'  => array(
					'skin!' => array( 'skin2', 'skin4' ),
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'carousel_style_section',
			array(
				'label'     => __( 'Carousel', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'carousel' => 'yes',
					'skin!'    => 'skin4',
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
					'{{WRAPPER}} .premium-testimonial-box .slick-arrow' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .premium-testimonial-box .slick-arrow:hover' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .premium-testimonial-box .slick-arrow i' => 'font-size: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .premium-testimonial-box .slick-arrow' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .premium-testimonial-box .slick-arrow:hover' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .premium-testimonial-box .slick-arrow' => 'border-radius: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .premium-testimonial-box .slick-arrow' => 'padding: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'container_style_section',
			array(
				'label' => __( 'Container', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'content_align',
			array(
				'label'                => __( 'Alignment', 'premium-addons-for-elementor' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => array(
					'left'   => array(
						'title' => __( 'Left', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'selectors_dictionary' => array(
					'left'   => 'align-items: flex-start; justify-content: flex-start; text-align: left',
					'center' => 'align-items: center; justify-content: flex-start; text-align: center',
					'right'  => 'align-items: flex-end; justify-content: flex-start; text-align: right',
				),
				'default'              => 'center',
				'selectors'            => array(
					'{{WRAPPER}} .premium-testimonial-content-wrapper' => '{{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'premium_testimonial_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-testimonial-content-wrapper',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_testimonial_container_border',
				'selector' => '{{WRAPPER}} .premium-testimonial-content-wrapper',
			)
		);

		$this->add_control(
			'premium_testimonial_container_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-testimonial-content-wrapper' => 'border-radius: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'container_adv_radius!' => 'yes',
				),
			)
		);

		$this->add_control(
			'container_adv_radius',
			array(
				'label'       => __( 'Advanced Border Radius', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Apply custom radius values. Get the radius value from ', 'premium-addons-for-elementor' ) . '<a href="https://9elements.github.io/fancy-border-radius/" target="_blank">here</a>',
			)
		);

		$this->add_control(
			'container_adv_radius_value',
			array(
				'label'     => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'selectors' => array(
					'{{WRAPPER}} .premium-testimonial-content-wrapper' => 'border-radius: {{VALUE}};',
				),
				'condition' => array(
					'container_adv_radius' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'premium_testimonial_container_box_shadow',
				'selector' => '{{WRAPPER}} .premium-testimonial-content-wrapper',
			)
		);

		$this->add_responsive_control(
			'premium_testimonial_box_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-testimonial-content-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Render Testimonials widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

        $papro_activated = apply_filters( 'papro_activated', false );

        if ( ! $papro_activated || version_compare( PREMIUM_PRO_ADDONS_VERSION, '2.9.8', '<' ) ) {

			if ( 'skin1' !== $settings['skin'] ) {

				?>
				<div class="premium-error-notice">
					<?php
						$message = __( 'This option is available in <b>Premium Addons Pro</b>.', 'premium-addons-for-elementor' );
						echo wp_kses_post( $message );
					?>
				</div>
				<?php
				return false;

			}
		}

		$person_title_tag = Helper_Functions::validate_html_tag( $settings['premium_testimonial_person_name_size'] );
		$job_tag          = Helper_Functions::validate_html_tag( $settings['premium_testimonial_company_name_size'] );

		$this->add_render_attribute(
			'testimonials_container',
			'class',
			array(
				'premium-testimonial-box',
			)
		);

		$show_image = 'skin4' !== $settings['skin'] ? $settings['show_image'] : 'yes';

		if ( 'yes' === $show_image ) {
			$this->add_render_attribute( 'img_wrap', 'class', 'premium-testimonial-img-wrapper' );
		}

		if ( 'yes' !== $settings['multiple'] ) {

			$this->add_inline_editing_attributes( 'premium_testimonial_person_name' );
			$this->add_inline_editing_attributes( 'premium_testimonial_company_name' );
			$this->add_inline_editing_attributes( 'heading' );
			$this->add_inline_editing_attributes( 'premium_testimonial_content', 'advanced' );

			if ( 'yes' === $show_image ) {

				$image_src = '';

				if ( ! empty( $settings['premium_testimonial_person_image']['url'] ) ) {
					$image_src = $settings['premium_testimonial_person_image']['url'];
					$alt       = esc_attr( Control_Media::get_image_alt( $settings['premium_testimonial_person_image'] ) );
				}
			}

			if ( 'yes' === $settings['premium_testimonial_company_link_switcher'] ) {

				$this->add_link_attributes( 'link', $settings['premium_testimonial_company_link'] );
				$this->add_render_attribute( 'link', 'class', 'premium-testimonial-company-link' );

			}
		} else {

			$testimonials = $settings['multiple_testimonials'];

			$this->add_render_attribute( 'testimonials_container', 'class', 'multiple-testimonials' );
			// $this->add_render_attribute( 'testimonials_container', 'data-testimonials-equal', $settings['multiple_equal_height'] );

		}

		$carousel = 'yes' === $settings['carousel'] ? true : false;

		if ( $carousel ) {

			$this->add_render_attribute( 'testimonials_container', 'data-carousel', $carousel );

			$speed = ! empty( $settings['carousel_autoplay_speed'] ) ? $settings['carousel_autoplay_speed'] : 5000;

			$this->add_render_attribute(
				'testimonials_container',
				array(
					'data-rtl' => is_rtl(),
				)
			);

		}

		?>

		<?php
		if ( 'yes' === $settings['multiple'] && 'skin4' === $settings['skin'] ) {

			$images_markup = '';

			foreach ( $testimonials as $index => $testimonial ) {

				$testionial_image_html = $this->get_author_image( $testimonial );
				$images_markup        .= '<div class="premium-testimonial__carousel-img premium-testimonial-img-wrapper" data-index="' . $index . '">' . $testionial_image_html . '</div>';
			}

			echo '<div class="premium-testimonial__carousel">' . wp_kses_post( $images_markup ) . '</div>';
		}
		?>

		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'testimonials_container' ) ); ?>>

			<?php if ( 'yes' !== $settings['multiple'] ) : ?>

				<div class="premium-testimonial-container">

					<?php if ( 'skin3' !== $settings['skin'] ) : ?>
						<div class="premium-testimonial-upper-quote">
							<?php $this->render_quote_icon(); ?>
						</div>
					<?php endif; ?>

					<div class="premium-testimonial-content-wrapper">

						<?php if ( ! empty( $settings['heading'] ) ) : ?>
							<div class="premium-testimonial-heading">
								<p <?php echo wp_kses_post( $this->get_render_attribute_string( 'heading' ) ); ?>>
									<?php echo wp_kses_post( $settings['heading'] ); ?>
								</p>
							</div>
						<?php endif; ?>

						<div class="premium-testimonial-text-wrapper">
							<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'premium_testimonial_content' ) ); ?>>
								<?php echo $this->parse_text_editor( $settings['premium_testimonial_content'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</div>
						</div>

						<?php if ( ! empty( $settings['rating'] ) ) : ?>
							<div class="premium-testimonial__rating-wrapper">
								<?php echo Helper_Functions::render_rating_stars( $settings['rating'], $settings['fill'], $settings['empty'], $settings['star_size'] ); ?>
							</div>
						<?php endif; ?>

						<?php if ( ! in_array( $settings['skin'], array( 'skin1', 'skin4' ), true ) ) : ?>
							<div class="premium-testimonial__img-info">
						<?php endif; ?>


							<?php if ( ! empty( $image_src ) ) : ?>
								<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'img_wrap' ) ); ?>>
									<img src="<?php echo esc_url( $image_src ); ?>" alt="<?php echo esc_attr( $alt ); ?>" class="premium-testimonial-person-image">
								</div>
							<?php endif; ?>


							<div class="premium-testimonial-author-info">
								<<?php echo wp_kses_post( $person_title_tag ); ?> class="premium-testimonial-person-name">
									<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'premium_testimonial_person_name' ) ); ?>>
										<?php echo wp_kses_post( $settings['premium_testimonial_person_name'] ); ?>
									</span>
								</<?php echo wp_kses_post( $person_title_tag ); ?>>

								<?php if ( ! empty( $settings['premium_testimonial_company_name'] ) ) : ?>
									<<?php echo wp_kses_post( $job_tag ); ?> class="premium-testimonial-job">
									<?php if ( 'yes' === $settings['premium_testimonial_company_link_switcher'] ) : ?>
										<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'link' ) ); ?>>
											<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'premium_testimonial_company_name' ) ); ?>>
												<?php echo wp_kses_post( $settings['premium_testimonial_company_name'] ); ?>
											</span>
										</a>
									<?php else : ?>
										<span class="premium-testimonial-company-link" <?php echo wp_kses_post( $this->get_render_attribute_string( 'premium_testimonial_company_name' ) ); ?>>
											<?php echo wp_kses_post( $settings['premium_testimonial_company_name'] ); ?>
										</span>
									<?php endif; ?>
									</<?php echo wp_kses_post( $job_tag ); ?>>
								<?php endif; ?>
							</div>

						<?php if ( ! in_array( $settings['skin'], array( 'skin1', 'skin4' ), true ) ) : ?>
							</div>
						<?php endif; ?>

					</div>

					<?php if ( in_array( $settings['skin'], array( 'skin1', 'skin3' ), true ) ) : ?>
						<div class="premium-testimonial-lower-quote">
							<?php $this->render_quote_icon(); ?>
						</div>
					<?php endif; ?>
				</div>

			<?php else : ?>

				<?php if ( 'skin4' === $settings['skin'] ) : ?>
					<div class="premium-testimonial-upper-quote">
						<?php $this->render_quote_icon(); ?>
					</div>
				<?php endif; ?>

				<?php
				foreach ( $testimonials as $index => $testimonial ) :

					if ( 'yes' === $show_image ) {
						$testionial_image_html = $this->get_author_image( $testimonial );
					}

					if ( 'yes' === $testimonial['link_switcher'] ) {

						$this->add_render_attribute( 'link_' . $index, 'class', 'premium-testimonial-company-link' );
						$this->add_link_attributes( 'link_' . $index, $testimonial['link'] );
					}

					?>

					<div class="premium-testimonial-container">

						<?php if ( ! in_array( $settings['skin'], array( 'skin3', 'skin4' ), true ) ) : ?>
							<div class="premium-testimonial-upper-quote">
								<?php $this->render_quote_icon(); ?>
							</div>
						<?php endif; ?>

						<div class="premium-testimonial-content-wrapper">

							<?php if ( ! empty( $testimonial['heading'] ) ) : ?>
								<div class="premium-testimonial-heading">
									<p <?php echo wp_kses_post( $this->get_render_attribute_string( 'heading' ) ); ?>>
										<?php echo wp_kses_post( $testimonial['heading'] ); ?>
									</p>
								</div>
							<?php endif; ?>

							<div class="premium-testimonial-text-wrapper">
								<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'premium_testimonial_content' ) ); ?>>
									<?php echo $this->parse_text_editor( $testimonial['content'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</div>
							</div>

							<?php if ( ! empty( $testimonial['rating'] ) ) : ?>
								<div class="premium-testimonial__rating-wrapper">
									<?php echo Helper_Functions::render_rating_stars( $testimonial['rating'], $settings['fill'], $settings['empty'], $settings['star_size'] ); ?>
								</div>
							<?php endif; ?>

							<?php if ( ! in_array( $settings['skin'], array( 'skin1', 'skin4' ), true ) ) : ?>
								<div class="premium-testimonial__img-info">
							<?php endif; ?>

								<?php if ( 'skin4' !== $settings['skin'] ) : ?>
									<?php if ( ! empty( $testionial_image_html ) ) : ?>
										<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'img_wrap' ) ); ?>>
											<?php echo wp_kses_post( $testionial_image_html ); ?>
										</div>
									<?php endif; ?>
								<?php endif; ?>

								<div class="premium-testimonial-author-info">
									<<?php echo wp_kses_post( $person_title_tag ); ?> class="premium-testimonial-person-name">
										<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'premium_testimonial_person_name' ) ); ?>>
											<?php echo wp_kses_post( $testimonial['person_name'] ); ?>
										</span>
									</<?php echo wp_kses_post( $person_title_tag ); ?>>

									<?php if ( ! empty( $testimonial['company_name'] ) ) : ?>
										<<?php echo wp_kses_post( $job_tag ); ?> class="premium-testimonial-job">
										<?php if ( 'yes' === $testimonial['link_switcher'] ) : ?>
											<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'link_' . $index ) ); ?>>
												<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'premium_testimonial_company_name' ) ); ?>>
													<?php echo wp_kses_post( $testimonial['company_name'] ); ?>
												</span>
											</a>
										<?php else : ?>
											<span class="premium-testimonial-company-link" <?php echo wp_kses_post( $this->get_render_attribute_string( 'premium_testimonial_company_name' ) ); ?>>
												<?php echo wp_kses_post( $testimonial['company_name'] ); ?>
											</span>
										<?php endif; ?>
										</<?php echo wp_kses_post( $job_tag ); ?>>
									<?php endif; ?>
								</div>

							<?php if ( ! in_array( $settings['skin'], array( 'skin1', 'skin4' ), true ) ) : ?>
								</div>
							<?php endif; ?>

						</div>

						<?php if ( in_array( $settings['skin'], array( 'skin1', 'skin3' ), true ) ) : ?>
							<div class="premium-testimonial-lower-quote">
								<?php $this->render_quote_icon(); ?>
							</div>
						<?php endif; ?>

					</div>

				<?php endforeach; ?>
			<?php endif; ?>


		</div>
		<?php

	}

	/**
	 * Render Quote Icon
	 *
	 * @since 4.10.13
	 * @access protected
	 */
	protected function render_quote_icon() {

		$settings = $this->get_settings_for_display();

		if ( 'rounded' === $settings['icon_style'] ) {

			$svg_html = '<svg id="Layer_1" class="premium-testimonial-quote" xmlns="http://www.w3.org/2000/svg" width="48" height="37" viewBox="0 0 48 37"><path class="cls-1" d="m37,37c6.07,0,11-4.93,11-11s-4.93-11-11-11c-.32,0-.63.02-.94.05.54-4.81,2.18-9.43,4.79-13.52.19-.31.2-.7.03-1.01-.18-.32-.51-.52-.88-.52h-2c-.27,0-.54.11-.73.31-5.14,5.41-11.27,14.26-11.27,25.69,0,6.07,4.93,10.99,11,11h0Zm-26,0c6.07,0,11-4.93,11-11s-4.93-11-11-11c-.32,0-.63.02-.94.05.54-4.81,2.18-9.43,4.79-13.52.19-.31.2-.7.03-1.01-.18-.32-.51-.52-.87-.52h-2c-.27,0-.54.11-.73.31C6.13,5.72,0,14.57,0,26c0,6.07,4.93,10.99,11,11h0Zm0,0"/></svg>';

		} else {

			$svg_html = '<svg id="Layer_1" class="premium-testimonial-quote" xmlns="http://www.w3.org/2000/svg" width="48" height="37.5" viewBox="0 0 48 37.5"><path class="cls-1" d="m21,16.5v21H0v-21.3C0,1.8,13.5,0,13.5,0l1.8,4.2s-6,.9-7.2,5.7c-1.2,3.6,1.2,6.6,1.2,6.6h11.7Zm27,0v21h-21v-21.3C27,1.8,40.5,0,40.5,0l1.8,4.2s-6,.9-7.2,5.7c-1.2,3.6,1.2,6.6,1.2,6.6h11.7Z"/></svg>';
		}

		echo $svg_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	}

	/**
	 * Get Author Image
	 *
	 * @since 4.10.13
	 * @access protected
	 */
	protected function get_author_image( $testimonial ) {

		$testionial_image_html = '';
		if ( ! empty( $testimonial['person_image']['url'] ) ) {

			$image_src = $testimonial['person_image']['url'];
			$image_id  = attachment_url_to_postid( $image_src );

			$settings['image_data'] = Helper_Functions::get_image_data( $image_id, $testimonial['person_image']['url'], 'thumbnail' );
			$testionial_image_html  = Group_Control_Image_Size::get_attachment_image_html( $settings, 'thumbnail', 'image_data' );

		}

		return $testionial_image_html;

	}

}
