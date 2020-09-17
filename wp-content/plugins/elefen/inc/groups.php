<?php

function elefen_groups_register_settings() {
   register_setting( 'elefen_groups', 'elefen_groups' );          
}
add_action( 'admin_init', 'elefen_groups_register_settings' );

function elefen_groups_register_options_page() {
  add_submenu_page( 'elefen', 'Groups', 'Groups', 'manage_options', 'elefen_groups', 'elefen_groups_options_page' );
}
add_action('admin_menu', 'elefen_groups_register_options_page');

function create_tables_if_not_exist(){
	
    global $wpdb;
	require_once(ABSPATH.'wp-admin/includes/upgrade.php');
	
	$table_name = $wpdb->prefix."elefen_groups";
	if ($wpdb->get_var("SHOW TABLES LIKE '".$table_name."'") != $table_name){
	    $sql = 'CREATE TABLE '.$table_name.'(
	        id INTEGER NOT NULL AUTO_INCREMENT,
	        group_name VARCHAR(255),
	        description VARCHAR(255),
	        parent_id INTEGER,
	        PRIMARY KEY  (id))';
	
	    dbDelta($sql);
	
	}
	
	$table_name = $wpdb->prefix."elefen_groups_post_type";
	if ($wpdb->get_var("SHOW TABLES LIKE '".$table_name."'") != $table_name){
	    $sql = 'CREATE TABLE '.$table_name.'(
	        id INTEGER NOT NULL AUTO_INCREMENT,
	        post_type VARCHAR(255),
	        PRIMARY KEY  (id))';
	
	    dbDelta($sql);
	
	}

	$table_name = $wpdb->prefix."elefen_rel_groups_users";
	if ($wpdb->get_var("SHOW TABLES LIKE '".$table_name."'") != $table_name){
	    $sql = 'CREATE TABLE '.$table_name.'(
	        id INTEGER NOT NULL AUTO_INCREMENT,
	        group_id INTEGER NOT NULL,
	        user_id INTEGER NOT NULL,
	        PRIMARY KEY  (id))';
	
	    dbDelta($sql);
	
	}

	$table_name = $wpdb->prefix."elefen_rel_groups_posts";
	if ($wpdb->get_var("SHOW TABLES LIKE '".$table_name."'") != $table_name){
	    $sql = 'CREATE TABLE '.$table_name.'(
	        id INTEGER NOT NULL AUTO_INCREMENT,
	        group_id INTEGER NOT NULL,
	        post_id INTEGER NOT NULL,
	        PRIMARY KEY  (id))';
	
	    dbDelta($sql);
	
	}

	$table_name = $wpdb->prefix."elefen_rel_groups_terms";
	if ($wpdb->get_var("SHOW TABLES LIKE '".$table_name."'") != $table_name){
	    $sql = 'CREATE TABLE '.$table_name.'(
	        id INTEGER NOT NULL AUTO_INCREMENT,
	        group_id INTEGER NOT NULL,
	        term_id INTEGER NOT NULL,
	        PRIMARY KEY  (id))';
	
	    dbDelta($sql);
	
	}

}

add_action('init', 'taxonomy_group_init');
function taxonomy_group_init(){
	
	global $wpdb;
	
	$table_name = $wpdb->prefix."elefen_groups_post_type";
	$sql = 'select * from '.$table_name;

	$results = $wpdb->get_results($sql);
	
	
	add_action( 'create_term', 'add_groups_taxonomy_fields', 10, 3 );  
	
	foreach ( $results  as $result ) {
			
	   $taxonomies = get_object_taxonomies($result->post_type, 'objects');
	   
	   foreach($taxonomies as $taxonomy){

		   add_action( $taxonomy->name.'_edit_form_fields','taxonomy_edit_group_meta_field', 99, 2 );
		   add_action( 'edited_'.$taxonomy->name, 'save_groups_taxonomy_fields', 99, 2 );  
		   

		   add_filter('manage_edit-'.$taxonomy->name.'_columns', 'groups_modify_taxonomy_table');
		   add_filter('manage_'.$taxonomy->name.'_custom_column', 'groups_modify_taxonomy_table_row',10,3);
		   
	   }
	   
	}

}

add_action('init', 'post_type_group_init');
function post_type_group_init(){
	
	global $wpdb;
	
	$table_name = $wpdb->prefix."elefen_groups_post_type";
	$sql = 'select * from '.$table_name;

	$results = $wpdb->get_results($sql);
	
	foreach ( $results  as $post_type ) {
			
	   add_filter('manage_edit-'.$post_type->post_type.'_columns', 'groups_modify_posttype_table');
	   add_filter('manage_'.$post_type->post_type.'_posts_custom_column', 'groups_modify_posttype_table_row',10,3);
		
	}

}



// Add term page
function taxonomy_add_group_meta_field() {

	global $wpdb;
	
	$table_name = $wpdb->prefix."elefen_groups";
	$sql = 'select * from '.$table_name.' order by group_name';

	$results = $wpdb->get_results($sql);

	$elems = array();
	
	function buildTree(array $elements, $parentId = 0) {
	    $branch = array();
	
	    foreach ($elements as $element) {
	    	
	        if ($element['parent_id'] == $parentId) {
	        	
	            $children = buildTree($elements, $element['id']);
				
	            if ($children) {
	                $element['children'] = $children;
	            }
	            $branch[] = $element;
	        }
	    }
	
	    return $branch;
	}
	
	$array = json_decode(json_encode($results), True);
	
	$tree = buildTree($array);

	echo "<div style='line-height: 1.3;font-weight: 600;margin-top:20px;margin-bottom:20px;'>Groups</div>";
				
	function buildTableRecursive(array $elements, $depth) {

	    foreach ($elements as $element) {
	    	
			$iWhile = 1;
			$sTrait = "";
			$margin = "0";
			while($iWhile<$depth){
				$sTrait = $sTrait."— ";
				$iWhile++;
			}
			$margin = 18 * ($depth - 1);
			
				echo '<li style="margin-left:'.$margin.'px;line-height: 22px;word-wrap: break-word;padding:0px;margin-bottom:0px;">';
					
					echo '<input id="chk_'.$element['id'].'" type="checkbox" name="terms_group[]" value="'.$element['id'].'">';
					
					echo '<label style="display:inline-block;" for="chk_'.$element['id'].'">'.$element['group_name'].'</label>';
					
				echo '</li>';
			
	        if (array_key_exists('children', $element)){
	        	
	            buildTableRecursive($element['children'], ($depth+1));
				
	        }
			
			if($depth == 1){
				echo '<li>';
					echo '<hr>';
				echo '</li>';
			}
			
	    }
		
	}

	echo "<ul>";		
		
		buildTableRecursive($tree, 1);
		
	echo "</ul>";

}

// Save extra taxonomy fields callback function.
function save_groups_taxonomy_fields( $term_id ) {
	
	global $wpdb;

    if ( isset( $_POST['hid_group_change'] ) ){
    	
    	
		$table_name = $wpdb->prefix."elefen_rel_groups_terms";
		
		//POLYLANG TRANSLATION
		$arrTermIds = array();
		array_push($arrTermIds, $term_id);
		if(is_plugin_active( 'polylang-pro/polylang.php' ) == 1 || is_plugin_active( 'polylang/polylang.php' ) == 1){
			
			global $polylang;
		    $translationIds = $polylang->model->get_translations('term', $term_id);
		    $currentLang = pll_get_term($term_id, pll_current_language());
			
		    foreach ($translationIds as $key=>$translationID){
		        if($translationID != $currentLang){
		        	
		            $availableLang = $polylang->model->get_languages_list();
					
		            foreach( $availableLang as $lang){
		            	
		                if($key == $lang->slug){
		                	
							array_push($arrTermIds, $translationID);
							
		                }
						
		            }
		        }
		    }

		}
		
		foreach($arrTermIds as $term_id){
		
			$sql = 'select * from '.$table_name.' where term_id = '.$term_id;
		
			$results = $wpdb->get_results($sql);
			$arrDbChoices = array();
			
			foreach ( $results  as $result ) {
				
				array_push($arrDbChoices,$result->group_id);
				
			}
			
			//AJOUT DANS TABLE SI PAS LA
			foreach($_POST['terms_group'] as $choice){
				
				echo $choice;
				
				echo "<hr>";
					
				if(in_array($choice, $arrDbChoices)){
					
				}else{
	
					$wpdb->insert($wpdb->prefix."elefen_rel_groups_terms", array('term_id'=>$term_id, 'group_id'=>$choice));
					
				}
				
			}
			
			//DELETE DANS TABLE SI PAS LA
			foreach($arrDbChoices as $choice){
				
				if(in_array($choice, $_POST['terms_group'])){
					
				}else{
					
					$wpdb->delete($wpdb->prefix."elefen_rel_groups_terms", array('term_id'=>$term_id, 'group_id'=>$choice));
					
				}
				
			}
			
		}
							
	
    }


	


}   

function add_groups_taxonomy_fields( $term_id, $tt_id, $taxonomy ) {
	
	global $wpdb;
	
	$parts = parse_url($_POST['_wp_http_referer']);
	parse_str($parts['query'], $query);

	if(isset($_POST['term_tr_lang'])){
		
		foreach($_POST['term_tr_lang'] as $originalTermid){
					
			$table_name = $wpdb->prefix."elefen_rel_groups_terms";
			$sql = 'select * from '.$table_name.' where term_id = '.$originalTermid;
		
			$results = $wpdb->get_results($sql);
			
			foreach ( $results  as $result ) {
				
				$wpdb->insert($wpdb->prefix."elefen_rel_groups_terms", array('term_id'=>$term_id, 'group_id'=>$result->group_id));
				
			}	
			
		}
		
	}
	
	
}

// edit term page
function taxonomy_edit_group_meta_field($test) {
	
	global $wpdb;
	
	$table_name = $wpdb->prefix."elefen_rel_groups_terms";
	$sql = 'select * from '.$table_name.' where term_id = '.$_GET["tag_ID"];

	$results = $wpdb->get_results($sql);
	$arrChoices = array();
	
	foreach ( $results  as $result ) {
		
		array_push($arrChoices,$result->group_id);
		
	}
	
	
	$table_name = $wpdb->prefix."elefen_groups";
	$sql = 'select * from '.$table_name.' order by group_name';

	$results = $wpdb->get_results($sql);

	$elems = array();
	
	function buildTree(array $elements, $parentId = 0) {
	    $branch = array();
	
	    foreach ($elements as $element) {
	    	
	        if ($element['parent_id'] == $parentId) {
	        	
	            $children = buildTree($elements, $element['id']);
				
	            if ($children) {
	                $element['children'] = $children;
	            }
	            $branch[] = $element;
	        }
	    }
	
	    return $branch;
	}
	
	$array = json_decode(json_encode($results), True);
	
	$tree = buildTree($array);
	
	echo '<tr class="form-field">';
		echo '<th scope="row" valign="top"><label for="cat_Image_url">Groups</label></th>';
		echo '<td>';

			function buildTableRecursive(array $elements, $depth, $arrChoices) {
		
			    foreach ($elements as $element) {
			    	
					$iWhile = 1;
					$sTrait = "";
					$margin = "0";
					while($iWhile<$depth){
						$sTrait = $sTrait."— ";
						$iWhile++;
					}
					$margin = 18 * ($depth - 1);
					
					$isChecked = '';
					if(in_array($element['id'], $arrChoices)){
						$isChecked = 'checked';
					}
			
						echo '<li style="margin-left:'.$margin.'px;line-height: 22px;word-wrap: break-word;padding:0px;margin-bottom:0px;">';
							
							echo '<input '.$isChecked.' id="chk_'.$element['id'].'" type="checkbox" name="terms_group[]" value="'.$element['id'].'">';
							
							echo '<label for="chk_'.$element['id'].'">'.$element['group_name'].'</label>';
							
						echo '</li>';
					
			        if (array_key_exists('children', $element)){
			        	
			            buildTableRecursive($element['children'], ($depth+1), $arrChoices);
						
			        }
					
			    }
				
			}
		
			echo "<ul style='max-height:500px;overflow-x:auto;'>";		
				
				buildTableRecursive($tree, 1, $arrChoices);
				
			echo "</ul>";
			
			echo "<input type='hidden' name='hid_group_change' value='-1' />";
		
		echo "</td>";
	
	echo "</tr>";

}


//Add users to groups
function add_users_to_groups($users = null, $groups = null){
	
	global $wpdb;
	$table_name = $wpdb->prefix."elefen_rel_groups_users";
	
	if (is_array($users)){
		
		if (is_array($groups)){
				
			foreach ( $users  as $user ) {
				
				//AJOUT DANS TABLE SI PAS LA
				foreach($groups as $group){
								
					$countInDb = $wpdb->get_var("SELECT COUNT(*) from ".$wpdb->prefix."elefen_rel_groups_users where group_id = ".$group." and user_id = ".$user );			
						
					if($countInDb == 0){
						$wpdb->insert($wpdb->prefix."elefen_rel_groups_users", array('user_id'=>$user, 'group_id'=>$group));
					}
					
				}	
				
			}

    	}else{
    		
			foreach ( $users  as $user ) {
				
				$countInDb = $wpdb->get_var("SELECT COUNT(*) from ".$wpdb->prefix."elefen_rel_groups_users where group_id = ".$groups." and user_id = ".$user );			
					
				if($countInDb == 0){
					$wpdb->insert($wpdb->prefix."elefen_rel_groups_users", array('user_id'=>$user, 'group_id'=>$groups));
				}
				
			}
			
    	}
        
    }else{
    	
		if (is_array($groups)){
				
			foreach($groups as $group){
							
				$countInDb = $wpdb->get_var("SELECT COUNT(*) from ".$wpdb->prefix."elefen_rel_groups_users where group_id = ".$group." and user_id = ".$users );			
					
				if($countInDb == 0){
					$wpdb->insert($wpdb->prefix."elefen_rel_groups_users", array('user_id'=>$users, 'group_id'=>$group));
				}
				
			}	
			
    	}else{
    		
			$countInDb = $wpdb->get_var("SELECT COUNT(*) from ".$wpdb->prefix."elefen_rel_groups_users where group_id = ".$groups." and user_id = ".$users );			
					
			if($countInDb == 0){
				$wpdb->insert($wpdb->prefix."elefen_rel_groups_users", array('user_id'=>$users, 'group_id'=>$groups));
			}
			
		}
    					
    }
	
}

//Remove users to groups
function remove_users_to_groups($users = null, $groups = null){
	
	global $wpdb;
	$table_name = $wpdb->prefix."elefen_rel_groups_users";
	
	if (is_array($users)){
		
		if (is_array($groups)){
				
			foreach ( $users  as $user ) {
				
				//AJOUT DANS TABLE SI PAS LA
				foreach($groups as $group){
								
					$countInDb = $wpdb->get_var("SELECT COUNT(*) from ".$wpdb->prefix."elefen_rel_groups_users where group_id = ".$group." and user_id = ".$user );			
						
					if($countInDb > 0){
						$wpdb->delete($wpdb->prefix."elefen_rel_groups_users", array('user_id'=>$user, 'group_id'=>$group));
					}
					
				}	
				
			}

    	}else{
    		
			foreach ( $users  as $user ) {
				
				$countInDb = $wpdb->get_var("SELECT COUNT(*) from ".$wpdb->prefix."elefen_rel_groups_users where group_id = ".$groups." and user_id = ".$user );			
					
				if($countInDb > 0){
					$wpdb->delete($wpdb->prefix."elefen_rel_groups_users", array('user_id'=>$user, 'group_id'=>$groups));
				}
				
			}
			
    	}
        
    }else{
    	
		if (is_array($groups)){
				
			foreach($groups as $group){
							
				$countInDb = $wpdb->get_var("SELECT COUNT(*) from ".$wpdb->prefix."elefen_rel_groups_users where group_id = ".$group." and user_id = ".$users );			
					
				if($countInDb > 0){
					$wpdb->delete($wpdb->prefix."elefen_rel_groups_users", array('user_id'=>$users, 'group_id'=>$group));
				}
				
			}	
			
    	}else{
    		
			$countInDb = $wpdb->get_var("SELECT COUNT(*) from ".$wpdb->prefix."elefen_rel_groups_users where group_id = ".$groups." and user_id = ".$users );			
					
			if($countInDb > 0){
				$wpdb->delete($wpdb->prefix."elefen_rel_groups_users", array('user_id'=>$users, 'group_id'=>$groups));
			}
			
		}
    					
    }
	
}

function is_user_in_group($user_id, $group_id){
				
	global $wpdb;
	$table_name = $wpdb->prefix."elefen_rel_groups_users";
	
	$countInDb = $wpdb->get_var("SELECT COUNT(*) from ".$wpdb->prefix."elefen_rel_groups_users where group_id = ".$group_id." and user_id = ".$user_id );	
			
	if($countInDb > 0){
		return true;
	}else{
		return false;
	}
	
}

function get_groups_by_user_id($user_id){
				
	global $wpdb;
	$table_name = $wpdb->prefix."elefen_rel_groups_users";
	
	$sql = 'select g.id, g.group_name from '.$wpdb->prefix.'elefen_rel_groups_users rg inner join '.$wpdb->prefix.'elefen_groups g ON g.id = rg.group_id where rg.user_id = '.$user_id.' order by g.group_name';
		
	$results = $wpdb->get_results($sql);
	
	$groups = array();
	
	foreach($results as $result){
		
		$groups[$result->id]=$result->group_name;
		
	}
						
	return $groups;			
	
}

function elefen_groups_options_page()
{

	create_tables_if_not_exist();
	
	if(isset($_GET['if_already_connected_html'])){
		
		update_option( "if_already_connected_html", $_GET['if_already_connected_html'] );
		
	}
	
	?>
	
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<link href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css" rel="stylesheet" />
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

	<style>

		td .row-title{cursor:pointer;color:#00a0d2;}
		.row-actions span{cursor:pointer;}


		ul.documentation{list-style: disc;}
		ul.documentation li{margin-left:40px;margin-bottom:20px;}
		
	</style>
	
	<div class="wrap">
		<h1><?php echo __("Advanced settings - groups", "elefen"); ?></h1>
		
		<div id="tabs">
			  <ul>
			  	<li><a id='atabs-1' href="#tabs-1"><?php echo __("Groups Management", "elefen"); ?></a></li>
			    <li><a id='atabs-2' href="#tabs-2"><?php echo __("Select post types and taxonomies", "elefen"); ?></a></li>
			    <li><a id='atabs-3' href="#tabs-3"><?php echo __("Connection management", "elefen"); ?></a></li>
			    <li><a id='atabs-4' href="#tabs-4"><?php echo __("Documentation", "elefen"); ?></a></li>
			  </ul>
			
			<div id="tabs-1">
				
				<?php
					
					global $wpdb;
					
					echo "<h1 class='wp-heading-inline'>".__("Groups Management", "elefen")."<input class='button button-primary button-large open-add-group' style='margin-left:10px;' type='button' value='".__("Add", "elefen")."' /></h1>";
					
					echo '<div id="add_group_custom" style="display:none;" class="postbox ">';
						echo '<div class="meta-box-sortables ui-sortable">';
						
							echo '<div class="inside">';
							
								echo '<table class="form-table">';
									echo '<tbody>';
						
										echo '<tr>';
											echo '<th><label for="fc_map_width">"'.__("Group name", "elefen").'"</label></th>';
											echo '<td><input id="add-group-title" type="text" name="post_title" size="30" style="width: 100%;" value="" id="title" spellcheck="true" autocomplete="off"></td>';
										echo '</tr>';
										
										echo '<tr>';
											echo '<th><label for="fc_map_width">"'.__("Group description", "elefen").'"</label></th>';
											echo '<td><textarea id="add-group-description" maxlength="255" style="width: 100%;" class="description add"></textarea></td>';
										echo '</tr>';
										
									echo '</tbody>';
								echo '</table>';
								
								echo '<div style="text-align:right;"><input style="" class="button button-primary button-large add-group" type="button" value="'.__("Save", "elefen").'" /></div>';
							
							echo '</div>';
							
						echo '</div>';
					
					echo '</div>';
					
					echo "<table id='groups-management' class='wp-list-table widefat fixed striped'>";
					
						$table_name = $wpdb->prefix."elefen_groups";
						$sql = 'select * from '.$table_name.' g order by g.group_name';
		
						$results = $wpdb->get_results($sql);
						
						$elems = array();
						
						function buildTree(array $elements, $parentId = 0) {
						    $branch = array();
						
						    foreach ($elements as $element) {
						    	
						        if ($element['parent_id'] == $parentId) {
						        	
						            $children = buildTree($elements, $element['id']);
									
						            if ($children) {
						                $element['children'] = $children;
						            }
						            $branch[] = $element;
						        }
						    }
						
						    return $branch;
						}
						
						$array = json_decode(json_encode($results), True);
						
						$tree = buildTree($array);
						
						function buildTableRecursive(array $elements, $depth) {
		
						    foreach ($elements as $element) {
						    	
								$iWhile = 1;
								$sTrait = "";
								while($iWhile<$depth){
									$sTrait = $sTrait."— ";
									$iWhile++;
								}
		
								echo "<tr data-id='".$element['id']."'>";
									echo "<td style='width:100px;'>".$element['id']."</td>";
									echo "<td class='title column-title has-row-actions column-primary page-title'>
											<strong class='row-title' data-id='".$element['id']."'><span class='s-trait'>".$sTrait."</span> <span class='group-name'>".$element['group_name']."</span></strong>
											<div class='row-actions' data-id='".$element['id']."'>
												<span class='edit'><a data-id='".$element['id']."' data-parentid='".$element['parent_id']."' class='show-edit'>".__("Edit", "elefen")."</a></span>
												<span class='trash'><a data-id='".$element['id']."' class='delete-group'>".__("Delete", "elefen")."</a></span>
											</div>
										</td>";
									echo "<td class='row-description' data-id='".$element['id']."'>".$element['description']."</td>";
								echo "</tr>";
								
						        if (array_key_exists('children', $element)){
						        	
						            buildTableRecursive($element['children'], ($depth+1));
									
						        }
								
						    }
							
						}
						 
						echo "<table id='groups-management' class='wp-list-table widefat fixed striped'>";
		
						echo "<thead>";
							echo "<tr>";
								echo "<th style='width:100px;'>id</th>";
								echo "<th>".__("Name", "elefen")."</th>";
								echo "<th>".__("Description", "elefen")."</th>";
							echo "</tr>";
						echo "</thead>";
						
						buildTableRecursive($tree, 1);
						
					echo "</table>";
				
				echo "</div>";
				
				echo "<div id='tabs-2'>";
					
				?>
					
				<h3><?php echo __("Select post types and taxonomies", "elefen"); ?></h3>
				
				<?php
				
					$table_name = $wpdb->prefix."elefen_groups_post_type";
					$sql = 'select * from '.$table_name;
				
					$results = $wpdb->get_results($sql);
					$arrPostType = array();
					
					foreach ( $results  as $result ) {
						   	
						array_push($arrPostType,$result->post_type);
							
					}
					
					$table_name = $wpdb->prefix."elefen_groups_taxonomy";
					$sql = 'select * from '.$table_name;
				
					$results = $wpdb->get_results($sql);
					$arrTaxonomies = array();
					
					foreach ( $results  as $result ) {
						   	
						array_push($arrTaxonomies,$result->taxonomy);
							
					}
					
					$post_types = get_post_types();
		
					echo "<table>";
							
						foreach ( $post_types  as $post_type ) {
						   	
							$objPostType = get_post_type_object($post_type);
							
							$isChecked = '';
							if(in_array($post_type, $arrPostType)){
								$isChecked = 'checked';
							}
							
							echo "<tr>";
								echo "<td class='td-posttype'>".$objPostType->label."</td>";
								echo "<td><input class='edit-posttype' ".$isChecked." type='checkbox' name='chk_post_type[]' value='".$objPostType->name."' /></td>";
							echo "</tr>";
							
							/*
							$taxonomies = get_object_taxonomies($post_type, 'objects');
							
							foreach($taxonomies as $taxonomy){
								
								$isChecked = '';
								if(in_array($taxonomy->name, $arrTaxonomies)){
									$isChecked = 'checked';
								}
								
								echo "<tr>";
									echo "<td class='td-taxonomy'>".$taxonomy->label."</td>";
									echo "<td><input class='edit-taxonomy' ".$isChecked." type='checkbox' name='chk_taxonomy[]' value='".$taxonomy->name."' /></td>";
								echo "</tr>";
								
							}
							*/
							
						}
					
					echo "</table>";
					
				echo "</div>";
				
				echo "<div id='tabs-3'>";
				
					echo "<h1 class='wp-heading-inline'>".__("Login page", "elefen")."</h1>";
					$url_connection_redirect = get_option( 'url_connection_redirect' );
					echo "<br />";
					echo "<p>".__("Please enter the url of your login page if you want to redirect the wp-login.php", "elefen")."</p>";
					echo "<input class='' style='width: 400px;max-width:100%;' type='text' id='urlconnection' value='".$url_connection_redirect."' />";
					echo "<input class='button button-primary button-large save-url-connection' style='margin-left:10px;' type='button' value='".__("Save", "elefen")."' />";
					
					echo "<br />";
					echo "<br />";
					echo "<br />";
					
					echo "<h1 class='wp-heading-inline'>".__("Html if already connected", "elefen")."</h1>";
					echo "<br />";
					echo "<br />";
					
					echo "<form id='frm_if_already_connected_html' action=''>";
					
						$settings = array(
						    'teeny' => true,
						    'textarea_rows' => 15,
						    'tabindex' => 1
						);
						
						wp_editor( __(get_option('if_already_connected_html')), 'if_already_connected_html', $settings);
						
						
						echo "<br />";
						echo "<div style='text-align:right;'><input class='button button-primary button-large save-already-connected-html' style='margin-left:10px;' type='submit' value='".__("Save", "elefen")."' /></div>";
						
						echo "<input type='hidden' name='tabs' value='tabs-3' />";
						echo "<input type='hidden' name='page' value='elefen_groups' />";
						
					echo "</form>";
				
				echo "</div>";
				
				
				echo "<div id='tabs-4'>";
					
					?>
						
					<h3><?php echo __("Documentation", "elefen"); ?></h3>
					
					
					<div>
						<ul class='documentation'>
							
							<li>Les administrateurs n'ont aucunes restrictions en rapport aux groupes.</li>
							
							<li>Si aucun groupe n'est coché dans un post ou une taxonomy, aucune restriction n'est appliqué.</li>
							
							<li>Le système de parents pour les posts et les taxonomies n'est pas pris en compte par le système de groupes. Exemple, si vous appliquer un accès a un groupe sur un post, vous devrez également l'appliquer également sur son enfant pour que le système le prenne en compte.</li>
							
							<li>Si un ou des groupes sont associé à un post ainsi qu'à une taxonomy associé à ce post, vous devez être associé à au moins un groupe dans le post et la catégorie.</li>
							
						</ul>
					</div>
					
					<?php
					
				echo "</div>";
				 
			?>
			
		</div>	
		
	</div>
	
	<?php $ajax_nonce = wp_create_nonce( "my-special-string" ); ?>
	
	<script>
		
		jQuery(document).ready(function(){
			
			jQuery( "#tabs" ).tabs();
			
			<?php
			
				if(isset($_GET['tabs'])){
					
				?>	
					
					jQuery("#a<?php echo $_GET['tabs']; ?>").trigger('click');
					
				<?php
					
				}
						
			?>
			
			var tabsSelected = jQuery("");
			
			function loadSelectGroups(){

				    return jQuery.ajax({
				    	type : "post",
			          	dataType : "json",
						url : "<?php echo admin_url('admin-ajax.php'); ?>",
						data : {action: "load_table_groups", security:'<?php echo $ajax_nonce; ?>'}
					});
				
			}
			
			
			jQuery("body").on("click", ".open-add-group", function(){
				
				jQuery("table#groups-management div.submit button.cancel").trigger("click");
				
				jQuery.when(loadSelectGroups()).done(function(a1){
					
					var myList = a1;

					jQuery('#add-parent-id').append('<option value="0">---</option>');

					myList.forEach(function(element) {
	
					   jQuery('#add-parent-id').append('<option value="' + element["id"] + '">( ' + element["id"] + ' ) ' + element["group_name"] + '</option>');
					  
					});
					
					jQuery("#add_group_custom").css('display', 'block');
					
				});
				
			});
			
			jQuery("table#groups-management").on("click"," span.group-name" ,function(){
				
				jQuery(this).parent().parent().find("div.row-actions span.edit a.show-edit").trigger("click");
				
			});
			
			jQuery("table#groups-management").on("click", "td div.row-actions span.edit a.show-edit", function(){
				
				var myThis = jQuery(this);
				var groupid = jQuery(this).data("id");
				var parentid = jQuery(this).data("parentid");
				
				jQuery("#add_group_custom").css('display', 'none');
				
				jQuery.when(loadSelectGroups()).done(function(a1){
					
					
					jQuery("table#groups-management div.submit button.cancel").trigger("click");
						
					var myList = a1;
					
					var title = jQuery("table#groups-management .row-title[data-id='" + groupid + "'] span.group-name").text();
					var description = jQuery("table#groups-management .row-description[data-id='" + groupid + "']").text();
					
					var html = '<tr class="inline-edit-row inline-edit-row-post quick-edit-row quick-edit-row-post inline-edit-post inline-editor">';
					
						html = html + '<td class="colspanchange" colspan="3" style="padding-bottom: 20px;">';
							html = html + '<fieldset class="inline-edit-col-left">';
						
								html = html + '<legend class="inline-edit-legend"><?php echo __("Quick edit", "elefen"); ?></legend>';
								
								html = html + '<div class="inline-edit-col">';
							
									html = html + '<label>';
										html = html + '<span class="title"><?php echo __("Title", "elefen"); ?></span>';
										html = html + '<span class="input-text-wrap"><input type="text" class="group-name" data-id="' + groupid + '" value="' + title + '"></span>';
									html = html + '</label>';
						
									html = html + '<label>';
										html = html + '<span class="title"><?php echo __("Description", "elefen"); ?></span>';
										html = html + '<span class="input-text-wrap"><textarea class="description" data-id="' + groupid + '">' + description + '</textarea></span>';
									html = html + '</label>';
									
									html = html + '<label>';
										html = html + '<span class="title"><?php echo __("Parent", "elefen"); ?></span>';
										
										html = html + '<span class="input-text-wrap">';
										
											html = html + '<select class="parent" data-id="' + groupid + '">';
											
												var selected = '';
												
												if(parentid == 0){
													selected = 'selected="selected"';
												}
											
												html = html + '<option value="0" ' + selected + '>---</option>';
												selected = '';
											
												myList.forEach(function(element) {
													
													if(parentid == element["id"]){
														selected = 'selected="selected"';
													}
											
												   html = html + '<option ' + selected + ' value="' + element["id"] + '">( ' + element["id"] + ' ) ' + element["group_name"] + '</option>';
												  
												   selected = '';
												  
												});
												
											html = html + '</select>';
										
										html = html + '</span>';
										
									html = html + '</label>';
									
								html = html + '</div>';
								
							html = html + '</fieldset>';
							
							html = html + '<div class="submit inline-edit-save">';
							
								html = html + '<button data-id="' + groupid + '" type="button" class="button cancel alignleft"><?php echo __("Cancel", "elefen"); ?></button>';
								html = html + '<button data-id="' + groupid + '" data-parentid="' + parentid + '"  type="button" class="button button-primary save alignright edit-group"><?php echo __("update", "elefen"); ?></button>';
								
							html = html + '</div>';
							
						html = html + '</td>';
					html = html + '</tr>';
					
					jQuery(myThis).parent().parent().parent().parent().after(html);
					jQuery(myThis).parent().parent().parent().parent().hide();
					
				});
				
			});
			
			jQuery("table#groups-management").on("click", ".submit.inline-edit-save button.cancel", function(){
				
				var groupId = jQuery(this).data('id');	
							
				jQuery(this).parent().parent().parent().remove();
				
				jQuery("table#groups-management tr[data-id='" + groupId + "']").show();
				
			});
			
			jQuery(".edit-posttype").change(function(){
				
				var posttype = jQuery(this).val();
				var isChecked = jQuery(this).is(":checked");

				if(isChecked == true){
					
					jQuery.ajax({
				    	type : "post",
			          	async: true,
			          	dataType : "html",
						url : "<?php echo admin_url('admin-ajax.php'); ?>",
						data : {action: "add_posttype", posttype:posttype, security:'<?php echo $ajax_nonce; ?>'},
		
						success : function(code_html, statut){
		
							alert(code_html);
								
						},
						error : function(resultat, statut, erreur){
						},
		
						complete : function(resultat, statut){
						}
		
					});
					
					
				}else{
					
					jQuery('<div></div>').appendTo('body')
					    .html('<div><h6>Êtes-vous certain de vouloir supprimer post type et toutes ses taxonomies? Vous allez par le fait même supprimé toutes les liens existant etre les posts/taxonomies et les groupes.</h6></div>')
					    .dialog({
					      modal: true,
					      title: 'Suppression du post type',
					      zIndex: 9999999,
					      autoOpen: true,
					      width: 'auto',
					      resizable: false,
					      buttons: {
					        Oui: function() {
		
								jQuery.ajax({
							    	type : "post",
						          	async: true,
						          	dataType : "html",
									url : "<?php echo admin_url('admin-ajax.php'); ?>",
									data : {action: "remove_posttype", posttype:posttype, security:'<?php echo $ajax_nonce; ?>'},
					
									success : function(code_html, statut){
					
										alert(code_html);
											
									},
									error : function(resultat, statut, erreur){
									},
					
									complete : function(resultat, statut){
									}
					
								});
		
					          jQuery(this).dialog("close");
					        },
					        Non: function() {
							  
							  jQuery( "input[type='checkbox'][value='" + posttype + "']" ).prop( "checked", true );
					          jQuery(this).dialog("close");
					        }
					      },
					      close: function(event, ui) {
					        jQuery(this).remove();
					      }
					});
				
				}
					
			});
			
			jQuery(".edit-taxonomy").change(function(){
				
				var taxonomy = jQuery(this).val();
				var isChecked = jQuery(this).is(":checked");

				if(isChecked == true){
					
					jQuery.ajax({
				    	type : "post",
			          	async: true,
			          	dataType : "html",
						url : "<?php echo admin_url('admin-ajax.php'); ?>",
						data : {action: "add_taxonomy", taxonomy:taxonomy, security:'<?php echo $ajax_nonce; ?>'},
		
						success : function(code_html, statut){
		
							alert(code_html);
								
						},
						error : function(resultat, statut, erreur){
						},
		
						complete : function(resultat, statut){
						}
		
					});
					
				}else{
					
					jQuery.ajax({
				    	type : "post",
			          	async: true,
			          	dataType : "html",
						url : "<?php echo admin_url('admin-ajax.php'); ?>",
						data : {action: "remove_taxonomy", taxonomy:taxonomy, security:'<?php echo $ajax_nonce; ?>'},
		
						success : function(code_html, statut){
		
							alert(code_html);
								
						},
						error : function(resultat, statut, erreur){
						},
		
						complete : function(resultat, statut){
						}
		
					});
					
				}
				
			});
			
			
			
			jQuery("table#groups-management").on("click", ".edit-group", function(){
				
				var id = jQuery(this).data("id");
				var oldParentid = jQuery(this).data("parentid");

				var groupName = jQuery("table#groups-management input.group-name[data-id='" + id + "']").val();
				var groupDescription = jQuery("table#groups-management tr.inline-edit-row div.inline-edit-col textarea.description[data-id='" + id + "']").val();
				var parentId = jQuery("table#groups-management input.parent[data-id='" + id + "']").val();
				
				jQuery.ajax({
			    	type : "post",
		          	async: true,
		          	dataType : "html",
					url : "<?php echo admin_url('admin-ajax.php'); ?>",
					data : {action: "edit_group", id:id, groupName:groupName, groupDescription: groupDescription,parentId: parentId, security:'<?php echo $ajax_nonce; ?>'},
	
					success : function(code_html, statut){
	
						jQuery("table#groups-management tr.inline-edit-row").remove();
						
						jQuery("table#groups-management tr[data-id='" + id + "'] td strong.row-title").text(groupName);
						jQuery("table#groups-management tr[data-id='" + id + "'] td.row-description").text(groupDescription);
				
						jQuery("table#groups-management tr[data-id='" + id + "']").show();
						
						if(oldParentid != parentId){
							location.reload();
						}
							
					},
					error : function(resultat, statut, erreur){
					},
	
					complete : function(resultat, statut){
					}
	
				});
				
			});
			
			jQuery(".delete-group").click(function(){
				
				var id = jQuery(this).attr("data-id");
				
				console.log(id);

				jQuery('<div></div>').appendTo('body')
			    .html('<div><h6>Êtes-vous certain de vouloir supprimer ce groupe? Si vous supprimer le groupe, tous les liens entre des utilisateurs et le groupe seront supprimés égalements.</h6></div>')
			    .dialog({
			      modal: true,
			      title: 'Suppression du groupe',
			      zIndex: 10000,
			      autoOpen: true,
			      width: 'auto',
			      resizable: false,
			      buttons: {
			        Oui: function() {
			        	
			        	console.log(id);

						jQuery.ajax({
					    	type : "post",
				          	async: true,
				          	dataType : "html",
							url : "<?php echo admin_url('admin-ajax.php'); ?>",
							data : {action: "delete_group", id:id, security:'<?php echo $ajax_nonce; ?>'},
			
							success : function(code_html, statut){
			
								alert(code_html);
								location.reload();
									
							},
							error : function(resultat, statut, erreur){
							},
			
							complete : function(resultat, statut){
							}
			
						});

			          jQuery(this).dialog("close");
			        },
			        Non: function() {

			          jQuery(this).dialog("close");
			        }
			      },
			      close: function(event, ui) {
			        jQuery(this).remove();
			      }
			    });

			});
			
			
			jQuery(".add-group").click(function(){
				
				var groupName = jQuery("#add-group-title").val();
				var groupDescription = jQuery("#add-group-description").val();
				var parentId = jQuery("#add-parent-id").val();

				jQuery.ajax({
			    	type : "post",
		          	async: true,
		          	dataType : "html",
					url : "<?php echo admin_url('admin-ajax.php'); ?>",
					data : {action: "add_group", groupName:groupName, groupDescription: groupDescription,parentId: parentId, security:'<?php echo $ajax_nonce; ?>'},
	
					success : function(code_html, statut){
	
						alert(code_html);
						
						if(code_html == 'AJOUT RÉUSSIE!'){
							location.reload();
						}
						
							
					},
					error : function(resultat, statut, erreur){
					},
	
					complete : function(resultat, statut){
					}
	
				});
				
			});	
			
			
			
			jQuery(".save-url-connection").on("click", function(){
				
				var urlconnection = jQuery("#urlconnection").val();
				
				jQuery.ajax({
			    	type : "post",
		          	async: true,
		          	dataType : "html",
					url : "<?php echo admin_url('admin-ajax.php'); ?>",
					data : {action: "edit_url_connection", urlconnection:urlconnection, security:'<?php echo $ajax_nonce; ?>'},
	
					success : function(code_html, statut){
	
						alert(code_html);
							
					},
					error : function(resultat, statut, erreur){
					},
	
					complete : function(resultat, statut){
					}
	
				});
				
			});
	
	
		});
		
	</script>
	
	
	<?php
}


function groups_meta_box() {
		
	global $wpdb;
	
	$table_name = $wpdb->prefix."elefen_groups_post_type";
	$sql = 'select * from '.$table_name;

	$results = $wpdb->get_results($sql);
	
	foreach ( $results  as $result ) {
				   	
		add_meta_box(
	       'groups',       // $id
	       'Groups',                  // $title
	       'show_groups_meta_box',  // $callback
	       $result->post_type,                 // $page
	       'side',                  // $context
	       'high'                     // $priority
	   );
	   
	}
	

}
add_action( 'add_meta_boxes', 'groups_meta_box' );

function show_groups_meta_box(){
	
	//POLYLANG TRANSLATION
	$post_id = get_the_ID();
	if(is_plugin_active( 'polylang-pro/polylang.php' ) == 1 || is_plugin_active( 'polylang/polylang.php' ) == 1){
		
		if(isset($_GET['from_post'])){
			
			$screen = get_current_screen();
			if ( $screen->action == 'add' ) {
			    $post_id = $_GET['from_post'];
				echo $post_id;
			}
			
		}
		
	}
	
	global $wpdb;
	
	$table_name = $wpdb->prefix."elefen_rel_groups_posts";
	$sql = 'select * from '.$table_name.' where post_id = '.$post_id;

	$results = $wpdb->get_results($sql);
	$arrChoices = array();
	
	foreach ( $results  as $result ) {
		
		array_push($arrChoices,$result->group_id);
		
	}

	$table_name = $wpdb->prefix."elefen_groups";
	$sql = 'select * from '.$table_name.' order by group_name';

	$results = $wpdb->get_results($sql);

	$elems = array();
	
	function buildTree(array $elements, $parentId = 0) {
	    $branch = array();
	
	    foreach ($elements as $element) {
	    	
	        if ($element['parent_id'] == $parentId) {
	        	
	            $children = buildTree($elements, $element['id']);
				
	            if ($children) {
	                $element['children'] = $children;
	            }
	            $branch[] = $element;
	        }
	    }
	
	    return $branch;
	}
	
	$array = json_decode(json_encode($results), True);
	
	$tree = buildTree($array);

				
	function buildTableRecursive(array $elements, $depth, $arrChoices) {

	    foreach ($elements as $element) {
	    	
			$iWhile = 1;
			$sTrait = "";
			$margin = "0";
			while($iWhile<$depth){
				$sTrait = $sTrait."— ";
				$iWhile++;
			}
			$margin = 18 * ($depth - 1);
			
			$isChecked = '';
			if(in_array($element['id'], $arrChoices)){
				$isChecked = 'checked';
			}
			
				echo '<li style="margin-left:'.$margin.'px;line-height: 22px;word-wrap: break-word;padding:0px;margin-bottom:0px;">';
					echo '<input '.$isChecked.' type="checkbox" id="chk_'.$element['id'].'" name="posttype_group[]" value="'.$element['id'].'">';
					echo '<label for="chk_'.$element['id'].'">'.$element['group_name'].'</label>';
				echo '</li>';
			
	        if (array_key_exists('children', $element)){
	        	
	            buildTableRecursive($element['children'], ($depth+1), $arrChoices);
				
	        }
			
	    }
		
	}

	echo "<ul>";		
		
		buildTableRecursive($tree, 1, $arrChoices);
		
	echo "</ul>";
	
	echo "<input type='hidden' name='hid_group_change' value='-1' />";
			
}

function save_groups_metabox_callback( $post_id ) {
 
    if (array_key_exists('hid_group_change', $_POST)){
    	
		global $wpdb;
	
		$table_name = $wpdb->prefix."elefen_rel_groups_posts";
		
		//POLYLANG TRANSLATION
		$arrPostIds = array();
		array_push($arrPostIds, $post_id);
		if(is_plugin_active( 'polylang-pro/polylang.php' ) == 1 || is_plugin_active( 'polylang/polylang.php' ) == 1){
			
			global $polylang;
		    $translationIds = $polylang->model->get_translations('post', $post_id);
		    $currentLang = pll_get_post($post_id, pll_current_language());
			
		    foreach ($translationIds as $key=>$translationID){
		        if($translationID != $currentLang){
		        	
		            $availableLang = $polylang->model->get_languages_list();
					
		            foreach( $availableLang as $lang){
		            	
		                if($key == $lang->slug){
		                	
							array_push($arrPostIds, $translationID);
							
		                }
						
		            }
		        }
		    }

		}
		
		
		foreach($arrPostIds as $post_id){
			
			if(isset($_POST['posttype_group'])){
			
				$sql = 'select * from '.$table_name.' where post_id = '.$post_id;
		
				$results = $wpdb->get_results($sql);
				$arrDbChoices = array();
				
				foreach ( $results  as $result ) {
					
					array_push($arrDbChoices,$result->group_id);
					
				}
			
				//AJOUT DANS TABLE SI PAS LA
				foreach($_POST['posttype_group'] as $choice){
						
					if(in_array($choice, $arrDbChoices)){
						
					}else{
		
						$wpdb->insert($wpdb->prefix."elefen_rel_groups_posts", array('post_id'=>$post_id, 'group_id'=>$choice));
						
					}
					
				}
				
				//DELETE DANS TABLE SI PAS LA
				foreach($arrDbChoices as $choice){
					
					if(in_array($choice, $_POST['posttype_group'])){
						
					}else{
						
						$wpdb->delete($wpdb->prefix."elefen_rel_groups_posts", array('post_id'=>$post_id, 'group_id'=>$choice));
						
					}
					
				}
			
			}
			
		}

		
    }
     
}
add_action( 'save_post', 'save_groups_metabox_callback' );


add_action( 'show_user_profile', 'groups_user_fields' );
add_action( 'edit_user_profile', 'groups_user_fields' );
function groups_user_fields( $user ){
	
	?>
	
    <h3>Groups</h3>
    
    <?php

    global $wpdb;
	
	$table_name = $wpdb->prefix."elefen_rel_groups_users";
	$sql = 'select * from '.$table_name.' where user_id = '.$user->ID;

	$results = $wpdb->get_results($sql);
	$arrChoices = array();
	
	foreach ( $results  as $result ) {
		
		array_push($arrChoices,$result->group_id);
		
	}
	
    $table_name = $wpdb->prefix."elefen_groups";
	$sql = 'select * from '.$table_name.' order by group_name';

	$results = $wpdb->get_results($sql);
	
	echo "<div style='max-height:300px;overflow-y: scroll;'>";
	
		$elems = array();
		
		function buildTree(array $elements, $parentId = 0) {
		    $branch = array();
		
		    foreach ($elements as $element) {
		    	
		        if ($element['parent_id'] == $parentId) {
		        	
		            $children = buildTree($elements, $element['id']);
					
		            if ($children) {
		                $element['children'] = $children;
		            }
		            $branch[] = $element;
		        }
		    }
		
		    return $branch;
		}
		
		$array = json_decode(json_encode($results), True);
		
		$tree = buildTree($array);
	
					
		function buildTableRecursive(array $elements, $depth, $arrChoices) {
	
		    foreach ($elements as $element) {
		    	
				$iWhile = 1;
				$margin = "0";
				while($iWhile<$depth){
					$iWhile++;
				}
				$margin = 18 * ($depth - 1);
				
				$isChecked = '';
				if(in_array($element['id'], $arrChoices)){
					$isChecked = 'checked';
				}
				
					echo '<li style="margin-left:'.$margin.'px;line-height: 22px;word-wrap: break-word;padding:0px;margin-bottom:0px;">';
						echo '<input id="chk_'.$element['id'].'" '.$isChecked.' type="checkbox" name="user_group[]" value="'.$element['id'].'">';
						echo '<label for="chk_'.$element['id'].'">'.$element['group_name'].'</label>';
					echo '</li>';
				
		        if (array_key_exists('children', $element)){
		        	
		            buildTableRecursive($element['children'], ($depth+1), $arrChoices);
					
		        }
			
		    }
			
		}
	
		echo "<ul>";		
			
			buildTableRecursive($tree, 1, $arrChoices);
			
		echo "</ul>";


	echo "</div>";
	
	echo "<input type='hidden' name='hid_group_change' value='-1' />";
    
    
}

add_action( 'personal_options_update', 'save_groups_user_fields' );
add_action( 'edit_user_profile_update', 'save_groups_user_fields' );
function save_groups_user_fields( $user_id ) 
{
    if ( !current_user_can( 'edit_user', $user_id ) ) { return false; }else{
    		
        if(isset($_POST['hid_group_change']) && $_POST['hid_group_change'] != ""){
				
			global $wpdb;
		
			$table_name = $wpdb->prefix."elefen_rel_groups_users";
			$sql = 'select * from '.$table_name.' where user_id = '.$user_id;
		
			$results = $wpdb->get_results($sql);
			$arrDbChoices = array();
			
			foreach ( $results  as $result ) {
				
				array_push($arrDbChoices,$result->group_id);
				
			}
			
			//AJOUT DANS TABLE SI PAS LA
			foreach($_POST['user_group'] as $choice){
					
				if(in_array($choice, $arrDbChoices)){
					
				}else{
	
					$wpdb->insert($wpdb->prefix."elefen_rel_groups_users", array('user_id'=>$user_id, 'group_id'=>$choice));
					
				}
				
			}
			
			//DELETE DANS TABLE SI PAS LA
			foreach($arrDbChoices as $choice){
				
				if(in_array($choice, $_POST['user_group'])){
					
				}else{
					
					$wpdb->delete($wpdb->prefix."elefen_rel_groups_users", array('user_id'=>$user_id, 'group_id'=>$choice));
					
				}
				
			}
			
        }

    }
    
}

add_action('wp_ajax_edit_group', 'edit_group');
add_action('wp_ajax_nopriv_edit_group', 'edit_group');
function edit_group(){
	
	if(current_user_can('administrator') ){
		
		check_ajax_referer( 'my-special-string', 'security' );
	
		global $wpdb;
	
		$idGroup = $_POST['id'];
		$groupName = $_POST['groupName'];
		$description = $_POST['groupDescription'];
		$parentId = $_POST['parentId'];
		
		$parentId = "0";
		
		
		$wpdb->update($wpdb->prefix."elefen_groups", array('group_name'=>$groupName, 'description'=>$description, 'parent_id'=>$parentId), array('id'=>$idGroup));
		
		echo __("successfully saved", "elefen");
	
	}else{
		echo __("Unauthorized", "elefen");
	}
	
	wp_die();
	
}

add_action('wp_ajax_delete_group', 'delete_group');
add_action('wp_ajax_nopriv_delete_group', 'delete_group');
function delete_group(){
			
	if(current_user_can('administrator') ){
	
		check_ajax_referer( 'my-special-string', 'security' );
	
		global $wpdb;
	
		$idGroup = $_POST['id'];
		
		$wpdb->delete($wpdb->prefix."elefen_groups", array('id'=>$idGroup));
		
		$wpdb->delete($wpdb->prefix."elefen_rel_groups_posts", array('group_id'=>$idGroup));
		
		$wpdb->delete($wpdb->prefix."elefen_rel_groups_terms", array('group_id'=>$idGroup));
		
		$wpdb->delete($wpdb->prefix."elefen_rel_groups_users", array('group_id'=>$idGroup));
		
		echo "Successfully saved".$idGroup;
		
		echo __("Successfully saved", "elefen");
		
	}else{
		echo __("Unauthorized", "elefen");
	}
	
	wp_die();
	
}

add_action('wp_ajax_edit_url_connection', 'edit_url_connection');
add_action('wp_ajax_nopriv_edit_url_connection', 'edit_url_connection');
function edit_url_connection(){
			
	if(current_user_can('administrator') ){	
		
		check_ajax_referer( 'my-special-string', 'security' );
	
		$urlconnection = $_POST['urlconnection'];
		
		update_option( "url_connection_redirect", $urlconnection );
		
		echo __("Successfully saved", "elefen");
	
	}else{
		echo __("Unauthorized", "elefen");
	}
		
	wp_die();
	
}


add_action('wp_ajax_add_group', 'add_group');
add_action('wp_ajax_nopriv_add_group', 'add_group');
function add_group(){
	
	if(current_user_can('administrator') ){	
	
		check_ajax_referer( 'my-special-string', 'security' );
	
		global $wpdb;
	
		$groupName = $_POST['groupName'];
		$description = $_POST['groupDescription'];
		$parentId = $_POST['parentId'];
		
		if($groupName == ""){
	
			echo __("Invalid group name", "elefen");
			
		}else{
			
			$wpdb->insert($wpdb->prefix."elefen_groups", array('group_name'=>$groupName, 'description'=>$description, 'parent_id'=>$parentId));
		
			echo __("Successfully added", "elefen");
			
		}

	}else{
		echo __("Unauthorized", "elefen");
	}
	
	wp_die();
	
}

add_action('wp_ajax_add_posttype', 'add_posttype');
add_action('wp_ajax_nopriv_add_posttype', 'add_posttype');
function add_posttype(){
		
	if(current_user_can('administrator') ){	
		
		check_ajax_referer( 'my-special-string', 'security' );
	
		global $wpdb;
	
		$posttype = $_POST['posttype'];
		
		$wpdb->insert($wpdb->prefix."elefen_groups_post_type", array('post_type'=>$posttype));
		
		echo __("Successfully added", "elefen");
	
	}else{
		echo __("Unauthorized", "elefen");
	}
	
	wp_die();
	
}

add_action('wp_ajax_remove_posttype', 'remove_posttype');
add_action('wp_ajax_nopriv_remove_posttype', 'remove_posttype');
function remove_posttype(){
	
	if(current_user_can('administrator') ){	
	
		check_ajax_referer( 'my-special-string', 'security' );
	
		global $wpdb;
	
		$posttype = $_POST['posttype'];
	
		$taxonomies = get_object_taxonomies($posttype, 'objects');
		$arrTermsid = array();				
		foreach($taxonomies as $taxonomy){
			
			$terms = get_terms([
			    'taxonomy' => $taxonomy->slug,
			    'hide_empty' => false,
			]);
			
			foreach($terms as $term){
				
				if(in_array($term->term_id, $arrTermsid)){
					
				}else{
					array_push($arrTermsid, $term->term_id);
				}
				
			}
			
		}
	
		$termids = implode( ',', array_map( 'absint', $arrTermsid ) );		
		$wpdb->query( "DELETE FROM ".$wpdb->prefix."elefen_rel_groups_terms WHERE term_id IN($termids)" );
		
		$sql = "select * from ".$wpdb->prefix."posts where post_type = '".$posttype."'";
	
		$results = $wpdb->get_results($sql);
		$arrPostIds = array();
		
		foreach( $results  as $result ){
			
			array_push($arrPostIds,$result->ID);
				
		}
	
		$postids = implode( ',', array_map( 'absint', $arrPostIds ) );	
		$wpdb->query( "DELETE FROM ".$wpdb->prefix."elefen_rel_groups_posts WHERE post_id IN($postids)" );	
	
		$wpdb->delete($wpdb->prefix."elefen_groups_post_type", array('post_type'=>$posttype));
	
		echo __("Successfully deleted", "elefen");
	
	}else{
		echo __("Unauthorized", "elefen");
	}
	
	wp_die();
	
}

add_action('wp_ajax_add_taxonomy', 'add_taxonomy');
add_action('wp_ajax_nopriv_add_taxonomy', 'add_taxonomy');
function add_taxonomy(){
	
	if(current_user_can('administrator') ){	
	
		check_ajax_referer( 'my-special-string', 'security' );
	
		global $wpdb;
	
		$taxonomy = $_POST['taxonomy'];
		
		$wpdb->insert($wpdb->prefix."elefen_groups_taxonomy", array('taxonomy'=>$taxonomy));
		
		echo __("Successfully added", "elefen");
	
	}else{
		echo __("Unauthorized", "elefen");
	}
	
	wp_die();
	
}

add_action('wp_ajax_remove_taxonomy', 'remove_taxonomy');
add_action('wp_ajax_nopriv_remove_taxonomy', 'remove_taxonomy');
function remove_taxonomy(){
			
	if(current_user_can('administrator') ){			
			
		check_ajax_referer( 'my-special-string', 'security' );
	
		global $wpdb;
	
		$taxonomy = $_POST['taxonomy'];
		
		$wpdb->delete($wpdb->prefix."elefen_groups_taxonomy", array('taxonomy'=>$taxonomy));
		
		echo __("Successfully deleted", "elefen");
	
	}else{
		echo __("Unauthorized", "elefen");
	}
	
	wp_die();
	
}

add_action('wp_ajax_load_table_groups', 'load_table_groups');
add_action('wp_ajax_nopriv_load_table_groups', 'load_table_groups');
function load_table_groups(){
	
	if(current_user_can('administrator') ){	
	
		global $wpdb;
				
		check_ajax_referer( 'my-special-string', 'security' );
	
		$table_name = $wpdb->prefix."elefen_groups";
		$sql = 'select * from '.$table_name.' order by group_name';
		
		$results = $wpdb->get_results($sql);
		$arrGroups = array();
		
		foreach( $results  as $result ){
			
			$arrTempo = array('id'=>$result->id,'group_name'=>$result->group_name,'parent_id'=>$result->parent_id);
	
			array_push($arrGroups, $arrTempo);
				
		}
		
		echo json_encode($arrGroups);
	
	}else{
		echo __("Unauthorized", "elefen");
	}
	
	
	wp_die();
	
}

//BIG QUERY
function join_group_query($join) {
    global $wpdb;
	
	if(!is_admin()){
		
		if(!current_user_can('administrator')){
			
			$userId = -1;				
						
			if( is_user_logged_in() ){
			    $userId = get_current_user_id();
			} 
	
			//POST
			$join .= " LEFT JOIN 
				(SELECT 
					gp.post_id
				FROM 
				    ".$wpdb->prefix."elefen_rel_groups_posts gp LEFT JOIN
				    (select group_id from ".$wpdb->prefix."elefen_rel_groups_users where user_id = ".$userId.") gu ON gp.group_id = gu.group_id
				GROUP BY
					gp.post_id
				HAVING 
					sum(IFNULL(gu.group_id, 0)) = 0) ga ON ga.post_id = $wpdb->posts.id ";
	
			//TAXONOMY	
			$join .= " LEFT JOIN 
				(SELECT 
					g1.object_id as post_id
				FROM 
					(select * from ".$wpdb->prefix."term_relationships tr left join 
					".$wpdb->prefix."elefen_rel_groups_terms gt ON tr.term_taxonomy_id = gt.term_id where ifnull(gt.group_id,0) > 0) g1 LEFT JOIN 
					(select group_id from ".$wpdb->prefix."elefen_rel_groups_users where user_id = ".$userId.") gu ON g1.group_id = gu.group_id
				GROUP BY 
					g1.object_id
				HAVING sum(ifnull(gu.group_id,0)) = 0) gb ON gb.post_id = $wpdb->posts.id ";		

		}

	}
	
    return $join;
}
add_filter('posts_join', 'join_group_query');

function where_group_query( $where, $query ) {

	if(!is_admin()){
				
		if(!current_user_can('administrator')){
		
			$where .= " AND (ifnull(ga.post_id,0) = 0 AND ifnull(gb.post_id,0) = 0)";
				
		}		
				
	}
	
    return $where;
}
add_filter( 'posts_where' , 'where_group_query', 10, 2 );

function groups_edit_get_terms_args( $args, $taxonomies ){
	
    if ( is_admin() )
        return $args;
        
    global $wpdb;
	
	$userId = -1;
	
	if ( is_user_logged_in() ) {
	    $userId = get_current_user_id();
	} 
        
	$sql = "
		SELECT 
			gt.term_id
		FROM 
			".$wpdb->prefix."elefen_rel_groups_terms gt left join
		    (select * from ".$wpdb->prefix."elefen_rel_groups_users where user_id = ".$userId.") gu ON gt.group_id = gu.group_id
		GROUP BY
			gt.term_id
		HAVING sum(ifnull(gu.group_id,0)) = 0";

	$results = $wpdb->get_results($sql);
	
	$arrExcludeTermIds = array();
	foreach ( $results as $result ) {
						
		array_push($arrExcludeTermIds, $result->term_id);			
		
	}
	
    $args['exclude'] = $arrExcludeTermIds; // Array of cat ids you want to exclude
    
    return $args;
}
add_filter( 'get_terms_args', 'groups_edit_get_terms_args', 10, 2 );


function groups_modify_taxonomy_table($columns){

    $columns['groups'] = __('Groups', 'elefen');
        
    return $columns;
	
}

function groups_modify_taxonomy_table_row($content, $column_name, $term_id){
	
	switch ($column_name){
        case 'groups' :
			
			global $wpdb;
			
			$sql = "
					select 
						DISTINCT g.group_name 
					from 
						".$wpdb->prefix."elefen_rel_groups_terms gt inner join
						".$wpdb->prefix."elefen_groups g ON gt.group_id = g.id
					where 
						gt.term_id = ".$term_id;

			$results = $wpdb->get_results($sql);
			
			$arrGroups = array();
			foreach ( $results  as $result ) {
					
			   array_push($arrGroups, $result->group_name);
			   
			}
			
			$sGroups = implode(", ", $arrGroups);
            $content = $sGroups;
			
			break;
    }
	
    return $content;
	
}

function groups_modify_posttype_table($columns){

	$columns["groups"] = __("Groups", "elefen");
        
    return $columns;
	
}

function groups_modify_posttype_table_row($column_name, $post_id){
	
	$content = "";
	switch ($column_name){
		
        case 'groups' :
			
			global $wpdb;
			
			$sql = "
					select 
						DISTINCT g.group_name 
					from 
						".$wpdb->prefix."elefen_rel_groups_posts gp inner join
						".$wpdb->prefix."elefen_groups g ON gp.group_id = g.id
					where 
						gp.post_id = ".$post_id;

			$results = $wpdb->get_results($sql);
			
			$arrGroups = array();
			foreach ( $results  as $result ) {
					
			   array_push($arrGroups, $result->group_name);
			   
			}
			
			$sGroups = implode(", ", $arrGroups);
            echo $sGroups;
			
			break;
    }
	
    return $content;
	
}


function groups_modify_user_table( $column ) {
	
    $column['groups'] = 'Groups';
    return $column;
	
}
add_filter( 'manage_users_columns', 'groups_modify_user_table' );

function groups_modify_user_table_row( $val, $column_name, $user_id ) {
	
    switch ($column_name) {
        case 'groups' :
			
			global $wpdb;
			
			$table_name = $wpdb->prefix."elefen_rel_groups_users";
			$sql = "
					select 
						DISTINCT g.group_name 
					from 
						".$wpdb->prefix."elefen_rel_groups_users ug inner join
						".$wpdb->prefix."elefen_groups g ON ug.group_id = g.id
					where 
						ug.user_id = ".$user_id;

			$results = $wpdb->get_results($sql);
			
			$arrGroups = array();
			foreach ( $results  as $result ) {
					
			   array_push($arrGroups, $result->group_name);
			   
			}
			
			$sGroups = implode(", ", $arrGroups);
            return $sGroups;
        default:
    }
    return $val;
	
}
add_filter( 'manage_users_custom_column', 'groups_modify_user_table_row', 10, 3 );


add_shortcode('groups-login-form', 'groups_login_form');
function groups_login_form($atts = []) {
	
	
	if ( is_user_logged_in() ) {
		return get_option('if_already_connected_html');
	}else{
		
			$msgError = "";

			if(isset($_GET['errors'])){
				
				if($_GET['errors'] == 'invalidcombo'){
					
					$msgError = __("Username or email not found", 'elefen');
					
				}
				
				if($_GET['errors'] == 'empty_username'){
					
					$msgError = __("Empty username or password", 'elefen');
					
				}
				
			}
			
			if(isset($_GET['login'])){
				
				if($_GET['login'] == 'failed'){
					
					$msgError = __("Username or email not found", 'elefen');
					
				}
				
				if($_GET['login'] == 'empty'){
					
					$msgError = __("Empty username or password", 'elefen');
					
				}
				
			}
			
			if(isset($_GET['action'])){
				
				if($_GET['action'] == 'passwordreset'){
					
					$msgError = __("Your password has been reset", 'elefen');
					
				}
			
			}

			if(isset($_GET['checkemail'])){
				
				if($_GET['checkemail'] == 'confirm'){
					
					$msgError = __("Check your email inbox to get your reset password link", 'elefen');
					
				}
			
			}

			echo "<div class='login-error-msg' style='color:red;'>".$msgError."</div>";
		
			$url_connection_redirect = get_option( 'url_connection_redirect' );
			if($url_connection_redirect == ""){
				$url_connection_redirect = home_url()."/login/";
			}
		
			echo '<script src="'.home_url().'/wp-includes/js/underscore.min.js"></script>';
			wp_enqueue_script( 'password-strength-meter' );
			
			$a = shortcode_atts( array(
		        'form_id' => 'loginform',
		        'label_username'=>'Username',
		 		'label_password'=>'Password',
		 		'label_remember'=>'Remember me?',
		 		'label_log_in'=>'Log in',
				'id_username'=>'user_login',
				'id_password'=>'user_pass',
				'id_remember'=>'rememberme',
				'id_submit'=>'wp-submit',
		 		'remember'=>true,
				'value_username'=>'',
				'value_remember'=>false,
				
				'login_form'=>true,
				'login_form_show_title'=>true,
				'lost_password_form'=>true,
				'lost_password_show_title'=>true
		    ), $atts );
			
			
		
		if(isset($_GET['action']) && $_GET['action'] == 'rp'){

				$attributes = array();
				
				if ( is_user_logged_in() ) {
			        echo __( 'You are already signed in.', 'personalize-login' );
			    } else {
			        if ( isset( $_REQUEST['login'] ) && isset( $_REQUEST['key'] ) ) {
			            $attributes['login'] = $_REQUEST['login'];
			            $attributes['key'] = $_REQUEST['key'];
			 
			            // Error messages
			            $errors = array();
			            if ( isset( $_REQUEST['error'] ) ) {
			                $error_codes = explode( ',', $_REQUEST['error'] );
			 
			                foreach ( $error_codes as $code ) {
			                    $errors []= $this->get_error_message( $code );
			                }
			            }
			            $attributes['errors'] = $errors;
						
						?>
			 
				         <div id="password-reset-form" class="widecolumn">
						    <?php if ( $a['lost_password_show_title'] ) : ?>
						        <h3><?php echo __( 'Pick a New Password', 'elefen' ); ?></h3>
						    <?php endif; ?>
						 
						    <form name="resetpassform" id="resetpassform" action="<?php echo site_url( 'wp-login.php?action=resetpass' ); ?>" method="post" autocomplete="off">
						        <input type="hidden" id="user_login" name="rp_login" value="<?php echo esc_attr( $attributes['login'] ); ?>" autocomplete="off" />
						        <input type="hidden" name="rp_key" value="<?php echo esc_attr( $attributes['key'] ); ?>" />
						         
						        <?php if ( count( $attributes['errors'] ) > 0 ) : ?>
						            <?php foreach ( $attributes['errors'] as $error ) : ?>
						                <p>
						                    <?php echo $error; ?>
						                </p>
						            <?php endforeach; ?>
						        <?php endif; ?>
						 
						        <p>
						            <label for="pass1"><?php echo __( 'New password', 'elefen' ) ?></label>
						            <input type="password" name="pass1" id="pass1" class="input" size="20" value="" autocomplete="off" />
						        </p>
						        <p>
						            <label for="pass2"><?php echo __( 'Repeat new password', 'elefen' ) ?></label>
						            <input type="password" name="pass2" id="pass2" class="input" size="20" value="" autocomplete="off" />
						        </p>
						         
						        <p class="description"><?php echo wp_get_password_hint(); ?></p>
						         
						        <p class="resetpass-submit">
						            <input type="submit" name="submit" id="resetpass-button"
						                   class="button" value="<?php echo __( 'Reset password', 'elefen' ); ?>" />
						        </p>
						    </form>
						</div>
						
						<?php
						
			        } else {
			            echo __( 'Invalid password reset link.', 'elefen' );
			        }
			    }
	
		}elseif(isset($_GET['action']) && $_GET['action'] == 'rpform'){
			
			if($a['lost_password_form'] == true):
				
				?>
			
				 <div id="password-lost-form" class="widecolumn">
				    <?php if ( $a['lost_password_show_title'] ) : ?>
				        <h3><?php __( 'Forgot password?', 'elefen' ); ?></h3>
				    <?php endif; ?>
				 
				    <p>
				        <?php
				            echo __("Enter your e-mail address and we will send you a link that you can use to choose a new password.", 'elefen');
				        ?>
				    </p>
				    
				    <form id="lostpasswordform" action="<?php echo wp_lostpassword_url(); ?>" method="post">
				        <p class="form-row">
				            <label for="user_login"><?php echo __( 'Email', 'elefen' ); ?></label>
				            <input type="text" name="user_login" id="user_login" />
				        </p>
				 
				        <p class="lostpassword-submit">
				            <input type="submit" name="submit" class="lostpassword-button" value="<?php echo __( 'Reset password', 'elefen' ); ?>"/>
				        </p>
				    </form>
				</div>
				
				<?php
			endif;

		}else{
			
			if($a['login_form'] == true):
			
				if ( $a['lost_password_show_title'] ) :
				        echo '<h3>'.__( 'Log in', 'elefen' ).'</h3>';
				endif; 
	
				?>
				
					<form name="<?php echo $a['form_id']; ?>" id="<?php echo $a['form_id']; ?>" action="<?php echo site_url(); ?>/wp-login.php" method="post">
					
						<p class="login-username">
							<input type="text" placeholder="<?php echo $a['label_username']; ?>" name="log" id="<?php echo $a['id_username']; ?>" class="input" value="" size="20">
						</p>
						<p class="login-password">
							<input type="password" placeholder="<?php echo $a['label_password']; ?>" name="pwd" id="<?php echo $a['id_password']; ?>" class="input" value="" size="20">
						</p>
						
						<?php if($a['id_password']): ?>
							<p class="login-remember"><label><input name="<?php echo $a['id_remember']; ?>" type="checkbox" id="<?php echo $a['id_remember']; ?>" value="forever"><?php echo $a['label_remember']; ?></label></p>
						<?php endif; ?>
							
						<p class="login-submit">
							<input type="submit" name="<?php echo $a['id_submit']; ?>" id="<?php echo $a['id_submit']; ?>" class="button button-primary" value="<?php echo $a['label_log_in']; ?>">
							<input type="hidden" name="redirect_to" value="<?php echo $url_connection_redirect; ?>">
						</p>
						
					</form>
					
					<a href="<?php echo $url_connection_redirect; ?>?action=rpform" ><?php echo __("forgot password?", "elefen"); ?></a>
				
				<?php
				
			endif;
			
			
		}
		
	
	}
		
}



add_action( 'login_form_rp', 'do_password_reset' );
add_action( 'login_form_resetpass', 'do_password_reset' );
function do_password_reset() {
	
	$url_connection_redirect = get_option( 'url_connection_redirect' );
	if($url_connection_redirect == ""){
		$url_connection_redirect = home_url()."/wp-login.php";
	}
	
	
    if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
        $rp_key = $_REQUEST['rp_key'];
        $rp_login = $_REQUEST['rp_login'];
 
        $user = check_password_reset_key( $rp_key, $rp_login );
 
        if ( ! $user || is_wp_error( $user ) ) {
            if ( $user && $user->get_error_code() === 'expired_key' ) {
                wp_redirect( $url_connection_redirect.'?resetpassword=expiredkey' );
            } else {
                wp_redirect( $url_connection_redirect.'?resetpassword=invalidkey' );
            }
            exit;
        }
 
        if( isset( $_POST['pass1'] ) ) {
            if ( $_POST['pass1'] != $_POST['pass2'] ) {
                // Passwords don't match
                $redirect_url = $url_connection_redirect;
 
                $redirect_url = add_query_arg( 'key', $rp_key, $redirect_url );
                $redirect_url = add_query_arg( 'login', $rp_login, $redirect_url );
                $redirect_url = add_query_arg( 'error', 'password_reset_mismatch', $redirect_url );
 
                wp_redirect( $redirect_url );
                exit;
            }
 
            if ( empty( $_POST['pass1'] ) ) {
                // Password is empty
                $redirect_url = $url_connection_redirect;
 
                $redirect_url = add_query_arg( 'key', $rp_key, $redirect_url );
                $redirect_url = add_query_arg( 'login', $rp_login, $redirect_url );
                $redirect_url = add_query_arg( 'error', 'password_reset_empty', $redirect_url );
 
                wp_redirect( $redirect_url );
                exit;
            }
 
            // Parameter checks OK, reset password
            reset_password( $user, $_POST['pass1'] );
            wp_redirect( $url_connection_redirect.'?password=changed' );
        } else {
            echo "Invalid request.";
        }
 
        exit;
    }
}





/* Main redirection of the default login page */
function redirect_login_page() {
	
	$url_connection_redirect = get_option( 'url_connection_redirect' );
	if($url_connection_redirect == ""){
		$url_connection_redirect = home_url()."/wp-login.php";
	}
	
	$login_page  = $url_connection_redirect;
	$page_viewed = basename($_SERVER['REQUEST_URI']);

	if($page_viewed == "wp-login.php" && $_SERVER['REQUEST_METHOD'] == 'GET') {
		
		wp_redirect($login_page);
		exit;
	}
}
add_action('init','redirect_login_page');

/* Where to go if a login failed */
function custom_login_failed() {
	
	$url_connection_redirect = get_option( 'url_connection_redirect' );
	if($url_connection_redirect == ""){
		$url_connection_redirect = home_url()."/wp-login.php";
	}
	
	$login_page = $url_connection_redirect;
	wp_redirect($login_page . '?login=failed');
	exit;
}
add_action('wp_login_failed', 'custom_login_failed');

/* Where to go if any of the fields were empty */
function verify_user_pass($user, $username, $password) {
	
	$url_connection_redirect = get_option( 'url_connection_redirect' );
	if($url_connection_redirect == ""){
		$url_connection_redirect = home_url()."/wp-login.php";
	}
	
	if(isset($_GET['redirect_to'])){
		
		wp_redirect($url_connection_redirect);
		exit;
		
	}
	
	if($username == "" || $password == "") {
		wp_redirect($url_connection_redirect."?login=empty");
		exit;
	}
}
add_filter('authenticate', 'verify_user_pass', 1, 3);

/* What to do on logout */
function logout_redirect() {
	
	$url_connection_redirect = get_option( 'url_connection_redirect' );
	if($url_connection_redirect == ""){
		$url_connection_redirect = home_url()."/wp-login.php";
	}
	
	$login_page  = $url_connection_redirect;
	wp_redirect($login_page . "?login=false");
	exit;
}
add_action('wp_logout','logout_redirect');

/* What to do on logout */
function password_reset_redirect() {
	
	$url_connection_redirect = get_option( 'url_connection_redirect' );
	if($url_connection_redirect == ""){
		$url_connection_redirect = home_url()."/wp-login.php";
	}
	
	$login_page = $url_connection_redirect;
	wp_redirect($login_page . "?action=passwordreset");
	exit;
}
add_action('after_password_reset','password_reset_redirect');

function do_password_lost() {
	
	$url_connection_redirect = get_option( 'url_connection_redirect' );
	if($url_connection_redirect == ""){
		$url_connection_redirect = home_url()."/wp-login.php";
	}
	
    if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
        $errors = retrieve_password();
        if ( is_wp_error( $errors ) ) {
            // Errors found
            $redirect_url = $url_connection_redirect;
            $redirect_url = add_query_arg( 'errors', join( ',', $errors->get_error_codes() ), $redirect_url );
        } else {
            // Email sent
            $redirect_url = $url_connection_redirect;
            $redirect_url = add_query_arg( 'checkemail', 'confirm', $redirect_url );
        }
 
        wp_redirect( $redirect_url );
        exit;
    }
}
add_action( 'login_form_lostpassword', 'do_password_lost' );

function replace_retrieve_password_message( $message, $key, $user_login, $user_data ) {
	
	$url_connection_redirect = get_option( 'url_connection_redirect' );
	if($url_connection_redirect == ""){
		$url_connection_redirect = home_url()."/wp-login.php";
	}
	
    // Create new message
    $msg  = __( 'Hello!', 'personalize-login' ) . "\r\n\r\n";
    $msg .= sprintf( __( 'You have asked us to reset your password for your account using the username %s.', 'elefen' ), $user_login ) . "\r\n\r\n";
    $msg .= __( "If this was an error or you did not request a password reset, simply ignore this email and nothing will happen.", 'elefen' ) . "\r\n\r\n";
    $msg .= __( 'To reset your password, visit the following address : ', 'elefen' ) . "\r\n\r\n";
    $msg .= $url_connection_redirect."?action=rp&key=$key&login=".rawurlencode( $user_login )."\r\n\r\n";
    $msg .= __( 'Merci!', 'elefen' ) . "\r\n";
 
    return $msg;
}
add_filter( 'retrieve_password_message', 'replace_retrieve_password_message', 10, 4 );


