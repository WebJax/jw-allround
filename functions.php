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
    wp_enqueue_style('mytheme-style', get_stylesheet_uri()), array(), filemtime( get_stylesheet_directory() . '/style.css' );
    wp_enqueue_script('mytheme-scripts', get_template_directory_uri() . '/script.js', array(), false, true);
}
add_action('wp_enqueue_scripts', 'mytheme_enqueue_scripts');

// Registrer block patterns
function mytheme_register_block_patterns() {
    register_block_pattern_category('custom', array(
        'label' => __('Custom Patterns', 'mytheme')
    ));
}
add_action('init', 'mytheme_register_block_patterns');
