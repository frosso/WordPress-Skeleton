<?php
/**
 * Plugin Name: Frosso: Theme Fix
 * Description: Miglioramenti al tema
 */

final class Frosso_Theme_Fix {
    function __construct( ) {
        add_filter( 'admin_init', array(
            &$this,
            'remove_dashboard_widgets'
        ) );
        add_action( 'init', array(
            &$this,
            'head_cleanup'
        ) );
        add_filter( 'body_class', array(
            &$this,
            'body_class'
        ) );
        add_filter( 'wp_enqueue_scripts', array(
            &$this,
            'enqueue_scripts'
        ), 0 );
    }

    function enqueue_scripts( ) {
        // jQuery is loaded using the same method from HTML5 Boilerplate:
        // Grab Google CDN's latest jQuery with a protocol relative URL; fallback
        // to local if offline
        // It's kept in the header instead of footer to avoid conflicts with
        // plugins.
        if ( !is_admin( ) ) {
            wp_deregister_script( 'jquery' );
            wp_register_script( 'jquery', '//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js', false, null, true );
            add_filter( 'script_loader_src', array(
                &$this,
                'jquery_local_fallback'
            ), 10, 2 );
            wp_enqueue_script( 'jquery' );
        }
    }

    // http://wordpress.stackexchange.com/a/12450
    function jquery_local_fallback( $src, $handle ) {
        static $add_jquery_fallback = false;

        if ( $add_jquery_fallback ) {
            // echo '
            // <script>
            // window.jQuery || document.write(\'<script src="' .
            // get_template_directory_uri() .
            // '/assets/js/vendor/jquery-1.10.1.min.js"><\/script>\')
            // </script>' . "\n"; //TODO: definire l'url di jQuery
            $add_jquery_fallback = false;
        }

        if ( $handle === 'jquery' ) {
            $add_jquery_fallback = true;
        }

        return $src;
    }

    /**
     * Remove unnecessary dashboard widgets
     *
     * @link
     * http://www.deluxeblogtips.com/2011/01/remove-dashboard-widgets-in-wordpress.html
     */
    function remove_dashboard_widgets( ) {
        remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_primary', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_secondary', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_quick_press', 'dashboard', 'normal' );
    }

    /**
     * Clean up wp_head()
     *
     * Remove unnecessary <link>'s
     * Remove inline CSS used by Recent Comments widget
     * Remove inline CSS used by posts with galleries
     * Remove self-closing tag and change ''s to "'s on rel_canonical()
     */
    function head_cleanup( ) {
        // remove junk from head
        remove_action( 'wp_head', 'index_rel_link' );
        remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
        remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
        remove_action( 'wp_head', 'adjacent_posts_rel_link', 10, 0 );

        // Originally from http://wpengineer.com/1438/wordpress-header/
        remove_action( 'wp_head', 'feed_links', 2 );
        remove_action( 'wp_head', 'feed_links_extra', 3 );
        remove_action( 'wp_head', 'rsd_link' );
        remove_action( 'wp_head', 'wlwmanifest_link' );
        remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
        remove_action( 'wp_head', 'wp_generator' );
        remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );

        global $wp_widget_factory;
        if ( isset( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'] ) ) {
            remove_action( 'wp_head', array(
                $wp_widget_factory->widgets['WP_Widget_Recent_Comments'],
                'recent_comments_style'
            ) );
        }

        add_filter( 'use_default_gallery_style', '__return_null' );

        if ( !class_exists( 'WPSEO_Frontend' ) ) {
            remove_action( 'wp_head', 'rel_canonical' );
            add_action( 'wp_head', array(
                &$this,
                'rel_canonical'
            ) );
        }
    }

    function rel_canonical( ) {
        global $wp_the_query;

        if ( !is_singular( ) ) {
            return;
        }

        if ( !$id = $wp_the_query->get_queried_object_id( ) ) {
            return;
        }

        $link = get_permalink( $id );
        echo "\t<link rel=\"canonical\" href=\"$link\">\n";
    }

    /**
     * Add and remove body_class() classes
     */
    function body_class( $classes ) {
        // Add post/page slug
        if ( is_single( ) || is_page( ) && !is_front_page( ) ) {
            $classes[] = basename( get_permalink( ) );
        }

        // Remove unnecessary classes
        $home_id_class = 'page-id-' . get_option( 'page_on_front' );
        $remove_classes = array(
            'page-template-default',
            $home_id_class
        );
        $classes = array_diff( $classes, $remove_classes );

        return $classes;
    }

}

new Frosso_Theme_Fix( );
