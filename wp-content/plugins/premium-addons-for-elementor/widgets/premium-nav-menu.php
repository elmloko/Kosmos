<?php
/**
 * Premium Nav Menu.
 */

namespace PremiumAddons\Widgets;

// Elementor Classes.
use Elementor\Widget_Base;
use Elementor\Utils;
use Elementor\Control_Media;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

// PremiumAddons Classes.
use PremiumAddons\Includes\Helper_Functions;
use PremiumAddons\Includes\Pa_Nav_Menu_Walker;
use PremiumAddons\Includes\Premium_Template_Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

/**
 * Class Premium_Person.
 */
class Premium_Nav_Menu extends Widget_Base {

	/**
	 * Template Instance
	 *
	 * @var template_instance
	 */
	protected $template_instance;

	/**
	 * Get Elementor Helper Instance.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function getTemplateInstance() {
		return $this->template_instance = Premium_Template_Tags::getInstance();
	}

	/**
	 * Retrieve Widget Name.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_name() {
		return 'premium-nav-menu';
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return __( 'Mega Menu', 'premium-addons-for-elementor' );
	}

	/**
	 * Retrieve Widget Icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string widget icon.
	 */
	public function get_icon() {
		return 'pa-mega-menu';
	}

	/**
	 * Retrieve Widget Dependent CSS.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array CSS style handles.
	 */
	public function get_style_depends() {
		return array(
			'font-awesome-5-all',
			'premium-addons',
		);
	}

	/**
	 * Retrieve Widget Dependent JS.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array JS script handles.
	 */
	public function get_script_depends() {
		return array(
			'lottie-js',
			'pa-headroom',
			'pa-menu',
		);
	}

	/**
	 * Retrieve Widget Categories.
	 *
	 * @since 1.5.1
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( 'premium-elements' );
	}

	/**
	 * Retrieve Widget Keywords.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget keywords.
	 */
	public function get_keywords() {
		return array( 'pa', 'premium', 'menu', 'nav', 'navigation', 'mega menu', 'header' );
	}

	/**
	 * Retrieve Widget Support URL.
	 *
	 * @access public
	 *
	 * @return string support URL.
	 */
	public function get_custom_help_url() {
		return 'https://premiumaddons.com/support/';
	}

	/**
	 * Get Menu List.
	 *
	 * @access private
	 * @since 4.9.3
	 *
	 * @return array
	 */
	private function get_menu_list() {

		$menus = wp_list_pluck( wp_get_nav_menus(), 'name', 'term_id' );  // term_id >> index key , name >> value of that index.

		return $menus;
	}

	/**
	 * Register Nav Menu Controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {

		$this->get_menu_settings_controls();

		$this->get_menu_content_controls();

		$this->add_random_badges_section( $this );

		$this->add_helpful_docs_section();

		$this->get_menu_style_controls();

	}

	/**
	 * Get menu style controls.
	 *
	 * @access private
	 * @since 4.9.3
	 */
	private function get_menu_style_controls() {
		$this->get_sticky_style();
		$this->get_ver_toggler_style();
		$this->get_menu_container_style();
		$this->get_menu_item_style();
		$this->get_menu_item_extras();
		$this->get_submenu_container_style();
		$this->get_submenu_item_style();
		$this->get_sub_menu_item_extras();
		$this->get_toggle_menu_sytle();
	}

	/**
	 * Get menu content controls.
	 *
	 * @access private
	 * @since 4.9.3
	 */
	private function get_menu_settings_controls() {

		$this->start_controls_section(
			'premium_nav_section',
			array(
				'label' => __( 'Menu Settings', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'menu_type',
			array(
				'label'   => __( 'Menu Type', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'wordpress_menu',
				'options' => array(
					'wordpress_menu' => __( 'WordPress Menu', 'premium-addons-for-elementor' ),
					'custom'         => __( 'Custom Menu', 'premium-addons-for-elementor' ),
				),
			)
		);

		$get_pro = Helper_Functions::get_campaign_link( 'https://premiumaddons.com/pro', 'editor-page', 'wp-editor', 'get-pro' );

		$papro_activated = apply_filters( 'papro_activated', false );

		if ( ! $papro_activated ) {
			$this->add_control(
				'custom_menu_notice',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => __( 'Custom Menu can be used in Premium Addons Pro.', 'premium-addons-for-elementor' ) . '<a href="' . esc_url( $get_pro ) . '" target="_blank">' . __( ' Upgrade now!', 'premium-addons-for-elementor' ) . '</a>',
					'content_classes' => 'papro-upgrade-notice',
					'condition'       => array(
						'menu_type' => 'custom',
					),
				)
			);
		}

		$menu_list = $this->get_menu_list();

		if ( ! empty( $menu_list ) ) {

			$this->add_control(
				'pa_nav_menus',
				array(
					'label'     => __( 'Menu', 'premium-addons-for-elementor' ),
					'type'      => Controls_Manager::SELECT,
					'options'   => $menu_list,
					'condition' => array(
						'menu_type' => 'wordpress_menu',
					),
				)
			);

		} else {
			$this->add_control(
				'empty_nav_menu_notice',
				array(
					'raw'             => '<strong>' . __( 'There are no menus in your site.', 'premium-addons-for-elementor' ) . '</strong><br>' . sprintf( __( 'Go to the <a href="%s" target="_blank">Menus screen</a> to create one.', 'premium-addons-for-elementor' ), admin_url( 'nav-menus.php?action=edit&menu=0' ) ),
					'type'            => Controls_Manager::RAW_HTML,
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
					'condition'       => array(
						'menu_type' => 'wordpress_menu',
					),
				)
			);
		}

		if ( $papro_activated ) {
			$this->add_control(
				'custom_nav_notice',
				array(
					'raw'             => __( 'It\'s not recommended to use Elementor Template and Link Submenu Items together under the same menu item', 'premium-addons-for-elemeentor' ),
					'type'            => Controls_Manager::RAW_HTML,
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
					'condition'       => array(
						'menu_type' => 'custom',
					),
				)
			);
		}

		$repeater = new Repeater();

		$repeater->add_control(
			'item_type',
			array(
				'label'   => __( 'Item Type', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'menu',
				'options' => array(
					'menu'    => __( 'Menu', 'premium-addons-for-elementor' ),
					'submenu' => __( 'Submenu', 'premium-addons-for-elementor' ),
				),
			)
		);

		$repeater->add_control(
			'menu_content_type',
			array(
				'label'     => __( 'Content Type', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'link'           => __( 'Link', 'premium-addons-for-elementor' ),
					'custom_content' => __( 'Elementor Template', 'premium-addons-for-elementor' ),
				),
				'default'   => 'link',
				'condition' => array(
					'item_type' => 'submenu',
				),
			)
		);

		$repeater->add_control(
			'text',
			array(
				'label'      => __( 'Text', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::TEXT,
				'default'    => __( 'Item', 'premium-addons-for-elementor' ),
				'dynamic'    => array(
					'active' => true,
				),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'item_type',
							'operator' => '==',
							'value'    => 'menu',
						),
						array(
							'name'     => 'menu_content_type',
							'operator' => '==',
							'value'    => 'link',
						),
					),
				),
			)
		);

		$repeater->add_control(
			'link',
			array(
				'label'      => __( 'Link', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::URL,
				'default'    => array(
					'url'         => '#',
					'is_external' => '',
				),
				'dynamic'    => array(
					'active' => true,
				),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'item_type',
							'operator' => '==',
							'value'    => 'menu',
						),
						array(
							'name'     => 'menu_content_type',
							'operator' => '==',
							'value'    => 'link',
						),
					),
				),
			)
		);

		$repeater->add_control(
			'live_temp_content',
			array(
				'label'       => __( 'Template Title', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'classes'     => 'premium-live-temp-title control-hidden',
				'label_block' => true,
				'condition'   => array(
					'item_type'         => 'submenu',
					'menu_content_type' => 'custom_content',
				),
			)
		);

		$repeater->add_control(
			'submenu_item_live',
			array(
				'type'        => Controls_Manager::BUTTON,
				'label_block' => true,
				'button_type' => 'default papro-btn-block',
				'text'        => __( 'Create / Edit Template', 'premium-addons-for-elementor' ),
				'event'       => 'createLiveTemp',
				'condition'   => array(
					'item_type'         => 'submenu',
					'menu_content_type' => 'custom_content',
				),
			)
		);

		$repeater->add_control(
			'submenu_item',
			array(
				'label'       => __( 'Select Existing Template', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT2,
				'classes'     => 'premium-live-temp-label',
				'label_block' => true,
				'options'     => $this->getTemplateInstance()->get_elementor_page_list(),
				'condition'   => array(
					'item_type'         => 'submenu',
					'menu_content_type' => 'custom_content',
				),
			)
		);

		$repeater->add_control(
			'section_full_width',
			array(
				'label'       => __( 'Full Width Dropdown', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Enable this option to set the dropdown width to the same width of the parent section', 'premium-addons-for-elementor' ),
				'condition'   => array(
					'item_type' => 'menu',
				),
			)
		);

		$repeater->add_responsive_control(
			'section_width',
			array(
				'label'     => __( 'Dropdown Minimum Width (px)', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 1500,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} ul.premium-sub-menu, {{WRAPPER}} {{CURRENT_ITEM}} .premium-mega-content-container' => 'min-width: {{SIZE}}{{UNIT}}',
				),
				'condition' => array(
					'item_type'           => 'menu',
					'section_full_width!' => 'yes',
				),
			)
		);

		$repeater->add_responsive_control(
			'section_position',
			array(
				'label'       => __( 'Align to Widget Center', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'This option centers the mega content to the center of the widget container. <b> Only works when Full Width Dropdown option is disabled </b>', 'premium-addons-for-elementor' ),
				'condition'   => array(
					'item_type'         => 'submenu',
					'menu_content_type' => 'custom_content',
				),
			)
		);

		$repeater->add_control(
			'icon_switcher',
			array(
				'label'      => __( 'Icon', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SWITCHER,
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'item_type',
							'operator' => '==',
							'value'    => 'menu',
						),
						array(
							'name'     => 'menu_content_type',
							'operator' => '==',
							'value'    => 'link',
						),
					),
				),
			)
		);

		$repeater->add_control(
			'icon_type',
			array(
				'label'       => __( 'Icon Type', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'description' => __( 'Use a font awesome icon or upload a custom image', 'premium-addons-for-elementor' ),
				'options'     => array(
					'icon'      => __( 'Icon', 'premium-addons-for-elementor' ),
					'image'     => __( 'Image', 'premium-addons-for-elementor' ),
					'animation' => __( 'Lottie Animation', 'premium-addons-for-elementor' ),
				),
				'default'     => 'icon',
				'conditions'  => array(
					'relation' => 'and',
					'terms'    => array(
						array(
							'name'  => 'icon_switcher',
							'value' => 'yes',
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'     => 'item_type',
									'operator' => '==',
									'value'    => 'menu',
								),
								array(
									'name'     => 'menu_content_type',
									'operator' => '==',
									'value'    => 'link',
								),
							),
						),

					),
				),
			)
		);

		$repeater->add_control(
			'item_icon',
			array(
				'label'                  => __( 'Select an Icon', 'premium-addons-for-elementor' ),
				'type'                   => Controls_Manager::ICONS,
				'label_block'            => false,
				'skin'                   => 'inline',
				'exclude_inline_options' => array( 'svg' ),
				'conditions'             => array(
					'relation' => 'and',
					'terms'    => array(
						array(
							'name'  => 'icon_switcher',
							'value' => 'yes',
						),
						array(
							'name'  => 'icon_type',
							'value' => 'icon',
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'     => 'item_type',
									'operator' => '==',
									'value'    => 'menu',
								),
								array(
									'name'     => 'menu_content_type',
									'operator' => '==',
									'value'    => 'link',
								),
							),
						),

					),
				),
			)
		);

		$repeater->add_control(
			'item_image',
			array(
				'label'      => __( 'Upload Image', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::MEDIA,
				'default'    => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'conditions' => array(
					'relation' => 'and',
					'terms'    => array(
						array(
							'name'  => 'icon_switcher',
							'value' => 'yes',
						),
						array(
							'name'  => 'icon_type',
							'value' => 'image',
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'     => 'item_type',
									'operator' => '==',
									'value'    => 'menu',
								),
								array(
									'name'     => 'menu_content_type',
									'operator' => '==',
									'value'    => 'link',
								),
							),
						),

					),
				),
			)
		);

		$repeater->add_control(
			'lottie_url',
			array(
				'label'       => __( 'Animation JSON URL', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'description' => 'Get JSON code URL from <a href="https://lottiefiles.com/" target="_blank">here</a>',
				'label_block' => true,
				'conditions'  => array(
					'relation' => 'and',
					'terms'    => array(
						array(
							'name'  => 'icon_switcher',
							'value' => 'yes',
						),
						array(
							'name'  => 'icon_type',
							'value' => 'animation',
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'     => 'item_type',
									'operator' => '==',
									'value'    => 'menu',
								),
								array(
									'name'     => 'menu_content_type',
									'operator' => '==',
									'value'    => 'link',
								),
							),
						),

					),
				),
			)
		);

		$repeater->add_control(
			'badge_switcher',
			array(
				'label'      => __( 'Badge', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SWITCHER,
				'separator'  => 'before',
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'item_type',
							'operator' => '==',
							'value'    => 'menu',
						),
						array(
							'name'     => 'menu_content_type',
							'operator' => '==',
							'value'    => 'link',
						),
					),
				),
			)
		);

		$repeater->add_control(
			'badge_text',
			array(
				'label'      => __( 'Badge Text', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::TEXT,
				'conditions' => array(
					'relation' => 'and',
					'terms'    => array(
						array(
							'name'  => 'badge_switcher',
							'value' => 'yes',
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'     => 'item_type',
									'operator' => '==',
									'value'    => 'menu',
								),
								array(
									'name'     => 'menu_content_type',
									'operator' => '==',
									'value'    => 'link',
								),
							),
						),

					),
				),
			)
		);

		if ( $papro_activated ) {

			do_action( 'pa_custom_menu_controls', $this, $repeater );

		}

		$this->end_controls_section();

	}

	/**
	 * Get menu content controls.
	 *
	 * @access private
	 * @since 4.9.3
	 */
	private function get_menu_content_controls() {

		$this->start_controls_section(
			'display_options_section',
			array(
				'label' => __( 'Display Options', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'menu_heading',
			array(
				'label' => __( 'Menu Settings', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'pa_nav_menu_layout',
			array(
				'label'        => __( 'Layout', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SELECT,
				'prefix_class' => 'premium-nav-',
				'options'      => array(
					'hor'      => 'Horizontal',
					'ver'      => 'Vertical',
					'dropdown' => 'Expand',
					'slide'    => 'Slide',
				),
				'render_type'  => 'template',
				'default'      => 'hor',
			)
		);

		$align_left  = is_rtl() ? 'flex-end' : 'flex-start';
		$align_right = is_rtl() ? 'flex-start' : 'flex-end';
		$align_def   = is_rtl() ? 'flex-end' : 'flex-start';

		$this->add_responsive_control(
			'pa_nav_menu_align',
			array(
				'label'     => __( 'Menu Alignment', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					$align_left     => array(
						'title' => __( 'Left', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-left',
					),
					'center'        => array(
						'title' => __( 'Center', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-center',
					),
					$align_right    => array(
						'title' => __( 'Right', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-right',
					),
					'space-between' => array(
						'title' => __( 'Strech', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-stretch',
					),
				),
				'default'   => 'center',
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .premium-main-nav-menu' => 'justify-content: {{VALUE}}',
				),
				'condition' => array(
					'pa_nav_menu_layout' => 'hor',
				),
			)
		);

		$this->add_responsive_control(
			'pa_nav_menu_align_ver',
			array(
				'label'     => __( 'Menu Alignment', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					$align_left  => array(
						'title' => __( 'Left', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'     => array(
						'title' => __( 'Center', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-center',
					),
					$align_right => array(
						'title' => __( 'Right', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => $align_def,
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .premium-main-nav-menu > .premium-nav-menu-item > .premium-menu-link' => 'justify-content: {{VALUE}}',
				),
				'condition' => array(
					'pa_nav_menu_layout' => 'ver',
				),
			)
		);

		$this->add_control(
			'pointer',
			array(
				'label'          => __( 'Item Hover Effect', 'premium-addons-for-elementor' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => 'none',
				'options'        => array(
					'none'        => __( 'None', 'premium-addons-for-elementor' ),
					'underline'   => __( 'Underline', 'premium-addons-for-elementor' ),
					'overline'    => __( 'Overline', 'premium-addons-for-elementor' ),
					'double-line' => __( 'Double Line', 'premium-addons-for-elementor' ),
					'framed'      => __( 'Framed', 'premium-addons-for-elementor' ),
					'background'  => __( 'Background', 'premium-addons-for-elementor' ),
					'text'        => __( 'Text', 'premium-addons-for-elementor' ),
				),
				'style_transfer' => true,
			)
		);

		$this->add_control(
			'animation_line',
			array(
				'label'     => __( 'Animation', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'fade',
				'options'   => array(
					'fade'     => 'Fade',
					'slide'    => 'Slide',
					'grow'     => 'Grow',
					'drop-in'  => 'Drop In',
					'drop-out' => 'Drop Out',
					'none'     => 'None',
				),
				'condition' => array(
					'pointer' => array( 'underline', 'overline', 'double-line' ),
				),
			)
		);

		$this->add_control(
			'animation_framed',
			array(
				'label'     => __( 'Animation', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'fade',
				'options'   => array(
					'fade'    => 'Fade',
					'grow'    => 'Grow',
					'shrink'  => 'Shrink',
					'draw'    => 'Draw',
					'corners' => 'Corners',
					'none'    => 'None',
				),
				'condition' => array(
					'pointer' => 'framed',
				),
			)
		);

		$this->add_control(
			'animation_background',
			array(
				'label'     => __( 'Animation', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'fade',
				'options'   => array(
					'fade'                   => 'Fade',
					'grow'                   => 'Grow',
					'shrink'                 => 'Shrink',
					'sweep-left'             => 'Sweep Left',
					'sweep-right'            => 'Sweep Right',
					'sweep-up'               => 'Sweep Up',
					'sweep-down'             => 'Sweep Down',
					'shutter-in-vertical'    => 'Shutter In Vertical',
					'shutter-out-vertical'   => 'Shutter Out Vertical',
					'shutter-in-horizontal'  => 'Shutter In Horizontal',
					'shutter-out-horizontal' => 'Shutter Out Horizontal',
					'none'                   => 'None',
				),
				'condition' => array(
					'pointer' => 'background',
				),
			)
		);

		$this->add_control(
			'animation_text',
			array(
				'label'     => __( 'Animation', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'grow',
				'options'   => array(
					'grow'   => 'Grow',
					'shrink' => 'Shrink',
					'sink'   => 'Sink',
					'float'  => 'Float',
					'skew'   => 'Skew',
					'rotate' => 'Rotate',
					'none'   => 'None',
				),
				'condition' => array(
					'pointer' => 'text',
				),
			)
		);

		$this->get_vertical_toggle_settings();

		$this->get_sticky_option_settings();

		$this->add_control(
			'submenu_heading',
			array(
				'label'     => __( 'Submenu Settings', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'submenu_icon',
			array(
				'label'                  => __( 'Submenu Indicator Icon', 'premium-addons-for-elementor' ),
				'type'                   => Controls_Manager::ICONS,
				'default'                => array(
					'value'   => 'fas fa-angle-down',
					'library' => 'fa-solid',
				),
				'recommended'            => array(
					'fa-solid' => array(
						'chevron-down',
						'angle-down',
						'caret-down',
						'plus',
					),
				),
				'label_block'            => false,
				'skin'                   => 'inline',
				'exclude_inline_options' => array( 'svg' ),
				'frontend_available'     => true,
			)
		);

		$this->add_control(
			'submenu_item_icon',
			array(
				'label'                  => __( 'Submenu Item Icon', 'premium-addons-for-elementor' ),
				'type'                   => Controls_Manager::ICONS,
				'recommended'            => array(
					'fa-solid' => array(
						'chevron-down',
						'angle-down',
						'caret-down',
						'plus',
					),
				),
				'label_block'            => false,
				'skin'                   => 'inline',
				'exclude_inline_options' => array( 'svg' ),
				'frontend_available'     => true,
				'condition'              => array(
					'menu_type' => 'wordpress_menu',
				),
			)
		);

		$default_pos   = is_rtl() ? 'left' : 'right';
		$default_align = is_rtl() ? 'flex-end' : 'flex-start';

		$this->add_responsive_control(
			'pa_nav_ver_submenu',
			array(
				'label'        => __( 'Submenu Position', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::CHOOSE,
				'render_type'  => 'template',
				'prefix_class' => 'premium-vertical-',
				'options'      => array(
					'left'  => array(
						'title' => __( 'Left', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-left',
					),
					'right' => array(
						'title' => __( 'Right', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'default'      => $default_pos,
				'toggle'       => false,
				'condition'    => array(
					'pa_nav_menu_layout' => 'ver',
				),
			)
		);

		$this->add_responsive_control(
			'pa_sub_menu_align',
			array(
				'label'     => __( 'Content Alignment', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => __( 'Left', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'     => array(
						'title' => __( 'Center', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end'   => array(
						'title' => __( 'Right', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => $default_align,
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .premium-sub-menu .premium-sub-menu-link' => 'justify-content: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'submenu_event',
			array(
				'label'       => __( 'Open Submenu On', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'hover',
				'render_type' => 'template',
				'options'     => array(
					'hover' => __( 'Hover', 'premium-addons-for-elementor' ),
					'click' => __( 'click', 'premium-addons-for-elementor' ),
				),
				'condition'   => array(
					'pa_nav_menu_layout' => array( 'hor', 'ver' ),
				),
			)
		);

		$this->add_control(
			'submenu_trigger',
			array(
				'label'       => __( 'Submenu Trigger', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'item',
				'render_type' => 'template',
				'options'     => array(
					'icon' => __( 'Submenu Dropdwon Icon', 'premium-addons-for-elementor' ),
					'item' => __( 'Submenu Item', 'premium-addons-for-elementor' ),
				),
				'condition'   => array(
					'pa_nav_menu_layout' => array( 'hor', 'ver' ),
					'submenu_event'      => 'click',
				),
			)
		);

		$this->add_control(
			'submenu_slide',
			array(
				'label'        => __( 'Submenu Animation', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SELECT,
				'prefix_class' => 'premium-nav-',
				'default'      => 'none',
				'options'      => array(
					'none'        => __( 'None', 'premium-addons-for-elementor' ),
					'slide-up'    => __( 'Slide Up', 'premium-addons-for-elementor' ),
					'slide-down'  => __( 'Slide Down', 'premium-addons-for-elementor' ),
					'slide-left'  => __( 'Slide Left', 'premium-addons-for-elementor' ),
					'slide-right' => __( 'Slide Right', 'premium-addons-for-elementor' ),
				),
				'condition'    => array(
					'pa_nav_menu_layout' => array( 'hor', 'ver' ),
				),
			)
		);

		// sub-items badge hover.
		$this->add_control(
			'sub_badge_hv_effects',
			array(
				'label'       => __( 'Badge Effects', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'render_type' => 'template',
				'default'     => '',
				'options'     => array(
					''            => __( 'None', 'premium-addons-for-elementor' ),
					'dot'         => __( 'Grow', 'premium-addons-for-elementor' ),
					'expand'      => __( 'Expand', 'premium-addons-for-elementor' ),
					'pulse'       => __( 'Pulse', 'premium-addons-for-elementor' ),
					'buzz'        => __( 'Buzz', 'premium-addons-for-elementor' ),
					'slide-right' => __( 'Slide Right', 'premium-addons-for-elementor' ),
					'slide-left'  => __( 'Slide Left', 'premium-addons-for-elementor' ),
				),
			)
		);

		$this->add_responsive_control(
			'dot_size',
			array(
				'label'       => __( 'Dot Size', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'label_block' => true,
				'size_units'  => array( 'px' ),
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 10,
					),
				),
				'selectors'   => array(
					'{{WRAPPER}} .premium-badge-dot .premium-sub-item-badge' => 'padding: {{SIZE}}{{UNIT}};',
				),
				'condition'   => array(
					'sub_badge_hv_effects' => 'dot',
				),
			)
		);

		// toggle menu settings.
		$this->add_control(
			'pa_toggle_heading',
			array(
				'label'     => __( 'Mobile Menu Settings', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'pa_nav_menu_layout' => array( 'hor', 'ver' ),
				),
			)
		);

		$this->add_control(
			'pa_mobile_menu_layout',
			array(
				'label'        => __( 'Layout', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SELECT,
				'render_type'  => 'template',
				'prefix_class' => 'premium-ham-',
				'options'      => array(
					'dropdown' => 'Expand',
					'slide'    => 'Slide',
				),
				'default'      => 'dropdown',
				'condition'    => array(
					'pa_nav_menu_layout' => array( 'hor', 'ver' ),
				),
			)
		);

		$this->add_control(
			'pa_mobile_menu_breakpoint',
			array(
				'label'     => __( 'Breakpoint', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'767'    => __( 'Mobile (<768)', 'premium-addons-for-elementor' ),
					'1024'   => __( 'Tablet (<1025)', 'premium-addons-for-elementor' ),
					'custom' => __( 'Custom', 'premium-addons-for-elementor' ),
				),
				'default'   => '1024',
				'condition' => array(
					'pa_nav_menu_layout' => array( 'hor', 'ver' ),
				),
			)
		);

		$this->add_control(
			'pa_custom_breakpoint',
			array(
				'label'       => __( 'Custom Breakpoint (px)', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 0,
				'max'         => 2000,
				'step'        => 5,
				'description' => 'Use this option to control when to turn your menu into a toggle menu, Default is 1025',
				'condition'   => array(
					'pa_nav_menu_layout'        => array( 'hor', 'ver' ),
					'pa_mobile_menu_breakpoint' => 'custom',
				),
			)
		);

		$this->end_controls_section();

		$this->get_dropdown_content_settings( $align_left, $align_right );

	}

	/**
	 * Add random badges control controls.
	 *
	 * @access private
	 * @since 4.9.34
	 */
	private function add_random_badges_section() {

		$this->start_controls_section(
			'premium_rn_badge_section',
			array(
				'label' => __( 'Random Badges', 'premium-addons-for-elementor' ),
			)
		);

		$papro_activated = apply_filters( 'papro_activated', false );

		if ( $papro_activated ) {
			if ( version_compare( PREMIUM_PRO_ADDONS_VERSION, '2.8.9', '>' ) ) {
				do_action( 'pa_rn_badges_controls', $this );

			} else {
				$this->add_control(
					'rn_badges_ver_notice',
					array(
						'type'            => Controls_Manager::RAW_HTML,
						'raw'             => __( 'Please update Premium Addons Pro version to 2.8.10 to use this option.', 'premium-addons-for-elementor' ),
						'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
					)
				);
			}
		} else {

			$get_pro = Helper_Functions::get_campaign_link( 'https://premiumaddons.com/pro', 'editor-page', 'wp-editor', 'get-pro' );

			$this->add_control(
				'rn_badges_notice',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => __( 'Random Badges can be used in Premium Addons Pro.', 'premium-addons-for-elementor' ) . '<a href="' . esc_url( $get_pro ) . '" target="_blank">' . __( ' Upgrade now!', 'premium-addons-for-elementor' ) . '</a>',
					'content_classes' => 'papro-upgrade-notice',
				)
			);
		}

		$this->end_controls_section();

	}

	/**
	 * Add Helpful Information Section
	 *
	 * @access private
	 * @since 4.9.35
	 */
	private function add_helpful_docs_section() {

		$this->start_controls_section(
			'section_pa_docs',
			array(
				'label' => __( 'Helpful Documentations', 'premium-addons-for-elementor' ),
			)
		);

		$docs = array(
			'https://premiumaddons.com/docs/elementor-mega-menu-widget-tutorial' => __( 'Getting started »', 'premium-addons-for-elementor' ),
			'https://premiumaddons.com/docs/elementor-mega-menu-widget-tutorial/#random-badges' => __( 'How to add random badges in Mega Menu widget »', 'premium-addons-for-elementor' ),
		);

		$doc_index = 1;
		foreach ( $docs as $url => $title ) {

			$doc_url = Helper_Functions::get_campaign_link( $url, 'editor-page', 'wp-editor', 'get-support' );

			$this->add_control(
				'doc_' . $doc_index,
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => sprintf( '<a href="%s" target="_blank">%s</a>', $doc_url, $title ),
					'content_classes' => 'editor-pa-doc',
				)
			);

			$doc_index++;

		}

		$this->end_controls_section();

	}

	/**
	 * Get sticky style options.
	 *
	 * @access private
	 * @since 4.9.15
	 */
	private function get_sticky_style() {

		$this->start_controls_section(
			'pa_sticky_style_sec',
			array(
				'label'     => __( 'Sticky Menu Style', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'pa_sticky_switcher' => 'yes',
					'pa_nav_menu_layout' => 'hor',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'           => 'pa_sticky_shadow',
				'label'          => __( 'Shadow', 'premium-addons-for-elementor' ),
				'fields_options' => array(
					'box_shadow' => array(
						'selectors' => array(
							'.premium-sticky-parent-{{ID}}' => 'box-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}} {{box_shadow_position.VALUE}} !important;',
						),
					),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'pa_sticky_bg',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '.premium-sticky-parent-{{ID}}',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pa_sticky_border',
				'selector' => '.premium-sticky-parent-{{ID}}',
			)
		);

		$this->add_responsive_control(
			'pa_sticky_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'.premium-sticky-parent-{{ID}}' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get sticky option settings.
	 *
	 * @access private
	 * @since 4.9.15
	 */
	private function get_sticky_option_settings() {

		$this->add_control(
			'pa_sticky_switcher',
			array(
				'label'              => __( 'Enable Sticky Menu', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'separator'          => 'before',
				'render_type'        => 'template',
				'prefix_class'       => 'premium-nav-sticky-',
				'condition'          => array(
					'pa_nav_menu_layout' => 'hor',
				),
			)
		);

		$this->add_control(
			'pa_sticky_target',
			array(
				'label'              => __( 'Sticky Target ID', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::TEXT,
				'frontend_available' => true,
				'render_type'        => 'template',
				'placeholder'        => 'sticky-target',
				'description'        => __( 'The target id to apply sticky effect on ( without the "#" ).', 'premium-addons-for-elementor' ),
				'condition'          => array(
					'pa_nav_menu_layout' => 'hor',
					'pa_sticky_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'pa_sticky_on_scroll',
			array(
				'label'              => __( 'Sticky on Scroll Up', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				// 'prefix_class' => 'premium-sticky-scroll-',
				'frontend_available' => true,
				'render_type'        => 'template',
				'condition'          => array(
					'pa_nav_menu_layout' => 'hor',
					'pa_sticky_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'pa_sticky_disabled_on',
			array(
				'label'              => __( 'Disable On', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::SELECT2,
				'frontend_available' => true,
				'options'            => Helper_Functions::get_all_breakpoints(),
				'multiple'           => true,
				'label_block'        => true,
				'render_type'        => 'template',
				'default'            => array( 'tablet', 'mobile' ),
				'condition'          => array(
					'pa_nav_menu_layout' => 'hor',
					'pa_sticky_switcher' => 'yes',
				),
			)
		);
	}

	/**
	 * Get vertical toggle settings.
	 *
	 * @access private
	 * @since 4.9.15
	 */
	private function get_vertical_toggle_settings() {

		$this->add_control(
			'pa_ver_toggle_switcher',
			array(
				'label'        => __( 'Enable Collapsed Menu', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'separator'    => 'before',
				'render_type'  => 'template',
				'prefix_class' => 'premium-ver-toggle-',
				'condition'    => array(
					'pa_nav_menu_layout' => 'ver',
				),
			)
		);

		$this->add_control(
			'pa_ver_toggle_txt',
			array(
				'label'     => __( 'Title', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Premium Menu', 'premium-addons-for-elementor' ),
				'condition' => array(
					'pa_nav_menu_layout'     => 'ver',
					'pa_ver_toggle_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'pa_ver_toggle_event',
			array(
				'label'        => __( 'Open On', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'click',
				'render_type'  => 'template',
				'prefix_class' => 'premium-ver-',
				'options'      => array(
					'hover'  => __( 'Hover', 'premium-addons-for-elementor' ),
					'click'  => __( 'Click', 'premium-addons-for-elementor' ),
					'always' => __( 'Always', 'premium-addons-for-elementor' ),
				),
				'condition'    => array(
					'pa_nav_menu_layout'     => 'ver',
					'pa_ver_toggle_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'pa_ver_toggle_open',
			array(
				'label'       => __( 'Opened By Default', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'render_type' => 'template',
				// 'prefix_class' => 'premium-ver-',
				'condition'   => array(
					'pa_nav_menu_layout'     => 'ver',
					'pa_ver_toggle_switcher' => 'yes',
					'pa_ver_toggle_event'    => 'click',
				),
			)
		);

		$this->add_control(
			'pa_ver_toggle_main_icon',
			array(
				'label'       => __( 'Title Icon', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::ICONS,
				'label_block' => false,
				'skin'        => 'inline',
				'default'     => array(
					'value'   => 'fas fa-bars',
					'library' => 'solid',
				),
				'condition'   => array(
					'pa_nav_menu_layout'     => 'ver',
					'pa_ver_toggle_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'pa_ver_toggle_toggle_icon',
			array(
				'label'       => __( 'Toggle Icon', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::ICONS,
				'label_block' => false,
				'skin'        => 'inline',
				'default'     => array(
					'value'   => 'fas fa-angle-down',
					'library' => 'solid',
				),
				'condition'   => array(
					'pa_nav_menu_layout'     => 'ver',
					'pa_ver_toggle_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'pa_ver_toggle_close_icon',
			array(
				'label'       => __( 'Close Icon', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::ICONS,
				'label_block' => false,
				'skin'        => 'inline',
				'default'     => array(
					'value'   => 'fas fa-angle-up',
					'library' => 'solid',
				),
				'condition'   => array(
					'pa_nav_menu_layout'     => 'ver',
					'pa_ver_toggle_switcher' => 'yes',
					'pa_ver_toggle_event!'   => 'always',
				),
			)
		);

		$this->add_control(
			'premium_ver_spacing',
			array(
				'label'       => __( 'Title Spacing', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'label_block' => true,
				'size_units'  => array( 'px', 'em' ),
				'description' => __( 'Use this option to control the spacing between the title icon and the title.', 'premium-addons-for-elementor' ),
				'selectors'   => array(
					'{{WRAPPER}} .premium-ver-toggler-txt' => 'text-indent: {{SIZE}}{{UNIT}};',
				),
				'condition'   => array(
					'pa_nav_menu_layout'     => 'ver',
					'pa_ver_toggle_switcher' => 'yes',
				),
			)
		);
	}

	/**
	 * Get dropdown content settings.
	 *
	 * @access private
	 * @since 4.9.15
	 *
	 * @param string $align_left   align-left val.
	 * @param string $align_right   align-right val.
	 */
	private function get_dropdown_content_settings( $align_left, $align_right ) {

		$this->start_controls_section(
			'premium_dropdown_section',
			array(
				'label' => __( 'Expand/Slide Menu Settings', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'btn_toggle_heading',
			array(
				'label' => __( 'Toggle Button', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'pa_mobile_toggle_text',
			array(
				'label'   => __( 'Text', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Menu', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'pa_mobile_toggle_close',
			array(
				'label'   => __( 'Close Text', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Close', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'pa_mobile_toggle_icon',
			array(
				'label'       => __( 'Icon', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::ICONS,
				'label_block' => false,
				'skin'        => 'inline',
				'default'     => array(
					'value'   => 'fas fa-bars',
					'library' => 'solid',
				),
			)
		);

		$this->add_control(
			'pa_mobile_close_icon',
			array(
				'label'       => __( 'Close Icon', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::ICONS,
				'label_block' => false,
				'skin'        => 'inline',
				'default'     => array(
					'value'   => 'fas fa-times',
					'library' => 'solid',
				),
			)
		);

		$this->add_responsive_control(
			'pa_mobile_toggle_pos',
			array(
				'label'     => __( 'Toggle Button Position', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					$align_left  => array(
						'title' => __( 'Left', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-left',
					),
					'center'     => array(
						'title' => __( 'Center', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-center',
					),
					$align_right => array(
						'title' => __( 'Right', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'default'   => 'center',
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .premium-hamburger-toggle' => 'justify-content: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'pa_mobile_menu_pos',
			array(
				'label'      => __( 'Menu Position', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::CHOOSE,
				'separator'  => 'before',
				'options'    => array(
					'left'   => array(
						'title' => __( 'Left', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'default'    => 'right',
				'toggle'     => false,
				'selectors'  => array(
					'{{WRAPPER}}.premium-ham-dropdown .premium-mobile-menu-container, {{WRAPPER}}.premium-nav-dropdown .premium-mobile-menu-container' => 'justify-content: {{VALUE}}',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'pa_nav_menu_layout',
							'value' => 'dropdown',
						),
						array(
							'relation' => 'and',
							'terms'    => array(
								array(
									'name'  => 'pa_mobile_menu_layout',
									'value' => 'dropdown',
								),
								array(
									'name'     => 'pa_nav_menu_layout',
									'operator' => 'in',
									'value'    => array( 'hor', 'ver' ),
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'pa_mobile_menu_align',
			array(
				'label'     => __( 'Menu Alignment', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					$align_left  => array(
						'title' => __( 'Left', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'     => array(
						'title' => __( 'Center', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-center',
					),
					$align_right => array(
						'title' => __( 'Right', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'flex-start',
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}}.premium-hamburger-menu .premium-main-mobile-menu > .premium-nav-menu-item > .premium-menu-link, {{WRAPPER}}.premium-nav-dropdown .premium-main-mobile-menu > .premium-nav-menu-item > .premium-menu-link, {{WRAPPER}}.premium-nav-slide .premium-main-mobile-menu > .premium-nav-menu-item > .premium-menu-link' => 'justify-content: {{VALUE}}',
				),
			)
		);

		$transform_sign = is_rtl() ? '' : '-';

		$this->add_responsive_control(
			'pa_ham_menu_width',
			array(
				'label'       => __( 'Toggle Menu Width', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'separator'   => 'before',
				'label_block' => true,
				'size_units'  => array( 'px', 'vw' ),
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
				),
				'selectors'   => array(
					'{{WRAPPER}}.premium-ham-dropdown .premium-main-mobile-menu, {{WRAPPER}}.premium-nav-dropdown .premium-main-mobile-menu' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.premium-ham-slide .premium-mobile-menu-outer-container, {{WRAPPER}}.premium-nav-slide .premium-mobile-menu-outer-container' => 'width: {{SIZE}}{{UNIT}}; transform:translateX(' . $transform_sign . '{{SIZE}}{{UNIT}} );',
				),
				'condition'   => array(
					'pa_toggle_full!' => 'yes',
				),
			)
		);

		$this->add_control(
			'pa_toggle_full',
			array(
				'label'       => __( 'Full Width', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'render_type' => 'template',
				'conditions'  => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'pa_nav_menu_layout',
							'value' => 'dropdown',
						),
						array(
							'relation' => 'and',
							'terms'    => array(
								array(
									'name'  => 'pa_mobile_menu_layout',
									'value' => 'dropdown',
								),
								array(
									'name'     => 'pa_nav_menu_layout',
									'operator' => 'in',
									'value'    => array( 'hor', 'ver' ),
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'pa_mobile_hide_icon',
			array(
				'label'        => __( 'Hide Items Icon', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'premium-hidden-icon-',
			)
		);

		$this->add_control(
			'pa_mobile_hide_badge',
			array(
				'label'        => __( 'Hide Items Badge', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'premium-hidden-badge-',
			)
		);

		$this->add_control(
			'close_after_click',
			array(
				'label'       => __( 'Close Menu After Click', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'render_type' => 'template',
			)
		);

		$this->add_control(
			'pa_disable_page_scroll',
			array(
				'label'        => __( 'Disable Page Scroll', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'premium-disable-scroll-',
				'description'  => __( 'Enable this option to disable page scroll when the slide menu is opened', 'premium-addons-for-elementor' ),
				'render_type'  => 'template',
				'conditions'   => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'pa_nav_menu_layout',
							'operator' => '===',
							'value'    => 'slide',
						),
						array(
							'name'     => 'pa_mobile_menu_layout',
							'operator' => '===',
							'value'    => 'slide',
						),
					),
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get Vertical toggler style.
	 *
	 * @access private
	 * @since 4.9.15
	 */
	private function get_ver_toggler_style() {

		$this->start_controls_section(
			'pa_ver_toggler_style_section',
			array(
				'label'     => __( 'Collapsed Menu Style', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'pa_ver_toggle_switcher' => 'yes',
					'pa_nav_menu_layout'     => 'ver',
				),
			)
		);

		$this->add_control(
			'pa_ver_title_heading',
			array(
				'label' => __( 'Title', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'pa_ver_title_typo',
				'selector' => '{{WRAPPER}} .premium-ver-toggler-txt',
			)
		);

		$this->add_control(
			'pa_ver_title_icon_size',
			array(
				'label'      => __( 'Icon Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-ver-title-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .premium-ver-title-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'pa_ver_toggle_main_icon[value]!' => '',
				),
			)
		);

		$this->start_controls_tabs( 'pa_ver_title_tabs' );

		$this->start_controls_tab(
			'pa_ver_title_tab_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'pa_ver_title_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-ver-toggler-txt' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pa_ver_title_icon_color',
			array(
				'label'     => __( 'Icon Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-ver-title-icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .premium-ver-title-icon svg, {{WRAPPER}} .premium-ver-title-icon svg path' => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'pa_ver_toggle_main_icon[value]!' => '',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pa_ver_title_tab_hov',
			array(
				'label' => __( 'Hover', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'pa_ver_title_color_hov',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-ver-toggler:hover .premium-ver-toggler-txt' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pa_ver_title_icon_color_hov',
			array(
				'label'     => __( 'Icon Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-ver-toggler:hover .premium-ver-title-icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .premium-ver-toggler:hover .premium-ver-title-icon svg, {{WRAPPER}} .premium-ver-toggler:hover .premium-ver-title-icon svg path' => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'pa_ver_toggle_main_icon[value]!' => '',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'pa_ver_toggle_heading',
			array(
				'label'      => __( 'Toggle Icon', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::HEADING,
				'separator'  => 'before',
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'pa_ver_toggle_toggle_icon[value]',
							'operator' => '!==',
							'value'    => '',
						),
						array(
							'name'     => 'pa_ver_toggle_close_icon[value]',
							'operator' => '!==',
							'value'    => '',
						),
					),
				),
			)
		);

		$this->add_control(
			'pa_ver_toggle_icon_size',
			array(
				'label'      => __( 'Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-ver-toggler-btn i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .premium-ver-toggler-btn svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'pa_ver_toggle_toggle_icon[value]',
							'operator' => '!==',
							'value'    => '',
						),
						array(
							'name'     => 'pa_ver_toggle_close_icon[value]',
							'operator' => '!==',
							'value'    => '',
						),
					),
				),
			)
		);

		$this->start_controls_tabs( 'pa_ver_toggle_icon_tabs' );

		$this->start_controls_tab(
			'pa_ver_toggle_tab_normal',
			array(
				'label'      => __( 'Normal', 'premium-addons-for-elementor' ),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'pa_ver_toggle_toggle_icon[value]',
							'operator' => '!==',
							'value'    => '',
						),
						array(
							'name'     => 'pa_ver_toggle_close_icon[value]',
							'operator' => '!==',
							'value'    => '',
						),
					),
				),
			)
		);

		$this->add_control(
			'pa_ver_toggle_icon_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-ver-toggler-btn i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .premium-ver-toggler-btn svg, {{WRAPPER}} .premium-ver-toggler-btn svg path' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pa_ver_toggle_tab_hov',
			array(
				'label'      => __( 'Hover', 'premium-addons-for-elementor' ),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'pa_ver_toggle_toggle_icon[value]',
							'operator' => '!==',
							'value'    => '',
						),
						array(
							'name'     => 'pa_ver_toggle_close_icon[value]',
							'operator' => '!==',
							'value'    => '',
						),
					),
				),
			)
		);

		$this->add_control(
			'pa_ver_toggle_icon_color_hov',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-ver-toggler:hover .premium-ver-toggler-btn i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .premium-ver-toggler:hover .premium-ver-toggler-btn svg,
					 {{WRAPPER}} .premium-ver-toggler:hover .premium-ver-toggler-btn svg path' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'pa_ver_container_heading',
			array(
				'label'     => __( 'Container', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->start_controls_tabs( 'pa_ver_toggler_tabs' );

		$this->start_controls_tab(
			'pa_ver_toggler_tab_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'pa_ver_toggler_bg',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-ver-toggler',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'pa_ver_toggler_shadow',
				'selector' => '{{WRAPPER}} .premium-ver-toggler',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pa_ver_toggler_tab_hov',
			array(
				'label' => __( 'Hover', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'pa_ver_toggler_bg_hov',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-ver-toggler:hover',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'pa_ver_toggler_shadow_hov',
				'selector' => '{{WRAPPER}} .premium-ver-toggler:hover',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'pa_ver_toggler_border',
				'selector'  => '{{WRAPPER}} .premium-ver-toggler',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'pa_ver_toggler_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-ver-toggler' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'pa_ver_toggler_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-ver-toggler' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get menu container style.
	 *
	 * @access private
	 * @since 4.9.3
	 */
	private function get_menu_container_style() {

		$this->start_controls_section(
			'premium_nav_style_section',
			array(
				'label'     => __( 'Desktop Menu Style', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'pa_nav_menu_layout' => array( 'hor', 'ver' ),
				),
			)
		);

		$this->add_responsive_control(
			'pa_nav_menu_height',
			array(
				'label'       => __( 'Height', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', 'em', '%', 'custom' ),
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}}.premium-nav-hor > .elementor-widget-container > .premium-nav-widget-container > .premium-ver-inner-container > .premium-nav-menu-container' => 'height: {{SIZE}}{{UNIT}};',
				),
				'condition'   => array(
					'pa_nav_menu_layout' => 'hor',
				),
			)
		);

		$this->add_responsive_control(
			'pa_nav_menu_width',
			array(
				'label'       => __( 'Width', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', '%', 'custom' ),
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
				),
				'label_block' => true,
				'selectors'   => array(
					// '{{WRAPPER}}.premium-nav-ver .premium-nav-menu-container, {{WRAPPER}}.premium-nav-ver .premium-ver-toggler' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.premium-nav-ver .premium-ver-inner-container' => 'width: {{SIZE}}{{UNIT}};',
				),
				'condition'   => array(
					'pa_nav_menu_layout' => 'ver',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'pa_nav_menu_shadow',
				'selector' => '{{WRAPPER}} .premium-nav-menu-container',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'pa_nav_menu_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-nav-menu-container',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pa_nav_menu_border',
				'selector' => '{{WRAPPER}} .premium-nav-menu-container',
			)
		);

		$this->add_control(
			'pa_nav_menu_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-nav-menu-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'pa_nav_menu_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-nav-menu-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get toggle menu container style.
	 *
	 * @access private
	 * @since 4.9.3
	 */
	private function get_toggle_menu_sytle() {

		$this->start_controls_section(
			'premium_toggle_mene_style_section',
			array(
				'label' => __( 'Expand/Slide Menu Style', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'pa_ham_toggle_style',
			array(
				'label' => __( 'Toggle Button', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->start_controls_tabs( 'pa_ham_toggle_style_tabs' );

		$this->start_controls_tab(
			'pa_ham_toggle_icon_tab',
			array(
				'label' => __( 'Icon', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_responsive_control(
			'pa_ham_toggle_icon_size',
			array(
				'label'       => __( 'Size', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', 'em', '%' ),
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}} .premium-hamburger-toggle i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .premium-hamburger-toggle svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'pa_ham_toggle_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-hamburger-toggle i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .premium-hamburger-toggle svg, {{WRAPPER}} .premium-hamburger-toggle svg path' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pa_ham_toggle_color_hover',
			array(
				'label'     => __( 'Hover Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-hamburger-toggle:hover i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .premium-hamburger-toggle:hover svg, {{WRAPPER}} .premium-hamburger-toggle:hover svg path' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pa_ham_toggle_label_tab',
			array(
				'label' => __( 'Text', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'pa_ham_toggle_txt_typo',
				'selector' => '{{WRAPPER}} .premium-hamburger-toggle .premium-toggle-text, {{WRAPPER}}.premium-ham-dropdown .premium-hamburger-toggle .premium-toggle-close',
			)
		);

		$this->add_control(
			'pa_ham_toggle_txt_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-hamburger-toggle .premium-toggle-text, {{WRAPPER}}.premium-ham-dropdown .premium-hamburger-toggle .premium-toggle-close' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pa_ham_toggle_txt_color_hover',
			array(
				'label'     => __( 'Hover Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-hamburger-toggle:hover .premium-toggle-text, {{WRAPPER}}.premium-ham-dropdown .premium-hamburger-toggle:hover .premium-toggle-close' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'pa_ham_toggle_txt_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-hamburger-toggle .premium-toggle-text, {{WRAPPER}}.premium-ham-dropdown .premium-hamburger-toggle .premium-toggle-close' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'pa_ham_toggle_bg',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'separator' => 'before',
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-hamburger-toggle' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pa_ham_toggle_bg_hover',
			array(
				'label'     => __( 'Hover Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-hamburger-toggle:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'pa_ham_toggle_shadow',
				'selector' => '{{WRAPPER}} .premium-hamburger-toggle',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pa_ham_toggle_border',
				'selector' => '{{WRAPPER}} .premium-hamburger-toggle',
			)
		);

		$this->add_control(
			'pa_ham_toggle_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-hamburger-toggle' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'pa_ham_toggle_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-hamburger-toggle' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pa_ham_toggle_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-hamburger-toggle' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'pa_ham_menu_style',
			array(
				'label'     => __( 'Toggle Menu', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'pa_ham_menu_item_color',
			array(
				'label'     => __( 'Menu Item Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-main-mobile-menu.premium-main-nav-menu > .premium-nav-menu-item > .premium-menu-link' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pa_ham_menu_overlay',
			array(
				'label'      => __( 'Overlay Color', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::COLOR,
				'selectors'  => array(
					'{{WRAPPER}} .premium-nav-slide-overlay' => 'background: {{VALUE}};',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'pa_nav_menu_layout',
							'value' => 'slide',
						),
						array(
							'relation' => 'and',
							'terms'    => array(
								array(
									'name'  => 'pa_mobile_menu_layout',
									'value' => 'slide',
								),
								array(
									'name'     => 'pa_nav_menu_layout',
									'operator' => 'in',
									'value'    => array( 'hor', 'ver' ),
								),
							),
						),
					),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'pa_ham_menu_shadow',
				'selector' => '{{WRAPPER}}.premium-ham-dropdown .premium-mobile-menu, {{WRAPPER}}.premium-nav-dropdown .premium-mobile-menu, {{WRAPPER}} .premium-mobile-menu-outer-container',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'pa_ham_menu_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}}.premium-ham-dropdown .premium-mobile-menu,
				 {{WRAPPER}}.premium-nav-dropdown .premium-mobile-menu,
				  {{WRAPPER}} .premium-mobile-menu-outer-container,
				  {{WRAPPER}} .premium-mobile-menu-container',

			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pa_ham_menu_border',
				'selector' => '{{WRAPPER}}.premium-ham-dropdown .premium-mobile-menu, {{WRAPPER}}.premium-nav-dropdown .premium-mobile-menu, {{WRAPPER}} .premium-mobile-menu-outer-container',
			)
		);

		$this->add_control(
			'pa_ham_menu_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}}.premium-ham-dropdown .premium-mobile-menu, {{WRAPPER}}.premium-nav-dropdown .premium-mobile-menu' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
				'condition'  => array(
					'pa_mobile_menu_layout' => 'dropdown',
				),
			)
		);

		$this->add_responsive_control(
			'pa_ham_menu_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}}.premium-ham-dropdown .premium-mobile-menu, {{WRAPPER}}.premium-nav-dropdown .premium-mobile-menu, {{WRAPPER}} .premium-mobile-menu-outer-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// Show close button style if desktop or mobile menu is set to slide.
		$close_btn_conditions = array(
			'relation' => 'or',
			'terms'    => array(
				array(
					'name'  => 'pa_nav_menu_layout',
					'value' => 'slide',
				),
				array(
					'name'  => 'pa_mobile_menu_layout',
					'value' => 'slide',
				),
			),
		);

		$this->start_controls_section(
			'ham_close_style_section',
			array(
				'label'      => __( 'Close Button Style', 'premium-addons-for-elementor' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => array_merge(
					$close_btn_conditions,
					array()
				),
			)
		);

		$this->start_controls_tabs( 'pa_ham_close_style_tabs' );

		$this->start_controls_tab(
			'pa_ham_close_icon_tab',
			array(
				'label' => __( 'Icon', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_responsive_control(
			'pa_ham_close_size',
			array(
				'label'       => __( 'Size', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', 'em', '%' ),
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}} .premium-mobile-menu-outer-container .premium-mobile-menu-close i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .premium-mobile-menu-outer-container .premium-mobile-menu-close svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'pa_ham_close_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-mobile-menu-outer-container .premium-mobile-menu-close i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .premium-mobile-menu-outer-container .premium-mobile-menu-close svg, {{WRAPPER}} .premium-mobile-menu-outer-container .premium-mobile-menu-close svg path' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pa_ham_close_color_hover',
			array(
				'label'     => __( 'Hover Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-mobile-menu-outer-container .premium-mobile-menu-close:hover i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .premium-mobile-menu-outer-container .premium-mobile-menu-close:hover svg, {{WRAPPER}} .premium-mobile-menu-outer-container .premium-mobile-menu-close:hover svg path' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pa_ham_close_txt_tab',
			array(
				'label' => __( 'Text', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'pa_ham_close_txt_typo',
				'selector' => '{{WRAPPER}} .premium-mobile-menu-outer-container .premium-mobile-menu-close .premium-toggle-close',
			)
		);

		$this->add_control(
			'pa_ham_close_txt_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-mobile-menu-outer-container .premium-mobile-menu-close .premium-toggle-close' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pa_ham_close_txt_color_hover',
			array(
				'label'     => __( 'Hover Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-mobile-menu-outer-container .premium-mobile-menu-close:hover .premium-toggle-close' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'pa_ham_close_txt_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-mobile-menu-outer-container .premium-mobile-menu-close .premium-toggle-close' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'pa_ham_close_bg',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .premium-mobile-menu-outer-container .premium-mobile-menu-close' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pa_ham_close_bg_hover',
			array(
				'label'     => __( 'Hover Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-mobile-menu-outer-container .premium-mobile-menu-close:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'pa_ham_close_shadow',
				'selector' => '{{WRAPPER}} .premium-mobile-menu-outer-container .premium-mobile-menu-close',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pa_ham_close_border',
				'selector' => '{{WRAPPER}} .premium-mobile-menu-outer-container .premium-mobile-menu-close',
			)
		);

		$this->add_control(
			'pa_ham_close_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-mobile-menu-outer-container .premium-mobile-menu-close' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'pa_ham_close_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-mobile-menu-outer-container .premium-mobile-menu-close' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pa_ham_close_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-mobile-menu-outer-container .premium-mobile-menu-close' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get Menu Item Extras.
	 * Adds Menu Items' Icon & Badge Style.
	 *
	 * @access private
	 * @since  4.9.4
	 */
	private function get_menu_item_extras() {

		$this->start_controls_section(
			'premium_nav_item_extra_style',
			array(
				'label' => __( 'Menu Item Icon & Badge', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->start_controls_tabs( 'pa_nav_items_extras' );

		$this->start_controls_tab(
			'pa_nav_item_icon_style',
			array(
				'label' => __( 'Icon', 'premium-addons-for-elementor' ),
			)
		);

		$left_order  = is_rtl() ? '1' : '0';
		$right_order = is_rtl() ? '0' : '1';
		$default     = is_rtl() ? $right_order : $left_order;

		$this->add_responsive_control(
			'pa_nav_item_icon_pos',
			array(
				'label'     => __( 'Position', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					$left_order  => array(
						'title' => __( 'Left', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-left',
					),
					$right_order => array(
						'title' => __( 'Right', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'default'   => $default,
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .premium-nav-menu-item > .premium-menu-link > .premium-item-icon' => 'order: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'menu_item_icon_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-nav-menu-item > .premium-menu-link > .premium-item-icon' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'menu_type' => 'custom',
				),
			)
		);

		$this->add_responsive_control(
			'pa_nav_item_icon_size',
			array(
				'label'       => __( 'Size', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'label_block' => true,
				'size_units'  => array( 'px' ),
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 300,
					),
				),
				'selectors'   => array(
					'{{WRAPPER}} .premium-nav-menu-item > .premium-menu-link > .premium-item-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .premium-nav-menu-item > .premium-menu-link > .premium-item-icon.dashicons, {{WRAPPER}} .premium-nav-menu-item > .premium-menu-link > img.premium-item-icon, {{WRAPPER}} .premium-nav-menu-item > .premium-menu-link > .premium-item-icon.premium-lottie-animation' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'menu_item_icon_back_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-nav-menu-item > .premium-menu-link > .premium-item-icon' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'menu_item_icon_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-nav-menu-item > .premium-menu-link > .premium-item-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'pa_nav_item_icon_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-nav-menu-item > .premium-menu-link > .premium-item-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pa_nav_item_icon_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-nav-menu-item > .premium-menu-link > .premium-item-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pa_nav_item_badge_style',
			array(
				'label' => __( 'Badge', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'pa_nav_item_badge_typo',
				'selector' => '{{WRAPPER}} .premium-nav-menu-item > .premium-menu-link > .premium-item-badge,
				{{WRAPPER}} .premium-ver-inner-container > div .premium-main-nav-menu > .premium-nav-menu-item > .premium-rn-badge,
				{{WRAPPER}} .premium-ver-inner-container > div .premium-main-nav-menu > .premium-nav-menu-item > .premium-menu-link > .premium-rn-badge',
			)
		);

		$this->add_control(
			'item_badge_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-nav-menu-item > .premium-menu-link > .premium-item-badge,
					{{WRAPPER}} .premium-ver-inner-container > div .premium-main-nav-menu > .premium-nav-menu-item > .premium-rn-badge,
					 {{WRAPPER}} .premium-ver-inner-container > div .premium-main-nav-menu > .premium-nav-menu-item > .premium-menu-link > .premium-rn-badge' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'menu_type' => 'custom',
				),
			)
		);

		$this->add_control(
			'item_badge_back_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-nav-menu-item > .premium-menu-link > .premium-item-badge,
					 {{WRAPPER}} .premium-ver-inner-container > div .premium-main-nav-menu > .premium-nav-menu-item > .premium-rn-badge,
					 {{WRAPPER}} .premium-ver-inner-container > div .premium-main-nav-menu > .premium-nav-menu-item > .premium-menu-link > .premium-rn-badge' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'menu_type' => 'custom',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pa_nav_item_badge_border',
				'selector' => '{{WRAPPER}} .premium-nav-menu-item > .premium-menu-link > .premium-item-badge,
				{{WRAPPER}} .premium-ver-inner-container > div .premium-main-nav-menu > .premium-nav-menu-item > .premium-rn-badge,
				{{WRAPPER}} .premium-ver-inner-container > div .premium-main-nav-menu > .premium-nav-menu-item > .premium-menu-link > .premium-rn-badge',
			)
		);

		$this->add_control(
			'pa_nav_item_badge_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-nav-menu-item > .premium-menu-link > .premium-item-badge,
					{{WRAPPER}} .premium-ver-inner-container > div .premium-main-nav-menu > .premium-nav-menu-item > .premium-rn-badge,
					{{WRAPPER}} .premium-ver-inner-container > div .premium-main-nav-menu > .premium-nav-menu-item > .premium-menu-link > .premium-rn-badge' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'pa_nav_item_badge_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-nav-menu-item > .premium-menu-link > .premium-item-badge,
					{{WRAPPER}} .premium-ver-inner-container > div .premium-main-nav-menu > .premium-nav-menu-item > .premium-rn-badge,
					 {{WRAPPER}} .premium-ver-inner-container > div .premium-main-nav-menu > .premium-nav-menu-item > .premium-menu-link > .premium-rn-badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pa_nav_item_badge_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-nav-menu-item > .premium-menu-link > .premium-item-badge,
					 {{WRAPPER}} .premium-ver-inner-container > div .premium-main-nav-menu > .premium-nav-menu-item > .premium-rn-badge,
					 {{WRAPPER}} .premium-ver-inner-container > div .premium-main-nav-menu > .premium-nav-menu-item > .premium-menu-link > .premium-rn-badge' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Get menu item style.
	 *
	 * @access private
	 * @since 4.9.3
	 */
	private function get_menu_item_style() {

		$this->start_controls_section(
			'premium_nav_item_style_section',
			array(
				'label' => __( 'Menu Item Style', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'pa_nav_item_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-main-nav-menu > .premium-nav-menu-item > .premium-menu-link',
			)
		);

		$this->add_responsive_control(
			'pa_nav_item_drop_icon_size',
			array(
				'label'      => __( 'Dropdown Icon Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-main-nav-menu > .premium-nav-menu-item > .premium-menu-link .premium-dropdown-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pa_pointer_thinkness',
			array(
				'label'      => __( 'Pointer Thickness', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-nav-pointer-underline .premium-menu-link-parent::after,
					{{WRAPPER}} .premium-nav-pointer-overline .premium-menu-link-parent::before,
					{{WRAPPER}} .premium-nav-pointer-double-line .premium-menu-link-parent::before,
					{{WRAPPER}} .premium-nav-pointer-double-line .premium-menu-link-parent::after' => 'height: {{SIZE}}px;',
					'{{WRAPPER}} .premium-nav-pointer-framed:not(.premium-nav-animation-draw):not(.premium-nav-animation-corners) .premium-menu-link-parent::before' => 'border-width: {{SIZE}}px;',
					'{{WRAPPER}} .premium-nav-pointer-framed.premium-nav-animation-draw .premium-menu-link-parent::before' => 'border-width: 0 0 {{SIZE}}px {{SIZE}}px;',
					'{{WRAPPER}} .premium-nav-pointer-framed.premium-nav-animation-draw .premium-menu-link-parent::after' => 'border-width: {{SIZE}}px {{SIZE}}px 0 0;',
					'{{WRAPPER}} .premium-nav-pointer-framed.premium-nav-animation-corners .premium-menu-link-parent::before' => 'border-width: {{SIZE}}px 0 0 {{SIZE}}px',
					'{{WRAPPER}} .premium-nav-pointer-framed.premium-nav-animation-corners .premium-menu-link-parent::after' => 'border-width: 0 {{SIZE}}px {{SIZE}}px 0',
				),
				'condition'  => array(
					'pointer!' => array( 'none', 'text', 'background' ),
				),

			)
		);

		$this->add_responsive_control(
			'pa_nav_item_drop_icon_margin',
			array(
				'label'      => __( 'Dropdown Icon Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-main-nav-menu > .premium-nav-menu-item > .premium-menu-link .premium-dropdown-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'pa_nav_items_styles' );

		$this->start_controls_tab(
			'pa_nav_item_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'pa_nav_item_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-main-nav-menu > .premium-nav-menu-item > .premium-menu-link' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pa_nav_item_drop_icon_color',
			array(
				'label'     => __( 'Dropdown Icon Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-main-nav-menu > .premium-nav-menu-item > .premium-menu-link .premium-dropdown-icon' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'pa_nav_item_bg',
				'types'    => array( 'classic', 'gradient' ),
				// 'selector' => '{{WRAPPER}} .premium-main-nav-menu > .premium-nav-menu-item',
				'selector' => '{{WRAPPER}} .premium-main-nav-menu > .premium-nav-menu-item > .premium-menu-link',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'pa_nav_item_shadow',
				'selector' => '{{WRAPPER}} .premium-main-nav-menu > .premium-nav-menu-item > .premium-menu-link',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pa_nav_item_border',
				'selector' => '{{WRAPPER}} .premium-main-nav-menu > .premium-nav-menu-item > .premium-menu-link',
			)
		);

		$this->add_control(
			'pa_nav_item_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-main-nav-menu > .premium-nav-menu-item > .premium-menu-link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'pa_nav_item_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-main-nav-menu > .premium-nav-menu-item > .premium-menu-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pa_nav_item_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-main-nav-menu > .premium-nav-menu-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pa_nav_item_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'pa_nav_item_color_hover',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-main-nav-menu > .premium-nav-menu-item:hover > .premium-menu-link' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pa_nav_item_drop_icon_hover',
			array(
				'label'     => __( 'Dropdown Icon Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-main-nav-menu > .premium-nav-menu-item:hover > .premium-menu-link .premium-dropdown-icon' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'pa_nav_item_bg_hover',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-main-nav-menu > .premium-nav-menu-item:hover > .premium-menu-link',
			)
		);

		$this->add_control(
			'menu_item_pointer_color_hover',
			array(
				'label'     => __( 'Item Hover Effect Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-nav-widget-container:not(.premium-nav-pointer-framed) .premium-menu-link-parent:before,
					{{WRAPPER}} .premium-nav-widget-container:not(.premium-nav-pointer-framed) .premium-menu-link-parent:after' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .premium-nav-pointer-framed .premium-menu-link-parent:before,
					{{WRAPPER}} .premium-nav-pointer-framed .premium-menu-link-parent:after' => 'border-color: {{VALUE}}',
				),
				'condition' => array(
					'pointer!' => array( 'none', 'text' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'pa_nav_item_shadow_hover',
				'selector' => '{{WRAPPER}} .premium-main-nav-menu > .premium-nav-menu-item:hover > .premium-menu-link',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pa_nav_item_border_hover',
				'selector' => '{{WRAPPER}} .premium-main-nav-menu > .premium-nav-menu-item:hover > .premium-menu-link',
			)
		);

		$this->add_control(
			'pa_nav_item_rad_hover',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-main-nav-menu > .premium-nav-menu-item:hover > .premium-menu-link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'pa_nav_item_padding_hover',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-main-nav-menu > .premium-nav-menu-item:hover > .premium-menu-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pa_nav_item_margin_hover',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-main-nav-menu > .premium-nav-menu-item:hover' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pa_nav_item_active',
			array(
				'label' => __( 'Active', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'pa_nav_item_color_active',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_ACCENT,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-main-nav-menu > .premium-active-item > .premium-menu-link' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pa_nav_item_drop_icon_active',
			array(
				'label'     => __( 'Dropdown Icon Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_ACCENT,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-main-nav-menu > .premium-active-item > .premium-menu-link .premium-dropdown-icon' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'pa_nav_item_bg_active',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-main-nav-menu > .premium-active-item > .premium-menu-link',
			)
		);

		$this->add_control(
			'menu_item_pointer_color_active',
			array(
				'label'     => __( 'Item Hover Effect Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-nav-widget-container:not(.premium-nav-pointer-framed) .premium-active-item .premium-menu-link-parent:before,
					{{WRAPPER}} .premium-nav-widget-container:not(.premium-nav-pointer-framed) .premium-active-item .premium-menu-link-parent:after' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .premium-nav-pointer-framed .premium-active-item .premium-menu-link-parent:before,
					{{WRAPPER}} .premium-nav-pointer-framed .premium-active-item .premium-menu-link-parent:after' => 'border-color: {{VALUE}}',
				),
				'condition' => array(
					'pointer!' => array( 'none', 'text' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'pa_nav_item_shadow_active',
				'selector' => '{{WRAPPER}} .premium-main-nav-menu > .premium-active-item',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pa_nav_item_border_active',
				'selector' => '{{WRAPPER}} .premium-main-nav-menu > .premium-active-item',
			)
		);

		$this->add_control(
			'pa_nav_item_rad_active',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-main-nav-menu > .premium-active-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'pa_nav_item_padding_active',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-main-nav-menu > .premium-active-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pa_nav_item_margin_active',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-main-nav-menu > .premium-active-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Get submenu container style.
	 *
	 * @access private
	 * @since 4.9.3
	 */
	private function get_submenu_container_style() {

		$this->start_controls_section(
			'premium_submenu_style_section',
			array(
				'label' => __( 'Submenu Style', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->start_controls_tabs( 'pa_sub_menus_style' );

		$this->start_controls_tab(
			'pa_sub_simple',
			array(
				'label' => __( 'Simple Panel', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_responsive_control(
			'pa_sub_minwidth',
			array(
				'label'       => __( 'Minimum Width', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'label_block' => true,
				'size_units'  => array( 'px', 'em', '%', 'custom' ),
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
				),
				'selectors'   => array(
					// '{{WRAPPER}} .premium-nav-menu-container .premium-sub-menu, {{WRAPPER}} .premium-mobile-menu-container .premium-sub-menu' => 'min-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .premium-mobile-menu-container .premium-sub-menu,
                    {{WRAPPER}}.premium-nav-ver .premium-nav-menu-item.menu-item-has-children .premium-sub-menu,
                    {{WRAPPER}}.premium-nav-hor .premium-nav-menu-item.menu-item-has-children .premium-sub-menu' => 'min-width: {{SIZE}}{{UNIT}};',
				),
				'condition'   => array(
					'menu_type!' => 'custom',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'pa_sub_shadow',
				'selector' => '{{WRAPPER}} .premium-nav-menu-container .premium-sub-menu, {{WRAPPER}} .premium-mobile-menu-container .premium-sub-menu',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'pa_sub_bg',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-nav-menu-container .premium-sub-menu, {{WRAPPER}} .premium-mobile-menu-container .premium-sub-menu',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pa_sub_border',
				'selector' => '{{WRAPPER}} .premium-nav-menu-container .premium-sub-menu, {{WRAPPER}} .premium-mobile-menu-container .premium-sub-menu',
			)
		);

		$this->add_control(
			'pa_sub_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-nav-menu-container .premium-sub-menu, {{WRAPPER}} .premium-mobile-menu-container .premium-sub-menu' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'pa_sub_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-nav-menu-container .premium-sub-menu, {{WRAPPER}} .premium-mobile-menu-container .premium-sub-menu' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pa_sub_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-nav-menu-container .premium-sub-menu, {{WRAPPER}} .premium-mobile-menu-container .premium-sub-menu' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pa_sub_mega',
			array(
				'label' => __( 'Mega Panel', 'premium-addons-for-elementor' ),
			)
		);

		$mega_pos = is_rtl() ? 'right' : 'left';

		$this->add_responsive_control(
			'pa_sub_mega_offset',
			array(
				'label'       => __( 'Offset', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'label_block' => true,
				'size_units'  => array( 'px', '%' ),
				'range'       => array(
					'px' => array(
						'min' => -1000,
						'max' => 2000,
					),
					'%'  => array(
						'min' => -100,
						'max' => 100,
					),
				),
				'selectors'   => array(
					'{{WRAPPER}}.premium-nav-hor .premium-nav-menu-container .premium-mega-content-container' => $mega_pos . ': {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.premium-nav-ver .premium-nav-menu-container .premium-mega-content-container' => 'top: {{SIZE}}{{UNIT}};',
				),
				'condition'   => array(
					'menu_type!' => 'custom',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'pa_sub_mega_shadow',
				'selector' => '{{WRAPPER}} .premium-nav-menu-container .premium-mega-content-container, {{WRAPPER}} .premium-mobile-menu-container .premium-mega-content-container',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'pa_sub_mega_bg',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-nav-menu-container .premium-mega-content-container, {{WRAPPER}} .premium-mobile-menu-container .premium-mega-content-container',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pa_sub_mega_border',
				'selector' => '{{WRAPPER}} .premium-nav-menu-container .premium-mega-content-container, {{WRAPPER}} .premium-mobile-menu-container .premium-mega-content-container',
			)
		);

		$this->add_control(
			'pa_sub_mega_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-nav-menu-container .premium-mega-content-container, {{WRAPPER}} .premium-mobile-menu-container .premium-mega-content-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'pa_sub_mega_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-nav-menu-container .premium-mega-content-container, {{WRAPPER}} .premium-mobile-menu-container .premium-mega-content-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pa_sub_mega_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-nav-menu-container .premium-mega-content-container, {{WRAPPER}} .premium-mobile-menu-container .premium-mega-content-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Get submenu item style.
	 *
	 * @access private
	 * @since 4.9.3
	 */
	private function get_submenu_item_style() {

		$this->start_controls_section(
			'premium_submenu_item_style_section',
			array(
				'label' => __( 'Submenu Item Style', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'pa_sub_item_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'selector' => '{{WRAPPER}} .premium-main-nav-menu .premium-sub-menu .premium-sub-menu-link',
			)
		);

		$this->add_responsive_control(
			'pa_sub_item_drop_icon_size',
			array(
				'label'      => __( 'Dropdown Icon Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-main-nav-menu .premium-sub-menu .premium-sub-menu-link .premium-dropdown-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'menu_type!' => 'custom',
				),
			)
		);

		$this->add_responsive_control(
			'pa_sub_item_drop_icon_margin',
			array(
				'label'      => __( 'Dropdown Icon Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-main-nav-menu .premium-sub-menu .premium-sub-menu-link .premium-dropdown-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'menu_type!' => 'custom',
				),
			)
		);

		$this->start_controls_tabs( 'pa_sub_items_styles' );

		$this->start_controls_tab(
			'pa_sub_item_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'pa_sub_item_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-main-nav-menu .premium-sub-menu .premium-sub-menu-link' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pa_sub_item_drop_icon_color',
			array(
				'label'     => __( 'Dropdown Icon Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-main-nav-menu .premium-sub-menu .premium-sub-menu-link .premium-dropdown-icon' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'menu_type!' => 'custom',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'pa_sub_item_bg',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-main-nav-menu .premium-sub-menu .premium-sub-menu-item',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'pa_sub_item_shadow',
				'selector' => '{{WRAPPER}} .premium-main-nav-menu .premium-sub-menu .premium-sub-menu-item',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pa_sub_item_border',
				'selector' => '{{WRAPPER}} .premium-main-nav-menu .premium-sub-menu .premium-sub-menu-item',
			)
		);

		$this->add_control(
			'pa_sub_item_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-main-nav-menu .premium-sub-menu .premium-sub-menu-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'pa_sub_item_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-main-nav-menu .premium-sub-menu .premium-sub-menu-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pa_sub_item_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-main-nav-menu .premium-sub-menu .premium-sub-menu-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pa_sub_item_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'pa_sub_item_color_hover',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fff',
				'selectors' => array(
					'{{WRAPPER}} .premium-main-nav-menu .premium-sub-menu-item:hover > .premium-sub-menu-link' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pa_sub_item_drop_icon_hover',
			array(
				'label'     => __( 'Dropdown Icon Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fff',
				'selectors' => array(
					'{{WRAPPER}} .premium-main-nav-menu .premium-sub-menu-item:hover > .premium-sub-menu-link .premium-dropdown-icon' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'menu_type!' => 'custom',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'           => 'pa_sub_item_bg_hover',
				'types'          => array( 'classic', 'gradient' ),
				'selector'       => '{{WRAPPER}}:not(.premium-hamburger-menu):not(.premium-nav-slide):not(.premium-nav-dropdown) .premium-main-nav-menu .premium-sub-menu .premium-sub-menu-item:hover,
									{{WRAPPER}}.premium-hamburger-menu .premium-main-nav-menu .premium-sub-menu > .premium-sub-menu-item:hover > .premium-sub-menu-link,
									{{WRAPPER}}.premium-nav-slide .premium-main-nav-menu .premium-sub-menu > .premium-sub-menu-item:hover > .premium-sub-menu-link,
									{{WRAPPER}}.premium-nav-dropdown .premium-main-nav-menu .premium-sub-menu > .premium-sub-menu-item:hover > .premium-sub-menu-link',
				'fields_options' => array(
					'background' => array(
						'default' => 'classic',
					),
					'color'      => array(
						'global' => array(
							'default' => Global_Colors::COLOR_SECONDARY,
						),
					),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'pa_sub_item_shadow_hover',
				'selector' => '{{WRAPPER}} .premium-main-nav-menu .premium-sub-menu .premium-sub-menu-item:hover',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pa_sub_item_border_hover',
				'selector' => '{{WRAPPER}} .premium-main-nav-menu .premium-sub-menu .premium-sub-menu-item:hover',
			)
		);

		$this->add_control(
			'pa_sub_item_rad_hover',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-main-nav-menu .premium-sub-menu .premium-sub-menu-item:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'pa_sub_item_padding_hover',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-main-nav-menu .premium-sub-menu .premium-sub-menu-item:hover' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pa_sub_item_margin_hover',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-main-nav-menu .premium-sub-menu .premium-sub-menu-item:hover' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pa_sub_item_active',
			array(
				'label' => __( 'Active', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'pa_sub_item_color_active',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-main-nav-menu .premium-sub-menu .premium-active-item .premium-sub-menu-link' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pa_sub_item_drop_icon_active',
			array(
				'label'     => __( 'Dropdown Icon Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-main-nav-menu .premium-sub-menu .premium-active-item .premium-sub-menu-link .premium-dropdown-icon' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'menu_type!' => 'custom',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'pa_sub_item_bg_active',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-main-nav-menu .premium-sub-menu .premium-active-item',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'pa_sub_item_shadow_active',
				'selector' => '{{WRAPPER}} .premium-main-nav-menu .premium-sub-menu .premium-active-item',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pa_sub_item_border_active',
				'selector' => '{{WRAPPER}} .premium-main-nav-menu .premium-sub-menu .premium-active-item',
			)
		);

		$this->add_control(
			'pa_sub_item_rad_active',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-main-nav-menu .premium-sub-menu .premium-active-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'pa_sub_item_padding_active',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-main-nav-menu .premium-sub-menu .premium-active-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pa_sub_item_margin_active',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-main-nav-menu .premium-sub-menu .premium-active-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Get Submenu Item Extras.
	 * Adds Submenu Items' Icon & Badge Style.
	 *
	 * @access private
	 * @since  4.9.4
	 */
	private function get_sub_menu_item_extras() {

		$this->start_controls_section(
			'premium_sub_extra_style',
			array(
				'label' => __( 'Submenu Item Icon & Badge', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->start_controls_tabs( 'pa_sub_items_extras' );

		$this->start_controls_tab(
			'pa_sub_icon_style',
			array(
				'label' => __( 'Icon', 'premium-addons-for-elementor' ),
			)
		);

		$left_order  = is_rtl() ? '1' : '0';
		$right_order = is_rtl() ? '0' : '1';
		$default     = is_rtl() ? $right_order : $left_order;

		$this->add_responsive_control(
			'pa_sub_icon_pos',
			array(
				'label'     => __( 'Position', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					$left_order  => array(
						'title' => __( 'Left', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-left',
					),
					$right_order => array(
						'title' => __( 'Right', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'default'   => $default,
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .premium-sub-menu-item .premium-sub-menu-link .premium-sub-item-icon' => 'order: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'sub_item_icon_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-sub-menu-item .premium-sub-menu-link .premium-sub-item-icon' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'menu_type' => 'custom',
				),
			)
		);

		$this->add_responsive_control(
			'pa_sub_icon_size',
			array(
				'label'       => __( 'Size', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'label_block' => true,
				'size_units'  => array( 'px' ),
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 300,
					),
				),
				'selectors'   => array(
					'{{WRAPPER}} .premium-sub-menu-item .premium-sub-menu-link .premium-sub-item-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .premium-sub-menu-item .premium-sub-menu-link .premium-sub-item-icon.dashicons, {{WRAPPER}} .premium-sub-menu-item .premium-sub-menu-link img.premium-sub-item-icon, {{WRAPPER}} .premium-sub-menu-item .premium-sub-menu-link .premium-sub-item-icon.premium-lottie-animation' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'sub_item_icon_back_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-sub-menu-item .premium-sub-menu-link .premium-sub-item-icon' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'sub_item_icon_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-sub-menu-item .premium-sub-menu-link .premium-sub-item-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'pa_sub_icon_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-sub-menu-item .premium-sub-menu-link .premium-sub-item-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pa_sub_icon_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-sub-menu-item .premium-sub-menu-link .premium-sub-item-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pa_sub_badge_style',
			array(
				'label' => __( 'Badge', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'pa_sub_badge_typo',
				'selector' => '{{WRAPPER}} .premium-sub-menu-item .premium-sub-menu-link .premium-sub-item-badge, {{WRAPPER}} .premium-sub-menu-item .premium-rn-badge, {{WRAPPER}} .premium-mega-content-container .premium-rn-badge',
			)
		);

		$this->add_control(
			'sub_item_badge_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-sub-menu-item .premium-sub-menu-link .premium-sub-item-badge, {{WRAPPER}} .premium-sub-menu-item .premium-rn-badge, {{WRAPPER}} .premium-mega-content-container .premium-rn-badge' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'menu_type' => 'custom',
				),
			)
		);

		$this->add_control(
			'sub_item_badge_back_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-sub-menu-item .premium-sub-menu-link .premium-sub-item-badge, {{WRAPPER}} .premium-sub-menu-item .premium-rn-badge, {{WRAPPER}} .premium-mega-content-container .premium-rn-badge' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'menu_type' => 'custom',
				),
			)
		);

		// TODO: check the all the badges CSS.
		$badge_pos = is_rtl() ? 'left' : 'right';

		$this->add_responsive_control(
			'pa_sub_badge_hor',
			array(
				'label'       => __( 'Horizontal Offset', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'label_block' => true,
				'size_units'  => array( 'px', '%' ),
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
				),
				'selectors'   => array(
					'{{WRAPPER}}:not(.premium-nav-ver) .premium-sub-menu-item .premium-sub-menu-link .premium-sub-item-badge, {{WRAPPER}}:not(.premium-nav-ver) .premium-sub-menu-item .premium-rn-badge, {{WRAPPER}} .premium-mega-content-container .premium-rn-badge' => $badge_pos . ' : {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.premium-nav-ver.premium-vertical-right .premium-sub-menu-item .premium-sub-menu-link .premium-sub-item-badge, {{WRAPPER}}.premium-nav-ver.premium-vertical-right .premium-sub-menu-item .premium-rn-badge' => 'right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.premium-nav-ver.premium-vertical-left .premium-sub-menu-item .premium-sub-menu-link .premium-sub-item-badge, {{WRAPPER}}.premium-nav-ver.premium-vertical-left .premium-sub-menu-item .premium-rn-badge' => 'left: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pa_sub_badge_ver',
			array(
				'label'       => __( 'Vertical Offset', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'label_block' => true,
				'size_units'  => array( 'px', '%' ),
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
				),
				'selectors'   => array(
					'{{WRAPPER}} .premium-sub-menu-item .premium-sub-menu-link .premium-sub-item-badge, {{WRAPPER}} .premium-sub-menu-item .premium-rn-badge, {{WRAPPER}} .premium-mega-content-container .premium-rn-badge' => 'top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pa_sub_badge_border',
				'selector' => '{{WRAPPER}} .premium-sub-menu-item .premium-sub-menu-link .premium-sub-item-badge, {{WRAPPER}} .premium-sub-menu-item .premium-rn-badge, {{WRAPPER}} .premium-mega-content-container .premium-rn-badge',
			)
		);

		$this->add_control(
			'pa_sub_badge_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-sub-menu-item .premium-sub-menu-link .premium-sub-item-badge, {{WRAPPER}} .premium-sub-menu-item .premium-rn-badge, {{WRAPPER}} .premium-mega-content-container .premium-rn-badge' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'pa_sub_badge_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-sub-menu-item .premium-sub-menu-link .premium-sub-item-badge,
					 {{WRAPPER}} .premium-sub-menu-item .premium-rn-badge,
					 {{WRAPPER}} .premium-mega-content-container .premium-rn-badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Render Nav Menu widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		$menu_type = $settings['menu_type'];

		$menu_id = 'wordpress_menu' === $menu_type ? $settings['pa_nav_menus'] : false;

		$papro_activated = apply_filters( 'papro_activated', false ) && version_compare( PREMIUM_PRO_ADDONS_VERSION, '2.8.9', '>' );

		$rn_badges_enabled = ( $papro_activated && 'yes' === $settings['rn_badge_enabled'] ) ? true : false;

		if ( 'wordpress_menu' === $menu_type ) {

			$is_valid = $this->is_valid_menu( $menu_id );

			if ( ! $is_valid ) {
				?>
					<div class="premium-error-notice">
						<?php echo esc_html( __( 'This is an empty menu. Please make sure your menu has items.', 'premium-addons-for-elementor' ) ); ?>
					</div>
				<?php

				return;
			}
		}

		$break_point = 'custom' === $settings['pa_mobile_menu_breakpoint'] ? $settings['pa_custom_breakpoint'] : $settings['pa_mobile_menu_breakpoint'];

		$break_point = '' === $break_point ? '1025' : $break_point;

		$stretch_dropdown = 'yes' === $settings['pa_toggle_full'];

		$close_after_click = 'yes' === $settings['close_after_click'];

		$is_click = 'click' === $settings['pa_ver_toggle_event'] && 'yes' !== $settings['pa_ver_toggle_open'];

		$is_hover = 'hover' === $settings['pa_ver_toggle_event'];

		if ( 'wordpress_menu' === $menu_type ) {

			$menu_list = $this->get_menu_list();

			if ( ! $menu_list ) {
				return;
			}
		}

		$div_end = '';

		$menu_settings = array(
			'breakpoint'      => (int) $break_point,
			'mobileLayout'    => $settings['pa_mobile_menu_layout'],
			'mainLayout'      => $settings['pa_nav_menu_layout'],
			'stretchDropdown' => $stretch_dropdown,
			'hoverEffect'     => $settings['sub_badge_hv_effects'],
			'submenuEvent'    => $settings['submenu_event'],
			'submenuTrigger'  => $settings['submenu_trigger'],
			'closeAfterClick' => $close_after_click,
		);

		if ( 'yes' === $settings['pa_sticky_switcher'] ) {

			$sticky_options = array(
				'targetId'  => $settings['pa_sticky_target'],
				'onScroll'  => 'yes' === $settings['pa_sticky_on_scroll'] ? true : false,
				'disableOn' => $settings['pa_sticky_disabled_on'],
			);

			$menu_settings['stickyOptions'] = $sticky_options;
		}

		if ( $rn_badges_enabled ) {

			$rn_badges_settings = apply_filters( 'pa_get_random_badges_settings', $settings );

			$menu_settings['rn_badges'] = $rn_badges_settings;
		}

		$is_edit_mode = \Elementor\Plugin::$instance->editor->is_edit_mode();
		$hidden_style = $is_edit_mode ? '' : 'visibility:hidden; opacity:0;';

		$this->add_render_attribute(
			'wrapper',
			array(
				'data-settings' => json_encode( $menu_settings ),
				'class'         => array(
					'premium-nav-widget-container',
					'premium-nav-pointer-' . $settings['pointer'],
				),
				'style'         => $hidden_style,
			)
		);

		if ( $stretch_dropdown ) {
			$this->add_render_attribute( 'wrapper', 'class', 'premium-stretch-dropdown' );
		}

		if ( 'yes' === $settings['pa_ver_toggle_switcher'] && ( $is_click || $is_hover ) ) {
			$this->add_render_attribute( 'wrapper', 'class', 'premium-ver-collapsed' );
		}

		switch ( $settings['pointer'] ) {
			case 'underline':
			case 'overline':
			case 'double-line':
				$this->add_render_attribute( 'wrapper', 'class', 'premium-nav-animation-' . $settings['animation_line'] );
				break;
			case 'framed':
				$this->add_render_attribute( 'wrapper', 'class', 'premium-nav-animation-' . $settings['animation_framed'] );
				break;
			case 'text':
				$this->add_render_attribute( 'wrapper', 'class', 'premium-nav-animation-' . $settings['animation_text'] );
				break;
			case 'background':
				$this->add_render_attribute( 'wrapper', 'class', 'premium-nav-animation-' . $settings['animation_background'] );
				break;
		}

		/**
		 * Hamburger Menu Button.
		 */
		?>
			<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'wrapper' ) ); ?>>
				<div class="premium-ver-inner-container">
					<div class="premium-hamburger-toggle premium-mobile-menu-icon" role="button" aria-label="Toggle Menu">
						<span class="premium-toggle-text">
							<?php
								Icons_Manager::render_icon( $settings['pa_mobile_toggle_icon'], array( 'aria-hidden' => 'true' ) );
								echo esc_html( $settings['pa_mobile_toggle_text'] );
							?>
						</span>
						<span class="premium-toggle-close">
							<?php
								Icons_Manager::render_icon( $settings['pa_mobile_close_icon'], array( 'aria-hidden' => 'true' ) );
								echo esc_html( $settings['pa_mobile_toggle_close'] );
							?>
						</span>
					</div>
					<?php

					if ( 'yes' === $settings['pa_ver_toggle_switcher'] ) {
						$this->add_vertical_toggler();
					}

					if ( 'wordpress_menu' === $menu_type ) {
						$args = array(
							'container'   => '',
							'menu'        => $menu_id,
							'menu_class'  => 'premium-nav-menu premium-main-nav-menu',
							'echo'        => false,
							'fallback_cb' => 'wp_page_menu',
							'walker'      => new Pa_Nav_Menu_Walker( $settings ),
						);

						$menu_html = wp_nav_menu( $args );

						if ( in_array( $settings['pa_nav_menu_layout'], array( 'hor', 'ver' ), true ) ) {
							?>
							<div class="premium-nav-menu-container premium-nav-default">
								<?php echo $menu_html; ?>
							</div>
							<?php
						}
					} else {
						?>
						<div class="premium-nav-menu-container">
							<ul class="premium-nav-menu premium-main-nav-menu">
								<?php
									$menu_html = $this->get_custom_menu();
									echo $menu_html;
								?>
							</ul>
						</div>
						<?php
					}

					if ( 'slide' === $settings['pa_mobile_menu_layout'] || 'slide' === $settings['pa_nav_menu_layout'] ) {
						$div_end = '</div>';
						?>
						<div class="premium-nav-slide-overlay"></div>
						<div class="premium-mobile-menu-outer-container">
							<div class="premium-mobile-menu-close" role="button" aria-label="Close Menu">
								<?php Icons_Manager::render_icon( $settings['pa_mobile_close_icon'], array( 'aria-hidden' => 'true' ) ); ?>
								<span class="premium-toggle-close"><?php echo esc_html( $settings['pa_mobile_toggle_close'] ); ?></span>
						</div>
						<?php
						/**
						 * @param int|bool $menu_id WordPress menu id | false if it's a custom menu.
						 * @param array $settings  menu settings.
						 */
						do_action( 'pa_slide_menu_top_template', $menu_id, $settings );
					}

					if ( 'wordpress_menu' === $menu_type ) {
						?>
							<div class="premium-mobile-menu-container">
								<?php echo $this->mobile_menu_filter( $menu_html, $menu_id ); ?>
							</div>
							<?php

					} else {
						?>
						<div class="premium-mobile-menu-container">
							<ul class="premium-mobile-menu premium-main-mobile-menu premium-main-nav-menu">
								<?php
									echo $menu_html;
								?>
							</ul>
						</div>
						<?php
					}

					if ( 'slide' === $settings['pa_mobile_menu_layout'] || 'slide' === $settings['pa_nav_menu_layout'] ) {
						do_action( 'pa_slide_menu_bottom_template', $menu_id, $settings );
					}

					echo wp_kses_post( $div_end );
					?>
				</div>
			</div>
		<?php

	}

	/**
	 * Add Collapsed Menu.
	 *
	 * @access private
	 * @since 4.9.15
	 */
	private function add_vertical_toggler() {

		$settings = $this->get_settings_for_display();
		$id       = $this->get_id();

		?>
		<div class="premium-ver-toggler premium-ver-toggler-<?php echo esc_attr( $id ); ?>">
			<div class="premium-ver-toggler-title">
				<span class="premium-ver-title-icon">
					<?php Icons_Manager::render_icon( $settings['pa_ver_toggle_main_icon'], array( 'aria-hidden' => 'true' ) ); ?>
				</span>
				<span class="premium-ver-toggler-txt">
					<?php echo esc_html( $settings['pa_ver_toggle_txt'] ); ?>
				</span>
			</div>
			<div class="premium-ver-toggler-btn">
			<span class="premium-ver-open">
				<?php Icons_Manager::render_icon( $settings['pa_ver_toggle_toggle_icon'], array( 'aria-hidden' => 'true' ) ); ?>
			</span>
			<?php if ( 'always' !== $settings['pa_ver_toggle_event'] ) : ?>
			<span class="premium-ver-close">
				<?php Icons_Manager::render_icon( $settings['pa_ver_toggle_close_icon'], array( 'aria-hidden' => 'true' ) ); ?>
			</span>
			<?php endif; ?>
			</div>
		</div>
		<?php

	}

	/**
	 * Is Valid Menu.
	 *
	 * @access private
	 * @since 4.9.10
	 *
	 * @param string|int $id  menu id.
	 *
	 * @return bool   true if the menu has items.
	 */
	private function is_valid_menu( $id ) {

		$is_valid = false;

		$item_count = wp_get_nav_menu_object( $id )->count;

		if ( 0 < $item_count ) {
			$is_valid = true;
		}

		return $is_valid;
	}

	private function mobile_menu_filter( $menu_html, $menu_id ) {

		// Increment the mobile menu id & change its classes to mobile menu classes.
		$slug    = 'menu-' . wp_get_nav_menu_object( $menu_id )->slug;
		$search  = array( 'id="' . $slug . '"', 'class="premium-nav-menu premium-main-nav-menu"' );
		$replace = array( 'id="' . $slug . '-1"', 'class="premium-mobile-menu premium-main-mobile-menu premium-main-nav-menu"' );

		return str_replace( $search, $replace, $menu_html );
	}

	/**
	 * Get Custom Menu.
	 *
	 * @access private
	 * @since 4.9.4
	 */
	private function get_custom_menu() {

		$settings = $this->get_settings_for_display();

		$papro_activated = apply_filters( 'papro_activated', false );

		$badge_effect = $settings['sub_badge_hv_effects'];

		if ( ! $papro_activated ) {
			return;
		}

		$menu_items = $settings['menu_items'];

		$is_sub_menu = false;
		$i           = 0;
		$is_child    = false;
		$is_link     = false;
		$html_output = '';

		foreach ( $menu_items as $index => $item ) {

			$item_link = $this->get_repeater_setting_key( 'link', 'menu_items', $index );

			if ( ! empty( $item['link']['url'] ) ) {

				$this->add_link_attributes( $item_link, $item['link'] );
			}

			$this->add_render_attribute(
				'menu-item-' . $index,
				array(
					// 'id'    => 'premium-nav-menu-item-' . $item['_id'],
					'class' => array(
						'menu-item',
						'premium-nav-menu-item',
						'elementor-repeater',
						'elementor-repeater-item-' . $item['_id'],
					),
				)
			);

			if ( 'submenu' === $item['item_type'] ) {

				if ( 'link' === $item['menu_content_type'] ) {

					// If no submenu items was rendered before.
					if ( false === $is_child ) {
						$html_output .= "<ul class='premium-sub-menu'>";
						$is_link      = true;
					}

					$this->add_render_attribute( 'menu-item-' . $index, 'class', 'premium-sub-menu-item' );

					if ( 'yes' === $item['badge_switcher'] ) {
						$this->add_render_attribute( 'menu-item-' . $index, 'class', 'has-pa-badge' );

						if ( '' !== $badge_effect ) {
							$this->add_render_attribute( 'menu-item-' . $index, 'class', 'premium-badge-' . $badge_effect );
						}
					}

					$html_output .= '<li ' . $this->get_render_attribute_string( 'menu-item-' . $index ) . '>';

					$html_output .= '<a ' . $this->get_render_attribute_string( $item_link ) . " class='premium-menu-link premium-sub-menu-link'>";

					$html_output .= $this->get_icon_html( $item, 'sub-' );

					$html_output .= $item['text'];

					$html_output .= $this->get_badge_html( $item, 'sub-' );

					$html_output .= '</a>';
					$html_output .= '</li>';
				} else {

					$this->add_render_attribute(
						'menu-content-item-' . $item['_id'],
						array(
							// 'id'    => 'premium-mega-content-' . $item['_id'],
							'class' => 'premium-mega-content-container',
						)
					);

					if ( 'yes' === $item['section_position'] ) {
						$this->add_render_attribute( 'menu-content-item-' . $item['_id'], 'class', 'premium-mega-content-centered' );
					}

					$html_output .= '<div ' . $this->get_render_attribute_string( 'menu-content-item-' . $item['_id'] ) . '>';

					$temp_id      = empty( $item['submenu_item'] ) ? $item['live_temp_content'] : $item['submenu_item'];
					$html_output .= $this->getTemplateInstance()->get_template_content( $temp_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					$html_output .= '</div>';

				}

				$is_child    = true;
				$is_sub_menu = true;

			} else {

				$next_item_exists   = array_key_exists( $index + 1, $menu_items );
				$next_item_is_child = $next_item_exists && 'submenu' === $menu_items[ $index + 1 ]['item_type'];

				if ( $next_item_is_child ) {
					$this->add_render_attribute( 'menu-item-' . $index, 'class', 'menu-item-has-children' );
				}

				if ( 'yes' === $item['badge_switcher'] ) {
					$this->add_render_attribute( 'menu-item-' . $index, 'class', 'has-pa-badge' );
				}

				if ( 'yes' === $item['section_full_width'] ) {
					$this->add_render_attribute( 'menu-item-' . $index, 'data-full-width', 'true' );
				}

				if ( $next_item_exists ) {
					if ( 'submenu' === $menu_items[ $index + 1 ]['item_type'] && 'custom_content' === $menu_items[ $index + 1 ]['menu_content_type'] ) {
						$this->add_render_attribute( 'menu-item-' . $index, 'class', 'premium-mega-nav-item' );
					}
				}

				$is_child = false;

				// If we need to create a new main item.
				if ( true === $is_sub_menu ) {
					$is_sub_menu = false;

					if ( $is_link ) {
						$html_output .= '</ul>';
						$is_link      = false;
					}

					$html_output .= '</li>';
				}

				$html_output .= '<li ' . $this->get_render_attribute_string( 'menu-item-' . $index ) . '>';

				$html_output .= '<a ' . $this->get_render_attribute_string( $item_link ) . " class='premium-menu-link premium-menu-link-parent'>";

					$html_output .= $this->get_icon_html( $item );

					$html_output .= $item['text'];

				if ( array_key_exists( $index + 1, $menu_items ) ) {
					$has_icon = ! empty( $settings['submenu_icon']['value'] );

					if ( 'submenu' === $menu_items[ $index + 1 ]['item_type'] && $has_icon ) {
						$icon_class   = 'premium-dropdown-icon ' . $settings['submenu_icon']['value'];
						$html_output .= sprintf( '<i class="%1$s"></i>', $icon_class );
					}
				}

				$html_output .= $this->get_badge_html( $item );

				$html_output .= '</a>';

			}

			?>

			<?php

		}

		return $html_output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Get Icon HTML.
	 *
	 * @access private
	 * @since 4.9.4
	 *
	 * @param array  $item  repeater item.
	 * @param string $type type.
	 *
	 * @return string
	 */
	private function get_icon_html( $item, $type = '' ) {

		$html = '';

		if ( 'yes' !== $item['icon_switcher'] ) {
			return '';
		}

		$class = 'premium-' . $type . 'item-icon ';

		if ( 'icon' === $item['icon_type'] ) {

			$icon_class = $class . $item['item_icon']['value'];
			$html      .= sprintf( '<i class="%1$s"></i>', $icon_class );

		} elseif ( 'image' === $item['icon_type'] ) {

			$html .= '<img class="' . $class . '" src="' . $item['item_image']['url'] . '" alt="' . Control_Media::get_image_alt( $item['item_image'] ) . '">';

		} else {

			$html .= '<div class="premium-lottie-animation ' . $class . '" data-lottie-url="' . $item['lottie_url'] . '" data-lottie-loop="true"></div>';

		}

		return $html;

	}

	/**
	 * Get Badge HTML.
	 *
	 * @access private
	 * @since 4.9.4
	 *
	 * @param array  $item  repeater item.
	 * @param string $type type.
	 *
	 * @return string
	 */
	private function get_badge_html( $item, $type = '' ) {

		if ( 'yes' !== $item['badge_switcher'] ) {
			return '';
		}

		$class = 'premium-' . $type . 'item-badge';

		$html = '<span class="' . $class . '">' . wp_kses_post( $item['badge_text'] ) . '</span>';

		return $html;

	}
}
