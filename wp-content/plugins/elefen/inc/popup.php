<?php

add_action( 'admin_init', 'elefen_popups_register_settings' );
function elefen_popups_register_settings() {
   register_setting( 'elefen_popups', 'elefen_popups' );          
}

add_action( 'wp_enqueue_scripts', 'elefen_popups_enqueue_scripts' );
function elefen_popups_enqueue_scripts() {
		wp_enqueue_script( 'elefen-cookie-script', plugins_url('/elefen/assets/js/jquery.cookie.js'), array( 'jquery' ) );	
		wp_enqueue_style( 'font-awesome-free', '//use.fontawesome.com/releases/v5.2.0/css/all.css' );
}

add_action( 'admin_enqueue_scripts', 'plugin_backend_scripts');
function plugin_backend_scripts( $hook ) {
    
    wp_enqueue_style('datetimepicker', plugins_url('/elefen/assets/css/jquery.datetimepicker.css') );
	wp_enqueue_script('datetimepicker', plugins_url('/elefen/assets/js/jquery.datetimepicker.full.js'), array('jquery') );
    
    wp_enqueue_style('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css' );
	wp_enqueue_script('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js', array('jquery') );
    wp_enqueue_style( 'wp-color-picker');
	wp_enqueue_script( 'wp-color-picker');
}

add_action('init', 'custom_popup_init');
function custom_popup_init(){
	$labels = array(
		'name'				=> 'Add bar or popup',
		'singular_name'	 	=> 'Bars & Popup',
		'menu_name'			=> 'Bars & Popup',
		'name_admin_bar'	=> 'Bars & Popup',
		'not_found'			=> 'No custom bars or popup found',
		'not_found_in_trash'	=> 'No custom bars or popup found in Trash.'
	);
	
	$args = array(
		'labels' 			=> $labels,
		'public' 			=> false,
		'show_ui'			=> true, 
		'show_in_menu'		=> true,
		'capability_type'	=>'post',
		'menu_position'		=> null,
		'supports' 			=> array ('title', 'thumbnail'),
		'register_meta_box_cb'	=> 'fc_meta_box_popup'
	);
	
	register_post_type('popups', $args);
}

function fc_meta_box_popup() {
	add_meta_box('popup_custom', 'Settings', 'fc_popup_metabox_content');
}

function fc_popup_metabox_content ($postPopup) {

	echo custom_css_js_popup();

	$prfx_stored_meta = get_post_meta( $postPopup->ID );
	$page = get_post_meta($postPopup->ID , 'page_meta_box', true);
	$info_page = get_post_meta($postPopup->ID, 'popup_page', true);
	$choix = get_post_meta($postPopup->ID, 'choix_style', true);
	$btn_class = get_post_meta($postPopup->ID, 'btn_class', true);
	$emplacement_bars = get_post_meta($postPopup->ID, 'emplacement_bars', true);
	$interval = get_post_meta($postPopup->ID, 'display_date', true);

	$date_start = date('Y-m-d H:i', get_post_meta($postPopup->ID, 'popup_start', true));
	$date_end = date('Y-m-d H:i', get_post_meta($postPopup->ID, 'popup_end', true));
	
	$post_list = get_posts( array(
		'post_type'		=> 'page',
	    'orderby'    	=> 'title',
	    'sort_order' 	=> 'asc'
	) );
	?>

	<div class="wrap">
		<h1><?php echo __('Advanced Settings', 'elefen') ?></h1>

		<div class="form-input">
			<label for="choix_style">
				<?php echo __('Type', 'elefen') ?>
				<span> <?php  echo __('Choose bar or popup for display' , 'elefen') ?></span>
			</label>
			<select name="choix_style" id="choix_style">
				<option value="popup" <?php selected( $choix, "popup") ?>><?php echo __('Popup', 'elefen') ?></option>
				<option value="bars" <?php selected( $choix, "bars") ?>><?php echo __('Bar') ?></option>
			</select>
		</div>

		<h1><?php echo __('Location', 'elefen') ?></h1>

		<div class="form-input">
			<label for="display_popup">
				<?php echo __('Test mode', 'elefen') ?>
				<span><?php echo __('Copy url to display popup or bar all time', 'elefen') ?></span>
			</label>
			<span><?php echo get_home_url()."/?popup=".get_the_ID()."" ?></span>
		</div>

		<div class="form-input">
			<label for="popup_page">
				<?php echo __('Display', 'elefen') ?>
				<span><?php echo __('Choose all page to display on all page, or select page to select specifique page to display your popup or bar', 'elefen') ?></span>
			</label>
			<select name= "popup_page" id="popup_page">
				<option value="all" <?php selected($info_page, 'all') ?> ><?php echo __('On all pages', 'elefen') ?></option>
				<option value="select"  <?php selected($info_page, 'select') ?> ><?php echo __('Select pages', 'elefen') ?></option>
			</select>
		</div>

		<div id="popup_class_page" class="form-input <?php echo ($info_page == 'all' || $info_page == null ? 'hide-input': '') ?>">
			<label for="popup_class_page">
			<?php echo __('Select page', 'elefen') ?></label>
			<select name="page_popup[]" id="page_popup" multiple="multiple" class='wc-enhanced-select'>
				<?php foreach ($post_list as $post) { ?>
					<option value="<?php echo $post->ID ; ?>" <?php echo ( !empty( $page ) && in_array( $post->ID, $page ) ? ' selected="selected"' : '' ) ?>><?php echo $post->post_title ; ?></option>
			<?php	} ?>
				</select> 
		</div>

		<div id="position_bars" class="form-input <?php echo ( $choix == 'popup' || $choix == null ? 'hide-input' : '' ) ?>">
			<label for="emplacement_bars">
				<?php echo __('Page location', 'elefen') ?>
				<span><?php echo __('Choose location of your bars (top or bottom)', 'elefen') ?></span>
			</label>
			<select name="emplacement_bars" id="emplacement_bars">
				<option value="top" <?php selected( $emplacement_bars,'top') ?>><?php echo __('Top', 'elefen') ?></option>
				<option value="bottom" <?php selected( $emplacement_bars,'bottom') ?>><?php echo __('Bottom', 'elefen') ?></option>
			</select>
		</div>

		<div class="form-input" id="class-btn">
			<label for="btn_class">
				<?php echo __('CSS selector', 'elefen') ?>
				<span><?php echo __('Enter a CSS selector to display bar or popup on click of a button', 'elefen') ?></span>
			</label>
			<input type="text" name="btn_class" id="btn_class" value="<?php echo ($btn_class); ?>"/>
		</div>

		<h1><?php echo __('Style', 'elefen') ?></h1>

		<div class="form-input">
			<label for="fc_background_color">
				<?php echo __('Background color', 'elefen') ?>
			</label>
			<input name="fc_background_color" type="text" id="fc_background_color" value="<?php echo ($prfx_stored_meta['fc_background_color'][0]) ?>" >
		</div>
		
		<div class="form-input">
			<label for="fc_popup_color"><?php echo __('Text color', 'elefen') ?></label>
			<input name="fc_popup_color" type="text" id="fc_popup_color" value="<?php echo ($prfx_stored_meta['fc_popup_color'][0]) ?>">
		</div>

		<h1><?php echo __('Popup duration', 'elefen') ?></h1>
		
		<div class="form-input" id="interval">
			<label for="display_date"><?php echo __('Activate date interval', 'elefen') ?></label>
			<input type="checkbox" name="display_date" id="display_date" value="yes" <?php checked( $interval, 'yes' ); ?> />
		</div>

		<div class="form-input date <?php echo ($interval == 'no' ? 'hide-input' : '') ?>" id="date-debut">
			<label for="date-picker-start"><?php echo __('Start date', 'elefen') ?></label>
			<input name="popup_start" type="text" id="date-picker-start" value="<?php echo $date_start ; ?>" >
			<span class="input-group-addon">
					<span class="glyphicon glyphicon-calendar"></span>
			</span>
		</div>
	
		<div class="form-input <?php echo ($interval == 'no' ? 'hide-input' : '') ?>" id="date-fin">
			<label for="date-picker-end"><?php echo __('End date', 'elefen') ?></label>
			<input name="popup_end" type="text" id="date-picker-end" value="<?php echo $date_end ; ?>" >
		</div>

		<div class="form-input">
			<label for="time_cookie"><?php echo __('Cookie time (0 for no cookie)', 'elefen') ?></label>
			<input type="number" name="time_cookie" id="time_cookie" value="<?php echo ($prfx_stored_meta['time_cookie'][0]) ?>"> &nbsp; <?php echo __('days', 'elefen') ?>
		</div>
			
			
		<?php
			wp_editor($prfx_stored_meta['meta_content_editor'][0], 'meta_content_editor');
		?>	
	</div>

<?php
}

function custom_css_js_popup() {
	?>

	<style>
		#ui-datepicker-div {background: #f5f5f5;padding: 15px;border: 1px solid #cacaca;}
		form h2 {margin-bottom: 0px;}
		.ui-icon {display: block;text-indent: 0px;overflow: hidden;background-repeat: no-repeat;cursor: pointer;}
		.form-input {display: flex;margin-bottom: 20px;align-items: center;}
		.form-input label {width: 25%;font-weight: bold;}
		.hide-input {display : none;}	
		.select2.select2-container.select2-container--default {	width: 162px !important;}
		.wp-picker-input-wrap label {display: inline-block;vertical-align: top;	width: auto !important;}
		.form-input label span {display: block;font-weight: normal;font-size: 12px;margin-top: 5px;}
		.wrap h1 {margin-bottom : 25px !important;}
	</style>
	
	<script>

		jQuery(document).ready(function(){

			jQuery('#fc_popup_color').wpColorPicker();
			jQuery('#fc_background_color').wpColorPicker();
			jQuery('.wc-enhanced-select').select2();
			
			jQuery('#date-picker-start').datetimepicker({
				format: 'Y-m-d H:i' 
			});
			jQuery('#date-picker-end').datetimepicker({
				format: 'Y-m-d H:i'
			});

			jQuery('#choix_style option').on('click', function() {
				if(jQuery(this).val() == 'bars') {
					jQuery('#class-click, #class-btn').addClass('hide-input');
					jQuery('#position_bars').removeClass('hide-input');
				} else {
					jQuery('#class-click').removeClass('hide-input');
					jQuery('#position_bars').addClass('hide-input');
					if (jQuery('#class-click input').prop('checked')) {
						jQuery('#class-btn').removeClass('hide-input')
					}
				}
			});

			jQuery('#class-click input').on('click', function() {
				if (!jQuery(this).prop('checked')) {
					jQuery('#class-btn').addClass('hide-input');
				} else {
					jQuery('#class-btn').removeClass('hide-input')
				}
			});

			jQuery('#popup_page option').on('click', function() {
				if (jQuery(this).val() == 'all') {
					jQuery('#popup_class_page').addClass('hide-input');
				} else {
					jQuery('#popup_class_page').removeClass('hide-input');
				}
			});

			jQuery('#interval input').on('click', function() {
				if (jQuery(this).prop('checked')) {
					jQuery('#date-debut, #date-fin').removeClass('hide-input');
				} else {
					jQuery('#date-debut, #date-fin').addClass('hide-input');
				}
			})
		}); 	

	</script>
	<?php
}


add_action('save_post', 'save_post_meta_popup');
function save_post_meta_popup($post_id) {
	if( isset( $_POST[ 'popup_page' ] ) ) {
   		update_post_meta( $post_id, 'popup_page', $_POST['popup_page'] );
	} 
	if ( isset ( $_POST['meta_content_editor'] ) ) {
		update_post_meta( $post_id, 'meta_content_editor', $_POST['meta_content_editor'] );
	}
	if (isset($_POST['btn_class'])) {
		update_post_meta($post_id, 'btn_class', sanitize_text_field($_POST['btn_class']));
	}	
	if (isset($_POST['popup_class_page'])) {
		update_post_meta($post_id, 'popup_class_page', sanitize_text_field($_POST['popup_class_page']));
	}			
	if (isset($_POST['page_popup'])) {
		update_post_meta($post_id, 'page_meta_box', $_POST['page_popup']);
	}
	if(isset($_POST['choix_style'])) {
		update_post_meta($post_id, 'choix_style', $_POST['choix_style']);
	}
	if(isset($_POST['fc_popup_color'])) {
		update_post_meta( $post_id, 'fc_popup_color', $_POST['fc_popup_color'] );
	}
	if(isset($_POST['fc_background_color'])) {
		update_post_meta( $post_id, 'fc_background_color', $_POST['fc_background_color'] );
	}
	if(isset($_POST['time_cookie'])) {
		update_post_meta($post_id, 'time_cookie', $_POST['time_cookie']);
	}
	if(isset($_POST['emplacement_bars'])) {
		update_post_meta( $post_id, 'emplacement_bars', $_POST['emplacement_bars'] );
	}
	if(isset($_POST['display_date'])) {
		update_post_meta($post_id, 'display_date', 'yes');
	} else {
		update_post_meta($post_id, 'display_date', 'no');
	}
	if (isset($_POST['popup_start']) && isset($_POST['display_date'])) {
		update_post_meta($post_id, 'popup_start', strtotime($_POST['popup_start']));
	} else {
		update_post_meta($post_id, 'popup_start', strtotime('2020-01-01 00:00'));
	}
	if (isset($_POST['popup_end']) && isset($_POST['display_date'])) {
		update_post_meta($post_id, 'popup_end', strtotime($_POST['popup_end']));
	} else {
		update_post_meta($post_id, 'popup_end', strtotime('2100-01-01 00:00'));
	}
}


add_filter('wp_footer', 'display_popup_and_bars');
function display_popup_and_bars($content) {
	
	global $wpdb;
	global $wp_query;

	$id_page = get_the_id();
	
	$results = $wpdb->get_results( "SELECT
										DISTINCT
										 wp.post_id
									FROM 
										{$wpdb->prefix}postmeta wp inner JOIN
									    (select meta_value, post_id from {$wpdb->prefix}postmeta where meta_key = 'popup_start') pm1 on pm1.post_id = wp.post_id inner JOIN
									    (select meta_value, post_id from {$wpdb->prefix}postmeta where meta_key = 'popup_end') pm2 on pm2.post_id = wp.post_id
									where 
										pm1.meta_value <= '".time()."' AND
									    pm2.meta_value >= '".time()."'", OBJECT );
			
			
    $current_page = get_the_ID();
	foreach ($results as $result) {
		$id = $result->post_id;	
		$page = get_post_meta($id, 'popup_page', true);
		$page_pop = get_post_meta($id, 'page_meta_box', true);
		$type = get_post_meta($id, 'choix_style', true);
		if ($page == 'all' || in_array($current_page, $page_pop)) {
				echo contruct_bars($id);
		}
	}
	
	echo style_base();
}

function contruct_popup($id) {

	$prfx_stored_meta = get_post_meta($id);

	$popup = "<div style='display : none;' class='wrapper_popup_".$id."'>
				 <div class='nav-popup-custom'>
						".$prfx_stored_meta['meta_content_editor'][0]."
						<a class='close-popup' href='javascript:void(null);'><i class='fas fa-times'></i></a>
				 </div>
			 </div>
	";

	return $popup;
}

function contruct_bars($id) {

	$info = get_post_meta($id);
	$type = $info['choix_style'][0];
	
	$html .= "<div style='display : none;' id='".$type.'-'.$id."' class='wrapper_".$type."'>
        		<div class='nav-".$type."-custom'>
        			".$info['meta_content_editor'][0]."
        			<a class='close-".$type."' href='javascript:void(null);'><i class='fas fa-times'></i></a>
        		</div>
    		</div>";
	
	if ($type == 'bars') {
		$html .= "<style>
					#".$type.'-'.$id." {
						background: ". $info['fc_background_color'][0] .";
						".$info['emplacement_bars'][0]." : 0;
						color : ".$info['fc_popup_color'][0].";
					}
					#".$type.'-'.$id." .close-bars {
						color: ".$info['fc_popup_color'][0].";
					}
				</style>";
	} else {
		$html .= "<style>
					#".$type.'-'.$id." .nav-popup-custom {
						background: ".$info['fc_background_color'][0].";
						color: ".$info['fc_popup_color'][0].";
					}
					#".$type.'-'.$id." .close-popup {
						color: ".$info['fc_popup_color'][0].";
					}
				</style>";
	}
	
	$html .= "<script>jQuery( document ).ready(function() {";
	    $html .= "jQuery('#".$type.'-'.$id." .close-".$type."').click(function(){
    	        	jQuery('#".$type.'-'.$id."').hide();
    				jQuery.cookie('cookie_close_".$type."_".$id."', 'true', { expires: ".$info['time_cookie'][0].", path:'/' });
    			  });";
    	if ($_GET['popup'] == $id) {
    	    $html .= "jQuery('#".$type.'-'.$id."').show();";
    	}else if ( !$info['btn_class'][0] ) {
    	    $html .= "if (!jQuery.cookie('cookie_close_".$type."_".$id."')) { jQuery('#".$type.'-'.$id."').show(); }";
    	}else{
    	    $html .= "jQuery('".$info['btn_class'][0]."').on('click', function(){ jQuery('#".$type.'-'.$id."').show(); });";
    	}
    $html .= "}); </script>";

	return $html;
}

function style_base() {

	$style = "<style>
				.wrapper_bars {
					position: fixed;
					width: 100%;
					text-align: center;
					z-index : 999;
				}
				.wrapper_bars .close-bars {
					position: absolute;
					right: 10px;
					top: 2px;
				}
				.wrapper_popup {
					position: fixed;
					background: rgb(0,0,0,0.5);
					top: 0;
					width: 100%;
					color: white;
					left: 0;
					height: 100%;
				}
				.wrapper_popup .nav-popup-custom {
					position: fixed;
					width: 50%;
					top: 50%;
					left: 50%;
					transform: translate(-50%, -50%);
					padding: 25px;
				}
				.wrapper_popup .close-popup {
					position: absolute;
					top: 5px;
					right: 15px;
				}
			</style> ";

	return $style;
}