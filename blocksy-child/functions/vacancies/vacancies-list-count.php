<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // direct access
}

/**
 * Shortcode: [vacancies_list_count]
 * Renders a span with id 'vacancies-list-count'. JS will update this with the count of visible vacancies.
 */
function bchild_register_vacancies_list_count_shortcode() {
    $script_handle = 'bchild-vacancies-list-count';
    $script_src = get_stylesheet_directory_uri() . '/functions/vacancies/vacancies-list-count.js';
    wp_register_script( $script_handle, $script_src, array(), null, true );
    wp_enqueue_script( $script_handle );
}
add_action( 'wp_enqueue_scripts', 'bchild_register_vacancies_list_count_shortcode' );

function bchild_vacancies_list_count_shortcode( $atts ) {
    return '<span id="vacancies-list-count">0</span>';
}
add_shortcode( 'vacancies_list_count', 'bchild_vacancies_list_count_shortcode' );
