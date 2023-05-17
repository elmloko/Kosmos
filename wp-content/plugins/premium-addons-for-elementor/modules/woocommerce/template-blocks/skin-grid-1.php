<?php
/**
 * PA Grid Skin.
 *
 * @package PA
 */

namespace PremiumAddons\Modules\Woocommerce\TemplateBlocks;

use PremiumAddons\Modules\Woocommerce\TemplateBlocks\Skin_Style;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

/**
 * Class Skin_Grid_1
 */
class Skin_Grid_1 extends Skin_Style {

	/**
	 * Member Variable
	 *
	 * @var instance
	 */
	private static $instance;

	/**
	 *  Initiator
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

}

