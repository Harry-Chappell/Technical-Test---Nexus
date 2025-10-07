<?php
namespace BlocksyChild;

if ( !defined( 'WP_DEBUG' ) ) {
    // Allow inclusion in WP context only — keep parity with functions.php guard.
    return;
}

class PageColorOverrides {
    /**
     * Attachable method to output CSS custom properties for page overrides.
     * This mirrors the original procedural function but lives in a namespaced class.
     */
    public function output() {
        if ( ! is_page() ) {
            return; // only apply on pages
        }

        $color1 = get_post_meta( get_queried_object_id(), 'color-1', true );
        $color2 = get_post_meta( get_queried_object_id(), 'color-2', true );

        // Exit early if no custom colors set
        if ( ! $color1 && ! $color2 ) {
            return;
        }

        echo '<style>:root {';
        if ( $color1 ) {
            echo '--theme-palette-color-1:' . esc_attr( $color1 ) . ';';
        }
        if ( $color2 ) {
            echo '--theme-palette-color-2:' . esc_attr( $color2 ) . ';';
        }
        echo '}</style>';
    }
}
