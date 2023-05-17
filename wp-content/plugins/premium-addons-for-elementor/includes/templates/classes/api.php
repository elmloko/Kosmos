<?php

namespace PremiumAddons\Includes\Templates\Classes;

use PremiumAddons\Includes\Templates;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No access of directly access
}

if ( ! class_exists( 'Premium_Templates_API' ) ) {

	/**
	 * Premium API.
	 *
	 * Premium API class is responsible for getting API data.
	 *
	 * @since 3.6.0
	 */
	class Premium_Templates_API {

		/**
		 * API URL which is used to get the response from.
		 *
		 * @since  3.6.0
		 * @var (String) URL
		 */
		private $config = array();

		/**
		 * API enabled
		 *
		 * @since  3.6.0
		 * @var (Boolean)
		 */
		private $enabled = null;

		/**
		 * Premium_API constructor.
		 *
		 * Get all API data.
		 *
		 * @since 3.6.0
		 * @access public
		 */
		public function __construct() {

			$this->config = Templates\premium_templates()->config->get( 'api' );

		}

		/**
		 * Is Enabled.
		 *
		 * Check if remote API is enabled.
		 *
		 * @since 3.6.0
		 * @access public
		 *
		 * @return boolean
		 */
		public function is_enabled() {

			if ( null !== $this->enabled ) {
				return $this->enabled;
			}

			if ( empty( $this->config['enabled'] ) || true !== $this->config['enabled'] ) {
				$this->enabled = false;
				return $this->enabled;
			}

			if ( empty( $this->config['base'] ) || empty( $this->config['path'] ) || empty( $this->config['endpoints'] ) ) {
				$this->enabled = false;
				return $this->enabled;
			}

			$this->enabled = true;

			return $this->enabled;
		}

		/**
		 * API URL.
		 *
		 * Get API for template library area data.
		 *
		 * @since 3.6.0
		 * @access public
		 */
		public function api_url( $flag ) {

			if ( ! $this->is_enabled() ) {
				return false;
			}

			if ( empty( $this->config['endpoints'][ $flag ] ) ) {
				return false;
			}

			return $this->config['base'] . $this->config['path'] . $this->config['endpoints'][ $flag ];
		}

		/**
		 * Request Args
		 *
		 * Get request arguments for the remote request.
		 *
		 * @since 3.6.0
		 * @access public
		 *
		 * @return array
		 */
		public function request_args() {
			return array(
				'timeout'   => 60,
				'sslverify' => false,
			);
		}

	}

}
