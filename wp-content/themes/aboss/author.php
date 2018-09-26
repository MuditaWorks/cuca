<?php
/**
 * The template for displaying Author Archive pages
 *
 * Used to display archive-type pages for posts by an author.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Aboss_Theme
 * @since Aboss 1.0
 */

$aboss_opt = get_option( 'aboss_opt' );

get_header();

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
				</header
				<?php Aboss_Class::aboss_breadcrumb(); ?>
				> 
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

						<?php
							/* Queue the first post, that way we know
							 * what author we're dealing with (if that is the case).
							 *
							 * We reset this later so we can run the loop
							 * properly with a call to rewind_posts().
							 */
							the_post();
						?>

						<header class="archive-header">
							<h1 class="archive-title"><?php printf( esc_html__( 'Author Archives: %s', 'aboss' ), '<span class="vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( "ID" ) ) ) . '" title="' . esc_attr( get_the_author() ) . '" rel="me">' . get_the_author() . '</a></span>' ); ?></h1>
						</header><!-- .archive-header -->

						<?php
							/* Since we called the_post() above, we need to
							 * rewind the loop back to the beginning that way
							 * we can run the loop properly, in full.
							 */
							rewind_posts();
						?>

						<?php
						// If a user has filled out their description, show a bio on their entries.
						if ( get_the_author_meta( 'description' ) ) : ?>
						<div class="author-info archives">
							<div class="author-avatar">
								<?php
								/**
								 * Filter the author bio avatar size.
								 *
								 * @since Aboss 1.0
								 *
								 * @param int $size The height and width of the avatar in pixels.
								 */
								$author_bio_avatar_size = apply_filters( 'aboss_author_bio_avatar_size', 68 );
								echo get_avatar( get_the_author_meta( 'user_email' ), $author_bio_avatar_size );
								?>
							</div><!-- .author-avatar -->
							<div class="author-description">
								<h2><?php printf( esc_html__( 'About %s', 'aboss' ), get_the_author() ); ?></h2>
								<p><?php the_author_meta( 'description' ); ?></p>
							</div><!-- .author-description	-->
						</div><!-- .author-info -->
						<?php endif; ?>

						<?php /* Start the Loop */ ?>
						<?php while ( have_posts() ) : the_post(); ?>
							<?php get_template_part( 'content', get_post_format() ); ?>
						<?php endwhile; ?>
						
						<div class="pagination">
							<?php Aboss_Class::aboss_pagination(); ?>
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