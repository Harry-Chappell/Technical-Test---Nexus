<?php
/**
 * Render social links HTML for the custom footer (prefers child-theme assets, falls back to Blocksy metadata).
 *
 * @param array $args {
 *   @type array|string $excludes Array of social keys to exclude or comma-separated string.
 * }
 * @return string HTML markup
 */
if ( ! function_exists( 'blocksy_render_social_links' ) ) {
    function blocksy_render_social_links( $args = array() ) {
        $args = wp_parse_args( $args, array(
            'excludes' => array(),
        ) );

        // Normalize excludes to array
        $excludes = $args['excludes'];
        if ( is_string( $excludes ) ) {
            $excludes = array_filter( array_map( 'trim', explode( ',', $excludes ) ) );
        }
        if ( ! is_array( $excludes ) ) {
            $excludes = array();
        }

        // Determine social keys
        if ( function_exists( 'blocksy_get_social_networks_list' ) ) {
            $networks = blocksy_get_social_networks_list();
            $social_keys = is_array( $networks ) ? array_keys( $networks ) : array();
        }

        $output = '';

        foreach ( $social_keys as $key ) {
            if ( in_array( $key, $excludes, true ) ) {
                continue;
            }

            // Resolve URL (expects blocksy_social_url to exist)
            $url = function_exists( 'blocksy_social_url' ) ? blocksy_social_url( $key ) : '';
            if ( empty( $url ) ) {
                continue;
            }

            // Label: prefer Blocksy networks list label when available
            $label = isset( $networks[ $key ]['label'] ) ? $networks[ $key ]['label'] : ucfirst( $key );

            // Icon: prefer child theme assets; fall back to Blocksy metadata icon when asset missing
            $icon_markup = '';
            if ( function_exists( 'inline_svg_from_assets' ) ) {
                $icon_markup = inline_svg_from_assets( $key . '.svg', 'width:20px;height:20px;' );
            }

            if ( empty( trim( $icon_markup ) ) && function_exists( 'blocksy_get_social_metadata' ) ) {
                $meta = blocksy_get_social_metadata( array( 'social' => $key, 'type' => 'url' ) );
                if ( is_array( $meta ) && ! empty( $meta['icon'] ) ) {
                    $icon_markup = $meta['icon'];
                }
            }

            $output .= '<a href="' . esc_url( $url ) . '" target="_blank" rel="noopener noreferrer" aria-label="' . esc_attr( $label ) . '">';
            $output .= $icon_markup;
            $output .= '</a>';
        }

        return $output;
    }
}
