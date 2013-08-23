<?php
/*
Plugin Name: Frosso custom login URL
Description: Cambia l'URL login in /admin/
Version: 0.1
Author: Frosso
*/

// Add rewrite rule and flush on plugin activation
register_activation_hook( __FILE__, 'wp_frosso_login_URL_activate' );
function wp_frosso_login_URL_activate() {
    wp_frosso_login_URL_rewrite();
    flush_rewrite_rules();
}
 
// Flush on plugin deactivation
register_deactivation_hook( __FILE__, 'wp_frosso_login_URL_deactivate' );
function wp_frosso_login_URL_deactivate() {
    flush_rewrite_rules();
}
 
// Create new rewrite rule
add_action( 'init', 'wp_frosso_login_URL_rewrite' );
function wp_frosso_login_URL_rewrite() {
    add_rewrite_rule( 'admin/?$', '/wp/wp-login.php', 'top' );
}