<?php
/*
Plugin Name: Premium Addons for Elementor
Description: Premium Addons for Elementor plugin includes widgets and addons like Blog Post Grid, Megamenu, Post Carousel, Advanced Slider, Modal Popup, Google Maps, Pricing Tables, Lottie Animations, Countdown, Testimonials.
Plugin URI: https://premiumaddons.com
Version: 4.9.55
Elementor tested up to: 3.12.2
Elementor Pro tested up to: 3.12.3
Author: Leap13
Author URI: https://leap13.com/
Text Domain: premium-addons-for-elementor
Domain Path: /languages
License: GNU General Public License v3.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No access of directly access.
}

// Define Constants.
define( 'PREMIUM_ADDONS_VERSION', '4.9.55' );
define( 'PREMIUM_ADDONS_URL', plugins_url( '/', __FILE__ ) );
define( 'PREMIUM_ADDONS_PATH', plugin_dir_path( __FILE__ ) );
define( 'PREMIUM_ASSETS_PATH', set_url_scheme( wp_upload_dir()['basedir'] . '/premium-addons-elementor' ) );
define( 'PREMIUM_ASSETS_URL', set_url_scheme( wp_upload_dir()['baseurl'] . '/premium-addons-elementor' ) );
define( 'PREMIUM_ADDONS_FILE', __FILE__ );
define( 'PREMIUM_ADDONS_BASENAME', plugin_basename( PREMIUM_ADDONS_FILE ) );
define( 'PREMIUM_ADDONS_STABLE_VERSION', '4.9.54' );

/*
 * Load plugin core file
 */
require_once PREMIUM_ADDONS_PATH . 'includes/class-pa-core.php';
