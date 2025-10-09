<?php
/**
 * Helper: fetch a social URL from Blocksy customizer (kept in its own file for reuse).
 *
 * @param string $key Social key.
 * @return string URL or empty string.
 */
if ( ! function_exists( 'blocksy_social_url' ) ) {
    function blocksy_social_url( $key ) {
        // Try Blocksy helper if available
        if ( function_exists( 'blocksy_get_theme_mod' ) ) {
            $val = blocksy_get_theme_mod( $key, '' );
        }

        if ( is_string( $val ) ) {
            $val = trim( $val );
            return $val;
        }

        return '';
    }
}
