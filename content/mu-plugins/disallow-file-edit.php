<?php

! defined( 'ABSPATH' ) AND exit;
/** Plugin Name: Disable »Theme/Plugin Editor« */

// @url: http://wordpress.stackexchange.com/a/54210
if ( ! class_exists( 'disable_admin_editor' ) )
{
    add_action( 'plugins_loaded', array( 'disable_admin_editor', 'init' ) );

class disable_admin_editor
{
    public static $instance;

    public static function init()
    {
        null === self :: $instance AND self :: $instance = new self;
        return self :: $instance;
    }

    public function __construct()
    {
        ! defined( 'DISALLOW_FILE_EDIT' ) AND define( 'DISALLOW_FILE_EDIT', true );

        add_action( 'admin_init', array( $this, 'remove_edit_submenu' ) );
        add_filter( 'plugin_action_links', array( $this, 'remove_edit_action' ), 10, 2 );
        add_action( 'admin_footer-plugin-editor.php', array( $this, 'readonly_textarea' ) );
    }

    /**
     * Remove Edit submenu
     */
    public function remove_edit_submenu()
    {
        remove_submenu_page( 'plugins.php', 'plugin-editor.php' );
        remove_submenu_page( 'themes.php', 'theme-editor.php' );
    }

    /**
     * Remove the »Edit« link from all plugins
     * @param  array  $links
     * @param  string $file
     * @return array  $links
     */
    public function remove_edit_action( $links, $file ) 
    {
        unset( $links['edit'] );
        return $links;
    }

    /**
     * Makes the editor read-only and removes the Update button
     */
    public function readonly_textarea()
    {
        echo '<script type="text/javascript">
        jQuery(document).ready( function($) {
            $( "#newcontent" ).attr( "readonly", true );
            $( "input#submit" ).remove();
        } );     
        </script>';
    }
} // END Class 

} // endif;