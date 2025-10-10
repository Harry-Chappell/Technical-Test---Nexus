<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // direct access
}

/**
 * Shortcode: [vacancies_list]
 * Renders an empty div with id 'vacancies-list'. JS will render Bootstrap cards for each vacancy from the JSON.
 */
function bchild_register_vacancies_list_shortcode() {
    $script_handle = 'bchild-vacancies-list';
    $script_src = get_stylesheet_directory_uri() . '/functions/vacancies/vacancies-list.js';
    wp_register_script( $script_handle, $script_src, array(), null, true );
    wp_enqueue_script( $script_handle );

    // Expose config for JS
    $config = array(
        'jsonKey'    => 'bchild_vacancies_json',
        'filtersKey' => 'vacancies-filters.json',
    );
    wp_add_inline_script( $script_handle, 'window.bchildVacanciesListConfig = ' . wp_json_encode( $config ) . ';', 'before' );
}
add_action( 'wp_enqueue_scripts', 'bchild_register_vacancies_list_shortcode' );

function bchild_vacancies_list_shortcode( $atts ) {
    return '<div id="vacancies-list"></div>';
}
add_shortcode( 'vacancies_list', 'bchild_vacancies_list_shortcode' );
