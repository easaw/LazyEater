jQuery.noConflict();
jQuery(window).load(function(){
		
	jQuery('#restaurant_lists_content').masonry({
		isAnimated: true,
		itemSelector: ".restaurant_wrap",
		isOriginLeft: frozr.masonry_rtl
	 }); 
});