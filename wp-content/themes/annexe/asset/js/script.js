function sizeWindow() {
    if (jQuery(window).width() > 768) {
        jQuery('body').addClass('desktop-page');
        jQuery('body').removeClass('mobile-page');
    } else {
        jQuery('body').addClass('mobile-page');
        jQuery('body').removeClass('desktop-page');
    }
}


jQuery(document).ready(function(){


    jQuery('.hamburger-menu').on('click', function(){
        jQuery(this).toggleClass('active');
        jQuery('.menu-header').toggleClass('active');
        
    });

    jQuery('.menu-header li > a').on('click', function(){
        jQuery('.hamburger-menu').removeClass('active');
        jQuery('.menu-header').removeClass('active');
    });

    jQuery('#mail').on('click', function(e){
        if( typeof ga !== 'undefined' ){
        	ga('send', 'event', 
            {
                eventCategory: 'Lien',
                eventAction: 'Click',
                eventLabel: 'Courriel'
            });
        } 
    });

    jQuery('#phone').on('click', function(){
        if( typeof ga !== 'undefined' ){
        	ga('send', 'event', 
            {
                eventCategory: 'Lien',
                eventAction: 'Click',
                eventLabel: 'Telephone'
            });
        } 
    });

    jQuery('#gform_submit_button_1').on('click', function(){
        if( typeof ga !== 'undefined' ){
        	ga('send', 'event', 
            {
                eventCategory: 'Formulaire',
                eventAction: 'Click',
                eventLabel: 'Formulaire'
            });
        } 
    });

    sizeWindow();
    jQuery(window).on('resize', sizeWindow);

});