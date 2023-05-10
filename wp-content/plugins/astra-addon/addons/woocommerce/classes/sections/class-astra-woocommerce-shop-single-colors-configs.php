<?php
/**
 * Shop Options for our theme.
 *
 * @package     Astra Addon
 * @link        https://www.brainstormforce.com
 * @since       Astra 1.4.3
 */

// Block direct access to the file.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Bail if Customizer config base class does not exist.
if ( ! class_exists( 'Astra_Customizer_Config_Base' ) ) {
	return;
}

if ( ! class_exists( 'Astra_Woocommerce_Shop_Single_Colors_Configs' ) ) {

	/**
	 * Register Woocommerce Shop Single Color Layout Configurations.
	 */
	// @codingStandardsIgnoreStart
	class Astra_Woocommerce_Shop_Single_Colors_Configs extends Astra_Customizer_Config_Base {
 // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedClassFound
		// @codingStandardsIgnoreEnd

		/**
		 * Register Woocommerce Shop Single Color Layout Configurations.
		 *
		 * @param Array                $configurations Astra Customizer Configurations.
		 * @param WP_Customize_Manager $wp_customize instance of WP_Customize_Manager.
		 * @since 1.4.3
		 * @return Array Astra Customizer Configurations with updated configurations.
		 */
		public function register_configuration( $configurations, $wp_customize ) {

			$_configs = array(

				/**
				 * Option: Divider.
				 */
				array(
					'name'     => ASTRA_THEME_SETTINGS . '[woo-single-product-general-color-divider]',
					'section'  => 'section-woo-shop-single',
					'title'    => __( 'General Colors', 'astra-addon' ),
					'type'     => 'control',
					'control'  => 'ast-heading',
					'priority' => 5,
					'settings' => array(),
					'context'  => array(
						astra_addon_builder_helper()->design_tab_config,
					),
				),

				/**
				 * Single Product Title Color
				 */
				array(
					'name'              => ASTRA_THEME_SETTINGS . '[single-product-title-color]',
					'default'           => astra_get_option( 'single-product-title-color' ),
					'type'              => 'control',
					'section'           => 'section-woo-shop-single',
					'control'           => 'ast-color',
					'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_alpha_color' ),
					'transport'         => 'postMessage',
					'title'             => __( 'Title Color', 'astra-addon' ),
					'context'           => array(
						astra_addon_builder_helper()->design_tab_config,
						array(
							'setting'  => ASTRA_THEME_SETTINGS . '[single-product-structure]',
							'operator' => 'contains',
							'value'    => 'title',
						),
					),
					'priority'          => 80,
					'divider'           => array( 'ast_class' => 'ast-section-spacing' ),
				),

				/**
				 * Single Product Title Color
				 */
				array(
					'name'              => ASTRA_THEME_SETTINGS . '[single-product-category-color]',
					'default'           => astra_get_option( 'single-product-category-color' ),
					'type'              => 'control',
					'section'           => 'section-woo-shop-single',
					'control'           => 'ast-color',
					'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_alpha_color' ),
					'transport'         => 'postMessage',
					'title'             => __( 'Category Color', 'astra-addon' ),
					'context'           => array(
						astra_addon_builder_helper()->design_tab_config,
						array(
							'setting'  => ASTRA_THEME_SETTINGS . '[single-product-structure]',
							'operator' => 'contains',
							'value'    => 'category',
						),
					),
					'priority'          => 80,
				),

				/**
				 * Single Product Price Color
				 */
				array(
					'name'              => ASTRA_THEME_SETTINGS . '[single-product-price-color]',
					'default'           => astra_get_option( 'single-product-price-color' ),
					'control'           => 'ast-color',
					'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_alpha_color' ),
					'transport'         => 'postMessage',
					'type'              => 'control',
					'section'           => 'section-woo-shop-single',
					'title'             => __( 'Price Color', 'astra-addon' ),
					'context'           => array(
						astra_addon_builder_helper()->design_tab_config,
						array(
							'setting'  => ASTRA_THEME_SETTINGS . '[single-product-structure]',
							'operator' => 'contains',
							'value'    => 'price',
						),
					),
					'priority'          => 80,
				),

				/**
				 * Single Product Content Color
				 */
				array(
					'name'              => ASTRA_THEME_SETTINGS . '[single-product-content-color]',
					'default'           => astra_get_option( 'single-product-content-color' ),
					'type'              => 'control',
					'section'           => 'section-woo-shop-single',
					'control'           => 'ast-color',
					'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_alpha_color' ),
					'transport'         => 'postMessage',
					'title'             => __( 'Content Color', 'astra-addon' ),
					'context'           => array(
						astra_addon_builder_helper()->design_tab_config,
						array(
							'setting'  => ASTRA_THEME_SETTINGS . '[shop-product-structure]',
							'operator' => 'contains',
							'value'    => 'title',
						),
					),
					'priority'          => 80,
				),

				/**
				 * Single Product Breadcrumb Color
				 */
				array(
					'name'              => ASTRA_THEME_SETTINGS . '[single-product-breadcrumb-color]',
					'default'           => astra_get_option( 'single-product-breadcrumb-color' ),
					'type'              => 'control',
					'section'           => 'section-woo-shop-single',
					'control'           => 'ast-color',
					'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_alpha_color' ),
					'transport'         => 'postMessage',
					'title'             => __( 'Breadcrumb Color', 'astra-addon' ),
					'context'           => array(
						astra_addon_builder_helper()->design_tab_config,
						array(
							'setting'  => ASTRA_THEME_SETTINGS . '[single-product-breadcrumb-disable]',
							'operator' => '==',
							'value'    => true,
						),
					),
					'priority'          => 80,
				),
			);

			$configurations = array_merge( $configurations, $_configs );

			return $configurations;

		}
	}
}


new Astra_Woocommerce_Shop_Single_Colors_Configs();





