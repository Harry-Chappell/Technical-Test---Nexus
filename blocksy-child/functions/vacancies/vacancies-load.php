<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // direct access
}

/**
 * Enqueue client JS on the page with slug 'vacancies'.
 * The script uses sessionStorage to cache the vacancies JSON and a version token.
 * It logs actions to the console to help debug why sessionStorage may not be
 * written in some environments.
 */
function bchild_enqueue_vacancies_client() {
    if ( ! is_page() ) {
        return;
    }

    global $post;
    if ( empty( $post ) || $post->post_name !== 'vacancies' ) {
        return;
    }

    // Prefer the latest timestamped vacancies*.json file if present, otherwise fall back to vacancies.json
    $theme_dir = get_stylesheet_directory();
    $pattern = $theme_dir . '/vacancies*.json';
    $files = glob( $pattern );
    $json_path = $theme_dir . '/vacancies.json';
    if ( ! empty( $files ) ) {
        // sort descending by filemtime and pick the newest
        usort( $files, function( $a, $b ) { return filemtime( $b ) - filemtime( $a ); } );
        $json_path = $files[0];
    }
    $json_url = str_replace( $theme_dir, get_stylesheet_directory_uri(), $json_path );
    $version   = file_exists( $json_path ) ? (string) filemtime( $json_path ) : '';

    $basename = basename( $json_path );
    $data = array(
        'jsonUrl' => $json_url,
        // expose the actual filename as the version token so the client can
        // compare filenames instead of filemtime values
        'filename' => $basename,
        'version' => $version,
    );

    // Print a small debug block on the vacancies page: filename, server path and URL
    // (visible only on the vacancies page). This helps confirm where the JSON is stored.
    // add_action( 'the_content', function( $content ) use ( $json_path, $json_url, $version ) {
    //     if ( ! is_main_query() ) return $content;
    //     $info  = '<div class="bchild-vacancies-info" style="border:1px solid #ddd;padding:8px;margin:8px 0;font-size:90%;">';
    //     $info .= '<strong>Vacancies JSON:</strong> ' . esc_html( basename( $json_path ) ) . '<br/>';
    //     $info .= '<strong>Server path:</strong> ' . esc_html( $json_path ) . '<br/>';
    //     $info .= '<strong>Public URL:</strong> <a href="' . esc_url( $json_url ) . '" target="_blank" rel="noopener">' . esc_html( $json_url ) . '</a><br/>';
    //     $info .= '<strong>Version token:</strong> ' . esc_html( $version );
    //     $info .= '</div>';
    //     return $info . $content;
    // }, 5 );

    wp_register_script( 'bchild-vacancies-client', '' );
    wp_enqueue_script( 'bchild-vacancies-client' );

    // Expose config synchronously before the main inline script.
    wp_add_inline_script( 'bchild-vacancies-client', 'window.bchildVacanciesConfig = ' . wp_json_encode( $data ) . ';', 'before' );

    // Main inline JS: manages sessionStorage caching and dispatches an event.
    // This script includes console.logs for easier debugging in the browser.
    $js = <<<'JS'
(function(){
    var cfg = window.bchildVacanciesConfig || {};
    var url = cfg.jsonUrl || '';
    // prefer the filename as the authoritative token; fallback to version (filemtime) if not present
    var serverFile = cfg.filename || cfg.version || '';
    var keyData = 'bchild_vacancies_json';
    var keyVer = 'bchild_vacancies_json_version';

    function safeSet(k, v){
        try{ sessionStorage.setItem(k, v); return true; }catch(e){ console.warn('bchild: sessionStorage.setItem failed', e); return false; }
    }

    function fetchAndStore(){
        if(!url) return Promise.reject(new Error('No URL'));
        return fetch(url, {cache:'no-store'})
            .then(function(r){ if(!r.ok) throw new Error('Bad response'); return r.text(); })
            .then(function(t){
                var ok = safeSet(keyData, t) && safeSet(keyVer, serverFile);
                if(ok){
                    try{ console.log('bchild: fetched and saved vacancies (file)', serverFile); }catch(e){}
                }
                try{ window.dispatchEvent(new CustomEvent('bchild:vacancies:loaded',{detail:{version:serverFile,from:'network'}})); }catch(e){}
                return t;
            });
    }

    try{
        var cachedVer = sessionStorage.getItem(keyVer) || '';
        var cachedJson = sessionStorage.getItem(keyData) || null;
        // if the stored filename/token matches the server filename, use the cached JSON
        if(cachedJson && cachedVer === serverFile){
            try{ console.log('bchild: using cached vacancies (file)', serverFile); }catch(e){}
            try{ window.dispatchEvent(new CustomEvent('bchild:vacancies:loaded',{detail:{version:serverFile,from:'session'}})); }catch(e){}
        } else {
            // server file differs (or no cache) -> fetch and overwrite sessionStorage
            fetchAndStore().catch(function(){
                console.warn('bchild: failed to fetch vacancies JSON', url);
                if(cachedJson){
                    try{ window.dispatchEvent(new CustomEvent('bchild:vacancies:loaded',{detail:{version:cachedVer,from:'session-fallback'}})); }catch(e){}
                }
            });
        }
    }catch(e){ console.warn('bchild: client error', e); }
})();
JS;

    wp_add_inline_script( 'bchild-vacancies-client', $js );
}
add_action( 'wp_enqueue_scripts', 'bchild_enqueue_vacancies_client' );


/**
 * Return count of vacancies by reading the vacancies.json file.
 * Falls back to DB count if file not available or invalid.
 */
function bchild_get_vacancies_count() {
    // Look for the latest timestamped vacancies*.json file (same logic as client)
    $theme_dir = get_stylesheet_directory();
    $pattern = $theme_dir . '/vacancies*.json';
    $files = glob( $pattern );
    $json_path = $theme_dir . '/vacancies.json';
    if ( ! empty( $files ) ) {
        usort( $files, function( $a, $b ) { return filemtime( $b ) - filemtime( $a ); } );
        $json_path = $files[0];
    }

    if ( file_exists( $json_path ) ) {
        $contents = @file_get_contents( $json_path );
        if ( $contents !== false ) {
            $data = json_decode( $contents, true );
            if ( is_array( $data ) ) {
                return count( $data );
            }
        }
    }

    $count = wp_count_posts( 'vacancies' );
    return isset( $count->publish ) ? intval( $count->publish ) : 0;
}

function bchild_vacancies_count_shortcode( $atts ) {
    // Server-side fallback count (used if JS is disabled or sessionStorage empty)
    $server_count = bchild_get_vacancies_count();
    // Unique ID so multiple shortcodes won't conflict
    static $idx = 0;
    $idx++;
    $id = 'bchild-vacancies-count-' . $idx;

    // Inline script that will try to read the cached JSON from sessionStorage
    // and update the placeholder with the client-side count. If no cached JSON
    // is found, it leaves the server-side count.
    $nonce_js = "(function(){try{var el=document.getElementById('$id');if(!el) return;var txt=sessionStorage.getItem('bchild_vacancies_json');if(txt){try{var data=JSON.parse(txt);if(Array.isArray(data)){el.textContent = data.length;return;}}catch(e){console.warn('bchild: parse cached vacancies failed', e);} } }catch(e){console.warn('bchild: vacancies_count script error', e);} })();";

    // Ensure the script runs after DOM is ready by using a short inline runner
    $script_tag = '<script>' . $nonce_js . '</script>';

    return '<span id="' . esc_attr( $id ) . '">' . esc_html( $server_count ) . '</span>' . $script_tag;
}
add_shortcode( 'vacancies_count', 'bchild_vacancies_count_shortcode' );
