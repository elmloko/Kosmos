<?php

namespace Saltus\WP\Plugin\Saltus\InteractiveMaps;

/**
 * The core class, where logic is defined.
 */
class Core
{
    /**
     * Unique identifier (slug)
     *
     * @var string
     */
    public  $name ;
    /**
     * Current version.
     *
     * @var string
     */
    public  $version ;
    /**
     * Plugin file path
     *
     * @var string
     */
    public  $file_path ;
    /**
     * Saltus framework instance
     *
     * @var object
     */
    public  $framework ;
    /**
     * Arrays that will store data to be localized for javascript files
     *
     * @var array
     */
    public  $script_localize_data ;
    public  $script_localize_options ;
    public  $script_localize_async_srcs ;
    /**
     * Content to output in footer with script tags
     *
     * @var string
     */
    public  $footer_extra ;
    /**
     * Content to output in footer
     *
     * @var string
     */
    public  $footer_content ;
    /**
     * Instance of Actions class
     *
     * @var object
     */
    public  $actions ;
    /**
     * Setup the class variables
     *
     * @param string $name      Plugin name.
     * @param string $version   Plugin version. Use semver.
     * @param string $file_path Plugin file path
     * @param string $saltus    Saltus Framework
     */
    public function __construct(
        string $name,
        string $version,
        string $file_path,
        $framework
    )
    {
        $this->name = $name;
        $this->version = $version;
        $this->file_path = $file_path;
        $this->framework = $framework;
    }
    
    /**
     * Get the identifier, also used for i18n domain.
     *
     * @return string The unique identifier (slug)
     */
    public function get_name()
    {
        return $this->name;
    }
    
    /**
     * Get the current version.
     *
     * @return string The current version.
     */
    public function get_version()
    {
        return $this->version;
    }
    
    /**
     * Start the logic for this plugins.
     *
     * Runs on 'plugins_loaded' which is pre- 'init' filter
     */
    public function init()
    {
        $this->set_locale();
        $this->set_assets();
        $this->register_shortcode();
        $this->set_localize();
        $this->set_footer_content();
        $this->set_actions();
        $this->prepare_edit_screen();
        $this->register_util_shortcodes();
        $this->register_blocks();
        $this->prepare_meta_sanitize();
        $this->add_integrations();
        $this->prepare_assets_src();
        $this->admin_url_filters();
    }
    
    /**
     * Add filters to check if assets src is correct (fix bitnami issue for csf assets)
     *
     * @return void
     */
    public function prepare_assets_src()
    {
        add_filter( 'script_loader_src', array( $this, 'check_admin_assets_src' ) );
        add_filter( 'style_loader_src', array( $this, 'check_admin_assets_src' ) );
    }
    
    /**
     * Function to remove unwanted parts from src url - fix opts/bitnami issue
     *
     * @param [type] $url
     * @return void
     */
    public function check_admin_assets_src( $url )
    {
        global  $current_screen ;
        if ( !is_admin() || !isset( $current_screen ) || 'igmap' !== $current_screen->post_type ) {
            return $url;
        }
        return str_replace( '/plugins/opt/interactive-geo-maps', '/plugins/interactive-geo-maps', $url );
    }
    
    /**
     * Adds filter to admin_url
     *
     * @return void
     */
    public function admin_url_filters()
    {
        add_filter(
            'admin_url',
            array( $this, 'check_remember_tab_url' ),
            1,
            10
        );
    }
    
    /**
     * Adds check to see if extra parameter is set on admin url on save igm
     * Used to remember tab
     *
     * @param string $link
     * @return string
     */
    public function check_remember_tab_url( $link )
    {
        global  $current_screen ;
        if ( !is_admin() || !isset( $current_screen ) || 'igmap' !== $current_screen->post_type || wp_doing_ajax() ) {
            return $link;
        }
        
        if ( isset( $_REQUEST['igmtab'] ) ) {
            $params['igmtab'] = $_REQUEST['igmtab'];
            $link = add_query_arg( $params, $link );
        }
        
        return $link;
    }
    
    /**
     * Add css class to body in admin to control the show/hide addons menu
     *
     * @return void
     */
    public function add_admin_body_class()
    {
        add_filter( 'admin_body_class', function ( $classes ) {
            return $classes . ' igm-pro';
        } );
    }
    
    /**
     * Adds integration widgets
     *
     * @return void
     */
    public function add_integrations()
    {
        // elementor widget
        add_action( 'elementor/widgets/register', [ $this, 'elementor_widget' ] );
    }
    
    /**
     * Registers Elementor Widget
     *
     * @return void
     */
    public function elementor_widget()
    {
        \Elementor\Plugin::instance()->widgets_manager->register( new Plugin\Integrations\ElementorMapWidget() );
    }
    
    /**
     * Add filter for Codestar to sanitize properly the meta info when saving
     *
     * @return void
     */
    public function prepare_meta_sanitize()
    {
        add_filter(
            'csf_map_info_save',
            array( $this, 'sanitize_meta_save' ),
            1,
            3
        );
    }
    
    /**
     * Set initial empty array for localize data
     *
     * @return void
     */
    private function set_localize()
    {
        $this->script_localize_data = [];
        $this->script_localize_options = [];
        $this->script_localize_async_srcs = [];
    }
    
    /**
     * Set initial empty footer content
     *
     * @return void
     */
    private function set_footer_content()
    {
        $this->extra_scripts = '';
        $this->extra_styles = '';
        $this->footer_content = '';
        $this->footer_scripts = '';
    }
    
    /**
     * Add content to localize data array - data for the maps
     *
     * @param string $value
     * @return void
     */
    public function add_localize_data( $value )
    {
        array_push( $this->script_localize_data, $value );
    }
    
    /**
     * Add content to options localize data array
     *
     * @param string $value
     * @return void
     */
    public function add_localize_options( $value )
    {
        array_push( $this->script_localize_options, $value );
    }
    
    /**
     * Add content to options localize async sources array
     *
     * @param string $value
     * @return void
     */
    public function add_localize_async_srcs( $value )
    {
        array_push( $this->script_localize_async_srcs, $value );
    }
    
    /**
     * Add content to footer scripts
     *
     * @param string $value
     * @return void
     */
    public function add_extra_scripts( $value )
    {
        $this->extra_scripts .= $value;
    }
    
    /**
     * Add content styles
     *
     * @param string $value
     * @return void
     */
    public function add_extra_styles( $value )
    {
        $this->extra_styles .= $value;
    }
    
    /**
     * Add content to footer
     *
     * @param string $value
     * @return void
     */
    public function add_footer_content( $value )
    {
        $this->footer_content .= $value;
    }
    
    /**
     * Collect content for raw scripts
     *
     * @param string $value
     * @return void
     */
    public function add_footer_scripts( $value )
    {
        $this->footer_scripts .= $value;
    }
    
    /**
     * Instanciate actions class
     *
     * @return void
     */
    public function set_actions()
    {
        $this->actions = new Plugin\Actions( $this );
    }
    
    /**
     * Load translations
     */
    private function set_locale()
    {
        $i18n = new Plugin\I18n( $this->name );
        $i18n->load_plugin_textdomain( dirname( $this->file_path ) );
    }
    
    /**
     * Load assets
     */
    private function set_assets()
    {
        $assets = new Plugin\Assets( $this );
        $assets->load_assets();
    }
    
    /**
     * Register Shortcode
     */
    public function register_shortcode()
    {
        add_shortcode( 'display-map', array( $this, 'render_shortcode' ) );
        // alternative shortcode to avoid conflicts with other map plugins
        add_shortcode( 'display-igmap', array( $this, 'render_shortcode' ) );
    }
    
    /**
     * Register blocks
     */
    public function register_blocks()
    {
        $map_block = new Plugin\Blocks\MapBlock( $this );
    }
    
    /* Util Shortcodes */
    public function register_util_shortcodes()
    {
        $dropdown_preview = new Plugin\Utils\MapListDropdown( $this );
        $list_preview = new Plugin\Utils\MapListOutput( $this );
        $current_map_preview = new Plugin\Utils\MapListCurrent( $this );
    }
    
    /**
     * Render shortcode
     */
    public function render_shortcode( $atts, $content = null, $tag = '' )
    {
        // normalize attribute keys, lowercase
        $atts = array_change_key_case( (array) $atts, CASE_LOWER );
        // override default attributes with user attributes
        $map_atts = shortcode_atts( array(
            'id'           => null,
            'meta'         => null,
            'regions'      => null,
            'roundmarkers' => null,
            'demo'         => null,
        ), $atts, $tag );
        if ( !isset( $map_atts['id'] ) ) {
            return;
        }
        $map_atts['id'] = (int) $map_atts['id'];
        if ( $map_atts['id'] === 0 ) {
            return;
        }
        $id_post_type = get_post_type( $map_atts['id'] );
        if ( $id_post_type !== 'igmap' ) {
            return;
        }
        $map = new Plugin\Map( $this );
        $html = $map->render( $map_atts, $this );
        // add footer scripts
        add_action( 'wp_footer', array( $this, 'footer_content' ) );
        return $html;
    }
    
    /**
     * Init edit screen
     *
     * @return void
     */
    public function prepare_edit_screen()
    {
        $edit = new Plugin\EditMap( $this );
    }
    
    /**
     * Add extra styles
     *
     * @return void
     */
    public function extra_styles()
    {
        if ( '' !== $this->extra_styles ) {
            wp_add_inline_style( $this->name . '_main', $this->extra_styles );
        }
    }
    
    /**
     * Output footer content
     *
     * @return void
     */
    public function footer_content()
    {
        
        if ( '' !== $this->footer_content && !is_admin() ) {
            $html = '<div id="igm-hidden-footer-content">' . $this->footer_content . '</div>';
            // we should sanitize for security, but users want to include all kinds of content, including forms.
            /*
            	$allowed_html = wp_kses_allowed_html( 'post' );
            	$allowed_html['style'] = [
            		'type' => true,
            	];
            	echo wp_kses( $html, $allowed_html );
            */
            echo  $html ;
        }
    
    }
    
    /**
     * Check if string is valid json
     *
     * @param [type] $string
     * @return boolean
     */
    public function isJson( $string )
    {
        json_decode( $string );
        return json_last_error() === JSON_ERROR_NONE;
    }
    
    /**
     * Function to sanitize meta on save
     *
     * @param array $request with meta info
     * @param int $post_id
     * @param obj $csf class
     * @return array
     */
    public function sanitize_meta_save( $request, $post_id, $csf )
    {
        if ( empty($request) || !is_array( $request ) ) {
            return $request;
        }
        // if map_info for regions or markers doesn't have useDefaults,
        // it's a free map, we need to make sure we save the useDefaults for backward compatibility
        // in case use upgrades.
        if ( isset( $request['regions'] ) && is_array( $request['regions'] ) && !empty($request['regions']) && !isset( $request['regions'][0]['useDefaults'] ) ) {
            foreach ( $request['regions'] as $key => $field ) {
                if ( !isset( $field['useDefaults'] ) ) {
                    $request['regions'][$key]['useDefaults'] = '1';
                }
            }
        }
        if ( isset( $request['roundMarkers'] ) && is_array( $request['roundMarkers'] ) && !empty($request['roundMarkers']) && !isset( $request['roundMarkers'][0]['useDefaults'] ) ) {
            foreach ( $request['roundMarkers'] as $key => $field ) {
                if ( !isset( $field['useDefaults'] ) ) {
                    $request['roundMarkers'][$key]['useDefaults'] = '1';
                }
            }
        }
        //replace line breaks on meta info to make it compatible with export
        array_walk_recursive( $request, function ( &$value ) {
            $value = str_replace( "\r\n", "\n", $value );
        } );
        return $request;
    }

}