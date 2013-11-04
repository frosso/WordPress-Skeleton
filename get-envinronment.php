<?php

/**
 * Ambienti di sviluppo disponibili
 */
class Envinronments {
    const local = 'lo';
    const staging = 'st';
    const production = 'pr';
}

if ( file_exists( dirname( __FILE__ ) . '/local-config.php' ) ) {
    define( 'DEV_ENV', Envinronments::local );
} elseif ( strpos( $_SERVER['SERVER_NAME'], 'remedia.me' ) !== false ) {
    define( 'DEV_ENV', Envinronments::staging );
} else {
    define( 'DEV_ENV', Envinronments::production );
}
