<?php
/*
 This is a sample local-config.php file
 In it, you *must* include the four main database defines

 You may include other settings here that you only want enabled on your local
 development checkouts
 */

define( 'DB_NAME', 'local_db_name' );
define( 'DB_USER', 'local_db_user' );
define( 'DB_PASSWORD', 'local_db_password' );
define( 'DB_HOST', 'localhost' );
// Probably 'localhost'

// ini_set( 'display_errors', 0 );
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_DISPLAY', true );
define( 'SAVEQUERIES', true );

/**
 * Dove mettiamo Wordpress?
 */
define( 'SOTTOCARTELLA_INSTALLAZIONE', '/sito' );
define( 'WP_HOME', 'http://' . $_SERVER['SERVER_NAME'] . SOTTOCARTELLA_INSTALLAZIONE );
define( 'WP_SITEURL', WP_HOME . '/wp' );
// ========================
// Custom Content Directory
// ========================
define( 'WP_CONTENT_DIR', dirname( __FILE__ ) . '/content' );
define( 'WP_CONTENT_URL', WP_HOME . '/content' );
