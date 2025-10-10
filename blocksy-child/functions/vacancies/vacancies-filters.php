<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // direct access
}

/**
 * Shortcode: [vacancies_filters]
 * Renders a filters panel whose options are populated from the vacancies JSON
 * stored in sessionStorage (key: bchild_vacancies_json). The panel updates
 * and saves selected filters into sessionStorage under 'vacancies-filters.json'.
 */
function bchild_register_vacancies_filters_shortcode() {
    // Register and enqueue the vanilla JS file
    $script_handle = 'bchild-vacancies-filters';
    $script_src = get_stylesheet_directory_uri() . '/functions/vacancies/vacancies-filters.js';
    wp_register_script( $script_handle, $script_src, array(), null, true );
    wp_enqueue_script( $script_handle );

    // Expose keys so the JS can read/write the correct sessionStorage keys
    $config = array(
        'jsonKey'    => 'bchild_vacancies_json',
        'filtersKey' => 'vacancies-filters.json',
    );
    wp_add_inline_script( $script_handle, 'window.bchildVacanciesFiltersConfig = ' . wp_json_encode( $config ) . ';', 'before' );
}
add_action( 'wp_enqueue_scripts', 'bchild_register_vacancies_filters_shortcode' );


function bchild_vacancies_filters_shortcode( $atts ) {
    // Render the panel skeleton. JS will populate options from sessionStorage JSON.
    // We'll provide selects for three taxonomies and checkboxes for 'location'.

    $html  = '<div class="bchild-vacancies-filters" data-vacancies-filters="true">';
    $html .= '<div class="bchild-vacancies-filters-row">';
    $html .= '<label>Full / Part</label><select id="vacancies-filter-full-part" data-tax="full-part" class="bchild-v-filter-select"><option value="">(any)</option></select>';
    $html .= '</div>';
    $html .= '<div class="bchild-vacancies-filters-row">';
    $html .= '<label>Job Type</label><select id="vacancies-filter-job-type" data-tax="job-type" class="bchild-v-filter-select"><option value="">(any)</option></select>';
    $html .= '</div>';
    $html .= '<div class="bchild-vacancies-filters-row">';
    $html .= '<label>Sector</label><select id="vacancies-filter-sector" data-tax="sector" class="bchild-v-filter-select"><option value="">(any)</option></select>';
    $html .= '</div>';

    // Locations: checkboxes container
    $html .= '<div class="bchild-vacancies-filters-row">';
    $html .= '<fieldset><legend>Location</legend><div id="vacancies-filter-location-list" class="bchild-v-locations"></div></fieldset>';
    $html .= '</div>';

    $html .= '<div class="bchild-vacancies-filters-actions">';
    $html .= '<button type="button" class="bchild-v-clear">Clear</button>';
    $html .= '</div>';
    $html .= '</div>';

    return $html;
}
add_shortcode( 'vacancies_filters', 'bchild_vacancies_filters_shortcode' );
