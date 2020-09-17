<?php

function elefen_maps_register_settings() {
   register_setting( 'elefen_maps', 'elefen_maps' );          
}
add_action( 'admin_init', 'elefen_maps_register_settings' );

function elefen_maps_register_options_page() {
  add_submenu_page( 'elefen', 'Maps', 'Maps', 'manage_options', 'elefen_maps', 'elefen_maps_options_page' );
}
add_action('admin_menu', 'elefen_maps_register_options_page');


function elefen_maps_options_page()
{
	
	//$dir = plugin_dir_path(  __FILE__  )."inc/";
	//$files = array_diff(scandir($dir), array('..', '.'));	
	?>
	<div class="wrap">
	<h1>Advanced Settings</h1>
	
	<form method="post" action="options.php">
	    <?php 
    		settings_fields( 'elefen_maps' );
    		do_settings_sections( 'elefen_maps' );
			$elefen_maps = get_option('elefen_maps');
    	?>
	    
	    <style>
	    .wp-list-table th{ width:auto; }
	    .wp-list-table th, .wp-list-table td, .wp-list-table tr{ vertical-align:middle; }
	    .wp-list-table td{text-align:left; }
	    .wp-list-table tr:hover td, .wp-list-table tr:hover th{ background-color:#f7fcfe; }
	    </style>
	    
	    <h2>Maps</h2>
	    <table class="wp-list-table widefat">
	    		<tbody id="the-list">
		        <tr>
			        <th scope="row">
			        		Google map<br />
			        		<small>API</small>
			        	</th>
			        <td>
			        		<input type="text" size="60"name="elefen_maps[google_map]" value="<?php echo $elefen_maps['google_map']; ?>" />
			        	</td>
		        </tr>
	        </tbody>
	    </table>
	    
	    <?php submit_button(); ?>
	
	</form>
	</div>

	
<?php 
}

add_filter( 'clean_url', 'elefen_find_add_key', 99, 3 );
function elefen_find_add_key( $url, $original_url, $_context ) {

    $key = get_option('elefen_maps');

    // If no key added no point in checking
    if ( ! $key ) {
        return $url;
    }

    if ( strstr( $url, "maps.google.com/maps/api/js" ) !== false || strstr( $url, "maps.googleapis.com/maps/api/js" ) !== false ) {// it's a Google maps url

        if ( strstr( $url, "key=" ) === false ) {// it needs a key
            $url = add_query_arg( 'key', $key, $url );
            $url = str_replace( "&#038;", "&amp;", $url ); // or $url = $original_url
        }

    }

    return $url;
}

add_action('init', 'custom_map_init');
function custom_map_init(){
	$labels = array(
		'name'				=> 'Customize your maps',
		'singular_name'	 	=> 'Custom Map',
		'menu_name'			=> 'Custom maps',
		'name_admin_bar'	=> 'Maps',
		'not_found'			=> 'No custom map found',
		'not_found_in_trash'	=> 'No custom maps found in Trash.'
	);
	
	$args = array(
		'labels' 			=> $labels,
		'public' 			=> true,
		'show_ui'			=> true, 
		'show_in_menu'		=> true,
		'capability_type'	=>'post',
		'menu_position'		=> null,
		'supports' 			=> array ('title', 'thumbnail'),
		'register_meta_box_cb'	=> 'fc_meta_box_maps'
	);
	
	register_post_type('maps', $args);
}

function fc_meta_box_maps() {
	add_meta_box('map_custom', 'Settings', 'fc_maps_metabox_content');
}

function fc_maps_metabox_content ($postMap) {
	
    wp_enqueue_script('wp-color-picker');
    wp_enqueue_style( 'wp-color-picker' );
    ?>
    
    <script type="text/javascript">
    jQuery(document).ready(function($) {   
        $('#fc_map_color').wpColorPicker();
        $('#fc_map_water').wpColorPicker();
    });             
    </script>
    <?php
	
	$height = get_post_meta($postMap->ID, 'fc_map_height', true);
	$longitude = get_post_meta($postMap->ID, 'fc_map_longitude', true);
	$latitude = get_post_meta($postMap->ID, 'fc_map_latitude', true);
	$width = get_post_meta($postMap->ID, 'fc_map_width', true);
	$color_picker = get_post_meta($postMap->ID, 'fc_map_color', true);
	$color_water = get_post_meta($postMap->ID, 'fc_map_water', true);
	$road = get_post_meta($postMap->ID, 'fc_map_road', true);
	$interest = get_post_meta($postMap->ID, 'fc_map_interest', true);
	$defautUI  = get_post_meta($postMap->ID, 'fc_map_UI', true);
	$zoom  = get_post_meta($postMap->ID, 'fc_map_zoom', true);
	$icon  = get_post_meta($postMap->ID, 'fc_map_icon', true);
	?>
	
	<table class="form-table">
		<tr>
			<th><label for="fc_map_width">Width</label></th>
			<td><input id="fc_map_width" class="widefat" type="text" name="fc_map_width" value= "<?php echo $width; ?>"></td>
		</tr>
		<tr>
			<th><label for="fc_map_height">Height</label></th>
			<td><input id="fc_map_height" class="widefat" type="text" name="fc_map_height" value= "<?php echo $height; ?>"></td>
		</tr>
		<tr>
			<th><label for="fc_map_latitude">Latitude</label></th>
			<td><input id="fc_map_latitude" class="widefat" type="text" name="fc_map_latitude" value= "<?php echo $latitude; ?>"></td>

		</tr>
		<tr>
			<th><label for="fc_map_longitude">Longitude</label></th>
			<td><input id="fc_map_longitude" class="widefat" type="text" name="fc_map_longitude" value= "<?php echo $longitude; ?>"></td>
		</tr>
		<tr>
			<th><label for="fc_map_color">Color</label></th>
			<td><input name="fc_map_color" type="text" id="fc_map_color" value="<?php echo $color_picker ?>" data-default-color="#ffffff"></td>
		</tr>
		<tr>
			<th><label for="fc_map_water">Water Color</label></th>
			<td><input name="fc_map_water" type="text" id="fc_map_water" value="<?php echo $color_water ?>" data-default-color="#ffffff"></td>
		</tr>
		<tr>
			<th><label for="fc_map_road">Road name</label></th>
			<td>
				<select name="fc_map_road" id="fc_map_road" value="<?php echo $road ?>">
					<option class="default_value" value="<?php echo $road ?>" selected="selected"><?php echo ($road=='off'?'Hide':'Show')  ?></option>
					<option value="on">Show</option>
					<option value="off">Hide</option>
				</select>
			</td>
		</tr>
		<tr>
			<th><label for="fc_map_interest">Interest point</label></th>
			<td>
				<select name="fc_map_interest" id="fc_map_interest" value="<?php echo $interest ?>">
					<option class="default_value" value="<?php echo $interest?>" selected="selected"><?php echo ($interest=='off'?'Hide':'Show') ?></option>
					<option value="on">Show</option>
					<option value="off">Hide</option>
				</select>
			</td>
		</tr>
		<tr>
			<th><label for="fc_map_UI">Controls</label></th>
			<td>
				<select name="fc_map_UI" id="fc_map_UI" value="<?php echo $defautUI?>">
					<option class="default_value" value="<?php echo $defautUI?>" selected="selected"><?php echo ($defautUI=='false'?'Show':'Hide') ?></option>
					<option value="false">Show</option>
					<option value="true">Hide</option>
				</select>
			</td>
		</tr>
		<tr>
			<th><label for="fc_map_zoom">Zoom</label></th>
			<td><input id="fc_map_zoom" class="widefat" type="text" name="fc_map_zoom" value= "<?php echo $zoom; ?>"></td>
		</tr>
		<tr>
			<th><label for="fc_map_icon">Icon</label></th>
			<td><input id="fc_map_icon" class="widefat" type="text" name="fc_map_icon" value= "<?php echo $icon; ?>"></td>
		</tr>
	</table>
	
	<style>		
		.default_value{display:none;}
	</style>
	<?php
}

add_action('save_post', 'fc_save_post_maps');

function fc_save_post_maps($post_id) {
	if (isset($_POST['fc_map_height'])) {
		$height = sanitize_text_field( $_POST['fc_map_height']);
		update_post_meta($post_id, 'fc_map_height', $height);
	}
	if (isset($_POST['fc_map_width'])) {
		$width = sanitize_text_field( $_POST['fc_map_width']);
		update_post_meta($post_id, 'fc_map_width', $width);
	}
	if (isset($_POST['fc_map_color'])) {
		$color_picker = sanitize_text_field( $_POST['fc_map_color']);
		update_post_meta($post_id, 'fc_map_color', $color_picker);
	}
	if (isset($_POST['fc_map_water'])) {
		$color_water = sanitize_text_field( $_POST['fc_map_water']);
		update_post_meta($post_id, 'fc_map_water', $color_water);
	}
	if (isset($_POST['fc_map_longitude'])) {
		$longitude = sanitize_text_field( $_POST['fc_map_longitude']);
		update_post_meta($post_id, 'fc_map_longitude', $longitude);
	}
	if (isset($_POST['fc_map_latitude'])) {
		$latitude = sanitize_text_field( $_POST['fc_map_latitude']);
		update_post_meta($post_id, 'fc_map_latitude', $latitude);
	}
	if (isset($_POST['fc_map_road'])) {
		$road = sanitize_text_field( $_POST['fc_map_road']);
		update_post_meta($post_id, 'fc_map_road', $road);
	}
	if (isset($_POST['fc_map_interest'])) {
		$interest = sanitize_text_field( $_POST['fc_map_interest']);
		update_post_meta($post_id, 'fc_map_interest', $interest);
	}
	if (isset($_POST['fc_map_UI'])) {
		$defautUI = sanitize_text_field( $_POST['fc_map_UI']);
		update_post_meta($post_id, 'fc_map_UI', $defautUI);
	}
	if (isset($_POST['fc_map_zoom'])) {
		$zoom = sanitize_text_field( $_POST['fc_map_zoom']);
		update_post_meta($post_id, 'fc_map_zoom', $zoom);
	}
	if (isset($_POST['fc_map_icon'])) {
		$icon = sanitize_text_field( $_POST['fc_map_icon']);
		update_post_meta($post_id, 'fc_map_icon', $icon);
	}

}
 
function shortcode_professionnels($atts){
	
	
	$mySlug =$atts['slug'];

	
	if ( $post = get_page_by_path( $mySlug , OBJECT, 'maps' ) ) {
	    $id = $post->ID;
	 }else{
	    $id = 0;
	 }
	$post = get_post();	
	$width = get_post_meta($id, 'fc_map_width', true);
	$height = get_post_meta($id, 'fc_map_height', true);
	$longitude = get_post_meta($id, 'fc_map_longitude', true);
	$latitude = get_post_meta($id, 'fc_map_latitude', true);
	$color_picker = get_post_meta($id, 'fc_map_color', true);
	$color_water = get_post_meta($id, 'fc_map_water', true);
	$road = get_post_meta($id, 'fc_map_road', true);
	$interest = get_post_meta($id, 'fc_map_interest', true);
	$defaultUI = get_post_meta($id, 'fc_map_UI', true);
	$API = get_option('elefen_maps');
	$zoom  = get_post_meta($id, 'fc_map_zoom', true);
	$icon  = get_post_meta($id, 'fc_map_icon', true);
	
	if (!empty($icon)) {
		$custom_icon =	'var icon = {
									    url: "'.$icon.'", // url
									    scaledSize: new google.maps.Size(40, 40),
									    origin: new google.maps.Point(0,0), 
							    		anchor: new google.maps.Point(0, 0) 
									};
		
						  var marker = new google.maps.Marker({
						    position: new google.maps.LatLng('.$latitude.', '.$longitude.'),
						    icon: icon,
						    map: map
					  });';
	} else {
			$custom_icon =  '        var marker = new google.maps.Marker({
          position: new google.maps.LatLng('.$latitude.', '.$longitude.'),
          map: map,
         
        });
		';
	}
	
	// CODE POUR DÉCLARER LA CARTE
	$html = '		<style>
		#map{ width:'.$width.'; height:'.$height.'; }
		</style>

		<div id="map"></div>
	    <script>
	      var map;
      function initMap() {
        map = new google.maps.Map(document.getElementById(\'map\'), {
		  center: new google.maps.LatLng('.$latitude.', '.$longitude.'),
          zoom: '.$zoom.',
          disableDefaultUI: '.$defaultUI.',
          styles:  [
			    		{
					        "featureType": "all",
					        "elementType": "geometry.fill",
					        "stylers": [ { "color": "'.$color_picker.'" } ] },
					    {
					        "featureType": "poi",
					        "elementType": "labels",
					        "stylers": [ { "visibility": "'.$interest.'" } ] },
					    {
					        "featureType": "road",
					        "elementType": "labels",
					        "stylers": [ { "visibility": "'.$road.'" } ] },
					    {
					        "featureType": "water",
					        "elementType": "all",
					        "stylers": [ { "color": "'.$color_water.'" } ] }, 
					    {
					        "featureType": "all",
					        "elementType": "labels",
					        "stylers": [ { "invert_lightness": true } ] },
			            {
					        "featureType": "transit",
					        "elementType": "all",
					        "stylers": [ { "visibility": "'.$interest.'" } ] },
					 	{
					        "featureType": "landscape",
					        "elementType": "labels",
					        "stylers": [ { "visibility": "'.$interest.'"  } ] }
			        ]
        });
		
		'.$custom_icon.'
		
      }
    </script>

	    </script>
	    <script src="https://maps.googleapis.com/maps/api/js?key='.$API['google_map'].'&callback=initMap"   async defer></script>' 
		;
	// Renseigments des marqueurs
	return $html;	

}
add_shortcode('custom_map', 'shortcode_professionnels');
 
