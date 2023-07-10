<?php

namespace PremiumAddons\Includes\Templates\Sources;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

abstract class Premium_Templates_Source_Base {

	/**
	 * @abstract
	 * @since 3.6.0
	 * @access public
	 */
	abstract public function get_slug();

	/**
	 * @abstract
	 * @since 3.6.0
	 * @access public
	 */
	abstract public function get_items();

	/**
	 * @abstract
	 * @since 3.6.0
	 * @access public
	 */
	abstract public function get_categories();

	/**
	 * Return source item list
	 *
	 * @since 3.6.0
	 * @access public
	 */
	abstract public function get_keywords();

	/**
	 * @abstract
	 * @since 3.6.0
	 * @access public
	 */
	abstract public function get_item( $template_id );

	/**
	 * @abstract
	 * @since 3.6.0
	 * @access public
	 */
	abstract public function transient_lifetime();

	/**
	 * Returns templates transient key for current source
	 *
	 * @return string
	 */
	public function templates_key() {
		return 'premium_templates_' . $this->get_slug() . '_1.0.0';
	}

	/**
	 * Returns categories  transient key for current source
	 *
	 * @return string
	 */
	public function categories_key() {
		return 'premium_categories_' . $this->get_slug() . '_1.0.0';
	}

	/**
	 * Returns keywords transient key for current source
	 *
	 * @return string
	 */
	public function keywords_key() {
		return 'premium_keywords_' . $this->get_slug() . '_1.0.0';
	}

	/**
	 * Set templates cache.
	 *
	 * @param array $value
	 */
	public function set_templates_cache( $value ) {

		$localhost = array(
			'127.0.0.1',
			'::1',
		);

		if ( ! isset( $_SERVER['REMOTE_ADDR'] ) ) {
			return;
		}

		// Load templates remotely if localhost.
		if ( ! in_array( $_SERVER['REMOTE_ADDR'], $localhost ) ) {
			set_transient( $this->templates_key(), $value, $this->transient_lifetime() );
		}
	}

	/**
	 * Set templates cache.
	 */
	public function get_templates_cache() {

		// if ( $this->is_debug_active() ) {
		// return false;
		// }

		return get_transient( $this->templates_key() );
	}

	/**
	 * Delete templates cache
	 */
	public function delete_templates_cache() {
		delete_transient( $this->templates_key() );
	}

	/**
	 * Set categories cache.
	 */
	public function set_categories_cache( $value ) {
		set_transient( $this->categories_key(), $value, $this->transient_lifetime() );
	}

	/**
	 * Set categories cache.
	 *
	 * @param array $value
	 */
	public function get_categories_cache() {

		// if ( $this->is_debug_active() ) {
		// return false;
		// }

		return get_transient( $this->categories_key() );
	}

	/**
	 * Delete categories cache
	 *
	 * @return [type] [description]
	 */
	public function delete_categories_cache() {
		delete_transient( $this->categories_key() );
	}

	/**
	 * Set categories cache.
	 *
	 * @param array $value cached value.
	 */
	public function set_keywords_cache( $value ) {
		set_transient( $this->keywords_key(), $value, $this->transient_lifetime() );
	}

	/**
	 * Set categories cache.
	 *
	 * @param array $value cached value.
	 */
	public function get_keywords_cache() {

		// if ( $this->is_debug_active() ) {
		// return false;
		// }

		return get_transient( $this->keywords_key() );
	}

	/**
	 * Delete categories cache
	 *
	 * @return [type] [description]
	 */
	public function delete_keywords_cache() {
		delete_transient( $this->keywords_key() );
	}

	/**
	 * Check if debug is active
	 *
	 * @return boolean
	 */
	public function is_debug_active() {

		if ( defined( 'PREMIUM_API_DEBUG' ) && true === PREMIUM_API_DEBUG ) {
			return true;
		} else {
			return false;
		}

	}

	/**
	 * Returns template ID prefix for premium templates
	 *
	 * @return string
	 */
	public function id_prefix() {
		return 'premium_';
	}

	/**
	 * @since 3.6.0
	 * @access protected
	 */
	protected function replace_elements_ids( $content ) {
		return \Elementor\Plugin::$instance->db->iterate_data(
			$content,
			function( $element ) {
				$element['id'] = \Elementor\Utils::generate_random_string();
				return $element;
			}
		);
	}

	/**
	 * Process content for export/import.
	 *
	 * Process the content and all the inner elements, and prepare all the
	 * elements data for export/import.
	 *
	 * @since 3.6.0
	 * @access protected
	 *
	 * @param array  $content A set of elements.
	 * @param string $method  Accepts either `on_export` to export data or
	 *                        `on_import` to import data.
	 * @param string $with_media include templates media.
	 *
	 * @return mixed Processed content data.
	 */
	protected function process_export_import_content( $content, $method, $with_media ) {

		return \Elementor\Plugin::$instance->db->iterate_data(
			$content,
			function( $element_data ) use ( $method, $with_media ) {
				$element = \Elementor\Plugin::$instance->elements_manager->create_element_instance( $element_data );

				// If the widget/element isn't exist, like a plugin that creates a widget but deactivated.
				if ( ! $element ) {
					return null;
				}

				return $this->process_element_export_import_content( $element, $method, $with_media );
			}
		);
	}

	/**
	 * Process single element content for export/import.
	 *
	 * Process any given element and prepare the element data for export/import.
	 *
	 * @since 3.6.0
	 * @access protected
	 *
	 * @param Controls_Stack $element
	 * @param string         $method
	 * @param string         $with_media include templates media.
	 *
	 * @return array Processed element data.
	 */
	protected function process_element_export_import_content( $element, $method, $with_media ) {

		$element_data = $element->get_data();

		if ( method_exists( $element, $method ) ) {
			// TODO: Use the internal element data without parameters.
			$element_data = $element->{$method}( $element_data );
		}

		foreach ( $element->get_controls() as $control ) {
			$control_class = \Elementor\Plugin::$instance->controls_manager->get_control( $control['type'] );

			// If the control isn't exist, like a plugin that creates the control but deactivated.
			if ( ! $control_class ) {
				return $element_data;
			}

			if ( method_exists( $control_class, $method ) ) {

				if ( 'media' !== $control['type'] && 'hedia' !== $control['type'] && 'repeater' !== $control['type'] ) {
					$element_data['settings'][ $control['name'] ] = $control_class->{$method}( $element->get_settings( $control['name'] ), $control );
				} elseif ( 'repeater' === $control['type'] ) {
						$element_data['settings'][ $control['name'] ] = $this->on_import_repeater( $element->get_settings( $control['name'] ), $with_media, $control );
				} else {
					if ( ! empty( $element_data['settings'][ $control['name'] ]['url'] ) ) {
						$element_data['settings'][ $control['name'] ] = $this->on_import_media( $element->get_settings( $control['name'] ), $with_media );
					}
				}
			}
		}

		return $element_data;
	}

	public function on_import_media( $settings, $media ) {

		if ( empty( $settings['url'] ) || false != strpos( $settings['url'], 'placeholder' ) ) {
			return $settings;
		}

		if ( ! $media ) {

			$file_ext = pathinfo( $settings['url'] )['extension'];
			switch ( true ) {
				case 'mp4' === $file_ext:
					$settings['url'] = 'https://premiumtemplates.io/wp-content/uploads/2018/10/video-placeholder.mp4';
					break;
				case 'jpg' === $file_ext || 'png' === $file_ext:
					$settings['url'] = ELEMENTOR_ASSETS_URL . 'images/placeholder.png';
					break;
				case 'json' === $file_ext:
					$settings['url'] = 'https://assets1.lottiefiles.com/packages/lf20_FPxkbx.json';
					break;
			}
		} else {
			$settings = \Elementor\Plugin::$instance->templates_manager->get_import_images_instance()->import( $settings );
		}

		return $settings;
	}

	public function on_import_repeater( $settings, $media, $control_data = array() ) {
		if ( empty( $settings ) || empty( $control_data['fields'] ) ) {
			return $settings;
		}

		$method = 'on_import';

		foreach ( $settings as &$item ) {
			foreach ( $control_data['fields'] as $field ) {
				if ( empty( $field['name'] ) || empty( $item[ $field['name'] ] ) ) {
					continue;
				}

				$control_obj = \Elementor\Plugin::$instance->controls_manager->get_control( $field['type'] );

				if ( ! $control_obj ) {
					continue;
				}

				if ( method_exists( $control_obj, $method ) ) {
					if ( 'media' !== $field['type'] && 'hedia' !== $field['type'] ) {
						$item[ $field['name'] ] = $control_obj->{$method}( $item[ $field['name'] ], $field );
					} else {
						if ( ! empty( $item[ $field['name'] ]['url'] ) ) {
							$item[ $field['name'] ] = $this->on_import_media( $item[ $field['name'] ], $media );
						}
					}
				}
			}
		}

		return $settings;
	}
}
