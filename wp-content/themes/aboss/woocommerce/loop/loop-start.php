<?php
/**
 * Product Loop Start
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.3.0
 */
global $wp_query, $woocommerce_loop;

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
		aboss_Class::aboss_shop_class('shop-sidebar');
		$shopcolclass = 9;
		$productcols = 3;
}
$aboss_viewmode = aboss_Class::aboss_show_view_mode();
?> 

<div class="shop-products products <?php echo esc_attr($aboss_viewmode);?> <?php echo esc_attr($shoplayout);?>">