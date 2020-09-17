<?php

function elefen_history_register_settings() {
   register_setting( 'elefen_history', 'elefen_history' );          
}
add_action( 'admin_init', 'elefen_history_register_settings' );

function elefen_history_register_options_page() {
  add_submenu_page( 'elefen', 'History', 'History', 'manage_options', 'elefen_history', 'elefen_history_options_page' );
}
add_action('admin_menu', 'elefen_history_register_options_page');

function elefen_history_options_page() {
	global $wpdb;
	
	$elefen_history = $wpdb->get_results( "
		SELECT option_name, option_value  
		FROM $wpdb->options
		WHERE option_name LIKE 'elefen_history_%' 
		"
	);
	
	echo '<style>#history_table_wrapper{ margin-top:20px; padding-right:20px; }</style>';
	
	echo '<table id="history_table" class="display">
		    <thead>
		        <tr>
		            <th>User</th>
		            <th>Action</th>
		            <th>Object type</th>
		            <th>Object</th>
		            <th>Date</th>
		        </tr>
		    </thead>
		    <tbody>
		        ';
	
	foreach ($elefen_history as $history) {
	$history = unserialize($history->option_value);
	$history = unserialize($history);
	$user = get_user_by('id', $history['user_ID']);
	$date = date('Y-m-d h:i:sa', $history['date']);
	
	echo '    <tr>
		            <td style="text-align: center;">'.$user->user_nicename.' ('.$history['user_ID'].')</td>
		            <td style="text-align: center;">'.$history['action'].'</td>
		            <td style="text-align: center;">'.$history['object_type'].'</td>
		            <td style="text-align: center;">'.$history['object'].'</td>
		            <td style="text-align: center;">'.$date.'</td>
            	</tr>
		';
	}
	echo        ' 
		    </tbody>
		</table>';
}

function my_upgrate_function( $upgrader_object, $options ) {
	
	
	if ($options['plugins']) {
		foreach($options['plugins'] as $each_plugin){
			$plugin_name = explode('/', $each_plugin); 
			history_data(get_current_user_id(), $options['action'], $options['type'], $plugin_name[0]);
		}	
	} else {
		foreach($options['themes'] as $each_plugin){
			$i++;
			history_data(get_current_user_id(), $options['action'], $options['type'], $each_plugin);
		}
	}
}
add_action( 'upgrader_process_complete', 'my_upgrate_function',10, 2);

function detect_plugin_activation( $plugin, $network_activation ) {
	$plugin_name = explode('/', $each_plugin); 
	$plugin_name = explode('/', $plugin); 	
	history_data(get_current_user_id(), 'activate', 'plugin', $plugin_name[0]);
}
add_action( 'activated_plugin', 'detect_plugin_activation', 10, 2 );

function detect_plugin_deactivation(  $plugin, $network_activation ) {
    $plugin_name = explode('/', $each_plugin); 
	$plugin_name = explode('/', $plugin); 
	history_data(get_current_user_id(), 'desactivate', 'plugin', $plugin_name[0]);	
}
add_action( 'deactivated_plugin', 'detect_plugin_deactivation', 10, 2 );

function detect_delete_post($post_id) {
	history_data(get_current_user_id(), 'delete', 'post', get_the_title($post_id));
}
add_action( 'before_delete_post', 'detect_delete_post' );

function detect_save_post($post_id) {
	history_data(get_current_user_id(), 'update', 'post', get_the_title($post_id));	
}
add_action( 'save_post', 'detect_save_post' );

function detect_user_register($user_id){
	$user = get_user_by('id', $user_id);
	history_data(get_current_user_id(), 'update', 'user', $user->user_email);	
}
add_action( 'edit_user_profile_update', 'detect_user_register', 10, 1 );

function detect_user_registration($user_id ) {
	$user = get_user_by('id', $user_id);
	history_data(get_current_user_id(), 'create', 'user', $user->user_email);
}
add_action( 'user_register', 'detect_user_registration', 10, 1 );

function my_delete_user( $user_id ) {
	$user = get_user_by('id', $user_id);
	history_data(get_current_user_id(), 'delete', 'user', $user->user_email);
}
add_action( 'delete_user', 'my_delete_user' );

function detect_switch_theme() {
	$theme  = wp_get_theme();
	history_data(get_current_user_id(), 'activate', 'theme', $theme->get( 'Name' ));
}
add_action('switch_theme', 'detect_switch_theme');

function history_data($user_id, $action, $object_type, $object) {
	$elefen_history = array(
		'user_ID' 		=> $user_id,
		'action' 		=> $action,
		'object_type'	=> $object_type,
		'object' 		=> $object, 
		'date'			=> time(),
	);	
	add_option('elefen_history_'.$object.'_'.time(), serialize($elefen_history) );
}

function add_table_style() {
	wp_enqueue_script( 'js_dataTable', 'https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"', array(), null, true );
	wp_enqueue_script( 'js_history', plugins_url('/elefen/assets/js/history.js'), array(), null, true );
	wp_enqueue_style ( 'style_dataTable', 'https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css"', array(), '0.1.0', 'all' );
}
add_action('admin_head', 'add_table_style'); 

add_action('updated_option', function( $option_name , $old_value, $value){
	
	if ($option_name == 'blog_public') {
		$value = get_option($option_name);
		history_data(get_current_user_id(), ($value == 1 ? "desactivate" : "activate" ), 'option' , 'Index change');
	}
});









