<?php

// ===================================================
// Load database info and local development parameters
// ===================================================

include_once dirname( __FILE__ ) . '/get-envinronment.php';
if ( !defined( 'DEV_ENV' ) ) {
    die( 'DEV_ENV not defined' );
}

if ( DEV_ENV == Envinronments::local ) {
    define( 'WP_LOCAL_DEV', true );
    include (dirname( __FILE__ ) . '/local-config.php');
} elseif ( DEV_ENV == Envinronments::staging ) {
    define( 'WP_LOCAL_DEV', false );
    define( 'DB_NAME', 'remediabasewp' );
    define( 'DB_USER', 'remediabasewp' );
    define( 'DB_PASSWORD', 'remediabasewp' );
    define( 'DB_HOST', 'localhost' );
    // Probably 'localhost'
    // ===========
    // Hide errors
    // ===========
    define( 'WP_DEBUG', false );
    define( 'WP_DEBUG_DISPLAY', false );

    /**
     * Dove mettiamo Wordpress? (in produzione/staging probabilmente si trova nella root del sito)
     */
    define( 'WP_SITEURL', 'http://' . $_SERVER['SERVER_NAME'] . '/wp' );
    define( 'WP_HOME', 'http://' . $_SERVER['SERVER_NAME'] );
    // ========================
    // Custom Content Directory
    // ========================
    define( 'WP_CONTENT_DIR', dirname( __FILE__ ) . '/content' );
    define( 'WP_CONTENT_URL', 'http://' . $_SERVER['SERVER_NAME'] . '/content' );
}


// ================================================
// You almost certainly do not want to change these
// ================================================
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );

// ==============================================================
// Salts, for security
// Grab these from: https://api.wordpress.org/secret-key/1.1/salt
// ==============================================================
define( 'AUTH_KEY',         'put your unique phrase here' );
define( 'SECURE_AUTH_KEY',  'put your unique phrase here' );
define( 'LOGGED_IN_KEY',    'put your unique phrase here' );
define( 'NONCE_KEY',        'put your unique phrase here' );
define( 'AUTH_SALT',        'put your unique phrase here' );
define( 'SECURE_AUTH_SALT', 'put your unique phrase here' );
define( 'LOGGED_IN_SALT',   'put your unique phrase here' );
define( 'NONCE_SALT',       'put your unique phrase here' );

// ==============================================================
// Table prefix
// Change this if you have multiple installs in the same database
// ==============================================================
$table_prefix  = 'wp_';

// ================================
// Language
// Leave blank for American English
// ================================
define( 'WPLANG', '' );

// =================================================================
// Debug mode
// Debugging? Enable these. Can also enable them in local-config.php
// =================================================================
// define( 'SAVEQUERIES', true );
// define( 'WP_DEBUG', true );

// ======================================
// Load a Memcached config if we have one
// ======================================
// if ( file_exists( dirname( __FILE__ ) . '/memcached.php' ) )
    // $memcached_servers = include( dirname( __FILE__ ) . '/memcached.php' );

// ===================
// Bootstrap WordPress
// ===================
if ( !defined( 'ABSPATH' ) )
    define( 'ABSPATH', dirname( __FILE__ ) . '/wp/' );
require_once( ABSPATH . 'wp-settings.php' );