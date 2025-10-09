<?php

function custom_footer_shortcode() {
    // Load helper that fetches social URLs and renderer
    require_once get_stylesheet_directory() . '/functions/blocksy-social.php';
    require_once get_stylesheet_directory() . '/functions/render-menu.php';
    require_once get_stylesheet_directory() . '/functions/render-socials.php';


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


    <div class="row mt-5">
        <div class="col d-flex flex-column gap-1">
            <img id="footer-logo" src="https://harrych.app/ell/nexus/wp-content/uploads/2025/10/Nexus-logo-Over-dark.svg" alt="Nexus Logo" class="mb-5">
            <a id="get-in-touch" href="#" class="mb-2">Get in Touch</a>
            <a id="phone-link" href="tel:01912020747" class="fs-2">0191 20 20 747</a>
            <span id="open-times" class="small">09:00 to 17:00 - Mon to Fri</span>
        </div>

        <div class="col">
            <?php render_menu_by_name('About'); ?>
            <?php render_menu_by_name('Partnerships'); ?>
        </div>
        <div class="col">
            <?php render_menu_by_name('Careers'); ?>
        </div>
        <div class="col">
            <?php render_menu_by_name('News and Media Hub'); ?>
            <?php render_menu_by_name('Contact Nexus'); ?>
        </div>
    </div>
    <div class="row d-flex flex-column mb-0">
        <div id="socials" class="d-flex">

            <?php
        
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

        <p class="copyright">© Nexus Tyne and Wear <?php echo date('Y'); ?>. All rights reserved.</p>
    </div>

    <?php
    return ob_get_clean();
}
add_shortcode('custom_footer', 'custom_footer_shortcode');