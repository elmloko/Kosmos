<?php

/**
 * Interactive Geo Maps
 *
 * @wordpress-plugin
 * Plugin Name:       Interactive Geo Maps
 * Plugin URI:        https://interactivegeomaps.com/
 * Description:       Create interactive geographic vector maps of the world, continents or any country in the world. Color full regions or create markers on specific locations that will have information on hover and can also have actions on click. This plugin uses the online amcharts library to generate the maps.
 * Version:           1.5.11
 * Requires PHP:      7.0
 * Author:            Carlos Moreira
 * Author URI:        https://cmoreira.net/
 * Text Domain:       interactive-geo-maps
 * Domain Path:       /languages
 *
 */
namespace Saltus\WP\Plugin\Saltus\InteractiveMaps;

// If this file is called directly, quit.
if ( !defined( 'WPINC' ) ) {
    exit;
}
// Only run plugin code if PHP version bigger than 7.0 for now
if ( version_compare( PHP_VERSION, '7.0', '<' ) ) {
    return;
}
// Freemius logic

if ( function_exists( __NAMESPACE__ . '\\igmfreemiusinit' ) ) {
    igmfreemiusinit()->set_basename( false, __FILE__ );
} else {
    
    if ( !function_exists( __NAMESPACE__ . '\\igmfreemiusinit' ) ) {
        // Create a helper function for easy SDK access.
        function igmfreemiusinit()
        {
            global  $igmfreemiusinit ;
            
            if ( !isset( $igmfreemiusinit ) ) {
                // Include Freemius SDK.
                if ( file_exists( dirname( __FILE__ ) . '/vendor/freemius/wordpress-sdk/start.php' ) ) {
                    require_once dirname( __FILE__ ) . '/vendor/freemius/wordpress-sdk/start.php';
                }
                $igmfreemiusinit = fs_dynamic_init( array(
                    'id'             => '5114',
                    'slug'           => 'interactive-geo-maps',
                    'type'           => 'plugin',
                    'public_key'     => 'pk_81cc828e3f6fa811c70bab7631a4f',
                    'is_premium'     => false,
                    'premium_suffix' => 'PRO',
                    'has_addons'     => true,
                    'has_paid_plans' => true,
                    'trial'          => array(
                    'days'               => 7,
                    'is_require_payment' => true,
                ),
                    'menu'           => array(
                    'slug'    => 'edit.php?post_type=igmap',
                    'support' => false,
                ),
                    'is_live'        => true,
                ) );
            }
            
            return $igmfreemiusinit;
        }
        
        // Init Freemius.
        igmfreemiusinit();
        // Signal that SDK was initiated.
        do_action( 'igmfreemiusinit_loaded' );
        /**
         * Prevent trial notice from displaying
         *
         * @param bool  $show
         * @param array $msg
         * @return bool
         */
        function igm_remove_trial_notice( $show, $msg )
        {
            if ( 'trial_promotion' === $msg['id'] ) {
                // Don't show the trial promotional admin notice.
                return false;
            }
            return $show;
        }
        
        /**
         * Returns the plugin icon url
         *
         * @return string
         */
        function igm_plugin_icon()
        {
            return dirname( __FILE__ ) . '/assets/imgs/icon-256x256.png';
        }
        
        igmfreemiusinit()->add_filter(
            'show_admin_notice',
            'Saltus\\WP\\Plugin\\Saltus\\InteractiveMaps\\igm_remove_trial_notice',
            10,
            2
        );
        // set plugin icon for freemius
        igmfreemiusinit()->add_filter( 'plugin_icon', 'Saltus\\WP\\Plugin\\Saltus\\InteractiveMaps\\igm_plugin_icon' );
        igmfreemiusinit()->override_i18n( array(
            'start-trial' => __( 'Free 7 Day Pro Trial', 'interactive-geo-maps' ),
            'upgrade'     => __( 'Get Pro Features', 'interactive-geo-maps' ),
        ) );
    }
    
    if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
        require_once dirname( __FILE__ ) . '/vendor/autoload.php';
    }
    
    if ( class_exists( \Saltus\WP\Framework\Core::class ) ) {
        /*
         * The path to the plugin root directory is mandatory,
         * so it loads the models from a subdirectory.
         */
        $framework = new \Saltus\WP\Framework\Core( dirname( __FILE__ ) );
        $framework->register();
        /**
         * Initialize plugin
         */
        add_action( 'plugins_loaded', function () use( $framework ) {
            $plugin = new Core(
                'interactive-geo-maps',
                '1.5.11',
                __FILE__,
                $framework
            );
            $plugin->init();
        } );
    }

}
