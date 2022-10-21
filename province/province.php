<?php

/*
 * Plugin Name: Province. 
 * Description: Province lists.
 * Author: Besticoder
 * Author URI: https://www.besticoder.com/
 * Version: 1.0
 *  Domain Path: /languages
 */

function province_plugin_scripts() {
	wp_enqueue_style( 'custom-style', plugin_dir_url( __FILE__ ) . 'assets/css/style.css' );


}

add_action( 'wp_enqueue_scripts', 'province_plugin_scripts' );





wp_register_script( 'custom-plugin-js', plugin_dir_url( __FILE__ ) . 'assets/js/custom-plugin.js', null, null, true );
wp_enqueue_script( 'custom-plugin-js' );



require_once plugin_dir_path( __FILE__ ) . 'public/shortcode.php';




