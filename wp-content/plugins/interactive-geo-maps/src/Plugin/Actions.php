<?php

namespace Saltus\WP\Plugin\Saltus\InteractiveMaps\Plugin;

use Saltus\WP\Plugin\Saltus\InteractiveMaps\Core;

/**
 * Manage available click actions
 */
class Actions {

	/**
	 * Available click actions
	 */
	public $actions;

	/**
	 * Define Actions
	 *
	 * @param Core $core This plugin's instance.
	 */
	public function __construct( Core $core ) {

		$this->actions = $this->get_actions();

		// filters - set default actions
		add_filter( 'igm_click_actions', array( $this, 'default_actions' ) );

	}

	/**
	 * Set available actions
	 *
	 * @return void
	 */
	public function get_actions() {

		$actions = [
			/* translators: "None" is one of the options for "Click Action" */
			'none'         => __( 'None', 'interactive-geo-maps' ),
			/* translators: "Open URL" is one of the options for "Click Action" */
			'open_url'     => __( 'Open URL', 'interactive-geo-maps' ),
			/* translators: "Open URL (new tab)" is one of the options for "Click Action" */
			'open_url_new' => __( 'Open URL (new tab)', 'interactive-geo-maps' ),
		];

		return $actions;

	}

	public function default_actions( $actions ) {

		$actions = array_merge( $actions, $this->get_actions() );

		// default
		return $actions;

	}

}
