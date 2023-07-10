<?php
/**
 * Post Format Condition Handler.
 */

namespace PremiumAddons\Includes\PA_Display_Conditions\Conditions;

// Elementor Classes.
use Elementor\Controls_Manager;

// PA Classes.
use PremiumAddons\Includes\Helper_Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Post_Format.
 */
class Post_Format extends Condition {

	/**
	 * Get Controls Options.
	 *
	 * @access public
	 * @since 4.7.0
	 *
	 * @return array|void  controls options.
	 */
	public function get_control_options() {

		return array(
			'label'       => __( 'Value', 'premium-addons-for-elementor' ),
			'description' => __( 'Value', 'premium-addons-for-elementor' ),
			'type'        => Controls_Manager::SELECT2,
			'default'     => array(),
			'label_block' => true,
			'options'     => array(
				'standard' => __( 'Standard', 'premium-addons-for-elementor' ),
				'aside'    => __( 'Aside', 'premium-addons-for-elementor' ),
				'chat'     => __( 'Chat', 'premium-addons-for-elementor' ),
				'gallery'  => __( 'Gallery', 'premium-addons-for-elementor' ),
				'link'     => __( 'Link', 'premium-addons-for-elementor' ),
				'image'    => __( 'Image', 'premium-addons-for-elementor' ),
				'quote'    => __( 'Quote', 'premium-addons-for-elementor' ),
				'status'   => __( 'Status', 'premium-addons-for-elementor' ),
				'video'    => __( 'Video', 'premium-addons-for-elementor' ),
				'audio'    => __( 'Audio', 'premium-addons-for-elementor' ),
			),
			'multiple'    => true,
			'condition'   => array(
				'pa_condition_key' => 'post_format',
			),
		);
	}

	/**
	 * Compare Condition Value.
	 *
	 * @access public
	 * @since 4.7.0
	 *
	 * @param array       $settings       element settings.
	 * @param string      $operator       condition operator.
	 * @param string      $value          condition value.
	 * @param string      $compare_val    compare value.
	 * @param string|bool $tz        time zone.
	 *
	 * @return bool|void
	 */
	public function compare_value( $settings, $operator, $value, $compare_val, $tz ) {

		$post_format = get_post_format( get_the_ID() ) ? : 'standard';

		$condition_result = is_array( $value ) && ! empty( $value ) && in_array( $post_format, $value, true ) ? true : false;

		return Helper_Functions::get_final_result( $condition_result, $operator );
	}

}
