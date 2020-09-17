function change_acf_column(){
    if (jQuery(this).val() != '')  {

        var value = jQuery(this).val();

        if (value < 100 ) {
            value = value - 1;
        }
        jQuery(this).parents('.layout[data-layout="colonne"]').css({
            'width': value+'%',
            'margin' : '10px 0'
        });
        jQuery(this).parents('.layout[data-layout="colonne"]').parent().css({
            'display' : 'flex', 
            'flex-wrap' : 'wrap', 
            'justify-content': 'space-between'
        });
    }
}

function change_section_label(){
    
    var value = jQuery(this).val().toUpperCase();

    if (value) {
        var span = jQuery(this).parents('.layout[data-layout="section"]')
            .children('.acf-fc-layout-handle.ui-sortable-handle')
            .children('.acf-fc-layout-order').html();

        jQuery(this).parents('.layout[data-layout="section"]')
            .children('.acf-fc-layout-handle.ui-sortable-handle')
            .html('<span class="acf-fc-layout-order">'+span+'</span> '+value);
    }
}

jQuery(document).ready(function(){
    jQuery('.acf-postbox').on('change', function(){
        jQuery('.acf-field.acf-field-number[data-name="largeur"] .acf-input-wrap input[type="number"]').each(change_acf_column);
    });

    jQuery('.acf-field.acf-field-number[data-name="largeur"] .acf-input-wrap input[type="number"]').each(change_acf_column);

    jQuery('.layout[data-layout="row"]').css('background', '#cecece');
    jQuery('.layout[data-layout="section"]').css('background', '#a7a7a7');

    jQuery('.acf-field[data-name="id"] input[type="text"]').each(change_section_label);

    jQuery('.acf-field[data-name="id"] input[type="text"]').on('change', function(){

        var value = jQuery(this).val().toUpperCase();

        if (value) {
            var span = jQuery(this).parents('.layout[data-layout="section"]')
                .children('.acf-fc-layout-handle.ui-sortable-handle')
                .children('.acf-fc-layout-order').html();

            jQuery(this).parents('.layout[data-layout="section"]')
                .children('.acf-fc-layout-handle.ui-sortable-handle')
                .html('<span class="acf-fc-layout-order">'+span+'</span> '+value);
        }
        
    });

});