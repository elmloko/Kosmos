<?php
/**
 * PA WooCommerce Skin Grid - Default.
 *
 * @package PA
 */

namespace PremiumAddons\Modules\Woocommerce\Skins;

use Elementor\Repeater;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Skin_Base as Elementor_Skin_Base;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

use PremiumAddons\Modules\Woocommerce\Widgets\Woo_Products;
use PremiumAddons\Includes\Helper_Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

/**
 * Class Skin_Grid_Base
 *
 * @property Products $parent
 */
abstract class Skin_Base extends Elementor_Skin_Base {

	/**
	 * Register control actions.
	 *
	 * @since 4.7.0
	 * @access protected
	 */
	protected function _register_controls_actions() {
		// Product Rating Style.
		add_action( 'elementor/element/premium-woo-products/section_image_style/after_section_end', array( $this, 'register_product_rating_style' ) );
		// Product Price Style.
		add_action( 'elementor/element/premium-woo-products/section_image_style/after_section_end', array( $this, 'register_product_price_style' ) );

		add_action( 'elementor/element/premium-woo-products/section_image_style/after_section_end', array( $this, 'register_title_style_controls' ) );

		add_action( 'elementor/element/premium-woo-products/section_image_style/after_section_end', array( $this, 'register_style_sale_controls' ) );

		add_action( 'elementor/element/premium-woo-products/section_image_style/after_section_end', array( $this, 'register_style_featured_controls' ) );

		add_action( 'elementor/element/premium-woo-products/section_image_style/after_section_end', array( $this, 'register_cat_style_controls' ) );
		// Product Quick View Style.
		add_action( 'elementor/element/premium-woo-products/section_image_style/after_section_end', array( $this, 'register_quick_view_modal_style_controls' ), 20 );
	}


	/**
	 * Register Style Sale section.
	 *
	 * @access public
	 * @since 4.8.3
	 *
	 * @param array $pro_skins   pro skins.
	 */
	public function register_style_sale_controls() {

		$this->start_controls_section(
			'section_sale_style',
			array(
				'label'     => __( 'Sale Ribbon', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'sale' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'sale_size',
			array(
				'label'      => __( 'Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 20,
						'max' => 200,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-woocommerce:not(.premium-woo-skin-grid-10) .premium-woo-product-sale-wrap .premium-woo-product-onsale' => 'min-height: {{SIZE}}{{UNIT}}; min-width: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'_skin!' => 'grid-10',
				),
			)
		);

		$this->add_control(
			'sale_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-woo-product-sale-wrap .premium-woo-product-onsale' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'sale_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-woocommerce:not(.premium-woo-skin-grid-10) .premium-woo-product-sale-wrap .premium-woo-product-onsale, {{WRAPPER}} .premium-woocommerce.premium-woo-skin-grid-10 .premium-woo-product-sale-wrap' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .premium-woocommerce.premium-woo-skin-grid-10 .premium-woo-product-sale-wrap::after' => 'border-left-color:{{VALUE}}; border-right-color:{{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'sale_text_shadow',
				'selector' => '{{WRAPPER}} .premium-woo-product-sale-wrap .premium-woo-product-onsale',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'sale_shadow',
				'selector' => '{{WRAPPER}} .premium-woocommerce:not(.premium-woo-skin-grid-10) .premium-woo-product-sale-wrap .premium-woo-product-onsale, {{WRAPPER}} .premium-woocommerce.premium-woo-skin-grid-10 .premium-woo-product-sale-wrap',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'sale_typography',
				'global'         => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'fields_options' => array(
					'font_size'   => array(
						'selectors' => array(
							'{{WRAPPER}} .premium-woocommerce:not(.premium-woo-skin-grid-10) .premium-woo-product-sale-wrap .premium-woo-product-onsale' => 'font-size: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .premium-woocommerce.premium-woo-skin-grid-10 .premium-woo-product-sale-wrap' => 'width: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .premium-woocommerce.premium-woo-skin-grid-10 .premium-woo-product-sale-wrap .premium-woo-product-onsale' => 'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .premium-woocommerce.premium-woo-skin-grid-10 .premium-woo-product-sale-wrap::after' => 'border-left-width: calc( {{SIZE}}{{UNIT}} / 2); border-right-width: calc( {{SIZE}}{{UNIT}} / 2);',
						),
					),
					'line_height' => array(
						'default'   => array(
							'size' => '32',
							'unit' => 'px',
						),
						'selectors' => array(
							'{{WRAPPER}} .premium-woocommerce:not(.premium-woo-skin-grid-10) .premium-woo-product-sale-wrap .premium-woo-product-onsale' => 'line-height: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .premium-woocommerce.premium-woo-skin-grid-10 .premium-woo-product-sale-wrap' => 'width: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .premium-woocommerce.premium-woo-skin-grid-10 .premium-woo-product-sale-wrap::after' => 'border-left-width: calc( {{SIZE}}{{UNIT}} / 2); border-right-width: calc( {{SIZE}}{{UNIT}} / 2);',
						),
					),
				),
				'selector'       => '{{WRAPPER}} .premium-woo-product-sale-wrap .premium-woo-product-onsale',
			)
		);

		$this->add_control(
			'sales_notice',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'Use <b>Line Height</b> to control the ribbon size.', 'premium-addons-for-elementor' ),
				'content_classes' => 'papro-upgrade-notice',
				'condition'       => array(
					'_skin' => 'grid-10',
				),
			)
		);

		$this->add_responsive_control(
			'sale_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-woocommerce:not(.premium-woo-skin-grid-10) .premium-woo-product-sale-wrap .premium-woo-product-onsale' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
				'condition'  => array(
					'_skin!' => 'grid-10',
				),
			)
		);

		$this->add_responsive_control(
			'sale_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-woocommerce:not(.premium-woo-skin-grid-10) .premium-woo-product-sale-wrap .premium-woo-product-onsale' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'_skin!' => 'grid-10',
				),
			)
		);

		$this->add_responsive_control(
			'sale_padding_10',
			array(
				'label'              => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'allowed_dimensions' => 'vertical',
				'size_units'         => array( 'px', 'em', '%' ),
				'selectors'          => array(
					'{{WRAPPER}} .premium-woocommerce.premium-woo-skin-grid-10 .premium-woo-product-sale-wrap .premium-woo-product-onsale' => 'padding: {{TOP}}{{UNIT}} 0 {{BOTTOM}}{{UNIT}} 0;',
				),
				'condition'          => array(
					'_skin' => 'grid-10',
				),
			)
		);

		$this->add_responsive_control(
			'sale_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-woocommerce:not(.premium-woo-skin-grid-10) .premium-woo-product-sale-wrap .premium-woo-product-onsale, {{WRAPPER}} .premium-woocommerce.premium-woo-skin-grid-10 .premium-woo-product-sale-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Style Featured section.
	 *
	 * @access public
	 * @since 4.8.3
	 *
	 * @param array $pro_skins   pro skins.
	 */
	public function register_style_featured_controls() {

		$this->start_controls_section(
			'section_featured_style',
			array(
				'label'     => __( 'Featured Ribbon', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'featured' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'featured_size',
			array(
				'label'      => __( 'Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 20,
						'max' => 200,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-woocommerce:not(.premium-woo-skin-grid-10) .premium-woo-product-featured-wrap .premium-woo-product-featured' => 'min-height: {{SIZE}}{{UNIT}}; min-width: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'_skin!' => 'grid-10',
				),
			)
		);

		$this->add_control(
			'featured_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-woo-product-featured-wrap .premium-woo-product-featured' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'featured_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					// '{{WRAPPER}} .premium-woocommerce:not(.premium-woo-skin-grid-10) .premium-woo-product-featured-wrap .premium-woo-product-featured, {{WRAPPER}} .premium-woocommerce.premium-woo-skin-grid-10 .premium-woo-product-featured-wrap' => 'background-color: {{VALUE}};',
					'{{WRAPPER}}:not([data-widget_type="premium-woo-products.grid-10"]) .premium-woo-product-featured-wrap .premium-woo-product-featured, {{WRAPPER}}[data-widget_type="premium-woo-products.grid-10"] .premium-woo-product-featured-wrap' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .premium-woocommerce.premium-woo-skin-grid-10 .premium-woo-product-featured-wrap::after' => 'border-left-color:{{VALUE}}; border-right-color:{{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'featured_text_shadow',
				'selector' => '{{WRAPPER}} .premium-woo-product-featured-wrap .premium-woo-product-featured',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'featured_shadow',
				'selector' => '{{WRAPPER}}:not([data-widget_type="premium-woo-products.grid-10"]) .premium-woo-product-featured-wrap .premium-woo-product-featured, {{WRAPPER}}[data-widget_type="premium-woo-products.grid-10"] .premium-woo-product-featured-wrap',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'featured_typography',
				'global'         => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'fields_options' => array(
					'font_size'   => array(
						'selectors' => array(
							'{{WRAPPER}} .premium-woocommerce:not(.premium-woo-skin-grid-10) .premium-woo-product-featured-wrap' => 'font-size: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .premium-woocommerce.premium-woo-skin-grid-10 .premium-woo-product-featured-wrap' => 'width: {{SIZE}}{{UNIT}};  line-height: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .premium-woocommerce.premium-woo-skin-grid-10 .premium-woo-product-featured-wrap .premium-woo-product-featured' => 'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .premium-woocommerce.premium-woo-skin-grid-10 .premium-woo-product-featured-wrap::after' => 'border-left-width: calc( {{SIZE}}{{UNIT}} / 2); border-right-width: calc( {{SIZE}}{{UNIT}} / 2);',
						),
					),
					'line_height' => array(
						'default'   => array(
							'size' => '32',
							'unit' => 'px',
						),
						'selectors' => array(
							'{{WRAPPER}} .premium-woocommerce:not(.premium-woo-skin-grid-10) .premium-woo-product-featured-wrap' => 'line-height: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .premium-woocommerce.premium-woo-skin-grid-10 .premium-woo-product-featured-wrap' => 'width: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .premium-woocommerce.premium-woo-skin-grid-10 .premium-woo-product-featured-wrap::after' => 'border-left-width: calc( {{SIZE}}{{UNIT}} / 2); border-right-width: calc( {{SIZE}}{{UNIT}} / 2);',
						),
					),
				),
				'selector'       => '{{WRAPPER}} .premium-woo-product-featured-wrap .premium-woo-product-featured',
			)
		);

		$this->add_control(
			'featured_notice',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'Use <b>Line Height</b> to control the ribbon size.', 'premium-addons-for-elementor' ),
				'content_classes' => 'papro-upgrade-notice',
				'condition'       => array(
					'_skin' => 'grid-10',
				),
			)
		);

		$this->add_responsive_control(
			'featured_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-woo-product-featured-wrap .premium-woo-product-featured' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'_skin!' => 'grid-10',
				),
			)
		);

		$this->add_responsive_control(
			'featured_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-woo-product-featured-wrap .premium-woo-product-featured' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'_skin!' => 'grid-10',
				),
			)
		);

		$this->add_responsive_control(
			'featured_padding_10',
			array(
				'label'              => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'allowed_dimensions' => 'vertical',
				'size_units'         => array( 'px', 'em', '%' ),
				'selectors'          => array(
					'{{WRAPPER}} .premium-woo-product-featured-wrap .premium-woo-product-featured' => 'padding: {{TOP}}{{UNIT}} 0 {{BOTTOM}}{{UNIT}} 0;',
				),
				'condition'          => array(
					'_skin' => 'grid-10',
				),
			)
		);

		$this->add_responsive_control(
			'featured_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-woo-product-featured-wrap .premium-woo-product-featured' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Title Style Controls.
	 *
	 * @since 4.7.0
	 * @access public
	 */
	public function register_title_style_controls() {

		$this->start_controls_section(
			'section_title_style',
			array(
				'label' => __( 'Title', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-woocommerce .woocommerce-loop-product__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_hover_color',
			array(
				'label'     => __( 'Hover Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-woocommerce .woocommerce-loop-product__title:hover' => 'color: {{VALUE}};',
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
				'selector' => '{{WRAPPER}} .premium-woocommerce .woocommerce-loop-product__title',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'title_text_shadow',
				'selector' => '{{WRAPPER}} .premium-woocommerce .woocommerce-loop-product__title',
			)
		);

		$this->add_responsive_control(
			'title_spacing',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-woocommerce .woocommerce-loop-product__title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Register Category Style Controls.
	 *
	 * @since 4.7.0
	 * @access public
	 */
	public function register_cat_style_controls() {

		$this->start_controls_section(
			'section_category_style',
			array(
				'label' => __( 'Category', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'category_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-woocommerce .premium-woo-product-category' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'category_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'selector' => '{{WRAPPER}} .premium-woocommerce .premium-woo-product-category',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'category_text_shadow',
				'selector' => '{{WRAPPER}} .premium-woocommerce .premium-woo-product-category',
			)
		);

		$this->add_responsive_control(
			'category_spacing',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-woocommerce .premium-woo-product-category' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Register Quick View Style Controls.
	 *
	 * @since 4.7.0
	 * @access public
	 */
	public function register_quick_view_modal_style_controls() {

		$this->start_controls_section(
			'quick_view_modal_style',
			array(
				'label' => __( 'Quick View Modal', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'lightbox_overlay_color',
			array(
				'label'     => __( 'Overlay Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.premium-woo-quick-view-{{ID}} .premium-woo-quick-view-back' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'lightbox_bg_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.premium-woo-quick-view-{{ID}} #premium-woo-quick-view-modal .premium-woo-lightbox-content' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'lightbox_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'.premium-woo-quick-view-{{ID}} .premium-woo-lightbox-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'lightbox_border',
				'selector' => '.premium-woo-quick-view-{{ID}} .premium-woo-lightbox-content',
			)
		);

		$this->add_control(
			'lightbox_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'.premium-woo-quick-view-{{ID}} .premium-woo-lightbox-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'close_icon_color',
			array(
				'label'     => __( 'Close Icon Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array(
					'.premium-woo-quick-view-{{ID}} #premium-woo-quick-view-close' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'close_icon_size',
			array(
				'label'     => __( 'Close Icon Size', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 10,
						'max' => 50,
					),
				),
				'selectors' => array(
					'.premium-woo-quick-view-{{ID}} #premium-woo-quick-view-close' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Product Price Style Controls.
	 *
	 * @since 4.7.0
	 * @access public
	 */
	public function register_product_price_style() {

		$this->start_controls_section(
			'section_price_style',
			array(
				'label' => __( 'Price', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'price_text_shadow',
				'selector' => '{{WRAPPER}} .premium-woocommerce li.product .price',
			)
		);

		$this->add_responsive_control(
			'price_spacing',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-woocommerce li.product .price' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs(
			'price_style_tabs'
		);

		$this->start_controls_tab(
			'price_tab',
			array(
				'label' => __( 'Price', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'price_color',
			array(
				'label'     => __( 'Price Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-woocommerce li.product .price' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'price_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'selector' => '{{WRAPPER}} .premium-woocommerce li.product .price',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'slashed_price_tab',
			array(
				'label' => __( 'Slashed', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'slashed_price_color',
			array(
				'label'     => __( 'Slashed Price Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-woocommerce li.product .price del' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'slashed_price_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'selector' => '{{WRAPPER}} .premium-woocommerce li.product .price del',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

	}

	/**
	 * Register Product Rating Style Controls.
	 *
	 * @since 4.7.0
	 * @access public
	 */
	public function register_product_rating_style() {

		$this->start_controls_section(
			'section_rating_style',
			array(
				'label' => __( 'Rating', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'star_color',
			array(
				'label'     => __( 'Star Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-woocommerce li.product div.star-rating' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'empty_star_color',
			array(
				'label'     => __( 'Empty Star Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-woocommerce li.product div.star-rating::before' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'star_size',
			array(
				'label'     => __( 'Star Size', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'unit' => 'em',
				),
				'range'     => array(
					'em' => array(
						'min'  => 0,
						'max'  => 4,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-woocommerce li.product .star-rating' => 'font-size: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'rating_spacing',
			array(
				'label'      => __( 'Bottom Spacing', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'em' => array(
						'min'  => 0,
						'max'  => 5,
						'step' => 0.1,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}}  .premium-woocommerce li.product .star-rating' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();

	}

}
