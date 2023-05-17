<?php
/**
 * PA Core.
 */

namespace PremiumAddons\Includes;

if ( ! class_exists( 'PA_Core' ) ) {

	/**
	 * Intialize and Sets up the plugin
	 */
	class PA_Core {

		/**
		 * Member Variable
		 *
		 * @var instance
		 */
		private static $instance = null;

		/**
		 * Sets up needed actions/filters for the plug-in to initialize.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @return void
		 */
		public function __construct() {

			// Autoloader.
			spl_autoload_register( array( $this, 'autoload' ) );

			// Run plugin and require the necessary files.
			add_action( 'plugins_loaded', array( $this, 'premium_addons_elementor_setup' ) );

			// Load Elementor files.
			add_action( 'elementor/init', array( $this, 'elementor_init' ) );

			add_action( 'elementor/elements/categories_registered', array( $this, 'register_widgets_category' ), 9 );
			add_action( 'init', array( $this, 'init' ), -999 );

			// Register Activation hooks.
			register_activation_hook( PREMIUM_ADDONS_FILE, array( $this, 'set_transient' ) );

		}

		/**
		 * AutoLoad
		 *
		 * @since 3.20.9
		 * @param string $class class.
		 */
		public function autoload( $class ) {

			if ( 0 !== strpos( $class, 'PremiumAddons' ) ) {
				return;
			}

			$class_to_load = $class;

			if ( ! class_exists( $class_to_load ) ) {
				$filename = strtolower(
					preg_replace(
						array( '/^PremiumAddons\\\/', '/([a-z])([A-Z])/', '/_/', '/\\\/' ),
						array( '', '$1-$2', '-', DIRECTORY_SEPARATOR ),
						$class_to_load
					)
				);

				$filename = PREMIUM_ADDONS_PATH . $filename . '.php';

				if ( is_readable( $filename ) ) {
					include $filename;
				}
			}
		}

		/**
		 * Installs translation text domain and checks if Elementor is installed
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @return void
		 */
		public function premium_addons_elementor_setup() {

			// Load plugin textdomain.
			$this->load_domain();

			// load plugin necessary files.
			$this->load_files();

		}

		/**
		 * Set transient for admin review notice
		 *
		 * @since 3.1.7
		 * @access public
		 *
		 * @return void
		 */
		public function set_transient() {

			$cache_key = 'premium_notice_' . PREMIUM_ADDONS_VERSION;

			$expiration = 3600 * 72;

			set_transient( $cache_key, true, $expiration );
		}


		/**
		 * Require initial necessary files
		 *
		 * @since 2.6.8
		 * @access public
		 *
		 * @return void
		 */
		public function load_files() {

			\PremiumAddons\Admin\Includes\Admin_Helper::get_instance();

			$enabled_elements = \PremiumAddons\Admin\Includes\Admin_Helper::get_enabled_elements();

			if ( $enabled_elements['premium-assets-generator'] ) {
				\PremiumAddons\Includes\Assets_Manager::get_instance();
			}

		}

		/**
		 * Load plugin translated strings using text domain
		 *
		 * @since 2.6.8
		 * @access public
		 *
		 * @return void
		 */
		public function load_domain() {

			load_plugin_textdomain( 'premium-addons-for-elementor' );

		}

		/**
		 * Elementor Init
		 *
		 * Initialize plugin after Elementor is run.
		 *
		 * @since 2.6.8
		 * @access public
		 *
		 * @return void
		 */
		public function elementor_init() {

			require_once PREMIUM_ADDONS_PATH . 'includes/class-premium-template-tags.php';

			Compatibility\Premium_Addons_Wpml::get_instance();

			Addons_Integration::get_instance();

		}

		/**
		 * Register Widgets Category
		 *
		 * Register a new category for Premium Addons widgets
		 *
		 * @since 4.0.0
		 * @access public
		 *
		 * @param object $elements_manager elements manager.
		 */
		public function register_widgets_category( $elements_manager ) {

			$elements_manager->add_category(
				'premium-elements',
				array(
					'title' => Helper_Functions::get_category(),
				),
				1
			);

		}

		/**
		 * Init
		 *
		 * @since 3.4.0
		 * @access public
		 *
		 * @return void
		 */
		public function init() {

			if ( \PremiumAddons\Admin\Includes\Admin_Helper::check_premium_templates() ) {
				require_once PREMIUM_ADDONS_PATH . 'includes/templates/templates.php';
			}
		}


		/**
		 * Creates and returns an instance of the class
		 *
		 * @since 2.6.8
		 * @access public
		 *
		 * @return object
		 */
		public static function get_instance() {

			if ( ! isset( self::$instance ) ) {

				self::$instance = new self();

			}

			return self::$instance;
		}

	}
}

if ( ! function_exists( 'pa_core' ) ) {

	/**
	 * Returns an instance of the plugin class.
	 *
	 * @since  1.0.0
	 * @return object
	 */
	function pa_core() {
		return PA_Core::get_instance();
	}
}

pa_core();
