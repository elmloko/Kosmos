<?php
/**
 * Pa_Weather_Handler.
 */

namespace PremiumAddons\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
 * Pa_Weather_Handler Class.
 */
class Pa_Weather_Handler {

	/**
	 * Api Settings.
	 *
	 * @var api_settings
	 */
	private static $api_settings = null;

	/**
	 * Onecall Api URL.
	 *
	 * @var onecall_api
	 */
	private static $onecall_api = 'https://api.openweathermap.org/data/3.0/onecall';

	/**
	 * Weather Api URL.
	 *
	 * @var weather_api
	 */
	private static $weather_api = 'https://api.openweathermap.org/data/2.5/forecast';

	/**
	 * Class Constructor.
	 *
	 * @param array $api_settings API settings.
	 */
	public function __construct( $api_settings = array() ) {
		self::$api_settings = $api_settings;
	}

	/**
	 * Get Weather Data.
	 *
	 * @access public
	 * @since 2.8.23
	 *
	 * @return array
	 */
	public static function get_weather_data() {

		$settings = self::$api_settings;

		$loc_type = $settings['location_type'];

		$req_url = self::$onecall_api;

		$city_data = self::get_city_name();

		$settings['lat'] = $city_data['lat'];

		$settings['long'] = $city_data['long'];

		$req_url .= '?lat=' . $settings['lat'] . '&lon=' . $settings['long'] . '&units=' . $settings['unit'] . '&lang=' . $settings['lang'];

		$req_url .= '&exclude=minutely,alerts';

		$forecast_tabs = isset( $settings['forecast_tabs'] ) && $settings['forecast_tabs'] ? true : false;

		if ( ! $settings['forecast'] || $forecast_tabs ) {
			$req_url .= ',daily';
		}

		if ( ! $settings['hourly'] ) {
			$req_url .= ',hourly';
		}

		$req_url .= '&appid=' . $settings['api_key'];

		$weather_data = wp_remote_get(
			$req_url,
			array(
				'timeout'   => 60,
				'sslverify' => false,
			)
		);

		if ( is_wp_error( $weather_data ) || empty( $weather_data ) ) {
			return array(
				'status' => false,
				'res'    => $weather_data,
			);
		}

		$weather_data = json_decode( wp_remote_retrieve_body( $weather_data ), true );

		$weather_data['city_name'] = $city_data['city_name'];

		if ( $forecast_tabs ) {
			$weather_data['tabs_data'] = self::get_available_days( $city_data['tabs_data'] );
		}

		return $weather_data;
	}

	/**
	 * Get City Name.
	 *
	 * @access public
	 * @since 2.8.23
	 *
	 * @return array
	 */
	public static function get_city_name() {

		$req_url = self::$weather_api;

		$settings = self::$api_settings;

		$loc_type = $settings['location_type'];

		if ( 'current' === $loc_type ) {

			$current_location = self::get_current_location();
			$lat              = $current_location['lat'];
			$long             = $current_location['long'];

			$req_url .= '?lat=' . $lat . '&lon=' . $long;

		} else {
			if ( 'coords' === $settings['custom_location_type'] ) {
				$req_url .= '?lat=' . $settings['lat'] . '&lon=' . $settings['long'];
			} else { // by city name.
				$req_url .= '?q=' . $settings['city_name'];
			}
		}

		$req_url .= '&lang=' . $settings['lang'] . '&units=' . $settings['unit'] . '&appid=' . $settings['api_key'];

		$city_data = wp_remote_get(
			$req_url,
			array(
				'timeout'   => 60,
				'sslverify' => false,
			)
		);

		if ( is_wp_error( $city_data ) || empty( $city_data ) ) {
			return;
		}

		$city_data = json_decode( wp_remote_retrieve_body( $city_data ), true );

		return array(
			'city_name' => $city_data['city']['name'],
			'lat'       => $city_data['city']['coord']['lat'],
			'long'      => $city_data['city']['coord']['lon'],
			'tabs_data' => $city_data['list'],
		);
	}

	/**
	 * Get Current Location.
	 *
	 * @access public
	 * @since 2.8.23
	 *
	 * @return array
	 */
	public static function get_current_location() {

		if ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {

			$x_forward = sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) );

			if ( is_array( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {

				$http_x_headers         = explode( ',', filter_var_array( $x_forward ) );
				$_SERVER['REMOTE_ADDR'] = $http_x_headers[0];
			} else {
				$_SERVER['REMOTE_ADDR'] = $x_forward;
			}
		}

		$ip_address = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';

		$location_data = json_decode( rplg_urlopen( 'http://www.geoplugin.net/json.gp?ip=' . $ip_address )['data'] );

		if ( 404 === $location_data->geoplugin_status ) {
			return false; // localhost.
		}

		return array(
			'lat'  => $location_data->geoplugin_latitude,
			'long' => $location_data->geoplugin_longitude,
		);
	}

	public static function get_available_days( $data ) {

		$days_forecast = array();

		foreach ( $data as $element ) {

			$date = date( 'Y-m-d', $element['dt'] );

			if ( ! isset( $days_forecast[ $date ] ) ) {
				$days_forecast[ $date ] = array();
			}

			$days_forecast[ $date ][] = $element;
		}

		return $days_forecast;
	}
}


