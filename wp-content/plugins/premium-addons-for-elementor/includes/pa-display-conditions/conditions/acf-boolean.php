<?php
/**
 * Acf Boolean Condition Handler.
 */

namespace PremiumAddons\Includes\PA_Display_Conditions\Conditions;

// Elementor Classes.
use Elementor\Controls_Manager;

// PA Classes.
use PremiumAddons\Includes\ACF_Helper;
use PremiumAddons\Includes\Helper_Functions;
use PremiumAddons\Includes\Controls\Premium_Acf_Selector;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Acf_Boolean.
 */
class Acf_Boolean extends Condition {

	/**
	 * Holds defaults options for acf queries.
	 *
	 * @access protected
	 * @var $query_options_defaults.
	 */
	protected $query_options_defaults = array(
		'show_type'       => false,
		'show_field_type' => true,
		'include_option'  => true,
		'show_group'      => true,
		'field_type'      => 'boolean',
	);

	/**
	 * Get Controls Options.
	 *
	 * @access public
	 * @since 4.7.0
	 *
	 * @return array|void  controls options
	 */
	public function get_control_options() {

		return array(
			'label'       => __( 'Value', 'premium-addons-for-elementor' ),
			'type'        => Controls_Manager::SELECT,
			'label_block' => true,
			'options'     => array(
				'true'  => __( 'True', 'premium-addons-for-elementor' ),
				'false' => __( 'False', 'premium-addons-for-elementor' ),
			),
			'default'     => 'true',
			'condition'   => array(
				'pa_condition_key' => 'acf_boolean',
			),
		);

	}

	/**
	 * Get Query Options.
	 *
	 * @access public
	 * @since 4.7.0
	 *
	 * @return array|void  controls options
	 */
	public function get_query_options() {
		return $this->query_options_defaults;
	}

	/**
	 * Get Value Controls Options.
	 *
	 * @access public
	 * @since 4.7.0
	 *
	 * @return array  controls options.
	 */
	public function add_value_control() {

		return array(
			'label'         => __( 'ACF Field', 'premium-addons-for-elementor' ),
			'type'          => Premium_Acf_Selector::TYPE,
			'options'       => array(),
			'query_type'    => 'acf',
			'label_block'   => true,
			'multiple'      => false,
			'query_options' => $this->get_query_options(),
			'description'   => __( 'ACF True/False', 'premium-addons-for-elementor' ),
			'condition'     => array(
				'pa_condition_key' => 'acf_boolean',
			),
		);

	}

	/**
	 * Compare Condition Value.
	 *
	 * @access public
	 * @since 4.7.0
	 *
	 * @param array       $settings        element settings.
	 * @param string      $operator        condition operator.
	 * @param string      $value           condition value.
	 * @param string|void $compare_val     compare value.
	 * @param string|bool $tz        time zone.
	 *
	 * @return bool|void
	 */
	public function compare_value( $settings, $operator, $compare_val, $value, $tz ) {

		$field = get_field_object( $value );

		$acf_helper = new ACF_Helper();

		$value = $acf_helper->get_acf_field_value( $value, $field['parent'] );

		$input_val = 'true' ===  sanitize_text_field( $compare_val ) ? true : false;

		$condition_result = $value === $input_val ? true : false;

		return Helper_Functions::get_final_result( $condition_result, $operator );

	}

}
