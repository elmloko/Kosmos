<?php
/**
 * Acf Choice Condition Handler.
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
 * Class Acf_Choice.
 */
class Acf_Choice extends Condition {

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
		'field_type'      => 'choice',
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
			'type'        => Controls_Manager::TEXTAREA,
			'label_block' => true,
			'description' => __( 'Enter each accepted choice on a separate line in the same format as the field\'s return format. You can specify the value ( red ), the label ( Red ), or both value and label ( red : Red ).', 'premium-addons-for-elementor' ),
			'condition'   => array(
				'pa_condition_key' => 'acf_choice',
			),
		);

	}

	/**
	 * Get Query Options.
	 *
	 * @access public
	 * @since 4.7.0
	 *
	 * @return array|void  controls options.
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
			'description'   => __( 'ACF Choice ( Select, Checkbox, Radio ).', 'premium-addons-for-elementor' ),
			'condition'     => array(
				'pa_condition_key' => 'acf_choice',
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

		if ( empty( $value ) ) {
			$condition_result = empty( $value ) === empty( $compare_val ) ? true : false;
		} else {
			$is_radio = 'radio' === $field['type'];

			$single_select = 'select' === $field['type'] && ! $field['multiple'] ? true : false;

			$field_values = ACF_Helper::format_acf_values( $value, $field['return_format'], $is_radio, $single_select );

			$compare_vals = false !== strpos( $compare_val, ' : ' ) ? explode( "\n", ( $compare_val ) ) : array( $compare_val . ' : ' . $compare_val );

			$condition_result = ! empty( array_intersect( $compare_vals, $field_values ) ) ? true : false;
		}

		return Helper_Functions::get_final_result( $condition_result, $operator );
	}

}
