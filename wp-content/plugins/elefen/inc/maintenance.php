<?php
function elefen_maintenance_register_settings() {
   register_setting( 'elefen_maintenance', 'elefen_maintenance' );          
}
add_action( 'admin_init', 'elefen_maintenance_register_settings' );

function elefen_maintenance_register_options_page() {
  add_submenu_page( 'elefen', 'Maintenance', 'Maintenance', 'manage_options', 'elefen_maintenance', 'elefen_maintenance_options_page' );
}
add_action('admin_menu', 'elefen_maintenance_register_options_page');


function myuploadscript() {
	if ( ! did_action( 'wp_enqueue_media' ) ) {
		wp_enqueue_media();
	}
 	wp_enqueue_script( 'myuploadscript', plugin_dir_url(__FILE__).'../assets/js/uploadfile.js', array('jquery'), null, false );
}
 
add_action( 'admin_enqueue_scripts', 'myuploadscript' );

function elefen_maintenance_options_page() {
    
    ?>

    <style>
    .forms-input {display: flex;align-items: baseline;margin-bottom: 30px;}
    .forms-input label {font-weight: bold;	width: 25%;}
    .wrap > h1 {margin-bottom: 50px;}
    .true_pre_image, .upload_image_button img {max-height: 150px;}
    
    </style>

    <div class="wrap">
        <h1>Mantenance Setting</h1>
        
        <form action="options.php" method="post">
        
            <?php 

            settings_fields('elefen_maintenance');
            do_settings_sections( 'elefen_maintenance' );
            $elefen_maintenance = get_option('elefen_maintenance');

            ?>

            <div class="forms-input">
                <label for="enable">Enable</label>
                <input type="checkbox" name="elefen_maintenance[enable]" <?php echo ($elefen_maintenance['enable'] == 'on' ? 'checked' : '') ?>>
            </div>

            <div class="forms-input">
                <label for="elefen_maintenance[logo]">Logo</label>
                <?php echo image_uploader_field('elefen_maintenance[logo]', $elefen_maintenance['logo']) ?>
            </div>

            <div class="forms-input">
                <label for="elefen_maintenance[texte]">Texte</label>
                <textarea name="elefen_maintenance[texte]" cols="30" rows="10"><?php echo $elefen_maintenance['texte'] ?></textarea>
            </div>

            <div class="forms-input">
                <label for="">Redirection link (Empty for no redirection)</label>
                <input type="text" name="elefen_maintenance[redirection]" value="<?php echo $elefen_maintenance['redirection'] ?>">
            </div>

            <div class="forms-input">
                <label for="elefen_maintenance[robots]">Allow Robots Google</label>
                <input type="checkbox" name="elefen_maintenance[robots]" <?php echo ($elefen_maintenance['robots'] == 'on' ? 'checked' : '') ?>>
            </div>

            <?php submit_button(); ?>
        
        </form>
    </div>

    <?php

}


function image_uploader_field( $name, $value = '') {
	$image = ' button">Upload image';
	$image_size = 'full'; // it would be better to use thumbnail size here (150x150 or so)
	$display = 'none'; // display state ot the "Remove image" button
 
	if( $image_attributes = wp_get_attachment_image_src( $value, $image_size ) ) {
 
		// $image_attributes[0] - image URL
		// $image_attributes[1] - image width
		// $image_attributes[2] - image height
 
		$image = '"><img src="' . $image_attributes[0] . '" style="max-width:95%;display:block;" />';
		$display = 'inline-block';
 
	} 
 
	return '
	<div>
		<a href="#" class="upload_image_button' . $image . '</a>
		<input type="hidden" name="' . $name . '" id="' . $name . '" value="' . esc_attr( $value ) . '" />
		<a href="#" class="remove_image_button" style="display:inline-block;display:' . $display . '">Remove image</a>
	</div>';
}

// Activate WordPress Maintenance Mode
function wp_maintenance_mode() {

    $elefen_maintenance = get_option('elefen_maintenance');

    $robots = false;

    if ($elefen_maintenance['robots']) {

        $crawler = crawlerDetect($_SERVER['HTTP_USER_AGENT']);

        if ( $crawler ) {
            $robots = true;
        }

    }
   

    if ($robots == false ) {
        if ((!current_user_can('edit_themes') || !is_user_logged_in()) && ( !is_login_page()) ) {

            add_filter( 'wp_die_handler', function () {

                    return function ( $message, $title, $args ) {
                        _default_wp_die_handler( $message, get_bloginfo('name'), $args );
                    };
            
                return '_default_wp_die_handler';
            } );

            if ($elefen_maintenance['redirection']) {
                $html = '                
                <script>
                        var counter = 10;
                        
                        setInterval(function() {
                            counter--;
                            if (counter >= 0) {
                            span = document.getElementById("count");
                            console.log(counter);
                            span.innerHTML = counter;
                            }
                            if (counter === 0) {
                                window.location.href = "'.$elefen_maintenance['redirection'].'";
                            }
    
                        }, 1000);
                </script>';
            }
            
            $html .= '
                    <style>
                        .logo img { max-width: 100%; }
                        .logo { text-align: center; margin-bottom: 50px;max-width : 100%}
                        .redirection { margin-top: 25px; text-align: center; font-weight: bold;}
                        html {background: #222;height : 100vh; display : flex; align-items: center;}
                        body#error-page {
                            background: #222;
                            border: none;
                            box-shadow: none;
                            color: white;
                            text-align: center;
                            font-size: calc(14px + 1vw);
                            width: 100% !important;
                            max-width: 100%;
                        }
                        #error-page p, #error-page .wp-die-message {font-size: calc(14px + 1vw);}

                    </style>
    
    
    
                    <div class="logo">
                        <img src="'.wp_get_attachment_url($elefen_maintenance['logo']).'"/>
                    </div>
                    <div class="text-maintenance">
                        '.$elefen_maintenance['texte'].'
                    </div>
                    ';
    
            if ($elefen_maintenance['redirection']) {
                $html .= '<div class="redirection"><span id="count">10</span></span>';
            }
                    
    
            wp_die($html);
        }
    } 
    

}
add_action('get_header', 'wp_maintenance_mode');



function my_page_title() {
    return 'Your value is '; // add dynamic content to this title (if needed)
}

function is_login_page() {
    return in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'));
}

function crawlerDetect($USER_AGENT)
{
    $crawlers = array(
        array('Google', 'Google')
    );
    
    
    foreach ($crawlers as $c)
    {
        if (stristr($USER_AGENT, $c[0]))
        {
            return $c[1];
        }
    }
 
    return false;
}
 
 



