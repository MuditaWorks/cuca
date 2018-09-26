<?php
/**
 * Template Name: Full Width
 *
 * Description: Full Width page template
 *
 * @package WordPress
 * @subpackage Aboss_Theme
 * @since Aboss 1.0
 */
$aboss_opt = get_option( 'aboss_opt' );

get_header();
?>
<div class="main-container full-width">
	<div class="title-breadcrumb"> 
		<div class="container">
			<div class="title-breadcrumb-inner">
				
				<header class="entry-header"> 
					<h1 class="entry-title"><?php the_title(); ?></h1> 
				</header>
				<?php Aboss_Class::aboss_breadcrumb(); ?>   
			</div>  
		</div>
	</div>
	
	<div class="page-content">
		<div class="container">
			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'content', 'page' ); ?>
			<?php endwhile; ?>
		</div>
	</div>
</div>
<?php get_footer(); ?>