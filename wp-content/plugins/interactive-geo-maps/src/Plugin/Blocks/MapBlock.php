<?php

namespace Saltus\WP\Plugin\Saltus\InteractiveMaps\Plugin\Blocks;

class MapBlock {


	public $arguments;

	public $core;

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct( $core ) {

		$this->core = $core;

		// enqueue assets
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_assets' ) );

	}

	public function enqueue_block_assets(){

		// styles
		wp_register_style(
			$this->core->name . '_blocks',
			plugins_url( 'assets/block-styles.css', __FILE__ ),
			false,
			$this->core->version
		);

		wp_enqueue_style( $this->core->name . '_blocks' );

		// map block javascript
		wp_enqueue_script(
			$this->core->name . '_map_block',
			plugins_url( 'assets/mapBlock.js', __FILE__ ),
			array( 'wp-blocks', 'wp-i18n', 'wp-element' ),
			$this->core->version,
			false
		);

		$args = array(
			'posts_per_page'   => -1,
			'post_type'        => 'igmap',
			'suppress_filters' => true,
		);

		$maps = get_posts( $args );

		$object = (object) [ 'property' => 'Here we go' ];

		$map_options = array(
			(object) [
				'value'      => '',
				/* translators: used in block editor when no map is selected. */
				'label'      => __( 'Please select a map', 'interactive-geo-maps' ),
				'paddingTop' => '56',
				'maxWidth'   => '',
				'image'      => ''
			],
		);

		foreach ( $maps as $map ) {

			$meta       = get_post_meta( $map->ID, 'map_info', true );
			$height     = isset( $meta['visual']['paddingTop'] ) ? $meta['visual']['paddingTop'] : '56.25';
			$height     = strpos( $height, '%' ) !== false ? str_replace( '%', '', $height ) : $height;
			$max_width  = isset( $meta['visual']['maxWidth'] ) && $meta['visual']['maxWidth'] !== '0' && $meta['visual']['maxWidth'] !== '' ? $meta['visual']['maxWidth'] : '2200';
			$image_meta = get_post_meta( $map->ID, 'map_image', true );
			$image      = isset( $image_meta['mapImage'] ) && $image_meta['mapImage'] !== '' ? $image_meta['mapImage'] : '';

			array_push(
				$map_options,
				(object) [
					'value'      => $map->ID,
					'label'      => $map->post_title,
					'paddingTop' => $height,
					'maxWidth'   => $max_width,
					'image'      => $image,
				]
			);
		}

		wp_localize_script( $this->core->name . '_map_block', 'igmMapBlockOptions', $map_options );

	}

}
