<?php 
add_action( 'admin_init', 'elefen_gravity_register_settings' );
function elefen_gravity_register_settings() {
   register_setting( 'elefen_gravity', 'elefen_gravity' );          
}


add_action('admin_enqueue_scripts', 'script_gravity_form');
function script_gravity_form(){
    wp_enqueue_script( 'admin_script', plugins_url('/elefen/assets/js/admin-gravity.js'));
    wp_enqueue_style( 'admin_style' , plugins_url('/elefen/assets/css/admin-gravity.css'));
    
}
