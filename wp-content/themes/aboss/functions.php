<?php
/**
 * Aboss functions and definitions
 */

/**
* Require files
*/
	//TGM-Plugin-Activation
require_once( get_template_directory().'/class-tgm-plugin-activation.php' );
	//Init the Redux Framework
if ( class_exists( 'ReduxFramework' ) && !isset( $redux_demo ) && file_exists( get_template_directory().'/theme-config.php' ) ) {
	require_once( get_template_directory().'/theme-config.php' );
}
	// Theme files
if ( !class_exists( 'aboss_widgets' ) && file_exists( get_template_directory().'/include/abosswidgets.php' ) ) {
	require_once( get_template_directory().'/include/abosswidgets.php' );
}
if ( file_exists( get_template_directory().'/include/styleswitcher.php' ) ) {
	require_once( get_template_directory().'/include/styleswitcher.php' );
}
if ( file_exists( get_template_directory().'/include/wooajax.php' ) ) {
	require_once( get_template_directory().'/include/wooajax.php' );
}
if ( file_exists( get_template_directory().'/include/map_shortcodes.php' ) ) {
	require_once( get_template_directory().'/include/map_shortcodes.php' );
}
if ( file_exists( get_template_directory().'/include/shortcodes.php' ) ) {
	require_once( get_template_directory().'/include/shortcodes.php' );
} 

Class Aboss_Class {
	
	/**
	* Global values
	*/
	static function aboss_post_odd_event(){
		global $wp_session;
		
		if(!isset($wp_session["aboss_postcount"])){
			$wp_session["aboss_postcount"] = 0;
		}
		
		$wp_session["aboss_postcount"] = 1 - $wp_session["aboss_postcount"];
		
		return $wp_session["aboss_postcount"];
	}
	static function aboss_post_thumbnail_size($size){
		global $wp_session;
		
		if($size!=''){
			$wp_session["aboss_postthumb"] = $size;
		}
		
		return $wp_session["aboss_postthumb"];
	}
	static function aboss_shop_class($class){
		global $wp_session;
		
		if($class!=''){
			$wp_session["aboss_shopclass"] = $class;
		}
		
		return $wp_session["aboss_shopclass"];
	}
	static function aboss_show_view_mode(){

		$aboss_opt = get_option( 'aboss_opt' );
		
		$aboss_viewmode = 'grid-view'; //default value
		
		if(isset($aboss_opt['default_view'])) {
			$aboss_viewmode = $aboss_opt['default_view'];
		}
		if(isset($_GET['view']) && $_GET['view']!=''){
			$aboss_viewmode = $_GET['view'];
		}
		
		return $aboss_viewmode;
	}
	static function aboss_shortcode_products_count(){
		global $wp_session;
		
		$aboss_productsfound = 0;
		if(isset($wp_session["aboss_productsfound"])){
			$aboss_productsfound = $wp_session["aboss_productsfound"];
		}
		
		return $aboss_productsfound;
	}

	static function aboss_products_count() {
		global $wp_query;

		$aboss_opt = get_option( 'aboss_opt' );
		$perp = $aboss_opt['product_per_page'];
		$max_page = $wp_query->max_num_pages;
		$total = total_product_count();
		$number_products_page = (($perp*$max_page) - $total );
		$current_page = max( 1, get_query_var( 'paged' ) );
		$aboss_products_count =0;
		if ($current_page == $max_page) {
			
			$aboss_products_count = $perp - $number_products_page;
		}
		return $aboss_products_count;
	}
	
	/**
	* Constructor
	*/
	function __construct() {
		// Register action/filter callbacks
		
			//WooCommerce - action/filter
		add_theme_support( 'woocommerce' );
		remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
		remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
		add_filter( 'get_product_search_form', array($this, 'aboss_woo_search_form'));
		add_filter( 'woocommerce_shortcode_products_query', array($this, 'aboss_woocommerce_shortcode_count'));
		add_action( 'woocommerce_share', array($this, 'aboss_woocommerce_social_share'), 35 );
		add_action( 'woocommerce_archive_description', array($this, 'aboss_woocommerce_category_image'), 2 );

		remove_action('woocommerce_after_shop_loop','woocommerce_pagination',10);
		add_action('woocommerce_pagination','woocommerce_pagination',10);

			//move message to top
		remove_action( 'woocommerce_before_shop_loop', 'wc_print_notices', 10 );
		add_action( 'woocommerce_show_message', 'wc_print_notices', 10 );

		//remove add to cart button after item
		remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10); 

		 
		
			//Single product organize   
		remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
		add_action( 'woocommerce_after_single_product_summary' , 'woocommerce_output_product_data_tabs', 15 );

		 
		
			//WooProjects - Project organize
		remove_action( 'projects_before_single_project_summary', 'projects_template_single_title', 10 );
		add_action( 'projects_single_project_summary', 'projects_template_single_title', 5 );
		remove_action( 'projects_before_single_project_summary', 'projects_template_single_short_description', 20 );
		remove_action( 'projects_before_single_project_summary', 'projects_template_single_gallery', 40 );
		add_action( 'projects_single_project_gallery', 'projects_template_single_gallery', 40 );
		
			//WooProjects - projects list
		remove_action( 'projects_loop_item', 'projects_template_loop_project_title', 20 );
		
			//Theme actions
		add_action( 'after_setup_theme', array($this, 'aboss_setup'));
		add_action( 'tgmpa_register', array($this, 'aboss_register_required_plugins')); 
		add_action( 'widgets_init', array($this, 'aboss_override_woocommerce_widgets'), 15 );
		
		add_action( 'wp_enqueue_scripts', array($this, 'aboss_scripts_styles') );
		add_action( 'wp_head', array($this, 'aboss_custom_code_header'));
		add_action( 'widgets_init', array($this, 'aboss_widgets_init'));
		//add_action( 'add_meta_boxes', 'add_aboss_meta_box');
		add_action( 'save_post', array($this, 'aboss_save_meta_box_data'));
		add_action('comment_form_before_fields', array($this, 'aboss_before_comment_fields'));
		add_action('comment_form_after_fields', array($this, 'aboss_after_comment_fields'));
		add_action( 'customize_register', array($this, 'aboss_customize_register'));
		add_action( 'customize_preview_init', array($this, 'aboss_customize_preview_js'));
		add_action('init', array($this, 'aboss_remove_redux_framework_notification'));
		add_action('admin_enqueue_scripts', array($this, 'aboss_admin_style'));
		
			//Theme filters 
		add_filter( 'loop_shop_per_page', array($this, 'aboss_woo_change_per_page'), 20 );
		add_filter( 'woocommerce_output_related_products_args', array($this, 'aboss_woo_related_products_limit'));
		add_filter( 'get_search_form', array($this, 'aboss_search_form'));
		add_filter('excerpt_more', array($this, 'aboss_new_excerpt_more'));
		add_filter( 'excerpt_length', array($this, 'aboss_change_excerpt_length'), 999 );
		add_filter('wp_nav_menu_objects', array($this, 'aboss_first_and_last_menu_class'));
		add_filter( 'wp_page_menu_args', array($this, 'aboss_page_menu_args'));
		add_filter('dynamic_sidebar_params', array($this, 'aboss_widget_first_last_class'));
		add_filter('dynamic_sidebar_params', array($this, 'aboss_mega_menu_widget_change'));
		add_filter( 'dynamic_sidebar_params', array($this, 'aboss_put_widget_content'));
		
		//Adding theme support
		if ( ! isset( $content_width ) ) {
			$content_width = 625;
		}
	}
	
	/**
	* Filter callbacks
	* ----------------
	*/
	 
	// Change products per page
	function aboss_woo_change_per_page() {
		$aboss_opt = get_option( 'aboss_opt' );
		
		return $aboss_opt['product_per_page'];
	}
	//Change number of related products on product page. Set your own value for 'posts_per_page'
	function aboss_woo_related_products_limit( $args ) {
		global $product;

		$aboss_opt = get_option( 'aboss_opt' );

		$args['posts_per_page'] = $aboss_opt['related_amount'];

		return $args;
	}
	// Count number of products from shortcode
	function aboss_woocommerce_shortcode_count( $args ) {
		global $wp_session;
		
		$aboss_productsfound = new WP_Query($args);
		$aboss_productsfound = $aboss_productsfound->post_count;
		
		$wp_session["aboss_productsfound"] = $aboss_productsfound;
		
		return $args;
	}
	//Change search form
	function aboss_search_form( $form ) {
		if(get_search_query()!=''){
			$search_str = get_search_query();
		} else {
			$search_str = esc_html__( 'Search...', 'aboss' );
		}
		
		$form = '<form role="search" method="get" id="blogsearchform" class="searchform" action="' . esc_url(home_url( '/' ) ). '" >
		<div class="form-input">
			<input class="input_text" type="text" value="'.esc_attr($search_str).'" name="s" id="search_input" />
			<button class="button" type="submit" id="blogsearchsubmit"><i class="ion-ios-search-strong"></i></button>
			</div>
		</form>';

		return $form;
	}
	//Change woocommerce search form
	function aboss_woo_search_form( $form ) {
		global $wpdb;
		
		if(get_search_query()!=''){
			$search_str = get_search_query();
		} else {
			$search_str = esc_html__( 'Search product...', 'aboss' );
		}
		
		$form = '<form role="search" method="get" id="searchform" action="'.esc_url( home_url( '/'  ) ).'">';
			$form .= '<div class="form-input">';
				$form .= '<input type="text" value="'.esc_attr($search_str).'" name="s" id="ws" placeholder="'.esc_attr($search_str).'" />';
				$form .= '<button class="btn btn-primary" type="submit" id="wsearchsubmit"><i class="ion-ios-search-strong"></i></button>';
				$form .= '<input type="hidden" name="post_type" value="product" />';
			$form .= '</div>';
		$form .= '</form>';

		return $form;
	}
	// Replaces the excerpt "more" text by a link
	function aboss_new_excerpt_more($more) {
		return '';
	}
	//Change excerpt length
	function aboss_change_excerpt_length( $length ) {
		$aboss_opt = get_option( 'aboss_opt' );
		
		if(isset($aboss_opt['excerpt_length'])){
			return $aboss_opt['excerpt_length'];
		}
		
		return 22;
	}
	//Add 'first, last' class to menu
	function aboss_first_and_last_menu_class($items) {
		$items[1]->classes[] = 'first';
		$items[count($items)]->classes[] = 'last';
		return $items;
	}
	/**
	 * Filter the page menu arguments.
	 *
	 * Makes our wp_nav_menu() fallback -- wp_page_menu() -- show a home link.
	 *
	 * @since Aboss 1.0
	 */
	function aboss_page_menu_args( $args ) {
		if ( ! isset( $args['show_home'] ) )
			$args['show_home'] = true;
		return $args;
	}
	//Add first, last class to widgets
	function aboss_widget_first_last_class($params) {
		global $my_widget_num;
		
		$class = '';
		
		$this_id = $params[0]['id']; // Get the id for the current sidebar we're processing
		$arr_registered_widgets = wp_get_sidebars_widgets(); // Get an array of ALL registered widgets	

		if(!$my_widget_num) {// If the counter array doesn't exist, create it
			$my_widget_num = array();
		}

		if(!isset($arr_registered_widgets[$this_id]) || !is_array($arr_registered_widgets[$this_id])) { // Check if the current sidebar has no widgets
			return $params; // No widgets in this sidebar... bail early.
		}

		if(isset($my_widget_num[$this_id])) { // See if the counter array has an entry for this sidebar
			$my_widget_num[$this_id] ++;
		} else { // If not, create it starting with 1
			$my_widget_num[$this_id] = 1;
		}

		if($my_widget_num[$this_id] == 1) { // If this is the first widget
			$class .= ' widget-first ';
		} elseif($my_widget_num[$this_id] == count($arr_registered_widgets[$this_id])) { // If this is the last widget
			$class .= ' widget-last ';
		}
		
		$params[0]['before_widget'] = str_replace('first_last', ' '.$class.' ', $params[0]['before_widget']);
		
		return $params;
	}
	//Change mega menu widget from div to li tag
	function aboss_mega_menu_widget_change($params) {
		
		$sidebar_id = $params[0]['id'];
		
		$pos = strpos($sidebar_id, '_menu_widgets_area_');
		
		if ( !$pos == false ) {
			$params[0]['before_widget'] = '<li class="widget_mega_menu">'.$params[0]['before_widget'];
			$params[0]['after_widget'] = $params[0]['after_widget'].'</li>';
		}
		
		return $params;
	}
	// Push sidebar widget content into a div
	function aboss_put_widget_content( $params ) {
		global $wp_registered_widgets;

		if( $params[0]['id']=='sidebar-category' ){
			$settings_getter = $wp_registered_widgets[ $params[0]['widget_id'] ]['callback'][0];
			$settings = $settings_getter->get_settings();
			$settings = $settings[ $params[1]['number'] ];
			
			if($params[0]['widget_name']=="Text" && isset($settings['title']) && $settings['text']=="") { // if text widget and no content => don't push content
				return $params;
			}
			if( isset($settings['title']) && $settings['title']!='' ){
				$params[0][ 'after_title' ] .= '<div class="widget_content">';
				$params[0][ 'after_widget' ] = '</div>'.$params[0][ 'after_widget' ];
			} else {
				$params[0][ 'before_widget' ] .= '<div class="widget_content">';
				$params[0][ 'after_widget' ] = '</div>'.$params[0][ 'after_widget' ];
			}
		}
		
		return $params;
	}
	
	/**
	* Action hooks
	* ----------------
	*/
	/**
	 * Aboss setup.
	 *
	 * Sets up theme defaults and registers the various WordPress features that
	 * Aboss supports.
	 *
	 * @uses load_theme_textdomain() For translation/localization support.
	 * @uses add_editor_style() To add a Visual Editor stylesheet.
	 * @uses add_theme_support() To add support for post thumbnails, automatic feed links,
	 * 	custom background, and post formats.
	 * @uses register_nav_menu() To add support for navigation menus.
	 * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
	 *
	 * @since Aboss 1.0
	 */
	function aboss_setup() {
		/*
		 * Makes aboss available for translation.
		 *
		 * Translations can be added to the /languages/ directory.
		 * If you're building a theme based on Aboss, use a find and replace
		 * to change 'aboss' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'aboss', get_template_directory() . '/languages' );

		// This theme styles the visual editor with editor-style.css to match the theme style.
		add_editor_style();

		// Adds RSS feed links to <head> for posts and comments.
		add_theme_support( 'automatic-feed-links' );

		// This theme supports a variety of post formats.
		add_theme_support( 'post-formats', array( 'image', 'gallery', 'video', 'audio' ) );

		// Register menus
		register_nav_menu( 'primary', esc_html__( 'Primary Menu', 'aboss' ) );
		register_nav_menu( 'topmenu', esc_html__( 'Top Menu', 'aboss' ) );
		register_nav_menu( 'mobilemenu', esc_html__( 'Mobile Menu', 'aboss' ) );

		/*
		 * This theme supports custom background color and image,
		 * and here we also set up the default background color.
		 */
		add_theme_support( 'custom-background', array(
			'default-color' => 'e6e6e6',
		) );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );
		
		// This theme uses a custom image size for featured images, displayed on "standard" posts.
		add_theme_support( 'post-thumbnails' );

		set_post_thumbnail_size( 1170, 9999 ); // Unlimited height, soft crop
		add_image_size( 'aboss-category-thumb', 1170, 650, true ); // (cropped)
		add_image_size( 'aboss-post-thumb', 590, 400, true ); // (cropped)
		add_image_size( 'aboss-post-thumbwide', 423, 253, true ); // (cropped)
	}
	//Override woocommerce widgets
	function aboss_override_woocommerce_widgets() {
		//Show mini cart on all pages
		if ( class_exists( 'WC_Widget_Cart' ) ) {
			unregister_widget( 'WC_Widget_Cart' ); 
			include_once( get_template_directory().'/woocommerce/class-wc-widget-cart.php' );
			register_widget( 'Custom_WC_Widget_Cart' );
		}
	}
	// Add image to category description
	function aboss_woocommerce_category_image() {
		if ( is_product_category() ){
			global $wp_query;
			
			$cat = $wp_query->get_queried_object();
			$thumbnail_id = get_woocommerce_term_meta( $cat->term_id, 'thumbnail_id', true );
			$image = wp_get_attachment_url( $thumbnail_id );
			
			if ( $image ) {
				echo '<p class="category-image-desc"><img src="' . esc_url($image) . '" alt=" ' . esc_attr( $cat->name ) . ' " /></p>';
			}
		}
	}
	//Display social sharing on product page
	function aboss_woocommerce_social_share(){
		$aboss_opt = get_option( 'aboss_opt' );
	?>
		<?php if ($aboss_opt['share_code']!='') { ?>
			<div class="share_buttons">
				<?php 
					echo wp_kses($aboss_opt['share_code'], array(
						'div' => array(
							'class' => array()
						),
						'span' => array(
							'class' => array(),
							'displayText' => array()
						),
					));
				?>
			</div>
		<?php } ?>
	<?php
	}
	/**
	 * Enqueue scripts and styles for front-end.
	 *
	 * @since Aboss 1.0
	 */
	function aboss_scripts_styles() {
		global $wp_styles, $wp_scripts;

		$aboss_opt = get_option( 'aboss_opt' );
		
		/*
		 * Adds JavaScript to pages with the comment form to support
		 * sites with threaded comments (when in use).
		*/
		
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) )
			wp_enqueue_script( 'comment-reply' );
		
		// Add Bootstrap JavaScript
		wp_enqueue_script( 'bootstrap', get_template_directory_uri() . '/js/bootstrap.min.js', array('jquery'), '3.2.0', true );
		
		// Add Slick files
		wp_enqueue_script( 'slick', get_template_directory_uri() . '/js/slick/slick.min.js', array('jquery'), '1.6.0', true );
		wp_enqueue_style( 'slick', get_template_directory_uri() . '/js/slick/slick.css', array(), '1.6.0' );
		
		// Add owl-carousel
		// Add owl-carousel file
		wp_enqueue_script( 'owl-carousel', get_template_directory_uri() . '/js/owl.carousel.js', array('jquery'), '1.3.3', true ); 
		wp_enqueue_script( 'owl-carousel-min', get_template_directory_uri() . '/js/owl.carousel.min.js', array('jquery'), '1.3.3', true );

		// Add Chosen js files
		wp_enqueue_script( 'chosen', get_template_directory_uri() . '/js/chosen/chosen.jquery.min.js', array('jquery'), '1.3.0', true );
		wp_enqueue_script( 'chosenproto', get_template_directory_uri() . '/js/chosen/chosen.proto.min.js', array('jquery'), '1.3.0', true );
		wp_enqueue_style( 'chosen', get_template_directory_uri() . '/js/chosen/chosen.min.css', array(), '1.3.0' );
		
		// Add parallax script files
		
		// Add Fancybox
		wp_enqueue_script( 'fancybox', get_template_directory_uri() . '/js/fancybox/jquery.fancybox.pack.js', array('jquery'), '2.1.5', true );
		wp_enqueue_script( 'fancybox-buttons', get_template_directory_uri() . '/js/fancybox/helpers/jquery.fancybox-buttons.js', array('jquery'), '1.0.5', true );
		wp_enqueue_script( 'fancybox-media', get_template_directory_uri() . '/js/fancybox/helpers/jquery.fancybox-media.js', array('jquery'), '1.0.6', true );
		wp_enqueue_script( 'fancybox-thumbs', get_template_directory_uri() . '/js/fancybox/helpers/jquery.fancybox-thumbs.js', array('jquery'), '1.0.7', true );
		wp_enqueue_style( 'fancybox-style', get_template_directory_uri() . '/js/fancybox/jquery.fancybox.css', array(), '2.1.5' );
		wp_enqueue_style( 'fancybox-buttons', get_template_directory_uri() . '/js/fancybox/helpers/jquery.fancybox-buttons.css', array(), '1.0.5' );
		wp_enqueue_style( 'fancybox-thumbs', get_template_directory_uri() . '/js/fancybox/helpers/jquery.fancybox-thumbs.css', array(), '1.0.7' );
		
		//Superfish
		wp_enqueue_script( 'superfish', get_template_directory_uri() . '/js/superfish/superfish.min.js', array('jquery'), '1.3.15', true );
		
		//Add Shuffle js
		wp_enqueue_script( 'modernizr', get_template_directory_uri() . '/js/modernizr.custom.min.js', array('jquery'), '2.6.2', true );
		wp_enqueue_script( 'shuffle', get_template_directory_uri() . '/js/jquery.shuffle.min.js', array('jquery'), '3.0.0', true );

		//Add mousewheel
		wp_enqueue_script( 'mousewheel', get_template_directory_uri() . '/js/jquery.mousewheel.min.js', array('jquery'), '3.1.12', true );
		
		// Add jQuery countdown file
		wp_enqueue_script( 'countdown', get_template_directory_uri() . '/js/jquery.countdown.min.js', array('jquery'), '2.0.4', true );
		
		// Add jQuery counter files
		wp_enqueue_script( 'waypoints', get_template_directory_uri() . '/js/waypoints.min.js', array('jquery'), '1.0', true );
		wp_enqueue_script( 'counterup', get_template_directory_uri() . '/js/jquery.counterup.min.js', array('jquery'), '1.0', true );
		
		// Add jQuery TimeCircles
		wp_enqueue_script( 'timecircle', get_template_directory_uri() . '/js/TimeCircles.js', array(), true );
		
		// Add variables.js file
		wp_enqueue_script( 'variables', get_template_directory_uri() . '/js/variables.js', array('jquery'), '20140826', true );
		
		// Add theme.js file
		wp_enqueue_script( 'aboss-theme-jquery', get_template_directory_uri() . '/js/aboss-theme.js', array('jquery'), '20140826', true );

		$font_url = $this->aboss_get_font_url();
		if ( ! empty( $font_url ) )
			wp_enqueue_style( 'aboss-fonts', esc_url_raw( $font_url ), array(), null );

		// Loads our main stylesheet.
		wp_enqueue_style( 'aboss-style', get_stylesheet_uri() );
		
		// Mega Main Menu
		wp_enqueue_style( 'megamenu-style', get_template_directory_uri() . '/css/megamenu_style.css', array(), '2.0.4' );
	
		// Load fontawesome css
		wp_enqueue_style( 'fontawesome', get_template_directory_uri() . '/css/font-awesome.min.css', array(), '4.2.0' );

		// Load Ionicons css
		wp_enqueue_style( 'ionicons', get_template_directory_uri() . '/css/ionicons.css', array(), '2.0.1' );
		 
		// Load Ionicons css
		wp_enqueue_style( 'strokegapicons', get_template_directory_uri() . '/css/stroke-gap-icons.css', array(), '1.0.0' );
		 		 
		 
		// Load animate css
		wp_enqueue_style( 'animate', get_template_directory_uri() . '/css/animate.css', array());
		
		// Load owl-carousel css
		wp_enqueue_style( 'owl-carousel', get_template_directory_uri() . '/css/owl.carousel.css', array(), '1.3.3');
		
		// Load bootstrap css
		wp_enqueue_style( 'bootstrap', get_template_directory_uri() . '/css/bootstrap.min.css', array(), '3.2.0' );
				
		// Compile Less to CSS
		$previewpreset = (isset($_REQUEST['preset']) ? $_REQUEST['preset'] : null);
			//get preset from url (only for demo/preview)
		if($previewpreset){
			$_SESSION["preset"] = $previewpreset;
		}
		$presetopt = 1;
		if(!isset($_SESSION["preset"])){
			$_SESSION["preset"] = 1;
		}
		if($_SESSION["preset"] != 1) {
			$presetopt = $_SESSION["preset"];
		} else { /* if no preset varialbe found in url, use from theme options */
			if(isset($aboss_opt['preset_option'])){
				$presetopt = $aboss_opt['preset_option'];
			}
		}
		if(!isset($presetopt)) $presetopt = 1; /* in case first time install theme, no options found */
		
		if(isset($aboss_opt['enable_less'])){
			if($aboss_opt['enable_less']){
				$themevariables = array(
					'body_font'=> $aboss_opt['bodyfont']['font-family'],
					'text_color'=> $aboss_opt['bodyfont']['color'],
					'text_selected_bg' => $aboss_opt['text_selected_bg'],
					'text_selected_color' => $aboss_opt['text_selected_color'],
					'text_size'=> $aboss_opt['bodyfont']['font-size'],
					'border_color'=> $aboss_opt['border_color']['border-color'],
					
					'heading_font'=> $aboss_opt['headingfont']['font-family'],
					'heading_color'=> $aboss_opt['headingfont']['color'],
					'heading_font_weight'=> $aboss_opt['headingfont']['font-weight'],  
					
					'menu_font'=> $aboss_opt['menufont']['font-family'],
					'menu_color'=> $aboss_opt['menufont']['color'],
					'menu_font_size'=> $aboss_opt['menufont']['font-size'],
					'menu_font_weight'=> $aboss_opt['menufont']['font-weight'],
					'sub_menu_bg' => $aboss_opt['sub_menu_bg'],
					'sub_menu_color' => $aboss_opt['sub_menu_color'],

					'vmenu_font'=> $aboss_opt['vmenufont']['font-family'],
					'vmenu_color'=> $aboss_opt['vmenufont']['color'],
					'vmenu_font_size'=> $aboss_opt['vmenufont']['font-size'],
					'vmenu_font_weight'=> $aboss_opt['vmenufont']['font-weight'],
					'vsub_menu_bg' => $aboss_opt['vsub_menu_bg'],

					'price_font'=> $aboss_opt['pricefont']['font-family'],
					'price_color'=> $aboss_opt['pricefont']['color'], 
					'price_size'=> $aboss_opt['pricefont']['font-size'],
					'price_font_weight'=> $aboss_opt['pricefont']['font-weight'],
					
					'link_color' => $aboss_opt['link_color']['regular'],
					'link_hover_color' => $aboss_opt['link_color']['hover'],
					'link_active_color' => $aboss_opt['link_color']['active'],
					
					'primary_color' => $aboss_opt['primary_color'],
					
					'sale_color' => $aboss_opt['sale_color'],
					'saletext_color' => $aboss_opt['saletext_color'],
					'rate_color' => $aboss_opt['rate_color'],

					'topbar_color' => $aboss_opt['topbar_color'],
					'topbar_link_color' => $aboss_opt['topbar_link_color']['regular'],
					'topbar_link_hover_color' => $aboss_opt['topbar_link_color']['hover'],
					'topbar_link_active_color' => $aboss_opt['topbar_link_color']['active'],

					'header_color' => $aboss_opt['header_color'],
					'header_link_color' => $aboss_opt['header_link_color']['regular'],
					'header_link_hover_color' => $aboss_opt['header_link_color']['hover'],
					'header_link_active_color' => $aboss_opt['header_link_color']['active'],


					'footer_color' => $aboss_opt['footer_color'],
					'footer_link_color' => $aboss_opt['footer_link_color']['regular'],
					'footer_link_hover_color' => $aboss_opt['footer_link_color']['hover'],
					'footer_link_active_color' => $aboss_opt['footer_link_color']['active'],
				);
				
				if(isset($aboss_opt['header_sticky_bg']['rgba']) && $aboss_opt['header_sticky_bg']['rgba']!="") {
					$themevariables['header_sticky_bg'] = $aboss_opt['header_sticky_bg']['rgba'];
				} else {
					$themevariables['header_sticky_bg'] = 'rgba(54,63,77,0.95)';
				}
				if(isset($aboss_opt['header_bg']['background-color']) && $aboss_opt['header_bg']['background-color']!="") {
					$themevariables['header_bg'] = $aboss_opt['header_bg']['background-color'];
				} else {
					$themevariables['header_bg'] = '#ffffff';
				}
				if(isset($aboss_opt['header_bg']['background-image']) && $aboss_opt['header_bg']['background-image']!="") {
					$themevariables['header_bg_image'] = $aboss_opt['header_bg']['background-image'];
				} else {
					$themevariables['header_bg_image'] = 'none';
				}
				if(isset($aboss_opt['footer_bg']['background-color']) && $aboss_opt['footer_bg']['background-color']!="") {
					$themevariables['footer_bg'] = $aboss_opt['footer_bg']['background-color'];
				} else {
					$themevariables['footer_bg'] = '#ffffff';
				}
				 
				switch ($presetopt) { 
					case 2: 
						$themevariables['primary_color'] = '#dd3a3f';  
						$themevariables['header_bg'] = '#fff'; 						
						$themevariables['rate_color'] = '#dd3a3f'; 						
						$themevariables['topbar_color'] = '#363f4d'; 
						$themevariables['topbar_link_color'] = '#dd3a3f';
						$themevariables['header_color'] = '#ffffff';   
						$themevariables['header_link_color'] = '#dd3a3f';
						$themevariables['link_hover_color'] = '#dd3a3f';
						$themevariables['menu_color'] = '#fff'; 						
					break;  
					case 3:   
						$themevariables['primary_color'] = '#ba9f74';  
						$themevariables['rate_color'] = '#ba9f74'; 	
						$themevariables['header_bg'] = '#fff';
						$themevariables['menu_color'] = '#fff'; 						
						$themevariables['topbar_color'] = '#363f4d'; 
						$themevariables['topbar_link_color'] = '#ba9f74';
						$themevariables['header_color'] = '#ffffff';   
						$themevariables['header_link_color'] = '#363f4d'; 
						$themevariables['link_hover_color'] = '#ba9f74';     		
						$themevariables['header_sticky_bg'] = 'rgba(255,255,255,0.95)';   
					break;   
					case 4: 
						$themevariables['primary_color'] = '#a8ca01';  
						$themevariables['header_bg'] = '#68b501';
						$themevariables['menu_color'] = '#292929'; 	
						$themevariables['header_color'] = '#292929';   
						$themevariables['header_link_color'] = '#292929';     
						$themevariables['price_color'] = '#292929';  
						$themevariables['footer_link_hover_color'] = '#68b501';   
						$themevariables['footer_link_active_color'] = '#68b501';      
					break;  
					case 5: 
						$themevariables['primary_color'] = '#68b501';  
						$themevariables['header_bg'] = '#fff';
						$themevariables['topbar_color'] = '#363f4d'; 
						$themevariables['topbar_link_color'] = '#363f4d';
						$themevariables['header_color'] = '#363f4d';   
						$themevariables['header_link_color'] = '#363f4d';   
						$themevariables['price_color'] = '#68b501';  
						$themevariables['footer_link_hover_color'] = '#68b501';   
						$themevariables['footer_link_active_color'] = '#68b501';      
					break;  
					case 6:  
						$themevariables['primary_color'] = '#f1b306';  
						$themevariables['header_bg'] = '#20252d';
						$themevariables['price_color'] = '#f1b306';  
						$themevariables['footer_link_hover_color'] = '#f1b306';   
						$themevariables['footer_link_active_color'] = '#f1b306';  
					break;   
				}

				if(function_exists('compileLessFile')){
					compileLessFile('reset.less', 'reset'.$presetopt.'.css', $themevariables);
					compileLessFile('global.less', 'global'.$presetopt.'.css', $themevariables);
					compileLessFile('pages.less', 'pages'.$presetopt.'.css', $themevariables);
					compileLessFile('woocommerce.less', 'woocommerce'.$presetopt.'.css', $themevariables);
					compileLessFile('layouts.less', 'layouts'.$presetopt.'.css', $themevariables);
					compileLessFile('responsive.less', 'responsive'.$presetopt.'.css', $themevariables);
				}
			}
		}
		
		// Load main theme css style files
		wp_enqueue_style( 'aboss-reset', get_template_directory_uri() . '/css/reset'.$presetopt.'.css', array('bootstrap'), '1.0.0' );
		wp_enqueue_style( 'aboss-global', get_template_directory_uri() . '/css/global'.$presetopt.'.css', array('aboss-reset'), '1.0.0' );
		wp_enqueue_style( 'aboss-pages', get_template_directory_uri() . '/css/pages'.$presetopt.'.css', array('aboss-global'), '1.0.0' );
		wp_enqueue_style( 'aboss-woocommerce', get_template_directory_uri() . '/css/woocommerce'.$presetopt.'.css', array('aboss-pages'), '1.0.0' );
		wp_enqueue_style( 'aboss-layouts', get_template_directory_uri() . '/css/layouts'.$presetopt.'.css', array('aboss-woocommerce'), '1.0.0' );
		wp_enqueue_style( 'aboss-responsive', get_template_directory_uri() . '/css/responsive'.$presetopt.'.css', array('aboss-layouts'), '1.0.0' );
		wp_enqueue_style( 'aboss-custom', get_template_directory_uri() . '/css/opt_css.css', array('aboss-responsive'), '1.0.0' );
		
		if(function_exists('WP_Filesystem')){
			if ( ! WP_Filesystem() ) {
				$url = wp_nonce_url();
				request_filesystem_credentials($url, '', true, false, null);
			}
			
			global $wp_filesystem;
			//add custom css, sharing code to header
			if($wp_filesystem->exists(get_template_directory(). '/css/opt_css.css')){
				$customcss = $wp_filesystem->get_contents(get_template_directory(). '/css/opt_css.css');
				
				if(isset($aboss_opt['custom_css']) && $customcss!=$aboss_opt['custom_css']){ //if new update, write file content
					$wp_filesystem->put_contents(
						get_template_directory(). '/css/opt_css.css',
						$aboss_opt['custom_css'],
						FS_CHMOD_FILE // predefined mode settings for WP files
					);
				}
			} else {
				$wp_filesystem->put_contents(
					get_template_directory(). '/css/opt_css.css',
					$aboss_opt['custom_css'],
					FS_CHMOD_FILE // predefined mode settings for WP files
				);
			}
		}
		
		if(isset($aboss_opt['enable_sswitcher'])){
			if($aboss_opt['enable_sswitcher']){
				// Add styleswitcher.js file
				wp_enqueue_script( 'styleswitcher', get_template_directory_uri() . '/js/styleswitcher.js', array(), '20140826', true );
				// Load styleswitcher css style
				wp_enqueue_style( 'styleswitcher-style', get_template_directory_uri() . '/css/styleswitcher.css', array(), '1.0.0' );
				// Load scroll bar js
				wp_enqueue_script( 'scrollbar', get_template_directory_uri() . '/js/jquery.scrollbar.min.js', array('jquery'), '0.2.8', true );
				wp_enqueue_style( 'scrollbar-style', get_template_directory_uri() . '/css/scrollbar.css', array(), '1.0.0' );
			}
		}
		
		//add javascript variables
		ob_start(); ?>
		"use strict";
		var aboss_brandnumber = <?php if(isset($aboss_opt['brandnumber'])) { echo esc_js($aboss_opt['brandnumber']); } else { echo '6'; } ?>,
			aboss_brandscrollnumber = <?php if(isset($aboss_opt['brandscrollnumber'])) { echo esc_js($aboss_opt['brandscrollnumber']); } else { echo '2';} ?>,
			aboss_brandpause = <?php if(isset($aboss_opt['brandpause'])) { echo esc_js($aboss_opt['brandpause']); } else { echo '3000'; } ?>,
			aboss_brandanimate = <?php if(isset($aboss_opt['brandanimate'])) { echo esc_js($aboss_opt['brandanimate']); } else { echo '700';} ?>;
		var aboss_brandscroll = false;
			<?php if(isset($aboss_opt['brandscroll'])){ ?>
				aboss_brandscroll = <?php echo esc_js($aboss_opt['brandscroll'])==1 ? 'true': 'false'; ?>;
			<?php } ?>
		var aboss_categoriesnumber = <?php if(isset($aboss_opt['categoriesnumber'])) { echo esc_js($aboss_opt['categoriesnumber']); } else { echo '6'; } ?>,
			aboss_categoriesscrollnumber = <?php if(isset($aboss_opt['categoriesscrollnumber'])) { echo esc_js($aboss_opt['categoriesscrollnumber']); } else { echo '2';} ?>,
			aboss_categoriespause = <?php if(isset($aboss_opt['categoriespause'])) { echo esc_js($aboss_opt['categoriespause']); } else { echo '3000'; } ?>,
			aboss_categoriesanimate = <?php if(isset($aboss_opt['categoriesanimate'])) { echo esc_js($aboss_opt['categoriesanimate']); } else { echo '700';} ?>;
		var aboss_categoriesscroll = false;
			<?php if(isset($aboss_opt['categoriesscroll'])){ ?>
				aboss_categoriesscroll = <?php echo esc_js($aboss_opt['categoriesscroll'])==1 ? 'true': 'false'; ?>;
			<?php } ?>
		var aboss_blogpause = <?php if(isset($aboss_opt['blogpause'])) { echo esc_js($aboss_opt['blogpause']); } else { echo '3000'; } ?>,
			aboss_blabossmate = <?php if(isset($aboss_opt['blabossmate'])) { echo esc_js($aboss_opt['blabossmate']); } else { echo '700'; } ?>;
		var aboss_blogscroll = false;
			<?php if(isset($aboss_opt['blogscroll'])){ ?>
				aboss_blogscroll = <?php echo esc_js($aboss_opt['blogscroll'])==1 ? 'true': 'false'; ?>;
			<?php } ?>
		var aboss_testipause = <?php if(isset($aboss_opt['testipause'])) { echo esc_js($aboss_opt['testipause']); } else { echo '3000'; } ?>,
			aboss_testianimate = <?php if(isset($aboss_opt['testianimate'])) { echo esc_js($aboss_opt['testianimate']); } else { echo '700'; } ?>;
		var aboss_testiscroll = false; 
			<?php if(isset($aboss_opt['testiscroll'])){ ?>
				aboss_testiscroll = <?php echo esc_js($aboss_opt['testiscroll'])==1 ? 'true': 'false'; ?>;
			<?php } ?>
		var aboss_catenumber = <?php if(isset($aboss_opt['catenumber'])) { echo esc_js($aboss_opt['catenumber']); } else { echo '6'; } ?>,
			aboss_catescrollnumber = <?php if(isset($aboss_opt['catescrollnumber'])) { echo esc_js($aboss_opt['catescrollnumber']); } else { echo '2';} ?>,
			aboss_catepause = <?php if(isset($aboss_opt['catepause'])) { echo esc_js($aboss_opt['catepause']); } else { echo '3000'; } ?>,
			aboss_cateanimate = <?php if(isset($aboss_opt['cateanimate'])) { echo esc_js($aboss_opt['cateanimate']); } else { echo '700';} ?>;
		var aboss_catescroll = false;
			<?php if(isset($aboss_opt['catescroll'])){ ?>
				aboss_catescroll = <?php echo esc_js($aboss_opt['catescroll'])==1 ? 'true': 'false'; ?>;
			<?php } ?>
		var aboss_menu_number = <?php if(isset($aboss_opt['categories_menu_items'])) { echo esc_js((int)$aboss_opt['categories_menu_items']+1); } else { echo '9';} ?>; 

		var aboss_sticky_header = false;
			<?php if(isset($aboss_opt['sticky_header'])){ ?>
				aboss_sticky_header = <?php echo esc_js($aboss_opt['sticky_header'])==1 ? 'true': 'false'; ?>;
			<?php } ?>

		var aboss_item_first = <?php if(isset($aboss_opt['item_first'])) { echo esc_js($aboss_opt['item_first']); } else { echo '12'; } ?>,
			aboss_moreless_products = <?php if(isset($aboss_opt['moreless_products'])) { echo esc_js($aboss_opt['moreless_products']); } else { echo '4'; } ?>;

		jQuery(document).ready(function(){
			jQuery("#ws").focus(function(){
				if(jQuery(this).val()=="<?php echo esc_html__( 'Search product...', 'aboss' )?>"){
					jQuery(this).val("");
				}
			});
			jQuery("#ws").focusout(function(){
				if(jQuery(this).val()==""){
					jQuery(this).val("<?php echo esc_html__( 'Search product...', 'aboss' )?>");
				}
			});
			jQuery("#wsearchsubmit").click(function(){
				if(jQuery("#ws").val()=="<?php echo esc_html__( 'Search product...', 'aboss' )?>" || jQuery("#ws").val()==""){
					jQuery("#ws").focus();
					return false;
				}
			});
			jQuery("#search_input").focus(function(){
				if(jQuery(this).val()=="<?php echo esc_html__( 'Search...', 'aboss' )?>"){
					jQuery(this).val("");
				}
			});
			jQuery("#search_input").focusout(function(){
				if(jQuery(this).val()==""){
					jQuery(this).val("<?php echo esc_html__( 'Search...', 'aboss' )?>");
				}
			});
			jQuery("#blogsearchsubmit").click(function(){
				if(jQuery("#search_input").val()=="<?php echo esc_html__( 'Search...', 'aboss' )?>" || jQuery("#search_input").val()==""){
					jQuery("#search_input").focus();
					return false;
				}
			});
		});
		<?php
		$jsvars = ob_get_contents();
		ob_end_clean();
		
		if(function_exists('WP_Filesystem')){
			if($wp_filesystem->exists(get_template_directory(). '/js/variables.js')){
				$jsvariables = $wp_filesystem->get_contents(get_template_directory(). '/js/variables.js');
				
				if($jsvars!=$jsvariables){ //if new update, write file content
					$wp_filesystem->put_contents(
						get_template_directory(). '/js/variables.js',
						$jsvars,
						FS_CHMOD_FILE // predefined mode settings for WP files
					);
				}
			} else {
				$wp_filesystem->put_contents(
					get_template_directory(). '/js/variables.js',
					$jsvars,
					FS_CHMOD_FILE // predefined mode settings for WP files
				);
			}
		}
		//add css for footer, header templates
		$jscomposer_templates_args = array(
			'orderby'          => 'title',
			'order'            => 'ASC',
			'post_type'        => 'templatera',
			'post_status'      => 'publish',
			'posts_per_page'   => 100,
		);
		$jscomposer_templates = get_posts( $jscomposer_templates_args );

		if(count($jscomposer_templates) > 0) {
			foreach($jscomposer_templates as $jscomposer_template){
				if($jscomposer_template->post_title == $aboss_opt['header_layout'] || $jscomposer_template->post_title == $aboss_opt['footer_layout']){
					$jscomposer_template_css = get_post_meta ( $jscomposer_template->ID, '_wpb_shortcodes_custom_css', false );
					if(isset($jscomposer_template_css[0])) {
						wp_add_inline_style( 'aboss-custom', $jscomposer_template_css[0] );
					} 
				}
			}
		}
		
		//page width
		wp_add_inline_style( 'aboss-css-custom', '.wrapper.box-layout, .wrapper.box-layout .container, .wrapper.box-layout .row-container {max-width: '.$aboss_opt['box_layout_width'].'px;}' );
	}
	
	//add sharing code to header
	function aboss_custom_code_header() {
		global $aboss_opt;

		if ( isset($aboss_opt['share_head_code']) && $aboss_opt['share_head_code']!='') {
			echo wp_kses($aboss_opt['share_head_code'], array(
				'script' => array(
					'type' => array(),
					'src' => array(),
					'async' => array()
				),
			));
		}
	}
	/**
	 * Register sidebars.
	 *
	 * Registers our main widget area and the front page widget areas.
	 *
	 * @since Aboss 1.0
	 */
	function aboss_widgets_init() {
		$aboss_opt = get_option( 'aboss_opt' );
		
		register_sidebar( array(
			'name' => esc_html__( 'Blog Sidebar', 'aboss' ),
			'id' => 'sidebar-1',
			'description' => esc_html__( 'Sidebar on blog page', 'aboss' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget' => '</aside>',
			'before_title' => '<h3 class="widget-title"><span>',
			'after_title' => '</span></h3>',
		) );
		
		register_sidebar( array(
			'name' => esc_html__( 'Shop Sidebar', 'aboss' ),
			'id' => 'sidebar-shop', 
			'description' => esc_html__( 'Sidebar on shop page (only sidebar shop layout)', 'aboss' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget' => '</aside>',
			'before_title' => '<h3 class="widget-title"><span>',
			'after_title' => '</span></h3>',
		) );
		register_sidebar( array(
			'name' => esc_html__( 'Pages Sidebar', 'aboss' ),
			'id' => 'sidebar-page',
			'description' => esc_html__( 'Sidebar on content pages', 'aboss' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget' => '</aside>',
			'before_title' => '<h3 class="widget-title"><span>',
			'after_title' => '</span></h3>',
		) );
		
		if(isset($aboss_opt['custom-sidebars']) && $aboss_opt['custom-sidebars']!=""){
			foreach($aboss_opt['custom-sidebars'] as $sidebar){
				$sidebar_id = str_replace(' ', '-', strtolower($sidebar));
				
				if($sidebar_id!='') {
					register_sidebar( array(
						'name' => $sidebar,
						'id' => $sidebar_id,
						'description' => $sidebar,
						'before_widget' => '<aside id="%1$s" class="widget %2$s">',
						'after_widget' => '</aside>',
						'before_title' => '<h3 class="widget-title"><span>',
						'after_title' => '</span></h3>',
					) );
				}
			}
		}
	}
	static function aboss_meta_box_callback( $post ) {

		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'aboss_meta_box', 'aboss_meta_box_nonce' );

		/*
		 * Use get_post_meta() to retrieve an existing value
		 * from the database and use the value for the form.
		 */
		$value = get_post_meta( $post->ID, '_aboss_post_intro_value', true );
		echo '<label for="aboss_post_intro">';
		esc_html_e( 'This content will be used to replace the featured image, use shortcode here', 'aboss' );
		echo '</label><br />';
		wp_editor( $value, 'aboss_post_intro', $settings = array() );
	}
	static function aboss_custom_sidebar_callback( $post ) {
		global $wp_registered_sidebars;

		$aboss_opt = get_option( 'aboss_opt' );

		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'aboss_meta_box', 'aboss_meta_box_nonce' );

		/*
		 * Use get_post_meta() to retrieve an existing value
		 * from the database and use the value for the form.
		 */

		//show sidebar dropdown select
		$csidebar = get_post_meta( $post->ID, '_aboss_custom_sidebar', true );

		echo '<label for="aboss_custom_sidebar">';
		esc_html_e( 'Select a custom sidebar for this post/page', 'aboss' );
		echo '</label><br />';

		echo '<select id="aboss_custom_sidebar" name="aboss_custom_sidebar">';
			echo '<option value="">'.esc_html__('- None -', 'aboss').'</option>';
			foreach($wp_registered_sidebars as $sidebar){
				$sidebar_id = $sidebar['id'];
				if($csidebar == $sidebar_id){
					echo '<option value="'.$sidebar_id.'" selected="selected">'.$sidebar['name'].'</option>';
				} else {
					echo '<option value="'.$sidebar_id.'">'.$sidebar['name'].'</option>';
				}
			}
		echo '</select><br />';

		//show custom sidebar position
		$csidebarpos = get_post_meta( $post->ID, '_aboss_custom_sidebar_pos', true );

		echo '<label for="aboss_custom_sidebar_pos">';
		esc_html_e( 'Sidebar position', 'aboss' );
		echo '</label><br />';

		echo '<select id="aboss_custom_sidebar_pos" name="aboss_custom_sidebar_pos">'; ?>
			<option value="left" <?php if($csidebarpos == 'left') {echo 'selected="selected"';}?>><?php echo esc_html__('Left', 'aboss'); ?></option>
			<option value="right" <?php if($csidebarpos == 'right') {echo 'selected="selected"';}?>><?php echo esc_html__('Right', 'aboss'); ?></option>
		<?php echo '</select>';
	}

	function add_aboss_meta_box() {

		// $screens = array( 'post', 'page' );

		// foreach ( $screens as $screen ) {
		// 	if($screen == 'post'){
		// 		add_meta_box(
		// 			'aboss_post_intro_section',
		// 			esc_html__( 'Post featured content', 'aboss' ),
		// 			'Aboss_Class::aboss_meta_box_callback',
		// 			$screen
		// 		);
		// 		add_meta_box(
		// 			'aboss_custom_sidebar',
		// 			esc_html__( 'Custom Sidebar', 'aboss' ),
		// 			'Aboss_Class::aboss_custom_sidebar_callback',
		// 			$screen
		// 		);
		// 	}
		// 	if($screen == 'page'){
		// 		add_meta_box(
		// 			'aboss_custom_sidebar',
		// 			esc_html__( 'Custom Sidebar', 'aboss' ),
		// 			'Aboss_Class::aboss_custom_sidebar_callback',
		// 			$screen
		// 		);
		// 	}
		// }
	}
	function aboss_save_meta_box_data( $post_id ) {

		/*
		 * We need to verify this came from our screen and with proper authorization,
		 * because the save_post action can be triggered at other times.
		 */

		// Check if our nonce is set.
		if ( ! isset( $_POST['aboss_meta_box_nonce'] ) ) {
			return;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST['aboss_meta_box_nonce'], 'aboss_meta_box' ) ) {
			return;
		}

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check the user's permissions.
		if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return;
			}

		} else {

			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
		}

		/* OK, it's safe for us to save the data now. */
		
		// Make sure that it is set.
		if ( ! ( isset( $_POST['aboss_post_intro'] ) || isset( $_POST['aboss_custom_sidebar'] ) ) )  {
			return;
		}

		// Sanitize user input.
		$my_data = sanitize_text_field( $_POST['aboss_post_intro'] );
		// Update the meta field in the database.
		update_post_meta( $post_id, '_aboss_post_intro_value', $my_data );

		// Sanitize user input.
		$my_data = sanitize_text_field( $_POST['aboss_custom_sidebar'] );
		// Update the meta field in the database.
		update_post_meta( $post_id, '_aboss_custom_sidebar', $my_data );

		// Sanitize user input.
		$my_data = sanitize_text_field( $_POST['aboss_custom_sidebar_pos'] );
		// Update the meta field in the database.
		update_post_meta( $post_id, '_aboss_custom_sidebar_pos', $my_data );
		
		return;
	}
	//Change comment form
	function aboss_before_comment_fields() {
		echo '<div class="comment-input">';
	}
	function aboss_after_comment_fields() {
		echo '</div>';
	}
	/**
	 * Register postMessage support.
	 *
	 * Add postMessage support for site title and description for the Customizer.
	 *
	 * @since Aboss 1.0
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer object.
	 */
	function aboss_customize_register( $wp_customize ) {
		$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
		$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
		$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';
	}
	/**
	 * Enqueue Javascript postMessage handlers for the Customizer.
	 *
	 * Binds JS handlers to make the Customizer preview reload changes asynchronously.
	 *
	 * @since Aboss 1.0
	 */
	function aboss_customize_preview_js() {
		wp_enqueue_script( 'aboss-customizer', get_template_directory_uri() . '/js/theme-customizer.js', array( 'customize-preview' ), '20130301', true );
	}
	// Remove Redux Ads, Notification
	function aboss_remove_redux_framework_notification() {
		if ( class_exists('ReduxFrameworkPlugin') ) {
			remove_filter( 'plugin_row_meta', array( ReduxFrameworkPlugin::get_instance(), 'plugin_metalinks'), null, 2 );
		}
		if ( class_exists('ReduxFrameworkPlugin') ) {
			remove_action('admin_notices', array( ReduxFrameworkPlugin::get_instance(), 'admin_notices' ) );    
		}
	}
	function aboss_admin_style() {
	  wp_enqueue_style('admin-styles', get_template_directory_uri().'/css/admin.css');
	}
	/**
	* Utility methods
	* ---------------
	*/
	
	//Add breadcrumbs
	static function aboss_breadcrumb() {
		global $post;

		$aboss_opt = get_option( 'aboss_opt' );
		
		$brseparator = '<span class="separator">></span>';
		if (!is_home()) {
			echo '<div class="breadcrumbs">';
			
			echo '<a href="';
			echo esc_url( home_url( '/' ));
			echo '">';
			echo esc_html__('Home', 'aboss');
			echo '</a>'.$brseparator;
			if (is_category() || is_single()) {
				$categories = get_the_category();
				if ( count( $categories ) > 0 ) {
					echo '<a href="' . esc_url( get_category_link( $categories[0]->term_id ) ) . '">' . esc_html( $categories[0]->name ) . '</a>';
				}
				if (is_single()) {
					if ( count( $categories ) > 0 ) { echo ''.$brseparator; }
					the_title();
				}
			} elseif (is_page()) {
				if($post->post_parent){
					$anc = get_post_ancestors( $post->ID );
					$title = get_the_title();
					foreach ( $anc as $ancestor ) {
						$output = '<a href="'.get_permalink($ancestor).'" title="'.get_the_title($ancestor).'">'.get_the_title($ancestor).'</a>'.$brseparator;
					}
					echo wp_kses($output, array(
							'a'=>array(
								'href' => array(),
								'title' => array()
							),
							'span'=>array(
								'class'=>array()
							)
						)
					);
					echo '<span title="'.$title.'"> '.$title.'</span>';
				} else {
					echo '<span> '.get_the_title().'</span>';
				}
			}
			elseif (is_tag()) {single_tag_title();}
			elseif (is_day()) {printf( esc_html__( 'Archive for: %s', 'aboss' ), '<span>' . get_the_date() . '</span>' );}
			elseif (is_month()) {printf( esc_html__( 'Archive for: %s', 'aboss' ), '<span>' . get_the_date( _x( 'F Y', 'monthly archives date format', 'aboss' ) ) . '</span>' );}
			elseif (is_year()) {printf( esc_html__( 'Archive for: %s', 'aboss' ), '<span>' . get_the_date( _x( 'Y', 'yearly archives date format', 'aboss' ) ) . '</span>' );}
			elseif (is_author()) {echo "<span>".esc_html__('Archive for','aboss'); echo'</span>';}
			elseif (isset($_GET['paged']) && !empty($_GET['paged'])) {echo "<span>".esc_html__('Blog Archives','aboss'); echo'</span>';}
			elseif (is_search()) {echo "<span>".esc_html__('Search Results','aboss'); echo'</span>';}
			
			echo '</div>';
		} else {
			echo '<div class="breadcrumbs">';
			
			echo '<a href="';
			echo esc_url( home_url( '/' ) );
			echo '">';
			echo esc_html__('Home', 'aboss');
			echo '</a>'.$brseparator;
			
			if(isset($aboss_opt['blog_header_text']) && $aboss_opt['blog_header_text']!=""){
				echo esc_html($aboss_opt['blog_header_text']);
			} else {
				echo esc_html__('Blog', 'aboss');
			}
			
			echo '</div>';
		}
	}
	static function aboss_limitStringByWord ($string, $maxlength, $suffix = '') {

		if(function_exists( 'mb_strlen' )) {
			// use multibyte functions by Iysov
			if(mb_strlen( $string )<=$maxlength) return $string;
			$string = mb_substr( $string, 0, $maxlength );
			$index = mb_strrpos( $string, ' ' );
			if($index === FALSE) {
				return $string;
			} else {
				return mb_substr( $string, 0, $index ).$suffix;
			}
		} else { // original code here
			if(strlen( $string )<=$maxlength) return $string;
			$string = substr( $string, 0, $maxlength );
			$index = strrpos( $string, ' ' );
			if($index === FALSE) {
				return $string;
			} else {
				return substr( $string, 0, $index ).$suffix;
			}
		}
	}
	static function aboss_excerpt_by_id($post, $length = 10, $tags = '<a><em><strong>') {
 
		if(is_int($post)) {
			// get the post object of the passed ID
			$post = get_post($post);
		} elseif(!is_object($post)) {
			return false;
		}
	 
		if(has_excerpt($post->ID)) {
			$the_excerpt = $post->post_excerpt;
			return apply_filters('the_content', $the_excerpt);
		} else {
			$the_excerpt = $post->post_content;
		}
	 
		$the_excerpt = strip_shortcodes(strip_tags($the_excerpt), $tags);
		$the_excerpt = preg_split('/\b/', $the_excerpt, $length * 2+1);
		$excerpt_waste = array_pop($the_excerpt);
		$the_excerpt = implode($the_excerpt);
	 
		return apply_filters('the_content', $the_excerpt);
	}
	/**
	 * Return the Google font stylesheet URL if available.
	 *
	 * The use of Open Sans by default is localized. For languages that use
	 * characters not supported by the font, the font can be disabled.
	 *
	 * @since Aboss 1.2
	 *
	 * @return string Font stylesheet or empty string if disabled.
	 */
	function aboss_get_font_url() {
		$fonts_url = '';
		 
		/* Translators: If there are characters in your language that are not
		* supported by Poppins, translate this to 'off'. Do not translate
		* into your own language.
		*/
		$poppins = _x( 'on', 'Poppins font: on or off', 'aboss' );
		 
		if ( 'off' !== $poppins ) {
			$font_families = array();

			if ( 'off' !== $poppins ) {
				$font_families[] = 'Poppins:700italic,400,800,600';
			}
			
			//default font
			if ( !class_exists('ReduxFrameworkPlugin') ) {
				
				$oswald = _x( 'on', 'Oswald font: on or off', 'aboss' );
				$lobster_two = _x( 'on', 'Lobster Two font: on or off', 'aboss' );
				$playfairdisplay = _x( 'on', 'PlayfairDisplay font: on or off', 'aboss' );
				
				if ( 'off' !== $oswald ) {
					$font_families[] = 'Oswald:300,400,500,600,700';
				}
				if ( 'off' !== $lobster_two ) {
					$font_families[] = 'Lobster Two';
				}
				if ( 'off' !== $playfairdisplay ) {
					$font_families[] = 'PlayfairDisplay';
				}
			}

			$query_args = array(
				'family' => urlencode( implode( '|', $font_families ) ),
				'subset' => urlencode( 'latin,latin-ext' ),
			);
			 
			$fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
		}
		 
		return esc_url_raw( $fonts_url );
	}
	/**
	 * Displays navigation to next/previous pages when applicable.
	 *
	 * @since Aboss 1.0
	 */
	static function aboss_content_nav( $html_id ) {
		global $wp_query;

		$html_id = esc_attr( $html_id );

		if ( $wp_query->max_num_pages > 1 ) : ?>
			<nav id="<?php echo esc_attr($html_id); ?>" class="navigation" role="navigation">
				<h3 class="assistive-text"><?php esc_html_e( 'Post navigation', 'aboss' ); ?></h3>
				<div class="nav-previous"><?php next_posts_link( wp_kses(__( '<span class="meta-nav">&larr;</span> Older posts', 'aboss' ),array('span'=>array('class'=>array())) )); ?></div>
				<div class="nav-next"><?php previous_posts_link( wp_kses(__( 'Newer posts <span class="meta-nav">&rarr;</span>', 'aboss' ), array('span'=>array('class'=>array())) )); ?></div>
			</nav>
		<?php endif;
	}
	/* Pagination */
	static function aboss_pagination() {
		global $wp_query;

		$big = 999999999; // need an unlikely integer
		if($wp_query->max_num_pages > 1) {
			echo '<div class="pagination-inner">';
		}
		
		echo paginate_links( array(
			'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
			'format' => '?paged=%#%',
			'current' => max( 1, get_query_var('paged') ),
			'total' => $wp_query->max_num_pages,
			'prev_text'    => esc_html__('Previous', 'aboss'),
			'next_text'    =>esc_html__('Next', 'aboss'),
		) );
		if($wp_query->max_num_pages > 1) {
			echo '</div>';
		}
	}
	/**
	 * Template for comments and pingbacks.
	 *
	 * To override this walker in a child theme without modifying the comments template
	 * simply create your own aboss_comment(), and that function will be used instead.
	 *
	 * Used as a callback by wp_list_comments() for displaying the comments.
	 *
	 * @since Aboss 1.0
	 */
	static function aboss_comment( $comment, $args, $depth ) {
		$GLOBALS['comment'] = $comment;
		switch ( $comment->comment_type ) :
			case 'pingback' :
			case 'trackback' :
			// Display trackbacks differently than normal comments.
		?>
		<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
			<p><?php esc_html_e( 'Pingback:', 'aboss' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( esc_html__( '(Edit)', 'aboss' ), '<span class="edit-link">', '</span>' ); ?></p>
		<?php
				break;
			default :
			// Proceed with normal comments.
			global $post;
		?>
		<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
			<article id="comment-<?php comment_ID(); ?>" class="comment">
				<div class="comment-avatar">
					<?php echo get_avatar( $comment, 50 ); ?>
				</div>
				<div class="comment-info">
					<header class="comment-meta comment-author vcard">
						<?php
							
							printf( '<cite><b class="fn">%1$s</b> %2$s</cite>',
								get_comment_author_link(),
								// If current post author is also comment author, make it known visually.
								( $comment->user_id === $post->post_author ) ? '<span>' . esc_html__( 'Post author', 'aboss' ) . '</span>' : ''
							);
							printf( '<time datetime="%1$s">%2$s</time>',
								get_comment_time( 'c' ),
								/* translators: 1: date, 2: time */
								sprintf( esc_html__( '%1$s at %2$s', 'aboss' ), get_comment_date(), get_comment_time() )
							);
						?>
						<div class="reply">
							<?php comment_reply_link( array_merge( $args, array( 'reply_text' => esc_html__( 'Reply', 'aboss' ), 'after' => '', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
						</div><!-- .reply -->
					</header><!-- .comment-meta -->
					<?php if ( '0' == $comment->comment_approved ) : ?>
						<p class="comment-awaiting-moderation"><?php esc_html_e( 'Your comment is awaiting moderation.', 'aboss' ); ?></p>
					<?php endif; ?>

					<section class="comment-content comment">
						<?php comment_text(); ?>
						<?php edit_comment_link( esc_html__( 'Edit', 'aboss' ), '<p class="edit-link">', '</p>' ); ?>
					</section><!-- .comment-content -->
				</div>
			</article><!-- #comment-## -->
		<?php
			break;
		endswitch; // end comment_type check
	}
	/**
	 * Set up post entry meta.
	 *
	 * Prints HTML with meta information for current post: categories, tags, permalink, author, and date.
	 *
	 * Create your own aboss_entry_meta() to override in a child theme.
	 *
	 * @since Aboss 1.0
	 */
	static function aboss_entry_meta() {
		
		// Translators: used between list items, there is a space after the comma.
		$tag_list = get_the_tag_list( '', ', ' );

		$num_comments = (int)get_comments_number();
		$write_comments = '';
		if ( comments_open() ) {
			if ( $num_comments == 0 ) {
				$comments = esc_html__('0 comments', 'aboss');
			} elseif ( $num_comments > 1 ) {
				$comments = $num_comments . esc_html__(' comments', 'aboss');
			} else {
				$comments = esc_html__('1 comment', 'aboss');
			}
			$write_comments = '<a href="' . get_comments_link() .'">'. $comments.'</a>';
		}

		$utility_text = esc_html__( '%1$s ', 'aboss' );
			printf($utility_text, $write_comments );
		
		if($tag_list != '') { 
			$utility_text1 = esc_html__( 'Tags: %1$s', 'aboss' );
			printf( $utility_text1,  $tag_list);
		}
	}
	static function aboss_entry_meta_small() {
		
		// Translators: used between list items, there is a space after the comma.
		$categories_list = get_the_category_list(', ');

		$author = sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>',
			esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
			esc_attr( sprintf( wp_kses(__( 'View all posts by %s', 'aboss' ), array('a'=>array())), get_the_author() ) ),
			get_the_author()
		);
		
		$utility_text = esc_html__( 'Posted by %1$s / %2$s', 'aboss' );

		printf( $utility_text, $author, $categories_list );
		
	}
	static function aboss_entry_comments() {
		
		$date = sprintf( '<time class="entry-date" datetime="%3$s">%4$s</time>',
			esc_url( get_permalink() ),
			esc_attr( get_the_time() ),
			esc_attr( get_the_date( 'c' ) ),
			esc_html( get_the_date() )
		);

		$num_comments = (int)get_comments_number();
		$write_comments = '';
		if ( comments_open() ) {
			if ( $num_comments == 0 ) {
				$comments = wp_kses(__('<span>0</span> comments', 'aboss'), array('span'=>array()));
			} elseif ( $num_comments > 1 ) {
				$comments = '<span>'.$num_comments .'</span>'. esc_html__(' comments', 'aboss');
			} else {
				$comments = wp_kses(__('<span>1</span> comment', 'aboss'), array('span'=>array()));
			}
			$write_comments = '<a href="' . get_comments_link() .'">'. $comments.'</a>';
		}
		
		$utility_text = esc_html__( '%1$s', 'aboss' );
		
		printf( $utility_text, $write_comments );
	}
	/**
	* TGM-Plugin-Activation
	*/
	function aboss_register_required_plugins() {

		$plugins = array(
			array(
				'name'               => esc_html__('RoadThemes Helper', 'aboss'),
				'slug'               => 'roadthemes-helper',
				'source'             => get_template_directory() . '/plugins/roadthemes-helper.zip',
				'required'           => true,
				'version'            => '1.0.0',
				'force_activation'   => false,
				'force_deactivation' => false,
				'external_url'       => '',
			),
			array(
				'name'               => esc_html__('Mega Main Menu', 'aboss'),
				'slug'               => 'mega_main_menu',
				'source'             => get_template_directory() . '/plugins/mega_main_menu.zip',
				'required'           => true,
				'external_url'       => '',
			),
			array(
				'name'               => esc_html__('Revolution Slider', 'aboss'),
				'slug'               => 'revslider',
				'source'             => get_template_directory() . '/plugins/revslider.zip',
				'required'           => true,
				'external_url'       => '',
			),
			array(
				'name'               => esc_html__('Import Sample Data', 'aboss'),
				'slug'               => 'road-importdata',
				'source'             => get_template_directory() . '/plugins/road-importdata.zip',
				'required'           => true,
				'external_url'       => '',
			),
			array(
				'name'               => esc_html__('Visual Composer', 'aboss'),
				'slug'               => 'js_composer',
				'source'             => get_template_directory() . '/plugins/js_composer.zip',
				'required'           => true,
				'external_url'       => '',
			),
			array(
				'name'               => esc_html__('Templatera', 'aboss'),
				'slug'               => 'templatera',
				'source'             => get_template_directory() . '/plugins/templatera.zip',
				'required'           => true,
				'external_url'       => '',
			),
			array(
				'name'               => esc_html__('Essential Grid', 'aboss'),
				'slug'               => 'essential-grid',
				'source'             => get_template_directory() . '/plugins/essential-grid.zip',
				'required'           => true,
				'external_url'       => '',
			),
			// Plugins from the WordPress Plugin Repository.
			array(
				'name'               => esc_html__('Redux Framework', 'aboss'),
				'slug'               => 'redux-framework',
				'required'           => true,
				'force_activation'   => false,
				'force_deactivation' => false,
			),
			array(
				'name'               => esc_html__('Contact Form 7', 'aboss'),
				'slug'      => 'contact-form-7',
				'required'  => true,
			),
			array(
				'name'               => esc_html__('Events Manager', 'aboss'),
				'slug'      => 'events-manager',
				'required'  => false,
			),
			array(
				'name'               => esc_html__('Instagram Feed', 'aboss'),
				'slug'      => 'instagram-feed',
				'required'  => false,
			),
			array(
				'name'               => esc_html__('Latest Tweets Widget', 'aboss'),
				'slug'      => 'latest-tweets-widget',
				'required'  => false,
			),
			array(
				'name'               => esc_html__('WS Facebook Like Box Widget', 'aboss'),
				'slug'      => 'ws-facebook-likebox',
				'required'  => false,
			),
			// array(
			// 	'name'      => 'MailPoet Newsletters',
			// 	'slug'      => 'wysija-newsletters',
			// 	'required'  => true,
			// ),
			array(
				'name'               => esc_html__('MailChimp for WordPress', 'aboss'),
				'slug'      => 'mailchimp-for-wp',
				'required'  => true,
			),
			array(
				'name'               => esc_html__('Shortcodes Ultimate', 'aboss'),
				'slug'      => 'shortcodes-ultimate',
				'required'  => true,
			),
			array(
				'name'      => 'Simple Local Avatars',
				'name'               => esc_html__('Simple Local Avatars', 'aboss'),
				'slug'      => 'simple-local-avatars',
				'required'  => false,
			),
			array(
				'name'               => esc_html__('Testimonials', 'aboss'),
				'slug'      => 'testimonials-by-woothemes',
				'required'  => true,
			),
			array(
				'name'               => esc_html__('TinyMCE Advanced', 'aboss'),
				'slug'      => 'tinymce-advanced',
				'required'  => false,
			),
			array(
				'name'               => esc_html__('Widget Importer & Exporter', 'aboss'),
				'slug'      => 'widget-importer-exporter',
				'required'  => true,
			),
			array(
				'name'               => esc_html__('WooCommerce', 'aboss'),
				'slug'      => 'woocommerce',
				'required'  => true,
			),
			array(
				'name'               => esc_html__('YITH WooCommerce Compare', 'aboss'),
				'slug'      => 'yith-woocommerce-compare',
				'required'  => false,
			),
			array(
				'name'               => esc_html__('YITH WooCommerce Wishlist', 'aboss'),
				'slug'      => 'yith-woocommerce-wishlist',
				'required'  => false,
			),
			array(
				'name'               => esc_html__('YITH WooCommerce Zoom Magnifier', 'aboss'),
				'slug'      => 'yith-woocommerce-zoom-magnifier',
				'required'  => false,
			),
		);

		/**
		 * Array of configuration settings. Amend each line as needed.
		 * If you want the default strings to be available under your own theme domain,
		 * leave the strings uncommented.
		 * Some of the strings are added into a sprintf, so see the comments at the
		 * end of each line for what each argument will be.
		 */
		$config = array(
			'default_path' => '',                      // Default absolute path to pre-packaged plugins.
			'menu'         => 'tgmpa-install-plugins', // Menu slug.
			'has_notices'  => true,                    // Show admin notices or not.
			'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
			'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
			'is_automatic' => false,                   // Automatically activate plugins after installation or not.
			'message'      => '',                      // Message to output right before the plugins table.
			'strings'      => array(
				'page_title'                      => esc_html__( 'Install Required Plugins', 'aboss' ),
				'menu_title'                      => esc_html__( 'Install Plugins', 'aboss' ),
				'installing'                      => esc_html__( 'Installing Plugin: %s', 'aboss' ), // %s = plugin name.
				'oops'                            => esc_html__( 'Something went wrong with the plugin API.', 'aboss' ),
				'notice_can_install_required'     => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.', 'aboss' ), // %1$s = plugin name(s).
				'notice_can_install_recommended'  => _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.', 'aboss' ), // %1$s = plugin name(s).
				'notice_cannot_install'           => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.', 'aboss' ), // %1$s = plugin name(s).
				'notice_can_activate_required'    => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.', 'aboss' ), // %1$s = plugin name(s).
				'notice_can_activate_recommended' => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.', 'aboss' ), // %1$s = plugin name(s).
				'notice_cannot_activate'          => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.', 'aboss' ), // %1$s = plugin name(s).
				'notice_ask_to_update'            => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.', 'aboss' ), // %1$s = plugin name(s).
				'notice_cannot_update'            => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.', 'aboss' ), // %1$s = plugin name(s).
				'install_link'                    => _n_noop( 'Begin installing plugin', 'Begin installing plugins', 'aboss' ),
				'activate_link'                   => _n_noop( 'Begin activating plugin', 'Begin activating plugins', 'aboss' ),
				'return'                          => esc_html__( 'Return to Required Plugins Installer', 'aboss' ),
				'plugin_activated'                => esc_html__( 'Plugin activated successfully.', 'aboss' ),
				'complete'                        => esc_html__( 'All plugins installed and activated successfully. %s', 'aboss' ), // %s = dashboard link.
				'nag_type'                        => 'updated' // Determines admin notice type - can only be 'updated', 'update-nag' or 'error'.
			)
		);

		tgmpa( $plugins, $config );

	}
}

// Instantiate theme
$aboss_Class = new aboss_Class();

//Fix duplicate id of mega menu
function aboss_mega_menu_id_change($params) {
	ob_start('aboss_mega_menu_id_change_call_back');
}
function aboss_mega_menu_id_change_call_back($html){
	$html = preg_replace('/id="mega_main_menu"/', 'id="mega_main_menu_first"', $html, 1);
	$html = preg_replace('/id="mega_main_menu_ul"/', 'id="mega_main_menu_ul_first"', $html, 1);
	
	return $html;
}
function total_product_count() {
    $args = array( 'post_type' => 'product', 'posts_per_page' => -1 );

    $products = new WP_Query( $args );

    return $products->found_posts;
}
function aboss_prefix_enqueue_script() {
	wp_add_inline_script( 'aboss-theme-jquery', 'var ajaxurl = "'.admin_url('admin-ajax.php').'";','before' );
}
add_action( 'wp_enqueue_scripts', 'aboss_prefix_enqueue_script' ); 
add_action('wp_loaded', 'aboss_mega_menu_id_change');