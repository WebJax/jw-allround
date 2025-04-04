<?php
function mytheme_setup() {
    add_theme_support('wp-block-styles');
    add_theme_support('editor-styles');
    add_editor_style('style.css');
    add_theme_support('custom-logo');
    add_theme_support('align-wide');
    add_theme_support('automatic-feed-links');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
}
add_action('after_setup_theme', 'mytheme_setup');

function mytheme_enqueue_scripts() {
    wp_enqueue_style('mytheme-style', get_stylesheet_uri(), array(), filemtime( get_stylesheet_directory() . '/style.css' ));
    wp_enqueue_script('mytheme-scripts', get_template_directory_uri() . '/script.js', array(), false, true);
}
add_action('wp_enqueue_scripts', 'mytheme_enqueue_scripts');

// Registrer block patterns
function mytheme_register_block_patterns() {
    register_block_pattern_category('jw-allround', array(
        'label' => __('JW Allround', 'jw-allround')
    ));
    
    // Læser block-patterns.json filen
    $patterns_file = get_template_directory() . '/block-patterns.json';
    if (file_exists($patterns_file)) {
        $patterns_json = file_get_contents($patterns_file);
        $patterns_data = json_decode($patterns_json, true);
        
        if (isset($patterns_data['patterns']) && is_array($patterns_data['patterns'])) {
            foreach ($patterns_data['patterns'] as $pattern) {
                if (isset($pattern['name']) && isset($pattern['title']) && isset($pattern['content'])) {
                    register_block_pattern(
                        'jw-allround/' . $pattern['name'],
                        array(
                            'title'       => $pattern['title'],
                            'categories'  => array('jw-allround'),
                            'content'     => $pattern['content'],
                        )
                    );
                }
            }
        }
    }
}
add_action('init', 'mytheme_register_block_patterns');

// Registrer temaet som et blok-tema
function mytheme_setup_block_template_support() {
    add_theme_support('block-templates');
    add_theme_support('block-template-parts');
}
add_action('after_setup_theme', 'mytheme_setup_block_template_support');

// Tilføj understøttelse af font-sizes og farvepaletten defineret i theme.json
function mytheme_setup_theme_json_settings() {
    add_theme_support('custom-spacing');
    add_theme_support('custom-line-height');
    add_theme_support('custom-units');
    add_theme_support('appearance-tools');
}
add_action('after_setup_theme', 'mytheme_setup_theme_json_settings');

/**
 * Tilføj sidespecifik klasse til body elementet
 */
function mytheme_body_class($classes) {
    if (is_single() || is_page()) {
        global $post;
        $classes[] = 'page-' . $post->post_name;
        
        // Tjek om det er en custom template
        $template = get_page_template_slug($post->ID);
        if ($template) {
            $template = str_replace('.html', '', basename($template));
            $classes[] = 'template-' . $template;
        }
    }
    
    if (is_front_page()) {
        $classes[] = 'front-page';
    }
    
    if (is_archive()) {
        $classes[] = 'archive-page';
        if (is_category()) {
            $category = get_category(get_query_var('cat'));
            $classes[] = 'category-' . $category->slug;
        }
        if (is_tag()) {
            $tag = get_queried_object();
            $classes[] = 'tag-' . $tag->slug;
        }
    }
    
    return $classes;
}
add_filter('body_class', 'mytheme_body_class');

/**
 * Tilføj tilpasset font-indlæsning for Hind Siliguri
 */
function mytheme_enqueue_fonts() {
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&display=swap', array(), null);
}
add_action('wp_enqueue_scripts', 'mytheme_enqueue_fonts');
add_action('enqueue_block_editor_assets', 'mytheme_enqueue_fonts');

/**
 * Tilføj WebP-understøttelse til WordPress mediebibliotek
 */
function mytheme_mime_types($mimes) {
    $mimes['webp'] = 'image/webp';
    return $mimes;
}
add_filter('upload_mimes', 'mytheme_mime_types');

/**
 * Tilføj billed-størrelser optimeret til forskellige skærmstørrelser
 */
function mytheme_add_image_sizes() {
    // Mobil-optimerede billeder
    add_image_size('mobile', 576, 9999, false);
    // Tablet-optimerede billeder
    add_image_size('tablet', 768, 9999, false);
    // Desktop-optimerede billeder
    add_image_size('desktop', 1366, 9999, false);
    // Retina-optimerede billeder
    add_image_size('retina', 2560, 9999, false);
}
add_action('after_setup_theme', 'mytheme_add_image_sizes');

/**
 * Tilføj responsive billede-attributter til forbedret ydeevne
 */
function mytheme_responsive_image_attributes($attributes) {
    if (isset($attributes['src'])) {
        // Tilføj loading="lazy" til alle billeder for lazy loading
        $attributes['loading'] = 'lazy';
        
        // Tilføj decoding="async" for asynkron afkodning
        $attributes['decoding'] = 'async';
    }
    return $attributes;
}
add_filter('wp_get_attachment_image_attributes', 'mytheme_responsive_image_attributes');

/**
 * Registrer picture elementer til WebP med fallback
 * Kræver at du har WebP-versioner af dine billeder
 */
function mytheme_generate_webp_picture($html, $attachment_id) {
    // Tjek om WebP-understøttelse er aktiveret
    if (!defined('WEBP_SUPPORT') || !WEBP_SUPPORT) {
        return $html;
    }
    
    // Få billedets URL
    $image_url = wp_get_attachment_url($attachment_id);
    
    // Tjek om billedet har en WebP-version (dette kræver, at du genererer WebP-versioner)
    $webp_url = preg_replace('/\.(jpe?g|png)$/i', '.webp', $image_url);
    $webp_path = str_replace(site_url('/'), ABSPATH, $webp_url);
    
    // Hvis WebP-versionen ikke findes, returner det oprindelige HTML
    if (!file_exists($webp_path)) {
        return $html;
    }
    
    // Få det oprindelige billede-attributter
    $image_attr = wp_get_attachment_image_src($attachment_id, 'full');
    
    // Erstat img-tag med picture-tag
    $html = '<picture>';
    $html .= '<source srcset="' . esc_url($webp_url) . '" type="image/webp">';
    $html .= $html; // Tilføj det oprindelige img-tag som fallback
    $html .= '</picture>';
    
    return $html;
}

// Aktiver WebP-understøttelse ved at definere en konstant (fjern kommentar for at aktivere)
define('WEBP_SUPPORT', true);
add_filter('wp_get_attachment_image', 'mytheme_generate_webp_picture', 10, 2);

/**
 * Optimér JavaScript og CSS indlæsning
 */
function mytheme_optimize_scripts() {
    // Indlæs JavaScript asynkront
    if (!is_admin()) {
        wp_scripts()->add_data('mytheme-scripts', 'async', true);
    }
}
add_action('wp_enqueue_scripts', 'mytheme_optimize_scripts', 20);

/**
 * Tilføj preload for kritiske ressourcer
 */
function mytheme_preload_resources() {
    $theme_uri = get_stylesheet_directory_uri();
    
    // Preload fonts
    echo '<link rel="preload" href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;600&display=swap" as="style">';
    
    // Preload kritisk CSS
    echo '<link rel="preload" href="' . $theme_uri . '/style.css" as="style">';
}
add_action('wp_head', 'mytheme_preload_resources', 1);

/**
 * Registrer og indlæs tilpassede blokke
 */
function mytheme_register_custom_blocks() {
    // Tjek om Gutenberg plugin er aktiv eller om WordPress version understøtter register_block_type
    if (function_exists('register_block_type')) {
        // Registrer og indlæs 'featured-box' blok
        wp_register_script(
            'jw-featured-box-block',
            get_template_directory_uri() . '/blocks/featured-box/block.js',
            array('wp-blocks', 'wp-element', 'wp-editor'),
            filemtime(get_template_directory() . '/blocks/featured-box/block.js')
        );
        
        // Registrer og indlæs 'testimonial-slider' blok
        wp_register_script(
            'jw-testimonial-slider-block',
            get_template_directory_uri() . '/blocks/testimonial-slider/block.js',
            array('wp-blocks', 'wp-element', 'wp-editor'),
            filemtime(get_template_directory() . '/blocks/testimonial-slider/block.js')
        );

        // Registrer og indlæs 'service-card' blok
        wp_register_script(
            'jw-service-card-block',
            get_template_directory_uri() . '/blocks/service-card/block.js',
            array('wp-blocks', 'wp-element', 'wp-editor'),
            filemtime(get_template_directory() . '/blocks/service-card/block.js')
        );

        // Registrer block type med callback funktioner
        register_block_type('jw-allround/featured-box', array(
            'editor_script' => 'jw-featured-box-block',
            'render_callback' => 'mytheme_render_featured_box_block'
        ));
        
        register_block_type('jw-allround/testimonial-slider', array(
            'editor_script' => 'jw-testimonial-slider-block',
            'render_callback' => 'mytheme_render_testimonial_slider_block'
        ));
        
        register_block_type('jw-allround/service-card', array(
            'editor_script' => 'jw-service-card-block',
            'render_callback' => 'mytheme_render_service_card_block'
        ));
    }
}
add_action('init', 'mytheme_register_custom_blocks');

/**
 * Render callbacks for custom blocks
 */
function mytheme_render_featured_box_block($attributes, $content) {
    $title = isset($attributes['title']) ? $attributes['title'] : '';
    $text = isset($attributes['text']) ? $attributes['text'] : '';
    $icon = isset($attributes['icon']) ? $attributes['icon'] : 'star';
    $link = isset($attributes['link']) ? $attributes['link'] : '';
    $linkText = isset($attributes['linkText']) ? $attributes['linkText'] : 'Læs mere';
    $backgroundColor = isset($attributes['backgroundColor']) ? $attributes['backgroundColor'] : '#ffffff';
    $textColor = isset($attributes['textColor']) ? $attributes['textColor'] : '#333333';
    
    $output = '<div class="jw-featured-box" style="background-color:' . esc_attr($backgroundColor) . ';color:' . esc_attr($textColor) . ';">';
    $output .= '<div class="jw-featured-box-icon"><span class="dashicons dashicons-' . esc_attr($icon) . '"></span></div>';
    $output .= '<h3>' . esc_html($title) . '</h3>';
    $output .= '<p>' . esc_html($text) . '</p>';
    
    if ($link) {
        $output .= '<a href="' . esc_url($link) . '" class="jw-featured-box-link">' . esc_html($linkText) . '</a>';
    }
    
    $output .= '</div>';
    
    return $output;
}

function mytheme_render_testimonial_slider_block($attributes, $content) {
    $testimonials = isset($attributes['testimonials']) ? $attributes['testimonials'] : array();
    
    $output = '<div class="jw-testimonial-slider">';
    $output .= '<div class="jw-testimonial-slider-inner">';
    
    foreach ($testimonials as $index => $testimonial) {
        $active = $index === 0 ? ' active' : '';
        $output .= '<div class="jw-testimonial-slide' . $active . '">';
        $output .= '<blockquote class="jw-testimonial-content">' . esc_html($testimonial['content']) . '</blockquote>';
        $output .= '<cite class="jw-testimonial-author">' . esc_html($testimonial['author']) . '</cite>';
        if (!empty($testimonial['company'])) {
            $output .= '<span class="jw-testimonial-company">' . esc_html($testimonial['company']) . '</span>';
        }
        $output .= '</div>';
    }
    
    $output .= '</div>';
    $output .= '<div class="jw-testimonial-controls">';
    $output .= '<button class="jw-testimonial-prev">Forrige</button>';
    $output .= '<button class="jw-testimonial-next">Næste</button>';
    $output .= '</div>';
    $output .= '</div>';
    
    return $output;
}

function mytheme_render_service_card_block($attributes, $content) {
    $title = isset($attributes['title']) ? $attributes['title'] : '';
    $description = isset($attributes['description']) ? $attributes['description'] : '';
    $imageUrl = isset($attributes['imageUrl']) ? $attributes['imageUrl'] : '';
    $imageAlt = isset($attributes['imageAlt']) ? $attributes['imageAlt'] : '';
    $linkUrl = isset($attributes['linkUrl']) ? $attributes['linkUrl'] : '';
    $linkText = isset($attributes['linkText']) ? $attributes['linkText'] : 'Læs mere';
    
    $output = '<div class="service-card">';
    
    if ($imageUrl) {
        $output .= '<div class="service-card-image">';
        $output .= '<img src="' . esc_url($imageUrl) . '" alt="' . esc_attr($imageAlt) . '" loading="lazy" />';
        $output .= '</div>';
    }
    
    $output .= '<div class="service-card-content">';
    $output .= '<h3 class="service-card-title">' . esc_html($title) . '</h3>';
    $output .= '<p class="service-card-description">' . esc_html($description) . '</p>';
    
    if ($linkUrl) {
        $output .= '<a href="' . esc_url($linkUrl) . '" class="service-card-link button">' . esc_html($linkText) . '</a>';
    }
    
    $output .= '</div>'; // .service-card-content
    $output .= '</div>'; // .service-card
    
    return $output;
}

/**
 * WooCommerce Integration - Tilføjer understøttelse for WooCommerce
 */
function mytheme_woocommerce_setup() {
    // Aktiverer understøttelse for WooCommerce i temaet
    add_theme_support('woocommerce');
    
    // Tilføj understøttelse af produktgalleri og zoom
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
    
    // Fjerner standard WooCommerce styling, så vi kan bruge temaets styling i stedet
    add_filter('woocommerce_enqueue_styles', '__return_empty_array');
    
    // Registrerer WooCommerce sidebars
    register_sidebar(array(
        'name'          => __('WooCommerce Sidebar', 'jw-allround'),
        'id'            => 'woocommerce-sidebar',
        'description'   => __('Widgets i denne sidebar vises på WooCommerce sider.', 'jw-allround'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
}
add_action('after_setup_theme', 'mytheme_woocommerce_setup');

/**
 * Tilføj brødkrummesti til WooCommerce
 */
function mytheme_woocommerce_breadcrumbs() {
    return array(
        'delimiter'   => ' &gt; ',
        'wrap_before' => '<nav class="woocommerce-breadcrumb">',
        'wrap_after'  => '</nav>',
        'before'      => '',
        'after'       => '',
        'home'        => _x('Hjem', 'breadcrumb', 'jw-allround'),
    );
}
add_filter('woocommerce_breadcrumb_defaults', 'mytheme_woocommerce_breadcrumbs');

/**
 * Tilføj produkter pr. række
 */
function mytheme_woocommerce_loop_columns() {
    return 3; // Viser 3 produkter pr. række
}
add_filter('loop_shop_columns', 'mytheme_woocommerce_loop_columns');

/**
 * Tilføj relaterede produkter pr. række
 */
function mytheme_woocommerce_related_products_args($args) {
    $args['posts_per_page'] = 3; // Viser 3 relaterede produkter
    $args['columns'] = 3; // Arrangeret i 3 kolonner
    return $args;
}
add_filter('woocommerce_output_related_products_args', 'mytheme_woocommerce_related_products_args');

/**
 * Tilpasset WooCommerce CSS på admin siden
 */
function mytheme_admin_woocommerce_styles() {
    // Kun tilføj på WooCommerce admin sider
    $screen = get_current_screen();
    if (in_array($screen->id, array('woocommerce_page_wc-settings'))) {
        wp_enqueue_style('woocommerce-admin-styles', get_template_directory_uri() . '/woocommerce-admin.css', array(), '1.0');
    }
}
add_action('admin_enqueue_scripts', 'mytheme_admin_woocommerce_styles');
