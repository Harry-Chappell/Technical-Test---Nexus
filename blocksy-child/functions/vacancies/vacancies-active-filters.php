<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // direct access
}

/**
 * Shortcode: [vacancies_active_filters]
 * Renders a div with id 'vacancies-active-filters'. JS will render Bootstrap button chips for each active filter.
 */
function bchild_register_vacancies_active_filters_shortcode() {
    $script_handle = 'bchild-vacancies-active-filters';
    $script_src = get_stylesheet_directory_uri() . '/functions/vacancies/vacancies-active-filters.js';
    wp_register_script( $script_handle, $script_src, array(), null, true );
    wp_enqueue_script( $script_handle );
}
add_action( 'wp_enqueue_scripts', 'bchild_register_vacancies_active_filters_shortcode' );

function bchild_vacancies_active_filters_shortcode( $atts ) {
    return '<div id="vacancies-active-filters" class="mb-3"></div>';
}
add_shortcode( 'vacancies_active_filters', 'bchild_vacancies_active_filters_shortcode' );
