<?php
/**
 * Class: Premium_Select
 * Name: Premium Select
 * Slug: premium-select
 */

namespace PremiumAddons\Includes\Controls;

use Elementor\Control_Select2;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
 * Premium Select extended from select2
 */
class Premium_Select extends Control_Select2 {

	const TYPE = 'premium-select';

	/**
	 * Returns the type of the control
	 */
	public function get_type() {
		return self::TYPE;
	}
}


