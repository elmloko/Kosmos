<?php
/**
 * Class: Premium_Post_Filter
 * Name:  Premium Post Filter
 * Slug:  premium-post-filter
 */

namespace PremiumAddons\Includes\Controls;

use Elementor\Control_Select2;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
 * Premium Post Filter extended from select2
 */
class Premium_Post_Filter extends Control_Select2 {

	const TYPE = 'premium-post-filter';

	/**
	 * Returns the type of the control
	 */
	public function get_type() {
		return self::TYPE;
	}

}


