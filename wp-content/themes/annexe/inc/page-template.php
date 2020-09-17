<?php 

function get_custom_page_template() {
    while (have_rows('page_template')) {
        the_row();
        if (get_row_layout() == 'section') {
            get_loop_start();
            while (have_rows('section')) {
                the_row();  
                if (get_row_layout() == 'row') {
                    get_loop_start();
                    while (have_rows('row')) {
                        the_row();
                        if (get_row_layout() == 'colonne') {
                            get_loop_start();
                            while (have_rows('contenu')){
                                the_row();
                                $name = get_row_layout();
                                include( get_template_directory().'/templates/template-'.$name.'.php' );
                            }
                            get_loop_end();
                        }
                    }
                    get_loop_end();
                }
            }
            get_loop_end();
        }
    }
}

function get_loop_start() {

    $classe = get_sub_field( 'classe_css' );
    $id = get_sub_field('id');
    $style = get_sub_field('style');
    $name = get_row_layout();

    if ($name == 'colonne') {
        $style .= 'width : '.get_sub_field('largeur').'%;';
    }

    $string_id = "";

    if ($id) {
        $string_id = 'id="'.$id.'"';
    }

    echo '<div '.$string_id.' class="wrap-'.$name.'-template '.$classe.'" style="'.$style.'">';
    echo '<div class="'.$name.'-template">';

}

function get_loop_end(){
    echo '</div></div>';
}
