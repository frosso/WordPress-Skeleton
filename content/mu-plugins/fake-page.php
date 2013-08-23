<?php
/**
 * Plugin Name: Frosso: fake page
 * Description: pagina 'dummy'
 */

// add a fake page to your blog for whatever reason
// you need - extra plugin page, etc.
// set slug to whatever you want the page URL to be
// ex: yoursite.com/fake/

if ( WP_LOCAL_DEV == true ) {

    //Make a fake page for wishlists to use
    add_filter( 'the_posts', 'my_fake_page' );

    function my_fake_page( $posts ) {
        global $wp, $wp_query;
        $page_slug = 'fake';
        $page_title = 'Fake Page';
        $page_content = '';
        $array_bloginfo = array(
            "admin_email",
            "atom_url",
            "charset",
            "comments_atom_url",
            "comments_atom_url",
            "comments_rss2_url",
            "description",
            "html_type",
            "language",
            "name",
            "pingback_url",
            "rdf_url",
            "stylesheet_directory",
            "stylesheet_url",
            "template_directory",
            "template_url",
            "text_direction",
            "url",
            "version",
            "wpurl",
        );
        foreach ( $array_bloginfo as $value ) {
            $page_content .= "get_bloginfo('$value') = " . get_bloginfo( $value ) . "<br />";
        }
        $page_content .= "get_stylesheet_uri() = " . get_stylesheet_uri( ) . "<br />";
        $page_content .= "get_stylesheet_directory_uri() = " . get_stylesheet_directory_uri( ) . "<br />";
        $page_content .= "get_stylesheet() = " . get_stylesheet( ) . "<br />";
        $page_content .= "get_theme_root_uri( get_stylesheet() ) = " . get_theme_root_uri( get_stylesheet( ) ) . "<br />";
        $page_content .= "apply_filters( 'stylesheet_directory_uri' ) = " . apply_filters( 'stylesheet_directory_uri', get_theme_root_uri( get_stylesheet( ) ) . "/" . get_stylesheet( ), get_stylesheet( ), get_theme_root_uri( get_stylesheet( ) ) ) . "<br />";
        $page_content .= "site_url() = " . site_url( ) . "<br />";
        $page_content .= "admin_url() = " . admin_url( ) . "<br />";
        $page_content .= "includes_url() = " . includes_url( ) . "<br />";
        $page_content .= "content_url() = " . content_url( ) . "<br />";
        $page_content .= "plugins_url() = " . plugins_url( ) . "<br />";
        $page_content .= "wp_upload_dir() = <pre>" . print_r( wp_upload_dir( ), 1 ) . "</pre><br />";

        //check if user is requesting our fake page
        if ( count( $posts ) == 0 && (strtolower( $wp->request ) == $page_slug || (isset( $wp->query_vars['page_id'] ) && $wp->query_vars['page_id'] == $page_slug)) ) {

            //create a fake post
            $post = new stdClass;
            $post->post_author = 1;
            $post->post_name = $page_slug;

            // fix undefined index
            $post->post_type = 'page';
            $post->post_parent = 0;
            // end fix undefined index

            $post->guid = get_bloginfo( 'wpurl' . '/' . $page_slug );
            $post->post_title = $page_title;
            //put your custom content here
            $post->post_content = $page_content;
            //just needs to be a number - negatives are fine
            $post->ID = -42;
            $post->post_status = 'static';
            $post->comment_status = 'closed';
            $post->ping_status = 'closed';
            $post->comment_count = 0;
            //dates may need to be overwritten if you have a "recent posts" widget or similar - set to whatever you want
            $post->post_date = current_time( 'mysql' );
            $post->post_date_gmt = current_time( 'mysql', 1 );

            $posts = NULL;
            $posts[] = $post;

            $wp_query->is_page = true;
            $wp_query->is_singular = true;
            $wp_query->is_home = false;
            $wp_query->is_archive = false;
            $wp_query->is_category = false;
            unset( $wp_query->query["error"] );
            $wp_query->query_vars["error"] = "";
            $wp_query->is_404 = false;
        }

        return $posts;
    }

    add_action( 'parse_query', 'wpse_71157_parse_query' );
    function wpse_71157_parse_query( $wp_query ) {
        if ( $wp_query->is_post_type_archive && $wp_query->is_tax )
            $wp_query->is_post_type_archive = false;
    }

}
