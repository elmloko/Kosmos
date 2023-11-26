<?php
/**
 * Premium Lottie Animations.
 */

namespace PremiumAddons\Widgets;

// Elementor Classes.
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Box_Shadow;

// PremiumAddons Classes.
use PremiumAddons\Includes\Helper_Functions;
use PremiumAddons\Includes\Premium_Template_Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

/**
 * Class Premium_Lottie
 */
class Premium_Lottie extends Widget_Base {

	/**
	 * Retrieve Widget Name.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_name() {
		return 'premium-lottie';
	}

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
	 * Retrieve Widget Dependent CSS.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array CSS style handles.
	 */
	public function get_style_depends() {
		return array(
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
			'pa-tilt',
			'lottie-js',
		);
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return __( 'Lottie Animations', 'premium-addons-for-elementor' );
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
		return 'pa-lottie-animations';
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
	 * Register Testimonials controls.
	 *
	 * @since 3.20.0
	 * @access protected
	 */
	protected function register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		$this->start_controls_section(
			'section_general_settings',
			array(
				'label' => __( 'General Settings', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'source',
			array(
				'label'   => __( 'File Source', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'url'  => __( 'External URL', 'premium-addons-for-elementor' ),
					'file' => __( 'Media File', 'premium-addons-for-elementor' ),
				),
				'default' => 'url',
			)
		);

		$this->add_control(
			'lottie_url',
			array(
				'label'       => __( 'Animation JSON URL', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'description' => 'Get JSON code URL from <a href="https://lottiefiles.com/" target="_blank">here</a>',
				'label_block' => true,
				'condition'   => array(
					'source' => 'url',
				),
			)
		);

		$this->add_control(
			'lottie_file',
			array(
				'label'              => __( 'Upload JSON File', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::MEDIA,
				'media_type'         => 'application/json',
				'frontend_available' => true,
				'condition'          => array(
					'source' => 'file',
				),
			)
		);

		$this->add_control(
			'lottie_loop',
			array(
				'label'        => __( 'Loop', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'default'      => 'true',
			)
		);

		$this->add_control(
			'lottie_reverse',
			array(
				'label'        => __( 'Reverse', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
			)
		);

		$this->add_control(
			'lottie_speed',
			array(
				'label'   => __( 'Animation Speed', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 1,
				'min'     => 0.1,
				'max'     => 3,
				'step'    => 0.1,
			)
		);

		$this->add_control(
			'trigger',
			array(
				'label'              => __( 'Trigger', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::SELECT,
				'options'            => array(
					'none'     => __( 'None', 'premium-addons-for-elementor' ),
					'viewport' => __( 'Viewport', 'premium-addons-for-elementor' ),
					'hover'    => __( 'Hover', 'premium-addons-for-elementor' ),
					'scroll'   => __( 'Scroll', 'premium-addons-for-elementor' ),
					'click'   => __( 'Click', 'premium-addons-for-elementor' ),
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'hover_leave_reverse',
			array(
				'label'        => __( 'Reset on Mouse Leave', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'condition'    => array(
					'trigger'         => 'hover',
					'lottie_reverse!' => 'true',
				),
			)
		);

		$this->add_control(
			'lottie_hover',
			array(
				'label'        => __( 'Play on Hover', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::HIDDEN,
				'return_value' => 'true',
			)
		);

		$this->add_control(
			'animate_on_scroll',
			array(
				'label'        => __( 'Play On Scroll', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::HIDDEN,
				'return_value' => 'true',
			)
		);

		$this->add_control(
			'animate_speed',
			array(
				'label'     => __( 'Speed', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 4,
				),
				'range'     => array(
					'px' => array(
						'max'  => 10,
						'step' => 0.1,
					),
				),
				'condition' => array(
					'trigger'         => 'scroll',
					'lottie_reverse!' => 'true',
				),
			)
		);

		$this->add_control(
			'animate_view',
			array(
				'label'     => __( 'Viewport', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'sizes' => array(
						'start' => 0,
						'end'   => 100,
					),
					'unit'  => '%',
				),
				'labels'    => array(
					__( 'Bottom', 'premium-addons-for-elementor' ),
					__( 'Top', 'premium-addons-for-elementor' ),
				),
				'scales'    => 1,
				'handles'   => 'range',
				'condition' => array(
					'trigger'         => array( 'scroll', 'viewport' ),
					'lottie_reverse!' => 'true',
				),
			)
		);

		$this->add_responsive_control(
			'animation_size',
			array(
				'label'      => __( 'Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 200,
				),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 800,
					),
					'em' => array(
						'min' => 1,
						'max' => 30,
					),
				),
				'separator'  => 'before',
				'selectors'  => array(
					'{{WRAPPER}}.premium-lottie-canvas .premium-lottie-animation, {{WRAPPER}}.premium-lottie-svg svg'    => 'width: {{SIZE}}{{UNIT}} !important;',
				),
			)
		);

		$this->add_responsive_control(
			'lottie_rotate',
			array(
				'label'       => __( 'Rotate (degrees)', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'description' => __( 'Set rotation value in degrees', 'premium-addons-for-elementor' ),
				'range'       => array(
					'px' => array(
						'min' => -180,
						'max' => 180,
					),
				),
				'default'     => array(
					'size' => 0,
				),
				'selectors'   => array(
					'{{WRAPPER}} .premium-lottie-animation' => 'transform: rotate({{SIZE}}deg)',
				),
			)
		);

		$this->add_responsive_control(
			'animation_align',
			array(
				'label'     => __( 'Alignment', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => __( 'Left', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'center',
				'toggle'    => false,
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-container' => 'text-align: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'link_switcher',
			array(
				'label' => __( 'Link', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'link_selection',
			array(
				'label'       => __( 'Link Type', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'url'  => __( 'URL', 'premium-addons-for-elementor' ),
					'link' => __( 'Existing Page', 'premium-addons-for-elementor' ),
				),
				'default'     => 'url',
				'label_block' => true,
				'condition'   => array(
					'link_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'link',
			array(
				'label'       => __( 'Link', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => array( 'active' => true ),
				'default'     => array(
					'url' => '#',
				),
				'placeholder' => 'https://premiumaddons.com/',
				'label_block' => true,
				'separator'   => 'after',
				'condition'   => array(
					'link_switcher'  => 'yes',
					'link_selection' => 'url',
				),
			)
		);

		$this->add_control(
			'existing_link',
			array(
				'label'       => __( 'Existing Page', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => $this->getTemplateInstance()->get_all_posts(),
				'multiple'    => false,
				'label_block' => true,
				'condition'   => array(
					'link_switcher'  => 'yes',
					'link_selection' => 'link',
				),
			)
		);

		$this->add_control(
			'lottie_renderer',
			array(
				'label'        => __( 'Render As', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SELECT,
				'options'      => array(
					'svg'    => __( 'SVG', 'premium-addons-for-elementor' ),
					'canvas' => __( 'Canvas', 'premium-addons-for-elementor' ),
				),
				'default'      => 'svg',
				'prefix_class' => 'premium-lottie-',
				'render_type'  => 'template',
				'label_block'  => true,
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'render_notice',
			array(
				'raw'             => __( 'Set render type to canvas if you\'re having performance issues on the page.', 'premium-addons-for-elemeentor' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$this->add_control(
			'mouse_tilt',
			array(
				'label'        => __( 'Enable Mouse Tilt', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
			)
		);

		$this->add_control(
			'mouse_tilt_rev',
			array(
				'label'        => __( 'Reverse', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'condition'    => array(
					'mouse_tilt' => 'true',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_pa_docs',
			array(
				'label' => __( 'Helpful Documentations', 'premium-addons-for-elementor' ),
			)
		);

		$docs = array(
			'https://www.youtube.com/watch?v=0QWzUpF57dw' => __( 'Check the video tutorial »', 'premium-addons-for-elementor' ),
			'https://premiumaddons.com/docs/lottie-animations-widget-tutorial' => __( 'Check the documentation article »', 'premium-addons-for-elementor' ),
			'https://premiumaddons.com/docs/how-to-speed-up-elementor-pages-with-many-lottie-animations' => __( 'How to speed up Elementor pages with many Lottie animations »', 'premium-addons-for-elementor' ),
			'https://premiumaddons.com/docs/customize-elementor-lottie-widget/' => __( 'How to Customize Lottie Animations »', 'premium-addons-for-elementor' ),
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

		$this->start_controls_section(
			'section_animation_style',
			array(
				'label' => __( 'Animation', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->start_controls_tabs( 'tabs_lottie' );

		$this->start_controls_tab(
			'tab_lottie_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'lottie_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-lottie-animation'  => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'opacity',
			array(
				'label'     => __( 'Opacity', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'max'  => 1,
						'min'  => 0.10,
						'step' => 0.01,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-lottie-animation' => 'opacity: {{SIZE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'     => 'css_filters',
				'selector' => '{{WRAPPER}} .premium-lottie-animation',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_lottie_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'lottie_hover_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-lottie-animation:hover'  => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'hover_opacity',
			array(
				'label'     => __( 'Opacity', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'max'  => 1,
						'min'  => 0.10,
						'step' => 0.01,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-lottie-animation:hover' => 'opacity: {{SIZE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'     => 'hover_css_filters',
				'selector' => '{{WRAPPER}} .premium-lottie-animation:hover',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'lottie_border',
				'selector'  => '{{WRAPPER}} .premium-lottie-animation',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'lottie_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-lottie-animation' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
				'condition'  => array(
					'lottie_adv_radius!' => 'yes',
				),
			)
		);

		$this->add_control(
			'lottie_adv_radius',
			array(
				'label'       => __( 'Advanced Border Radius', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Apply custom radius values. Get the radius value from ', 'premium-addons-for-elementor' ) . '<a href="https://9elements.github.io/fancy-border-radius/" target="_blank">here</a>',
			)
		);

		$this->add_control(
			'lottie_adv_radius_value',
			array(
				'label'     => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'selectors' => array(
					'{{WRAPPER}} .premium-lottie-animation' => 'border-radius: {{VALUE}};',
				),
				'condition' => array(
					'lottie_adv_radius' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'animation_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-lottie-animation' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Render Lottie Animations output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 3.20.2
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		$anim_url = 'url' === $settings['source'] ? $settings['lottie_url'] : $settings['lottie_file']['url'];

		if ( empty( $anim_url ) ) {
			return;
		}

		if ( '' !== $settings['trigger'] ) {
			$settings['lottie_hover']      = false;
			$settings['animate_on_scroll'] = false;
		}

		$this->add_render_attribute(
			'lottie',
			array(
				'class'               => 'premium-lottie-animation',
				'data-lottie-url'     => $anim_url,
				'data-lottie-loop'    => $settings['lottie_loop'],
				'data-lottie-reverse' => $settings['lottie_reverse'],
				'data-lottie-hover'   => $settings['lottie_hover'] || 'hover' === $settings['trigger'],
				'data-lottie-speed'   => $settings['lottie_speed'],
				'data-lottie-render'  => $settings['lottie_renderer'],
			)
		);

		if ( 'hover' === $settings['trigger'] && 'true' === $settings['hover_leave_reverse'] ) {
			$this->add_render_attribute( 'lottie', 'data-lottie-reset', 'true' );
		}

		if ( $settings['animate_on_scroll'] || 'scroll' === $settings['trigger'] ) {

			$this->add_render_attribute(
				'lottie',
				array(
					'class'              => 'premium-lottie-scroll',
					'data-lottie-scroll' => 'true',
					'data-scroll-start'  => isset( $settings['animate_view']['sizes']['start'] ) ? $settings['animate_view']['sizes']['start'] : '0',
					'data-scroll-end'    => isset( $settings['animate_view']['sizes']['end'] ) ? $settings['animate_view']['sizes']['end'] : '100',
					'data-scroll-speed'  => $settings['animate_speed']['size'],
				)
			);

		} elseif ( 'viewport' === $settings['trigger'] ) {
			$this->add_render_attribute(
				'lottie',
				array(
					'data-lottie-viewport' => 'true',
					'data-scroll-start'    => $settings['animate_view']['sizes']['start'],
					'data-scroll-end'      => $settings['animate_view']['sizes']['end'],
				)
			);
		} elseif ( 'click' === $settings['trigger'] ) {
			$this->add_render_attribute(
				'lottie',
				array(
					'data-lottie-click' => 'true',
				)
			);
		}

		if ( 'yes' === $settings['link_switcher'] ) {

			if ( 'url' === $settings['link_selection'] ) {
				$this->add_link_attributes( 'link', $settings['link'] );
			} else {
				$this->add_render_attribute( 'link', 'href', get_permalink( $settings['existing_link'] ) );
			}
		}

		if ( 'true' === $settings['mouse_tilt'] ) {
			$this->add_render_attribute( 'lottie', 'data-box-tilt', 'true' );
			if ( 'true' === $settings['mouse_tilt_rev'] ) {
				$this->add_render_attribute( 'lottie', 'data-box-tilt-reverse', 'true' );
			}
		}

		?>

		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'lottie' ) ); ?>>
			<?php if ( 'yes' === $settings['link_switcher'] ) : ?>
				<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'link' ) ); ?>></a>
			<?php endif; ?>
		</div>

		<?php
	}

	/**
	 * Render Testimonials widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 3.20.2
	 * @access protected
	 */
	protected function content_template() {

		?>

		<#

		var anim_url = null;

		if( 'url' === settings.source ) {
			anim_url = settings.lottie_url
		} else if( settings.lottie_file ) {
			anim_url = settings.lottie_file.url;
		}

		if( ! anim_url )
			return;

		if( '' !== settings.trigger ) {
			settings.lottie_hover = false;
			settings.animate_on_scroll = false;
		}

		view.addRenderAttribute( 'lottie', {
			'class': 'premium-lottie-animation',
			'data-lottie-url': anim_url,
			'data-lottie-loop': settings.lottie_loop,
			'data-lottie-reverse': settings.lottie_reverse,
			'data-lottie-hover': settings.lottie_hover || 'hover' === settings.trigger,
			'data-lottie-speed': settings.lottie_speed,
			'data-lottie-render': settings.lottie_renderer
		});

		if( 'hover' === settings.trigger && 'true' === settings.hover_leave_reverse ) {
			view.addRenderAttribute( 'lottie', 'data-lottie-reset', 'true' );
		}

		if( settings.animate_on_scroll || 'scroll' === settings.trigger ) {

			view.addRenderAttribute( 'lottie', {
				'class': 'premium-lottie-scroll',
				'data-lottie-scroll': 'true',
				'data-scroll-start': settings.animate_view.sizes.start,
				'data-scroll-end': settings.animate_view.sizes.end,
				'data-scroll-speed': settings.animate_speed.size,
			});

		} else if( 'viewport' === settings.trigger ) {

			view.addRenderAttribute( 'lottie', {
				'data-lottie-viewport': 'true',
				'data-scroll-start': settings.animate_view.sizes.start,
				'data-scroll-end': settings.animate_view.sizes.end,
			});

		} else if ( 'click' === settings.trigger ) {

			view.addRenderAttribute( 'lottie', {
				'data-lottie-click': 'true',
			});
		}

		if( 'yes' === settings.link_switcher ) {

			var button_url = '';

			if( settings.link_selection === 'url' ) {
				button_url = settings.link.url;
			} else {
				button_url = settings.existing_link;
			}

			view.addRenderAttribute( 'link', 'href', button_url );

		}

		if ( 'true' === settings.mouse_tilt ) {
			view.addRenderAttribute( 'lottie', 'data-box-tilt', 'true' );
			if ( 'true' === settings.mouse_tilt_rev ) {
				view.addRenderAttribute( 'lottie', 'data-box-tilt-reverse', 'true' );
			}
		}


	#>

		<div {{{ view.getRenderAttributeString('lottie') }}}>
			<# if( 'yes' === settings.link_switcher && '' !== button_url ) { #>
				<a {{{ view.getRenderAttributeString('link') }}}></a>
			<# } #>
		</div>

		<?php
	}


}
