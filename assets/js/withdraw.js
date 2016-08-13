//frozr withdraw
(function($) {

	var file_frame;
	var Frozr_Withdraw = {
		init: function() {
	
			//invoice upload
			$('.frozr-wid-image-btn').on('click', this.upload_wid_invoice);
			$('.frozr-remove-wid-image').on('click', this.remove_wid_invoice);
			
			$( document.body ).on( "change", "form.withdraw .ui-radio", this.wid_req_pen);
			$('form.withdraw').on('submit', this.save_withdraw);
			
			$('.delete_wid').on('click', 'a', this.delete_withdraw); 
		},
		wid_req_pen: function() {
			var wrapper = $(this).parent().parent().parent().parent();
			if ($(".pend_wid_req", this).prop("checked")) {
				$(".withdraw_invoice, .wid_reject_div", wrapper).addClass("frozr-hide");
				$(".wid_gen_info", wrapper).removeClass("frozr-hide");
				$( ".edit_wid" ).popup( "reposition", {positionTo: "window"} );
			} else if ($(".reject_wid_req", this).prop("checked")) {
				$(".withdraw_invoice, .wid_gen_info", wrapper).addClass("frozr-hide");
				$(".wid_reject_div", wrapper).removeClass("frozr-hide");
				$( ".edit_wid" ).popup( "reposition", {positionTo: "window"} );
			} else if ($(".com_wid_req", this).prop("checked")) {
				$(".wid_reject_div, .wid_gen_info", wrapper).addClass("frozr-hide");
				$(".withdraw_invoice", wrapper).removeClass("frozr-hide");
				$( ".edit_wid" ).popup( "reposition", {positionTo: "window"} );
			}
		},
		upload_wid_invoice: function(e) {
        e.preventDefault();

        var self = $(this);

        if ( file_frame ) {
            file_frame.open();
            return;
        }

        file_frame = wp.media({
            // Set the title of the modal.
            title: 'Upload featured image',
            button: {
                text: 'Set featured image',
            }
        });

        file_frame.on('select', function() {
            var selection = file_frame.state().get('selection');

            selection.map( function( attachment ) {
                attachment = attachment.toJSON();

                console.log(attachment, self);
               
			   // set the image
                var instruction = self.closest('.instruction-inside');
                var wrap = instruction.siblings('.image-wrap');
               
			   // set the image hidden id
                wrap.find('input.frozr-wid-image-id').val(attachment.id).change();

                // wrap.find('img').attr('src', attachment.sizes.thumbnail.url);
                wrap.find('div.withdraw_img').css("background-image","url(" + attachment.url + ")");

                instruction.addClass('frozr-hide');
                wrap.removeClass('frozr-hide');
            });
        });

        file_frame.open();

		},
		remove_wid_invoice: function(e) {
			e.preventDefault();

			var self = $(this);
			var wrap = self.closest('.image-wrap');
			var instruction = wrap.siblings('.instruction-inside');

			wrap.find('input.frozr-wid-image-id').val('0');
			wrap.addClass('frozr-hide');
			instruction.removeClass('frozr-hide');
		},
		/**
		 * Block edit screen
		 */
		block: function() {
			$( 'form.withdraw' ).block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
		},
		/**
		 * Unblock edit screen
		 */
		unblock: function() {
			$( 'form.withdraw' ).unblock();
		},
		/**
		 * delete request
		 *
		 */
		delete_withdraw: function(e) {
			e.preventDefault();
			if ( window.confirm( frozr.withdraw_delete ) ) {
			var data = {},
				req_id = $(this).attr('req_id');

			Frozr_Withdraw.block();

			data.action			= 'frozr_delete_withdraw';
			data.security		= frozr.delete_fro_withdraw;
			data.withdraw_id	= req_id;
			$.ajax({
				url: frozr.ajax_url,
				data: data,
				type: 'POST',
				success: function( response ) {
					Frozr_Withdraw.unblock();
					
					location.reload(true);
				}
			});
			}
		},
		save_withdraw: function(e) {
			e.preventDefault();
			var wrapper		= $(this),
				data		= {};

			Frozr_Withdraw.block();

			data                 = wrapper.serializeJSON();
			data.action          = 'frozr_save_withdraw';
			data.security        = frozr.frozr_save_withdraw;

			$.ajax({
				url: frozr.ajax_url,
				data: data,
				type: 'POST',
				success: function( response ) {
					Frozr_Withdraw.unblock();
					
					location.reload(true);
				}
			});
		},
	};
	
	Frozr_Withdraw.init();

})(jQuery);