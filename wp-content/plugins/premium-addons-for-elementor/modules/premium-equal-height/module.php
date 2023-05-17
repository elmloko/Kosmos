<?php
/**
 * Class: Module
 * Name: Premium Equal Height
 * Slug: premium-equal-height
 */

namespace PremiumAddons\Modules\Premium_Equal_Height;

// PremiumAddons Classes.
use PremiumAddons\Includes\Controls\Premium_Select;
use PremiumAddons\Admin\Includes\Admin_Helper;
use PremiumAddons\Includes\Helper_Functions;

// Elementor Classes.
use Elementor\Repeater;
use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Module For Premium Equal Height addon.
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
	 * Class Constructor Funcion.
	 */
	public function __construct() {

		// Enqueue the required JS file.
		add_action( 'elementor/preview/enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Create Premium Equal Height tab at the end of section layout tab.
		add_action( 'elementor/element/section/section_advanced/after_section_end', array( $this, 'register_controls' ), 10 );

		add_action( 'elementor/section/print_template', array( $this, '_print_template' ), 10, 2 );

		// Insert data before section rendering.
		add_action( 'elementor/frontend/section/before_render', array( $this, 'before_render' ), 10, 1 );

		// Check if scripts should be loaded.
		add_action( 'elementor/frontend/section/before_render', array( $this, 'check_script_enqueue' ) );

		if ( Helper_Functions::check_elementor_experiment( 'container' ) ) {
			add_action( 'elementor/element/container/section_layout/after_section_end', array( $this, 'register_controls' ), 10 );
			add_action( 'elementor/container/print_template', array( $this, '_print_template' ), 10, 2 );
			add_action( 'elementor/frontend/container/before_render', array( $this, 'before_render' ), 10, 1 );
			add_action( 'elementor/frontend/container/before_render', array( $this, 'check_script_enqueue' ) );
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

		if ( ! wp_script_is( 'pa-eq-height', 'enqueued' ) ) {
			wp_enqueue_script( 'pa-eq-height' );
		}

	}

	/**
	 * Register Premium Equal Height controls.
	 *
	 * @access public
	 * @param object $element for current element.
	 */
	public function register_controls( $element ) {

		$element->start_controls_section(
			'section_premium_eq_height',
			array(
				'label' => sprintf( '<i class="pa-extension-icon pa-dash-icon"></i> %s', __( 'Equal Height', 'premium-addons-for-elementor' ) ),
				'tab'   => Controls_Manager::TAB_ADVANCED,
			)
		);

		$element->add_control(
			'premium_eq_height_update',
			array(
				'label' => '<div class="elementor-update-preview editor-pa-preview-update" style="background-color: #fff;"><div class="elementor-update-preview-title">Update changes to page</div><div class="elementor-update-preview-button-wrapper"><button class="elementor-update-preview-button elementor-button elementor-button-success">Apply</button></div></div>',
				'type'  => Controls_Manager::RAW_HTML,
			)
		);

		$element->add_control(
			'premium_eq_height_switcher',
			array(
				'label'              => __( 'Enable Equal Height', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'return_value'       => 'yes',
				'render_type'        => 'template',
				'prefix_class'       => 'premium-equal-height-',
				'frontend_available' => true,
			)
		);

		$element->add_control(
			'premium_eq_height_type',
			array(
				'label'     => __( 'Apply on', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'widget',
				'options'   => array(
					'widget' => __( 'Widgets', 'premium-addons-for-elementor' ),
					'custom' => __( 'Custom Selector', 'premium-addons-for-elementor' ),
				),
				'condition' => array(
					'premium_eq_height_switcher' => 'yes',
				),
			)
		);

		$element->add_control(
			'premium_eq_height_target',
			array(
				'label'              => __( 'Widgets', 'premium-addons-for-elementor' ),
				'type'               => Premium_Select::TYPE,
				'render_type'        => 'template',
				'label_block'        => true,
				'multiple'           => true,
				'frontend_available' => true,
				'condition'          => array(
					'premium_eq_height_switcher' => 'yes',
					'premium_eq_height_type'     => 'widget',
				),
			)
		);

		$element->add_control(
			'premium_eq_height_custom_target',
			array(
				'label'       => __( 'Selectors', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => __( '.class-name, .class-name2 .my-custom-class', 'premium-addons-for-elementor' ),
				'description' => __( 'Enter selectors separated with \' , \' ', 'premium-addons-for-elementor' ),
				'condition'   => array(
					'premium_eq_height_switcher' => 'yes',
					'premium_eq_height_type'     => 'custom',
				),
			)
		);

		$element->add_control(
			'premium_eq_height_enable_on',
			array(
				'label'       => __( 'Enable Equal Height on', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'options'     => Helper_Functions::get_all_breakpoints(),
				'label_block' => true,
				'default'     => Helper_Functions::get_all_breakpoints( 'keys' ),
				'condition'   => array(
					'premium_eq_height_switcher' => 'yes',
				),
			)
		);

		$url     = 'https://premiumaddons.com/docs/elementor-column-equal-height/';
		$doc_url = Helper_Functions::get_campaign_link( $url, 'editor-page', 'wp-editor', 'get-support' );

		$element->add_control(
			'equal_height_notice',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf( '<a href="%s" target="_blank">%s</a>', $doc_url, __( 'How to use Premium Equal Height option »', 'premium-addons-for-elementor' ) ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$element->end_controls_section();

	}

	/**
	 * Render Premium Equal Height output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 4.2.5
	 * @access public
	 * @param object $template for current template.
	 * @param object $element for current element.
	 */
	public function _print_template( $template, $element ) {

		if ( $element->get_name() !== 'section' && $element->get_name() !== 'container' ) {
			return $template;
		}

		$old_template = $template;
		ob_start();

		?>
		<# if( 'yes' === settings.premium_eq_height_switcher ) {

			view.addRenderAttribute( 'eq_height', 'id', 'premium-temp-equal-height-' + view.getID() );
			var targetType = settings.premium_eq_height_type,

				target = 'custom' === targetType ? settings.premium_eq_height_custom_target.split(',') : settings.premium_eq_height_target,

				addonSettings = {
					'targetType': targetType,
					'target': target,
					'enableOn':settings.premium_eq_height_enable_on
				};

			view.addRenderAttribute( 'equal_height', {
				'id' : 'premium-temp-equal-height-' + view.getID(),
				'data-pa-eq-height': JSON.stringify( addonSettings )
			});

		#>
			<div {{{ view.getRenderAttributeString( 'equal_height' ) }}}></div>
		<# } #>
		<?php

		$slider_content = ob_get_contents();

		ob_end_clean();

		$template = $slider_content . $old_template;
		return $template;
	}

	/**
	 * Render Premium Equal Height output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 4.2.5
	 * @access public
	 *
	 * @param object $element for current element.
	 */
	public function before_render( $element ) {

		$settings = $element->get_settings_for_display();

		if ( 'yes' === $settings['premium_eq_height_switcher'] ) {

			$target_type = $settings['premium_eq_height_type'];

			$target = ( 'custom' === $target_type ) ? explode( ',', $settings['premium_eq_height_custom_target'] ) : $settings['premium_eq_height_target'];

			$addon_settings = array(
				'targetType' => $target_type,
				'target'     => $target,
				'enableOn'   => $settings['premium_eq_height_enable_on'],
			);

			$element->add_render_attribute( '_wrapper', 'data-pa-eq-height', wp_json_encode( $addon_settings ) );
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

		if ( 'yes' === $element->get_settings_for_display( 'premium_eq_height_switcher' ) ) {
			$this->enqueue_scripts();

			self::$load_script = true;

			remove_action( 'elementor/frontend/section/before_render', array( $this, 'check_script_enqueue' ) );
			remove_action( 'elementor/frontend/container/before_render', array( $this, 'check_script_enqueue' ) );
		}

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
