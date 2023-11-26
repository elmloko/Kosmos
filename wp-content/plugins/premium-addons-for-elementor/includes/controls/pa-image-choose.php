<?php

namespace PremiumAddons\Includes\Controls;

use Elementor\Base_Data_Control;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class Premium_Image_Choose extends Base_Data_Control {

	const TYPE = 'premium-image-choose';

	/**
	 * Get choose control type.
	 *
	 * Retrieve the control type, in this case `choose`.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Control type.
	 */
	public function get_type() {
		return self::TYPE;
	}

	/**
	 * Render choose control output in the editor.
	 *
	 * Used to generate the control HTML in the editor using Underscore JS
	 * template. The variables for the class are available using `data` JS
	 * object.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function content_template() {
		$control_uid = $this->get_control_uid( '{{value}}' );
		?>
		<div class="elementor-control-field">
			<label class="elementor-control-title">{{{ data.label }}}</label>
			<div class="elementor-control-input-wrapper">
				<div class="elementor-image-choices">
					<# _.each( data.options, function( options, value ) { #>
						<div class="image-choose-label-block" style="width:{{ options.width }}">
							<input id="<?php echo esc_attr( $control_uid ); ?>" type="radio" name="elementor-choose-{{ data.name }}-{{ data._cid }}" value="{{ value }}">
							<label class="elementor-image-choices-label" for="<?php echo esc_attr( $control_uid ); ?>" title="{{ options.title }}">
								<#
									if ( options.isDivider ) {

										#>{{{options.imagesmall}}}<#
									} else {
										#>
										<img class="imagesmall" src="{{ options.imagesmall }}" alt="{{ options.title }}" {{ options.imagesmallheight ? `style=height:` + options.imagesmallheight : `` }} />
										<#
									}
								#>
								<span class="elementor-screen-only">{{{ options.title }}}</span>
							</label>
						</div>
					<# } ); #>
				</div>
			</div>
		</div>

		<# if ( data.description ) { #>
		<div class="elementor-control-field-description">{{{ data.description }}}</div>
		<# } #>
		<?php
	}

	/**
	 * Get choose control default settings.
	 *
	 * Retrieve the default settings of the choose control. Used to return the
	 * default settings while initializing the choose control.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return array Control default settings.
	 */
	protected function get_default_settings() {
		return array(
			'label_block' => true,
			'options'     => array(),
		);
	}
}