<?php get_header(); ?>

<?php 
if (have_rows('page_template')) {
    include('inc/page-template.php');

    ?><div class="main-content"><?php 
    get_custom_page_template();
    ?></div><?php
} else {
    if (have_posts()) {
        while (have_posts()) {
            the_post(); ?>
            <section class="main-content">
                <div class="content-wrap">
                    <div class="page content">
                        <h1 class="title"><?php wp_title(''); ?></h1>
                        <?php the_content(); ?>
                    </div><!-- /.blog -->
                </div><!-- /.content-wrap -->
            </section><?php
        } 
        
     }
}

get_footer(); ?>