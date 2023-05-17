<?php
/**
 * Plugin bootstrap file
 *
 * @sassy-social-share
 * Plugin Name:       Sassy Social Share
 * Plugin URI:        https://www.heateor.com
 * Description:       Slickest, Simplest and Optimized Share buttons. Facebook, Twitter, Reddit, Pinterest, WhatsApp and over 100 more
 * Version:           3.3.51
 * Author:            Team Heateor
 * Author URI:        https://www.heateor.com
 * Text Domain:       sassy-social-share
 * Domain Path:       /languages
 */

defined( 'ABSPATH' ) or die( "Cheating........Uh!!" );

// If this file is called directly, halt
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'HEATEOR_SSS_VERSION', '3.3.51' );
define( 'HEATEOR_SSS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

// core plugin class object
$heateor_sss = null;

// attributes to allow in the HTML of the social share and social media follow icons
$heateor_sss_default_attribs = array(
    'id' => array(),
    'class' => array(),
    'title' => array(),
    'style' => array(),
    'data' => array(),
    'focusable' => array(),
    'width' => array(),
    'height' => array(),
    'opacity' => array(),
    'data-heateor-sss-href' => array(),
    'data-heateor-sss-no-counts' => array(),
    'data-heateor-ss-offset' => array(),
    'data-heateor-sss-st-count' => array()
);

// tags to allow in the HTML of the social share and social media follow icons
$heateor_sss_allowed_tags = array(
    'div'           => array_merge( $heateor_sss_default_attribs, array(
        'data-href' => array(),
        'data-layout' => array(),
        'data-action' => array(),
        'data-show-faces' => array(),
        'data-share' => array(),
    ) ),
    'span'          => array_merge( $heateor_sss_default_attribs, array(
        'onClick' => array(),
        'onclick' => array(),
    ) ),
    'p'             => $heateor_sss_default_attribs,
    'a'             => array_merge( $heateor_sss_default_attribs, array(
        'href' => array( 'Javascript:void(0)', 'javascript:void(0)' ),
        'onClick' => array(),
        'onclick' => array(),
        'target' => array( '_blank', '_top' ),
        'rel' => array(),
        'data-url' => array(),
        'data-counturl' => array(),
        'data-text' => array(),
        'data-via' => array(),
        'data-lang' => array(),
    ) ),
    'svg'           => array_merge( $heateor_sss_default_attribs, array(
        'viewBox' => array(),
        'viewbox' => array(),
        'aria-hidden' => array(),
        'xmlns' => array(),
        'xml:space' => array(),
        'version' => array(),
        'xmlns:xlink' => array(),
    ) ),
    'script'           => array_merge( $heateor_sss_default_attribs, array(
        'src' => array(),
        'type' => array(),
        'data-url' => array(),
        'data-counter' => array(),
        'async' => array(),
    ) ),
    'u'             => $heateor_sss_default_attribs,
    'i'             => $heateor_sss_default_attribs,
    'q'             => $heateor_sss_default_attribs,
    'b'             => $heateor_sss_default_attribs,
    'ul'            => $heateor_sss_default_attribs,
    'ol'            => $heateor_sss_default_attribs,
    'li'            => $heateor_sss_default_attribs,
    'br'            => $heateor_sss_default_attribs,
    'hr'            => $heateor_sss_default_attribs,
    'strong'        => $heateor_sss_default_attribs,
    'blockquote'    => $heateor_sss_default_attribs,
    'del'           => $heateor_sss_default_attribs,
    'strike'        => $heateor_sss_default_attribs,
    'em'            => $heateor_sss_default_attribs,
    'code'          => $heateor_sss_default_attribs,
    'path'          => array_merge( $heateor_sss_default_attribs, array(
        'stroke-width' => array(),
        'stroke'  => array(),
        'fill' => array(),
        'd' => array()
    ) ),
    'circle'        => array_merge( $heateor_sss_default_attribs, array(
        'stroke-width' => array(),
        'stroke'  => array(),
        'fill' => array(),
        'cx' => array(),
        'cy' => array(),
        'r' => array()
    ) ),
    'polygon'        => array_merge( $heateor_sss_default_attribs, array(
        'stroke-width' => array(),
        'stroke'  => array(),
        'fill' => array(),
        'points' => array()
    ) ),
    'g'          => array_merge( $heateor_sss_default_attribs, array(
        'stroke-width' => array(),
        'stroke'  => array(),
        'stroke-linecap' => array(),
        'stroke-miterlimit' => array(),
        'fill' => array(),
        'fill' => array(),
        'fill' => array(),
        'fill' => array(),
    ) ),
    'style'          => array_merge( $heateor_sss_default_attribs, array(
        'type' => array(),
    ) )
);

/**
 * Save default plugin options
 */
function heateor_sss_save_default_options() {

	// default options
	add_option( 'heateor_sss', array(
	   'horizontal_sharing_shape' => 'round',
	   'horizontal_sharing_size' => '35',
	   'horizontal_sharing_width' => '70',
	   'horizontal_sharing_height' => '35',
	   'horizontal_border_radius' => '',
	   'horizontal_font_color_default' => '',
	   'horizontal_sharing_replace_color' => '#fff',
	   'horizontal_font_color_hover' => '',
	   'horizontal_sharing_replace_color_hover' => '#fff',
	   'horizontal_bg_color_default' => '',
	   'horizontal_bg_color_hover' => '',
	   'horizontal_border_width_default' => '',
	   'horizontal_border_color_default' => '',
	   'horizontal_border_width_hover' => '',
	   'horizontal_border_color_hover' => '',
	   'vertical_sharing_shape' => 'square',
	   'vertical_sharing_size' => '40',
	   'vertical_sharing_width' => '80',
	   'vertical_sharing_height' => '40',
	   'vertical_border_radius' => '',
	   'vertical_font_color_default' => '',
	   'vertical_sharing_replace_color' => '#fff',
	   'vertical_font_color_hover' => '',
	   'vertical_sharing_replace_color_hover' => '#fff',
	   'vertical_bg_color_default' => '',
	   'vertical_bg_color_hover' => '',
	   'vertical_border_width_default' => '',
	   'vertical_border_color_default' => '',
	   'vertical_border_width_hover' => '',
	   'vertical_border_color_hover' => '',
	   'hor_enable' => '1',
	   'horizontal_target_url' => 'default',
	   'horizontal_target_url_custom' => '',
	   'title' => 'Spread the love',
	   'comment_container_id' => 'respond',
	   'instagram_username' => '',
	   'horizontal_re_providers' => array( 'facebook', 'twitter', 'reddit', 'linkedin', 'pinterest', 'MeWe', 'mix', 'whatsapp' ),
	   'hor_sharing_alignment' => 'left',
	   'top' => '1',
	   'post' => '1',
	   'page' => '1',
	   'horizontal_more' => '1',
	   'vertical_enable' => '1',
	   'vertical_target_url' => 'default',
	   'vertical_target_url_custom' => '',
	   'vertical_comment_container_id' => 'respond',
	   'vertical_instagram_username' => '',
	   'vertical_re_providers' => array( 'facebook', 'twitter', 'reddit', 'linkedin', 'pinterest', 'MeWe', 'mix', 'whatsapp' ),
	   'vertical_bg' => '',
	   'alignment' => 'left',
	   'left_offset' => '-10',
	   'right_offset' => '-10',
	   'top_offset' => '100',
	   'vertical_post' => '1',
	   'vertical_page' => '1',
	   'vertical_home' => '1',
	   'vertical_more' => '1',
	   'hide_mobile_sharing' => '1',
	   'vertical_screen_width' => '783',
	   'bottom_mobile_sharing' => '1',
	   'horizontal_screen_width' => '783',
	   'bottom_sharing_position' => '0',
	   'bottom_sharing_alignment' => 'left',
	   'bottom_sharing_position_radio' => 'responsive',
	   'footer_script' => '1',
	   'delete_options' => '1',
	   'share_count_cache_refresh_count' => '10',
	   'share_count_cache_refresh_unit' => 'minutes',
	   'bitly_access_token' => '',
	   'language' => get_locale(),
	   'twitter_username' => '',
	   'custom_css' => '',
	   'amp_enable' => '1',
	   'instagram_username' => '',
	   'vertical_instagram_username' => '',
	   'youtube_username' => '',
	   'vertical_youtube_username' => '',
       'rutube_username' => '',
       'vertical_rutube_username' => '',
       'google_news_url' => '',
       'vertical_google_news_url' => ''
	) );

	// plugin version
	add_option( 'heateor_sss_version', HEATEOR_SSS_VERSION );

}

/**
 * Plugin activation function
 */
function heateor_sss_activate_plugin( $network_wide ) {

	if ( ! current_user_can( 'activate_plugins' ) ) {
        return;
    }

	global $wpdb;

	if ( function_exists( 'is_multisite' ) && is_multisite() ) {
		if ( $network_wide ) {
			$old_blog =  $wpdb->blogid;
			//Get all blog ids
			$blog_ids =  $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

			foreach ( $blog_ids as $blog_id ) {
				switch_to_blog( $blog_id );
				heateor_sss_save_default_options();
			}
			switch_to_blog( $old_blog );
			return;
		}
	}
	heateor_sss_save_default_options();
	set_transient( 'heateor-sss-admin-notice-on-activation', true, 5 );

}
register_activation_hook( __FILE__, 'heateor_sss_activate_plugin' );

/**
 * Save default options for the new subsite created
 */
function heateor_sss_new_subsite_default_options( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {

    if ( is_plugin_active_for_network( 'sassy-social-share/sassy-social-share.php' ) ) { 
        switch_to_blog( $blog_id );
        heateor_sss_save_default_options();
        restore_current_blog();
    }

}
add_action( 'wpmu_new_blog', 'heateor_sss_new_subsite_default_options', 10, 6 );

/**
 * The core plugin class
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-sassy-social-share.php';

/**
 * Begins execution of the plugin.
 *
 * @since    1.0
 */
function heateor_sss_run() {

	global $heateor_sss;
	$heateor_sss = new Sassy_Social_Share( HEATEOR_SSS_VERSION );

}
heateor_sss_run();
