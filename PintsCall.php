<?php
/**
 * Plugin Name: Raspberry Pints Tap Access
 * Description: This plugin will use your API Key and Secret to pull in your active taplist onto your website.
 * Version: 1.0
 * Author: Six Pack Apps
 * Author URI: http://raspberrypints.com
 * License: GPL2
 */

/*  Copyright 2016  Six Pack Apps  (email : dev@sixpackapps.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
**************************************************************************/
// Define WP RPints Plugin
define( 'WPRPINTS_PLUGIN_VERSION', '1.0' );
define( 'WPRPINTS_PLUGIN__MINIMUM_WP_VERSION', '4.5' );
define( 'WPRPINTS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'WPRPINTS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

// Blocks direct access to plugin
defined( 'ABSPATH' ) or die( "Access Forbidden" );

// [rpints] Code
function validationCall( $creds, $content = null ) {
	$RP = shortcode_atts( array (
			'api_key' => '',
			'api_secret' => '',
			'page' => ''
		), $creds );

    //Building Validation URL
    $RP_Taplist_url = "http://raspberrypints.com/" . esc_attr($RP['page']) . "/?". esc_attr($RP['api_key']) .":". esc_attr($RP['api_secret']);

    //Returning the Results to DOM
    return "<iframe style='width: 100%; height: 1000px; border: none; outline: none;' src='". esc_url($RP_Taplist_url) ."' width='300' height='150'></iframe>";
}

add_shortcode( 'RPints', 'validationCall' );
