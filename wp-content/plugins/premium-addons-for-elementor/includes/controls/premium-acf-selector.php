<?php
/**
 * Class: Premium_Acf_Selector
 * Name:  Premium Acf Selector
 * Slug:  premium-acf-selector
 */

namespace PremiumAddons\Includes\Controls;

use Elementor\Control_Select2;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
 * Premium Acf Selector extends from select2
 */
class Premium_Acf_Selector extends Control_Select2 {

	const TYPE = 'premium-acf-selector';

	/**
	 * Returns the type of the control
	 */
	public function get_type() {
		return self::TYPE;
	}

}


