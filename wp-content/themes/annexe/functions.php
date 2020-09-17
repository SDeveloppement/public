<?php

include('inc/shortcode.php');

// Ajouter la prise en charge des images mises en avant
add_theme_support( 'post-thumbnails' );

// Ajouter automatiquement le titre du site dans l'en-tête du site
add_theme_support( 'title-tag' );

add_theme_support( 'menus' );
add_theme_support( 'widgets' );


function add_theme_scripts() {

    wp_enqueue_style( 'style', get_stylesheet_uri() );
    wp_enqueue_script( 'script', get_template_directory_uri() . '/asset/js/script.js', array(), '1.0.0', true );
    wp_enqueue_style( 'style_css', get_template_directory_uri() . '/asset/style/style.css' );

    wp_enqueue_script('snappscroll', get_template_directory_uri() .  '/asset/js/snap-scroll.js');

}
add_action( 'wp_enqueue_scripts', 'add_theme_scripts' );

function add_admin_scripts() {

    wp_enqueue_script( 'script_admin', get_template_directory_uri() . '/asset/js/admin.js', array(), '1.0.0', true );
    wp_enqueue_style( 'style_admin', get_template_directory_uri() . '/asset/style/admin.css', array(), '1.0.0', true );

}
add_action( 'admin_enqueue_scripts', 'add_admin_scripts' );

add_filter( 'big_image_size_threshold', '__return_false' );
