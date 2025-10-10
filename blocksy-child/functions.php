<?php
if ( !defined( 'WP_DEBUG' ) ) {
    die( 'Direct access forbidden.' );
}
add_action( 'wp_enqueue_scripts', function () {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
} );
add_action( 'wp_enqueue_scripts', function () {
    // Enqueue Bootstrap from CDN (change version here to update)
    $bootstrap_version = '5.0.2';
    $bootstrap_css_cdn = "https://cdn.jsdelivr.net/npm/bootstrap@{$bootstrap_version}/dist/css/bootstrap.min.css";
    $bootstrap_js_cdn = "https://cdn.jsdelivr.net/npm/bootstrap@{$bootstrap_version}/dist/js/bootstrap.bundle.min.js";

    wp_enqueue_style( 'bootstrap-css', $bootstrap_css_cdn, array(), $bootstrap_version );

    // prefer compiled child CSS in /dist/style.css when available; load after Bootstrap so it can override
    $compiled = get_stylesheet_directory() . '/dist/style.css';
    if ( file_exists( $compiled ) ) {
        wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/dist/style.css', array( 'parent-style', 'bootstrap-css' ), filemtime( $compiled ) );
    } else {
        // fallback to the theme's style.css (useful before running the build step)
        wp_enqueue_style( 'child-style', get_stylesheet_uri(), array( 'parent-style', 'bootstrap-css' ) );
    }

    // Enqueue Bootstrap JS bundle (includes Popper). Load in footer.
    wp_enqueue_script( 'bootstrap-js', $bootstrap_js_cdn, array(), $bootstrap_version, true );
    //wp_register_style('legacyCss',  get_stylesheet_directory_uri() . '/legacy/style.css', array(), null, 'all');
    //wp_enqueue_style('legacyCss');
} );

remove_action( 'wp_head', 'feed_links_extra', 3 );
remove_action( 'wp_head', 'feed_links', 2 );
remove_action( 'wp_head', 'index_rel_link' );
remove_action( 'wp_head', 'wp_generator' );

function itsme_disable_feed() {
    wp_safe_redirect( get_bloginfo( 'url' ), 301 );
    exit;
}
add_action( 'do_feed', 'itsme_disable_feed', 1 );
add_action( 'do_feed_rdf', 'itsme_disable_feed', 1 );
add_action( 'do_feed_rss', 'itsme_disable_feed', 1 );
add_action( 'do_feed_rss2', 'itsme_disable_feed', 1 );
add_action( 'do_feed_atom', 'itsme_disable_feed', 1 );
add_action( 'do_feed_rss2_comments', 'itsme_disable_feed', 1 );
add_action( 'do_feed_atom_comments', 'itsme_disable_feed', 1 );
add_action( 'after_setup_theme', 'remove_admin_bar' );

function remove_admin_bar() {
    if ( !current_user_can( 'administrator' ) && !is_admin() ) {
        show_admin_bar( false );
    }
}




// PSR-4 autoloading: prefer composer's autoload if available
$composer_autoload = __DIR__ . '/vendor/autoload.php';
if ( file_exists( $composer_autoload ) ) {
    require_once $composer_autoload;
}

// Fallback: provide a minimal autoloader for the BlocksyChild namespace if composer isn't installed.
spl_autoload_register( function ( $class ) {
    $prefix = 'BlocksyChild\\';
    $base_dir = __DIR__ . '/src/';

    // does the class use the namespace prefix?
    $len = strlen( $prefix );
    if ( strncmp( $prefix, $class, $len ) !== 0 ) {
        return;
    }

    // get the relative class name
    $relative_class = substr( $class, $len );

    // replace namespace separators with directory separators, append with .php
    $file = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';

    if ( file_exists( $file ) ) {
        require $file;
    }
} );

// Instantiate and register the PageColorOverrides class if it exists
if ( class_exists( '\\BlocksyChild\\PageColorOverrides' ) ) {
    $overrides = new \BlocksyChild\PageColorOverrides();
    add_action( 'wp_head', [ $overrides, 'output' ], 100 );
}



require_once __DIR__ . '/components/functions-components.php';
require_once __DIR__ . '/functions/custom_footer.php';
// Vacancies JSON generator
require_once __DIR__ . '/functions/vacancies-json.php';