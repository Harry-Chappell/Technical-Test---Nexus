<?php
if (!defined('ABSPATH')) {
    exit;
}

global $selected_content_block_id;
$selected_content_block_id = null;
add_filter('blocksy:posts-listing:cards:custom-output', function ($output) {
    global $selected_content_block_id;
    $hook_id = intval($selected_content_block_id);
    if ($hook_id) {
        $atts = blocksy_get_post_options($hook_id);
        $data = ['output' => blc_render_content_block($hook_id)];
        return $data;
    }
    return $output;
}, 999);
function hcdigital_register_swiper_slider_block() {
    
    $dir_path = __DIR__;
    $relative_path = str_replace(THEME_PATH, '', $dir_path);
    $dir_url = THEME_URL . $relative_path;
    wp_register_script(
        'hcdigital-swiper-slider-block-editor',
        $dir_url . '/block/block-swiper-slider.js',
        ['wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n', 'wp-data'],
        filemtime($dir_path . '/block/block-swiper-slider.js')
    );
    $block_instance_id = 'hcdigital-swiper-' . uniqid();
    register_block_type('hcdigital/swiper-slider', [
        'editor_script' => 'hcdigital-swiper-slider-block-editor',
        'render_callback' => 'hcdigital_render_swiper_slider_block',
        'attributes' => [
            'useGutenbergEditor' => ['type' => 'boolean', 'default' => true],
            'dataSource' => ['type' => 'string', 'default' => 'latest'],
            'selectedPosts' => ['type' => 'array', 'default' => []],
            'selectedPostType' => ['type' => 'string', 'default' => 'post'],
            'metaQuery' => ['type' => 'string', 'default' => ''],
            'slidesPerView' => ['type' => 'object', 'default' => ['desktop' => 1, 'tablet' => 1, 'mobile' => 1]],
            'slidesPerGroupOn' => ['type' => 'boolean', 'default' => false],
            'slidesPerGroup' => ['type' => 'object', 'default' => ['desktop' => 1, 'tablet' => 1, 'mobile' => 1]],
            'spaceBetween' => ['type' => 'object', 'default' => ['desktop' => 20, 'tablet' => 20, 'mobile' => 20]],
            'overflow' => ['type' => 'object', 'default' => ['desktop' => false, 'tablet' => false, 'mobile' => false]],
            'outOfRangeSlidesOpacity' => ['type' => 'object', 'default' => ['desktop' => 1, 'tablet' => 1, 'mobile' => 1]],
            'loop' => ['type' => 'boolean', 'default' => true],
            'centeredSlides' => ['type' => 'boolean', 'default' => false],
            'pagination' => ['type' => 'object', 'default' => ['desktop' => true, 'tablet' => true, 'mobile' => true]],
            'navigation' => ['type' => 'object', 'default' => ['desktop' => true, 'tablet' => true, 'mobile' => true]],
            'customRenderBullet' => ['type' => 'boolean', 'default' => false],
            'customRenderBulletHtml' => ['type' => 'string', 'default' => ''],
            'navigationAlign' => ['type' => 'object', 'default' => ['desktop' => 'center', 'tablet' => 'center', 'mobile' => 'center']],
            'navigationOffset' => ['type' => 'object', 'default' => ['desktop' => 0, 'tablet' => 0, 'mobile' => 0]],
            'navigationJustify' => ['type' => 'object', 'default' => ['desktop' => 'sides', 'tablet' => 'sides', 'mobile' => 'sides']],
            'navigationContentContainer' => ['type' => 'object', 'default' => ['desktop' => false, 'tablet' => false, 'mobile' => false]],
            'navigationButtonsHorizontalOffset' => ['type' => 'object', 'default' => ['desktop' => 0, 'tablet' => 0, 'mobile' => 0]],
            'navigationArrowsPosition' => ['type' => 'object', 'default' => ['desktop' => 'inside', 'tablet' => 'inside', 'mobile' => 'inside']],
            'navigationArrowsSpacing' => ['type' => 'object', 'default' => ['desktop' => 12, 'tablet' => 12, 'mobile' => 12]],
            'navigationButtonSize' => ['type' => 'object', 'default' => ['desktop' => 40, 'tablet' => 40, 'mobile' => 40]],
            'navigationArrowSize' => ['type' => 'object', 'default' => ['desktop' => 20, 'tablet' => 20, 'mobile' => 20]],
            'navigationButtonBorderWidth' => ['type' => 'number', 'default' => 0],
            'navigationButtonBorderColor' => ['type' => 'string', 'default' => ''],
            'navigationArrowBorderRadius' => ['type' => 'object', 'default' => ['desktop' => 0, 'tablet' => 0, 'mobile' => 0]],
            'dynamicBullets' => ['type' => 'object', 'default' => ['desktop' => false, 'tablet' => false, 'mobile' => false]],
            'dynamicMainBullets' => ['type' => 'object', 'default' => ['desktop' => 1, 'tablet' => 1, 'mobile' => 1]],
            'selectedContentBlockId' => ['type' => 'string', 'default' => ''],
            'postsToShow' => ['type' => 'number', 'default' => 12],
            'effect' => ['type' => 'string', 'default' => 'slide'],
            'autoplay' => ['type' => 'boolean', 'default' => true],
            'autoscroll' => ['type' => 'boolean', 'default' => false],
            'speed' => ['type' => 'number', 'default' => 5000],
            'paddingLeft' => ['type' => 'object', 'default' => ['desktop' => 0, 'tablet' => 0, 'mobile' => 0]],
            'paddingRight' => ['type' => 'object', 'default' => ['desktop' => 0, 'tablet' => 0, 'mobile' => 0]],
            'paddingTop' => ['type' => 'object', 'default' => ['desktop' => 0, 'tablet' => 0, 'mobile' => 0]],
            'paddingBottom' => ['type' => 'object', 'default' => ['desktop' => 0, 'tablet' => 0, 'mobile' => 0]],
            'breakpoints' => ['type' => 'object', 'default' => ['tablet' => 690, 'desktop' => 1000]],
            'paginationAttachedToArrows' => ['type' => 'object', 'default' => ['desktop' => false, 'tablet' => false, 'mobile' => false]],
            'paginationOffset' => ['type' => 'object', 'default' => ['desktop' => 0, 'tablet' => 0, 'mobile' => 0]],
            'scrollbar' => ['type' => 'boolean', 'default' => false],
            'scrollbarPosition' => ['type' => 'string', 'default' => 'bottom'],
            'scrollbarColor' => ['type' => 'string', 'default' => 'rgba(0, 0, 0, 0.1)'],
            'scrollbarDragColor' => ['type' => 'string', 'default' => 'rgba(0, 0, 0, 0.25)'],
            'paginationBulletSize' => ['type' => 'object','default' => ['desktop' => 8,'tablet' => 8,'mobile' => 8]],
            'paginationActiveBulletSize' => ['type' => 'object','default' => ['desktop' => 24,'tablet' => 24,'mobile' => 24]],
            'navigationArrowBgColor' => [ 'type' => 'string', 'default' => '' ],
            'navigationArrowColor' => [ 'type' => 'string', 'default' => '' ],
            'paginationBulletColor' => [ 'type' => 'string', 'default' => '#ccc' ],
            'paginationActiveBulletColor' => [ 'type' => 'string', 'default' => '#000' ],
            'customNavigation' => ['type' => 'boolean', 'default' => false],
            'customNavigationSVG' => ['type' => 'string', 'default' => ''],
            'equaliseColumns' => ['type' => 'boolean', 'default' => true],
            
        ]
    ]);
}
add_action('init', 'hcdigital_register_swiper_slider_block');
function hcdigital_render_swiper_slider_block($attributes, $content) {
    $block_instance_id = 'swiper-' . substr(md5(json_encode($attributes)), 0, 8);
    $next_el_class = 'swiper-button-next-' . $block_instance_id;
    $prev_el_class = 'swiper-button-prev-' . $block_instance_id;
    $pagination_el_class = 'swiper-pagination-' . $block_instance_id;
    $scrollbar_el_class = 'swiper-scrollbar-' . $block_instance_id;
    $paginationAttachedToArrows = $attributes['paginationAttachedToArrows'];
    global $selected_content_block_id;
    $dir_path = __DIR__;
    $relative_path = str_replace(THEME_PATH, '', $dir_path);
    $dir_url = THEME_URL . $relative_path;
    if ( ! is_admin() ) {
        $dynamic_styles = '';
        wp_enqueue_style('swiper-css', $dir_url . '/swiper/swiper-bundle.min.css', [], '11.2.10');
        wp_enqueue_style('swiper-slider-style', $dir_url . '/style-swiper-slider.css', ['swiper-css'], filemtime($dir_path . '/style-swiper-slider.css'));
        

        $pagination_text_align_map = [
            'left' => 'left',
            'center' => 'center',
            'right' => 'right',
        ];
        $pagination_text_align_mobile = isset($attributes['paginationJustify']['mobile']) ? $pagination_text_align_map[$attributes['paginationJustify']['mobile']] ?? 'center' : 'center';
        $pagination_text_align_tablet = isset($attributes['paginationJustify']['tablet']) ? $pagination_text_align_map[$attributes['paginationJustify']['tablet']] ?? 'center' : 'center';
        $pagination_text_align_desktop = isset($attributes['paginationJustify']['desktop']) ? $pagination_text_align_map[$attributes['paginationJustify']['desktop']] ?? 'center' : 'center';
        if (!empty($attributes['pagination']['mobile'])) {
            $dynamic_styles .= "\n#{$block_instance_id} .swiper-pagination { text-align: {$pagination_text_align_mobile}; }";
        }
        if (!empty($attributes['pagination']['tablet'])) {
            $dynamic_styles .= "\n@media (min-width: {$attributes['breakpoints']['tablet']}px) { #{$block_instance_id} .swiper-pagination { text-align: {$pagination_text_align_tablet}; } }";
        }
        if (!empty($attributes['pagination']['desktop'])) {
            $dynamic_styles .= "\n@media (min-width: {$attributes['breakpoints']['desktop']}px) { #{$block_instance_id} .swiper-pagination { text-align: {$pagination_text_align_desktop}; } }";
        }

        
    

        $pagination_offset_mobile = isset($attributes['paginationOffset']['mobile']) ? intval($attributes['paginationOffset']['mobile']) : 0;
        $pagination_offset_tablet = isset($attributes['paginationOffset']['tablet']) ? intval($attributes['paginationOffset']['tablet']) : 0;
        $pagination_offset_desktop = isset($attributes['paginationOffset']['desktop']) ? intval($attributes['paginationOffset']['desktop']) : 0;

        
        if (!empty($attributes['pagination']['mobile']) && $pagination_offset_mobile != 0 && !$paginationAttachedToArrows['mobile']) {
            $dynamic_styles .= "
                #{$block_instance_id} .swiper-pagination { 
                    transform: translateY({$pagination_offset_mobile}px); 
                }";
            if ($pagination_offset_mobile > 0) {
                $dynamic_styles .= "
                #{$block_instance_id}  {
                    margin-bottom: {$pagination_offset_mobile}px;
                }";
            }
        }
        if (!empty($attributes['pagination']['tablet']) && $pagination_offset_tablet != 0 && !$paginationAttachedToArrows['tablet']) {
            $dynamic_styles .= "
            @media (min-width: {$attributes['breakpoints']['tablet']}px) { 
                #{$block_instance_id} .swiper-pagination { 
                    transform: translateY({$pagination_offset_tablet}px); 
                } 
            }";
            if ($pagination_offset_tablet >= 0) {
                $dynamic_styles .= "
                @media (min-width: {$attributes['breakpoints']['tablet']}px) { 
                    #{$block_instance_id}  {
                        margin-bottom: {$pagination_offset_tablet}px;
                    }
                }";
            }
        }
        if (!empty($attributes['pagination']['desktop']) && $pagination_offset_desktop != 0 && !$paginationAttachedToArrows['desktop']) {
            $dynamic_styles .= "
            @media (min-width: {$attributes['breakpoints']['desktop']}px) { 
                #{$block_instance_id} .swiper-pagination { 
                    transform: translateY({$pagination_offset_desktop}px); 
                } 
            }";
            if ($pagination_offset_desktop >= 0) {
                $dynamic_styles .= "
                @media (min-width: {$attributes['breakpoints']['desktop']}px) { 
                    #{$block_instance_id}  {
                        margin-bottom: {$pagination_offset_desktop}px;
                    }
                }";
            }
        }
        if (!empty($attributes['customNavigation'])) {
            $dynamic_styles .= "
                #{$block_instance_id} .swiper-button-next::after,
                #{$block_instance_id} .swiper-button-prev::after {
                    display: none;
                }
            ";
        }
        $tablet_breakpoint = $attributes['breakpoints']['tablet'];
        $desktop_breakpoint = $attributes['breakpoints']['desktop'];
    

        
        $scrollbar_position = isset($attributes['scrollbarPosition']) ? $attributes['scrollbarPosition'] : 'bottom';
        $scrollbar_color = isset($attributes['scrollbarColor']) ? $attributes['scrollbarColor'] : 'rgba(0, 0, 0, 0.1)';
        $scrollbar_drag_color = isset($attributes['scrollbarDragColor']) ? $attributes['scrollbarDragColor'] : 'rgba(0, 0, 0, 0.25)';

        $overflow_mobile = $attributes['overflow']['mobile'] ? 'visible' : 'hidden';
        $overflow_tablet = $attributes['overflow']['tablet'] ? 'visible' : 'hidden';
        $overflow_desktop = $attributes['overflow']['desktop'] ? 'visible' : 'hidden';

        $padding_left_mobile = $attributes['paddingLeft']['mobile'] . 'px';
        $padding_left_tablet = $attributes['paddingLeft']['tablet'] . 'px';
        $padding_left_desktop = $attributes['paddingLeft']['desktop'] . 'px';

        $padding_right_mobile = $attributes['paddingRight']['mobile'] . 'px';
        $padding_right_tablet = $attributes['paddingRight']['tablet'] . 'px';
        $padding_right_desktop = $attributes['paddingRight']['desktop'] . 'px';

        $padding_top_mobile = $attributes['paddingTop']['mobile'] . 'px';
        $padding_top_tablet = $attributes['paddingTop']['tablet'] . 'px';
        $padding_top_desktop = $attributes['paddingTop']['desktop'] . 'px';

        $padding_bottom_mobile = $attributes['paddingBottom']['mobile'] . 'px';
        $padding_bottom_tablet = $attributes['paddingBottom']['tablet'] . 'px';
        $padding_bottom_desktop = $attributes['paddingBottom']['desktop'] . 'px';


        $navigationButtonSizeMobile = $attributes['navigationButtonSize']['mobile'];
        $navigationButtonSizeTablet = $attributes['navigationButtonSize']['tablet'];
        $navigationButtonSizeDesktop = $attributes['navigationButtonSize']['desktop'];

        $navigationArrowsSpacingMobile = intval($attributes['navigationArrowsSpacing']['mobile'] ?? 12);
        $navigationArrowsSpacingTablet = intval($attributes['navigationArrowsSpacing']['tablet'] ?? 12);
        $navigationArrowsSpacingDesktop = intval($attributes['navigationArrowsSpacing']['desktop'] ?? 12);
        
        $navigationButtonsJustifyTablet = $attributes['navigationJustify']['tablet'];
        $navigationButtonsJustifyMobile = $attributes['navigationJustify']['mobile'];
        $navigationButtonsJustifyDesktop = $attributes['navigationJustify']['desktop'];


        $arrow_size_mobile = $attributes['navigationArrowSize']['mobile'] . 'px';
        $arrow_size_tablet = $attributes['navigationArrowSize']['tablet'] . 'px';
        $arrow_size_desktop = $attributes['navigationArrowSize']['desktop'] . 'px';

        $arrow_border_radius_mobile = $attributes['navigationArrowBorderRadius']['mobile'] . 'px';
        $arrow_border_radius_tablet = $attributes['navigationArrowBorderRadius']['tablet'] . 'px';
        $arrow_border_radius_desktop = $attributes['navigationArrowBorderRadius']['desktop'] . 'px';

        $navigation_button_border_width = isset($attributes['navigationButtonBorderWidth']) ? intval($attributes['navigationButtonBorderWidth']) : 0;
        $navigation_button_border_color = isset($attributes['navigationButtonBorderColor']) ? $attributes['navigationButtonBorderColor'] : '';
        $navigation_button_border_style = $navigation_button_border_width > 0 ? 'solid' : 'none';

        $border_style_css = '';
        if ($navigation_button_border_width > 0) {
            $border_style_css .= "
                border-width: {$navigation_button_border_width}px;
                border-style: {$navigation_button_border_style};";
            if (!empty($navigation_button_border_color)) {
                $border_style_css .= "
                border-color: {$navigation_button_border_color};";
            }
        }

        $nav_align_positions = [ 
            'top' => 'top: 0; bottom:auto; transform: translateY(0);',
            'center' => 'top: 50%; bottom:auto; transform: translateY(-50%);',
            'bottom' => 'bottom: 0;top:auto;transform: translateY(0);',
        ];

        $nav_justify_styles = [
            'left' => 'justify-content: flex-start;',
            'center' => 'justify-content: center;',
            'right' => 'justify-content: flex-end;',
            'sides' => 'justify-content: space-between;',
        ];

        $nav_style_mobile = $nav_align_positions[$attributes['navigationAlign']['mobile']] ?? '';
        $nav_style_tablet = $nav_align_positions[$attributes['navigationAlign']['tablet']] ?? '';
        $nav_style_desktop = $nav_align_positions[$attributes['navigationAlign']['desktop']] ?? '';

        $nav_justify_mobile = $nav_justify_styles[$navigationButtonsJustifyMobile] ?? '';
        $nav_justify_tablet = $nav_justify_styles[$navigationButtonsJustifyTablet] ?? '';
        $nav_justify_desktop = $nav_justify_styles[$navigationButtonsJustifyDesktop] ?? '';

      

        $navigationButtonOffsetMobile = isset($attributes['navigationButtonOffset']['mobile']) ? intval($attributes['navigationButtonOffset']['mobile']) : 24;
        $navigationButtonOffsetTablet = isset($attributes['navigationButtonOffset']['tablet']) ? intval($attributes['navigationButtonOffset']['tablet']) : 24;
        $navigationButtonOffsetDesktop = isset($attributes['navigationButtonOffset']['desktop']) ? intval($attributes['navigationButtonOffset']['desktop']) : 24;
        

        $navigationArrowsPositionMobile = $attributes['navigationArrowsPosition']['mobile'] ?? 'inside';
        $navigationArrowsPositionTablet = $attributes['navigationArrowsPosition']['tablet'] ?? 'inside';
        $navigationArrowsPositionDesktop = $attributes['navigationArrowsPosition']['desktop'] ?? 'inside';



        $arrow_nav_spacing_mobile = '';
        $arrow_nav_spacing_tablet = '';
        $arrow_nav_spacing_desktop = '';

        $containerMarginMobile = "margin:0;";
        $containerMarginTablet = "margin:0;";
        $containerMarginDesktop = "margin:0;";


        if ($attributes['navigation']['mobile']) {
                if ($navigationArrowsPositionMobile === 'contained') {
                    $navigationAlignMobile = $attributes['navigationAlign']['mobile'] ?? 'center';
                    $margin_y_value = $navigationButtonSizeMobile + $navigationButtonOffsetMobile;
                    $margin_x_value = $navigationButtonSizeMobile * 2 + $navigationArrowsSpacingMobile + $navigationButtonOffsetMobile;

                if ($navigationAlignMobile === 'center') {
                    if ($navigationButtonsJustifyMobile === 'sides') {
                        $margin_x_value = $navigationButtonSizeMobile + $navigationButtonOffsetMobile;
                        $containerMarginMobile .= " margin-left: {$margin_x_value}px; margin-right: {$margin_x_value}px;";
                    } elseif ($navigationButtonsJustifyMobile === 'right') {
                        $containerMarginMobile .= " margin-left: auto; margin-right: {$margin_x_value}px;";
                    } elseif ($navigationButtonsJustifyMobile === 'left') {
                        $containerMarginMobile .= " margin-left: {$margin_x_value}px; margin-right: auto;";
                    }
                } elseif ($navigationAlignMobile === 'top') {
                    $margin_y_value = $navigationButtonSizeMobile + $navigationButtonOffsetMobile;
                    $containerMarginMobile .= " margin-top: {$margin_y_value}px;margin-bottom: 0;";
                } elseif ($navigationAlignMobile === 'bottom') {
                    $containerMarginMobile .= " margin-bottom: {$margin_y_value}px; margin-top: 0;";
                }
            }
        }

        if ($attributes['navigation']['tablet']) {
            if ($navigationArrowsPositionTablet === 'contained') {
                $navigationAlignTablet = $attributes['navigationAlign']['tablet'] ?? 'center';



                $margin_y_value = $navigationButtonSizeTablet + $navigationButtonOffsetTablet;
                $margin_x_value = $navigationButtonSizeTablet * 2 + $navigationArrowsSpacingTablet + $navigationButtonOffsetTablet;

                if ($navigationAlignTablet === 'center') {
                    if ($navigationButtonsJustifyTablet === 'sides') {
                        $margin_x_value = $navigationButtonSizeTablet + $navigationButtonOffsetTablet;
                        $containerMarginTablet .= " margin-left: {$margin_x_value}px; margin-right: {$margin_x_value}px;";
                    } elseif ($navigationButtonsJustifyTablet === 'right') {
                        $containerMarginTablet .= " margin-left: auto; margin-right: {$margin_x_value}px;";
                    } elseif ($navigationButtonsJustifyTablet === 'left') {
                        $containerMarginTablet .= " margin-left: {$margin_x_value}px; margin-right: auto;";
                    }
                } elseif ($navigationAlignTablet === 'top') {
                    $containerMarginTablet .= " margin-top: {$margin_y_value}px;margin-bottom: 0;";
                } elseif ($navigationAlignTablet === 'bottom') {
                    $containerMarginTablet .= " margin-bottom: {$margin_y_value}px; margin-top: 0;";
                }
            }
        }

        
        if ($attributes['navigation']['desktop']) {
            if ($navigationArrowsPositionDesktop === 'contained') {

                $navigationAlignDesktop = $attributes['navigationAlign']['desktop'] ?? 'center';
                

                $margin_y_value = $navigationButtonSizeDesktop + $navigationButtonOffsetDesktop;
                if ($navigationAlignDesktop === 'center') {
                    $margin_x_value = $navigationButtonSizeDesktop * 2 + $navigationArrowsSpacingDesktop + $navigationButtonOffsetDesktop;
                    if ($navigationButtonsJustifyDesktop === 'sides') {
                        $margin_x_value = $navigationButtonSizeDesktop + $navigationButtonOffsetDesktop;
                        $containerMarginDesktop .= " margin-left: {$margin_x_value}px; margin-right: {$margin_x_value   }px;";
                    } elseif ($navigationButtonsJustifyDesktop === 'right') {
                        $containerMarginDesktop .= " margin-left: auto; margin-right: {$margin_x_value}px;";
                    } elseif ($navigationButtonsJustifyDesktop === 'left') {
                        $containerMarginDesktop .= " margin-left: {$margin_x_value}px; margin-right: auto;";
                    }
                } elseif ($navigationAlignDesktop === 'top') {
                    $margin_y_value = $navigationButtonSizeDesktop + $navigationButtonOffsetDesktop;
                    $containerMarginDesktop .= " margin-top: {$margin_y_value}px; margin-bottom: 0;";
                } elseif ($navigationAlignDesktop === 'bottom') {
                    $containerMarginDesktop .= " margin-bottom: {$margin_y_value}px;margin-top: 0;";
                }
            }
        }

        $dynamic_styles .= "
            #{$block_instance_id}.swiper-container-wrapper .swiper-container {
                {$containerMarginMobile}
            }
            @media (min-width: {$tablet_breakpoint}px) {
                #{$block_instance_id}.swiper-container-wrapper .swiper-container {
                
                    {$containerMarginTablet}
                }
            }
            @media (min-width: {$desktop_breakpoint}px) {
                #{$block_instance_id}.swiper-container-wrapper .swiper-container {
                    {$containerMarginDesktop}
                }
            }
        ";







        $navigationMarginMobile = "margin:0;";

        if ($navigationArrowsPositionMobile === 'outside') {
            $navigationAlignMobile = $attributes['navigationAlign']['mobile'] ?? 'center';
            
            
            $margin_y_value = $navigationButtonSizeMobile + $navigationButtonOffsetMobile;
            $margin_x_value = $navigationButtonSizeMobile * 2 + $navigationArrowsSpacingMobile + $navigationButtonOffsetMobile;
            if ($navigationAlignMobile === 'center') {

                if ($navigationButtonsJustifyMobile === 'sides') {
                    $margin_x_value = $navigationButtonSizeMobile + $navigationButtonOffsetMobile;
                    $navigationMarginMobile .= " margin-left: -{$margin_x_value}px; margin-right: -{$margin_x_value}px;";
                } elseif ($navigationButtonsJustifyMobile === 'right') {
                    $navigationMarginMobile .= " margin-left: auto; margin-right: -{$margin_x_value}px;";
                } elseif ($navigationButtonsJustifyMobile === 'left') {
                    $navigationMarginMobile .= " margin-left: -{$margin_x_value}px; margin-right: auto;";
                }
            } elseif ($navigationAlignMobile === 'top') {
                $navigationMarginMobile .= " margin-top: -{$margin_y_value}px;margin-bottom: 0;";
            } elseif ($navigationAlignMobile === 'bottom') {
                $navigationMarginMobile .= " margin-bottom: -{$margin_y_value}px; margin-top: 0;";
            }
        } elseif ($navigationArrowsPositionMobile === 'inside') {
            $navigationAlignMobile = $attributes['navigationAlign']['mobile'] ?? 'center';
            if ($navigationAlignMobile === 'center') {
                if ($navigationButtonsJustifyMobile === 'sides') {
                    $navigationMarginMobile .= " margin-left: {$navigationButtonOffsetMobile}px; margin-right: {$navigationButtonOffsetMobile}px;";
                } elseif ($navigationButtonsJustifyMobile === 'right') {
                    $navigationMarginMobile .= " margin-left: auto; margin-right: {$navigationButtonOffsetMobile}px;";
                } elseif ($navigationButtonsJustifyMobile === 'left') {
                    $navigationMarginMobile .= " margin-left: {$navigationButtonOffsetMobile}px; margin-right: auto;";
                }
            } elseif ($navigationAlignMobile === 'top') {
                $navigationMarginMobile .= " margin: {$navigationButtonOffsetMobile}px;";
            } elseif ($navigationAlignMobile === 'bottom') {
                $navigationMarginMobile .= " margin: {$navigationButtonOffsetMobile}px;";
            }
        } 

        $navigationMarginTablet = "margin:0;";
        if ($navigationArrowsPositionTablet === 'outside') {
            $navigationAlignTablet = $attributes['navigationAlign']['tablet'] ?? 'center';
            $margin_y_value = $navigationButtonSizeTablet + $navigationButtonOffsetTablet;
            $margin_x_value = $navigationButtonSizeTablet * 2 + $navigationArrowsSpacingTablet + $navigationButtonOffsetTablet;

            if ($navigationAlignTablet === 'center') {
                if ($navigationButtonsJustifyTablet === 'sides') {
                    $margin_x_value = $navigationButtonSizeTablet + $navigationButtonOffsetTablet;
                    $navigationMarginTablet .= " margin-left: -{$margin_x_value}px; margin-right: -{$margin_x_value}px;";
                } elseif ($navigationButtonsJustifyTablet === 'right') {
                    $navigationMarginTablet .= " margin-left: auto; margin-right: -{$margin_x_value}px;";
                } elseif ($navigationButtonsJustifyTablet === 'left') {
                    $navigationMarginTablet .= " margin-left: -{$margin_x_value}px; margin-right: auto;";
                }
            } elseif ($navigationAlignTablet === 'top') {
                $navigationMarginTablet .= " margin-top: -{$margin_y_value}px;margin-bottom: 0;";
            } elseif ($navigationAlignTablet === 'bottom') {
                $navigationMarginTablet .= " margin-bottom: -{$margin_y_value}px; margin-top: 0;";
            }
        }
        elseif ($navigationArrowsPositionTablet === 'inside') {
            $navigationAlignTablet = $attributes['navigationAlign']['tablet'] ?? 'center';
            if ($navigationAlignTablet === 'center') {
                if ($navigationButtonsJustifyTablet === 'sides') {
                    $navigationMarginTablet .= " margin-left: {$navigationButtonOffsetTablet}px; margin-right: {$navigationButtonOffsetTablet}px;";
                } elseif ($navigationButtonsJustifyTablet === 'right') {
                    $navigationMarginTablet .= " margin-left: auto; margin-right: {$navigationButtonOffsetTablet}px;";
                } elseif ($navigationButtonsJustifyTablet === 'left') {
                    $navigationMarginTablet .= " margin-left: {$navigationButtonOffsetTablet}px; margin-right: auto;";
                }
            } elseif ($navigationAlignTablet === 'top') {
                $navigationMarginTablet .= " margin: {$navigationButtonOffsetTablet}px;";
            } elseif ($navigationAlignTablet === 'bottom') {
                $navigationMarginTablet .= " margin: {$navigationButtonOffsetTablet}px;";
            }
        } 

        $navigationMarginDesktop = "margin: 0;";
        if ($navigationArrowsPositionDesktop === 'outside') {
            $navigationAlignDesktop = $attributes['navigationAlign']['desktop'] ?? 'center';
            
            $margin_y_value = $navigationButtonSizeDesktop + $navigationButtonOffsetDesktop;
            if ($navigationAlignDesktop === 'center') {
                $margin_x_value = $navigationButtonSizeDesktop * 2 + $navigationArrowsSpacingDesktop + $navigationButtonOffsetDesktop;
                if ($navigationButtonsJustifyDesktop === 'sides') {
                    $margin_x_value = $navigationButtonSizeDesktop + $navigationButtonOffsetDesktop;
                    $navigationMarginDesktop .= " margin-left: -{$margin_x_value}px; margin-right: -{$margin_x_value   }px;";
                } elseif ($navigationButtonsJustifyDesktop === 'right') {
                    $navigationMarginDesktop .= " margin-left: auto; margin-right: -{$margin_x_value}px;";
                } elseif ($navigationButtonsJustifyDesktop === 'left') {
                    $navigationMarginDesktop .= " margin-left: -{$margin_x_value}px; margin-right: auto;";
                }
            } elseif ($navigationAlignDesktop === 'top') {
                $margin_y_value = $navigationButtonSizeDesktop + $navigationButtonOffsetDesktop;
                $navigationMarginDesktop .= " margin-top: -{$margin_y_value}px; margin-bottom: 0;";
            } elseif ($navigationAlignDesktop === 'bottom') {
                $navigationMarginDesktop .= " margin-bottom: -{$margin_y_value}px;margin-top: 0;";
            }
        } 
        elseif ($navigationArrowsPositionDesktop === 'inside') {
            $navigationAlignDesktop = $attributes['navigationAlign']['desktop'] ?? 'center';
            if ($navigationAlignDesktop === 'center') {            
                if ($navigationButtonsJustifyDesktop === 'sides') {
                    $navigationMarginDesktop .= " margin-left: {$navigationButtonOffsetDesktop}px; margin-right: {$navigationButtonOffsetDesktop   }px;";
                } elseif ($navigationButtonsJustifyDesktop === 'right') {
                    $navigationMarginDesktop .= " margin-left: auto; margin-right: {$navigationButtonOffsetDesktop}px;";
                } elseif ($navigationButtonsJustifyDesktop === 'left') {
                    $navigationMarginDesktop .= " margin-left: {$navigationButtonOffsetDesktop}px; margin-right: auto;";
                }
            } elseif ($navigationAlignDesktop === 'top') {
                $navigationMarginDesktop .= " margin: {$navigationButtonOffsetDesktop}px;";
            } elseif ($navigationAlignDesktop === 'bottom') {
                $navigationMarginDesktop .= " margin: {$navigationButtonOffsetDesktop}px;";
            }
        } 
        

        






       

        $navigation_margin_mobile_modifier = '';
        $navigation_margin_tablet_modifier = '';
        $navigation_margin_desktop_modifier = '';


        if ($attributes['navigationAlign']['mobile'] === 'top') {
             $navigation_margin_mobile_modifier = '-';
        }
        if ($attributes['navigationAlign']['tablet'] === 'top') {
             $navigation_margin_tablet_modifier = '-';
        }
        if ($attributes['navigationAlign']['desktop'] === 'top') {
             $navigation_margin_desktop_modifier = '-';
        }

        $navigation_arrow_bg_color = isset($attributes['navigationArrowBgColor']) ? $attributes['navigationArrowBgColor'] : '';
        $navigation_arrow_color = isset($attributes['navigationArrowColor']) ? $attributes['navigationArrowColor'] : '';

        $dynamic_styles .= "
            #{$block_instance_id} .swiper-scrollbar {
                background-color: {$scrollbar_color};
            }
            #{$block_instance_id} .swiper-scrollbar-drag {
                background-color: {$scrollbar_drag_color};
            }
            .show-mobile { display: block !important; }
            .hide-mobile { display: none !important; }
            #{$block_instance_id} .swiper-navigation {
                display: " . ($attributes['navigation']['mobile'] ? 'flex' : 'none') . ";
                --swiper-navigation-size: {$arrow_size_mobile};
                --swiper-navigation-color: {$navigation_arrow_color};
                {$arrow_nav_spacing_mobile}
                {$nav_style_mobile}
                {$nav_justify_mobile}
                gap: {$navigationArrowsSpacingMobile}px;
                {$navigationMarginMobile}
            }
            #{$block_instance_id} .swiper-button-next, #{$block_instance_id} .swiper-button-prev {
                border-radius: {$arrow_border_radius_mobile};
                width: {$navigationButtonSizeMobile}px;
                height: {$navigationButtonSizeMobile}px;" .
                ($navigation_arrow_bg_color ? "\n                background-color: {$navigation_arrow_bg_color};" : "") . "
                {$border_style_css}            }
            #{$block_instance_id}.swiper-container-wrapper .swiper-container {
                overflow: {$overflow_mobile};
            }
            @media (min-width: {$tablet_breakpoint}px) {
                .show-mobile { display: none !important; }
                .hide-mobile { display: block !important; }
                .show-tablet { display: block !important; }
                .hide-tablet { display: none !important; }
                #{$block_instance_id} .swiper-navigation {
                    display: " . ($attributes['navigation']['tablet'] ? 'flex' : 'none') . ";
                    --swiper-navigation-size: {$arrow_size_tablet};
                    --swiper-navigation-color: {$navigation_arrow_color};
                    {$arrow_nav_spacing_tablet}
                    {$nav_style_tablet}
                    {$nav_justify_tablet}
                    gap: {$navigationArrowsSpacingTablet}px;
                    {$navigationMarginTablet}
                }
                #{$block_instance_id} .swiper-button-next, #{$block_instance_id} .swiper-button-prev {
                    border-radius: {$arrow_border_radius_tablet};
                    width: {$navigationButtonSizeTablet}px;
                    height: {$navigationButtonSizeTablet}px;" .
                    ($navigation_arrow_bg_color ? "\n                    background-color: {$navigation_arrow_bg_color};" : "") . "
                    {$border_style_css}                }
                #{$block_instance_id}.swiper-container-wrapper .swiper-container {
                    overflow: {$overflow_tablet};
                }
            }
            @media (min-width: {$desktop_breakpoint}px) {
                .show-tablet { display: none !important; }
                .hide-tablet { display: block !important; }
                .show-desktop { display: block !important; }
                .hide-desktop { display: none !important; }
                #{$block_instance_id} .swiper-navigation {
                    display: " . ($attributes['navigation']['desktop'] ? 'flex' : 'none') . ";
                    --swiper-navigation-size: {$arrow_size_desktop};
                    --swiper-navigation-color: {$navigation_arrow_color};
                    {$arrow_nav_spacing_desktop}
                    {$nav_style_desktop}
                    {$nav_justify_desktop}
                    gap: {$navigationArrowsSpacingDesktop}px;
                    {$navigationMarginDesktop}
                }
                #{$block_instance_id} .swiper-button-next, #{$block_instance_id} .swiper-button-prev {
                    border-radius: {$arrow_border_radius_desktop};
                    width: {$navigationButtonSizeDesktop}px;
                    height: {$navigationButtonSizeDesktop}px;" .
                    ($navigation_arrow_bg_color ? "\n                    background-color: {$navigation_arrow_bg_color};" : "") . "
                    {$border_style_css}                }
                #{$block_instance_id}.swiper-container-wrapper .swiper-container {
                    overflow: {$overflow_desktop};
                }
            }
        ";
        $navigationButtonsHorizontalOffsetMobile = null;
        $navigationButtonsHorizontalOffsetTablet = null;
        $navigationButtonsHorizontalOffsetDesktop = null;

        $navigationContentContainerStyle = "max-width: var(--stk-block-default-width, var(--stk-block-width-default-detected, 900px))!important;width: var(--theme-block-width) !important;margin-left:auto!important;margin-right:auto!important;";
        $navigationContentContainerStyleMobile = '';
        if ($attributes['navigation']['mobile'] && $attributes['navigationContentContainer']['mobile']) {
            $navigationContentContainerStyleMobile = $navigationContentContainerStyle;
            $navigationButtonsHorizontalOffsetMobile = $attributes['navigationButtonsHorizontalOffset']['mobile'];
        }

        $navigationContentContainerStyleTablet = 'max-width:initial!important;width:auto!important;';
        if ($attributes['navigation']['tablet'] && $attributes['navigationContentContainer']['tablet']) {
            $navigationContentContainerStyleTablet = $navigationContentContainerStyle;
            $navigationButtonsHorizontalOffsetTablet = $attributes['navigationButtonsHorizontalOffset']['tablet'];
        }

        $navigationContentContainerStyleDesktop = 'max-width:initial!important;width:auto!important;';
        if ($attributes['navigation']['desktop'] && $attributes['navigationContentContainer']['desktop']) {
            $navigationContentContainerStyleDesktop = $navigationContentContainerStyle;
            $navigationButtonsHorizontalOffsetDesktop = $attributes['navigationButtonsHorizontalOffset']['desktop'];
        }


        if ($navigationButtonsHorizontalOffsetMobile !== null) {
            $dynamic_styles .= "
                #{$block_instance_id} .swiper-navigation .swiper-button-prev {
                    transform: translateX(-{$navigationButtonsHorizontalOffsetMobile}px);
                }
                #{$block_instance_id} .swiper-navigation .swiper-button-next {
                    transform: translateX( ". $navigationButtonsHorizontalOffsetMobile * -1 . "px);
                }
            ";
        }
        if ($navigationButtonsHorizontalOffsetTablet !== null) {
            $dynamic_styles .= "
                @media (min-width: {$tablet_breakpoint}px) {
                    #{$block_instance_id} .swiper-navigation .swiper-button-prev {
                        transform: translateX(". $navigationButtonsHorizontalOffsetTablet * -1 . "px);
                    }
                    #{$block_instance_id} .swiper-navigation .swiper-button-next {
                        transform: translateX({$navigationButtonsHorizontalOffsetTablet}px);
                    }
                }
            ";
        }   
        if ($navigationButtonsHorizontalOffsetDesktop !== null) {
            $dynamic_styles .= "
                @media (min-width: {$desktop_breakpoint}px) {
                    #{$block_instance_id} .swiper-navigation .swiper-button-prev {
                        transform: translateX(". $navigationButtonsHorizontalOffsetDesktop * -1 . "px);
                    }
                    #{$block_instance_id} .swiper-navigation .swiper-button-next {
                        transform: translateX({$navigationButtonsHorizontalOffsetDesktop}px);
                    }
                }
            ";
        }
        $dynamic_styles .= "
            #{$block_instance_id} .swiper-navigation {
                display: " . ($attributes['navigation']['mobile'] ? 'flex' : 'none') . ";
                {$navigationContentContainerStyleMobile}
            }
            @media (min-width: {$tablet_breakpoint}px) {
                #{$block_instance_id} .swiper-navigation {
                    display: " . ($attributes['navigation']['tablet'] ? 'flex' : 'none') . ";
                    {$navigationContentContainerStyleTablet}
                }
            }
            @media (min-width: {$desktop_breakpoint}px) {
                #{$block_instance_id} .swiper-navigation {
                    display: " . ($attributes['navigation']['desktop'] ? 'flex' : 'none') . ";
                    {$navigationContentContainerStyleDesktop}
                }
            }
        ";


        $out_of_range_opacity_mobile = isset($attributes['outOfRangeSlidesOpacity']['mobile']) ? $attributes['outOfRangeSlidesOpacity']['mobile'] : 1;
        $out_of_range_opacity_tablet = isset($attributes['outOfRangeSlidesOpacity']['tablet']) ? $attributes['outOfRangeSlidesOpacity']['tablet'] : 1;
        $out_of_range_opacity_desktop = isset($attributes['outOfRangeSlidesOpacity']['desktop']) ? $attributes['outOfRangeSlidesOpacity']['desktop'] : 0;

      
        
        $dynamic_styles .= "
            #{$block_instance_id}.swiper-container-wrapper .swiper-slide:not(.swiper-slide-visible) {
                opacity: {$out_of_range_opacity_mobile};
            }
            @media (min-width: {$tablet_breakpoint}px) {
                #{$block_instance_id}.swiper-container-wrapper .swiper-slide:not(.swiper-slide-visible) {
                    opacity: {$out_of_range_opacity_tablet};
                }
            }
            @media (min-width: {$desktop_breakpoint}px) {
                #{$block_instance_id}.swiper-container-wrapper .swiper-slide:not(.swiper-slide-visible) {
                    opacity: {$out_of_range_opacity_desktop};
                }
            }
        ";

        



        $pagination_bullet_size_mobile = isset($attributes['paginationBulletSize']['mobile']) ? intval($attributes['paginationBulletSize']['mobile']) : 8;
        $pagination_bullet_size_tablet = isset($attributes['paginationBulletSize']['tablet']) ? intval($attributes['paginationBulletSize']['tablet']) : 8;
        $pagination_bullet_size_desktop = isset($attributes['paginationBulletSize']['desktop']) ? intval($attributes['paginationBulletSize']['desktop']) : 8;
        $pagination_active_bullet_size_mobile = isset($attributes['paginationActiveBulletSize']['mobile']) ? intval($attributes['paginationActiveBulletSize']['mobile']) : 24;
        $pagination_active_bullet_size_tablet = isset($attributes['paginationActiveBulletSize']['tablet']) ? intval($attributes['paginationActiveBulletSize']['tablet']) : 24;
        $pagination_active_bullet_size_desktop = isset($attributes['paginationActiveBulletSize']['desktop']) ? intval($attributes['paginationActiveBulletSize']['desktop']) : 24;


        $pagination_bullet_color = isset($attributes['paginationBulletColor']) ? $attributes['paginationBulletColor'] : '#ccc';
        $pagination_active_bullet_color = isset($attributes['paginationActiveBulletColor']) ? $attributes['paginationActiveBulletColor'] : '#000';

        $dynamic_styles .= "
            #{$block_instance_id} .swiper-pagination .swiper-pagination-bullet {
                width: {$pagination_bullet_size_mobile}px;
                height: {$pagination_bullet_size_mobile}px;
                border-radius: {$pagination_bullet_size_mobile}px;
                background: {$pagination_bullet_color};
            }
            #{$block_instance_id} .swiper-pagination .swiper-pagination-bullet.swiper-pagination-bullet-active {
                width: {$pagination_active_bullet_size_mobile}px;
                background: {$pagination_active_bullet_color};
            }
            @media (min-width: {$tablet_breakpoint}px) {
                #{$block_instance_id} .swiper-pagination .swiper-pagination-bullet {
                    width: {$pagination_bullet_size_tablet}px;
                    height: {$pagination_bullet_size_tablet}px;
                    border-radius: {$pagination_bullet_size_tablet}px;
                    background: {$pagination_bullet_color};
                }
                #{$block_instance_id} .swiper-pagination .swiper-pagination-bullet.swiper-pagination-bullet-active {
                    width: {$pagination_active_bullet_size_tablet}px;
                    background: {$pagination_active_bullet_color};
                }
            }
            @media (min-width: {$desktop_breakpoint}px) {
                #{$block_instance_id} .swiper-pagination .swiper-pagination-bullet {
                    width: {$pagination_bullet_size_desktop}px;
                    height: {$pagination_bullet_size_desktop}px;
                    border-radius: {$pagination_bullet_size_desktop}px;
                    background: {$pagination_bullet_color};
                }
                #{$block_instance_id} .swiper-pagination .swiper-pagination-bullet.swiper-pagination-bullet-active {
                    width: {$pagination_active_bullet_size_desktop}px;
                    background: {$pagination_active_bullet_color};
                }
            }
        ";

        

        wp_add_inline_style('swiper-slider-style', $dynamic_styles);

        wp_enqueue_script('swiper-js', $dir_url . '/swiper/swiper-bundle.min.js', [], '11.2.10', true);

        $init_script = "
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.swiper-container[data-swiper-options]').forEach(function(element) {
                    if (element.swiper) return;
                    try {
                        const options = JSON.parse(element.dataset.swiperOptions || '{}');
                        
                        let swiperInstance;
                        const handleVideoPlayback = (swiper) => {
                            if (!swiper || !swiper.slides || swiper.slides.length === 0) return;
                            swiper.slides.forEach(slide => {
                                const video = slide.querySelector('video');
                                if (video && !video.paused) {
                                    video.pause();
                                }
                            });
                            const activeSlide = swiper.slides[swiper.activeIndex];
                            if (activeSlide) {
                                const video = activeSlide.querySelector('video');
                                if (video && video.paused && !video.dataset.userPaused) {
                                    video.muted = true;
                                    video.play();
                                }
                            }
                        };

                        function addVideoPlayPauseButton(slide, video) {
                            if (slide.dataset.videocontrols === 'true' && video && !slide.querySelector('.swiper-slide-video-toggle')) {
                                const btn = document.createElement('div');
                                const playIcon = `<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" fill=\"currentColor\" class=\"bi bi-play-fill\" viewBox=\"0 0 16 16\"><path d=\"m11.596 8.697-6.363 3.692c-.54.313-1.233-.066-1.233-.697V4.308c0-.63.692-1.01 1.233-.696l6.363 3.692a.802.802 0 0 1 0 1.393\"/></svg>`;
                                const pauseIcon = `<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" fill=\"currentColor\" class=\"bi bi-pause-fill\" viewBox=\"0 0 16 16\"><path d=\"M5.5 3.5A1.5 1.5 0 0 1 7 5v6a1.5 1.5 0 0 1-3 0V5a1.5 1.5 0 0 1 1.5-1.5m5 0A1.5 1.5 0 0 1 12 5v6a1.5 1.5 0 0 1-3 0V5a1.5 1.5 0 0 1 1.5-1.5\"/></svg>`;

                                btn.className = 'swiper-slide-video-toggle';
                                btn.innerHTML = video.paused ? playIcon : pauseIcon;
                                btn.addEventListener('click', function() {
                                    if (video.paused) {
                                        video.muted = true;
                                        video.play();
                                        btn.innerHTML = pauseIcon;
                                        video.dataset.userPaused = '';
                                    } else {
                                        video.pause();
                                        btn.innerHTML = playIcon;
                                        video.dataset.userPaused = 'true';
                                    }
                                });
                                video.addEventListener('play', function() {
                                    btn.innerHTML = pauseIcon;
                                });
                                video.addEventListener('pause', function() {
                                    btn.innerHTML = playIcon;
                                });
                                slide.appendChild(btn);
                            }
                        }

                        const setupVideoListeners = (swiper) => {
                            swiper.slides.forEach(function (slide) {
                                const videos = slide.querySelectorAll('video');
                                if (videos.length > 0) {
                                    slide.classList.add('has-video');
                                    videos.forEach(function(video) {
                                        video.pause();
                                        if (video.dataset.listenersAttached) return;
                                        video.dataset.listenersAttached = 'true';
                                        video.loop = false; 
                                        video.addEventListener('play', () => {
                                            if (swiper.params.autoplay.enabled && swiper.autoplay && swiper.autoplay.running) {
                                                swiper.autoplay.stop();
                                            }
                                        });
                                        video.addEventListener('pause', () => {
                                            // Do nothing on pause to prevent conflicts
                                        });
                                        video.addEventListener('ended', () => {
                                            if (swiper.params.autoplay.enabled) {
                                                swiper.slideNext();
                                            }
                                        });
                                        setTimeout(function() { video.currentTime = 0;}, 500);
                                        addVideoPlayPauseButton(slide, video);
                                    });
                                    
                                }
                            });
                        };
                        options.on = {
                            init: function (swiper) {
                                swiperInstance = swiper;
                                const wrapper = swiper.el.querySelector('.swiper-wrapper');
                                var containerWrapper = swiper.el.closest('.swiper-container-wrapper');
                                var pagination = containerWrapper.querySelector('.swiper-pagination');
                                var attached = containerWrapper.querySelector('.pagination-attached-to-arrows');

                                // Helper to move pagination for breakpoints
                                function movePagination() {
                                    var width = window.innerWidth;
                                    var tabletMin = options.breakpoints ? Object.keys(options.breakpoints)[0] : 690;
                                    var desktopMin = options.breakpoints ? Object.keys(options.breakpoints)[1] : 1000;
                                    var attachedBreakpoints = options.pagination.paginationAttachedToArrows || {};

                                    // Remove pagination from attached container
                                    if (attached && attached.contains(pagination)) {
                                        attached.removeChild(pagination);
                                        containerWrapper.querySelector('.swiper-container').appendChild(pagination);
                                    }

                                    // Mobile
                                    if (width < tabletMin && attachedBreakpoints.mobile) {
                                        if (attached && !attached.contains(pagination)) {
                                            attached.appendChild(pagination);
                                        }
                                    }
                                    // Tablet
                                    else if (width >= tabletMin && width < desktopMin && attachedBreakpoints.tablet) {
                                        if (attached && !attached.contains(pagination)) {
                                            attached.appendChild(pagination);
                                        }
                                    }
                                    // Desktop
                                    else if (width >= desktopMin && attachedBreakpoints.desktop) {
                                        if (attached && !attached.contains(pagination)) {
                                            attached.appendChild(pagination);
                                        }
                                    }
                                }

                                movePagination();
                                window.addEventListener('resize', movePagination);

                                const initialSetup = () => {
                                    swiper.update();
                                    setupVideoListeners(swiper);
                                    setTimeout(() => handleVideoPlayback(swiper), 100);
                                };

                                const observer = new MutationObserver((mutationsList, observer) => {
                                    for(const mutation of mutationsList) {
                                        
                                        if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                                            initialSetup();
                                            if (wrapper.children.length > 0) {
                                                observer.disconnect(); 
                                            }
                                            break;
                                        }
                                    }
                                });
                                
                                observer.observe(wrapper, { childList: true });

                                if (wrapper.children.length > 0) {
                                     initialSetup();
                                     observer.disconnect();
                                }
                            },
                            sliderFirstMove: function(swiper) {
                                swiper.slides.forEach(slide => {
                                    const video = slide.querySelector('video');
                                    if (video && !video.paused) {
                                        video.pause();
                                    }
                                });
                            },
                            touchEnd: function(swiper) {
                                 handleVideoPlayback(swiper);
                            },
                            slideChangeTransitionEnd: function(swiper) {
                                handleVideoPlayback(swiper);
                                
                                const activeSlide = swiper.slides[swiper.activeIndex];
                                if (activeSlide && !activeSlide.querySelector('video') && swiper.params.autoplay.enabled && swiper.autoplay && !swiper.autoplay.running) {
                                    swiper.autoplay.start();
                                }
                            },
                            
                        };
                          
                        if (!options.pagination || typeof options.pagination !== 'object') {
                            options.pagination = {};
                        }
                        if (options.customRenderBullet && options.customRenderBulletHtml) {
                            options.pagination.renderBullet = function (index, className) {
                                return options.customRenderBulletHtml
                                    .replace('{index}', index + 1)
                                    .replace('{className}', className);
                            };
                        }

                       
                        const slideCount = element.querySelectorAll('.swiper-slide').length;
                        if (slideCount > 1) {
                            new Swiper(element, options);
                        } else {
                            const containerWrapper = element.closest('.swiper-container-wrapper');
                            if (containerWrapper) {
                                const navigation = containerWrapper.querySelector('.swiper-navigation');
                                const pagination = containerWrapper.querySelector('.swiper-pagination');
                                const scrollbar = containerWrapper.querySelector('.swiper-scrollbar');
                                if (navigation) navigation.style.display = 'none';
                                if (pagination) pagination.style.display = 'none';
                                if (scrollbar) scrollbar.style.display = 'none';
                            }
                        }

                        //new Swiper(element, options);

                    } catch (e) {
                        console.error('Error parsing Swiper options:', e);
                    };
                });
            });
        ";
        
        wp_add_inline_script('swiper-js', $init_script);
    }
    $selected_content_block_id = $attributes['selectedContentBlockId'] ? intval($attributes['selectedContentBlockId']) : null;
    
    $slider_options = [
        // 'mousewheel' => true,
        // 'forceToAxis' => true,
        // 'cssMode' => true,
        'effect' => $attributes['effect'],
        'loop' => $attributes['loop'],
        'watchSlidesProgress' => true,
        'centeredSlides' => $attributes['centeredSlides'],
        'watchOverflow' => !$attributes['loop'],
        'slidesPerView' => floatval($attributes['slidesPerView']['mobile']),
        'spaceBetween' => intval($attributes['spaceBetween']['mobile']),
        'navigation' =>  ['nextEl' => '.' . $next_el_class, 'prevEl' => '.' . $prev_el_class],
        'pagination' => [ 
            'el' => '.swiper-pagination.' . $pagination_el_class , 
            'paginationAttachedToArrows' => $paginationAttachedToArrows,
            'clickable' => true, 
            'dynamicBullets' => !empty($attributes['dynamicBullets']['mobile']), 
            'dynamicMainBullets' => intval($attributes['dynamicMainBullets']['mobile'])
        ],
        'customRenderBullet' => $attributes['customRenderBullet'],
        'customRenderBulletHtml' => $attributes['customRenderBulletHtml'],
        'breakpoints' => [
            intval($attributes['breakpoints']['tablet']) => [
                'slidesPerView' => floatval($attributes['slidesPerView']['tablet']),
                'spaceBetween' => intval($attributes['spaceBetween']['tablet']),
            ],
            intval($attributes['breakpoints']['desktop']) => [
                'slidesPerView' => floatval($attributes['slidesPerView']['desktop']),
                'spaceBetween' => intval($attributes['spaceBetween']['desktop']),
            ],
        ],
        //'direction' => !empty($attributes['vertical']) ? 'vertical' : 'horizontal',
        
    ];

    if (!empty($attributes['slidesPerGroupOn'])) {
        
        $slider_options['loopAddBlankSlides'] = false;
        $slider_options['slidesPerGroup'] = intval($attributes['slidesPerGroup']['mobile']);
        $slider_options['breakpoints'][intval($attributes['breakpoints']['tablet'])]['slidesPerGroup'] = intval($attributes['slidesPerGroup']['tablet']);
        $slider_options['breakpoints'][intval($attributes['breakpoints']['desktop'])]['slidesPerGroup'] = intval($attributes['slidesPerGroup']['desktop']);
    }
    if ($attributes['scrollbar']) {
        $slider_options['scrollbar'] = [
            'el' => '.' . $scrollbar_el_class,
            'draggable' => true,
        ];
    }
    if ($attributes['autoplay']) {
        if ($attributes['autoscroll']) {
            $slider_options['autoplay'] = [
                'delay' => 0
            ];
            $slider_options['freeMode'] = true;
            $slider_options['speed'] = $attributes['speed'];
        } else {
            $slider_options['autoplay'] = [
                'delay' => $attributes['speed'],
                'disableOnInteraction' => false,
            ];
        }
    }

    $options_json = htmlspecialchars(json_encode($slider_options), ENT_QUOTES, 'UTF-8');
    
    $classes = ['swiper-container'];
    // if (!empty($attributes['vertical'])) {
    //     $classes[] = 'swiper-container-vertical';
    // }
    $container_classes = implode(' ', $classes);
    
    $wrapper_classes = ['swiper-container-wrapper'];
    if (!empty($attributes['className'])) {
        $wrapper_classes[] = $attributes['className'];
    }


    if($attributes['equaliseColumns']) {
        $wrapper_classes[] = 'equalise-columns';
    }

    $output = "<div id='{$block_instance_id}' style='position: relative; width: 100%;' class='" . esc_attr(implode(' ', $wrapper_classes)) . "'>";
        $output .= "<div  class='{$container_classes}' data-swiper-options='{$options_json}'>";

            if ($attributes['scrollbar'] && $attributes['scrollbarPosition'] == 'top') {
                $output .= "<div class='swiper-scrollbar {$scrollbar_el_class} scrollbar-position-{$scrollbar_position}'></div>";
            }
            
            if ($attributes['useGutenbergEditor']) {
                $output .= "<div class='swiper-wrapper'>";
                $output .= $content;
            } else {
                $prefix = $attributes['selectedPostType'] . '_archive';
                $output .= "<div class='swiper-wrapper' data-prefix='{$prefix}' >";
                $data_source = isset($attributes['dataSource']) ? $attributes['dataSource'] : 'latest';

                $query_args = [
                    'post_type' => $attributes['selectedPostType'],
                    'post_status' => 'publish',
                ];

                if ($data_source === 'specific') {
                    $selected_posts = !empty($attributes['selectedPosts']) ? $attributes['selectedPosts'] : [];
                    if (!empty($selected_posts)) {
                        $query_args['post__in'] = $selected_posts;
                        $query_args['posts_per_page'] = -1;
                        $query_args['orderby'] = 'post__in';
                    } else {

                        $query_args['post__in'] = [0];
                    }
                } else {
                    $query_args['posts_per_page'] = $attributes['postsToShow'];

                    if (!empty($attributes['metaQuery'])) {
                        $current_post_id = get_the_ID();
                        $meta_query_string = str_replace('{{POST_ID}}', $current_post_id, $attributes['metaQuery']);

                        parse_str($meta_query_string, $meta_params);
                        $meta_query = [];
                        if (isset($meta_params['key'])) {
                            $meta_query[] = [
                                'key'     => $meta_params['key'],
                                'value'   => $meta_params['value'],
                                'compare' => isset($meta_params['compare']) ? $meta_params['compare'] : '=',
                            ];
                        } else {
                            foreach ($meta_params as $key => $value) {
                                $meta_query[] = [
                                    'key' => $key,
                                    'value' => $value,
                                    'compare' => '='
                                ];
                            }
                        }
                        
                        if (count($meta_query) > 1) {
                            $meta_query['relation'] = 'AND';
                        }
                        
                        $query_args['meta_query'] = $meta_query;
                    }

                    if (!empty($attributes['taxQuery'])) {
                        parse_str($attributes['taxQuery'], $tax_params);
                        $tax_query = [];
                        foreach ($tax_params as $taxonomy => $term) {
                            $terms = array_map('trim', explode(',', $term));
                            $tax_query[] = [
                                'taxonomy' => $taxonomy,
                                'field'    => 'slug',
                                'terms'    => $terms,
                            ];
                        }
                        if (count($tax_query) > 1) {
                            $tax_query['relation'] = 'AND';
                        }
                        $query_args['tax_query'] = $tax_query;
                    }
                }

                $query = new WP_Query($query_args);

                if ($query->have_posts()) {
                    while ($query->have_posts()) {
                        $query->the_post();
                        $output .= "<div class='swiper-slide entries' data-archive='default' data-prefix='{$prefix}' data-cards='boxed' data-id='" . get_the_ID() . "'>";
                        ob_start();
                        blocksy_render_archive_card([
                            'prefix' => $prefix,
                            'has_default_layout' => true,
                        ]);
                        $output .= ob_get_clean();
                        

                        $output .= "</div>";
                    }
                }
                wp_reset_postdata();
            }
            $output .= "</div>";
    
            
            if ($attributes['scrollbar'] && $attributes['scrollbarPosition'] == 'bottom') {
                $output .= "<div class='swiper-scrollbar {$scrollbar_el_class} scrollbar-position-{$scrollbar_position}'></div>";
            }

            $output .= "</div>";
            
            if (!empty($attributes['pagination']) && ( !empty($attributes['pagination']['mobile']) || !empty($attributes['pagination']['tablet']) || !empty($attributes['pagination']['desktop']) ) ) {
                $pagination_classes = get_device_classes($attributes['pagination']);
                $device_classes = '';
                $attached_classes = '';
                if (!empty($attributes['pagination']['mobile'])) {
                    $device_classes .= ' show-mobile';
                    if (!empty($attributes['paginationAttachedToArrows']['mobile'])) {
                        $attached_classes .= ' pagination-attached-to-arrows-mobile';
                    }
                }
                if (!empty($attributes['pagination']['tablet'])) {
                    $device_classes .= ' show-tablet';
                    if (!empty($attributes['paginationAttachedToArrows']['tablet'])) {
                        $attached_classes .= ' pagination-attached-to-arrows-tablet';
                    }
                }
                if (!empty($attributes['pagination']['desktop'])) {
                    $device_classes .= ' show-desktop';
                    if (!empty($attributes['paginationAttachedToArrows']['desktop'])) {
                        $attached_classes .= ' pagination-attached-to-arrows-desktop';
                    }
                }
                $output .= "<div class='swiper-pagination {$pagination_el_class}{$device_classes}{$attached_classes} {$pagination_classes}'></div>";
            }

        

        $navigation_classes = ['swiper-navigation'];
        $output .= "<div class='" . implode(' ', $navigation_classes) . "'>";
            
            $custom_nav = !empty($attributes['customNavigation']) && !empty($attributes['customNavigationSVG']);
            $svg_code = $attributes['customNavigationSVG'] ?? '';

            if ($custom_nav) {
                $output .= "<div class='swiper-button-prev {$prev_el_class}'><span style='color: {$navigation_arrow_color}; display: flex; align-items: center; justify-content: center; width: 80%; height: 80%; transform: rotate(180deg);'>{$svg_code}</span></div>";
            } else {
                $output .= "<div class='swiper-button-prev {$prev_el_class}'></div>";
            }

            $pagination_attached_to_arrows_classes = get_device_classes($attributes['paginationAttachedToArrows']);
            $output .= "<div class='pagination-attached-to-arrows {$pagination_attached_to_arrows_classes}'></div>";

            if ($custom_nav) {
                $output .= "<div class='swiper-button-next {$next_el_class}'><span style='color: {$navigation_arrow_color}; display: flex; align-items: center; justify-content: center; width: 80%; height: 80%;'>{$svg_code}</span></div>";
            } else {
                $output .= "<div class='swiper-button-next {$next_el_class}'></div>";
            }
        $output .= "</div>";
         
    $output .= "</div>";

    return $output;

    add_filter('blocksy:posts-listing:cards:custom-output', function ($output) {
        $hook_id = intval($attributes['selectedContentBlockId']);
        if ($hook_id ) {
            $atts = blocksy_get_post_options($hook_id);
            $data = ['output' => blc_render_content_block($hook_id)];
            return $data;
        }
        return $output;
    }, 999 );
}
function get_device_classes($attribute, $prefix = 'show', $negate_prefix = 'hide') {
    $classes = [];
    foreach (['mobile', 'tablet', 'desktop'] as $device) {
        if (isset($attribute[$device])) {
            $classes[] = ($attribute[$device] ? "{$prefix}-{$device}" : "{$negate_prefix}-{$device}");
        }
    }
    return implode(' ', $classes);
}