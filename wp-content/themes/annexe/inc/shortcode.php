<?php

function add_google_map(){
    $html = ' <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2777.436359206857!2d-72.50774848411282!3d45.88258521344867!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4cc813df6b85e237%3A0x6dfa90c0a4e65795!2s1284%20Rue%20Cormier%2C%20Drummondville%2C%20QC%20J2C%207M8!5e0!3m2!1sfr!2sca!4v1597780861395!5m2!1sfr!2sca" width="600" height="450" frameborder="0" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>' ;
    
    return $html;
}
add_shortcode( 'map', 'add_google_map' );

function add_row_header(){
    $html = '	<div class="image-header-wrapper" style="background: url(\''.get_template_directory_uri().'/images/Flou.jpg\')">
                    <div class="background-image-header">
                        <div class="logo-sub-header">
                            <img src="'.get_template_directory_uri().'/images/LAnnexe_LogoF-BlancXCouleur.png" alt="">
                            <span>Centre d’expertise intégré pour entrepreneurs</span>
                        </div>
                    </div>
                </div>';

    return $html; 
}
add_shortcode('header', 'add_row_header');