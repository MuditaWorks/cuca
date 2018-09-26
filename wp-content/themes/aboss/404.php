<?php
/**
 * The template for displaying 404 pages (Not Found)
 *
 * @package WordPress
 * @subpackage aboss_Theme
 * @since aboss 1.0
 */

$aboss_opt = get_option( 'aboss_opt' );

get_header();

?>
	<div class="main-container error404">
		<div class="container">
			<div class="search-form-wrapper">
				<h1><?php esc_html_e( "404", 'aboss' ); ?></h1>
				<h2><?php esc_html_e( "PAGE NOT BE FOUND", 'aboss' ); ?></h2>
				<p class="home-link"><?php esc_html_e( "Sorry but the page you are looking for does not exist, have been removed, name changed or is temporarity unavailable.", 'aboss' ); ?></p>
				<?php get_search_form(); ?>
				<a class="button" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php esc_attr_e( 'Back to home', 'aboss' ); ?>"><?php esc_html_e( 'Back to home page', 'aboss' ); ?></a>
			</div>
		</div> 
	</div>
</div>
<?php get_footer(); ?>