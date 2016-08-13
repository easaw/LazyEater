// Frozr Register

jQuery(function($) {

    $('.user-role input[type=radio]').on('change', function() {
        var value = $(this).val(),
			wrapper = $('.show_if_seller');

        if ( value === 'seller') {
            wrapper.slideDown();
			$("input", wrapper).prop( "disabled", false );
			$('.fro_cus_tos_wrapper').hide();
        } else {
            wrapper.slideUp();
			$("input", wrapper).prop( "disabled", true );
			$('.fro_cus_tos_wrapper').show();
        }
    });
	
	$('.fro_cus_tos_btn').on('click', function(e) {
		e.preventDefault();
		$('#pop_fro_customer_tos').popup('open');
	});

	$('.fro_sel_tos_btn').on('click', function(e) {
		e.preventDefault();
		$('#pop_fro_seller_tos').popup('open');
	});
	
	$('.f_go_back').on('click', function() {
		window.history.back();
	});

	$( document.body ).on('click', '.le_makeorder_button', function() {
		$('.wc-radios').trigger('change');
	});
	
	$('.send_seller_msg_pop').on('click', function() {
		var sellerid = $(this).data('userid');
		$('#seller_mgs .frozr_seller_id_msg').val(sellerid);
	});
	
    $('#company-name').on('focusout', function() {
        var value = $(this).val().toLowerCase().replace(/-+/g, '').replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, '');
        $('#seller-url').val(value);
        $('#url-alart').text( value );
        $('#seller-url').focus();
    });

    $('#seller-url').keydown(function(e) {
        var text = $(this).val();

        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 91, 109, 110, 173, 189, 190]) !== -1 ||
             // Allow: Ctrl+A
            (e.keyCode == 65 && e.ctrlKey === true) ||
             // Allow: home, end, left, right
            (e.keyCode >= 35 && e.keyCode <= 39)) {
                 // let it happen, don't do anything
                return;
        }

        if ((e.shiftKey || (e.keyCode < 65 || e.keyCode > 90) && (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105) ) {
            e.preventDefault();
        }
    });

    $('#seller-url').keyup(function(e) {
        $('#url-alart').text( $(this).val() );
    });

    $('#shop-phone').keydown(function(e) {
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 91, 107, 109, 110, 187, 189, 190]) !== -1 ||
             // Allow: Ctrl+A
            (e.keyCode == 65 && e.ctrlKey === true) ||
             // Allow: home, end, left, right
            (e.keyCode >= 35 && e.keyCode <= 39)) {
                 // let it happen, don't do anything
                 return;
        }

        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });

    $('#seller-url').on('focusout', function() {
        var self		= $(this),
			wrapper		= self.closest('.form-row'),
			data        = {};

		if ( self.val() === '' ) {
			return;
		}
			
		wrapper.block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});

		data.url_slug		= self.val();
		data.action			= 'shop_url';
		data.security		= frozr.new_restaurant_nonce;

		$.ajax({
			url: frozr.ajax_url,
			data: data,
			type: 'POST',
			success: function( response ) {
				if ( response == 0){
					$('#url-alart').removeClass('text-success').addClass('text-danger');
					$('#url-alart-mgs').removeClass('text-success').addClass('text-danger').text(frozr.seller.notAvailable);
				} else {
					$('#url-alart').removeClass('text-danger').addClass('text-success');
					$('#url-alart-mgs').removeClass('text-danger').addClass('text-success').text(frozr.seller.available);
				}
				wrapper.unblock();
			}
		});
    });
    $('#frozr-form-contact-seller').on('submit', function(e) {
		e.preventDefault();
        var self		= $(this),
			data        = {};
			
		self.block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});

		data				= self.serializeJSON();
		data.action			= 'frozr_contact_seller';
		data.security		= frozr.frozr_contact_seller;

		$.ajax({
			url: frozr.ajax_url,
			data: data,
			type: 'POST',
			success: function( response ) {
				$('.ajax-response', self).html(response);
				self.unblock();
			}
		});
    });
	//Restaurant timing settings
	$('.rest_open').change(function(){
	var wrapper = $(this).parent().parent().parent();
		$('.rest_time_inputs, .rest_shifts_cont', wrapper).removeClass('frozr-hide').toggle(this.checked);
	});
	$('.rest_shifts').change(function(){
	var wrapper = $(this).parent().parent().parent();
		$('.rest_two', wrapper).removeClass('frozr-hide').toggle(this.checked);
	});
	//Duplicate fields
	$('.multi-field-wrapper').each(function() {
		var wrapper = $('.multi-fields', this);
		$(this).on('click', '.add-field', function() {
			$('.multi-field:first-child', wrapper).clone(true).appendTo(wrapper).find('input').val('').focus();
		});
		$('.multi-field .remove-field', wrapper).click(function() {
			if ($('.multi-field', wrapper).length > 1)
				$(this).parent('.multi-field').remove();
		});
	});
});

//frozr settings
(function($) {
	//make front-page search boxes sortable
	$( "#resturants_advance_search_box_fst" ).sortable({
		items: "> .sortable_front_boxes ",
		scroll: false,
		connectWith: ".sort_adv_box, #restaurant_search_box_trash",
		start: function( event, ui ) {
			if (!$( ".trash_btn" ).parent().parent().hasClass('show_trash')) {
			$( ".trash_btn" ).trigger('click');
			}
		},
		stop: function( event, ui ) {
			$( ".trash_btn" ).trigger('click');
		},
		update: function( event, ui ) {
		var rbssort = $(this).sortable( "serialize", { attribute : "ord" } );
			if (this === ui.item.parent()[0]) {
				if (ui.sender !== null) {
					var senderord = ui.sender.sortable( "serialize", { attribute : "ord" } );
					if (ui.sender[0].id == 'resturants_advance_search_box') { 
						var sendertp = 'snd';
					} else {
						var sendertp = 'trd';
					}
					ui.item.removeClass('sortable_front_boxes search_adv_wrapper ui-sortable-handle').addClass('sortable_front_boxes rsb-boxes ui-sortable-handle');
					Frozr_Settings.save_front_sorts_outside(rbssort, 'fst', senderord, sendertp);
				} else {
					Frozr_Settings.save_front_sorts_inside(rbssort, 'fst');
				}
			}
		}
	});
	$( ".sort_adv_box" ).sortable({
		items: "> .sortable_front_boxes ",
		scroll: false,
		connectWith: "#resturants_advance_search_box_fst, #restaurant_search_box_trash",
		start: function( event, ui ) {
			if (!$( ".trash_btn" ).parent().parent().hasClass('show_trash')) {
			$( ".trash_btn" ).trigger('click');
			}
		},
		stop: function( event, ui ) {
			$( ".trash_btn" ).trigger('click');
		},
		update: function( event, ui ) {
			var retsort = $(this).sortable( "serialize", { attribute : "ord" } );
			if (this === ui.item.parent()[0]) {
				if (ui.sender !== null) {
					var senderord = ui.sender.sortable( "serialize", { attribute : "ord" } );
					if (ui.sender[0].id == 'restaurant_search_box_trash') { 
						var sendertp = 'trd';
					} else {
						var sendertp = 'fst';
					}
					if (!$( "span:first-child", ui.item ).hasClass('src_adv_wrp_imgid')) {
						var clsv  = ui.item.attr('ord').split('_');
						ui.item.prepend('<span class="src_adv_wrp_imgid '+clsv[1]+'img" data-cls="'+clsv[1]+'img"><span class="front_edit_btns"><a data-ajax="false" data-enhance="false" href="#" class="src_adv_wrp_img ui-link" data-uploader_title="Set filter box image" data-uploader_button_text="Set" title="Edit Image"><i class="fs-icon-edit"></i></a></span></span>');
					}
					ui.item.removeClass('sortable_front_boxes rsb-boxes ui-sortable-handle').addClass('sortable_front_boxes search_adv_wrapper ui-sortable-handle');
					Frozr_Settings.save_front_sorts_outside(retsort, 'snd', senderord, sendertp);
				} else {
					Frozr_Settings.save_front_sorts_inside(retsort, 'snd');
				}
			}
		}
	});
	$( "#restaurant_search_box_trash" ).sortable({
		connectWith: "#resturants_advance_search_box_fst, .sort_adv_box",
		items: "> .sortable_front_boxes ",
		scroll: false,
		update: function( event, ui ) {
			var trashsort = $(this).sortable( "serialize", { attribute : "ord" } );
			if (this === ui.item.parent()[0]) {
				if (ui.sender !== null) {
					var senderord = ui.sender.sortable( "serialize", { attribute : "ord" } );
					if (ui.sender[0].id == 'resturants_advance_search_box_fst') { 
						var sendertp = 'fst';
					} else {
						var sendertp = 'snd';
					}
					if (ui.item.attr('ord') == 'srt_reco' || ui.item.attr('ord') == 'srt_popu' || ui.item.attr('ord') == 'srt_type') {
						$('.front_inputs_wrap',ui.item).remove();
						$('h2',ui.item).removeClass('control_edit');					
					}
					Frozr_Settings.save_front_sorts_outside(trashsort, 'trd', senderord, sendertp);
				} else {
					Frozr_Settings.save_front_sorts_inside(trashsort, 'trd');
				}
			}
		}
	});
	
	var file_frame, file_frame_two, product_featured_frame;
	var Frozr_Settings = {
		init: function() {
			var self = this;
			
			//hide some elements
			$('.front_inputs_wrap').hide();
			
			//dish popup
			$(".pop_make_order_wrapper, .dish_special_comments_field textarea, .order_ppl_num_field, .order_car_info_field, .lepop_rest_address, .closed_order_notice, .open_closed_order_notice, .delivery_notice").hide();
			$('.pop_make_order_btn').on('click', self.load_pop_dish_order);
			$(".dish_special_comments_field label").click(self.show_sp_comts);
			$(".adv_loc_src_checkbox").click(self.refresh_loc_list);
			$( document.body )
				.on( "change", ".src_adv_wrp_imgid", self.save_filter_img)
				.on( "click", ".user_location_ul a", self.set_user_cookie)
				.on( "change", ".cart .wc-radios", self.show_adtl_input);
			
			//restaurant tables
			$( "#frozr_tables_btn" ).on( "click", self.show_rest_tables);
			
			//image upload
			$('a.frozr-banner-drag, .src_adv_wrp_img').on('click', self.imageUpload);
			$('a.frozr-remove-banner-image').on('click', self.removeBanner);
			$('a.frozr-gravatar-drag').on('click', self.gragatarImageUpload);
			$('a.frozr-remove-gravatar-image').on('click', self.removeGravatar);
			
			//single add to cart
			$('form.ajax_lazy_submit').on('submit', self.add_to_cart);

			//front mods set
			$('form.front_edit_form').on('submit', self.save_front_mods);
			$( ".control_edit,.front_inputs_cncl" ).on( "click", self.edit_search_boxs_txt);
			$( ".front_inputs_save" ).on( "click", self.save_filters_text);
			$( ".trash_btn" ).on( "click", self.show_trash_box);
			
			//restaurant review
			$('form.rest_rating_login').on('submit', self.rating_login);
			$('form.rest_rating_form').on('submit', self.save_restaurant_rating);
			
			//user profile
			$('#frozr_update_profile').on('click', self.save_restaurant_settings);

			//seller edit
			$('form.seller_edit_form').on('submit', self.save_seller_settings);
			
			//dashboard
			$('.print_summary_report').on('click', self.print_summary_report);
			$('.order_print_butn').on('click', self.frozr_print_order);
			$('.show_resutl').on('click', self.dash_totals);
			$('.show_custom').on('click', self.show_dash_totals_inputs);
			$('.custom_start_end').on('submit', self.dash_totals);
			$('#seller_summary_select').on('change', self.dash_totals);
			$('input#show_rest_tables').on( 'click', self.show_tables_settings);

			//dashboard dishes
			$('.delete_dish').on('click', self.delete_dish);
			
			//Restaurant Invitation
			$('#rest_invit_form').on('submit', self.rest_invitation);
			
		},
		rest_invitation: function (e) {
			e.preventDefault();
			var wrapper		= $(this).parent(),
				self		= $(this),
				data		= {};

			wrapper.block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});

			data			= self.serializeJSON();
			data.action		= 'frozr_send_rest_invitation';
			data.security	= frozr.frozr_rest_invitation_nonce;

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
		add_to_cart: function (e) {
			e.preventDefault();
			var self		= $(this),
				data		= {};

			$('button', self).addClass('fro_adding_to_cart').prepend('<i class="fa fa-spinner fa-pulse fa-fw"></i>');

			data				= self.serializeJSON();
			data.pid			= self.data('product_id');
			data.action			= 'frozr_ajax_add_to_cart';
			data.security		= frozr.frozr_ajax_add_to_cart;

			$.ajax({
				url: frozr.ajax_url,
				data: data,
				type: 'POST',
				success: function( response ) {
					if (response.fragments) {
						var fragments = response.fragments;
						var cart_hash = response.cart_hash;

						$('button i', self).removeClass().addClass('fs-icon-check');

						// Block fragments class
						if ( fragments ) {
							$.each( fragments, function( key, value ) {
								$( key, document.body ).replaceWith( value );
							});
						}

						//refresh cart count
						$('.frozr_top_cart_count', document.body).text(response.count_items).show();

						// Trigger event so themes can refresh other areas
						$( document.body ).trigger( 'added_to_cart', [ fragments, cart_hash, self ] );

					} else {
						$('.fro_woo_notices').html('<div class="woocommerce-error">' + response.data + '</div>');						
					}
					setTimeout(function(){ $('button i', self).remove(); }, 3000);
				}
			});
		},
		show_trash_box: function () {
			$(this).parent().parent().toggleClass('show_trash');
		},
		save_filters_text: function() {
			var self		= $(this).parent().parent(),
				wrapper		= self.parent(),
				wrap		= $('input',self),
				data		= {};

			self.block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});

			data.valu			= wrap.data('tlt');
			data.txt			= wrap.val();
			data.action			= 'frozr_save_filter_text';
			data.security		= frozr.frozr_save_front_mods_nonce;

			$.ajax({
				url: frozr.ajax_url,
				data: data,
				type: 'POST',
				success: function( response ) {
					$('.fro_woo_notices').html('<div class="woocommerce-error">' + response.msg + '</div>');						
					self.unblock();
					$('h2, h1', wrapper).html(response.txt);
					$(".front_inputs_cncl", self).trigger("click");
				}
			});
		},
		edit_search_boxs_txt: function() {
			if ($(this).hasClass('front_inputs_cncl')) {
				var wrapper = $(this).parent().parent().parent();
				$('.control_edit', wrapper).show();
				$(this).parent().parent().hide();
			} else {
				var wrapper = $(this).parent();
				$('.front_inputs_wrap', wrapper).show();
				$(this).hide();
			}
		},
		save_filter_img: function() {
			var self		= $(this),
				data        = {};
				
			self.block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});

			data.type			= self.data('cls');
			data.valu			= self.data('imageid');
			data.action			= 'frozr_save_front_mods';
			data.security		= frozr.frozr_save_front_mods_nonce;

			$.ajax({
				url: frozr.ajax_url,
				data: data,
				type: 'POST',
				success: function( response ) {
					$('.fro_woo_notices').html('<div class="woocommerce-error">' + response + '</div>');						
					self.unblock();
				}
			});
		
		},
		save_front_sorts_outside: function(infun, intp, outfun, outtp) {
			var self		= $('#resturants_search_box, #resturants_advance_search_box'),
				data        = {};
			
			self.block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
		
			data.sort			= infun;
			data.tp				= intp;
			data.action			= 'frozr_save_front_mods';
			data.security		= frozr.frozr_save_front_mods_nonce;

			$.ajax({
				url: frozr.ajax_url,
				data: data,
				type: 'POST',
				success: function( response ) {
					console.log(data.sort);
					data.sort			= outfun;
					data.tp				= outtp;
					$.ajax({
						url: frozr.ajax_url,
						data: data,
						type: 'POST',
						success: function( response ) {
							console.log(data.sort);
							$('.fro_woo_notices').html('<div class="woocommerce-error">' + response + '</div>');						
							self.unblock();
						}
					});
				}
			});
		},
		save_front_sorts_inside: function(fun, tp) {
			var self		= $('#resturants_search_box, #resturants_advance_search_box'),
				data        = {};
			
			self.block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});

			data.sort			= fun;
			data.tp				= tp;
			data.action			= 'frozr_save_front_mods';
			data.security		= frozr.frozr_save_front_mods_nonce;

			$.ajax({
				url: frozr.ajax_url,
				data: data,
				type: 'POST',
				success: function( response ) {
					console.log(data.sort);
					$('.fro_woo_notices').html('<div class="woocommerce-error">' + response + '</div>');						
					self.unblock();
				}
			});
		},
		save_front_mods: function(e) {
			e.preventDefault();
			var wrapper		= $(this).parent(),
				wrap		= wrapper.parent(),
				data		= {};
			
			wrapper.block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
			
			data				= $(this).serializeJSON();
			data.mod			= $(this).data('modtype');
			data.action			= 'frozr_save_front_mods';
			data.security		= frozr.frozr_save_front_mods_nonce;

			$.ajax({
				url: frozr.ajax_url,
				data: data,
				type: 'POST',
				success: function( response ) {
					if (response.msg) {
						$('.rest_list_btn.'+response.btn).html(response.output[0]).css({'background-color':response.output[1],'color':response.output[3]});
						if ($('.rest_list_btn.'+response.btn+' i').length > 0 && response.output[2] !== 'none') {
							$('.rest_list_btn.'+response.btn+' i').removeClass().addClass(response.output[2]);
						} else if (!$('.rest_list_btn.'+response.btn+' i').length > 0 && response.output[2] !== 'none') {
							$('.rest_list_btn.'+response.btn).prepend('<i class="'+response.output[2]+'"></i> ');
						} else if ($('.rest_list_btn.'+response.btn+' i').length > 0 && response.output[2] == 'none') {
							$('.rest_list_btn.'+response.btn+' i').remove();
						}
					} else {
						$('.fro_woo_notices').html('<div class="woocommerce-error">' + response + '</div>');						
					}
					if (wrap.hasClass('ui-popup-active')) {
						wrapper.popup("close");
					}
					wrapper.unblock();
				}
			});
		
		},
		refresh_loc_list: function(e) {
			e.preventDefault();
			var wrapper		= $(this).parent().parent(),
				parent		= $(this).parent(),
				data		= {};
			
			$('a.active', parent).removeClass('active');
			$(this).addClass('active');
		
			wrapper.block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
			
			data.svals			= $(this).data('src');
			data.action			= 'frozr_adv_loc_filter';
			data.security		= frozr.frozr_adv_loc_filter_nonce;

			$.ajax({
				url: frozr.ajax_url,
				data: data,
				type: 'POST',
				success: function( response ) {
					if (response.error) {
						$('.fro_woo_notices').html('<div class="woocommerce-error">' + response.data + '</div>');						
					} else {
						$('.user_location_ul', wrapper).html(response);
					}
					wrapper.unblock();
				}
			});
		},
		show_tables_settings: function() {
			if ($(this).prop("checked")) {
				$('#usr_tables_opts .multi-field-wrapper').removeClass('frozr-hide');
			} else {
				$('#usr_tables_opts .multi-field-wrapper').addClass('frozr-hide');
			}
		},
		save_seller_settings: function(e) {
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
			
			data				= wrapper.serializeJSON();
			data.action			= 'frozr_seller_settings';
			data.security		= frozr.frozr_seller_settings_nonce;

			$.ajax({
				url: frozr.ajax_url,
				data: data,
				type: 'POST',
				success: function( response ) {
					$('.ajax-response', wrapper).html(response);
					wrapper.unblock();
				}
			});
		},
		show_rest_tables: function(e) {
			e.preventDefault();
			if ($('#table_seats_list', wrapper).val() !== '') {
			var wrapper     = $(this).parent().parent(),
				data        = {};

			wrapper.block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});

			data.usr			= $('#table_seats_list', wrapper).data('rest');
			data.seats			= $('#table_seats_list', wrapper).val();
			data.action			= 'frozr_get_tables_settings';
			data.security		= frozr.restaurant_tables_nonce;

			$.ajax({
				url: frozr.ajax_url,
				data: data,
				type: 'POST',
				success: function( response ) {
					if (response) {
						$('.rest_table_info_wrapper').html(response);
						$( wrapper ).popup( "reposition", {positionTo: "window"} );
					}
					wrapper.unblock();
				}
			});
			}
		},
		show_adtl_input: function(e) {
			e.preventDefault();
			
			var wrapper = $(this).parent().parent();
			if ($( "input#curbside_order_l_type", wrapper).prop("checked")) {
				if ($( "input#curbside_order_l_type", wrapper).hasClass('show_closed_order_notice')) {
					$(".dish_special_comments_field, .quantity, .single_add_to_cart_button", wrapper).hide(200);
					$(".closed_order_notice", wrapper).show(400);
					$(".open_closed_order_notice", wrapper).hide(200);
				} else if ($( "input#curbside_order_l_type", wrapper).hasClass('show_open_closed_order_notice')) {
					$(".dish_special_comments_field, .quantity, .single_add_to_cart_button", wrapper).show(400);
					$(".open_closed_order_notice", wrapper).show(400);
					$(".closed_order_notice", wrapper).hide(200);
					$(".order_car_info_field", wrapper).show(400);
					$(".lepop_rest_address", wrapper).show(400);
				} else {
				$(".order_car_info_field", wrapper).show(400);
				$(".lepop_rest_address", wrapper).show(400);
				}
			} else {
				$(".order_car_info_field", wrapper).hide(200);
				$(".lepop_rest_address", wrapper).hide(200);
			}
			if ($( "input#dine-in_order_l_type", wrapper).prop("checked")) {
				if ($( "input#dine-in_order_l_type", wrapper).hasClass('show_closed_order_notice')) {
					$(".dish_special_comments_field, .quantity, .single_add_to_cart_button", wrapper).hide(200);
					$(".closed_order_notice", wrapper).show(400);
					$(".open_closed_order_notice", wrapper).hide(200);
				} else if ($( "input#dine-in_order_l_type", wrapper).hasClass('show_open_closed_order_notice')) {
					$(".dish_special_comments_field, .quantity, .single_add_to_cart_button", wrapper).show(400);
					$(".open_closed_order_notice", wrapper).show(400);
					$(".order_ppl_num_field", wrapper).show(400);
					$(".closed_order_notice", wrapper).hide(200);
				} else {
					$(".order_ppl_num_field", wrapper).show(400);
				}
			} else {
				$(".order_ppl_num_field", wrapper).hide(200);
			}
			if ($( "input#delivery_order_l_type", wrapper).prop("checked")) {
				if (wrapper.parent().hasClass('no_delivery_location')) {
					$(".dish_special_comments_field, .quantity, .single_add_to_cart_button", wrapper).hide(200);
					$(".delivery_notice", wrapper).show(400);
					$(".closed_order_notice", wrapper).hide(200);
					$(".open_closed_order_notice", wrapper).hide(200);
				} else if ($( "input#delivery_order_l_type", wrapper).hasClass('show_open_closed_order_notice')) {
					$(".dish_special_comments_field, .quantity, .single_add_to_cart_button", wrapper).show(400);
					$(".open_closed_order_notice", wrapper).show(400);
					$(".delivery_notice", wrapper).show(400);
					$(".closed_order_notice", wrapper).hide(200);
				} else if ($( "input#delivery_order_l_type", wrapper).hasClass('show_closed_order_notice')) {
					$(".dish_special_comments_field, .quantity, .single_add_to_cart_button", wrapper).hide(200);
					$(".closed_order_notice", wrapper).show(400);
					$(".open_closed_order_notice", wrapper).hide(200);
				} else {
					$(".delivery_notice", wrapper).show(400);
				}
			} else {
				$(".delivery_notice", wrapper).hide(200);
			}
			if ($( "input#pickup_order_l_type", wrapper).prop("checked")) {
				if ($( "input#pickup_order_l_type", wrapper).hasClass('show_closed_order_notice')) {
					$(".dish_special_comments_field, .quantity, .single_add_to_cart_button", wrapper).hide(200);
					$(".closed_order_notice", wrapper).show(400);
					$(".open_closed_order_notice", wrapper).hide(200);
				} else if ($( "input#pickup_order_l_type", wrapper).hasClass('show_open_closed_order_notice')) {
					$(".dish_special_comments_field, .quantity, .single_add_to_cart_button", wrapper).show(400);
					$(".open_closed_order_notice", wrapper).show(400);
					$(".lepop_rest_address", wrapper).show(400);
					$(".closed_order_notice", wrapper).hide(200);
				} else {
					$(".lepop_rest_address", wrapper).show(400);
				}
			}
		},
		show_sp_comts: function(e) {
			e.preventDefault();
			$(this).next().slideToggle();
		},
		load_pop_dish_order: function(e) {
			e.preventDefault();
			
			$('.dish_quick_info, .pop_make_order_wrapper').slideToggle(400);
			$('span', this).text(function(i, text){
				return text === frozr.make_order_btn_txt ? frozr.make_order_b_btn_txt : frozr.make_order_btn_txt;
			});
			$('.wc-radios').trigger('change');
		},
		imageUpload: function(e) {
			e.preventDefault();

			var self = $(this),
				wrapper = self.parent().parent();

			// If the media frame already exists, reopen it.
			if ( file_frame_two ) {
				file_frame_two.open();
				return;
			}

			// Create the media frame.
			file_frame_two = wp.media.frames.file_frame_two = wp.media({
				title: jQuery( this ).data( 'uploader_title' ),
				button: {
					text: jQuery( this ).data( 'uploader_button_text' )
				},
				multiple: false
			});

			// When an image is selected, run a callback.
			file_frame_two.on( 'select', function() {
				var attachment = file_frame_two.state().get('selection').first().toJSON();
				
				wrapper.attr('data-imageid', attachment.id).css('background-image', 'url('+attachment.url+')').trigger('change');

				jQuery('input.frozr-banner-field', wrapper).val(attachment.id);
				jQuery('.frozr-banner-img, .src_adv_wrp_imgid', wrapper);
				jQuery('.frozr-banner .image-wrap', wrapper).removeClass('frozr-hide');

				jQuery('.button-area', wrapper).addClass('frozr-hide');
			});

			// Finally, open the modal
			file_frame_two.open();

		},
		gragatarImageUpload: function(e) {
			e.preventDefault();

			var self = $(this),
				wrapper = self.parent().parent();

			// If the media frame already exists, reopen it.
			if ( file_frame ) {
				file_frame.open();
				return;
			}

			// Create the media frame.
			file_frame = wp.media.frames.file_frame = wp.media({
				title: jQuery( this ).data( 'uploader_title' ),
				button: {
					text: jQuery( this ).data( 'uploader_button_text' )
				},
				multiple: false
			});
			
			// When an image is selected, run a callback.
			file_frame.on( 'select', function() {
				var attachment = file_frame.state().get('selection').first().toJSON();
				
				jQuery('input.frozr-gravatar-field', wrapper).val(attachment.id);
				jQuery('.frozr-gravatar-img', wrapper).css('background-image', 'url('+attachment.url+')');
				jQuery('.gravatar-wrap', wrapper).removeClass('frozr-hide');
				jQuery('.gravatar-button-area', wrapper).addClass('frozr-hide');
			});

			// Finally, open the modal
			file_frame.open();

		},
		removeBanner: function(e) {
			e.preventDefault();

			var self = $(this);
			var wrap = self.closest('.image-wrap');
			var instruction = wrap.siblings('.button-area');

			wrap.find('input.frozr-file-field').val('0');
			wrap.addClass('frozr-hide');
			instruction.removeClass('frozr-hide');
		},
		removeGravatar: function(e) {
			e.preventDefault();
	
			var self = $(this);
			var wrap = self.closest('.gravatar-wrap');
			var instruction = wrap.siblings('.gravatar-button-area');

			wrap.find('input.frozr-file-field').val('0');
			wrap.addClass('frozr-hide');
			instruction.removeClass('frozr-hide');
		},
		get_restaurant_fields: function() {
			var data = $( 'form#settings-form' ).serializeJSON();

			$( 'form#settings-form select' ).each( function( index, element ) {
				var select = $( element );
				data[ select.attr( 'name' ) ] = select.val();
			});

			return data;
		},
		save_restaurant_settings: function(e) {
			e.preventDefault();
			var wrapper     = $( '#settings-form' ),
				data        = {};

			wrapper.block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});

			data                 = Frozr_Settings.get_restaurant_fields();
			data.action          = 'frozr_save_restaurant_settings';
			data.security        = frozr.restaurant_settings_nonce;

			$.ajax({
				url: frozr.ajax_url,
				data: data,
				type: 'POST',
				success: function( response ) {
					if (response.success) {
						$('.fro_woo_notices').html('<div class="woocommerce-message">' + response.data + '</div>');
					}
					wrapper.unblock();
				}
			});
		},
		rating_login: function(e) {
			e.preventDefault();
			var wrapper     = $( '.rest_rating_form_wrapper' ),
				data        = {};

			wrapper.block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});

			data                 = $( 'form.rest_rating_login' ).serializeJSON();
			data.action          = 'frozr_rating_login';
			data.security        = frozr.rating_user_login;

			$.ajax({
				url: frozr.ajax_url,
				data: data,
				type: 'POST',
				success: function( response ) {
					if (response != '') {
						wrapper.prepend(response);
					} else {
					$( 'form.rest_rating_login' ).hide();
					$( 'form.rest_rating_form' ).show();
					}
					wrapper.unblock();
				}
			});
		},
		save_restaurant_rating: function(e) {
			e.preventDefault();
			var wrapper     = $( '.rest_rating_form_wrapper' ),
				restid     = $( '.rest_rating_submit', wrapper ).data('restid'),
				orderid     = $( '.rest_rating_submit', wrapper ).data('orderid'),
				 data        = {};

			wrapper.block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});

			data                 = $( 'form.rest_rating_form' ).serializeJSON();
			data.action          = 'frozr_save_rest_rating';
			data.order_id        = orderid;
			data.seller_id        = restid;
			data.security        = frozr.add_rest_review;

			$.ajax({
				url: frozr.ajax_url,
				data: data,
				type: 'POST',
				success: function( response ) {
					if (response != '') {
						wrapper.html(response);
					}
					wrapper.unblock();
					location.reload(true);
				}
			});
		},
		frozr_print_order: function(e) {
			e.preventDefault();
			var btn     	= $(this),
				wrapper     = btn.parent(),
				data		= {};
				 

			wrapper.block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
			data.action		= 'frozr_print_order';
			data.order_id	= btn.data('orderid');
			data.security	= frozr.frozr_dash_print;

			$.ajax({
				url: frozr.ajax_url,
				data: data,
				type: 'POST',
				success: function( response ) {
					wrapper.unblock();
					window.location.href=response.url;
				}
			});
		},
		print_summary_report: function(e) {
			e.preventDefault();
			var btn     	= $(this),
				wrapper     = btn.parent().closest('.dash_totals'),
				data		= {};
				 

			wrapper.block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
			data.action		= 'frozr_print_summary_report';
			data.rtype		= $('.show_resutl.active', wrapper).data('rtype');
			data.auser		= $('#seller_summary_select', wrapper).val();
			data.startd		= $('.dast_totals_start', wrapper).val();
			data.endd		= $('.dast_totals_end', wrapper).val();
			data.security	= frozr.frozr_dash_print;

			$.ajax({
				url: frozr.ajax_url,
				data: data,
				type: 'POST',
				success: function( response ) {
					wrapper.unblock();
					window.location.href=response.url;
				}
			});
		},
		dash_totals: function(e) {
			e.preventDefault();
			var btn     	= $(this),
				wrapper     = btn.parent().closest('.dash_totals'),
				data		= {};
				 

			wrapper.block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
			$('.show_resutl', wrapper).removeClass('active');
			data.action		= 'frozr_get_totals_data';
			data.rtype		= btn.data('rtype');
			data.auser		= $('#seller_summary_select', wrapper).val();
			data.startd		= $('.dast_totals_start', wrapper).val();
			data.endd		= $('.dast_totals_end', wrapper).val();
			data.security	= frozr.get_total_dash_rep;

			$.ajax({
				url: frozr.ajax_url,
				data: data,
				type: 'POST',
				success: function( response ) {
					btn.addClass('active');
					$('.dash_totals_results', wrapper).html(response);
					wrapper.unblock();
				}
			});
		},
		set_user_cookie: function(e) {
			e.preventDefault();
			var btn			= $(this),
				bynaft		= btn.data('aft'),
				srctyps		= btn.data('src'),
				wrapper		= btn.parent().closest('.user_location_ul'),
				data		= {};
				 

			wrapper.block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});

			data.action		= 'frozr_user_loc_cookie';
			data.userloc	= btn.data('loc');
			data.srctyp		= srctyps;
			data.useraft	= bynaft;
			data.security	= frozr.set_user_loc;

			$.ajax({
				url: frozr.ajax_url,
				data: data,
				type: 'POST',
				success: function( response ) {
					if (bynaft == 'refresh') {
						location.reload(true);
					}
					if (bynaft == 'check') {
						if (response) {
							$('.fro_woo_notices').html('<div class="woocommerce-message">' + response + '</div>');
							if ($(".dish-info, #loc_pop").parent().hasClass('ui-popup-active')) {
								$(".dish-info, #loc_pop").popup("close");
								$( ".dish-info, #loc_pop" ).popup({
								  afterclose: function( event, ui ) {location.reload(true);}
								});
							}
							wrapper.unblock();
						} else {
							if ($(".dish-info, #loc_pop").parent().hasClass('ui-popup-active')) {
							$(".dish-info, #loc_pop").popup("close");
							$( ".dish-info, #loc_pop" ).popup({
							  afterclose: function( event, ui ) {location.reload(true);}
							});
							} else {
								document.location.href="/";
							}
						}
					}
				}
			});
		},
		show_dash_totals_inputs: function(e) {
			e.preventDefault();
			$(this).next('form').toggle();
		},
		delete_dish: function(e) {
		if ( window.confirm( frozr.delete_dish ) ) {
			e.preventDefault();
			var btn     	= $(this),
				wrapper     = btn.parent().closest('tr'),
				data		= {};
				 

			wrapper.block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});

			data.action		= 'frozr_delete_dish';
			data.dishid		= btn.data('dish');
			data.security	= frozr.frozr_delete_dish_nonce;

			$.ajax({
				url: frozr.ajax_url,
				data: data,
				type: 'POST',
				success: function( response ) {
					if (response.success) {
						$('.fro_woo_notices').html('<div class="woocommerce-message">' + response.data + '</div>');
						wrapper.remove();
					} else {
						$('.fro_woo_notices').html('<div class="woocommerce-error">' + response.data + '</div>');						
					}
				}
			});
		}
		},
		show_dash_totals_inputs: function(e) {
			e.preventDefault();
			$(this).next('form').toggle();
		}
	};
	
	Frozr_Settings.init();

})(jQuery);