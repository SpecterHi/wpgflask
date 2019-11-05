<?php
/*
Plugin Name: Gravity Forms Data export Add-On
Plugin URI: http://www.gravityforms.com
Description: A Data export add-on to demonstrate the use of the Add-On Framework
Version: 1.0
Author: lbr
Author URI: https://github.com/liubingren
*/

define( 'GF_Data_Export_ADDON_VERSION', '2.1' );

add_action( 'gform_loaded', array( 'GF_Simple_AddOn_Bootstrap', 'load' ), 5 );

class GF_Simple_AddOn_Bootstrap {

    public static function load() {

        if ( ! method_exists( 'GFForms', 'include_addon_framework' ) ) {
            return;
        }

        require_once( 'class-gfdataexportaddon.php' );

        GFAddOn::register( 'GFDataExportAddOn' );
    }

}

function gf_data_export_addon() {
    return GFDataExportAddOn::get_instance();
}