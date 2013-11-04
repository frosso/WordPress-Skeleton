<?php

class DisableAuthorUrl {
    function __construct( ) {
        add_action( 'template_redirect', array(
            &$this,
            'redirect'
        ) );
    }

    function redirect( ) {
        if ( is_author( ) ) {
            wp_redirect( home_url( ), 302 );
            exit ;
        }
    }

}

new DisableAuthorUrl( );
