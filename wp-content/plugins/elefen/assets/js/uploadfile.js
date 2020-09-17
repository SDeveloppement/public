function rafraichissement() {
    var counter = 10;

    setInterval(function() {
        counter--;
        if (counter >= 0) {
        span = document.getElementById("count");
        span.innerHTML = counter;
        }
        // Display 'counter' wherever you want to display it.
        if (counter === 0) {
        //    alert('this is where it happens');
            window.location.href = "https://elefen.co/wickham-extranet/";
        }

    }, 1000);
}

function upload_image(e) {
    e.preventDefault();
    
            var button = jQuery(this),
                custom_uploader = wp.media({
            title: 'Insert image',
            library : {
                type : 'image'
            },
            button: {
                text: 'Use this image' // button label text
            },
            multiple: false // for multiple image selection set to true
        }).on('select', function() { // it also has "open" and "close" events 
            var attachment = custom_uploader.state().get('selection').first().toJSON();
            jQuery(button).removeClass('button').html('<img class="true_pre_image" src="' + attachment.url + '" style="max-width:95%;display:block;" />').next().val(attachment.id).next().show();
        })
        .open();
}

function remove_image(){
    jQuery(this).hide().prev().val('').prev().addClass('button').html('Upload image');
    return false;
}

jQuery(document).on('ready', function() {

    jQuery('body').on('click', '.upload_image_button', upload_image);
    jQuery('body').on('click', '.remove_image_button', remove_image);


    if (jQuery('#count').length) {
        rafraichissement();
    }
    

});

