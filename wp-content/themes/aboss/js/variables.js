		"use strict";
		var aboss_brandnumber = 6,
			aboss_brandscrollnumber = 2,
			aboss_brandpause = 3000,
			aboss_brandanimate = 2000;
		var aboss_brandscroll = false;
							aboss_brandscroll = true;
					var aboss_categoriesnumber = 6,
			aboss_categoriesscrollnumber = 2,
			aboss_categoriespause = 3000,
			aboss_categoriesanimate = 2000;
		var aboss_categoriesscroll = false;
					var aboss_blogpause = 3000,
			aboss_blabossmate = 2000;
		var aboss_blogscroll = false;
							aboss_blogscroll = true;
					var aboss_testipause = 3000,
			aboss_testianimate = 2000;
		var aboss_testiscroll = false; 
							aboss_testiscroll = false;
					var aboss_catenumber = 6,
			aboss_catescrollnumber = 2,
			aboss_catepause = 3000,
			aboss_cateanimate = 700;
		var aboss_catescroll = false;
					var aboss_menu_number = 10; 

		var aboss_sticky_header = false;
							aboss_sticky_header = true;
			
		var aboss_item_first = 12,
			aboss_moreless_products = 4;

		jQuery(document).ready(function(){
			jQuery("#ws").focus(function(){
				if(jQuery(this).val()=="Search product..."){
					jQuery(this).val("");
				}
			});
			jQuery("#ws").focusout(function(){
				if(jQuery(this).val()==""){
					jQuery(this).val("Search product...");
				}
			});
			jQuery("#wsearchsubmit").click(function(){
				if(jQuery("#ws").val()=="Search product..." || jQuery("#ws").val()==""){
					jQuery("#ws").focus();
					return false;
				}
			});
			jQuery("#search_input").focus(function(){
				if(jQuery(this).val()=="Search..."){
					jQuery(this).val("");
				}
			});
			jQuery("#search_input").focusout(function(){
				if(jQuery(this).val()==""){
					jQuery(this).val("Search...");
				}
			});
			jQuery("#blogsearchsubmit").click(function(){
				if(jQuery("#search_input").val()=="Search..." || jQuery("#search_input").val()==""){
					jQuery("#search_input").focus();
					return false;
				}
			});
		});
		