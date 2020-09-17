<?php

/*
Plugin Name: Elefen
Plugin URI: https://www.elefen.com
Description: Our plugin adds many tweaks to optimize your website and simplify its management.
Author: Elefen
Version: 1.7
Author URI: https://www.elefen.com
*/

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

function elefen_register_settings() {
   register_setting( 'elefen_options_group', 'elefen_options' );      
}
add_action( 'admin_init', 'elefen_register_settings' );

function elefen_register_options_page() {
  add_menu_page('Réglages supplémentaires', 'Réglages supplémentaires', 'manage_options', 'elefen', 'elefen_options_page');
}
add_action('admin_menu', 'elefen_register_options_page');

function elefen_options_page()
{
	//$dir = plugin_dir_path(  __FILE__  )."inc/";
	//$files = array_diff(scandir($dir), array('..', '.'));	
	?>
	<div class="wrap">
	<h1>Advanced Settings</h1>
	
	<form method="post" action="options.php">
	    <?php 
	    		settings_fields( 'elefen_options_group' );
	    		do_settings_sections( 'elefen_options_group' );
			$elefen_options = get_option('elefen_options');
	    	?>
	    
	    <style>
	    .wp-list-table th{ width:auto; }
	    .wp-list-table th, .wp-list-table td, .wp-list-table tr{ vertical-align:middle; }
	    .wp-list-table td{text-align:left; width:10%; }
	    .wp-list-table tr:hover td, .wp-list-table tr:hover th{ background-color:#f7fcfe; }
	    </style>
	    
	    <h2>Wordpress Tweaks and Development</h2>
	    <table class="wp-list-table widefat">
	    		<tbody id="the-list">
		        <tr>
			        <td>
			        		<input type="checkbox" name="elefen_options[block-editor]" value="1" <?php echo isset($elefen_options['block-editor']) ? 'checked' : '' ; ?> />
			        	</td>
			        <th scope="row">
			        		Disable Block Editor<br />
			        		<small>Disable Gutenberg, the block editor released with Wordpress 5.0.</small>
			        	</th>
		        </tr>
		        <tr>
			        <td>
			        		<input type="checkbox" name="elefen_options[custom-js-css]" value="1" <?php echo isset($elefen_options['custom-js-css']) ? 'checked' : '' ; ?> />
			        	</td>
			        <th scope="row">
			        		Custom JS & CSS<br />
			        		<small>Add custom CSS and JS to your theme's footer.</small>
			        	</th>
		        </tr>
		        <tr>
			        <td>
			        		<input type="checkbox" name="elefen_options[template-overlay]" value="1" <?php echo isset($elefen_options['template-overlay']) ? 'checked' : '' ; ?> />
			        	</td>
			        <th scope="row">
			        		Template Overlay<br />
			        		<small>Superpose your template one your pages.</small>
			        	</th>
		        </tr>
		        <tr>
			        <td>
			        		<input type="checkbox" name="elefen_options[history]" value="1" <?php echo isset($elefen_options['history']) ? 'checked' : '' ; ?> />
			        	</td>
			        <th scope="row">
			        		History<br />
			        		<small>Show history modification</small>
			        	</th>
		        </tr>
		        <tr>
			        <td>
			        		<input type="checkbox" name="elefen_options[admin-menu]" value="1" <?php echo isset($elefen_options['admin-menu']) ? 'checked' : '' ; ?> />
			        	</td>
			        <th scope="row">
			        		Manage Admin Menu<br />
			        		<small>Show or hide menu items. You will be able to toggle them afterwards.</small>
			        	</th>
		        </tr>
				<tr>
			        <td>
			        		<input type="checkbox" name="elefen_options[maintenance]" value="1" <?php echo isset($elefen_options['maintenance']) ? 'checked' : '' ; ?> />
			        	</td>
			        <th scope="row">
			        		Manage Maintenance<br />
			        		<small>Active maintenance mode on your website.</small>
			        	</th>
		        </tr>
				<tr>
			        <td>
			        		<input type="checkbox" name="elefen_options[custom-gravityform]" value="1" <?php echo isset($elefen_options['custom-gravityform']) ? 'checked' : '' ; ?> />
			        	</td>
			        <th scope="row">
			        		Custom Gravity form<br />
			        		<small>Active custom gravity form.</small>
			        	</th>
		        </tr>
	        </tbody>
	    </table>
	    
	    <h2>Files and images</h2>
	    <table class="wp-list-table widefat">
	    		<tbody id="the-list">
		        <tr>
			        <td>
			        		<input type="checkbox" name="elefen_options[clean-file-name]" value="1" <?php echo isset($elefen_options['clean-file-name']) ? 'checked' : '' ; ?> />
			        	</td>
			        <th scope="row">
			        		Clean Filename<br />
			        		<small>Clean accents and special chars from the filename on upload. This setting will aplly to any filetype.</small>
			        	</th>
		        </tr>
		        
		        <tr>
			        <td>
			        		<input type="checkbox" name="elefen_options[compress-images]" value="1" <?php echo isset($elefen_options['compress-images']) ? 'checked' : '' ; ?> />
			        	</td>
			        <th scope="row">
			        		Images Compression<br />
			        		<small>Change images compression and limit file dimensions.</small>
			        	</th>
		        </tr>
		        
	        </tbody>
	    </table>
	    
	     <h2>Webmaster & Analytics</h2>
	    <table class="wp-list-table widefat">
	    		<tbody id="the-list">
		        
		        <tr>
			        <td>
			        		<input type="checkbox" name="elefen_options[tracking-code]" value="1" <?php echo isset($elefen_options['tracking-code']) ? 'checked' : '' ; ?> />
			        	</td>
			        <th scope="row">
			        		Tracking Codes<br />
			        		<small>Define your tracking codes.</small>
			        	</th>
		        </tr>
		        
	        </tbody>
	    </table>
	    
	     <h2>Maps</h2>
	    <table class="wp-list-table widefat">
	    		<tbody id="the-list">
		        
		        <tr>
			        <td>
			        		<input type="checkbox" name="elefen_options[maps]" value="1" <?php echo isset($elefen_options['maps']) ? 'checked' : '' ; ?> />
			        	</td>
			        <th scope="row">
			        		Maps<br />
			        		<small>Customize your map</small>
			        	</th>
		        </tr>
		        
	        </tbody>
	    </table>
	    
	     <h2>Groups</h2>
	    <table class="wp-list-table widefat">
	    		<tbody id="the-list">
		        
		        <tr>
			        <td>
			        		<input type="checkbox" name="elefen_options[groups]" value="1" <?php echo isset($elefen_options['groups']) ? 'checked' : '' ; ?> />
			        	</td>
			        <th scope="row">
			        		Groups<br />
			        		<small>Set groups and permissions</small>
			        	</th>
		        </tr>
		        
	        </tbody>
	    </table>
	    
	    <h2>Bars & Popup</h2>
	    <table class="wp-list-table widefat">
	    		<tbody id="the-list">
		        <tr>
			        <td>
			        		<input type="checkbox" name="elefen_options[popup]" value="1" <?php echo isset($elefen_options['popup']) ? 'checked' : '' ; ?> />
			        	</td>
			        <th scope="row">
			        		Bars & Popup<br />
			        		<small>Add Bars & Popup on your WebSite</small>
			        	</th>
		        </tr>
		        
	        </tbody>
	    </table>
	    
	    <?php submit_button(); ?>
	
	</form>
	</div>
	<?php
}

//function elefen_init_files() {
	$elefen_options = get_option('elefen_options');
	if( is_array($elefen_options) ){
		foreach( $elefen_options as $key => $value ){
			include_once(plugin_dir_path(  __FILE__  ).'inc/'.$key.'.php');
		}
	}
//}
//add_action('init', 'elefen_init_files');

