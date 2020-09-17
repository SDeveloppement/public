<?php

function elefen_compress_images_register_settings() {
   register_setting( 'elefen_compress_images', 'elefen_compress_images' );          
}
add_action( 'admin_init', 'elefen_compress_images_register_settings' );

function elefen_compress_images_register_options_page() {
  add_submenu_page( 'elefen', 'Images Compression', 'Images Compression', 'manage_options', 'elefen_compress_images', 'elefen_compress_images_options_page' );
}
add_action('admin_menu', 'elefen_compress_images_register_options_page');

function elefen_compress_images_options_page()
{
	//$dir = plugin_dir_path(  __FILE__  )."inc/";
	//$files = array_diff(scandir($dir), array('..', '.'));	
	?>
	<div class="wrap">
	<h1>Advanced Settings</h1>
	
	<form method="post" action="options.php">
	    <?php 
	    		settings_fields( 'elefen_compress_images' );
	    		do_settings_sections( 'elefen_compress_images' );
			$elefen_compress_images = get_option('elefen_compress_images');
	    	?>
	    
	    <style>
	    .wp-list-table th{ width:auto; }
	    .wp-list-table th, .wp-list-table td, .wp-list-table tr{ vertical-align:middle; }
	    .wp-list-table td{text-align:left; }
	    .wp-list-table tr:hover td, .wp-list-table tr:hover th{ background-color:#f7fcfe; }
	    </style>
	    
	    <h2>Images Compression</h2>
	    <table class="wp-list-table widefat">
	    		<tbody id="the-list">
		        <tr>
			        <th scope="row">
			        		JPEG Quality<br />
			        		<small>Set JPEG quality from 0 to 100.</small>
			        	</th>
			        <td>
			        		<input type="text" name="elefen_compress_images[images_compression]" value="<?php echo $elefen_compress_images['images_compression']; ?>" />
			        	</td>
		        </tr>
		        <tr>
			        <th scope="row">
			        		Resize Images<br />
			        		<small>Set maximum width and height in pixels. Applies to JPG, GIF and PNG.</small>
			        	</th>
			        <td>
			        		<input type="number" name="elefen_compress_images[max_width]" min="0" value="<?php echo $elefen_compress_images['max_width']; ?>" /> X 
			        		<input type="number" name="elefen_compress_images[max_height]" min="0" value="<?php echo $elefen_compress_images['max_height']; ?>" />
			        	</td>
		        </tr>
	        </tbody>
	    </table>
	    
	    <?php submit_button(); ?>
	
	</form>
	</div>
	<?php
}

add_filter('jpeg_quality', function($arg){
	$elefen_compress_images = get_option('elefen_compress_images');
	if( $elefen_compress_images['images_compression'] )
		return intval($elefen_compress_images['images_compression']);
	else
		return 100;
}, 1000);

function elefen_handle_upload_callback( $data ) {
	$elefen_compress_images = get_option('elefen_compress_images');
	if( $elefen_compress_images['images_compression'] )
		$image_quality = intval($elefen_compress_images['images_compression']);
	else
		$image_quality = 100;
    $file_path = $data['file'];
    $image = false;

    switch ( $data['type'] ) {
        case 'image/jpeg': {
            $image = imagecreatefromjpeg( $file_path );
            imagejpeg( $image, $file_path, $image_quality );
            break;          
        }

        case 'image/png': {
            $image = imagecreatefrompng( $file_path );
            imagepng( $image, $file_path, $image_quality );
            break;          
        }

        case 'image/gif': {         
            // Nothing to do here since imagegif doesn't have an 'image quality' option
            break;
        }
    }

    return $data;
}
add_filter( 'wp_handle_upload', 'elefen_handle_upload_callback', 1000 );



function elefen_handle_resize_callback($image_data){
	$elefen_compress_images = get_option('elefen_compress_images');
	$max_width  = $elefen_compress_images['max_width'];
	$max_height = $elefen_compress_images['max_height'];


	//---------- In with the old v1.6.2, new v1.7 (WP_Image_Editor) ------------

	if($max_width && $max_height) {

		$fatal_error_reported = false;
		$valid_types = array('image/gif','image/png','image/jpeg','image/jpg');
	
	    if(empty($image_data['file']) || empty($image_data['type'])) { $fatal_error_reported = true; }
	    else if(!in_array($image_data['type'], $valid_types)) { $fatal_error_reported = true; }
	
	    $image_editor = wp_get_image_editor($image_data['file']);
	    $image_type = $image_data['type'];
	
	    if($fatal_error_reported || is_wp_error($image_editor)) {
	    }else{
			$to_save = false;
	      	$resized = false;
	        $sizes = $image_editor->get_size();
	
	        if((isset($sizes['width']) && $sizes['width'] > $max_width) || (isset($sizes['height']) && $sizes['height'] > $max_height)) {
	          	$image_editor->resize($max_width, $max_height, false);
	          	$resized = true;
	          	$to_save = true;
	          	$sizes = $image_editor->get_size();
	     	}
	
	      	if($to_save) {
	        		$saved_image = $image_editor->save($image_data['file']);
	      	}
		}
	}

  	return $image_data;
}
add_action('wp_handle_upload', 'elefen_handle_resize_callback', 900);