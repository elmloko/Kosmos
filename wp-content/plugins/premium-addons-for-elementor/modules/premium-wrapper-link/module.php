<?php
/**
 * Class: Module
 * Name: Wrapper Link
 * Slug: premium-wrapper-link
 */

namespace PremiumAddons\Modules\PremiumWrapperLink;

// Elementor Classes.
use Elementor\Controls_Manager;

// Premium Addons Classes.
use PremiumAddons\Admin\Includes\Admin_Helper;
use PremiumAddons\Includes\Helper_Functions;
use PremiumAddons\Includes\Premium_Template_Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

/**
 * Class Module For Premium Wrapper Link Addon.
 */
class Module {

	/**
	 * Load Script
	 *
	 * @var $load_script
	 */
	private static $load_script = null;

	/**
	 * Class object
	 *
	 * @var instance
	 */
	private static $instance = null;

	/**
	 * Template Instance
	 *
	 * @var template_instance
	 */
	protected $template_instance;

	/**
	 * Class Constructor Funcion.
	 */
	public function __construct() {

		$modules = Admin_Helper::get_enabled_elements();

		// Checks if Wrapper Link addon is enabled.
		$wrapper_link = $modules['premium-wrapper-link'];

		if ( ! $wrapper_link ) {
			return;
		}

		// Enqueue the required JS file.
		add_action( 'elementor/preview/enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Creates Premium Wrapper Link tab at the end of layout/content tab.
		add_action( 'elementor/element/section/section_layout/after_section_end', array( $this, 'register_controls' ), 10 );
		add_action( 'elementor/element/column/section_advanced/after_section_end', array( $this, 'register_controls' ), 10 );
		add_action( 'elementor/element/common/_section_style/after_section_end', array( $this, 'register_controls' ), 10 );

		// Frontend Hooks.
		add_action( 'elementor/frontend/section/before_render', array( $this, 'before_render' ) );
		add_action( 'elementor/frontend/column/before_render', array( $this, 'before_render' ) );
		add_action( 'elementor/widget/before_render_content', array( $this, 'before_render' ), 10, 1 );

		add_action( 'elementor/frontend/before_render', array( $this, 'check_script_enqueue' ) );

		if ( Helper_Functions::check_elementor_experiment( 'container' ) ) {
			add_action( 'elementor/element/container/section_layout/after_section_end', array( $this, 'register_controls' ), 10 );
			add_action( 'elementor/frontend/container/before_render', array( $this, 'before_render' ) );
		}

	}

	/**
	 * Register Global Tooltip controls.
	 *
	 * @since 1.0.0
	 * @access public
	 * @param object $element for current element.
	 */
	public function register_controls( $element ) {

		$tabs = Controls_Manager::TAB_CONTENT;

		if ( 'section' === $element->get_name() || 'column' === $element->get_name() || 'container' === $element->get_name() ) {
			$tabs = Controls_Manager::TAB_LAYOUT;
		}

		$element->start_controls_section(
			'section_premium_wrapper_link',
			array(
				'label' => sprintf( '<i class="pa-extension-icon pa-dash-icon"></i> %s', __( 'Wrapper Link', 'premium-addons-for-elementor' ) ),
				'tab'   => $tabs,
			)
		);

        $element->add_control(
            'wrapper_link_notice',
            array(
                'raw'             => __( 'Please note that Wrapper Link works on the frontend.', 'premium-addons-for-elemeentor' ),
                'type'            => Controls_Manager::RAW_HTML,
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
            )
        );

		$element->add_control(
			'premium_wrapper_link_selection',
			array(
				'label'       => __( 'Link Type', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'url'  => __( 'URL', 'premium-addons-for-elementor' ),
					'link' => __( 'Existing Page', 'premium-addons-for-elementor' ),
				),
				'default'     => 'url',
				'label_block' => true,
			)
		);

		$element->add_control(
			'premium_wrapper_link',
			array(
				'label'       => __( 'Link', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => array(
					'active' => true,
				),
				'placeholder' => 'https://example.com',
				'condition'   => array(
					'premium_wrapper_link_selection' => 'url',
				),
			)
		);

		$element->add_control(
			'premium_wrapper_existing_link',
			array(
				'label'       => __( 'Existing Page', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => $this->getTemplateInstance()->get_all_posts(),
				'condition'   => array(
					'premium_wrapper_link_selection' => 'link',
				),
				'multiple'    => false,
				'label_block' => true,
			)
		);

		$element->end_controls_section();

	}


	/**
	 * Render Wrapper Link output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access public
	 * @param object $element for current element.
	 */
	public function before_render( $element ) {

		$settings = $element->get_settings_for_display();

		$link_settings = array(
			'link'         => $settings['premium_wrapper_link'],
			'type'         => $settings['premium_wrapper_link_selection'],
			'existingPage' => $settings['premium_wrapper_link_selection'] == 'link' ? get_permalink( $settings['premium_wrapper_existing_link'] ) : '',
		);

		if ( $link_settings && ( ! empty( $link_settings['link']['url'] ) || ! empty( $link_settings['existingPage'] ) ) ) {
			$element->add_render_attribute(
				'_wrapper',
				array(
					'data-premium-element-link' => json_encode( $link_settings ),
					'style'                     => 'cursor: pointer',
				)
			);
		}

	}

	/**
	 * Enqueue scripts.
	 *
	 * Registers required dependencies for the extension and enqueues them.
	 *
	 * @since 1.6.5
	 * @access public
	 */
	public function enqueue_scripts() {

		if ( ! wp_script_is( 'pa-wrapper-link', 'enqueued' ) ) {
			wp_enqueue_script( 'pa-wrapper-link' );
		}
	}

	/**
	 * Check Script Enqueue
	 *
	 * Check if the script files should be loaded.
	 *
	 * @since 4.7.7
	 * @access public
	 *
	 * @param object $element for current element.
	 */
	public function check_script_enqueue( $element ) {

		if ( self::$load_script ) {
			return;
		}

		$this->enqueue_scripts();

		self::$load_script = true;

		remove_action( 'elementor/frontend/before_render', array( $this, 'check_script_enqueue' ) );

	}

	/**
	 * Get Elementor Helper Instance.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function getTemplateInstance() {

		$this->template_instance = Premium_Template_Tags::getInstance();

		return $this->template_instance;
	}

	/**
	 * Creates and returns an instance of the class
	 *
	 * @since 4.2.5
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
