<?php
function elefen_js_css_register_settings() {
   register_setting( 'elefen_js_css', 'elefen_js_css' );          
}
add_action( 'admin_init', 'elefen_js_css_register_settings' );

function elefen_js_css_register_options_page() {
  add_submenu_page( 'elefen', 'Custom JS & CSS', 'Custom JS & CSS', 'manage_options', 'elefen_js_css', 'elefen_js_css_options_page' );
}
add_action('admin_menu', 'elefen_js_css_register_options_page');

function elefen_js_css_options_page() {
	//$dir = plugin_dir_path(  __FILE__  )."inc/";
	//$files = array_diff(scandir($dir), array('..', '.'));	
	?>
	<div class="wrap">
	<h1>Advanced Settings</h1>	
	<form method="post" action="options.php">
	    <?php 
	    		settings_fields( 'elefen_js_css' );
	    		do_settings_sections( 'elefen_js_css' );
			$elefen_js_css = get_option('elefen_js_css');
	    	?>
	    
	    <style>
	    .wp-list-table th{ width:auto; }
	    .wp-list-table th, .wp-list-table td, .wp-list-table tr{ vertical-align:middle; }
	    .wp-list-table td{text-align:left; }
	    .wp-list-table tr:hover td, .wp-list-table tr:hover th{ background-color:#f7fcfe; }
	    textarea{ width:100%; min-height:500px; }
	    form .submit{ position:fixed;bottom: 0px;z-index: 999; background:#f1f1f1; width:100%; padding-top:1.5em; margin:0 !important; text-align:center;}
	    </style>
	    
	    <h2>Custom JS & CSS</h2>

		<h3>CSS</h3>
		<textarea name="elefen_js_css[css]"><?php echo $elefen_js_css['css']; ?></textarea>

		<h3>Javascript</h3>
		<textarea name="elefen_js_css[js]"><?php echo $elefen_js_css['js']; ?></textarea>
			      
	    
	    <?php submit_button(); ?>
	
	</form>
	</div>

<?php
}

function elefen_js_css() {
	$elefen_js_css = get_option('elefen_js_css');
	if( $elefen_js_css['css'] ):
    ?>
        <style>
            <?php echo $elefen_js_css['css']; ?>
        </style>
    <?php
	endif;
	
	if( $elefen_js_css['js'] ):
    ?>
        <script>
            <?php echo $elefen_js_css['js']; ?>
        </script>
    <?php
	endif;
}
add_action('wp_footer', 'elefen_js_css');

function elefen_enqueue_scripts() {	
	wp_enqueue_style( 'elefen-custom-style', plugin_dir_url(__FILE__) . '../assets/css/style.css' );
	wp_enqueue_script( 'elefen-custom-script', plugin_dir_url(__FILE__) . '../assets/js/script.js', array( 'jquery' ) );
}
add_action( 'wp_enqueue_scripts', 'elefen_enqueue_scripts' );
