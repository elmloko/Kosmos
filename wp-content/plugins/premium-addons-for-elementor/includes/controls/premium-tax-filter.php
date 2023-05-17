<?php
/**
 * Class: Premium_Tax_Filter
 * Name:  Premium Tax Filter
 * Slug:  premium-tax-filter
 */

namespace PremiumAddons\Includes\Controls;

use Elementor\Control_Select;

use PremiumAddons\Includes\Premium_Template_Tags as Blog_Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
 * Premium Post Filter extended from Elementor Select Control
 *
 * @since 4.3.3
 */
class Premium_Tax_Filter extends Control_Select {

	const TYPE = 'premium-tax-filter';

	/**
	 * Returns the type of the control
	 */
	public function get_type() {
		return self::TYPE;
	}

}


