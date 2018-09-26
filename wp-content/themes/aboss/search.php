<?php
/**
 * The template for displaying Search Results pages
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
	<div class="title-breadcrumb">
		<div class="container">
			
			<div class="title-breadcrumb-inner">
				<header class="entry-header">
					<h1 class="entry-title"><?php if(isset($aboss_opt)) { echo esc_html($aboss_opt['blog_header_text']); } else { esc_html_e('Blog', 'aboss');}  ?></h1>
				</header> 
			</div>
			<?php Aboss_Class::aboss_breadcrumb(); ?>
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
							<h1 class="archive-title"><?php printf( wp_kses(__( 'Search Results for: %s', 'aboss' ), array('span'=>array())), '<span>' . get_search_query() . '</span>' ); ?></h1>
						</header><!-- .archive-header -->

						<?php /* Start the Loop */ ?>
						<?php while ( have_posts() ) : the_post(); ?>
							<?php get_template_part( 'content', get_post_format() ); ?>
						<?php endwhile; ?>

						<div class="pagination">
							<?php Aboss_Class::aboss_pagination(); ?>
						</div>

					<?php else : ?>

						<article id="post-0" class="post no-results not-found">
							<header class="entry-header">
								<h1 class="entry-title"><?php esc_html_e( 'Nothing Found', 'aboss' ); ?></h1>
							</header>

							<div class="entry-content">
								<p><?php esc_html_e( 'Sorry, but nothing matched your search criteria. Please try again with some different keywords.', 'aboss' ); ?></p>
								<?php get_search_form(); ?>
							</div><!-- .entry-content -->
						</article><!-- #post-0 -->

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