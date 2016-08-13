/*global frozr */
jQuery( function( $ ) {

	// Date Picker
	$( document.body ).on( 'wc-init-datepickers', function() {
		$( '.date-picker-field, .date-picker' ).datepicker({
			dateFormat: 'yy-mm-dd',
			numberOfMonths: 1,
			showButtonPanel: true
		});
	}).trigger( 'wc-init-datepickers' );

	// Meta-Boxes - Open/close
	$( '.wc-metaboxes-wrapper' ).on( 'click', '.wc-metabox h3', function( event ) {
		// If the user clicks on some form input inside the h3, like a select list (for variations), the box should not be toggled
		if ( $( event.target ).filter( ':input, option, .sort' ).length ) {
			return;
		}

		$( this ).next( '.wc-metabox-content' ).stop().slideToggle();
	})
	.on( 'click', '.expand_all', function() {
		$( this ).closest( '.wc-metaboxes-wrapper' ).find( '.wc-metabox > .wc-metabox-content' ).show();
		return false;
	})
	.on( 'click', '.close_all', function() {
		$( this ).closest( '.wc-metaboxes-wrapper' ).find( '.wc-metabox > .wc-metabox-content' ).hide();
		return false;
	});
	$( '.wc-metabox.closed' ).each( function() {
		$( this ).find( '.wc-metabox-content' ).hide();
	});
	//show fat value input
	$( "#dish_fat" ).change(function() {
		if ($(this).prop("checked")) {
			$('#dish_fat_rate').removeClass('frozr-hide');
		} else {
			$('#dish_fat_rate').addClass('frozr-hide');
		}
	});
	// PRODUCT TYPE SPECIFIC OPTIONS
	$( 'select#product-type' ).change( function () {

		// Get value
		var select_val = $( this ).val();

		if ( 'variable' === select_val ) {
			$( 'input#_manage_stock' ).change();
			$( 'input#_downloadable' ).prop( 'checked', false );
			$( 'input#_virtual' ).removeAttr( 'checked' );
		} else if ( 'grouped' === select_val ) {
			$( 'input#_downloadable' ).prop( 'checked', false );
			$( 'input#_virtual' ).removeAttr( 'checked' );
		} else if ( 'external' === select_val ) {
			$( 'input#_downloadable' ).prop( 'checked', false );
			$( 'input#_virtual' ).removeAttr( 'checked' );
		}

		show_and_hide_panels();

		$( 'ul.wc-tabs li:visible' ).eq( 0 ).find( 'a' ).click();

		$( document.body ).trigger( 'woocommerce-product-type-change', select_val, $( this ) );

	}).change();

	$( document.body ).on( 'woocommerce-product-type-change', function( e, select_val ) {
		if ( 'variable' !== select_val && 0 < $( '#variable_product_options' ).find( 'input[name^=variable_sku]' ).length && $( document.body ).triggerHandler( 'woocommerce-display-product-type-alert', select_val ) !== false ) {
			window.alert( frozr.i18n_product_type_alert );
		}
	});

	$( 'input#_downloadable, input#_virtual' ).change( function() {
		show_and_hide_panels();
	});

	function show_and_hide_panels() {
		var product_type    = $( 'select#product-type' ).val();
		var is_virtual      = $( 'input#_virtual:checked' ).size();
		var is_downloadable = $( 'input#_downloadable:checked' ).size();

		// Hide/Show all with rules
		var hide_classes = '.hide_if_downloadable, .hide_if_virtual';
		var show_classes = '.show_if_downloadable, .show_if_virtual, .show_if_external';

		$.each( frozr.product_types, function( index, value ) {
			hide_classes = hide_classes + ', .hide_if_' + value;
			show_classes = show_classes + ', .show_if_' + value;
		} );

		$( hide_classes ).show();
		$( show_classes ).hide();

		// Shows rules
		if ( is_downloadable ) {
			$( '.show_if_downloadable' ).show();
		}
		if ( is_virtual ) {
			$( '.show_if_virtual' ).show();
		}

        $( '.show_if_' + product_type ).show();

		// Hide rules
		if ( is_downloadable ) {
			$( '.hide_if_downloadable' ).hide();
		}
		if ( is_virtual ) {
			$( '.hide_if_virtual' ).hide();
		}

		$( '.hide_if_' + product_type ).hide();

		$( 'input#_manage_stock' ).change();
	}

	// Sale price schedule
	$( '.sale_price_dates_fields' ).each( function() {
		var $these_sale_dates = $( this );
		var sale_schedule_set = false;
		var $wrap = $these_sale_dates.closest( 'div, table' );

		$these_sale_dates.find( 'input' ).each( function() {
			if ( $( this ).val() !== '' ) {
				sale_schedule_set = true;
			}
		});

		if ( sale_schedule_set ) {
			$wrap.find( '.sale_schedule' ).hide();
			$wrap.find( '.sale_price_dates_fields' ).show();
		} else {
			$wrap.find( '.sale_schedule' ).show();
			$wrap.find( '.sale_price_dates_fields' ).hide();
		}
	});

	$( '#woocommerce-product-data' ).on( 'click', '.sale_schedule', function() {
		var $wrap = $( this ).closest( 'div, table' );

		$( this ).hide();
		$wrap.find( '.cancel_sale_schedule' ).show();
		$wrap.find( '.sale_price_dates_fields' ).show();

		return false;
	});
	$( '#woocommerce-product-data' ).on( 'click', '.cancel_sale_schedule', function() {
		var $wrap = $( this ).closest( 'div, table' );

		$( this ).hide();
		$wrap.find( '.sale_schedule' ).show();
		$wrap.find( '.sale_price_dates_fields' ).hide();
		$wrap.find( '.sale_price_dates_fields' ).find( 'input' ).val('');

		return false;
	});

	// File inputs
	$( '#woocommerce-product-data' ).on( 'click','.downloadable_files a.insert', function() {
		$( this ).closest( '.downloadable_files' ).find( 'tbody' ).append( $( this ).data( 'row' ) );
		return false;
	});
	$( '#woocommerce-product-data' ).on( 'click','.downloadable_files a.delete',function() {
		$( this ).closest( 'tr' ).remove();
		return false;
	});

	// STOCK OPTIONS
	$( 'input#_manage_stock' ).change( function() {
		if ( $( this ).is( ':checked' ) ) {
			$( 'div.stock_fields' ).show();
		} else {
			$( 'div.stock_fields' ).hide();
		}
	}).change();

	// DATE PICKER FIELDS
	$( '.sale_price_dates_fields' ).each( function() {
		var dates = $( this ).find( 'input' ).datepicker({
			defaultDate: '',
			dateFormat: 'yy-mm-dd',
			numberOfMonths: 1,
			showButtonPanel: true,
			onSelect: function( selectedDate ) {
				var option   = $( this ).is( '#_sale_price_dates_from, .sale_price_dates_from' ) ? 'minDate' : 'maxDate';
				var instance = $( this ).data( 'datepicker' );
				var date     = $.datepicker.parseDate( instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings );
				dates.not( this ).datepicker( 'option', option, date );
			}
		});
	});

	// ATTRIBUTE TABLES

	// Initial order
	var woocommerce_attribute_items = $('.product_attributes').find('.woocommerce_attribute').get();

	woocommerce_attribute_items.sort(function(a, b) {
	   var compA = parseInt( $( a ).attr( 'rel' ), 10 );
	   var compB = parseInt( $( b ).attr( 'rel' ), 10 );
	   return (compA < compB) ? -1 : (compA > compB) ? 1 : 0;
	});
	$( woocommerce_attribute_items ).each( function( idx, itm ) {
		$( '.product_attributes' ).append(itm);
	});

	function attribute_row_indexes() {
		$( '.product_attributes .woocommerce_attribute' ).each( function( index, el ) {
			$( '.attribute_position', el ).val( parseInt( $( el ).index( '.product_attributes .woocommerce_attribute' ), 10 ) );
		});
	}

	$( '.product_attributes .woocommerce_attribute' ).each( function( index, el ) {
		if ( $( el ).css( 'display' ) !== 'none' && $( el ).is( '.taxonomy' ) ) {
			$( 'select.attribute_taxonomy' ).find( 'option[value="' + $( el ).data( 'taxonomy' ) + '"]' ).attr( 'disabled', 'disabled' );
		}
	});

	$( '.product_attributes' ).on( 'blur', 'input.attribute_name', function() {
		$( this ).closest( '.woocommerce_attribute' ).find( 'strong.attribute_name' ).text( $( this ).val() );
	});

	$( '.product_attributes' ).on( 'click', 'button.select_all_attributes', function() {
		$( this ).closest( 'td' ).find( 'select option' ).attr( 'selected', 'selected' );
		$( this ).closest( 'td' ).find( 'select' ).change();
		return false;
	});

	$( '.product_attributes' ).on( 'click', 'button.select_no_attributes', function() {
		$( this ).closest( 'td' ).find( 'select option' ).removeAttr( 'selected' );
		$( this ).closest( 'td' ).find( 'select').change();
		return false;
	});

	$( '.product_attributes' ).on( 'click', '.remove_row', function() {
		if ( window.confirm( frozr.remove_attribute ) ) {
			var $parent = $( this ).parent().parent();

			if ( $parent.is( '.taxonomy' ) ) {
				$parent.find( 'select, input[type=text]' ).val('');
				$parent.hide();
				$( 'select.attribute_taxonomy' ).find( 'option[value="' + $parent.data( 'taxonomy' ) + '"]' ).removeAttr( 'disabled' );
			} else {
				$parent.find( 'select, input[type=text]' ).val('');
				$parent.hide();
				attribute_row_indexes();
			}
		}
		return false;
	});

	// Attribute ordering
	$( '.product_attributes' ).sortable({
		items: '.woocommerce_attribute',
		cursor: 'move',
		axis: 'y',
		handle: 'h3',
		scrollSensitivity: 40,
		forcePlaceholderSize: true,
		helper: 'clone',
		opacity: 0.65,
		placeholder: 'wc-metabox-sortable-placeholder',
		start: function( event, ui ) {
			ui.item.css( 'background-color', '#f6f6f6' );
		},
		stop: function( event, ui ) {
			ui.item.removeAttr( 'style' );
			attribute_row_indexes();
		}
	});

	// Add a new attribute (via ajax)
	$( '.product_attributes' ).on( 'click', 'button.add_new_attribute', function() {

		$( '.product_attributes' ).block({ message: null, overlayCSS: { background: '#fff', opacity: 0.6 } });

		var		$wrapper		= $( this ).closest( '.woocommerce_attribute' ),
				attribute		= $wrapper.data( 'taxonomy' ),
				new_attribute_name = window.prompt( frozr.new_attribute_prompt );

		if ( new_attribute_name ) {

			var data = {
				action:   'frozr_add_new_attribute',
				taxonomy: attribute,
				term:     new_attribute_name,
				security: frozr.add_attribute_nonce
			};

			$.post( frozr.ajax_url, data, function( response ) {
				if ( response.error ) {
					// Error
					window.alert( response.error );
				} else if ( response.slug ) {
					// Success
					$wrapper.find( 'select.attribute_values' ).append( '<option value="' + response.slug + '" selected="selected">' + response.name + '</option>' );
					$wrapper.find( 'select.attribute_values' ).change();
				}

				$( '.product_attributes' ).unblock();
			});

		} else {
			$( '.product_attributes' ).unblock();
		}

		return false;
	});

	// Save attributes and update variations
	$( '.save_attributes' ).on( 'click', function() {
		var id = $( '#product-edit input.pid' ).val();

		$( '#woocommerce-product-data' ).block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});
		
		var data = {
			post_id:  id,
			data:     $( '.product_attributes' ).find( 'input, select, textarea' ).serialize(),
			action:   'frozr_save_attributes',
			security: frozr.save_attributes_nonce
		};

		$.post( frozr.ajax_url, data, function() {
			// Reload variations panel
			var this_page = window.location.toString();
			this_page = this_page.replace( 'new_dish/', 'dishes/?product_id=' + id + '&action=edit&' );

			// Load variations panel
			$( '#variable_product_options' ).load( this_page + ' #variable_product_options_inner', function() {
				wc_meta_boxes_product_variations_actions.reload();
			});
			$( '#woocommerce-product-data' ).unblock();
			
		});
	});

	// Uploading files
	var downloadable_file_frame;
	var file_path_field;

	jQuery( document.body ).on( 'click', '.upload_file_button', function( event ) {
		var $el = $( this );

		file_path_field = $el.closest( 'tr' ).find( 'td.file_url input' );

		event.preventDefault();

		// If the media frame already exists, reopen it.
		if ( downloadable_file_frame ) {
			downloadable_file_frame.open();
			return;
		}

		var downloadable_file_states = [
			// Main states.
			new wp.media.controller.Library({
				library:   wp.media.query(),
				multiple:  true,
				title:     $el.data('choose'),
				priority:  20,
				filterable: 'uploaded'
			})
		];

		// Create the media frame.
		downloadable_file_frame = wp.media.frames.downloadable_file = wp.media({
			// Set the title of the modal.
			title: $el.data('choose'),
			library: {
				type: ''
			},
			button: {
				text: $el.data('update')
			},
			multiple: true,
			states: downloadable_file_states
		});

		// When an image is selected, run a callback.
		downloadable_file_frame.on( 'select', function() {
			var file_path = '';
			var selection = downloadable_file_frame.state().get( 'selection' );

			selection.map( function( attachment ) {
				attachment = attachment.toJSON();
				if ( attachment.url ) {
					file_path = attachment.url;
				}
			});

			file_path_field.val( file_path );
		});

		// Set post to 0 and set our custom type
		downloadable_file_frame.on( 'ready', function() {
			downloadable_file_frame.uploader.options.uploader.params = {
				type: 'downloadable_product'
			};
		});

		// Finally, open the modal.
		downloadable_file_frame.open();
	});

	// Download ordering
	jQuery( '.downloadable_files tbody' ).sortable({
		items: 'tr',
		cursor: 'move',
		axis: 'y',
		handle: 'td.sort',
		scrollSensitivity: 40,
		forcePlaceholderSize: true,
		helper: 'clone',
		opacity: 0.65
	});

	// Product gallery file uploads
	var product_gallery_frame;
	var $image_gallery_ids = $( '#product_image_gallery' );
	var $product_images    = $( '#product_images_container' ).find( 'ul.product_images' );

	jQuery( '.add_product_images' ).on( 'click', 'a', function( event ) {
		var $el = $( this );

		event.preventDefault();

		// If the media frame already exists, reopen it.
		if ( product_gallery_frame ) {
			product_gallery_frame.open();
			return;
		}

		// Create the media frame.
		product_gallery_frame = wp.media.frames.product_gallery = wp.media({
			// Set the title of the modal.
			title: $el.data( 'choose' ),
			button: {
				text: $el.data( 'update' )
			},
			states: [
				new wp.media.controller.Library({
					title: $el.data( 'choose' ),
					filterable: 'all',
					multiple: true
				})
			]
		});

		// When an image is selected, run a callback.
		product_gallery_frame.on( 'select', function() {
			var selection = product_gallery_frame.state().get( 'selection' );
			var attachment_ids = $image_gallery_ids.val();

			selection.map( function( attachment ) {
				attachment = attachment.toJSON();

				if ( attachment.id ) {
					attachment_ids   = attachment_ids ? attachment_ids + ',' + attachment.id : attachment.id;
					var attachment_image = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;

					$product_images.append( '<li class="image" data-attachment_id="' + attachment.id + '"><img src="' + attachment_image + '" /><ul class="actions"><li><a href="#" class="delete" title="' + $el.data('delete') + '"><i class="fs-icon-close"></i></a></li></ul></li>' );
				}
			});

			$image_gallery_ids.val( attachment_ids );
		});

		// Finally, open the modal.
		product_gallery_frame.open();
		
		//make the body updateable
		$('#product-edit select:not(.variations-defaults select)').trigger('change');
	});

	// Image ordering
	$product_images.sortable({
		items: 'li.image',
		cursor: 'move',
		scrollSensitivity: 40,
		forcePlaceholderSize: true,
		forceHelperSize: false,
		helper: 'clone',
		opacity: 0.65,
		placeholder: 'wc-metabox-sortable-placeholder',
		start: function( event, ui ) {
			ui.item.css( 'background-color', '#f6f6f6' );
		},
		stop: function( event, ui ) {
			ui.item.removeAttr( 'style' );
		},
		update: function() {
			var attachment_ids = '';

			$( '#product_images_container' ).find( 'ul li.image' ).css( 'cursor', 'default' ).each( function() {
				var attachment_id = jQuery( this ).attr( 'data-attachment_id' );
				attachment_ids = attachment_ids + attachment_id + ',';
			});

			$image_gallery_ids.val( attachment_ids );
		}
	});

	// Remove images
	$( '#product_images_container' ).on( 'click', 'a.delete', function() {
		$( this ).closest( 'li.image' ).remove();

		var attachment_ids = '';

		$( '#product_images_container' ).find( 'ul li.image' ).css( 'cursor', 'default' ).each( function() {
			var attachment_id = jQuery( this ).attr( 'data-attachment_id' );
			attachment_ids = attachment_ids + attachment_id + ',';
		});

		$image_gallery_ids.val( attachment_ids );

		//make the body updateable
		$('#product-edit select:not(.variations-defaults select)').trigger('change');

		return false;
	});
	
    // featured image
    var product_featured_frame;
    $('#product-edit form').on('click', 'a.frozr-feat-image-btn', function(e) {
        e.preventDefault();

        var self = $(this);

        if ( product_featured_frame ) {
            product_featured_frame.open();
            return;
        }

        product_featured_frame = wp.media({
            // Set the title of the modal.
            title: 'Upload featured image',
            button: {
                text: 'Set featured image',
            }
        });

        product_featured_frame.on('select', function() {
            var selection = product_featured_frame.state().get('selection');

            selection.map( function( attachment ) {
                attachment = attachment.toJSON();

                console.log(attachment, self);
                // set the image hidden id
                self.siblings('input.frozr-feat-image-id').val(attachment.id).change();

                // set the image
                var instruction = self.closest('.instruction-inside');
                var wrap = instruction.siblings('.image-wrap');

                // wrap.find('img').attr('src', attachment.sizes.thumbnail.url);
                wrap.find('div.product-photo').css("background-image","url(" + attachment.url + ")");

                instruction.addClass('frozr-hide');
                wrap.removeClass('frozr-hide');
            });
        });

        product_featured_frame.open();
		
	});
    $('#product-edit form').on('click', 'a.frozr-remove-feat-image', function(e) {
        e.preventDefault();

        var self = $(this);
        var wrap = self.closest('.image-wrap');
        var instruction = wrap.siblings('.instruction-inside');

        instruction.find('input.frozr-feat-image-id').val('0').change();
        wrap.addClass('frozr-hide');
        instruction.removeClass('frozr-hide');
	});
	/**
	 * Variations actions
	 */
	var wc_meta_boxes_product_variations_actions = {

		/**
		 * Initialize variations actions
		 */
		init: function() {
			$( '#variable_product_options' )
				.on( 'change', 'input.variable_is_downloadable', this.variable_is_downloadable )
				.on( 'change', 'input.variable_is_virtual', this.variable_is_virtual )
				.on( 'change', 'input.variable_manage_stock', this.variable_manage_stock )
				.on( 'click', 'h3 .sort', this.set_menu_order )
				.on( 'reload', this.reload );

			$( 'input.variable_is_downloadable, input.variable_is_virtual, input.variable_manage_stock' ).change();
			$( document.body ).on( 'woocommerce_variations_added', this.variation_added );

            // post status change
            $('.frozr-toggle-sidebar').on('click', 'a.frozr-toggle-edit', this.sidebarToggle.showStatus);
            $('.frozr-toggle-sidebar').on('click', 'a.frozr-toggle-save', this.sidebarToggle.saveStatus);
            $('.frozr-toggle-sidebar').on('click', 'a.frozr-toggle-cacnel', this.sidebarToggle.cancel);
		},

        sidebarToggle: {
            showStatus: function(e) {
                var container = $(this).siblings('.frozr-toggle-select-container');

                if (container.is(':hidden')) {
                    container.slideDown('fast');

                    $(this).hide();
                }

                return false;
            },

            saveStatus: function(e) {
                var container = $(this).closest('.frozr-toggle-select-container');

                container.slideUp('fast');
                container.siblings('a.frozr-toggle-edit').show();

                // update the text
                var text = $('option:selected', container.find('select.frozr-toggle-select')).text();
                container.siblings('.frozr-toggle-selected-display').html(text);

                return false;
            },

            cancel: function(e) {
                var container = $(this).closest('.frozr-toggle-select-container');

                container.slideUp('fast');
                container.siblings('a.frozr-toggle-edit').show();

                return false;
            }
        },

		/**
		 * Reload UI
		 *
		 * @param {Object} event
		 * @param {Int} qty
		 */
		reload: function() {
			wc_meta_boxes_product_variations_ajax.load_variations( 1 );
		},

		/**
		 * Check if variation is downloadable and show/hide elements
		 */
		variable_is_downloadable: function() {
			$( this ).closest( '.woocommerce_variation' ).find( '.show_if_variation_downloadable' ).hide();

			if ( $( this ).is( ':checked' ) ) {
				$( this ).closest( '.woocommerce_variation' ).find( '.show_if_variation_downloadable' ).show();
			}
		},

		/**
		 * Check if variation is virtual and show/hide elements
		 */
		variable_is_virtual: function() {
			$( this ).closest( '.woocommerce_variation' ).find( '.hide_if_variation_virtual' ).show();

			if ( $( this ).is( ':checked' ) ) {
				$( this ).closest( '.woocommerce_variation' ).find( '.hide_if_variation_virtual' ).hide();
			}
		},

		/**
		 * Check if variation manage stock and show/hide elements
		 */
		variable_manage_stock: function() {
			$( this ).closest( '.woocommerce_variation' ).find( '.show_if_variation_manage_stock' ).hide();

			if ( $( this ).is( ':checked' ) ) {
				$( this ).closest( '.woocommerce_variation' ).find( '.show_if_variation_manage_stock' ).show();
			}
		},

		/**
		 * Run actions when variations is loaded
		 *
		 * @param {Object} event
		 * @param {Int} needsUpdate
		 */
		variations_loaded: function( event, needsUpdate ) {
			needsUpdate = needsUpdate || false;

			var wrapper = $( '#woocommerce-product-data' );

			if ( ! needsUpdate ) {
				// Show/hide downloadable, virtual and stock fields
				$( 'input.variable_is_downloadable, input.variable_is_virtual, input.variable_manage_stock', wrapper ).change();

				// Open sale schedule fields when have some sale price date
				$( '.woocommerce_variation', wrapper ).each( function( index, el ) {
					var $el       = $( el ),
						date_from = $( '.sale_price_dates_from', $el ).val(),
						date_to   = $( '.sale_price_dates_to', $el ).val();

					if ( '' !== date_from || '' !== date_to ) {
						$( 'a.sale_schedule', $el ).click();
					}
				});

				// Remove variation-needs-update classes
				$( '.woocommerce_variations .variation-needs-update', wrapper ).removeClass( 'variation-needs-update' );

				// Disable cancel and save buttons
				$( 'button.cancel-variation-changes, button.save-variation-changes', wrapper ).attr( 'disabled', 'disabled' );
			}

			// Datepicker fields
			$( '.sale_price_dates_fields', wrapper ).each( function() {
				var dates = $( this ).find( 'input' ).datepicker({
					defaultDate:     '',
					dateFormat:      'yy-mm-dd',
					numberOfMonths:  1,
					showButtonPanel: true,
					onSelect:        function( selectedDate ) {
						var option   = $( this ).is( '.sale_price_dates_from' ) ? 'minDate' : 'maxDate',
							instance = $( this ).data( 'datepicker' ),
							date     = $.datepicker.parseDate( instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings );

						dates.not( this ).datepicker( 'option', option, date );
						$( this ).change();
					}
				});
			});

			// Allow sorting
			$( '.woocommerce_variations', wrapper ).sortable({
				items:                '.woocommerce_variation',
				cursor:               'move',
				axis:                 'y',
				handle:               '.sort',
				scrollSensitivity:    40,
				forcePlaceholderSize: true,
				helper:               'clone',
				opacity:              0.65,
				stop:                 function() {
				    wc_meta_boxes_product_variations_actions.variation_row_indexes();
				}
			});

			$( document.body ).trigger( 'wc-enhanced-select-init' );
		},

		/**
		 * Run actions when added a variation
		 *
		 * @param {Object} event
		 * @param {Int} qty
		 */
		variation_added: function( event, qty ) {
			if ( 1 === qty ) {
				wc_meta_boxes_product_variations_actions.variations_loaded( null, true );
			}
		},

		/**
		 * Lets the user manually input menu order to move items around pages
		 */
		set_menu_order: function( event ) {
			event.preventDefault();
			var $menu_order  = $( this ).closest( '.woocommerce_variation' ).find('.variation_menu_order');
			var value        = window.prompt( frozr.i18n_enter_menu_order, $menu_order.val() );

			if ( value != null ) {
				// Set value, save changes and reload view
				$menu_order.val( parseInt( value, 10 ) ).change();
				wc_meta_boxes_product_variations_ajax.save_variations();
			}
		},

		/**
		 * Set menu order
		 */
		variation_row_indexes: function() {
			var wrapper      = $( '#variable_product_options' ).find( '.woocommerce_variations' ),
				current_page = parseInt( wrapper.attr( 'data-page' ), 10 ),
				offset       = parseInt( ( current_page - 1 ) * frozr.variations_per_page, 10 );

			$( '.woocommerce_variations .woocommerce_variation' ).each( function ( index, el ) {
				$( '.variation_menu_order', el ).val( parseInt( $( el ).index( '.woocommerce_variations .woocommerce_variation' ), 10 ) + 1 + offset ).change();
			});
		}
	};

	/**
	 * Variations media actions
	 */
	var wc_meta_boxes_product_variations_media = {

		/**
		 * wp.media frame object
		 *
		 * @type {Object}
		 */
		variable_image_frame: null,

		/**
		 * Variation image ID
		 *
		 * @type {Int}
		 */
		setting_variation_image_id: null,

		/**
		 * Variation image object
		 *
		 * @type {Object}
		 */
		setting_variation_image: null,

		/**
		 * wp.media post ID
		 *
		 * @type {Int}
		 */
		wp_media_post_id: wp.media.model.settings.post.id,

		/**
		 * Initialize media actions
		 */
		init: function() {
			$( '#variable_product_options' ).on( 'click', '.upload_image_button', this.add_image );
			$( 'a.add_media' ).on( 'click', this.restore_wp_media_post_id );
		},

		/**
		 * Added new image
		 *
		 * @param {Object} event
		 */
		add_image: function( event ) {
			var $button = $( this ),
				post_id = $button.attr( 'rel' ),
				$parent = $button.closest( '.upload_image' );

			wc_meta_boxes_product_variations_media.setting_variation_image    = $parent;
			wc_meta_boxes_product_variations_media.setting_variation_image_id = post_id;

			event.preventDefault();

			if ( $button.is( '.remove' ) ) {

				$( '.upload_image_id', wc_meta_boxes_product_variations_media.setting_variation_image ).val( '' ).change();
				wc_meta_boxes_product_variations_media.setting_variation_image.find( 'img' ).eq( 0 ).attr( 'src', frozr.woocommerce_placeholder_img_src );
				wc_meta_boxes_product_variations_media.setting_variation_image.find( '.upload_image_button' ).removeClass( 'remove' );

			} else {

				// If the media frame already exists, reopen it.
				if ( wc_meta_boxes_product_variations_media.variable_image_frame ) {
					wc_meta_boxes_product_variations_media.variable_image_frame.uploader.uploader.param( 'post_id', wc_meta_boxes_product_variations_media.setting_variation_image_id );
					wc_meta_boxes_product_variations_media.variable_image_frame.open();
					return;
				} else {
					wp.media.model.settings.post.id = wc_meta_boxes_product_variations_media.setting_variation_image_id;
				}

				// Create the media frame.
				wc_meta_boxes_product_variations_media.variable_image_frame = wp.media.frames.variable_image = wp.media({
					// Set the title of the modal.
					title: frozr.i18n_choose_image,
					button: {
						text: frozr.i18n_set_image
					},
					states: [
						new wp.media.controller.Library({
							title: frozr.i18n_choose_image,
							filterable: 'all'
						})
					]
				});

				// When an image is selected, run a callback.
				wc_meta_boxes_product_variations_media.variable_image_frame.on( 'select', function () {

					var attachment = wc_meta_boxes_product_variations_media.variable_image_frame.state().get( 'selection' ).first().toJSON(),
						url = attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;

					$( '.upload_image_id', wc_meta_boxes_product_variations_media.setting_variation_image ).val( attachment.id ).change();
					wc_meta_boxes_product_variations_media.setting_variation_image.find( '.upload_image_button' ).addClass( 'remove' );
					wc_meta_boxes_product_variations_media.setting_variation_image.find( 'img' ).eq( 0 ).attr( 'src', url );

					wp.media.model.settings.post.id = wc_meta_boxes_product_variations_media.wp_media_post_id;
				});

				// Finally, open the modal.
				wc_meta_boxes_product_variations_media.variable_image_frame.open();
			}
		},

		/**
		 * Restore wp.media post ID.
		 */
		restore_wp_media_post_id: function() {
			wp.media.model.settings.post.id = wc_meta_boxes_product_variations_media.wp_media_post_id;
		}
	};

	/**
	 * Product variations metabox ajax methods
	 */
	var wc_meta_boxes_product_variations_ajax = {

		/**
		 * Initialize variations ajax methods
		 */
		init: function() {
			$( 'li.variations_tab a' ).on( 'click', this.initial_load );

			$( '#variable_product_options' )
				.on( 'click', 'button.save-variation-changes', this.save_variations )
				.on( 'click', 'button.cancel-variation-changes', this.cancel_variations )
				.on( 'click', '.remove_variation', this.remove_variation );

			$( document.body )
				.on( 'change', '#variable_product_options .woocommerce_variations :input', this.input_changed )
				.on( 'change', '.variations-defaults select', this.defaults_changed );

			$( '#update_product' ).on( 'submit', this.save_on_submit );

			$( '.wc-metaboxes-wrapper' ).on( 'click', 'a.do_variation_action', this.do_variation_action );
		},

		/**
		 * Check if have some changes before leave the page
		 *
		 * @return {Bool}
		 */
		check_for_changes: function() {
			var need_update = $( '#variable_product_options' ).find( '.woocommerce_variations .variation-needs-update' );

			if ( 0 < need_update.length ) {
				if ( window.confirm( frozr.i18n_edited_variations ) ) {
					wc_meta_boxes_product_variations_ajax.save_changes();
				} else {
					need_update.removeClass( 'variation-needs-update' );
					return false;
				}
			}

			return true;
		},

		/**
		 * Block edit screen
		 */
		block: function() {
			$( '#woocommerce-product-data' ).block({
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
			$( '#woocommerce-product-data' ).unblock();
		},

		/**
		 * Initial load variations
		 *
		 * @return {Bool}
		 */
		initial_load: function() {
			if ( 0 === $( '#variable_product_options' ).find( '.woocommerce_variations .woocommerce_variation' ).length ) {
				wc_meta_boxes_product_variations_pagenav.go_to_page();
			}
		},

		/**
		 * Load variations via Ajax
		 *
		 * @param {Int} page (default: 1)
		 * @param {Int} per_page (default: 10)
		 */
		load_variations: function( page, per_page ) {
			page     = page || 1;
			per_page = per_page || frozr.variations_per_page;

			var wrapper = $( '#variable_product_options' ).find( '.woocommerce_variations' );

			wc_meta_boxes_product_variations_ajax.block();

			var id = $( '#product-edit input.pid' ).val();

			$.ajax({
				url: frozr.ajax_url,
				data: {
					action:     'frozr_load_variations',
					security:   frozr.load_variations_nonce,
					product_id: id,
					attributes: wrapper.data( 'attributes' ),
					page:       page,
					per_page:   per_page
				},
				type: 'POST',
				success: function( response ) {
				console.log(response);
					wrapper.empty().append( response ).attr( 'data-page', page );
					
					wc_meta_boxes_product_variations_actions.variations_loaded();

					wc_meta_boxes_product_variations_ajax.unblock();
				}
			});
		},

		/**
		 * Ger variations fields and convert to object
		 *
		 * @param  {Object} fields
		 *
		 * @return {Object}
		 */
		get_variations_fields: function( fields ) {
			var data = $( ':input', fields ).serializeJSON();

			$( '.variations-defaults select' ).each( function( index, element ) {
				var select = $( element );
				data[ select.attr( 'name' ) ] = select.val();
			});

			return data;
		},

		/**
		 * Save variations changes
		 *
		 * @param {Function} callback Called once saving is complete
		 */
		save_changes: function( callback ) {
			var wrapper     = $( '#variable_product_options' ).find( '.woocommerce_variations' ),
				need_update = $( '.variation-needs-update', wrapper ),
				id 			= $( '#product-edit input.pid' ).val(),
				data        = {};

			// Save only with products need update.
			if ( 0 < need_update.length ) {
				wc_meta_boxes_product_variations_ajax.block();

				data                 = wc_meta_boxes_product_variations_ajax.get_variations_fields( need_update );
				data.action          = 'frozr_save_variations';
				data.security        = frozr.save_variations_nonce;
				data.product_id      = id;
				data['product-type'] = $( '#product-type' ).val();

				$.ajax({
					url: frozr.ajax_url,
					data: data,
					type: 'POST',
					success: function( response ) {
						// Allow change page, delete and add new variations
						need_update.removeClass( 'variation-needs-update' );
						$( 'button.cancel-variation-changes, button.save-variation-changes' ).attr( 'disabled', 'disabled' );

						$( '#woocommerce-product-data' ).trigger( 'woocommerce_variations_saved' );

						if ( typeof callback === 'function' ) {
							callback( response );
						}

						wc_meta_boxes_product_variations_ajax.unblock();
					}
				});
			}
		},

		/**
		 * Save variations
		 *
		 * @return {Bool}
		 */
		save_variations: function() {
			$( '#variable_product_options' ).trigger( 'woocommerce_variations_save_variations_button' );

			wc_meta_boxes_product_variations_ajax.save_changes( function( error ) {
				var wrapper = $( '#variable_product_options' ).find( '.woocommerce_variations' ),
					current = wrapper.attr( 'data-page' );

				$( '#variable_product_options' ).find( '#woocommerce_errors' ).remove();

				if ( error ) {
					wrapper.before( error );
				}

				$( '.variations-defaults select' ).each( function() {
					$( this ).attr( 'data-current', $( this ).val() );
				});

				wc_meta_boxes_product_variations_pagenav.go_to_page( current );
			});

			return false;
		},

		/**
		 * Save on post form submit
		 */
		save_on_submit: function( e ) {
			var need_update = $( '#variable_product_options' ).find( '.woocommerce_variations .variation-needs-update' );

			if ( 0 < need_update.length ) {
				e.preventDefault();
				$( '#variable_product_options' ).trigger( 'woocommerce_variations_save_variations_on_submit' );
				wc_meta_boxes_product_variations_ajax.save_changes( wc_meta_boxes_product_variations_ajax.save_on_submit_done );
			}
		},

		/**
		 * After saved, continue with form submission
		 */
		save_on_submit_done: function() {
			$( '#update_product' ).submit();
		},

		/**
		 * Discart changes.
		 *
		 * @return {Bool}
		 */
		cancel_variations: function() {
			var current = parseInt( $( '#variable_product_options' ).find( '.woocommerce_variations' ).attr( 'data-page' ), 10 );

			$( '#variable_product_options' ).find( '.woocommerce_variations .variation-needs-update' ).removeClass( 'variation-needs-update' );
			$( '.variations-defaults select' ).each( function() {
				$( this ).val( $( this ).attr( 'data-current' ) );
			});

			wc_meta_boxes_product_variations_pagenav.go_to_page( current );

			return false;
		},

		/**
		 * Add variation
		 *
		 * @return {Bool}
		 */
		add_variation: function() {
			wc_meta_boxes_product_variations_ajax.block();

			var id = $( '#product-edit input.pid' ).val();
				data = {
				action: 'frozr_add_variation',
				post_id: id,
				loop: $( '.woocommerce_variation' ).size(),
				security: frozr.add_variation_nonce
			};

			$.post( frozr.ajax_url, data, function( response ) {
				var variation = $( response );
				variation.addClass( 'variation-needs-update' );

				$( '#variable_product_options' ).find( '.woocommerce_variations' ).prepend( variation );
				$( 'button.cancel-variation-changes, button.save-variation-changes' ).removeAttr( 'disabled' );
				$( '#variable_product_options' ).trigger( 'woocommerce_variations_added', 1 );
				wc_meta_boxes_product_variations_ajax.unblock();
			});

			return false;
		},

		/**
		 * Remove variation
		 *
		 * @return {Bool}
		 */
		remove_variation: function() {
			wc_meta_boxes_product_variations_ajax.check_for_changes();

			if ( window.confirm( frozr.i18n_remove_variation ) ) {
				var variation     = $( this ).attr( 'rel' ),
					variation_ids = [],
					data          = {
						action: 'frozr_remove_variations'
					};

				wc_meta_boxes_product_variations_ajax.block();

				if ( 0 < variation ) {
					variation_ids.push( variation );

					data.variation_ids = variation_ids;
					data.security      = frozr.delete_variations_nonce;

					$.post( frozr.ajax_url, data, function() {
						var wrapper      = $( '#variable_product_options' ).find( '.woocommerce_variations' ),
							current_page = parseInt( wrapper.attr( 'data-page' ), 10 ),
							total_pages  = Math.ceil( ( parseInt( wrapper.attr( 'data-total' ), 10 ) - 1 ) / frozr.variations_per_page ),
							page         = 1;

						$( '#woocommerce-product-data' ).trigger( 'woocommerce_variations_removed' );

						if ( current_page === total_pages || current_page <= total_pages ) {
							page = current_page;
						} else if ( current_page > total_pages && 0 !== total_pages ) {
							page = total_pages;
						}

						wc_meta_boxes_product_variations_pagenav.go_to_page( page, -1 );
					});

				} else {
					wc_meta_boxes_product_variations_ajax.unblock();
				}
			}

			return false;
		},

		/**
		 * Link all variations (or at least try :p)
		 *
		 * @return {Bool}
		 */
		link_all_variations: function() {
			wc_meta_boxes_product_variations_ajax.check_for_changes();

			if ( window.confirm( frozr.i18n_link_all_variations ) ) {
				wc_meta_boxes_product_variations_ajax.block();

				var id = $( '#product-edit input.pid' ).val(),
					data = {
					action: 'frozr_link_all_variations',
					post_id: id,
					security: frozr.link_variation_nonce
				};

				$.post( frozr.ajax_url, data, function( response ) {
					var count = parseInt( response, 10 );

					if ( 1 === count ) {
						window.alert( count + ' ' + frozr.i18n_variation_added );
					} else if ( 0 === count || count > 1 ) {
						window.alert( count + ' ' + frozr.i18n_variations_added );
					} else {
						window.alert( frozr.i18n_no_variations_added );
					}

					if ( count > 0 ) {
						wc_meta_boxes_product_variations_pagenav.go_to_page( 1, count );
						$( '#variable_product_options' ).trigger( 'woocommerce_variations_added', count );
					} else {
						wc_meta_boxes_product_variations_ajax.unblock();
					}
				});
			}

			return false;
		},

		/**
		 * Add new class when have changes in some input
		 */
		input_changed: function() {
			$( this )
				.closest( '.woocommerce_variation' )
				.addClass( 'variation-needs-update' );

			$( 'button.cancel-variation-changes, button.save-variation-changes' ).removeAttr( 'disabled' );

			$( '#variable_product_options' ).trigger( 'woocommerce_variations_input_changed' );
		},
		
		/**
		 * Added new .variation-needs-update class when defaults is changed
		 */
		defaults_changed: function() {
			$( this )
				.closest( '#variable_product_options' )
				.find( '.woocommerce_variation:first' )
				.addClass( 'variation-needs-update' );

			$( 'button.cancel-variation-changes, button.save-variation-changes' ).removeAttr( 'disabled' );

			$( '#variable_product_options' ).trigger( 'woocommerce_variations_defaults_changed' );
		},

		/**
		 * Actions
		 */
		do_variation_action: function() {
			var do_variation_action = $( 'select.variation_actions' ).val(),
				data       = {},
				changes    = 0,
				value;

			switch ( do_variation_action ) {
				case 'add_variation' :
					wc_meta_boxes_product_variations_ajax.add_variation();
					return;
				case 'link_all_variations' :
					wc_meta_boxes_product_variations_ajax.link_all_variations();
					return;
				case 'delete_all' :
					if ( window.confirm( frozr.i18n_delete_all_variations ) ) {
						if ( window.confirm( frozr.i18n_last_warning ) ) {
							data.allowed = true;
							changes      = parseInt( $( '#variable_product_options' ).find( '.woocommerce_variations' ).attr( 'data-total' ), 10 ) * -1;
						}
					}
					break;
				case 'variable_regular_price_increase' :
				case 'variable_regular_price_decrease' :
				case 'variable_sale_price_increase' :
				case 'variable_sale_price_decrease' :
					value = window.prompt( frozr.i18n_enter_a_value_fixed_or_percent );

					if ( value != null ) {
						if ( value.indexOf( '%' ) >= 0 ) {
							data.value = accounting.unformat( value.replace( /\%/, '' ), frozr.mon_decimal_point ) + '%';
						} else {
							data.value = accounting.unformat( value, frozr.mon_decimal_point );
						}
					}
					break;
				case 'variable_regular_price' :
				case 'variable_sale_price' :
				case 'variable_stock' :
				case 'variable_weight' :
				case 'variable_length' :
				case 'variable_width' :
				case 'variable_height' :
				case 'variable_download_limit' :
				case 'variable_download_expiry' :
					value = window.prompt( frozr.i18n_enter_a_value );

					if ( value != null ) {
						data.value = value;
					}
					break;
				case 'variable_sale_schedule' :
					data.date_from = window.prompt( frozr.i18n_scheduled_sale_start );
					data.date_to   = window.prompt( frozr.i18n_scheduled_sale_end );

					if ( null === data.date_from ) {
						data.date_from = false;
					}

					if ( null === data.date_to ) {
						data.date_to = false;
					}
					break;
				default :
					$( 'select.variation_actions' ).trigger( do_variation_action );
					data = $( 'select.variation_actions' ).triggerHandler( do_variation_action + '_ajax_data', data );
					break;
			}

			if ( 'delete_all' === do_variation_action && data.allowed ) {
				$( '#variable_product_options' ).find( '.variation-needs-update' ).removeClass( 'variation-needs-update' );
			} else {
				wc_meta_boxes_product_variations_ajax.check_for_changes();
			}

			wc_meta_boxes_product_variations_ajax.block();
			
			var id = $( '#product-edit input.pid' ).val();

			$.ajax({
				url: frozr.ajax_url,
				data: {
					action:      'frozr_bulk_edit_variations',
					security:    frozr.bulk_edit_variations_nonce,
					product_id:  id,
					bulk_action: do_variation_action,
					data:        data
				},
				type: 'POST',
				success: function() {
					wc_meta_boxes_product_variations_pagenav.go_to_page( 1, changes );
				}
			});
		}
	};

	/**
	 * Product variations pagenav
	 */
	var wc_meta_boxes_product_variations_pagenav = {

		/**
		 * Initialize products variations meta box
		 */
		init: function() {
			$( document.body )
				.on( 'woocommerce_variations_added', this.update_single_quantity )
				.on( 'change', '.variations-pagenav .page-selector', this.page_selector )
				.on( 'click', '.variations-pagenav .first-page', this.first_page )
				.on( 'click', '.variations-pagenav .prev-page', this.prev_page )
				.on( 'click', '.variations-pagenav .next-page', this.next_page )
				.on( 'click', '.variations-pagenav .last-page', this.last_page );
		},

		/**
		 * Set variations count
		 *
		 * @param {Int} qty
		 *
		 * @return {Int}
		 */
		update_variations_count: function( qty ) {
			var wrapper        = $( '#variable_product_options' ).find( '.woocommerce_variations' ),
				total          = parseInt( wrapper.attr( 'data-total' ), 10 ) + qty,
				displaying_num = $( '.variations-pagenav .displaying-num' );

			// Set the new total of variations
			wrapper.attr( 'data-total', total );

			if ( 1 === total ) {
				displaying_num.text( frozr.i18n_variation_count_single.replace( '%qty%', total ) );
			} else {
				displaying_num.text( frozr.i18n_variation_count_plural.replace( '%qty%', total ) );
			}

			return total;
		},

		/**
		 * Update variations quantity when add a new variation
		 *
		 * @param {Object} event
		 * @param {Int} qty
		 */
		update_single_quantity: function( event, qty ) {
			if ( 1 === qty ) {
				var page_nav = $( '.variations-pagenav' );

				wc_meta_boxes_product_variations_pagenav.update_variations_count( qty );

				if ( page_nav.is( ':hidden' ) ) {
					$( 'option, optgroup', '.variation_actions' ).show();
					$( '.variation_actions' ).val( 'add_variation' );
					$( '#variable_product_options' ).find( '.toolbar' ).show();
					page_nav.show();
					$( '.pagination-links', page_nav ).hide();
				}
			}
		},

		/**
		 * Set the pagenav fields
		 *
		 * @param {Int} qty
		 */
		set_paginav: function( qty ) {
			var wrapper          = $( '#variable_product_options' ).find( '.woocommerce_variations' ),
				new_qty          = wc_meta_boxes_product_variations_pagenav.update_variations_count( qty ),
				toolbar          = $( '#variable_product_options' ).find( '.toolbar' ),
				variation_action = $( '.variation_actions' ),
				page_nav         = $( '.variations-pagenav' ),
				displaying_links = $( '.pagination-links', page_nav ),
				total_pages      = Math.ceil( new_qty / frozr.variations_per_page ),
				options          = '';

			// Set the new total of pages
			wrapper.attr( 'data-total_pages', total_pages );

			$( '.total-pages', page_nav ).text( total_pages );

			// Set the new pagenav options
			for ( var i = 1; i <= total_pages; i++ ) {
				options += '<option value="' + i + '">' + i + '</option>';
			}

			$( '.page-selector', page_nav ).empty().html( options );

			// Show/hide pagenav
			if ( 0 === new_qty ) {
				toolbar.not( '.toolbar-top, .toolbar-buttons' ).hide();
				page_nav.hide();
				$( 'option, optgroup', variation_action ).hide();
				$( '.variation_actions' ).val( 'add_variation' );
				$( 'option[data-global="true"]', variation_action ).show();

			} else {
				toolbar.show();
				page_nav.show();
				$( 'option, optgroup', variation_action ).show();
				$( '.variation_actions' ).val( 'add_variation' );

				// Show/hide links
				if ( 1 === total_pages ) {
					displaying_links.hide();
				} else {
					displaying_links.show();
				}
			}
		},

		/**
		 * Check button if enabled and if don't have changes
		 *
		 * @return {Bool}
		 */
		check_is_enabled: function( current ) {
			return ! $( current ).hasClass( 'disabled' );
		},

		/**
		 * Change "disabled" class on pagenav
		 */
		change_classes: function( selected, total ) {
			var first_page = $( '.variations-pagenav .first-page' ),
				prev_page  = $( '.variations-pagenav .prev-page' ),
				next_page  = $( '.variations-pagenav .next-page' ),
				last_page  = $( '.variations-pagenav .last-page' );

			if ( 1 === selected ) {
				first_page.addClass( 'disabled' );
				prev_page.addClass( 'disabled' );
			} else {
				first_page.removeClass( 'disabled' );
				prev_page.removeClass( 'disabled' );
			}

			if ( total === selected ) {
				next_page.addClass( 'disabled' );
				last_page.addClass( 'disabled' );
			} else {
				next_page.removeClass( 'disabled' );
				last_page.removeClass( 'disabled' );
			}
		},

		/**
		 * Set page
		 */
		set_page: function( page ) {
			$( '.variations-pagenav .page-selector' ).val( page ).first().change();
		},

		/**
		 * Navigate on variations pages
		 *
		 * @param {Int} page
		 * @param {Int} qty
		 */
		go_to_page: function( page, qty ) {
			page = page || 1;
			qty  = qty || 0;

			wc_meta_boxes_product_variations_pagenav.set_paginav( qty );
			wc_meta_boxes_product_variations_pagenav.set_page( page );
		},

		/**
		 * Paginav pagination selector
		 */
		page_selector: function() {
			var selected = parseInt( $( this ).val(), 10 ),
				wrapper  = $( '#variable_product_options' ).find( '.woocommerce_variations' );

			$( '.variations-pagenav .page-selector' ).val( selected );

			wc_meta_boxes_product_variations_ajax.check_for_changes();
			wc_meta_boxes_product_variations_pagenav.change_classes( selected, parseInt( wrapper.attr( 'data-total_pages' ), 10 ) );
			wc_meta_boxes_product_variations_ajax.load_variations( selected );
		},

		/**
		 * Go to first page
		 *
		 * @return {Bool}
		 */
		first_page: function() {
			if ( wc_meta_boxes_product_variations_pagenav.check_is_enabled( this ) ) {
				wc_meta_boxes_product_variations_pagenav.set_page( 1 );
			}

			return false;
		},

		/**
		 * Go to previous page
		 *
		 * @return {Bool}
		 */
		prev_page: function() {
			if ( wc_meta_boxes_product_variations_pagenav.check_is_enabled( this ) ) {
				var wrapper   = $( '#variable_product_options' ).find( '.woocommerce_variations' ),
					prev_page = parseInt( wrapper.attr( 'data-page' ), 10 ) - 1,
					new_page  = ( 0 < prev_page ) ? prev_page : 1;

				wc_meta_boxes_product_variations_pagenav.set_page( new_page );
			}

			return false;
		},

		/**
		 * Go to next page
		 *
		 * @return {Bool}
		 */
		next_page: function() {
			if ( wc_meta_boxes_product_variations_pagenav.check_is_enabled( this ) ) {
				var wrapper     = $( '#variable_product_options' ).find( '.woocommerce_variations' ),
					total_pages = parseInt( wrapper.attr( 'data-total_pages' ), 10 ),
					next_page   = parseInt( wrapper.attr( 'data-page' ), 10 ) + 1,
					new_page    = ( total_pages >= next_page ) ? next_page : total_pages;

				wc_meta_boxes_product_variations_pagenav.set_page( new_page );
			}

			return false;
		},

		/**
		 * Go to last page
		 *
		 * @return {Bool}
		 */
		last_page: function() {
			if ( wc_meta_boxes_product_variations_pagenav.check_is_enabled( this ) ) {
				var last_page = $( '#variable_product_options' ).find( '.woocommerce_variations' ).attr( 'data-total_pages' );

				wc_meta_boxes_product_variations_pagenav.set_page( last_page );
			}

			return false;
		}
	};

	/**
	 * Product update ajax methods
	 */
	var frozr_update_product = {

		/**
		 * Initialize product update ajax methods
		 */
		init: function() {

			$('#product-edit').on('click', 'button.update_product', function() {frozr_update_product.save_changes('default');});
			$( 'button.add_attribute' ).on( 'click', this.add_att);
			
			$( document.body )
				.on( 'change', '.instruction-inside, #product-edit :input:not(#variable_product_options .woocommerce_variations :input), #product-edit textarea:not(#variable_product_options .woocommerce_variations textarea)', this.input_changed )
				.on( 'change', '#product-edit select:not(.variations-defaults select)', this.defaults_changed );

		},

		/**
		 * Block edit screen
		 */
		block: function() {
			$( '#product-edit' ).block({
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
			$( '#product-edit' ).unblock();
		},

		/**
		 * Get products fields and convert to object
		 *
		 * @return {Object}
		 */
		get_product_fields: function() {
			var data = $( '#product-edit form' ).serializeJSON();

			$( '#product-edit select:not(.variations-defaults select)' ).each( function( index, element ) {
				var select = $( element );
				data[ select.attr( 'name' ) ] = select.val();
			});

			return data;
		},
		add_att: function() {
			var size			= $( '.product_attributes .woocommerce_attribute' ).size(),
				attribute		= $( 'select.attribute_taxonomy' ).val(),
				$wrapper		= $( this ).closest( '#product_attributes' ).find( '.product_attributes' ),
				pid				= $( '#product-edit input.pid' ).val(),
				product_type	= $( 'select#product-type' ).val(),
				data         = {
				action:   'frozr_add_attribute',
				taxonomy: attribute,
				i:        size,
				security: frozr.add_attribute_nonce
			};
			//note that changes are being done.
			$( this ).closest( 'div' ).addClass( 'popt-needs-update' );

			$wrapper.block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
			
			$.ajax({
				url: frozr.ajax_url,
				data: data,
				type: 'POST',
				success: function( response ) {
						
					$wrapper.append( response );

					if ( product_type !== 'variable' ) {
						$wrapper.find( '.enable_variation' ).hide();
					}

					$( document.body ).trigger( 'wc-enhanced-select-init' );
					attribute_row_indexes();
					$wrapper.unblock();
						
					$( document.body ).trigger( 'woocommerce_added_attribute' );
					
					//add a draft product
					if (pid == 0) {
						frozr_update_product.save_changes('new_attr');
					}
				}
			});

			if ( attribute ) {
				$( 'select.attribute_taxonomy' ).find( 'option[value="' + attribute + '"]' ).attr( 'disabled','disabled' );
				$( 'select.attribute_taxonomy' ).val( '' );
			}

		},
		/**
		 * Save product changes
		 *
		 */
		save_changes: function(xn) {
			var wrapper     = $( '#product-edit' ),
				id 			= $( 'input.pid', wrapper ).val(),
				need_update = $( '.popt-needs-update', wrapper ),
				data        = {};

			// Save only with products need update.
			if ( 0 < need_update.length ) {
				
				if (xn !== 'new_attr') {
					frozr_update_product.block();
				}

				data				= frozr_update_product.get_product_fields();
				data.newpid			= xn;
				data.action			= 'frozr_update_product';
				data.security		= frozr.update_wc_product_nonce;
				data.product_id		= id;

				$.ajax({
					url: frozr.ajax_url,
					data: data,
					type: 'POST',
					success: function( response ) {
						if (xn !== 'new_attr') {
							$('.fro_woo_notices').html('<div class="woocommerce-message">' + response.msg + '</div>');
							if (!$('.f_control_nav .fs-icon-briefcase').length) { $('.f_control_nav').append('<a href="'+response.newp+'" data-ajax="false" class="dash_p_addnewp"><i class="fs-icon-briefcase">&nbsp;</i> '+frozr.add_new_product_text+'</a><a href="'+response.viewp+'" data-ajax="false" class="dash_p_viewp"><i class="fs-icon-eye">&nbsp;</i> '+frozr.view_product_text+'</a>') };
							// Allow change page, delete and add new variations
							need_update.removeClass( 'popt-needs-update' );
							frozr_update_product.unblock();
						} else {
							$( 'input.pid', wrapper ).val(response.pid);
						}
					}
				});
			}
		},
		/**
		 * Add new class when have changes in some input
		 */
		input_changed: function() {
			$( this )
				.closest( 'div' )
				.addClass( 'popt-needs-update' );
		},

		/**
		 * Added new .variation-needs-update class when defaults is changed
		 */
		defaults_changed: function() {
			$( this )
				.closest( 'div' )
				.addClass( 'popt-needs-update' );
		},

	};

	frozr_update_product.init();
	wc_meta_boxes_product_variations_media.init();
	wc_meta_boxes_product_variations_ajax.init();
	wc_meta_boxes_product_variations_pagenav.init();
});
