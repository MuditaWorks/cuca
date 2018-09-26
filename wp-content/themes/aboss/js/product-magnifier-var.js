"use strict";
// product-magnifier var
var aboss_magnifier_vars;
var yith_magnifier_options = {
		
		sliderOptions: {
			responsive: aboss_magnifier_vars.responsive,
			circular: aboss_magnifier_vars.circular,
			infinite: aboss_magnifier_vars.infinite,
			direction: 'left',
			debug: false,
			auto: false,
			align: 'left',
			height: 'auto', 
			//height: "100%", //turn vertical
			//width: 72,
			prev    : {
				button  : "#slider-prev",
				key     : "left"
			},
			next    : {
				button  : "#slider-next",
				key     : "right"
			},
			scroll : {
				items     : 1,
				pauseOnHover: true
			},
			items   : {
				visible: Number(aboss_magnifier_vars.visible),
			},
			swipe : {
				onTouch:    true,
				onMouse:    true
			},
			mousewheel : {
				items: 1
			}
		},
		
		showTitle: false,
		zoomWidth: aboss_magnifier_vars.zoomWidth,
		zoomHeight: aboss_magnifier_vars.zoomHeight,
		position: aboss_magnifier_vars.position,
		lensOpacity: aboss_magnifier_vars.lensOpacity,
		softFocus: aboss_magnifier_vars.softFocus,
		adjustY: 0,
		disableRightClick: false,
		phoneBehavior: aboss_magnifier_vars.phoneBehavior,
		loadingLabel: aboss_magnifier_vars.loadingLabel,
	};