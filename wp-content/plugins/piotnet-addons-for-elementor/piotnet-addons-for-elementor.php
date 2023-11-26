<?php
/**
 * Plugin Name: Piotnet Addons For Elementor
 * Description: Piotnet Addons For Elementor (PAFE) adds many new features for Elementor
 * Plugin URI:  https://pafe.piotnet.com/
 * Version:     2.4.25
 * Author:      Piotnet
 * Author URI:  https://piotnet.com/
 * Text Domain: pafe
 * Elementor tested up to: 3.17.3
 * Elementor Pro tested up to: 3.17.1
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'PAFE_VERSION', '2.4.25' );

define( 'PAFE_DIR', plugin_dir_path(__FILE__));
define( 'PAFE_URL', plugins_url( '/', __FILE__ ) );

final class Piotnet_Addons_For_Elementor {
	const MINIMUM_ELEMENTOR_VERSION = '3.0.0';
	const MINIMUM_PHP_VERSION = '5.4';
	const TAB_PAFE = 'tab_pafe';

	private static $_instance = null;

	public static function instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;

	}

	public function __construct() { 

		add_action( 'init', [ $this, 'i18n' ] );

		require_once( __DIR__ . '/inc/features.php' );
		$features = json_decode( PAFE_FEATURES_FREE, true );

		$extension = false;
		$widget = false;

		foreach ($features as $feature) {
			if( get_option( $feature['option'], 2 ) == 2 || get_option( $feature['option'], 2 ) == 1 ) {
				if (!empty($feature['extension'])) {
					$extension = true;
				}

				if (!empty($feature['widget'])) {
					$widget = true;
				}
			}
		}

		if ($extension) {
			add_action( 'wp_enqueue_scripts', [ $this, 'enqueue' ] );
		}

		if ($widget) {
			add_action( 'elementor/frontend/after_register_scripts', [ $this, 'enqueue_scripts_widget' ] );
			add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'enqueue_styles_widget' ] );
		}

		add_action( 'plugins_loaded', [ $this, 'init' ] );
		register_activation_hook( __FILE__, [ $this, 'plugin_activate'] );
		add_action( 'admin_init', [ $this, 'plugin_redirect'] );
		add_action( 'elementor/elements/categories_registered', [ $this, 'add_elementor_widget_categories' ] );

		add_filter( 'elementor/init', [ $this, 'add_pafe_tab'], 10,1);
		add_filter( 'elementor/controls/get_available_tabs_controls', [ $this, 'add_pafe_tab'], 10,1);
		
		require_once( __DIR__ . '/inc/features.php' );

		if ( !defined('PAFE_PRO_VERSION') ) {
			add_shortcode('pafe-template', [ $this, 'pafe_template_elementor' ] );

			if ( !defined('ELEMENTOR_PRO_VERSION') ) {
			    add_filter( 'manage_elementor_library_posts_columns', [ $this, 'set_custom_edit_columns' ] );
		    	add_action( 'manage_elementor_library_posts_custom_column', [ $this, 'custom_column' ], 10, 2 );
			}
		}

		if( get_option( 'pafe-features-posts-list', 2 ) == 2 || get_option( 'pafe-features-posts-list', 2 ) == 1 ) {
			require_once( __DIR__ . '/inc/ajax-posts-list.php' );
		}
        if ( defined('ELEMENTOR_VERSION') ) {
            add_action( 'init', [ $this, 'add_wpml_support' ] );
        }
			
		add_filter( 'deprecated_function_trigger_error', [ $this, 'remove_deprecated_function_trigger_error' ], 10, 1 ); 

	}

	public function remove_deprecated_function_trigger_error() {
		return false;
	}

	public function i18n() {

		load_plugin_textdomain( 'pafe' );

	}

	public function enqueue() {
		wp_enqueue_script( 'pafe-extension-free', plugin_dir_url( __FILE__ ) . 'assets/js/minify/extension.min.js', array('jquery'), PAFE_VERSION );
		wp_enqueue_style( 'pafe-extension-style-free', plugin_dir_url( __FILE__ ) . 'assets/css/minify/extension.min.css', [], PAFE_VERSION );		
	}

	public function enqueue_scripts_widget() {
		wp_register_script( 'pafe-widget-free', plugin_dir_url( __FILE__ ) . 'assets/js/minify/widget.min.js', array('jquery'), PAFE_VERSION );
	}

	public function enqueue_styles_widget() {
		wp_register_style( 'pafe-widget-style-free', plugin_dir_url( __FILE__ ) . 'assets/css/minify/widget.min.css', [], PAFE_VERSION );
	}

	public function enqueue_footer() {
		echo '<div data-pafe-ajax-url="'. admin_url( 'admin-ajax.php' ) .'"></div>';
	}

	public function init() {

		// Check if Elementor installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );
			return;
		}

		// Check for required Elementor version
		if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_elementor_version' ] );
			return;
		}

		// Check for required PHP version
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );
			return;
		}

		// Add Plugin actions
		if ( version_compare( ELEMENTOR_VERSION, '3.7.0', '<' ) ) {
			add_action( 'elementor/widgets/widgets_registered', [ $this, 'init_widgets' ] );
		} else {
			add_action( 'elementor/widgets/register', [ $this, 'init_widgets_new' ] );
		}
		add_action( 'elementor/controls/controls_registered', [ $this, 'init_controls' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue' ] );
		add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), [ $this, 'plugin_action_links' ], 10, 1 );
		add_filter( 'plugin_row_meta', [ $this, 'plugin_row_meta' ], 10, 2 );
		add_action( 'admin_menu', [ $this, 'admin_menu' ], 600 );
		add_action( 'wp_footer', [ $this, 'enqueue_footer' ], 600 );
	}

	public function add_pafe_tab($tabs){
		if(version_compare(ELEMENTOR_VERSION,'1.5.5')){
			Elementor\Controls_Manager::add_tab(self::TAB_PAFE, __( 'PAFE', 'pafe' ));
		}else{
			$tabs[self::TAB_PAFE] = __( 'PAFE', 'pafe' );
		}    
        return $tabs;
    }

	public function pafe_template_elementor($atts){
	    if(!class_exists('Elementor\Plugin')){
	        return '';
	    }
	    if(!isset($atts['id']) || empty($atts['id'])){
	        return '';
	    }

	    $post_id = $atts['id'];
	    $response = \Elementor\Plugin::instance()->frontend->get_builder_content_for_display($post_id);
	    return $response;
	}

	public function set_custom_edit_columns($columns) {
        $columns['pafe-shortcode'] = __( 'Shortcode', 'pafe' );
        return $columns;
    }

    public function custom_column( $column, $post_id ) {
        switch ( $column ) {
            case 'pafe-shortcode' :
                echo '<input class="elementor-shortcode-input" type="text" readonly="" onfocus="this.select()" value="[pafe-template id=' . '&quot;' . $post_id . '&quot;' . ']">'; 
                break;
        }
    }

	public function plugin_activate() {

	    add_option( 'piotnet_addons_for_elementor_do_activation_redirect', true );

	}

	public function plugin_redirect() {

	    if ( get_option( 'piotnet_addons_for_elementor_do_activation_redirect', false ) ) {
	        delete_option( 'piotnet_addons_for_elementor_do_activation_redirect' );
	        wp_redirect( 'admin.php?page=piotnet-addons-for-elementor' );
	    }

	}

	public function admin_notice_missing_main_plugin() {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'pafe' ),
			'<strong>' . esc_html__( 'Piotnet Addons For Elementor', 'pafe' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'pafe' ) . '</strong>'
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

	public function admin_notice_minimum_elementor_version() {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'pafe' ),
			'<strong>' . esc_html__( 'Piotnet Addons For Elementor', 'pafe' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'pafe' ) . '</strong>',
			 self::MINIMUM_ELEMENTOR_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

	public function admin_notice_minimum_php_version() {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			/* translators: 1: Plugin name 2: PHP 3: Required PHP version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'pafe' ),
			'<strong>' . esc_html__( 'Piotnet Addons For Elementor', 'pafe' ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', 'pafe' ) . '</strong>',
			 self::MINIMUM_PHP_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

	public function plugin_action_links( $links ) { 

		$links[] = '<a href="'. esc_url( get_admin_url(null, 'admin.php?page=piotnet-addons-for-elementor') ) .'">' . esc_html__( 'Settings', 'pafe' ) . '</a>';
		$links[] = '<a href="https://pafe.piotnet.com/?wpam_id=1" target="_blank" class="elementor-plugins-gopro">' . esc_html__( 'Go Pro', 'pafe' ) . '</a>';
		return $links;

	}

	public function plugin_row_meta( $links, $file ) {

		if ( strpos( $file, 'piotnet-addons-for-elementor.php' ) !== false ) {
			$links[] = '<a href="https://pafe.piotnet.com/tutorials" target="_blank">' . esc_html__( 'Video Tutorials', 'pafe' ) . '</a>';
		}
   		return $links;

	}

	public function admin_menu() {

		if ( !defined('PAFE_PRO_VERSION') ) {
			add_menu_page(
				'Piotnet Addons',
				'Piotnet Addons',
				'manage_options',
				'piotnet-addons-for-elementor',
				[ $this, 'admin_page' ],
				'dashicons-pafe-icon'
			);

			add_action( 'admin_init',  [ $this, 'pafe_settings' ] );
		}

	}

	public function pafe_settings() {

		require_once( __DIR__ . '/inc/features.php' );
		$features_free = json_decode( PAFE_FEATURES_FREE, true );
		$features_pro = json_decode( PAFE_FEATURES_PRO, true );
		$features = $features_free + $features_pro;

		foreach ($features as $feature) {
			if ( defined('PAFE_VERSION') && !$feature['pro'] || defined('PAFE_PRO_VERSION') && $feature['pro'] ) {
				register_setting( 'piotnet-addons-for-elementor-features-settings-group', $feature['option'] );
			}
		}

		register_setting( 'piotnet-addons-for-elementor-pro-settings-group', 'piotnet-addons-for-elementor-pro-username' );
		register_setting( 'piotnet-addons-for-elementor-pro-settings-group', 'piotnet-addons-for-elementor-pro-password' );
		
	}

	public function admin_page(){
		
		require_once( __DIR__ . '/inc/admin-page.php' );

	}

	public function admin_enqueue() {
		if ( !defined('PAFE_PRO_VERSION') ) {
			wp_enqueue_style( 'pafe-admin-css', plugin_dir_url( __FILE__ ) . 'assets/css/minify/pafe-admin.min.css', false, '1.3.0' );
			wp_enqueue_script( 'pafe-admin-js', plugin_dir_url( __FILE__ ) . 'assets/js/minify/pafe-admin.min.js', false, '1.2.0' );
		}
	}

	public function add_elementor_widget_categories( $elements_manager ) {

		$elements_manager->add_category(
			'pafe-free-widgets',
			[
				'title' => __( 'PAFE Free Widgets', 'pafe' ),
				'icon' => 'fa fa-plug',
			]
		);

	}

	public function init_widgets() {

		if( get_option( 'pafe-features-before-after-image-comparison-slider', 2 ) == 2 || get_option( 'pafe-features-before-after-image-comparison-slider', 2 ) == 1 ) {
			require_once( __DIR__ . '/widgets/pafe-before-after-image-comparison-slider.php' );
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \PAFE_Before_After_Image_Comparison_Slider() );
		}

		if( get_option( 'pafe-features-switch-content', 2 ) == 2 || get_option( 'pafe-features-switch-content', 2 ) == 1 ) {
			require_once( __DIR__ . '/widgets/pafe-switch-content.php' );
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \PAFE_Switch_Content() );
		}

		if( get_option( 'pafe-features-video-playlist', 2 ) == 2 || get_option( 'pafe-features-video-playlist', 2 ) == 1 ) {
			require_once( __DIR__ . '/widgets/pafe-video-playlist.php' );
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \PAFE_Video_Playlist() );
		}

		if( get_option( 'pafe-features-vertical-timeline', 2 ) == 2 || get_option( 'pafe-features-vertical-timeline', 2 ) == 1 ) {
			require_once( __DIR__ . '/widgets/pafe-vertical-timeline.php' );
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \PAFE_Vertical_Timeline() );
		}

		if( get_option( 'pafe-features-image-accordion', 2 ) == 2 || get_option( 'pafe-features-image-accordion', 2 ) == 1 ) {
			require_once( __DIR__ . '/widgets/pafe-image-accordion.php' );
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \PAFE_Image_Accordion() );
		}

		if( get_option( 'pafe-features-posts-list', 2 ) == 2 || get_option( 'pafe-features-posts-list', 2 ) == 1 ) {
			require_once( __DIR__ . '/widgets/pafe-posts-list.php' );
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \PAFE_Posts_List() );
		}

		if( get_option( 'pafe-features-sales-pop', 2 ) == 2 || get_option( 'pafe-features-sales-pop', 2 ) == 1 ) {
			require_once( __DIR__ . '/widgets/pafe-sales-pop.php' );
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \PAFE_Sales_Pop() ); 
		}
		if( get_option( 'pafe-features-countdown-cart', 2 ) == 2 || get_option( 'pafe-features-countdown-cart', 2 ) == 1 ) {
			require_once( __DIR__ . '/widgets/pafe-countdown-cart.php' );
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \PAFE_Countdown_Cart() ); 
		}
		if( get_option( 'pafe-features-dual-color-headline', 2 ) == 2 || get_option( 'pafe-features-dual-color-headline', 2 ) == 1 ) {
			require_once( __DIR__ . '/widgets/pafe-dual-color-headline.php' );
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \PAFE_DUal_Color_Headline() ); 
		}
		if( get_option( 'pafe-features-hotspot', 2 ) == 2 || get_option( 'pafe-features-hotspot', 2 ) == 1 ) {
			require_once( __DIR__ . '/widgets/pafe-hotspot.php' );
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \PAFE_Hotspot() ); 
		}
		if( get_option( 'pafe-features-progressbar', 2 ) == 2 || get_option( 'pafe-features-progressbar', 2 ) == 1 ) {
			require_once( __DIR__ . '/widgets/pafe-progressbar.php' );
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \PAFE_Progress_Bar() ); 
		}
		if( get_option( 'pafe-features-table', 2 ) == 2 || get_option( 'pafe-features-table', 2 ) == 1 ) {
			require_once( __DIR__ . '/widgets/pafe-table.php' );
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \PAFE_Table() ); 
		}

	}

	public function init_controls() { 

		// Include Control files

		if( get_option( 'pafe-features-image-carousel-multiple-custom-urls', 2 ) == 2 || get_option( 'pafe-features-image-carousel-multiple-custom-urls', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-image-carousel-multiple-custom-urls.php' );
			new PAFE_Image_Carousel_Multiple_Custom_Urls();
		}

		if( get_option( 'pafe-features-gradient-text', 2 ) == 2 || get_option( 'pafe-features-gradient-text', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-gradient-text.php' );
			new PAFE_Gradient_Text();
		}

		if( get_option( 'pafe-features-gradient-button', 2 ) == 2 || get_option( 'pafe-features-gradient-button', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-gradient-button.php' );
			new PAFE_Gradient_Button();
		}		

		if( get_option( 'pafe-features-tooltip', 2 ) == 2 || get_option( 'pafe-features-tooltip', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-tooltip.php' );
			new PAFE_Tooltip();
		}

		if( get_option( 'pafe-features-form-style', 2 ) == 2 || get_option( 'pafe-features-form-style', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-form-style.php' );
			new PAFE_Form_Style();
		}	

		if( get_option( 'pafe-features-particles', 2 ) == 2 || get_option( 'pafe-features-particles', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-particles.php' );
			new PAFE_Particles();
		}	

	}

	public function init_widgets_new($widgets_manager) {

		if( get_option( 'pafe-features-before-after-image-comparison-slider', 2 ) == 2 || get_option( 'pafe-features-before-after-image-comparison-slider', 2 ) == 1 ) {
			require_once( __DIR__ . '/widgets/pafe-before-after-image-comparison-slider.php' );
			$widgets_manager->register( new \PAFE_Before_After_Image_Comparison_Slider() );
		}

		if( get_option( 'pafe-features-switch-content', 2 ) == 2 || get_option( 'pafe-features-switch-content', 2 ) == 1 ) {
			require_once( __DIR__ . '/widgets/pafe-switch-content.php' );
			$widgets_manager->register( new \PAFE_Switch_Content() );
		}

		if( get_option( 'pafe-features-video-playlist', 2 ) == 2 || get_option( 'pafe-features-video-playlist', 2 ) == 1 ) {
			require_once( __DIR__ . '/widgets/pafe-video-playlist.php' );
			$widgets_manager->register( new \PAFE_Video_Playlist() );
		}

		if( get_option( 'pafe-features-vertical-timeline', 2 ) == 2 || get_option( 'pafe-features-vertical-timeline', 2 ) == 1 ) {
			require_once( __DIR__ . '/widgets/pafe-vertical-timeline.php' );
			$widgets_manager->register( new \PAFE_Vertical_Timeline() );
		}

		if( get_option( 'pafe-features-image-accordion', 2 ) == 2 || get_option( 'pafe-features-image-accordion', 2 ) == 1 ) {
			require_once( __DIR__ . '/widgets/pafe-image-accordion.php' );
			$widgets_manager->register( new \PAFE_Image_Accordion() );
		}

		if( get_option( 'pafe-features-posts-list', 2 ) == 2 || get_option( 'pafe-features-posts-list', 2 ) == 1 ) {
			require_once( __DIR__ . '/widgets/pafe-posts-list.php' );
			$widgets_manager->register( new \PAFE_Posts_List() );
		}

		if( get_option( 'pafe-features-sales-pop', 2 ) == 2 || get_option( 'pafe-features-sales-pop', 2 ) == 1 ) {
			require_once( __DIR__ . '/widgets/pafe-sales-pop.php' );
			$widgets_manager->register( new \PAFE_Sales_Pop() ); 
		}
		if( get_option( 'pafe-features-countdown-cart', 2 ) == 2 || get_option( 'pafe-features-countdown-cart', 2 ) == 1 ) {
			require_once( __DIR__ . '/widgets/pafe-countdown-cart.php' );
			$widgets_manager->register( new \PAFE_Countdown_Cart() ); 
		}
		if( get_option( 'pafe-features-dual-color-headline', 2 ) == 2 || get_option( 'pafe-features-dual-color-headline', 2 ) == 1 ) {
			require_once( __DIR__ . '/widgets/pafe-dual-color-headline.php' );
			$widgets_manager->register( new \PAFE_DUal_Color_Headline() ); 
		}
		if( get_option( 'pafe-features-hotspot', 2 ) == 2 || get_option( 'pafe-features-hotspot', 2 ) == 1 ) {
			require_once( __DIR__ . '/widgets/pafe-hotspot.php' );
			$widgets_manager->register( new \PAFE_Hotspot() ); 
		}
		if( get_option( 'pafe-features-progressbar', 2 ) == 2 || get_option( 'pafe-features-progressbar', 2 ) == 1 ) {
			require_once( __DIR__ . '/widgets/pafe-progressbar.php' );
			$widgets_manager->register( new \PAFE_Progress_Bar() ); 
		}
		if( get_option( 'pafe-features-table', 2 ) == 2 || get_option( 'pafe-features-table', 2 ) == 1 ) {
			require_once( __DIR__ . '/widgets/pafe-table.php' );
			$widgets_manager->register( new \PAFE_Table() ); 
		}

	}

    public function add_wpml_support()
    {
        require_once(__DIR__ . '/widgets/pafe-video-playlist.php');
        $widget = new PAFE_Video_Playlist();
        $widget->add_wpml_support();

        require_once(__DIR__ . '/widgets/pafe-table.php');
        $widget = new PAFE_Table();
        $widget->add_wpml_support();

        require_once(__DIR__ . '/widgets/pafe-switch-content.php');
        $widget = new PAFE_Switch_Content();
        $widget->add_wpml_support();
    }
}

Piotnet_Addons_For_Elementor::instance();
