function change_page() {
    var page = jQuery(this).data('page');
    var page_prev = page - 1;

    jQuery('.gpage').hide();
    jQuery('#gform_fields > li').addClass('hide-form');
    jQuery('.button-page, .display-button').removeClass('selected');
    jQuery(this).addClass('selected');

    if (jQuery('.start_page_' + page).length) {
        jQuery('.start_page_' + page).prevAll().removeClass('hide-form');
        jQuery('.start_page_' + page_prev).prevAll().addClass('hide-form');
    } else {
        jQuery('.start_page_' + page_prev).prevAll().addClass('hide-form');
        jQuery('.start_page_' + page_prev).nextAll().removeClass('hide-form');
    }

}

function add_page() {
    var page = jQuery('.button-page:last-child').data('page');
    var page_next = page + 1;

    jQuery('.pagination-wrapp').append('<a class="button-page" data-page="'+ page_next +'">Page '+ page_next +'</a>');
    jQuery('.button-page[data-page = "' + page_next + '"]').on('click', change_page);
    jQuery('.button-page[data-page = "' + page_next + '"]').trigger('click');
}

function show_form() {
    jQuery('.display-button, .button-page').removeClass('selected');
    jQuery(this).addClass('selected');
    
    jQuery('#gform_fields > li').removeClass('hide-form');
    jQuery('.gpage').show();
}

jQuery(document).ready(function() {
    
    if (jQuery('#gform_pagination').is(':visible')){

        var y = 1;
        jQuery('.gpage').each(function() {
            jQuery(this).addClass('start_page_' + y);
            y++;
        });

        jQuery('#gform_fields > li').addClass('hide-form');
        jQuery('.start_page_1').prevAll().removeClass('hide-form');

        jQuery('<div class="button-wrapp"><div class="display-wrapp"></div><div class="pagination-wrapp"></div></div>').insertBefore('#gform_pagination');
        jQuery('.display-wrapp').append('<a class="display-button show-form">Show form</a>');


        while (y !=0) {
            jQuery('.pagination-wrapp').prepend('<a class="button-page" data-page="'+ y +'">Page '+ y +'</a>');
            y--;
        }

        jQuery('.button-page:first-child').addClass('selected');

        jQuery('.button-page').on('click', change_page);
        jQuery('input[data-type="page"]').on('click', add_page);
        jQuery('.show-form').on('click', show_form);

        jQuery('.add-buttons input').on('click', function() {
            alert('Attention ! La question se place à la fin du formulaire, veuillez la replacer');
        });

    }
});
