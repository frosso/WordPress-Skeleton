<?php
/**
 * Plugin Name: Frosso: Network Fix
 * Description: Try to provide a better experience when working with WP Network.
 */

final class Frosso_Network_Fix {
    function __construct( ) {
        add_filter( 'network_admin_url', array(
            &$this,
            'get_network_admin_url'
        ), 10, 2 );
    }

    function get_network_admin_url( $url, $path ) {
        return str_replace( 'wp-admin/network/', 'wp/wp-admin/network/', $url );
    }

}

new Frosso_Network_Fix( );
