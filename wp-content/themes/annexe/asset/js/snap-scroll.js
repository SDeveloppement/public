jQuery(document).ready(function(){

    jQuery.fn.isInViewport = function() {

        var elementTop = jQuery(this).offset().top;
        var elementBottom = elementTop + jQuery(this).outerHeight();
        var viewportTop = jQuery(window).scrollTop();
        var viewportBottom = viewportTop + jQuery(window).height();

        return elementBottom > viewportTop && elementTop < viewportBottom;

    };


    jQuery.fn.isSnapBot = function() {

        var elementTop = (jQuery(this).offset().top) + 200;
        var elementBottom = elementTop + jQuery(this).outerHeight();
        var viewportTop = jQuery(window).scrollTop();
        var viewportBottom = viewportTop + jQuery(window).height();
        
        return elementBottom > viewportTop && elementTop < viewportBottom;
    }

    jQuery.fn.isSnapTop = function() {

        var elementTop = (jQuery(this).offset().top);
        var elementBottom = elementTop + jQuery(this).outerHeight() - 300;
        var viewportTop = jQuery(window).scrollTop();
        var viewportBottom = viewportTop + jQuery(window).height();
        
        return elementBottom > viewportTop && elementTop < viewportBottom;
    }

    var current_section = '';

    jQuery('.menu a').on("click", function (){
        var ref = jQuery(this).attr('href');
        current_section = jQuery(ref);
    });

    jQuery('.main-content > div').each(function(){
        if (jQuery(this).isInViewport()) {
            current_section = jQuery(this);
            return false;
        }
    });


    function snap_animation(target) {
        jQuery('body').addClass('do-animation');
        jQuery('html, body').animate({
            scrollTop: target
        }, 500, function(){
            setTimeout(function(){
                jQuery('body').removeClass('do-animation');
            }, 750);
        });
    }

    function scroll_finish(){
        
        if (jQuery(current_section).next().length && jQuery(current_section).next().isInViewport()) {

            var nextSnap = (jQuery(current_section).next().offset().top) - 55 ;

            if (jQuery(current_section).next().attr('id') == "fonctionnement") {
                var nextSnap = (jQuery(current_section).next().offset().top) - 55 ;
            }

            if (jQuery(current_section).next().attr('id') == "section-etape-2") {
                var nextSnap = (jQuery(current_section).next().offset().top) - 80 ;
            }

            var elementTop = (jQuery(current_section).offset().top);
            var elementBottom = elementTop + jQuery(current_section).outerHeight();
            var height = jQuery(window).height();

            var lastSnap = elementBottom - height;

            if (jQuery(current_section).next().isSnapBot()) {

                snap_animation(nextSnap);

                current_section = jQuery(current_section).next();
                
                onMouvement = true;


            } else {

                snap_animation(lastSnap);

            }

        } else if (jQuery(current_section).prev().length && jQuery(current_section).prev().isInViewport()) {
            
            var prevSnap = (jQuery(current_section).prev().offset().top) - 55 ;

            var elementTop = (jQuery(current_section).offset().top);
            var elementBottom = elementTop + jQuery(current_section).outerHeight();
            var height = jQuery(window).height();

            var lastSnap = elementTop - 55;


            if (jQuery(current_section).attr('id') == "fonctionnement") {
                var lastSnap = elementTop - 55;
            }

            if (jQuery(current_section).prev().attr('id') == "section-etape-2") {
                var prevSnap = (jQuery(current_section).prev().offset().top) - 80 ;
            }

            if (jQuery(current_section).prev().isSnapTop()) {

                snap_animation(prevSnap);


                current_section = jQuery(current_section).prev();

            } else {

                snap_animation(lastSnap);

            }
        } else {
            jQuery('.main-content > div').each(function(){
                if (jQuery(this).isInViewport()) {
                    current_section = jQuery(this);
                    return false;
                }
            });
        }

    }

   /* jQuery(window).on('scroll', function(e){
        if (!jQuery('body').hasClass('do-animation')) {

            if (jQuery('body').hasClass('mobile-page')) {
                clearTimeout(jQuery.data(this, 'scrollTimer'));
                jQuery.data(this, 'scrollTimer', setTimeout(function() {
                    scroll_finish();
                }, 250));
            }
        } 
    }); */

    var position = jQuery(window).scrollTop(); 

    jQuery(window).on('scroll', function(e) {

        var scroll = jQuery(window).scrollTop();

        jQuery('.title-row').each(function(){

            var elementTop = jQuery(this).offset().top;
            var elementBottom = elementTop + jQuery(this).outerHeight();
            var viewportTop = jQuery(window).scrollTop();

            var heightElement = elementBottom - elementTop;

            if ((parseInt(elementTop) - parseInt(viewportTop)) <= 70) { 
                
                jQuery('body').css('margin-top', heightElement + 'px');  
                jQuery('.title-row').removeClass('fixed-top');
                jQuery(this).addClass('fixed-top');          
            } 

            if (scroll < position) {
                
                if (jQuery(this).attr('id') == 'pourquoi') {

                    var topNext = jQuery(this).next().offset().top;
                    
                    if (elementBottom <= topNext) {
                        jQuery('body').css('margin-top', '0px'); 
                        jQuery(this).removeClass('fixed-top');
                    }
                }

            }
        });

        position = scroll;

    });



    var lastScrollTop = 0;
    jQuery(window).scroll(function(event){
        var st = jQuery(this).scrollTop();
        lastScrollTop = st;
    });

    var heightLastElement = 0;
    jQuery('.menu-item a').on('click', function(e) {

        e.preventDefault();
        var st = jQuery(window).scrollTop();
        var idSection = jQuery(this).attr('href');

        var elementTitle = idSection + ' .title-row';
        if (idSection == "#pourquoi") {
            elementTitle = idSection;
        }

        var elementTop = jQuery(elementTitle).offset().top;
        var elementBottom = elementTop + jQuery(elementTitle).outerHeight();

        var heightElement = elementBottom - elementTop;

        var elementContentTop = jQuery(elementTitle).next().offset().top;

        var target = elementContentTop - heightElement - 65;

        if (heightLastElement == 0) {
            heightLastElement = heightElement;
        }

        if (st > elementTop){ 
            target =  elementContentTop -  heightElement - heightLastElement - 65;
        } 

        heightLastElement = heightElement;


        jQuery('html, body').animate({
            scrollTop: target
        }, 500)
        
    });

    jQuery('.button-scroll-top').on('click', function(){
        jQuery('.title-row').removeClass('fixed-top');
        jQuery('html, body').animate({
            scrollTop: 0
        }, 1000)
    });

});