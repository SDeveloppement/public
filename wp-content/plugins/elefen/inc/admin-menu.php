<?php
function elefen_admin_menu_register_settings() {
   register_setting( 'elefen_admin_menu', 'elefen_admin_menu' );          
}
add_action( 'admin_init', 'elefen_admin_menu_register_settings' );

function elefen_admin_menu_register_options_page() {
  add_submenu_page( 'elefen', 'Manage Admin Menu', 'Manage Admin Menu', 'manage_options', 'elefen_admin_menu', 'elefen_admin_menu_options_page' );
}
add_action('admin_menu', 'elefen_admin_menu_register_options_page');

/**
 * 
 * Admin option page
 * 
 */
function elefen_admin_menu_options_page() {
	?>
	<div class="wrap">
	<h1>Manage Admin Menu</h1>	
	<form method="post" action="options.php">

	    <?php 
			settings_fields( 'elefen_admin_menu' );
			do_settings_sections( 'elefen_admin_menu' );
			$elefen_admin_menu = get_option('elefen_admin_menu');

			echo custom_css_js();
		?>
	    
		<div class="tab">
			<a class="tablinks" related-id="classe" onclick="openCity(event, 'classe')">Classes & IDs</a>
			<a class="tablinks" related-id="checkbox" onclick="openCity(event, 'checkbox')">Menu Admin</a>
		</div>

		<div id="classe" class="textarea-menu tabcontent">
			<textarea name="elefen_admin_menu[classes]"><?php echo $elefen_admin_menu['classes']; ?></textarea>
		</div>

		<div id="checkbox" class="checkbox-button-menu tabcontent">
			<?php
				echo get_menu_admin($elefen_admin_menu);
			?>
		</div>

	    <?php submit_button(); ?>
	
	</form>
	</div>

<?php
}

/**
 * Get menu id from Menu Admin
 * 
 * @param array $elefen_admin_menu 
 * 
 */
function get_menu_id($elefen_admin_menu) {
	
	foreach($elefen_admin_menu as $key => $menu_id) {

		if ($menu_id == 'on') {
			if (strpos($key, '-elefen_child_') !== false) {
				$string_sub = explode("-elefen_child_", $key);
				$string_id .= '#'.$string_sub[0].' > .wp-submenu.wp-submenu-wrap > li:nth-child('.$string_sub[1].') ,';
			} else {
				$string_id .= '#'.$key.' ,';
			}
		}
	}

	$string = rtrim($string_id, ",");
	return $string;
}

/**
 * Get sub menu admin foreach parent menu
 * 
 * @param string $slug
 * @param string $menu_id
 * 
 */
function get_submenu_admin($slug, $menu_id, $elefen_admin_menu) {

	global $submenu;
    $sub_menu_list = $submenu[$slug];
	
	if ($sub_menu_list) {
		$y = 1;

		foreach ($sub_menu_list as $sub_menu) {
			$y++;
			$menu_name = $sub_menu[0];
			$slug_id = 'elefen_admin_menu['.$menu_id.'-elefen_child_'.$y.']';

			?>
			<div class='form-input sub-menu'>
				<label for='<?php echo $slug_id ?>'><?php echo $menu_name ?></label>
				<input  type=checkbox id='<?php echo $slug_id ?>' name='<?php echo $slug_id ?>' <?php echo ($elefen_admin_menu[$menu_id.'-elefen_child_'.$y] == 'on' ? 'checked' : '') ?>>
			</div>
			<?php
		}
	}
}

/**
 * 
 * Hide menu admin 
 * 
 */
function elefen_admin_menu() {
	$elefen_admin_menu = get_option('elefen_admin_menu');

	$elefen_amdin_menu['menu_id'] = get_menu_id($elefen_admin_menu);


	if( $elefen_admin_menu['classes'] || $elefen_amdin_menu['menu_id'] ){
    ?>
        <script>
            jQuery(document).ready(function(){

                jQuery('<?php echo $elefen_admin_menu['classes']; ?>').addClass('hide-admin-menu');
				jQuery('<?php echo $elefen_amdin_menu['menu_id']; ?>').addClass('hide-admin-menu');
                jQuery('.hide-admin-menu').hide();
                jQuery('#wp-admin-bar-root-default').append('<li class="menupop"><a href="javascript:void(null);" class="toggle-admin-menu ab-item">Afficher toutes les options</a></li>');
                jQuery('.toggle-admin-menu').click(function(){
                    if( !jQuery('.hide-admin-menu').is(':visible') ){
                        jQuery(this).text('Cacher les options avancés');
                        jQuery('.hide-admin-menu').show();
                    }else{
                        jQuery(this).text('Afficher toutes les options');
                        jQuery('.hide-admin-menu').hide();
                    }
                });
            });
		</script>
		
    <?php
	} 
}
add_action('admin_head', 'elefen_admin_menu');



/**
 * 
 * Build admin checkbox menu
 *
 */
function get_menu_admin($elefen_admin_menu) {
	foreach ($GLOBALS['menu'] as $menu) {

		$menu_name = $menu[0];

		if ($menu_name != '') {

			$menu_id = $menu[5];
			$pattern = '[^a-zA-Z0-9_-]+';
			$replacement = '-';
			$menu_id = preg_replace('/[^a-zA-Z0-9\_\']/', '-', $menu_id);

			$slug_id = 'elefen_admin_menu['.$menu_id.']';

			?>

			<div class='form-input'>
				<label for='<?php echo $slug_id ?>'><?php echo $menu_name ?></label>
				<input type=checkbox id='<?php echo $slug_id ?>' name='<?php echo $slug_id ?>' <?php echo ($elefen_admin_menu[$menu_id] == 'on' ? 'checked' : '') ?>>
			</div>
			
			<?php

			echo get_submenu_admin($menu[2], $menu_id, $elefen_admin_menu);
		}
	}
}

/**
 * 
 * CSS and JS from admin page
 * 
 */
function custom_css_js() {
	?>

	<style>
		.wp-list-table th{ width:auto; }
		.wp-list-table th, .wp-list-table td, .wp-list-table tr{ vertical-align:middle; }
		.wp-list-table td{text-align:left; }
		.wp-list-table tr:hover td, .wp-list-table tr:hover th{ background-color:#f7fcfe; }
		textarea{ width:100%; min-height:500px; }
		.form-input {display: flex;	align-items: end; margin-bottom: 10px !important;}
		form .submit{ position:fixed;bottom: 0px;z-index: 999; background:#f1f1f1; width:100%; padding-top:1.5em; margin:0 !important; text-align:center;}
		.form-input label {	width: 15%;	display: block;	font-weight: bold;	font-size: 14px; box-sizing : border-box;}
		.form-input.sub-menu label {padding-left : 25px}
		.tab {overflow: hidden;	border: 1px solid #ccc;	background-color: #f1f1f1;}
		.tab a {background-color: inherit;	float: left;border: none;outline: none;	cursor: pointer;padding: 14px 16px;	transition: 0.3s;font-size: 17px; text-decoration : none; color: black;}
		.tab a:hover {	background-color: #ddd;	}
		.tab a.active {background-color: #ccc;	}
		.tabcontent {display: none;padding: 45px;border: 1px solid #ccc;border-top: none;margin-bottom: 25px;}

	</style>

	<script>

		jQuery(document).ready(function(){
		    <?php if($_GET['tab']): ?>
		        jQuery('.tab .tablinks[related-id="<?php echo $_GET['tab']; ?>"]').trigger('click');
		    <?php else: ?>
			    jQuery('.tab .tablinks:first-child').trigger('click');
			<?php endif; ?>
		});

        function removeParam(key, sourceURL) {
            var rtn = sourceURL.split("?")[0],
                param,
                params_arr = [],
                queryString = (sourceURL.indexOf("?") !== -1) ? sourceURL.split("?")[1] : "";
            if (queryString !== "") {
                params_arr = queryString.split("&");
                for (var i = params_arr.length - 1; i >= 0; i -= 1) {
                    param = params_arr[i].split("=")[0];
                    if (param === key) {
                        params_arr.splice(i, 1);
                    }
                }
                rtn = rtn + "?" + params_arr.join("&");
            }
            return rtn;
        }

		function openCity(evt, cityName) {
			var i, tabcontent, tablinks;
			tabcontent = document.getElementsByClassName("tabcontent");
			for (i = 0; i < tabcontent.length; i++) {
				tabcontent[i].style.display = "none";
			}
			tablinks = document.getElementsByClassName("tablinks");
			for (i = 0; i < tablinks.length; i++) {
				tablinks[i].className = tablinks[i].className.replace(" active", "");
			}
			document.getElementById(cityName).style.display = "block";
			evt.target.className += " active";
			document.getElementsByName('_wp_http_referer')[0].value = removeParam('tab', document.getElementsByName('_wp_http_referer')[0].value) + '&tab=' + cityName;
		}
	</script>

	<?php
}
