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
