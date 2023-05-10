<?php
/**
 * Advanced Hooks - Dynamic CSS
 *
 * @package Astra Addon
 */

add_filter( 'astra_addon_dynamic_css', 'astra_ext_advanced_hooks_dynamic_css' );

/**
 * Dynamic CSS
 *
 * @param  string $dynamic_css          Astra Dynamic CSS.
 * @param  string $dynamic_css_filtered Astra Dynamic CSS Filters.
 * @return string
 */
function astra_ext_advanced_hooks_dynamic_css( $dynamic_css, $dynamic_css_filtered = '' ) {

	$css = '';

	$common_desktop_css_output = array(
		'.ast-hide-display-device-desktop' => array(
			'display' => 'none',
		),
	);
	$common_tablet_css_output  = array(
		'.ast-hide-display-device-tablet' => array(
			'display' => 'none',
		),
	);
	$common_mobile_css_output  = array(
		'.ast-hide-display-device-mobile' => array(
			'display' => 'none',
		),
	);

	if ( Astra_Addon_Builder_Helper::apply_flex_based_css() ) {

		$option = array(
			'location'  => 'ast-advanced-hook-location',
			'exclusion' => 'ast-advanced-hook-exclusion',
			'users'     => 'ast-advanced-hook-users',
		);

		$result = Astra_Target_Rules_Fields::get_instance()->get_posts_by_conditions( ASTRA_ADVANCED_HOOKS_POST_TYPE, $option );

		if ( $result ) {

			foreach ( $result as $post_id => $post_data ) {

				$post_type = get_post_type();

				if ( ASTRA_ADVANCED_HOOKS_POST_TYPE !== $post_type ) {

					$action = get_post_meta( $post_id, 'ast-advanced-hook-action', true );

					if ( $action && ( 'astra_content_top' === $action || 'astra_content_bottom' === $action ) ) {

						$common_desktop_css_output['.site-content .ast-container'] = array(
							'flex-wrap' => 'wrap',
						);

						$common_desktop_css_output['[class^="astra-advanced-hook-"], [class*="astra-advanced-hook-"]'] = array(
							'width' => '100%',
						);

						break;
					}
				}
			}
		}
	}

	// Common options of Above Header.
	$css .= astra_parse_css( $common_desktop_css_output, astra_addon_get_tablet_breakpoint( '', 1 ) );
	$css .= astra_parse_css( $common_tablet_css_output, astra_addon_get_mobile_breakpoint( '', 1 ), astra_addon_get_tablet_breakpoint() );
	$css .= astra_parse_css( $common_mobile_css_output, '', astra_addon_get_mobile_breakpoint() );

	return $dynamic_css . $css;
}
