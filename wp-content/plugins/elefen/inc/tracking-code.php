<?php

function elefen_tracking_code_register_settings() {
   register_setting( 'elefen_tracking_code', 'elefen_tracking_code' );          
}
add_action( 'admin_init', 'elefen_tracking_code_register_settings' );

function elefen_tracking_code_register_options_page() {
  add_submenu_page( 'elefen', 'Tracking Codes', 'Tracking Codes', 'manage_options', 'elefen_tracking_code', 'elefen_tracking_code_options_page' );
}
add_action('admin_menu', 'elefen_tracking_code_register_options_page');



function elefen_tracking_code_options_page()
{
	
	//$dir = plugin_dir_path(  __FILE__  )."inc/";
	//$files = array_diff(scandir($dir), array('..', '.'));	
	?>
	<div class="wrap">
	<h1>Advanced Settings</h1>
	
	<form method="post" action="options.php">
	    <?php 
	    		settings_fields( 'elefen_tracking_code' );
	    		do_settings_sections( 'elefen_tracking_code' );
			$elefen_tracking_code = get_option('elefen_tracking_code');
	    	?>
	    
	    <style>
	    .wp-list-table th{ width:auto; }
	    .wp-list-table th, .wp-list-table td, .wp-list-table tr{ vertical-align:middle; }
	    .wp-list-table td{text-align:left; }
	    .wp-list-table tr:hover td, .wp-list-table tr:hover th{ background-color:#f7fcfe; }
	    </style>
	    
	    <h2>Tracking Codes</h2>
	    <table class="wp-list-table widefat">
	    		<tbody id="the-list">
		        <tr>
			        <th scope="row">
			        		Google Analytics<br />
			        		<small>Tracking ID</small>
			        	</th>
			        <td>
			        		<input type="text" name="elefen_tracking_code[google_analytics]" value="<?php echo $elefen_tracking_code['google_analytics']; ?>" />
			        	</td>
		        </tr>
		        <tr>
			        <th scope="row">
			        		Google Webmaster<br />
			        		<small>ID</small>
			        	</th>
			        <td>
							<input type="text" name="elefen_tracking_code[google_webmaster]" value="<?php echo $elefen_tracking_code['google_webmaster']; ?>" />
			        	</td>
		        </tr>
	        </tbody>
	    </table>
	    
	    <?php submit_button(); ?>
	
	</form>
	</div>

	
<?php 
} 


function elefen_tracking_code() {
	$elefen_tracking_code = get_option('elefen_tracking_code');
	if( $elefen_tracking_code['google_analytics'] ) :
		
	?>
	

		<!-- Google Analytics -->
		<script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

			ga('create', '<?php echo $elefen_tracking_code['google_analytics'] ?>', 'auto');
			ga('send', 'pageview');
		</script>
			<!-- End Google Analytics -->
	<?php
	endif;

	if($elefen_tracking_code['google_webmaster']):
		?>
		<meta name="google-site-verification" content="<?php echo $elefen_tracking_code['google_webmaster']; ?>" />
		<?php
	endif;
}
add_action('wp_head', 'elefen_tracking_code');
