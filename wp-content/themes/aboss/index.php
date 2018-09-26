<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * For example, it puts together the home page when no home.php file exists.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Aboss_Theme
 * @since Aboss 1.0
 */

$aboss_opt = get_option( 'aboss_opt' );

get_header();

$aboss_bloglayout = 'sidebar';

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
	$aboss_bloglayout = 'nosidebar';
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
		Aboss_Class::aboss_post_thumbnail_size('aboss-category-thumb');
		break;
	case 'grid':
		$aboss_blogclass = 'grid';
		$aboss_blogcolclass = 9;
		Aboss_Class::aboss_post_thumbnail_size('aboss-category-thumb');
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

						<?php /* Start the Loop */ ?>
						<?php while ( have_posts() ) : the_post(); ?>
							
							<?php get_template_part( 'content', get_post_format() ); ?>
							
						<?php endwhile; ?>

						<div class="pagination">
							<?php Aboss_Class::aboss_pagination(); ?>
						</div>
						
					<?php else : ?>

						<article id="post-0" class="post no-results not-found">

						<?php if ( current_user_can( 'edit_posts' ) ) :
							// Show a different message to a logged-in user who can add posts.
						?>
							<header class="entry-header">
								<h1 class="entry-title"><?php esc_html_e( 'No posts to display', 'aboss' ); ?></h1>
							</header>

							<div class="entry-content">
								<p><?php printf( wp_kses(__( 'Ready to publish your first post? <a href="%s">Get started here</a>.', 'aboss' ), array('a'=>array('href'=>array()))), admin_url( 'post-new.php' ) ); ?></p>
							</div><!-- .entry-content -->

						<?php else :
							// Show the default message to everyone else.
						?>
							<header class="entry-header">
								<h1 class="entry-title"><?php esc_html_e( 'Nothing Found', 'aboss' ); ?></h1>
							</header>

							<div class="entry-content">
								<p><?php esc_html_e( 'Apologies, but no results were found. Perhaps searching will help find a related post.', 'aboss' ); ?></p>
								<?php get_search_form(); ?>
							</div><!-- .entry-content -->
						<?php endif; // end current_user_can() check ?>

						</article><!-- #post-0 -->

					<?php endif; // end have_posts() check ?>
				</div>
				
			</div>
			<?php if( $aboss_blogsidebar=='right') : ?>
				<?php get_sidebar(); ?>
			<?php endif; ?>
		</div>
	</div> 
</div>
<?php get_footer(); ?>