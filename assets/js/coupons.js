//frozr coupons
(function($) {

	var file_frame;
	var Frozr_Coupons = {
		init: function() {
	
			//create\update coupon
			$('#coupons_form').on('submit', this.save_coupons);
			//delete coupon
			$('.delete_coupon').on('click', this.delete_coupon);
		},	
		/**
		 * Save Coupons
		 */
		save_coupons: function(e) {
			e.preventDefault();
			var wrapper		= $(this),
				data		= {};
			
			wrapper.block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
			
			data                 = wrapper.serializeJSON();
			data.action          = 'frozr_coupons_create';
			data.security        = frozr.coupon_nonce_field;
			
			$.ajax({
				url: frozr.ajax_url,
				data: data,
				type: 'POST',
				success: function( response ) {
					$('.fro_woo_notices').html('<div class="woocommerce-message">' + response + '</div>');
					wrapper.unblock();
				}
			});
		},
		/**
		 * delete coupon
		 *
		 */
		delete_coupon: function(e) {
			e.preventDefault();
			if ( window.confirm( frozr.coupon_delete ) ) {
			var data = {},
				wrapper = $(this).parent().parent().parent().parent(),
				req_id = $(this).data('coupid');

			wrapper.block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});

			data.action			= 'frozr_coupun_delete';
			data.security		= frozr.coupon_del_nonce;
			data.post_id		= req_id;
			$.ajax({
				url: frozr.ajax_url,
				data: data,
				type: 'POST',
				success: function( response ) {
					wrapper.remove();
				}
			});
			}
		},
	};
	
	Frozr_Coupons.init();

})(jQuery);