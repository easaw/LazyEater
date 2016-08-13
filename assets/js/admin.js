( function( $, wp, ajaxurl ) {
	$( function() {

	var Frozr_Admin_Settings = {
		init: function() {
			var self = this;
			// Delectation for Select2 script
			$(".lazyeater > form select").select2();
			self.frozr_default_show_hide_options('.frozr_fee_settings');
			
			//frozr sellers fees/commissions
			$('.frozr_fee_settings').on('click', '.frozr_add_new_rule', self.frozr_rule_duplicate);
			$('.frozr_fee_settings').on('click', '.frozr_edit_rule', self.frozr_edit_fee);
			$('.frozr_fee_settings').on('click', '.frozr_delete_rule', self.frozr_rule_delete);
			$('.frozr_fee_settings').on('click', '.frozr_back_to_fee_rules', self.frozr_rule_back);
			
			$( document.body )
				.on( 'frozr_fees_removed', '.frozr_fee_settings', self.reorder_seller_fee_rules )
				.on( 'frozr_new_fees_added', '.frozr_fee_settings', self.reorder_seller_fee_rules )
				.on( 'frozr_new_rule_removed', '.frozr_fee_settings', self.reorder_seller_fee_rules )
				.on('change', '.frozr_fee_settings .customers_effected select, .frozr_fee_settings .sellers_effected select', self.frozr_show_hide_options);
		},
		frozr_default_show_hide_options: function(table_wrap) {
			if ($('.customers_effected select', table_wrap).val() == 'all' ) {
				$('.customers', table_wrap).hide();
				$('.customers select', table_wrap).prop("disabled", true);
			}
			if ($('.sellers_effected select', table_wrap).val() == 'all' ) {
				$('.sellers', table_wrap).hide();
				$('.sellers select', table_wrap).prop("disabled", true);
			}
		},
		frozr_show_hide_options: function() {
			var wrapper = $('.frozr_fee_settings');
				var select_table = $( this ).closest('tr').next( 'tr' );

			if ( 'all_but' === $( this ).val() || 'specific' === $( this ).val()) {
				select_table.show();
				$('select', select_table).prop("disabled", false);
			} else {
				select_table.hide();
				$('select', select_table).prop("disabled", true);
			}

			wrapper.trigger( 'frozr_show_hide_options' );
			
		},
		frozr_rule_back: function() {
			var wrapper = $('.frozr_sellers_fee_table, .frozr_add_new_rule');
				$(this).parent().hide();
			wrapper.show();
			if ($('#fee_rule_new').length) {
				$('#fee_rule_new').remove();
				$('.frozr_fee_settings').trigger('frozr_new_rule_removed');
			}
		},
		frozr_edit_fee: function() {
			var r_div = $(this).data('rule'),
				wrapper = $('.frozr_sellers_fee_table, .frozr_add_new_rule');
				wrapper.hide();
				Frozr_Admin_Settings.frozr_default_show_hide_options('#'+r_div);
				$('#'+r_div).show();
		},
		frozr_rule_duplicate: function(e) {
			e.preventDefault();		
			var wrapper = $('.frozr_sellers_fee_table'),
				rules_table = $('.frozr_fee_settings');

			wrapper.block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
			$.ajax({
				url: frozr_admin.ajax_url,
				data: {
					action: 'frozr_add_fee_setting_row'
				},
				type: 'POST',
				success: function( response ) {
					rules_table.append(response);
					wrapper.hide();
					Frozr_Admin_Settings.frozr_default_show_hide_options('#fee_rule_new');
					$('.frozr_add_new_rule').hide();
					$('#fee_rule_new').show();
					$('.frozr_fee_empty_notice').hide();
					$('#fee_rule_new select').select2();
					wrapper.unblock();
					$('.frozr_fee_settings').trigger( 'frozr_new_fees_added' );
				}
			});
		},
		frozr_rule_delete: function(e) {
			e.preventDefault();
			var r_div = $(this).data('rule'),
				table = $(this).parent().parent();
				wrapper = $('.frozr_sellers_fee_table');

			table.remove();
			$('#'+r_div).remove();

			$('.frozr_fee_settings').trigger( 'frozr_fees_removed' );
		},
		reorder_seller_fee_rules: function () {
			var wrapper = $('.frozr_fee_settings');
			counter = 0;

			if ($('.frozr_seller_fee_rule', wrapper).length) {
			$('.frozr_seller_fee_rule', wrapper).each(function() {
				$('tr', this).each(function() {
					var rule = $(this),
						tdclass = rule.attr('class'),
						tds_new_classes = 'fro_settings[fro_lazy_fees]['+ counter +']['+ tdclass +']_field';

					rule.find('.fl-form-field').removeClass().addClass('fl-form-field ' + tds_new_classes);
					rule.find('label').attr('for', tds_new_classes);
					rule.find('input').attr({"id": tds_new_classes, "name": tds_new_classes});
					rule.find('select').attr({"id": tds_new_classes, "name": tds_new_classes});
				});
				counter++;
			});
			} else {
				$('.frozr_sellers_fee_table').hide();
				$('.frozr_fee_empty_notice').show();
			}

			wrapper.trigger( 'frozr_fees_reorderd' );
		},
	};
		
	Frozr_Admin_Settings.init();

	});
})( jQuery, wp, ajaxurl );