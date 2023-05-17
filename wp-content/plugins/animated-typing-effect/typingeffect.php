<?php
/**
 * Plugin Name: Typing Effect
 * Version: 1.3.6
 * Plugin URI: http://93digital.co.uk/
 * Description: Animated typing effect plugin, allowing you to generate a shortcode that 'types' out words on your page or post. Based on Typed.js by Matt Boldt.
 * Author: 93digital
 * Author URI: https://93digital.co.uk/
 * Text Domain: typing-effect
 * Domain Path: /languages/
 * License: GPL v3
 */

/**
 * Typing Effect Plugin
 * Copyright (C) 2021, 93digital - support@93digital.co.uk
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if ( ! defined( 'ABSPATH' ) ) {
  echo 'Hi there! I\'m just a plugin.';
  exit;
}

class nine93Typed {
  public function __construct() {
    add_action( 'admin_menu', array( & $this, 'add_menu_pages' ) );

    //Scripts
    add_action( 'admin_enqueue_scripts', array( &$this, 'register_admin_scripts' ) );
    add_action( 'wp_enqueue_scripts', array( &$this, 'register_scripts' ) );

    //Register the shortcode
    add_shortcode( 'typed' , array( & $this, 'do_shortcode' ) );
  }

  function add_menu_pages() {
    add_menu_page('Typing Effect', __( 'Typing Effect', '93digital-typed' ), 'administrator', '93digital-typed', array( & $this, 'shortcode_generator') );
  }

  function register_scripts() {
    wp_enqueue_style( 'typed-cursor', plugins_url( 'assets/css/cursor.css', __FILE__ ) );

    wp_enqueue_script( 'typed-script', plugins_url( 'assets/js/typed.js', __FILE__ ), array( 'jquery' ), 1.0, true );
    wp_enqueue_script( 'typed-frontend', plugins_url( 'assets/js/typed.fe.js', __FILE__ ), array( 'jquery' ), 1.0, true );
  }

  function register_admin_scripts() {
      wp_enqueue_script( '93typed-script', plugins_url( 'assets/js/typed.admin.js', __FILE__ ), array( 'jquery' ), 1.0, true );
      wp_enqueue_script( 'typed-script', plugins_url( 'assets/js/typed.js', __FILE__ ), array( 'jquery' ), 1.0, true );

      wp_enqueue_style( '93typed-css', plugins_url( 'assets/css/style.css', __FILE__ ) );
  }

  function shortcode_settings() {
    require_once( 'layout/shortcode-settings.php' );
  }

  function shortcode_generator() {
    add_meta_box( 'nine3digital-preview', __( 'Preview', 'ceceppaml' ), array( & $this, 'typed_preview' ), 'typed_metaboxes' );
    add_meta_box( 'nine3digital-typed', __( 'Settings', 'ceceppaml' ), array( & $this, 'shortcode_settings' ), 'typed_metaboxes' );

    require_once( 'layout/shortcode.php' );
  }

  function typed_preview() {
    echo '<div id="preview"><span class="preview"></span></div>';
    echo '<input type="button" name="update" value="Generate Shortcode" class="button button-large button-primary" />';
  }

  function do_shortcode( $atts ) {
    $span = '<span class="typed-me"';
    $options = array();

    //WP Convert the parameters in lowercase format, but I need in camel case
    $params = array(
      'typespeed' => 'type-speed',
      'backdelay' => 'back-delay',
      'startdelay' => 'start-delay',
      'loopcount' => 'loop-count',
      'shuffle' => 'shuffle',
    );

    //Generate the javascript code
    foreach( $atts as $key => $value ) {
      $key = isset( $params[ $key ] ) ? $params[$key] : $key;
      $span .= " data-{$key}=\"" . $value . '"';
    }

    return $span . "></span>";
  }
}

new nine93Typed();
