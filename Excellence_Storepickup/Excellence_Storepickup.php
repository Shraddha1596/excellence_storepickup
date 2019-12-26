<?php
/**
 *
 * 
 */
/*
Plugin Name: Excellence_Storepickup
Plugin URI: http://wordpress.org/plugins/
Description: Plugin to add a stores for pickup.
Author: Excellence
Version: 1
Author URI: http://wordpress.org/plugins/
*/

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

function excellence_ship_activation() {
    
    /*  Check if WooCommerce is active*/
	if (!is_plugin_active('woocommerce/woocommerce.php') )
    {
        deactivate_plugins(plugin_basename(__FILE__));
        set_transient( 'fx-admin-notice-example', true, 5 );
        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }
    }
    include_once('excellence_pickup_functions.php');

}
add_action( 'init', 'excellence_ship_activation' );

/* Add admin notice */
add_action( 'admin_notices', 'excellence_ship_notice' );


function excellence_ship_notice(){

    /* Check transient, if available display notice */
    if( get_transient( 'fx-admin-notice-example' ) ){
        ?>
        <div id="message" class="notice notice-error notice is-dismissible">
            <p>For Excellence Shipping plugin to work please install and activate woocommerce plugin</p>
        </div>
        <?php
            /* Delete transient, only display this notice once. */
            delete_transient( 'fx-admin-notice-example' );
    }
}
 
function excellence_ship_install() {
 	excellence_ship_activation();
 
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'excellence_ship_install' );

function excellence_ship_deactivation() {
  
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'excellence_ship_deactivation' );
