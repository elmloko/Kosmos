<?php

namespace Saltus\WP\Plugin\Saltus\InteractiveMaps\Plugin\Integrations;

/**
 * Elementor IGMap Widget.
 *
 * Elementor widget to add a previously created interactive map
 *
 * @since 1.0.0
 */
class ElementorMapWidget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve IGMap widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'igmap';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve IGMap widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Interactive Geo Map', 'interactive-geo-maps' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve IGMap widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-globe';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the IGMap widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'general' ];
	}

	/**
	 * Register IGMap widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {

		$args = array(
			'posts_per_page'   => -1,
			'post_type'        => 'igmap',
			'suppress_filters' => true,
			'orderby'          => 'ID',
			'order'            => 'DESC',
		);

		$maps = get_posts( $args );

		$options = [];

		foreach ( $maps as $key => $map ) {
			$options[ $map->ID ] = $map->post_title;
		}

		$options['default'] = __( 'Select Map to display', 'interactive-geo-maps' );

		$this->start_controls_section(
			'map_select',
			[
				'label' => __( 'Map to display', 'interactive-geo-maps' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'mapID',
			[
				'label'   => __( 'Map', 'interactive-geo-maps' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => $options,
				'default' => 'default',
			]
		);


		$this->end_controls_section();

	}

	/**
	 * Render IGMap widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();
		$map_id   = $settings['mapID'];

		if ( is_admin() ) {

			if ( $map_id && $map_id !== 'default' ) {
				$image_meta = get_post_meta( $map_id, 'map_image', true );
				$image      = isset( $image_meta['mapImage'] ) && $image_meta['mapImage'] !== '' ? $image_meta['mapImage'] : '';
				$html       = sprintf( '<img src="%s">', $image );
				$html      .= '<br><div style="text-align:center; font-size:0.8em;">' . __( 'Preview Only', 'interactive-geo-maps' ) . '</div>';
			} else {
				$html = __( 'Please select a map', 'interactive-geo-maps' );
			}
		} else {

			$html = do_shortcode( '[display-map id="' . $map_id . '"]' );

		}

		echo '<div class="igmap-elementor-widget">';

		echo $html;

		echo '</div>';

	}

}
