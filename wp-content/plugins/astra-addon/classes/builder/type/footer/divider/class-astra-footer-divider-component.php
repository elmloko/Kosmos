<?php
/**
 * Divider component.
 *
 * @package     Astra Builder
 * @link        https://www.brainstormforce.com
 * @since       Astra 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'ASTRA_ADDON_FOOTER_DIVIDER_DIR', ASTRA_EXT_DIR . 'classes/builder/type/footer/divider/' );
define( 'ASTRA_ADDON_FOOTER_DIVIDER_URI', ASTRA_EXT_URI . 'classes/builder/type/footer/divider/' );

/**
 * Divider Initial Setup
 *
 * @since 3.0.0
 */
// @codingStandardsIgnoreStart
class Astra_Footer_Divider_Component {
 // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedClassFound
	// @codingStandardsIgnoreEnd

	/**
	 * Constructor function that initializes required actions and hooks
	 */
	public function __construct() {

		// @codingStandardsIgnoreStart WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
		require_once ASTRA_ADDON_FOOTER_DIVIDER_DIR . 'classes/class-astra-footer-divider-component-loader.php';

		// Include front end files.
		if ( ! is_admin() ) {
			require_once ASTRA_ADDON_FOOTER_DIVIDER_DIR . 'dynamic-css/dynamic.css.php';
		}
		// @codingStandardsIgnoreEnd WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
	}
}

/**
 *  Kicking this off by creating an object.
 */
new Astra_Footer_Divider_Component();
