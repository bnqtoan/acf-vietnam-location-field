<?php
/*
Plugin Name: Advanced Custom Fields: Vietnam Location Field
Plugin URI: https://github.com/Vheissu/acf-country-field
Description: Adds a country as well as city/state field to your Wordpress sites.
Version: 1.0.1
Author: Dwayne Charrington
Author URI: http://ilikekillnerds.com
License: GPL
*/

load_plugin_textdomain( 'acf-VN_LOCATION_FIELD', false, dirname( plugin_basename(__FILE__) ) . '/lang/' );

// Activate and deactivate hooks
register_activation_hook( __FILE__, 'populate_db' );
register_deactivation_hook( __FILE__, 'depopulate_db' );

include_once (__DIR__.'/lib/functions.php');

// ACF version 5
function include_field_types_VN_LOCATION_FIELD( $version ) {
    include_once('acf-VN_LOCATION_FIELD-v5.php');
}
add_action('acf/include_field_types', 'include_field_types_VN_LOCATION_FIELD');

// ACF version 4
function register_fields_VN_LOCATION_FIELD() {
    include_once('acf-VN_LOCATION_FIELD-v4.php');
}
add_action('acf/register_fields', 'register_fields_VN_LOCATION_FIELD');  

function populate_db() {
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    ob_start();
    require_once "lib/install-data.php";
    $sql = ob_get_clean();
    dbDelta( $sql );
}

function depopulate_db() {
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    ob_start();
    require_once "lib/drop-tables.php";
    $sql = ob_get_clean();
    dbDelta( $sql );
}
    
?>
