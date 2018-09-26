<?php
/**
 * The Template for displaying all single products.
 *
 * Override this template by copying it to yourtheme/woocommerce/single-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

get_header( 'shop' ); 

$aboss_opt = get_option( 'aboss_opt' );

$shoplayout = 'sidebar';
if(isset($aboss_opt['shop_layout']) && $aboss_opt['shop_layout']!=''){
	$shoplayout = $aboss_opt['shop_layout'];
}
if(isset($_GET['layout']) && $_GET['layout']!=''){
	$shoplayout = $_GET['layout'];
}
$shopsidebar = 'left';
if(isset($aboss_opt['sidebarshop_pos']) && $aboss_opt['sidebarshop_pos']!=''){
	$shopsidebar = $aboss_opt['sidebarshop_pos'];
}
if(isset($_GET['sidebar']) && $_GET['sidebar']!=''){
	$shopsidebar = $_GET['sidebar'];
}
switch($shoplayout) {
	case 'fullwidth':
		aboss_Class::aboss_shop_class('shop-fullwidth');
		$shopcolclass = 12;
		$shopsidebar = 'none';
		$productcols = 4;
		break;
	default:
		aboss_Class::aboss_shop_class('shop-detail');
		$shopcolclass = 9;
		$productcols = 3;
}
?>



<div class="main-container"> 
	<div class="page-content"> 
		<div class="product-page">
			<div class="container">
				<div class="title-breadcrumb"> 
					<div class="title-breadcrumb-inner">  
						<?php do_action( 'woocommerce_before_main_content' ); ?> 
					</div>    
				</div> 
			</div> 
			<div class="product-view">
				<div class="container"> 
					<?php while ( have_posts() ) : the_post(); ?>

						<?php wc_get_template_part( 'content', 'single-product' ); ?>

					<?php endwhile; // end of the loop. ?>

					<?php
						/**
						 * woocommerce_after_main_content hook
						 *
						 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
						 */
						do_action( 'woocommerce_after_main_content' );
					?>

					<?php
						/**
						 * woocommerce_sidebar hook
						 *
						 * @hooked woocommerce_get_sidebar - 10
						 */
						//do_action( 'woocommerce_sidebar' );
					?> 
				</div> 
			</div>   
		</div> 
	</div>
</div>
<?php get_footer( 'shop' ); ?>