<?php

function custom_footer_shortcode() {
    ob_start();
    ?>

    <?php
    /**
     * Helper: inline an SVG from the theme's assets folder.
     * Uses file_get_contents() on the server filesystem so the SVG is output inline.
     *
     * @param string $filename SVG filename relative to the theme's `assets/` directory.
     * @param string $style Inline style to apply to the <svg> element.
     * @return string Safe SVG markup (or empty string on failure).
     */
    if ( ! function_exists( 'inline_svg_from_assets' ) ) {
        function inline_svg_from_assets( $filename, $style = '' ) {
        $base_dir = get_stylesheet_directory(); // absolute filesystem path to child theme
        $file_path = $base_dir . '/assets/' . ltrim( $filename, "/" );

        if ( ! file_exists( $file_path ) ) {
            return '';
        }

        $svg = file_get_contents( $file_path );
        if ( false === $svg ) {
            return '';
        }

        // Inject inline style, role and accessibility attributes on the top-level <svg>
        // This is a simple string replace - assumes the SVG starts with '<svg'
        $attrs = '';
        if ( $style ) {
            $attrs .= ' style="' . esc_attr( $style ) . '"';
        }
        $attrs .= ' role="img" aria-hidden="true" focusable="false"';

        // Insert attributes after the opening '<svg'
        $svg = preg_replace( '/^<svg(\s+)/i', '<svg$1' . $attrs . ' ', $svg, 1 );

        return $svg;
        }
    }

    ?>


    <div class="row">
        <div class="col"></div>
        <div class="col"></div>
        <div class="col"></div>
        <div class="col"></div>
    </div>
    <div class="row" id="socials">
        
        <?php
        // Load helper that fetches social URLs and renderer
        require_once get_stylesheet_directory() . '/functions/blocksy-social.php';
        require_once get_stylesheet_directory() . '/functions/render-socials.php';
    
        // Define networks to exclude
        $shortcode_atts = array( 'excludes' => array( 'phone', 'email' ) );
        
        // Determine social keys: prefer Blocksy's full list when available
        if ( function_exists( 'blocksy_get_social_networks_list' ) ) {
            $networks = blocksy_get_social_networks_list();
            $social_keys = is_array( $networks ) ? array_keys( $networks ) : array();
        }

        // Render socials using the reusable renderer. Allows passing 'excludes' via shortcode attributes.
        // Shortcode attributes can be overridden by external code before output if needed.
        echo blocksy_render_social_links( array( 'excludes' => $shortcode_atts['excludes'] ) );
        ?>


    </div>

    <?php
    return ob_get_clean();
}
add_shortcode('custom_footer', 'custom_footer_shortcode');