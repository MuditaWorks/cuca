<?php
/**
 * The template for displaying the footer
 *
 * Contains footer content and the closing of the #main and #page div elements.
 *
 * @package WordPress
 * @subpackage Aboss_Theme
 * @since Aboss 1.0
 */
 
$aboss_opt = get_option( 'aboss_opt' );
?>
			<?php if(isset($aboss_opt['footer_layout']) && $aboss_opt['footer_layout']!=''){
				$footer_class = str_replace(' ', '-', strtolower($aboss_opt['footer_layout']));
			} else {
				$footer_class = '';
			} ?>

			<div class="footer <?php echo esc_html($footer_class);?>">
				<?php
				if ( isset($aboss_opt['footer_layout']) && $aboss_opt['footer_layout']!="" ) {

					$jscomposer_templates_args = array(
						'orderby'          => 'title',
						'order'            => 'ASC',
						'post_type'        => 'templatera',
						'post_status'      => 'publish',
						'posts_per_page'   => 30,
					);
					$jscomposer_templates = get_posts( $jscomposer_templates_args );

					if(count($jscomposer_templates) > 0) {
						foreach($jscomposer_templates as $jscomposer_template){
							if($jscomposer_template->post_title == $aboss_opt['footer_layout']){
								echo do_shortcode($jscomposer_template->post_content);
							}
						}
					}
				} else { ?>
					<div class="footer-default widget-copyright copyright">
						<?php echo esc_html('Copyright '.get_bloginfo('name') .date(' Y').'. All Rights Reserved'); ?>
					</div>
				<?php
				}
				?>
			</div>
		</div><!-- .page -->
	</div><!-- .wrapper -->
	<!--<div class="aboss_loading"></div>-->
	<?php if ( isset($aboss_opt['back_to_top']) && $aboss_opt['back_to_top'] ) { ?>
	<div id="back-top" class="hidden-xs hidden-sm hidden-md"></div>
	<?php } ?>
	<?php wp_footer(); ?> 
</body>
</html>