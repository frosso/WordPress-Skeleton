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
        add_filter( 'show_admin_bar', '__return_false' );
        add_filter( 'wp_enqueue_scripts', array(
            &$this,
            'enqueue_scripts'
        ), 0 );
        //add_action('after_setup_theme', array(&$this, 'after_setup_theme'));
    }

    function enqueue_scripts( ) {
        // jQuery is loaded using the same method from HTML5 Boilerplate:
        // Grab Google CDN's latest jQuery with a protocol relative URL; fallback to local if offline
        // It's kept in the header instead of footer to avoid conflicts with plugins.
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
            // window.jQuery || document.write(\'<script src="' . get_template_directory_uri() . '/assets/js/vendor/jquery-1.10.1.min.js"><\/script>\')
            // </script>' . "\n"; //tODO: definire l'url di jQuery
            $add_jquery_fallback = false;
        }

        if ( $handle === 'jquery' ) {
            $add_jquery_fallback = true;
        }

        return $src;
    }

    function after_setup_theme( ) {
        $get_theme_name = explode( '/themes/', get_template_directory( ) );
        define( 'THEME_NAME', next( $get_theme_name ) );
        define( 'THEME_PATH', RELATIVE_CONTENT_PATH . '/themes/' . THEME_NAME );

        // Abilito features tema
        add_theme_support( 'rewrites' );
        // Enable URL rewrites
        add_theme_support( 'root-relative-urls' );
        // Enable relative URLs

        if ( !is_multisite( ) && !is_child_theme( ) ) {
            if ( current_theme_supports( 'rewrites' ) ) {
                add_action( 'generate_rewrite_rules', array(
                    &$this,
                    'add_rewrites'
                ) );
            }

            if ( !is_admin( ) && current_theme_supports( 'rewrites' ) ) {
                $tags = array(
                    'plugins_url',
                    'bloginfo',
                    'stylesheet_directory_uri',
                    'template_directory_uri',
                    'script_loader_src',
                    'style_loader_src'
                );

                foreach ( $tags as $tag ) {
                    add_filter( $tag, array(
                        &$this,
                        'clean_urls'
                    ) );
                }

            }
        }
    }

    /**
     * Remove unnecessary dashboard widgets
     *
     * @link http://www.deluxeblogtips.com/2011/01/remove-dashboard-widgets-in-wordpress.html
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

    function clean_urls( $content ) {
        if ( strpos( $content, RELATIVE_PLUGIN_PATH ) > 0 ) {
            return str_replace( '/' . RELATIVE_PLUGIN_PATH, '/plugins', $content );
        } else {
            return str_replace( '/' . THEME_PATH, '', $content );
        }
    }

    /**
     * URL rewriting
     *
     * Rewrites currently do not happen for child themes (or network installs)
     * @todo https://github.com/retlehs/roots/issues/461
     *
     * Rewrite:
     *   /wp-content/themes/themename/css/ to /css/
     *   /wp-content/themes/themename/js/  to /js/
     *   /wp-content/themes/themename/img/ to /img/
     *   /wp-content/plugins/              to /plugins/
     *
     * If you aren't using Apache, alternate configuration settings can be found in the docs.
     *
     * @link https://github.com/retlehs/roots/blob/master/doc/rewrites.md
     */
    function add_rewrites( $content ) {
        global $wp_rewrite;
        $new_non_wp_rules = array(
            'assets/css/(.*)' => THEME_PATH . '/assets/css/$1',
            'assets/js/(.*)' => THEME_PATH . '/assets/js/$1',
            'assets/img/(.*)' => THEME_PATH . '/assets/img/$1',
            'plugins/(.*)' => RELATIVE_PLUGIN_PATH . '/$1'
        );
        $wp_rewrite->non_wp_rules = array_merge( $wp_rewrite->non_wp_rules, $new_non_wp_rules );
        return $content;
    }

}

// Define helper constants
define( 'RELATIVE_PLUGIN_PATH', str_replace( home_url( ) . '/', '', plugins_url( ) ) );
define( 'RELATIVE_CONTENT_PATH', str_replace( home_url( ) . '/', '', content_url( ) ) );

new Frosso_Theme_Fix( );
