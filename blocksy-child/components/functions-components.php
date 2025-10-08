<?php
if ( !defined( 'WP_DEBUG' ) ) {
    die( 'Direct access forbidden.' );
}

define('NAMESPACE_PREFIX', 'hcdigital');
define('STORE_POST_TYPE', 'store');
define('MAIN_STORE_CATEGORY_TAXONOMY', 'store-category');
define('OPEN_HOURS_TAXONOMY', 'opening-hour');
define('THEME_PATH', get_stylesheet_directory());
define('THEME_URL', get_stylesheet_directory_uri());

$upload_dir = wp_upload_dir();
$json_path = $upload_dir['basedir'] . '/json';
$json_url = $upload_dir['baseurl'] . '/json';

if (!is_dir($json_path)) {
    wp_mkdir_p($json_path);
}

define('JSON_PATH', $json_path);
define('JSON_URL', $json_url);

require_once THEME_PATH . '/components/swiper-slider/functions-swiper-slider.php';
require_once THEME_PATH . '/components/forms/functions-forms.php';
