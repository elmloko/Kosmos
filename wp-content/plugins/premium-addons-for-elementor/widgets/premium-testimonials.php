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
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Typography;
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

		$this->start_controls_section(
			'premium_testimonial_person_settings',
			array(
				'label' => __( 'Author', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_testimonial_person_image',
			array(
				'label'       => __( 'Image', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::MEDIA,
				'dynamic'     => array( 'active' => true ),
				'default'     => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'description' => __( 'Choose an image for the author', 'premium-addons-for-elementor' ),
				'show_label'  => true,
			)
		);

		$this->add_control(
			'premium_testimonial_person_image_shape',
			array(
				'label'       => __( 'Image Style', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'description' => __( 'Choose image style', 'premium-addons-for-elementor' ),
				'options'     => array(
					'square'  => __( 'Square', 'premium-addons-for-elementor' ),
					'circle'  => __( 'Circle', 'premium-addons-for-elementor' ),
					'rounded' => __( 'Rounded', 'premium-addons-for-elementor' ),
				),
				'default'     => 'circle',
			)
		);

		$this->add_control(
			'premium_testimonial_person_name',
			array(
				'label'       => __( 'Name', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'John Doe',
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_testimonial_person_name_size',
			array(
				'label'       => __( 'HTML Tag', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'description' => __( 'Select a heading tag for author name', 'premium-addons-for-elementor' ),
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
				'label_block' => true,
			)
		);

		$this->add_control(
			'separator_text',
			array(
				'label'     => __( 'Separator', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'default'   => '-',
				'separator' => 'befpre',
			)
		);

		$this->add_control(
			'separator_align',
			array(
				'label'        => __( 'Align', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SELECT,
				'options'      => array(
					'row'    => __( 'Inline', 'premium-addons-for-elementor' ),
					'column' => __( 'Block', 'premium-addons-for-elementor' ),
				),
				'default'      => 'row',
				'prefix_class' => 'premium-testimonial-separator-',
				'selectors'    => array(
					'{{WRAPPER}} .premium-testimonial-author-info' => 'flex-direction: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'separator_spacing',
			array(
				'label'      => __( 'Spacing', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 5,
				),
				'selectors'  => array(
					'{{WRAPPER}}.premium-testimonial-separator-row .premium-testimonial-separator' => 'margin-right: calc( {{SIZE}}{{UNIT}}/2 ); margin-left: calc( {{SIZE}}{{UNIT}}/2 );',
					'{{WRAPPER}}.premium-testimonial-separator-column .premium-testimonial-separator' => 'margin-top: calc( {{SIZE}}{{UNIT}}/2 ); margin-bottom: calc( {{SIZE}}{{UNIT}}/2 );',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_testimonial_company_settings',
			array(
				'label' => __( 'Company', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_testimonial_company_name',
			array(
				'label'       => __( 'Name', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'default'     => 'Leap13',
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_testimonial_company_name_size',
			array(
				'label'       => __( 'HTML Tag', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'description' => __( 'Select a heading tag for company name', 'premium-addons-for-elementor' ),
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
				'description' => __( 'Add company URL', 'premium-addons-for-elementor' ),
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

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_testimonial_settings',
			array(
				'label' => __( 'Content', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_testimonial_content',
			array(
				'label'       => __( 'Testimonial Content', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::WYSIWYG,
				'dynamic'     => array( 'active' => true ),
				'default'     => __( 'Donec id elit non mi porta gravida at eget metus. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor. Cras mattis consectetur purus sit amet fermentum. Nullam id dolor id nibh ultricies vehicula ut id elit. Donec id elit non mi porta gravida at eget metus.', 'premium-addons-for-elementor' ),
				'label_block' => true,
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
				'label' => __( 'Image', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
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
					'size' => 110,
				),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 150,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-testimonial-img-wrapper' => 'width: {{SIZE}}{{UNIT}}; height:{{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'premium_testimonial_img_border_width',
			array(
				'label'     => __( 'Border Width (PX)', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'unit' => 'px',
					'size' => 2,
				),
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 15,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-testimonial-img-wrapper' => 'border-width: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'premium_testimonial_image_border_color',
			array(
				'label'     => __( 'Border Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-testimonial-img-wrapper' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_testimonials_person_style',
			array(
				'label' => __( 'Author', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'premium_testimonial_person_name_color',
			array(
				'label'     => __( 'Author Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-testimonial-person-name' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'author_name_typography',
				'label'    => __( 'Name Typograhy', 'premium-addons-for-elementor' ),
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
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

		$this->add_control(
			'premium_testimonial_separator_color',
			array(
				'label'     => __( 'Separator Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'separator' => 'before',
				'condition' => array(
					'separator_text!' => '',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-testimonial-separator' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'separator_typography',
				'label'     => __( 'Separator Typograhy', 'premium-addons-for-elementor' ),
				'condition' => array(
					'separator_text!' => '',
				),
				'selector'  => '{{WRAPPER}} .premium-testimonial-separator',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_testimonial_company_style',
			array(
				'label' => __( 'Company', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
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
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
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

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_testimonial_content_style',
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
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-testimonial-text-wrapper',
			)
		);

		$this->add_responsive_control(
			'premium_testimonial_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'top'    => 15,
					'bottom' => 15,
					'left'   => 0,
					'right'  => 0,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-testimonial-text-wrapper' => 'margin: {{top}}{{UNIT}} {{right}}{{UNIT}} {{bottom}}{{UNIT}} {{left}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_testimonial_quotes',
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
				'default'   => 'rgba(110,193,228,0.2)',
				'selectors' => array(
					'{{WRAPPER}} .premium-testimonial-upper-quote, {{WRAPPER}} .premium-testimonial-lower-quote'   => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_testimonial_quotes_size',
			array(
				'label'      => __( 'Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 120,
				),
				'range'      => array(
					'px' => array(
						'min' => 5,
						'max' => 250,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-testimonial-upper-quote, {{WRAPPER}} .premium-testimonial-lower-quote' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_testimonial_upper_quote_position',
			array(
				'label'      => __( 'Top Icon Position', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'top'  => 70,
					'left' => 12,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-testimonial-upper-quote' => 'top: {{TOP}}{{UNIT}}; left:{{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_testimonial_lower_quote_position',
			array(
				'label'      => __( 'Bottom Icon Position', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'bottom' => 3,
					'right'  => 12,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-testimonial-lower-quote' => 'right: {{RIGHT}}{{UNIT}}; bottom: {{BOTTOM}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_testimonial_container_style',
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
					'{{WRAPPER}} .premium-testimonial-content-wrapper, {{WRAPPER}} .premium-testimonial-author-info' => '{{VALUE}}',
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

		$this->add_inline_editing_attributes( 'premium_testimonial_person_name' );
		$this->add_inline_editing_attributes( 'premium_testimonial_company_name' );
		$this->add_inline_editing_attributes( 'premium_testimonial_content', 'advanced' );
		$person_title_tag = Helper_Functions::validate_html_tag( $settings['premium_testimonial_person_name_size'] );

		$company_title_tag = Helper_Functions::validate_html_tag( $settings['premium_testimonial_company_name_size'] );

		$image_src = '';

		if ( ! empty( $settings['premium_testimonial_person_image']['url'] ) ) {
			$image_src = $settings['premium_testimonial_person_image']['url'];
			$alt       = esc_attr( Control_Media::get_image_alt( $settings['premium_testimonial_person_image'] ) );
		}

		$this->add_render_attribute(
			'testimonial',
			'class',
			array(
				'premium-testimonial-box',
			)
		);

		$this->add_render_attribute(
			'img_wrap',
			'class',
			array(
				'premium-testimonial-img-wrapper',
				$settings['premium_testimonial_person_image_shape'],
			)
		);

		if ( 'yes' === $settings['premium_testimonial_company_link_switcher'] ) {

			$this->add_link_attributes( 'link', $settings['premium_testimonial_company_link'] );
			$this->add_render_attribute( 'link', 'class', 'premium-testimonial-company-link' );

		}

		?>

	<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'testimonial' ) ); ?>>
		<div class="premium-testimonial-container">
			<i class="fa fa-quote-left premium-testimonial-upper-quote"></i>
			<div class="premium-testimonial-content-wrapper">
				<?php if ( ! empty( $image_src ) ) : ?>
					<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'img_wrap' ) ); ?>>
						<img src="<?php echo esc_url( $image_src ); ?>" alt="<?php echo esc_attr( $alt ); ?>" class="premium-testimonial-person-image">
					</div>
				<?php endif; ?>

				<div class="premium-testimonial-text-wrapper">
					<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'premium_testimonial_content' ) ); ?>>
						<?php echo $this->parse_text_editor( $settings['premium_testimonial_content'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
				</div>

				<div class="premium-testimonial-author-info">
					<<?php echo wp_kses_post( $person_title_tag ); ?> class="premium-testimonial-person-name">
						<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'premium_testimonial_person_name' ) ); ?>>
							<?php echo wp_kses_post( $settings['premium_testimonial_person_name'] ); ?>
						</span>
					</<?php echo wp_kses_post( $person_title_tag ); ?>>

					<span class="premium-testimonial-separator"><?php echo esc_html( $settings['separator_text'] ); ?></span>

					<<?php echo wp_kses_post( $company_title_tag ); ?> class="premium-testimonial-company-name">
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
					</<?php echo wp_kses_post( $company_title_tag ); ?>>
				</div>
			</div>
			<i class="fa fa-quote-right premium-testimonial-lower-quote"></i>
		</div>
	</div>
		<?php

	}

	/**
	 * Render Testimonials widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function content_template() {
		?>
		<#

			view.addInlineEditingAttributes('premium_testimonial_person_name');
			view.addInlineEditingAttributes('premium_testimonial_company_name');
			view.addInlineEditingAttributes('premium_testimonial_content', 'advanced');
			view.addRenderAttribute('premium_testimonial_company_name', 'class', 'premium-testimonial-company-link');

			var personTag = elementor.helpers.validateHTMLTag( settings.premium_testimonial_person_name_size ),
				companyTag = elementor.helpers.validateHTMLTag( settings.premium_testimonial_company_name_size ),
				imageSrc = '',
				imageSrc,
				borderRadius;

			if( '' != settings.premium_testimonial_person_image.url ) {
				imageSrc = settings.premium_testimonial_person_image.url;
			}

			view.addRenderAttribute('testimonial', 'class', [
				'premium-testimonial-box'
			]);

			view.addRenderAttribute('img_wrap', 'class', [
				'premium-testimonial-img-wrapper',
				settings.premium_testimonial_person_image_shape
			]);


		#>

			<div {{{ view.getRenderAttributeString('testimonial') }}}>
				<div class="premium-testimonial-container">
					<i class="fa fa-quote-left premium-testimonial-upper-quote"></i>
					<div class="premium-testimonial-content-wrapper">
						<# if ( '' != imageSrc ) { #>
							<div {{{ view.getRenderAttributeString('img_wrap') }}}>
								<img src="{{ imageSrc }}" alt="premium-image" class="premium-testimonial-person-image">
							</div>
						<# } #>
						<div class="premium-testimonial-text-wrapper">
							<div {{{ view.getRenderAttributeString('premium_testimonial_content') }}}>{{{ settings.premium_testimonial_content }}}</div>
						</div>

						<div class="premium-testimonial-author-info">
							<{{{personTag}}} class="premium-testimonial-person-name">
								<span {{{ view.getRenderAttributeString('premium_testimonial_person_name') }}}>
								{{{ settings.premium_testimonial_person_name }}}
								</span>
							</{{{personTag}}}>

							<span class="premium-testimonial-separator"> {{{ settings.separator_text }}} </span>

							<{{{companyTag}}} class="premium-testimonial-company-name">
								<a href="{{ settings.premium_testimonial_company_link }}" {{{ view.getRenderAttributeString('premium_testimonial_company_name') }}}>
									{{{ settings.premium_testimonial_company_name }}}
								</a>
							</{{{companyTag}}}>
						</div>

					</div>

					<i class="fa fa-quote-right premium-testimonial-lower-quote"></i>

				</div>
			</div>

		<?php
	}

}
