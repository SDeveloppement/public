<?php

function elefen_template_overlay_register_settings() {
   register_setting( 'elefen_template_overlay', 'elefen_template_overlay' );          
}
add_action( 'admin_init', 'elefen_template_overlay_register_settings' );

function elefen_template_overlay_register_options_page() {
  add_submenu_page( 'elefen', 'Template Overlay', 'Template Overlay', 'manage_options', 'elefen_template_overlay', 'elefen_template_overlay_options_page' );
}
add_action('admin_menu', 'elefen_template_overlay_register_options_page');


add_action( 'admin_enqueue_scripts', 'load_wp_media_files' );
function load_wp_media_files( $page ) {
    wp_enqueue_media();
}


function elefen_template_overlay_options_page()
{
	?>
	<div class="wrap">
	<h1>Advanced Settings</h1>
	
	<form method="post" action="options.php">
	    <?php 
	    		settings_fields( 'elefen_template_overlay' );
	    		do_settings_sections( 'elefen_template_overlay' );
			$elefen_template_overlay = get_option('elefen_template_overlay');
	    	?>
	    
	    <style>
	    .wp-list-table th{ width:auto; }
	    .wp-list-table th, .wp-list-table td, .wp-list-table tr{ vertical-align:middle; }
	    .wp-list-table td{text-align:left; }
	    .wp-list-table tr:hover td, .wp-list-table tr:hover th{ background-color:#f7fcfe; }
	    .wp-list-table img{ max-height:50px; max-width:50px; vertical-align:middle; margin-right:20px }
	    </style>
	    
	    <script>
	    jQuery(document).ready( function($) {

	      jQuery('input#myprefix_media_manager').click(function(e) {
	
	             e.preventDefault();
	             var image_frame;
	             if(image_frame){
	                 image_frame.open();
	             }
	             // Define image_frame as wp.media object
	             image_frame = wp.media({
	                           title: 'Select Media',
	                           multiple : false,
	                           library : {
	                                type : 'image',
	                            }
	                       });
				image_frame.link_id = jQuery(this).attr('rel');
				
	                       image_frame.on('close',function(link_id) {
	                          // On close, get selections and save to the hidden input
	                          // plus other AJAX stuff to refresh the image preview
	                          var selection =  image_frame.state().get('selection');
	                          var gallery_ids = new Array();
	                          var my_index = 0;
	                          selection.each(function(attachment) {
	                             gallery_ids[my_index] = attachment['id'];
	                             my_index++;
	                          });
	                          var ids = gallery_ids.join(",");
	                          
	                          jQuery('input#template_'+image_frame.link_id+'_value').val(ids);
	                       });
	
	                      image_frame.on('open',function(link_id) {
	                        // On open, get the id from the hidden input
	                        // and select the appropiate images in the media manager
	                        var selection =  image_frame.state().get('selection');
	                        ids = jQuery('input#template_'+image_frame.link_id+'_value').val().split(',');
	                        ids.forEach(function(id) {
	                          attachment = wp.media.attachment(id);
	                          attachment.fetch();
	                          selection.add( attachment ? [ attachment ] : [] );
	                        });
	
	                      });
	
	                    image_frame.open();
		     });
		
			jQuery('.link-exemple').click(function(){
				jQuery(this).next().show();
			});
		
		});
		</script>
	    
	    <h2>Template Overlay</h2>
	    <table class="wp-list-table widefat">
	    		<tbody id="the-list">
		        <tr>
			        <th scope="row">
			        		How many template do you have?
			        	</th>
			        <td>
						<input type="text" name="elefen_template_overlay[number]" value="<?php echo $elefen_template_overlay['number']; ?>" />
			        	</td>
			        	<td></td>
		        </tr>
		        <?php for($i = 0; $i < $elefen_template_overlay['number']; $i++): ?>
		        <tr>
			        <th scope="row">
			        		<input placeholder="Template name" type="text" name="elefen_template_overlay[template_<?php echo $i; ?>_name]" value="<?php if(isset($elefen_template_overlay['template_'.$i.'_name'])) echo $elefen_template_overlay['template_'.$i.'_name']; ?>" />
			        	</th>
			        <td>
						<?php
						if( isset($elefen_template_overlay['template_'.$i.'_value']) ) { echo wp_get_attachment_image( $elefen_template_overlay['template_'.$i.'_value'], 'medium', false, array( 'id' => 'myprefix-preview-image' ) ); }
						?>
						<input type="hidden" name="elefen_template_overlay[template_<?php echo $i; ?>_value]" id="template_<?php echo $i; ?>_value" value="<?php echo $elefen_template_overlay['template_'.$i.'_value']; ?>" class="regular-text" />
						<input type='button' class="button-primary" value="<?php esc_attr_e( 'Select a image' ); ?>" rel="<?php echo $i; ?>" id="myprefix_media_manager"/>
			        	</td>
			        <td>
			        		<?php if( $elefen_template_overlay['template_'.$i.'_exemple'] ) echo '<a href="'.$elefen_template_overlay['template_'.$i.'_exemple'].(strpos($elefen_template_overlay['template_'.$i.'_exemple'],'?')?'&':'?').'template='.$i.'&opacity=8&offset=0" target="_blank"">'.$elefen_template_overlay['template_'.$i.'_exemple'].'</> <a href="javascript:void(null);" class="link-exemple"><small>[EDIT]</small></a>'; ?>
						<input placeholder="Test url" style="<?php if( $elefen_template_overlay['template_'.$i.'_exemple'] ) echo 'display:none;'; ?>" type="text" name="elefen_template_overlay[template_<?php echo $i; ?>_exemple]" value="<?php if(isset($elefen_template_overlay['template_'.$i.'_exemple'])) echo $elefen_template_overlay['template_'.$i.'_exemple']; ?>" />
			        	</td>
		        </tr>
		        <?php endfor; ?>
	        </tbody>
	    </table>
	    
	    <?php submit_button(); ?>
	
	</form>
	</div>
<?php 
} 



function elefen_template_overlay() {
	$elefen_template_overlay = get_option('elefen_template_overlay');
	if( $elefen_template_overlay['number'] ):
    ?>
    		<style>
    			.template_overlay{ position: fixed;z-index: 9999;bottom: 0px;right: 0px;color:#000; }
    			.template_overlay select, .template_overlay input{  background:#111!important; color:#eee; font-size:10px; display:inline-block; vertical-align:middle; width:auto!important; border:0px!important; border-radius:0px; height:20px; line-height:10px; padding:2.5px!important; }
    			.template_overlay input[type="number"]{ width:50px!important; }
    		</style>
    		
    		<script>
    		jQuery(document).ready(function(){
    			jQuery('.template_overlay .toggle').click(function(){
    				if( jQuery('body').attr('style') )
    					jQuery('body').removeAttr('style')
    				else
    					jQuery('body').attr('style','opacity:1!important;');
    			});
    		});
    		</script>
    		
        <div class="template_overlay">
        		<form action="" method="GET">
	           	<select name="template">
	           		<?php for($i = 0; $i < $elefen_template_overlay['number']; $i++): ?>
		        		<option value="<?php echo $i; ?>" <?php if(isset($_GET['template'])) echo ($i==$_GET['template']?'selected':''); ?>><?php echo $elefen_template_overlay['template_'.$i.'_name']; ?></option>
		        		<?php endfor; ?>
	           	</select>
	           	<input title="Opacity" type="number" min="1" max="99" name="opacity" value="<?php echo (isset($_GET['opacity'])?$_GET['opacity']:'5'); ?>" />
	           	<input type="number" title="Y Offset" min="0" name="offset" value="<?php echo (isset($_GET['offset'])?$_GET['offset']:'0'); ?>" />
	           	<input type="submit" value="Apply" />
	           	<input type="button" class="toggle" value="Toggle" />
           	</form>
        </div>
    <?php
	endif;
	
	if( isset($_GET['template']) ): ?>
	<?php $metas = wp_get_attachment_metadata( $elefen_template_overlay['template_'.$_GET['template'].'_value'] ); ?>
		<style>
    			body{ opacity:0.<?php echo $_GET['opacity']; ?>; }
    			html{ background-repeat:no-repeat; background-position:center <?php echo (isset($_GET['offset'])?$_GET['offset'].'px':'top'); ?>; background-image:url('<?php echo wp_get_attachment_image_url( $elefen_template_overlay['template_'.$_GET['template'].'_value'], 'full', false ); ?>'); }
    		</style>
	<?php endif;
}
add_action('wp_footer', 'elefen_template_overlay');

