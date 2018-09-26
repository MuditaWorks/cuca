<?php
/**
 * The template for displaying Archive pages
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * If you'd like to further customize these archive views, you may create a
 * new template file for each specific one. For example, Aboss already
 * has tag.php for Tag archives, category.php for Category archives, and
 * author.php for Author archives.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Aboss_Theme
 * @since Aboss 1.0
 */

$aboss_opt = get_option( 'aboss_opt' );

get_header();
?>
<?php 
$aboss_bloglayout = 'nosidebar';
if(isset($aboss_opt['blog_layout']) && $aboss_opt['blog_layout']!=''){
	$aboss_bloglayout = $aboss_opt['blog_layout'];
}
if(isset($_GET['layout']) && $_GET['layout']!=''){
	$aboss_bloglayout = $_GET['layout'];
}
$aboss_blogsidebar = 'right';
if(isset($aboss_opt['sidebarblog_pos']) && $aboss_opt['sidebarblog_pos']!=''){
	$aboss_blogsidebar = $aboss_opt['sidebarblog_pos'];
}
if(isset($_GET['sidebar']) && $_GET['sidebar']!=''){
	$aboss_blogsidebar = $_GET['sidebar'];
}
if ( !is_active_sidebar( 'sidebar-1' ) )  {
	$aboss_blogsidebar = 'nosidebar';
}
switch($aboss_bloglayout) {
	case 'sidebar':
		$aboss_blogclass = 'blog-sidebar';
		$aboss_blogcolclass = 9;
		Aboss_Class::aboss_post_thumbnail_size('aboss-category-thumb');
		break;
	case 'largeimage':
		$aboss_blogclass = 'blog-large';
		$aboss_blogcolclass = 9;
		$aboss_postthumb = '';
		break;
	default:
		$aboss_blogclass = 'blog-nosidebar';
		$aboss_blogcolclass = 12;
		$aboss_blogsidebar = 'none';
		Aboss_Class::aboss_post_thumbnail_size('aboss-post-thumb');
}
?>
<div class="main-container">
	<div class="container">
		<div class="title-breadcrumb"> 
			<div class="title-breadcrumb-inner">
				<header class="entry-header">
					<h1 class="entry-title"><?php if(isset($aboss_opt)) { echo esc_html($aboss_opt['blog_header_text']); } else { esc_html_e('Blog', 'aboss');}  ?></h1>
				</header>	
				<?php Aboss_Class::aboss_breadcrumb(); ?>
				 
			</div>
		</div>
	</div>
	<div class="container">
		<div class="row">
			
			<?php if($aboss_blogsidebar=='left') : ?>
				<?php get_sidebar(); ?>
			<?php endif; ?>
			
			<div class="col-xs-12 <?php echo 'col-md-'.$aboss_blogcolclass; ?>">
				<div class="page-content blog-page <?php echo esc_attr($aboss_blogclass); if($aboss_blogsidebar=='left') {echo ' left-sidebar'; } if($aboss_blogsidebar=='right') {echo ' right-sidebar'; } ?>">
					<?php if ( have_posts() ) : ?>
						<header class="archive-header">
							<?php
								the_archive_title( '<h1 class="archive-title">', '</h1>' );
								the_archive_description( '<div class="archive-description">', '</div>' );
							?>
						</header>

						<?php
						/* Start the Loop */
						while ( have_posts() ) : the_post();

							/* Include the post format-specific template for the content. If you want to
							 * this in a child theme then include a file called called content-___.php
							 * (where ___ is the post format) and that will be used instead.
							 */
							get_template_part( 'content', get_post_format() );

						endwhile;
						?>
						
						<div class="pagination">
							<?php aboss_Class::aboss_pagination(); ?>
						</div>
						
					<?php else : ?>
						<?php get_template_part( 'content', 'none' ); ?>
					<?php endif; ?>
				</div>
			</div>
			<?php if( $aboss_blogsidebar=='right') : ?>
				<?php get_sidebar(); ?>
			<?php endif; ?>
		</div>
	</div> 
</div>
<?php get_footer(); ?>