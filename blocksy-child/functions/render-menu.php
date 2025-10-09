<?php
function render_menu_by_name($menu_name) {
    if (wp_get_nav_menu_object($menu_name)) {
        echo '<h2 class="fs-4" style="color: var(--theme-palette-color-8);">' . $menu_name . '</h2>';
        wp_nav_menu(array(
            'menu' => $menu_name,
            'container' => 'nav',
            'menu_class' => 'footer-menu',
            'depth' => 1,
        ));
    } else {
        echo '<!-- Menu "' . esc_html($menu_name) . '" not found -->';
    }
}
?>