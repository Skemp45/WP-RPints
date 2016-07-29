<?php
/**
 * Plugin Name: RaspberryPints Tap Access
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

/////////////////////////////////////////////////////////////
//              Database Related Functions                 //
/////////////////////////////////////////////////////////////

//Table Creation
function RPDB_install () {
   global $wpdb;

   //define table name
   $table_name = $wpdb->prefix . "raspberrypints";
   $charset_collate = $wpdb->get_charset_collate();

   //sql statement to create table
   $sql = "CREATE TABLE $table_name (
      id int(255) NOT NULL AUTO_INCREMENT,
      api_key varchar(255) NOT NULL,
      api_secret varchar(255) NOT NULL,
      UNIQUE KEY id (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}


// //insert data into Table
function RPDB_insert_data($apiK, $apiS) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'raspberrypints';
  $results = $wpdb->get_row( "SELECT id FROM ". $table_name." LIMIT 1" );
  $apiId = $results->id;
  if($apiId < 1){
    $wpdb->insert(
  		$table_name,
  		array(
  			'api_key' => $apiK,
  			'api_secret' => $apiS
  		)
  	);
  }
  else
  {
  $wpdb->update(
    $table_name,
    array(
      'api_key' => $apiK,
      'api_secret' => $apiS
    ),
    array(
      'id' => 1
    )
  );
  }

  register_activation_hook( __FILE__, 'RPDB_insert_data' );
  echo "<script>window.location.reload()</script>";
}


//Install db settings on install
register_activation_hook( __FILE__, 'RPDB_install' );


/////////////////////////////////////////////////////////////
//           Start Admin Menu function section             //
/////////////////////////////////////////////////////////////
//Build out Admin UI
add_action('admin_menu', 'WPRP_menu');

 // add_menu_page( string $page_title, string $menu_title, string $capability, string $menu_slug, callable $function = '', string $icon_url = '', int $position = null

//Set Menu Settings
function WPRP_menu(){
        add_menu_page(
            'WP RPints Admin Page',
            'WPRP Admin',
            'manage_options',
            'wprp-admin',
            'wprp_init',
            'dashicons-editor-table' );
}

//Create Display Inside admin section
function wprp_init(){
  global $wpdb;

  $table_name = $wpdb->prefix . 'raspberrypints';
  $results = $wpdb->get_row( "SELECT id, api_key, api_secret FROM ". $table_name ." WHERE id = 1" );

  $apiKey = $results->api_key;
  $apiSecret = $results->api_secret;

  if (isset($_POST["apiKey"])){
    $apiK = $_POST["apiKey"];
    $apiS = $_POST["apiSecret"];
    RPDB_insert_data($apiK, $apiS);

  }

  //Title Section
  echo "<h1>RaspberryPints Taplist Admin</h1>";

  //set navigation
  echo "<h2 class='nav-tab-wrapper'><a class='nav-tab nav-tab-active' id='api-tab' href='#top#api'>API Settings</a><a class='nav-tab nav-tab-disabled' id='other-tab' href='#top#other'>Other</a></h2>";

  //Page Description
  echo "<p style='font-size:120%; width:60%;'>
  This is where you will need to set your API Key and Secrect so that when you implement the short code into your website it can make the proper API calls in order to display your active Taplist.
  </p>";

  //Content divs
  echo "<div id='api'>
          <form action='' method='POST'>
          <label><b>API Key:</b></label><br/>
              <input type='text' name='apiKey' value='".$apiKey."'/><br/>
          <label><b>API Secret:</b></label><br/>
              <input type='text' name='apiSecret' value='".$apiSecret."'/><br/><br/>
              <button class='button button-primary'>Submit</button>
          </form>
      </div>";
  echo "<div id='other'></div>";

  }
/////////////////////////////////////////////////////////////
//            End Admin Menu function section              //
/////////////////////////////////////////////////////////////


// [rpints] Code
function validationCall( $creds, $content = null ) {
	$RP = shortcode_atts( array (
			'api_key' => '',
			'api_secret' => '',
			'page' => ''
		), $creds );

    //Building Validation URL
    $RP_Taplist_url = "http://raspberrypints.com/" . esc_attr($RP['page']) . "/?". esc_attr($RP['api_key']) .":". esc_attr($RP['api_secret']);

    //Returning the Results to DOM this is TEMP while we plan how to validate
    return "<iframe style='width: 100%; height: 1000px; border: none; outline: none;' src='". esc_url($RP_Taplist_url) ."' width='300' height='150'></iframe>";
}

add_shortcode( 'RPints', 'validationCall' );
