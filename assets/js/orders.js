(function($) {

	/**
	 * Orders updates
	 */
	var Frozr_Order_Updates = {
		
		init: function() {
			if ($('.orders_list_table').length) {
				var refresh = setInterval(this.refresh_orders, 60000);//update orders every 1 minute
			}
		},
		refresh_orders: function() {

			var data	= {};

			data.ods		= $('.orders_lists').data('ods');
			data.action		= 'frozr_refresh_orders_list';
			data.security	= frozr.frozr_refresh_orders_list;
			$('.orders_list_table').block({ message: null, overlayCSS: { background: '#fff url(' + frozr.ajax_loader + ') no-repeat center', opacity: 0.6 } });

			$.ajax({
				url: frozr.ajax_url,
				data: data,
				type: 'POST',
				timeout: 10000,
				error: function(jqXHR) { 
					if(jqXHR.status==0) {
						$('.orders_lists').html(frozr.frozr_no_connection);
					}
				},
				success: function( response ) {
					$('.orders_lists').html(response);
					$('.orders_list_table').unblock();					
				}
			});
		},
	};
	/**
	 * Order Status
	 */
	var Frozr_Order_Status = {

		init: function() {
			
			$( document.body ).on("click", "a.order_status_butn", this.update_order_status);
		},
		update_order_status: function(e) {
			if (window.confirm('Sure?')) {
			e.preventDefault();
			var new_status = $(this).data('status'),
				order_id = $(this).data('orderid'),
				data        = {};

			$(this).closest('table').block({ message: null, overlayCSS: { background: '#fff url(' + frozr.ajax_loader + ') no-repeat center', opacity: 0.6 } });

			data.action		= 'frozr_set_order_status';
			data.security	= frozr.frozr_set_order_status;
			data.order_id	= order_id;
			data.order_sts	= new_status;

			$.ajax({
				url: frozr.ajax_url,
				data: data,
				type: 'POST',
				success: function( response ) {
					console.log(response);
					$(this).closest('table').unblock();
					
					location.reload(true);
				}
			});
			}
		},
	};
	/**
	 * Order Notes
	 */
	var Frozr_Order_Notes = {
		init: function() {
			$( '.or_notes' )
				.on( 'click', 'a.add_note', this.add_order_note )
				.on( 'click', 'a.delete_note', this.delete_order_note );

		},

		add_order_note: function() {
			if ( ! $( 'textarea#add_order_note' ).val() ) {
				return;
			}

			$( '.or_notes' ).block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});

			var orderid = $('#orders').data('orderid'),
				data = {
					action:    'frozr_add_order_note',
					post_id:   orderid,
					note:      $( 'textarea#add_order_note' ).val(),
					note_type: $( 'select#order_note_type' ).val(),
					security:  frozr.add_order_note
				};

			$.post( frozr.ajax_url, data, function( response ) {
				$( 'ul.order_notes' ).prepend( response );
				$( '.or_notes' ).unblock();
				$( '#add_order_note' ).val( '' );
			});

			return false;
		},

		delete_order_note: function() {
			var note = $( this ).closest( 'li.note' );

			$( note ).block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});

			var data = {
				action:   'frozr_delete_order_note',
				note_id:  $( note ).attr( 'rel' ),
				security: frozr.delete_order_note_nonce
			};

			$.post( frozr.ajax_url, data, function() {
				$( note ).remove();
			});

			return false;
		}
	};
	Frozr_Order_Status.init();
	Frozr_Order_Notes.init();
	Frozr_Order_Updates.init();

})(jQuery);