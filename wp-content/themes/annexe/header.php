<?php
/**
 * The template for displaying the header
 *
 * Displays all of the head element and everything up until the "site-content" div.
 *
 * @package WordPress
 * @subpackage loisir-sport
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">


	<!--[if lt IE 9]>
	<script src="<?php echo esc_url( get_template_directory_uri() ); ?>/js/html5.js"></script>
	<![endif]-->
	<?php wp_enqueue_script("jquery"); ?>

	<?php wp_head(); ?>
	
</head>
	
<body <?php body_class(); ?>>

<header>
	<div class="top-menu-wrapper-header">
		<div class="top-menu-header">
			<div class="logo-header">
				<a href="<?php echo home_url(); ?>">
					<img src="<?php echo get_template_directory_uri() ?>/images/LAnnexe_LogoF-BlancXCouleur.png" alt="">
				</a>
				
			</div>
			<div class="menu-header">
				<?php wp_nav_menu() ?>
			</div>
			<div class="hamburger-menu">
				<span class="fas fa-ellipsis-h"></span>
				<span class="fas fa-times"></span>
			</div>
		</div>
	</div>
</header>


