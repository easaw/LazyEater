<?php
/**
 * All Related Item Management Functions
 *
 * @package FrozrCoreLibrary
 * @subpackage Frozrmarketlibrary
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function frozr_product_listing_status_filter() {
    $permalink = home_url('/dashboard/dishes/');
    $status_class = isset( $_GET['post_status'] ) ? $_GET['post_status'] : 'all';
    $post_counts = frozr_count_posts( 'product', get_current_user_id() );
	$post_total = $post_counts->publish + $post_counts->pending + $post_counts->draft;
	if (frozr_mobile()) { $active_icon='fs-icon-caret-right'; } else {  $active_icon='fs-icon-caret-up'; }
    ?>
    <ul class="product_listing_status_filter">
        <li <?php echo $status_class == 'all' ? "class=\"active $active_icon\"" : ''; ?> >
            <a href="<?php echo $permalink; ?>"><?php printf( __( 'All (%d)', 'frozr' ), $post_total ); ?></a>
        </li>
        <li <?php echo $status_class == 'publish' ? "class=\"active $active_icon\"" : ''; ?> >
            <a href="<?php echo add_query_arg( array( 'post_status' => 'publish' ), $permalink ); ?>"><?php printf( __( 'Online (%d)', 'frozr' ), $post_counts->publish ); ?></a>
        </li>
        <li <?php echo $status_class == 'pending' ? "class=\"active $active_icon\"" : ''; ?> >
            <a href="<?php echo add_query_arg( array( 'post_status' => 'pending' ), $permalink ); ?>"><?php printf( __( 'Pending Review (%d)', 'frozr' ), $post_counts->pending ); ?></a>
        </li>
        <li <?php echo $status_class == 'draft' ? "class=\"active $active_icon\"" : ''; ?> >
            <a href="<?php echo add_query_arg( array( 'post_status' => 'draft' ), $permalink ); ?>"><?php printf( __( 'Draft (%d)', 'frozr' ), $post_counts->draft ); ?></a>
        </li>
		<li class="new_product_pl">
			<a href="<?php echo home_url( '/dashboard/new_dish/'); ?>" class="pull-right"><i class="fs-icon-briefcase">&nbsp;</i> <?php _e( 'Add new product', 'frozr' ); ?></a>
		</li>
		<?php do_action('frozr_after_dash_products_list_filter'); ?>
    </ul> <!-- .post-statuses-filter -->
    <?php
}
/**
 * Get user friendly post status based on post
 *
 * @param string $status
 * @return string
 */
function frozr_get_post_status( $status ) {
    switch ($status) {
        case 'publish':
            return __( 'Online', 'frozr' );
            break;

        case 'draft':
            return __( 'Draft', 'frozr' );
            break;

        case 'pending':
            return __( 'Pending', 'frozr' );
            break;

        default:
            return '';
            break;
    }
}
/**
 * Get edit item url
 *
 * @param type $product_id
 * @return type
 */
function frozr_edit_dish_url( $product_id ) {

    return add_query_arg( array( 'product_id' => $product_id, 'action' => 'edit' ), home_url('/dashboard/dishes/') );
}

function frozr_edit_add_dish_body($new = false) {
	
	global $post, $product, $post_id;
	
	if ($new == false) {
		$post_id = $post->ID;
		$seller_id = get_current_user_id();

		if ( isset( $_GET['product_id'] ) ) {
			$post_id = intval( $_GET['product_id'] );
			$post = get_post( $post_id );
		}

		// bail out if not author
		if ( $post->post_author != $seller_id ) {
			wp_die( __( 'Access Denied', 'frozr' ) );
		}
	} else {
		$post_id = 0;
	}
?>

<div id="product-edit" class="content-area-product-edit">

	<form class="form" role="form" method="post">

		<div id="edit-product">

			<?php frozr_dash_nav_control(); ?>
			
			<?php do_action('frozr_product_edit_before_main', $post_id); ?>

			<?php frozr_output_dish_data($post_id, $new); ?>

			<?php do_action('frozr_product_edit_after_main', $post_id); ?>

		</div> <!-- #edit-product -->
		<div class="update-button-wrap">
			<button type="button" class="button-primary update_product"><?php  if ($post_id) { _e( 'Update Item', 'frozr' ); } else {_e( 'Post Item', 'frozr' );} ?></button>
		</div>
	</form>
</div><!-- #product-edit .content-area-product-edit -->

<?php }
function frozr_output_dish_data( $post_id, $new ) {
		
	global $post;

	$product_title = isset( $post->post_title ) && $new == false ? $post->post_title : '';
	$product_content = isset( $post->post_content ) && $new == false ? $post->post_content : '';
	$product_excerpt = isset( $post->post_excerpt )&& $new == false ? $post->post_excerpt : '';
	$discts = get_terms( 'product_cat', 'fields=names&hide_empty=0' );
	//item cats
	$dishcats = wp_get_post_terms( $post_id, 'product_cat', array("fields" => "names") );
	$dishcats_slug = array();
	if (is_array($dishcats)) {
		foreach ( $dishcats as $dishcat ) {
			$dishcats_slug[] = $dishcat;
		}
		$dish_cats = join( ", ", $dishcats_slug );
	} elseif ( ! empty( $discts ) && ! is_wp_error( $discts )) {
		$dish_cats = $dishcats;
	}
	//get all item cats
	$dc_slug = array();
	if ( ! empty( $discts ) && ! is_wp_error( $discts ) ){
		 foreach ( $discts as $term ) {
		   $dc_slug[] = $term;
	}
	$product_cats = "'".join( "',' ", $dc_slug )."'";
	}
	$ingres = get_terms( 'ingredient', 'fields=names&hide_empty=0' );
	//item ingredients
	$ingredients = wp_get_post_terms( $post_id, 'ingredient', array("fields" => "names") );
	$ingredients_slug = array();
	if (is_array($ingredients)) {
		foreach ( $ingredients as $ingredient ) {
			$ingredients_slug[] = $ingredient;
		}
		$ingreds = join( ", ", $ingredients_slug );
	} elseif ( ! empty( $ingres ) && ! is_wp_error( $ingres )) {
		$ingreds = $ingredients;
	}
	//get all ingredients
	$ings_slug = array();
	if ( ! empty( $ingres ) && ! is_wp_error( $ingres ) ){
		 foreach ( $ingres as $term ) {
		   $ings_slug[] = $term;
	}
	$ings = "'".join( "',' ", $ings_slug )."'";
	}
	if ( $terms = wp_get_object_terms( $post_id, 'product_type' ) ) {
		$product_type = sanitize_title( current( $terms )->name );
	} else {
		$product_type = apply_filters( 'default_product_type', 'simple' );
	}
	//get products for linking item
	$upsel = ( null != (get_post_meta( $post_id, '_upsell_ids', true )) ) ? get_post_meta( $post_id, '_upsell_ids', true ) : array();
	$crsel = ( null != (get_post_meta( $post_id, '_crosssell_ids', true )) ) ? get_post_meta( $post_id, '_crosssell_ids', true ) : array();
	$vegp = ( null != (get_post_meta( $post_id, '_dish_veg', true )) ) ? get_post_meta( $post_id, '_dish_veg', true ) : 'veg';
	$spicp = ( null != (get_post_meta( $post_id, '_dish_spicy', true )) ) ? get_post_meta( $post_id, '_dish_spicy', true ) : '';
	$fatp = ( null != (get_post_meta( $post_id, '_dish_fat', true )) ) ? get_post_meta( $post_id, '_dish_fat', true ) : '';
	$fatrp = ( null != (get_post_meta( $post_id, '_dish_fat_rate', true )) ) ? get_post_meta( $post_id, '_dish_fat_rate', true ) : '';
	$argsupco = array(
		'posts_per_page'	=> -1,
		'exclude'			=> $post_id,
		'post_type'			=> 'product',
		'author'			=> get_current_user_id(),
		'post_status'		=> 'publish',
	);
	$linking_posts = get_posts( $argsupco );
	$argsgroup = array(
		'posts_per_page'	=> -1,
		'exclude'			=> $post_id,
		'post_type'			=> 'product',
		'author'			=> get_current_user_id(),
		'post_status'		=> 'publish',
		'tax_query'	=> array(
			array(
				'taxonomy' => 'product_type',
				'field' => 'slug',
				'terms' => 'grouped'
			)
		),
	);
	$group_posts = get_posts( $argsgroup );
	$product_type_selector = apply_filters( 'product_type_selector', array(
		'simple'   => __( 'Simple', 'frozr' ),
		'grouped'  => __( 'Grouped', 'frozr' ),
		// 'external' => __( 'External/Affiliate', 'frozr' ),
		'variable' => __( 'Variable', 'frozr' )
	), $product_type );

	$type_box = '<label for="product-type"><select id="product-type" name="product-type"><optgroup label="' . esc_attr__( 'Product Type', 'frozr' ) . '">';

	foreach ( $product_type_selector as $value => $label ) {
		$type_box .= '<option value="' . esc_attr( $value ) . '" ' . selected( $product_type, $value, false ) .'>' . esc_html( $label ) . '</option>';
	}

	$type_box .= '</optgroup></select></label>';

	/* $product_type_options = apply_filters( 'product_type_options', array(
		'virtual' => array(
			'id'            => '_virtual',
			'wrapper_class' => 'show_if_simple',
			'label'         => __( 'Virtual', 'frozr' ),
			'description'   => __( 'Virtual products are intangible and aren\'t shipped.', 'frozr' ),
			'default'       => 'no'
		),
		'downloadable' => array(
			'id'            => '_downloadable',
			'wrapper_class' => 'show_if_simple',
			'label'         => __( 'Downloadable', 'frozr' ),
			'description'   => __( 'Downloadable products give access to a file upon purchase.', 'frozr' ),
			'default'       => 'no'
		)
	));

	foreach ( $product_type_options as $key => $option ) {
		$selected_value = get_post_meta( $post_id, '_' . $key, true );
		
		if ( '' == $selected_value && isset( $option['default'] ) ) {
			$selected_value = $option['default'];
		}

		$type_box .= '<label for="' . esc_attr( $option['id'] ) . '" class="'. esc_attr( $option['wrapper_class'] ) . ' tips" data-tip="' . esc_attr( $option['description'] ) . '">' . esc_html( $option['label'] ) . ': <input type="checkbox" name="' . esc_attr( $option['id'] ) . '" id="' . esc_attr( $option['id'] ) . '" ' . checked( $selected_value, 'yes', false ) .' /></label>';
	}
	*/
	?>
	<div id="woocommerce-product-data" class="panel-wrap product_data">
		<div class="form-group gen_item_opts">		
			<div class="product-image">
				<?php
				$wrap_class = ' frozr-hide';
				$instruction_class = '';
				$feat_image_id = 0;
				if ( has_post_thumbnail( $post_id ) ) {
					$wrap_class = '';
					$instruction_class = ' frozr-hide';
					$feat_image_id = get_post_thumbnail_id( $post_id );
				}
				?>
				<div class="instruction-inside<?php echo $instruction_class; ?>">
					<input type="hidden" name="feat_image_id" class="frozr-feat-image-id" value="<?php echo $feat_image_id; ?>">
					<i class="fs-icon-cloud-upload"></i>
					<a href="#" class="frozr-feat-image-btn btn btn-sm"><?php _e( 'Upload item cover image', 'frozr' ); ?></a>
				</div>

				<div class="image-wrap<?php echo $wrap_class; ?>">
					<a class="close frozr-remove-feat-image"><i class="fs-icon-camera"></i></a>
					<?php if ( $feat_image_id ) {
					$large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'thumbnail');
					echo '<div class="product-photo" style="background-image: url( '.$large_image_url[0].');"></div>';
					} else { ?>
					<div class="product-photo"></div>
					<?php } ?>
				</div>
			</div>
			<div class="product-general-details">
				<div class="form-group">
					<input class="pid" type="hidden" name="frozr_product_id" value="<?php echo $post_id; ?>">
					<label for="post_title"><?php _e('Item Title','frozr'); ?></label>
					<?php frozr_post_input_box( $post_id, 'post_title', array( 'placeholder' => 'e.g. Sushi, Chicken Curry, Toast', 'value' => $product_title ) ); ?>
				</div>
				<div class="form-group">
					<label for="post_status"><?php _e( 'Item Status:', 'frozr' ); ?></label>
					<?php $post_statuses = apply_filters( 'frozr_products_status', array(
						'publish' => __( 'Publish', 'frozr' ),
						'draft' => __( 'Draft', 'frozr' )
					), $post ); ?>

					<select id="post_status" class="frozr-toggle-select" name="post_status">
						<?php foreach ($post_statuses as $status => $k) { ?>
							<option value="<?php echo $status; ?>"<?php selected( $post->post_status, $status ); ?>><?php echo $k; ?></option>
						<?php } ?>
					</select>
				</div>
				<div class="form-group">
					<label for="post_status"><?php _e( 'Item Type:', 'frozr' ); ?></label>
					<div class="post_status_wrapper">
						<?php echo $type_box; ?>
					</div>
				</div>
			</div>
		</div>
		<div data-role="tabs">
		<div class="frozr-edit-sidebar">
			<ul data-role="listview" data-inset="true" class="tablist-left">
				<?php
					$product_data_tabs = apply_filters( 'woocommerce_product_data_tabs', array(
						'general' => array(
							'label'  => __( 'General', 'frozr' ),
							'target' => 'general_product_data',
							'class'  => array(),
						),
						'inventory' => array(
							'label'  => __( 'Inventory', 'frozr' ),
							'target' => 'inventory_product_data',
							'class'  => array( 'show_if_simple', 'show_if_variable', 'show_if_grouped' ),
						),
						'shipping' => array(
							'label'  => __( 'Delivery', 'frozr' ),
							'target' => 'shipping_product_data',
							'class'  => array( 'hide_if_virtual', 'hide_if_grouped', 'hide_if_external' ),
						),
						'linked_product' => array(
							'label'  => __( 'Linked Products', 'frozr' ),
							'target' => 'linked_product_data',
							'class'  => array(),
						),
						'attribute' => array(
							'label'  => __( 'Attributes', 'frozr' ),
							'target' => 'product_attributes',
							'class'  => array(),
						),
						'variations' => array(
							'label'  => __( 'Variations', 'frozr' ),
							'target' => 'variable_product_options',
							'class'  => array( 'variations_tab', 'show_if_variable' ),
						),
						'advanced' => array(
							'label'  => __( 'Advanced', 'frozr' ),
							'target' => 'advanced_product_data',
							'class'  => array(),
						)
					));

					foreach ( $product_data_tabs as $key => $tab ) {
						?><li class="<?php echo $key; ?>_options <?php echo $key; ?>_tab <?php echo implode( ' ' , $tab['class'] ); ?>">
							<a href="#<?php echo $tab['target']; ?>"><?php echo esc_html( $tab['label'] ); ?></a>
						</li><?php
					}

					do_action( 'woocommerce_product_write_panel_tabs' );
				?>
			</ul>
			<div id="product_images_container">
				<ul class="product_images">
					<?php
						if ( metadata_exists( 'post', $post_id, '_product_image_gallery' ) ) {
							$product_image_gallery = get_post_meta( $post_id, '_product_image_gallery', true );
						} else {
							// Backwards compatibility
							$attachment_ids = get_posts( 'post_parent=' . $post_id . '&numberposts=-1&post_type=attachment&orderby=menu_order&order=ASC&post_mime_type=image&fields=ids&meta_key=_woocommerce_exclude_image&meta_value=0' );
							$attachment_ids = array_diff( $attachment_ids, array( get_post_thumbnail_id() ) );
							$product_image_gallery = implode( ',', $attachment_ids );
						}

						$attachments = array_filter( explode( ',', $product_image_gallery ) );
							
						if ( ! empty( $attachments ) ) {
							foreach ( $attachments as $attachment_id ) {
								echo '<li class="image" data-attachment_id="' . esc_attr( $attachment_id ) . '">
									' . wp_get_attachment_image( $attachment_id, 'thumbnail' ) . '
									<ul class="actions">
										<li><a href="#" class="delete tips" title="' . esc_attr__( 'Delete image', 'frozr' ) . '"><i class="fs-icon-close"></i></a></li>
									</ul>
								</li>';
							}
						}
					?>
				</ul>

				<input type="hidden" id="product_image_gallery" name="product_image_gallery" value="<?php echo esc_attr( $product_image_gallery ); ?>" />

			<p class="add_product_images hide-if-no-js">
				<a href="#" data-choose="<?php esc_attr_e( 'Add Images to item Gallery', 'frozr' ); ?>" data-update="<?php esc_attr_e( 'Add to gallery', 'frozr' ); ?>" data-delete="<?php esc_attr_e( 'Delete image', 'frozr' ); ?>" data-text="<?php esc_attr_e( 'Delete', 'frozr' ); ?>"><i class="fs-icon-photo"></i>&nbsp;<?php _e( 'Add item gallery images', 'frozr' ); ?></a>
			</p>
			</div>
		</div>	
		<div id="general_product_data" class="panel tablist-content woocommerce_options_panel">
		<span class="form-group-label"><?php _e( 'General options', 'frozr' ); ?></span>
		<div class="options_group">
			<div class="form-group ppdetail">
				<label for="post_excerpt"><?php _e('Item Details','frozr'); ?></label>
				<?php frozr_post_input_box( $post_id, 'post_excerpt', array( 'placeholder' => 'Short description about the item...', 'value' => $product_excerpt ), 'text' ); ?>
			</div>
			<div class="form-group col-1">
				<label class="control-label" for="product_cat"><?php _e( 'Item Recipe', 'frozr' ); ?>&nbsp;<a class="tips" title="<?php _e( 'This is very important option and used to classify your items in your restaurant page. Type first two/three letters and choose from list, if recipe is not in the list then complete typing the recipe title and hit the comma button.', 'frozr' ) ?>" href="#">[?]</a></label>
				<input id="product_cat" required="required" name="product_cat" value="<?php echo $dish_cats; ?>">
			</div>
			<div class="form-group col-1">
				<label class="control-label" for="dish_ingredients"><?php _e( 'Item Ingredients','frozr'); ?>&nbsp;<a class="tips" title="<?php _e( 'Type first two/three letters and choose from list, if ingredient is not in the list then complete typing your ingredient name and hit the comma button. Try put all item Ingredients so people can reach your item when filtering products.', 'frozr' ) ?>" href="#">[?]</a></label>
				<input id="dish-ingredients" name="dish_ingredients" value="<?php echo $ingreds; ?>">
			</div>
			<div class="form-group">
				<span class="control-label"><?php echo __( 'Item Details', 'frozr' ); ?></span>
				<div>
					<label><?php _e( 'Veg.', 'frozr' ); ?>
						<input type="radio" name="dish_veg" value="veg" <?php checked( $vegp, 'veg' ); ?>>
					</label>
					<label><?php _e( 'Non-Veg.', 'frozr' ); ?>
						<input type="radio" name="dish_veg" value="nonveg" <?php checked( $vegp, 'nonveg' ); ?>>
					</label>
					<label><?php _e( 'Dish is Spicy.', 'frozr' ); ?>
						<input type="checkbox" name="dish_spicy" value="yes" <?php checked( $spicp, 'yes' ); ?>>
					</label>						
					<label><?php _e( 'Show Fat Amount.', 'frozr' ); ?>
						<input type="checkbox" id="dish_fat" name="dish_fat" value="yes" <?php checked( $fatp, 'yes' ); ?>>
					</label>						
					<input type="number" id="dish_fat_rate" <?php if ($fatp != 'yes') { echo 'class="frozr-hide"'; } ?> name="dish_fat_rate" min="0" max="100" value="<?php esc_attr($fatrp); ?>" placeholder="<?php _e('Amount of Fat in Grams.','frozr'); ?>">
				</div>
			</div>
			<?php frozr_wp_textarea_input(  array( 'id' => 'post_content', 'value' => $product_content, 'label' => __( 'More Details on the item', 'frozr' ), 'placeholder' => __( 'Enter more description about the item.', 'frozr' ) ) ); ?>
		</div>
			
		<?php echo '<div class="options_group hide_if_grouped">';

			// SKU
			if ( wc_product_sku_enabled() ) {
				
				frozr_wp_text_input( array( 'id' => '_sku', 'label' => '<abbr title="'. __( 'Stock Keeping Unit', 'frozr' ) .'">' . __( 'SKU', 'frozr' ) . '</abbr>', 'desc_tip' => 'true', 'description' => __( 'SKU refers to a Stock-keeping unit, a unique identifier for each distinct product and service that can be purchased.', 'frozr' ) ) );
					
			} else {
				echo '<input type="hidden" name="_sku" value="' . esc_attr( get_post_meta( $post_id, '_sku', true ) ) . '" />';
			}

			do_action( 'woocommerce_product_options_sku' );

			echo '</div>';

			echo '<div class="options_group show_if_external">';

				// External URL
				frozr_wp_text_input( array( 'id' => '_product_url', 'label' => __( 'Item URL', 'frozr' ), 'placeholder' => 'http://', 'description' => __( 'Enter the external URL to the product.', 'frozr' ) ) );

				// Button text
				frozr_wp_text_input( array( 'id' => '_button_text', 'label' => __( 'Button text', 'frozr' ), 'placeholder' => _x('Buy product', 'placeholder', 'frozr'), 'description' => __( 'This text will be shown on the button linking to the external product.', 'frozr' ) ) );

			echo '</div>';

			echo '<div class="options_group pricing show_if_simple show_if_external">';

				// Price
				frozr_wp_text_input( array( 'id' => '_regular_price', 'label' => __( 'Regular Price', 'frozr' ) . ' (' . get_woocommerce_currency_symbol() . ')', 'data_type' => 'price' ) );

				// Special Price
				frozr_wp_text_input( array( 'id' => '_sale_price', 'data_type' => 'price', 'label' => __( 'Sale Price', 'frozr' ) . ' ('.get_woocommerce_currency_symbol().')', 'description' => '<a href="#" class="sale_schedule">' . __( 'Schedule', 'frozr' ) . '</a>' ) );

				// Special Price date range
				$sale_price_dates_from = ( $date = get_post_meta( $post_id, '_sale_price_dates_from', true ) ) ? date_i18n( 'Y-m-d', $date ) : '';
				$sale_price_dates_to   = ( $date = get_post_meta( $post_id, '_sale_price_dates_to', true ) ) ? date_i18n( 'Y-m-d', $date ) : '';

				echo '<p class="fl-form-field sale_price_dates_fields">
							<label for="_sale_price_dates_from">' . __( 'Sale Price Dates', 'frozr' ) . '</label>
							<input type="text" class="short" name="_sale_price_dates_from" id="_sale_price_dates_from" value="' . esc_attr( $sale_price_dates_from ) . '" placeholder="' . _x( 'From&hellip;', 'placeholder', 'frozr' ) . ' YYYY-MM-DD" maxlength="10" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" />
							<input type="text" class="short" name="_sale_price_dates_to" id="_sale_price_dates_to" value="' . esc_attr( $sale_price_dates_to ) . '" placeholder="' . _x( 'To&hellip;', 'placeholder', 'frozr' ) . '  YYYY-MM-DD" maxlength="10" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" />
							<a href="#" class="cancel_sale_schedule">'. __( 'Cancel', 'frozr' ) .'</a>
							<img class="help_tip" style="margin-top: 21px;" alt="' . esc_attr__( 'The sale will end at the beginning of the set date.', 'frozr' ) . '" src="' . esc_url( WC()->plugin_url() ) . '/assets/images/help.png" height="16" width="16" />
						</p>';

				do_action( 'woocommerce_product_options_pricing' );

			echo '</div>';
			
			/* echo '<div class="options_group show_if_downloadable">';
			?>
				<div class="fl-form-field downloadable_files">
					<label><?php _e( 'Downloadable Files', 'frozr' ); ?>:</label>
					<table class="widefat">
							<thead>
								<tr>
									<th class="sort">&nbsp;</th>
									<th><?php _e( 'Name', 'frozr' ); ?> <span class="tips" data-tip="<?php esc_attr_e( 'This is the name of the download shown to the customer.', 'frozr' ); ?>">[?]</span></th>
									<th colspan="2"><?php _e( 'File URL', 'frozr' ); ?> <span class="tips" data-tip="<?php esc_attr_e( 'This is the URL or absolute path to the file which customers will get access to. URLs entered here should already be encoded.', 'frozr' ); ?>">[?]</span></th>
									<th>&nbsp;</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$downloadable_files = get_post_meta( $post_id, '_downloadable_files', true );

								if ( $downloadable_files ) {
									foreach ( $downloadable_files as $key => $file ) {
										include( FROZR_WOO_INC . '/woo-views/html-product-download.php');
									}
								}
								?>
							</tbody>
							<tfoot>
								<tr>
									<th colspan="5">
										<a href="#" class="button insert" data-row="<?php
											$file = array(
												'file' => '',
												'name' => ''
											);
											ob_start();
											include( FROZR_WOO_INC . '/woo-views/html-product-download.php');
											echo esc_attr( ob_get_clean() );
										?>"><?php _e( 'Add File', 'frozr' ); ?></a>
									</th>
								</tr>
							</tfoot>
						</table>
					</div>
					<?php

					// Download Limit
					frozr_wp_text_input( array( 'id' => '_download_limit', 'label' => __( 'Download Limit', 'frozr' ), 'placeholder' => __( 'Unlimited', 'frozr' ), 'description' => __( 'Leave blank for unlimited re-downloads.', 'frozr' ), 'type' => 'number', 'custom_attributes' => array(
						'step' 	=> '1',
						'min'	=> '0'
					) ) );

					// Expirey
					frozr_wp_text_input( array( 'id' => '_download_expiry', 'label' => __( 'Download Expiry', 'frozr' ), 'placeholder' => __( 'Never', 'frozr' ), 'description' => __( 'Enter the number of days before a download link expires, or leave blank.', 'frozr' ), 'type' => 'number', 'custom_attributes' => array(
						'step' 	=> '1',
						'min'	=> '0'
					) ) );

					 // Download Type
					frozr_wp_select( array( 'id' => '_download_type', 'label' => __( 'Download Type', 'frozr' ), 'description' => sprintf( __( 'Choose a download type - this controls the <a href="%s">schema</a>.', 'frozr' ), 'http://schema.org/' ), 'options' => array(
						''            => __( 'Standard Product', 'frozr' ),
						'application' => __( 'Application/Software', 'frozr' ),
						'music'       => __( 'Music', 'frozr' ),
					) ) );

					do_action( 'woocommerce_product_options_downloads' );

				echo '</div>';
				*/

				if ( wc_tax_enabled() ) {

					echo '<div class="options_group show_if_simple show_if_external show_if_variable">';

						// Tax
						frozr_wp_select( array(
							'id'      => '_tax_status',
							'label'   => __( 'Tax Status', 'frozr' ),
							'options' => array(
								'taxable' 	=> __( 'Taxable', 'frozr' ),
								'shipping' 	=> __( 'Shipping only', 'frozr' ),
								'none' 		=> _x( 'None', 'Tax status', 'frozr' )
							),
							'desc_tip'    => 'true',
							'description' => __( 'Define whether or not the entire product is taxable, or just the cost of shipping it.', 'frozr' )
						) );

						$tax_classes         = WC_Tax::get_tax_classes();
						$classes_options     = array();
						$classes_options[''] = __( 'Standard', 'frozr' );

						if ( ! empty( $tax_classes ) ) {
							foreach ( $tax_classes as $class ) {
								$classes_options[ sanitize_title( $class ) ] = esc_html( $class );
							}
						}

						frozr_wp_select( array(
							'id'          => '_tax_class',
							'label'       => __( 'Tax Class', 'frozr' ),
							'options'     => $classes_options,
							'desc_tip'    => 'true',
							'description' => __( 'Choose a tax class for this product. Tax classes are used to apply different tax rates specific to certain types of product.', 'frozr' )
						) );

						do_action( 'woocommerce_product_options_tax' );

					echo '</div>';

				}

				do_action( 'woocommerce_product_options_general_product_data' );
				?>
			</div>

			<div id="inventory_product_data" class="panel tablist-content woocommerce_options_panel">
			<span class="form-group-label"><?php _e( 'Inventory options', 'frozr' ); ?></span>

				<?php

				echo '<div class="options_group">';

				if ( 'yes' == get_option( 'woocommerce_manage_stock' ) ) {

					// manage stock
					frozr_wp_checkbox( array( 'id' => '_manage_stock', 'wrapper_class' => 'show_if_simple show_if_variable', 'label' => __( 'Manage stock?', 'frozr' ), 'description' => __( 'Enable stock management at product level', 'frozr' ) ) );

					do_action( 'woocommerce_product_options_stock' );

					echo '<div class="stock_fields show_if_simple show_if_variable">';

					// Stock
					frozr_wp_text_input( array(
						'id'                => '_stock',
						'label'             => __( 'Stock Qty', 'frozr' ),
						'desc_tip'          => true,
						'description'       => __( 'Stock quantity. If this is a variable product this value will be used to control stock for all variations, unless you define stock at variation level.', 'frozr' ),
						'type'              => 'number',
						'custom_attributes' => array(
							'step' => 'any'
						),
						'data_type'         => 'stock'
					) );

					// Backorders?
					frozr_wp_select( array( 'id' => '_backorders', 'label' => __( 'Allow Backorders?', 'frozr' ), 'options' => array(
						'no'     => __( 'Do not allow', 'frozr' ),
						'notify' => __( 'Allow, but notify customer', 'frozr' ),
						'yes'    => __( 'Allow', 'frozr' )
					), 'desc_tip' => true, 'description' => __( 'If managing stock, this controls whether or not backorders are allowed. If enabled, stock quantity can go below 0.', 'frozr' ) ) );

					do_action( 'woocommerce_product_options_stock_fields' );

					echo '</div>';

				}

				// Stock status
				frozr_wp_select( array( 'id' => '_stock_status', 'wrapper_class' => 'hide_if_variable', 'label' => __( 'Stock status', 'frozr' ), 'options' => array(
					'instock' => __( 'In stock', 'frozr' ),
					'outofstock' => __( 'Out of stock', 'frozr' )
				), 'desc_tip' => true, 'description' => __( 'Controls whether or not the product is listed as "in stock" or "out of stock" on the frontend.', 'frozr' ) ) );

				do_action( 'woocommerce_product_options_stock_status' );

				echo '</div>';

				echo '<div class="options_group show_if_simple show_if_variable">';

				// Individual product
				frozr_wp_checkbox( array( 'id' => '_sold_individually', 'wrapper_class' => 'show_if_simple show_if_variable', 'label' => __( 'Sold Individually', 'frozr' ), 'description' => __( 'Enable this to only allow one of this item to be bought in a single order', 'frozr' ) ) );

				do_action( 'woocommerce_product_options_sold_individually' );

				echo '</div>';

				do_action( 'woocommerce_product_options_inventory_product_data' );
				?>

			</div>

			<div id="shipping_product_data" class="panel tablist-content woocommerce_options_panel">
			<span class="form-group-label"><?php _e( 'Delivery options', 'frozr' ); ?></span>

				<?php

				echo '<div class="options_group">';

					// Weight
					if ( wc_product_weight_enabled() ) {
						frozr_wp_text_input( array( 'id' => '_weight', 'label' => __( 'Weight', 'frozr' ) . ' (' . get_option( 'woocommerce_weight_unit' ) . ')', 'placeholder' => wc_format_localized_decimal( 0 ), 'desc_tip' => 'true', 'description' => __( 'Weight in decimal form', 'frozr' ), 'type' => 'text', 'data_type' => 'decimal' ) );
					}

					// Size fields
					if ( wc_product_dimensions_enabled() ) {
						?><p class="fl-form-field dimensions_field">
							<label for="product_length"><?php echo __( 'Dimensions', 'frozr' ) . ' (' . get_option( 'woocommerce_dimension_unit' ) . ')'; ?></label>
							<span class="wrap">
								<input id="product_length" placeholder="<?php esc_attr_e( 'Length', 'frozr' ); ?>" class="input-text wc_input_decimal" size="6" type="text" name="_length" value="<?php echo esc_attr( wc_format_localized_decimal( get_post_meta( $post_id, '_length', true ) ) ); ?>" />
								<input placeholder="<?php esc_attr_e( 'Width', 'frozr' ); ?>" class="input-text wc_input_decimal" size="6" type="text" name="_width" value="<?php echo esc_attr( wc_format_localized_decimal( get_post_meta( $post_id, '_width', true ) ) ); ?>" />
								<input placeholder="<?php esc_attr_e( 'Height', 'frozr' ); ?>" class="input-text wc_input_decimal last" size="6" type="text" name="_height" value="<?php echo esc_attr( wc_format_localized_decimal( get_post_meta( $post_id, '_height', true ) ) ); ?>" />
							</span>
							<a class="help_tip" href="#" title="<?php esc_attr_e( 'LxWxH in decimal form', 'frozr' ); ?>">[?]</a>
						</p><?php
					}

					do_action( 'woocommerce_product_options_dimensions' );

				echo '</div>';

				echo '<div class="options_group">';

					// Shipping Class
					$classes = get_the_terms( $post_id, 'product_shipping_class' );
					if ( $classes && ! is_wp_error( $classes ) ) {
						$current_shipping_class = current( $classes )->term_id;
					} else {
						$current_shipping_class = '';
					}

					$args = array(
						'taxonomy'         => 'product_shipping_class',
						'hide_empty'       => 0,
						'show_option_none' => __( 'No shipping class', 'frozr' ),
						'name'             => 'product_shipping_class',
						'id'               => 'product_shipping_class',
						'selected'         => $current_shipping_class,
						'class'            => 'select short'
					);
					?><p class="fl-form-field dimensions_field"><label for="product_shipping_class"><?php _e( 'Shipping class', 'frozr' ); ?></label> <?php wp_dropdown_categories( $args ); ?> <a href="#" class="help_tip" title="<?php esc_attr_e( 'Shipping classes are used by certain shipping methods to group similar products.', 'frozr' ); ?>">[?]</a></p><?php

					do_action( 'woocommerce_product_options_shipping' );

				echo '</div>';
				?>

			</div>

			<div id="product_attributes" class="panel tablist-content wc-metaboxes-wrapper">
			<span class="form-group-label"><?php _e( 'Item Attributes', 'frozr' ); ?></span>
				<div class="toolbar toolbar-top">
					<span class="expand-close">
						<a href="#" class="expand_all"><?php _e( 'Expand', 'frozr' ); ?></a> / <a href="#" class="close_all"><?php _e( 'Close', 'frozr' ); ?></a>
					</span>
					<select name="attribute_taxonomy" class="attribute_taxonomy">
						<option value=""><?php _e( 'Custom product attribute', 'frozr' ); ?></option>
						<?php
							global $wc_product_attributes;

							// Array of defined attribute taxonomies
							$attribute_taxonomies = wc_get_attribute_taxonomies();

							if ( $attribute_taxonomies ) {
								foreach ( $attribute_taxonomies as $tax ) {
									$attribute_taxonomy_name = wc_attribute_taxonomy_name( $tax->attribute_name );
									$label = $tax->attribute_label ? $tax->attribute_label : $tax->attribute_name;
									echo '<option value="' . esc_attr( $attribute_taxonomy_name ) . '">' . esc_html( $label ) . '</option>';
								}
							}
						?>
					</select>
					<button type="button" class="button add_attribute"><?php _e( 'Add', 'frozr' ); ?></button>
				</div>
				<div class="product_attributes wc-metaboxes">
					<?php
						// Product attributes - taxonomies and custom, ordered, with visibility and variation attributes set
						$attributes           = maybe_unserialize( get_post_meta( $post_id, '_product_attributes', true ) );

						// Output All Set Attributes
						if ( ! empty( $attributes ) ) {
							$attribute_keys  = array_keys( $attributes );
							$attribute_total = sizeof( $attribute_keys );

							for ( $i = 0; $i < $attribute_total; $i ++ ) {
								$attribute     = $attributes[ $attribute_keys[ $i ] ];
								$position      = empty( $attribute['position'] ) ? 0 : absint( $attribute['position'] );
								$taxonomy      = '';
								$metabox_class = array();

								if ( $attribute['is_taxonomy'] ) {
									$taxonomy = $attribute['name'];

									if ( ! taxonomy_exists( $taxonomy ) ) {
										continue;
									}

									$attribute_taxonomy = $wc_product_attributes[ $taxonomy ];
									$metabox_class[]    = 'taxonomy';
									$metabox_class[]    = $taxonomy;
									$attribute_label    = wc_attribute_label( $taxonomy );
								} else {
									$attribute_label    = apply_filters( 'woocommerce_attribute_label', $attribute['name'], $attribute['name'] );
								}
								include(FROZR_WOO_INC . '/woo-views/html-product-attribute.php');
							}
						}
					?>
				</div>
				<div class="toolbar">
					<span class="expand-close">
						<a href="#" class="expand_all"><?php _e( 'Expand', 'frozr' ); ?></a> / <a href="#" class="close_all"><?php _e( 'Close', 'frozr' ); ?></a>
					</span>
					<button type="button" class="button save_attributes button-primary"><?php _e( 'Save Attributes', 'frozr' ); ?></button>
				</div>
				<?php do_action( 'woocommerce_product_options_attributes' ); ?>
			</div>

			<div id="linked_product_data" class="panel tablist-content woocommerce_options_panel">
			<span class="form-group-label"><?php _e( 'Linking options', 'frozr' ); ?></span>

				<div class="options_group">

					<p class="fl-form-field">
						<label for="upsell_ids"><?php _e( 'Up-Sells', 'frozr' ); ?><a href="#" title="<?php _e( 'Up-sells are products which you recommend instead of the currently viewed product, for example, products that are more profitable or better quality or more expensive.', 'frozr' ); ?>">[?]</a></label>
						<select name="upsell_ids" id="upsell_ids" multiple="multiple" data-native-menu="false">
							<option><?php echo __('Choose Products','frozr'); ?></option>
							<?php foreach ( $linking_posts as $linking_post ) { setup_postdata( $linking_post ); ?>
							<option value="<?php echo $linking_post->ID; ?>" <?php if (in_array($linking_post->ID, $upsel)) { echo 'selected'; } ?> ><?php echo get_the_title($linking_post->ID); ?></option>
							<?php }
							wp_reset_postdata(); ?>
						</select>
					</p>

					<p class="fl-form-field">
						<label for="crosssell_ids"><?php _e( 'Cross-Sells', 'frozr' ); ?><a href="#" title="<?php _e( 'Cross-sells are products which you promote in the cart, based on the current product.', 'frozr' ); ?>">[?]</a></label>
						<select name="crosssell_ids" id="crosssell_ids" multiple="multiple" data-native-menu="false">
							<option><?php echo __('Choose Products','frozr'); ?></option>
							<?php foreach ( $linking_posts as $linking_post ) { setup_postdata( $linking_post ); ?>
							<option value="<?php echo $linking_post->ID; ?>" <?php if (in_array($linking_post->ID, $crsel)) { echo 'selected'; } ?> ><?php echo get_the_title($linking_post->ID); ?></option>
							<?php }
							wp_reset_postdata(); ?>
						</select>
					</p>
				</div>

				<div class="options_group grouping show_if_simple show_if_external">

					<p class="fl-form-field">
						<label for="parent_id"><?php _e( 'Grouping', 'frozr' ); ?><a href="#" title="<?php _e( 'Set this option to make this product part of a grouped product.', 'frozr' ); ?>">[?]</a></label>
						<select name="parent_id" id="parent_id" data-native-menu="false">
							<option><?php echo __('Choose Products','frozr'); ?></option>
							<option value=""><?php echo __('None','frozr'); ?></option>
							<?php foreach ( $group_posts as $group_post ) { setup_postdata( $group_post ); ?>
							<option value="<?php echo $group_post->ID; ?>" <?php if ($group_post->ID == $post->post_parent) { echo 'selected'; } ?> ><?php echo get_the_title($group_post->ID); ?></option>
							<?php }
							wp_reset_postdata(); ?>
						</select>
					</p>

					<?php
						frozr_wp_hidden_input( array( 'id' => 'previous_parent_id', 'value' => absint( $post->post_parent ) ) );

						do_action( 'woocommerce_product_options_grouping' );
					?>
				</div>

				<?php do_action( 'woocommerce_product_options_related' ); ?>
			</div>

			<div id="advanced_product_data" class="panel tablist-content woocommerce_options_panel">
			<span class="form-group-label"><?php _e( 'Advance options', 'frozr' ); ?></span>

				<div class="options_group hide_if_external">
					<?php
						// Purchase note
						frozr_wp_textarea_input(  array( 'id' => '_purchase_note', 'label' => __( 'Purchase Note', 'frozr' ), 'desc_tip' => 'true', 'description' => __( 'Enter an optional note to send the customer after purchase.', 'frozr' ) ) );
					?>
				</div>

				<div class="options_group">
					<?php
						// menu_order
						frozr_wp_text_input(  array( 'id' => 'menu_order', 'label' => __( 'Menu order', 'frozr' ), 'desc_tip' => 'true', 'description' => __( 'Custom ordering position.', 'frozr' ), 'value' => intval( $post->menu_order ), 'type' => 'number', 'custom_attributes' => array(
							'step' 	=> '1'
						)  ) );
					?>
				</div>

				<div class="options_group reviews">
					<?php
						frozr_wp_checkbox( array( 'id' => 'comment_status', 'label' => __( 'Enable reviews', 'frozr' ), 'cbvalue' => 'open', 'value' => esc_attr( $post->comment_status ) ) );

						do_action( 'woocommerce_product_options_reviews' );
					?>
				</div>

				<?php do_action( 'woocommerce_product_options_advanced' ); ?>

			</div>

			<?php
				frozr_output_variations($post_id);

				do_action( 'woocommerce_product_data_panels' );
				do_action( 'woocommerce_product_write_panels' ); // _deprecated
			?>

			<div class="clear"></div>
			</div>
		</div>
	<script>
		jQuery(function () {
			jQuery('#dish-ingredients').tagator({
				autocomplete: [<?php echo $ings; ?>]
			});
			jQuery('#product_cat').tagator({
				autocomplete: [<?php echo $product_cats; ?>]
			});
		});
	</script>
	<?php
}

function frozr_output_variations($post_id) {
		
		global $wpdb;
		
		// Get attributes
		$attributes = maybe_unserialize( get_post_meta( $post_id, '_product_attributes', true ) );

		// See if any are set
		$variation_attribute_found = false;

		if ( $attributes ) {
			foreach ( $attributes as $attribute ) {
				if ( ! empty( $attribute['is_variation'] ) ) {
					$variation_attribute_found = true;
					break;
				}
			}
		}
		$variations_count       = absint( $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(ID) FROM $wpdb->posts WHERE post_parent = %d AND post_type = 'product_variation' AND post_status IN ('publish', 'private')", $post_id ) ) );
		$variations_per_page    = absint( apply_filters( 'woocommerce_admin_meta_boxes_variations_per_page', 15 ) );
		$variations_total_pages = ceil( $variations_count / $variations_per_page );
		?>
		<div id="variable_product_options" class="panel tablist-content wc-metaboxes-wrapper"><div id="variable_product_options_inner">
		<span class="form-group-label"><?php _e( 'Variation options', 'frozr' ); ?></span>

			<?php if ( ! $variation_attribute_found ) : ?>

				<div id="message" class="inline notice woocommerce-message">
					<p><i class="fs-icon-info"></i>&nbsp<?php _e( 'Before you can add a variation you need to add some variation attributes on the <strong>Attributes</strong> tab.', 'frozr' ); ?></p>
					<p>
						<a class="button-primary" href="<?php echo esc_url( apply_filters( 'woocommerce_docs_url', 'http://docs.woothemes.com/document/variable-product/', 'product-variations' ) ); ?>" target="_blank"><?php _e( 'Learn more', 'frozr' ); ?></a>
					</p>
				</div>

			<?php else : ?>

				<div class="toolbar toolbar-variations-defaults">
					<div class="variations-defaults">
						<strong><?php _e( 'Default Form Values', 'frozr' ); ?>: <a href="#" class="tips" title="<?php esc_attr_e( 'These are the attributes that will be pre-selected on the frontend.', 'frozr' ); ?>">[?]</a></strong>
						<?php
							$default_attributes = maybe_unserialize( get_post_meta( $post_id, '_default_attributes', true ) );

							foreach ( $attributes as $attribute ) {

								// Only deal with attributes that are variations
								if ( ! $attribute['is_variation'] ) {
									continue;
								}

								// Get current value for variation (if set)
								$variation_selected_value = isset( $default_attributes[ sanitize_title( $attribute['name'] ) ] ) ? $default_attributes[ sanitize_title( $attribute['name'] ) ] : '';

								// Name will be something like attribute_pa_color
								echo '<select name="default_attribute_' . sanitize_title( $attribute['name'] ) . '" data-current="' . esc_attr( $variation_selected_value ) . '"><option value="">' . __( 'No default', 'frozr' ) . ' ' . esc_html( wc_attribute_label( $attribute['name'] ) ) . '&hellip;</option>';

								// Get terms for attribute taxonomy or value if its a custom attribute
								if ( $attribute['is_taxonomy'] ) {
									$post_terms = wp_get_post_terms( $post_id, $attribute['name'] );

									foreach ( $post_terms as $term ) {
										echo '<option ' . selected( $variation_selected_value, $term->slug, false ) . ' value="' . esc_attr( $term->slug ) . '">' . apply_filters( 'woocommerce_variation_option_name', esc_html( $term->name ) ) . '</option>';
									}

								} else {
									$options = wc_get_text_attributes( $attribute['value'] );

									foreach ( $options as $option ) {
										$selected = sanitize_title( $variation_selected_value ) === $variation_selected_value ? selected( $variation_selected_value, sanitize_title( $option ), false ) : selected( $variation_selected_value, $option, false );
										echo '<option ' . $selected . ' value="' . esc_attr( $option ) . '">' . esc_html( apply_filters( 'woocommerce_variation_option_name', $option ) )  . '</option>';
									}

								}

								echo '</select>';
							}
						?>
					</div>
					<div class="clear"></div>
				</div>

				<div class="toolbar toolbar-top">
					<select id="field_to_edit" class="variation_actions">
						<option data-global="true" value="add_variation"><?php _e( 'Add variation', 'frozr' ); ?></option>
						<option data-global="true" value="link_all_variations"><?php _e( 'Create variations from all attributes', 'frozr' ); ?></option>
						<option value="delete_all"><?php _e( 'Delete all variations', 'frozr' ); ?></option>
						<optgroup label="<?php esc_attr_e( 'Status', 'frozr' ); ?>">
							<option value="toggle_enabled"><?php _e( 'Toggle &quot;Enabled&quot;', 'frozr' ); ?></option>
							<!-- <option value="toggle_downloadable"> _e( 'Toggle &quot;Downloadable&quot;', 'frozr' ); </option>
							<option value="toggle_virtual">_e( 'Toggle &quot;Virtual&quot;', 'frozr' ); </option> -->
						</optgroup>
						<optgroup label="<?php esc_attr_e( 'Pricing', 'frozr' ); ?>">
							<option value="variable_regular_price"><?php _e( 'Set regular prices', 'frozr' ); ?></option>
							<option value="variable_regular_price_increase"><?php _e( 'Increase regular prices (fixed amount or percentage)', 'frozr' ); ?></option>
							<option value="variable_regular_price_decrease"><?php _e( 'Decrease regular prices (fixed amount or percentage)', 'frozr' ); ?></option>
							<option value="variable_sale_price"><?php _e( 'Set sale prices', 'frozr' ); ?></option>
							<option value="variable_sale_price_increase"><?php _e( 'Increase sale prices (fixed amount or percentage)', 'frozr' ); ?></option>
							<option value="variable_sale_price_decrease"><?php _e( 'Decrease sale prices (fixed amount or percentage)', 'frozr' ); ?></option>
							<option value="variable_sale_schedule"><?php _e( 'Set scheduled sale dates', 'frozr' ); ?></option>
						</optgroup>
						<optgroup label="<?php esc_attr_e( 'Inventory', 'frozr' ); ?>">
							<option value="toggle_manage_stock"><?php _e( 'Toggle &quot;Manage stock&quot;', 'frozr' ); ?></option>
							<option value="variable_stock"><?php _e( 'Stock', 'frozr' ); ?></option>
						</optgroup>
						<optgroup label="<?php esc_attr_e( 'Shipping', 'frozr' ); ?>">
							<option value="variable_length"><?php _e( 'Length', 'frozr' ); ?></option>
							<option value="variable_width"><?php _e( 'Width', 'frozr' ); ?></option>
							<option value="variable_height"><?php _e( 'Height', 'frozr' ); ?></option>
							<option value="variable_weight"><?php _e( 'Weight', 'frozr' ); ?></option>
						</optgroup>
						<!-- <optgroup label=" esc_attr_e( 'Downloadable products', 'frozr' ); ">
							<option value="variable_download_limit"> _e( 'Download limit', 'frozr' ); </option>
							<option value="variable_download_expiry"> _e( 'Download expiry', 'frozr' ); </option>
						</optgroup> -->
						<?php do_action( 'woocommerce_variable_product_bulk_edit_actions' ); ?>
					</select>
					<a class="button bulk_edit do_variation_action"><?php _e( 'Go', 'frozr' ); ?></a>

					<div class="variations-pagenav">
						<span class="displaying-num"><?php printf( _n( '%s item', '%s items', $variations_count, 'frozr' ), $variations_count ); ?></span>
						<span class="expand-close">
							(<a href="#" class="expand_all"><?php _e( 'Expand', 'frozr' ); ?></a> / <a href="#" class="close_all"><?php _e( 'Close', 'frozr' ); ?></a>)
						</span>
						<span class="pagination-links">
							<a class="first-page disabled" title="<?php esc_attr_e( 'Go to the first page', 'frozr' ); ?>" href="#">&laquo;</a>
							<a class="prev-page disabled" title="<?php esc_attr_e( 'Go to the previous page', 'frozr' ); ?>" href="#">&lsaquo;</a>
							<span class="paging-select">
								<label for="current-page-selector-1" class="screen-reader-text"><?php _e( 'Select Page', 'frozr' ); ?></label>
								<select class="page-selector" id="current-page-selector-1" title="<?php esc_attr_e( 'Current page', 'frozr' ); ?>">
									<?php for ( $i = 1; $i <= $variations_total_pages; $i++ ) : ?>
										<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
									<?php endfor; ?>
								</select>
								 <?php _ex( 'of', 'number of pages', 'frozr' ); ?> <span class="total-pages"><?php echo $variations_total_pages; ?></span>
							</span>
							<a class="next-page" title="<?php esc_attr_e( 'Go to the next page', 'frozr' ); ?>" href="#">&rsaquo;</a>
							<a class="last-page" title="<?php esc_attr_e( 'Go to the last page', 'frozr' ); ?>" href="#">&raquo;</a>
						</span>
					</div>
					<div class="clear"></div>
				</div>

				<div class="woocommerce_variations wc-metaboxes" data-attributes="<?php
					// esc_attr does not double encode - htmlspecialchars does
					echo htmlspecialchars( json_encode( $attributes ) );
				?>" data-total="<?php echo $variations_count; ?>" data-total_pages="<?php echo $variations_total_pages; ?>" data-page="1" data-edited="false">
				</div>

				<div class="toolbar">
					<button type="button" class="button-primary save-variation-changes" disabled="disabled"><?php _e( 'Save Changes', 'frozr' ); ?></button>
					<button type="button" class="button cancel-variation-changes" disabled="disabled"><?php _e( 'Cancel', 'frozr' ); ?></button>

					<div class="variations-pagenav">
						<span class="displaying-num"><?php printf( _n( '%s item', '%s items', $variations_count, 'frozr' ), $variations_count ); ?></span>
						<span class="expand-close">
							(<a href="#" class="expand_all"><?php _e( 'Expand', 'frozr' ); ?></a> / <a href="#" class="close_all"><?php _e( 'Close', 'frozr' ); ?></a>)
						</span>
						<span class="pagination-links">
							<a class="first-page disabled" title="<?php esc_attr_e( 'Go to the first page', 'frozr' ); ?>" href="#">&laquo;</a>
							<a class="prev-page disabled" title="<?php esc_attr_e( 'Go to the previous page', 'frozr' ); ?>" href="#">&lsaquo;</a>
							<span class="paging-select">
								<label for="current-page-selector-1" class="screen-reader-text"><?php _e( 'Select Page', 'frozr' ); ?></label>
								<select class="page-selector" id="current-page-selector-1" title="<?php esc_attr_e( 'Current page', 'frozr' ); ?>">
									<?php for ( $i = 1; $i <= $variations_total_pages; $i++ ) : ?>
										<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
									<?php endfor; ?>
								</select>
								 <?php _ex( 'of', 'number of pages', 'frozr' ); ?> <span class="total-pages"><?php echo $variations_total_pages; ?></span>
							</span>
							<a class="next-page" title="<?php esc_attr_e( 'Go to the next page', 'frozr' ); ?>" href="#">&rsaquo;</a>
							<a class="last-page" title="<?php esc_attr_e( 'Go to the last page', 'frozr' ); ?>" href="#">&raquo;</a>
						</span>
					</div>

					<div class="clear"></div>
				</div>

			<?php endif; ?>
		</div>
		</div>
	<?php
}
/**
 * Save the item data meta box.
 *
 * @access public
 * @param mixed $post_id
 * @return void
 */
function frozr_process_dish_meta( $post_id, $post ) {
		global $wpdb;

		//get Product type
		$product_type = empty( $_POST['product-type'] ) ? 'simple' : sanitize_title( stripslashes( $_POST['product-type'] ) );

		$product_info = array(
			'ID' => $post_id,
			'post_title' => wc_clean($_POST['post_title']),
			'post_content' => wp_kses_post($_POST['post_content']),
			'post_excerpt' => wp_kses_post($_POST['post_excerpt']),
			'post_status' => isset( $_POST['post_status'] ) ? esc_attr($_POST['post_status']) : 'pending',
			'comment_status' => isset( $_POST['_enable_reviews'] ) ? 'open' : 'closed'
		);
		if (isset($_POST['parent_id']) && 'grouped' != $product_type) {
			$product_info['post_parent'] = (int) $_POST['parent_id'];
		}
		wp_update_post( $product_info );

		/** set images **/
		$featured_image = absint( $_POST['feat_image_id'] );
		if ( $featured_image ) {
			set_post_thumbnail( $post_id, $featured_image );
		}

		$attachment_ids = isset( $_POST['product_image_gallery'] ) ? array_filter( explode( ',', wc_clean( $_POST['product_image_gallery'] ) ) ) : array();
		update_post_meta( $post_id, '_product_image_gallery', implode( ',', $attachment_ids ) );

		//item category
		$dvals = explode(',', $_POST['product_cat']);
		foreach($dvals as $key => $val) {
			$dvals[$key] = trim($val);
		}
		$dish_vals = array_diff($dvals, array(""));
		$dish_cat = array_map( 'strval', $dish_vals );

		wp_set_object_terms( $post_id, $dish_cat, 'product_cat' );

		//item ingredients
		$vals = explode(',', $_POST['dish_ingredients']);
		foreach($vals as $key => $val) {
			$vals[$key] = trim($val);
		}
		$ing_vals = array_diff($vals, array(""));
		$cat_ids = array_map( 'strval', $ing_vals );

		wp_set_object_terms( $post_id, $cat_ids, 'ingredient' );

		//Set dish details
		// Update the meta field.
		if( isset( $_POST[ 'dish_veg' ] ) ) {
			update_post_meta( $post_id, '_dish_veg', esc_attr($_POST[ 'dish_veg' ]) );
		} else {
			update_post_meta( $post_id, '_dish_veg', 'veg' );
		}
		if( isset( $_POST[ 'dish_spicy' ] ) ) {
			update_post_meta( $post_id, '_dish_spicy', 'yes' );
		} else {
			update_post_meta( $post_id, '_dish_spicy', '' );
		}
		if( isset( $_POST[ 'dish_fat' ] ) ) {
			update_post_meta( $post_id, '_dish_fat', 'yes' );
		} else {
			update_post_meta( $post_id, '_dish_fat', '' );
		}
		if( isset( $_POST[ 'dish_fat_rate' ] ) ) {
			update_post_meta( $post_id, '_dish_fat_rate', esc_attr($_POST[ 'dish_fat_rate' ]) );
		} else {
			update_post_meta( $post_id, '_dish_fat_rate', '' );
		}
		
		// Add any default post meta
		add_post_meta( $post_id, 'total_sales', '0', true );

		// Get types
		$is_downloadable = isset( $_POST['_downloadable'] ) ? 'yes' : 'no';
		$is_virtual	= isset( $_POST['_virtual'] ) ? 'yes' : 'no';

		// Product type + Downloadable/Virtual
		wp_set_object_terms( $post_id, $product_type, 'product_type' );
		update_post_meta( $post_id, '_downloadable', $is_downloadable );
		update_post_meta( $post_id, '_virtual', $is_virtual );
		update_post_meta( $post_id, '_visibility', apply_filters( 'woocommerce_product_visibility_default' , 'visible' ) );
		
		// Update post meta
		if ( isset( $_POST['_regular_price'] ) ) {
			update_post_meta( $post_id, '_regular_price', ( $_POST['_regular_price'] === '' ) ? '' : wc_format_decimal( $_POST['_regular_price'] ) );
		}

		if ( isset( $_POST['_sale_price'] ) ) {
			update_post_meta( $post_id, '_sale_price', ( $_POST['_sale_price'] === '' ? '' : wc_format_decimal( $_POST['_sale_price'] ) ) );
		}

		if ( isset( $_POST['_tax_status'] ) ) {
			update_post_meta( $post_id, '_tax_status', wc_clean( $_POST['_tax_status'] ) );
		}

		if ( isset( $_POST['_tax_class'] ) ) {
			update_post_meta( $post_id, '_tax_class', wc_clean( $_POST['_tax_class'] ) );
		}

		if ( isset( $_POST['_purchase_note'] ) ) {
			update_post_meta( $post_id, '_purchase_note', wp_kses_post( stripslashes( $_POST['_purchase_note'] ) ) );
		}

		// Featured
		if ( update_post_meta( $post_id, '_featured', isset( $_POST['_featured'] ) ? 'yes' : 'no' ) ) {
			delete_transient( 'wc_featured_products' );
		}

		// Dimensions
		if ( 'no' == $is_virtual ) {

			if ( isset( $_POST['_weight'] ) ) {
				update_post_meta( $post_id, '_weight', ( '' === $_POST['_weight'] ) ? '' : wc_format_decimal( $_POST['_weight'] ) );
			}

			if ( isset( $_POST['_length'] ) ) {
				update_post_meta( $post_id, '_length', ( '' === $_POST['_length'] ) ? '' : wc_format_decimal( $_POST['_length'] ) );
			}

			if ( isset( $_POST['_width'] ) ) {
				update_post_meta( $post_id, '_width', ( '' === $_POST['_width'] ) ? '' : wc_format_decimal( $_POST['_width'] ) );
			}

			if ( isset( $_POST['_height'] ) ) {
				update_post_meta( $post_id, '_height', ( '' === $_POST['_height'] ) ? '' : wc_format_decimal( $_POST['_height'] ) );
			}

		} else {
			update_post_meta( $post_id, '_weight', '' );
			update_post_meta( $post_id, '_length', '' );
			update_post_meta( $post_id, '_width', '' );
			update_post_meta( $post_id, '_height', '' );
		}

		// Save shipping class
		$product_shipping_class = $_POST['product_shipping_class'] > 0 && $product_type != 'external' ? absint( $_POST['product_shipping_class'] ) : '';
		wp_set_object_terms( $post_id, $product_shipping_class, 'product_shipping_class');

		// Unique SKU
		$sku     = get_post_meta( $post_id, '_sku', true );
		$new_sku = wc_clean( stripslashes( $_POST['_sku'] ) );

		if ( '' == $new_sku ) {
			update_post_meta( $post_id, '_sku', '' );
		} elseif ( $new_sku !== $sku ) {

			if ( ! empty( $new_sku ) ) {

				$unique_sku = wc_product_has_unique_sku( $post_id, $new_sku );

				if ( ! $unique_sku ) {
					$message = __( 'Product SKU must be unique.', 'frozr' );
					wp_send_json_error( $message );
				} else {
					update_post_meta( $post_id, '_sku', $new_sku );
				}
			} else {
				update_post_meta( $post_id, '_sku', '' );
			}
		}

		// Save Attributes
		$attributes = array();

		if ( isset( $_POST['attribute_names'] ) && isset( $_POST['attribute_values'] ) ) {

			$attribute_names  = $_POST['attribute_names'];
			$attribute_values = $_POST['attribute_values'];

			if ( isset( $_POST['attribute_visibility'] ) ) {
				$attribute_visibility = $_POST['attribute_visibility'];
			}

			if ( isset( $_POST['attribute_variation'] ) ) {
				$attribute_variation = $_POST['attribute_variation'];
			}

			$attribute_is_taxonomy   = $_POST['attribute_is_taxonomy'];
			$attribute_position      = $_POST['attribute_position'];
			$attribute_names_max_key = max( array_keys( $attribute_names ) );

			for ( $i = 0; $i <= $attribute_names_max_key; $i++ ) {
				if ( empty( $attribute_names[ $i ] ) ) {
					continue;
				}

				$is_visible   = isset( $attribute_visibility[ $i ] ) ? 1 : 0;
				$is_variation = isset( $attribute_variation[ $i ] ) ? 1 : 0;
				$is_taxonomy  = $attribute_is_taxonomy[ $i ] ? 1 : 0;

				if ( $is_taxonomy ) {

					$values_are_slugs = false;

					if ( isset( $attribute_values[ $i ] ) ) {

						// Select based attributes - Format values (posted values are slugs)
						if ( is_array( $attribute_values[ $i ] ) ) {
							$values           = array_map( 'sanitize_title', $attribute_values[ $i ] );
							$values_are_slugs = true;

						// Text based attributes - Posted values are term names - don't change to slugs
						} else {
							$values           = array_map( 'stripslashes', array_map( 'strip_tags', explode( WC_DELIMITER, $attribute_values[ $i ] ) ) );
						}

						// Remove empty items in the array
						$values = array_filter( $values, 'strlen' );

					} else {
						$values = array();
					}

					// Update post terms
					if ( taxonomy_exists( $attribute_names[ $i ] ) ) {

						foreach( $values as $key => $value ) {
							$term = get_term_by( $values_are_slugs ? 'slug' : 'name', trim( $value ), $attribute_names[ $i ] );

							if ( $term ) {
								$values[ $key ] = intval( $term->term_id );
							} else {
								$term = wp_insert_term( trim( $value ), $attribute_names[ $i ] );
								if ( isset( $term->term_id ) ) {
									$values[ $key ] = intval($term->term_id);
								}
							}
						}

						wp_set_object_terms( $post_id, $values, $attribute_names[ $i ] );
					}

					if ( ! empty( $values ) ) {
						// Add attribute to array, but don't set values
						$attributes[ sanitize_title( $attribute_names[ $i ] ) ] = array(
							'name'         => wc_clean( $attribute_names[ $i ] ),
							'value'        => '',
							'position'     => $attribute_position[ $i ],
							'is_visible'   => $is_visible,
							'is_variation' => $is_variation,
							'is_taxonomy'  => $is_taxonomy
						);
					}

				} elseif ( isset( $attribute_values[ $i ] ) ) {

					// Text based, possibly separated by pipes (WC_DELIMITER). Preserve line breaks in non-variation attributes.
					$values = $is_variation ? wc_clean( $attribute_values[ $i ] ) : implode( "\n", array_map( 'wc_clean', explode( "\n", $attribute_values[ $i ] ) ) );
					$values = implode( ' ' . WC_DELIMITER . ' ', wc_get_text_attributes( $values ) );

					// Custom attribute - Add attribute to array and set the values
					$attributes[ sanitize_title( $attribute_names[ $i ] ) ] = array(
						'name'         => wc_clean( $attribute_names[ $i ] ),
						'value'        => $values,
						'position'     => $attribute_position[ $i ],
						'is_visible'   => $is_visible,
						'is_variation' => $is_variation,
						'is_taxonomy'  => $is_taxonomy
					);
				}
			 }
		}

		if ( ! function_exists( 'attributes_cmp' ) ) {
			function attributes_cmp( $a, $b ) {
				if ( $a['position'] == $b['position'] ) {
					return 0;
				}

				return ( $a['position'] < $b['position'] ) ? -1 : 1;
			}
		}
		uasort( $attributes, 'attributes_cmp' );

		update_post_meta( $post_id, '_product_attributes', $attributes );

		// Sales and prices
		if ( in_array( $product_type, array( 'variable', 'grouped' ) ) ) {

			// Variable and grouped products have no prices
			update_post_meta( $post_id, '_regular_price', '' );
			update_post_meta( $post_id, '_sale_price', '' );
			update_post_meta( $post_id, '_sale_price_dates_from', '' );
			update_post_meta( $post_id, '_sale_price_dates_to', '' );
			update_post_meta( $post_id, '_price', '' );

		} else {

			$date_from = isset( $_POST['_sale_price_dates_from'] ) ? wc_clean( $_POST['_sale_price_dates_from'] ) : '';
			$date_to   = isset( $_POST['_sale_price_dates_to'] ) ? wc_clean( $_POST['_sale_price_dates_to'] ) : '';

			// Dates
			if ( $date_from ) {
				update_post_meta( $post_id, '_sale_price_dates_from', strtotime( $date_from ) );
			} else {
				update_post_meta( $post_id, '_sale_price_dates_from', '' );
			}

			if ( $date_to ) {
				update_post_meta( $post_id, '_sale_price_dates_to', strtotime( $date_to ) );
			} else {
				update_post_meta( $post_id, '_sale_price_dates_to', '' );
			}

			if ( $date_to && ! $date_from ) {
				$date_from = date( 'Y-m-d' );
				update_post_meta( $post_id, '_sale_price_dates_from', strtotime( $date_from ) );
			}

			// Update price if on sale
			if ( '' !== $_POST['_sale_price'] && '' == $date_to && '' == $date_from ) {
				update_post_meta( $post_id, '_price', wc_format_decimal( $_POST['_sale_price'] ) );
			} else {
				update_post_meta( $post_id, '_price', ( $_POST['_regular_price'] === '' ) ? '' : wc_format_decimal( $_POST['_regular_price'] ) );
			}

			if ( '' !== $_POST['_sale_price'] && $date_from && strtotime( $date_from ) <= strtotime( 'NOW', current_time( 'timestamp' ) ) ) {
				update_post_meta( $post_id, '_price', wc_format_decimal( $_POST['_sale_price'] ) );
			}

			if ( $date_to && strtotime( $date_to ) < strtotime( 'NOW', current_time( 'timestamp' ) ) ) {
				update_post_meta( $post_id, '_price', ( $_POST['_regular_price'] === '' ) ? '' : wc_format_decimal( $_POST['_regular_price'] ) );
				update_post_meta( $post_id, '_sale_price_dates_from', '' );
				update_post_meta( $post_id, '_sale_price_dates_to', '' );
			}
		}

		// Update parent if grouped so price sorting works and stays in sync with the cheapest child
		if ( $post->post_parent > 0 || 'grouped' == $product_type || $_POST['previous_parent_id'] > 0 ) {

			$clear_parent_ids = array();

			if ( $post->post_parent > 0 ) {
				$clear_parent_ids[] = $post->post_parent;
			}

			if ( 'grouped' == $product_type ) {
				$clear_parent_ids[] = $post_id;
			}

			if ( $_POST['previous_parent_id'] > 0 ) {
				$clear_parent_ids[] = absint( $_POST['previous_parent_id'] );
			}

			if ( ! empty( $clear_parent_ids ) ) {
				foreach ( $clear_parent_ids as $clear_id ) {
					$children_by_price = get_posts( array(
						'post_parent'    => $clear_id,
						'orderby'        => 'meta_value_num',
						'order'          => 'asc',
						'meta_key'       => '_price',
						'posts_per_page' => 1,
						'post_type'      => 'product',
						'fields'         => 'ids'
					) );

					if ( $children_by_price ) {
						foreach ( $children_by_price as $child ) {
							$child_price = get_post_meta( $child, '_price', true );
							update_post_meta( $clear_id, '_price', $child_price );
						}
					}

					wc_delete_product_transients( $clear_id );
				}
			}
		}

		// Sold Individually
		if ( ! empty( $_POST['_sold_individually'] ) ) {
			update_post_meta( $post_id, '_sold_individually', 'yes' );
		} else {
			update_post_meta( $post_id, '_sold_individually', '' );
		}

		// Stock Data
		if ( 'yes' === get_option( 'woocommerce_manage_stock' ) ) {

			$manage_stock = 'no';
			$backorders   = 'no';
			$stock_status = wc_clean( $_POST['_stock_status'] );

			if ( 'external' === $product_type ) {

				$stock_status = 'instock';

			} elseif ( 'variable' === $product_type ) {

				// Stock status is always determined by children so sync later
				$stock_status = '';

				if ( ! empty( $_POST['_manage_stock'] ) ) {
					$manage_stock = 'yes';
					$backorders   = wc_clean( $_POST['_backorders'] );
				}

			} elseif ( 'grouped' !== $product_type && ! empty( $_POST['_manage_stock'] ) ) {
				$manage_stock = 'yes';
				$backorders   = wc_clean( $_POST['_backorders'] );
			}

			update_post_meta( $post_id, '_manage_stock', $manage_stock );
			update_post_meta( $post_id, '_backorders', $backorders );

			if ( $stock_status ) {
				wc_update_product_stock_status( $post_id, $stock_status );
			}

			if ( ! empty( $_POST['_manage_stock'] ) ) {
				wc_update_product_stock( $post_id, wc_stock_amount( $_POST['_stock'] ) );
			} else {
				update_post_meta( $post_id, '_stock', '' );
			}

		} elseif ( 'variable' !== $product_type ) {
			wc_update_product_stock_status( $post_id, wc_clean( $_POST['_stock_status'] ) );
		}

		// Cross sells and upsells
		$upsell_ready = ('' != $_POST['upsell_ids']) ? implode( ',', $_POST['upsell_ids'] ) : '';
		$crosell_ready = ('' != $_POST['crosssell_ids']) ? implode( ',', $_POST['crosssell_ids'] ) : '';
		$upsells    = isset( $_POST['upsell_ids'] ) ? array_filter( array_map( 'intval', explode( ',', $upsell_ready ) ) ) : array();
		$crosssells = isset( $_POST['crosssell_ids'] ) ? array_filter( array_map( 'intval', explode( ',', $crosell_ready ) ) ) : array();

		update_post_meta( $post_id, '_upsell_ids', $upsells );
		update_post_meta( $post_id, '_crosssell_ids', $crosssells );

		// Downloadable options
		if ( 'yes' == $is_downloadable ) {

			$_download_limit = absint( $_POST['_download_limit'] );
			if ( ! $_download_limit ) {
				$_download_limit = ''; // 0 or blank = unlimited
			}

			$_download_expiry = absint( $_POST['_download_expiry'] );
			if ( ! $_download_expiry ) {
				$_download_expiry = ''; // 0 or blank = unlimited
			}

			// file paths will be stored in an array keyed off md5(file path)
			$files = array();

			if ( isset( $_POST['_wc_file_urls'] ) ) {
				$file_names         = isset( $_POST['_wc_file_names'] ) ? $_POST['_wc_file_names'] : array();
				$file_urls          = isset( $_POST['_wc_file_urls'] )  ? wp_unslash( array_map( 'trim', $_POST['_wc_file_urls'] ) ) : array();
				$file_url_size      = sizeof( $file_urls );
				$allowed_file_types = apply_filters( 'woocommerce_downloadable_file_allowed_mime_types', get_allowed_mime_types() );

				for ( $i = 0; $i < $file_url_size; $i ++ ) {
					if ( ! empty( $file_urls[ $i ] ) ) {
						// Find type and file URL
						if ( 0 === strpos( $file_urls[ $i ], 'http' ) ) {
							$file_is  = 'absolute';
							$file_url = esc_url_raw( $file_urls[ $i ] );
						} elseif ( '[' === substr( $file_urls[ $i ], 0, 1 ) && ']' === substr( $file_urls[ $i ], -1 ) ) {
							$file_is  = 'shortcode';
							$file_url = wc_clean( $file_urls[ $i ] );
						} else {
							$file_is = 'relative';
							$file_url = wc_clean( $file_urls[ $i ] );
						}

						$file_name = wc_clean( $file_names[ $i ] );
						$file_hash = md5( $file_url );

						// Validate the file extension
						if ( in_array( $file_is, array( 'absolute', 'relative' ) ) ) {
							$file_type  = wp_check_filetype( strtok( $file_url, '?' ) );
							$parsed_url = parse_url( $file_url, PHP_URL_PATH );
							$extension  = pathinfo( $parsed_url, PATHINFO_EXTENSION );

							if ( ! empty( $extension ) && ! in_array( $file_type['type'], $allowed_file_types ) ) {
								$message = sprintf( __( 'The downloadable file %s cannot be used as it does not have an allowed file type. Allowed types include: %s', 'frozr' ), '<code>' . basename( $file_url ) . '</code>', '<code>' . implode( ', ', array_keys( $allowed_file_types ) ) . '</code>' );
								wp_send_json_error( $message );
								continue;
							}
						}

						// Validate the file exists
						if ( 'relative' === $file_is ) {
							$_file_url = $file_url;
							if ( '..' === substr( $file_url, 0, 2 ) || '/' !== substr( $file_url, 0, 1 ) ) {
								$_file_url = realpath( ABSPATH . $file_url );
							}

							if ( ! apply_filters( 'woocommerce_downloadable_file_exists', file_exists( $_file_url ), $file_url ) ) {
								$message = sprintf( __( 'The downloadable file %s cannot be used as it does not exist on the server.', 'frozr' ), '<code>' . $file_url . '</code>' );
								wp_send_json_error( $message );
								continue;
							}
						}

						$files[ $file_hash ] = array(
							'name' => $file_name,
							'file' => $file_url
						);
					}
				}
			}

			// grant permission to any newly added files on any existing orders for this product prior to saving
			do_action( 'woocommerce_process_product_file_download_paths', $post_id, 0, $files );

			update_post_meta( $post_id, '_downloadable_files', $files );
			update_post_meta( $post_id, '_download_limit', $_download_limit );
			update_post_meta( $post_id, '_download_expiry', $_download_expiry );

			if ( isset( $_POST['_download_type'] ) ) {
				update_post_meta( $post_id, '_download_type', wc_clean( $_POST['_download_type'] ) );
			}
		}

		// Product url
		if ( 'external' == $product_type ) {

			if ( isset( $_POST['_product_url'] ) ) {
				update_post_meta( $post_id, '_product_url', esc_url_raw( $_POST['_product_url'] ) );
			}

			if ( isset( $_POST['_button_text'] ) ) {
				update_post_meta( $post_id, '_button_text', wc_clean( $_POST['_button_text'] ) );
			}
		}

		// Save variations
		if ( 'variable' == $product_type ) {
			// Update parent if variable so price sorting works and stays in sync with the cheapest child
			WC_Product_Variable::sync( $post_id );
			WC_Product_Variable::sync_stock_status( $post_id );
		}
		
		// Do action for product type
		do_action( 'woocommerce_process_product_meta_' . $product_type, $post_id );
}

/**
* Save meta box data
*
*/
function frozr_save_variations( $post_id, $post ) {
		global $wpdb;

		$attributes = (array) maybe_unserialize( get_post_meta( $post_id, '_product_attributes', true ) );

		if ( isset( $_POST['variable_sku'] ) ) {
			$variable_post_id               = $_POST['variable_post_id'];
			$variable_sku                   = $_POST['variable_sku'];
			$variable_regular_price         = $_POST['variable_regular_price'];
			$variable_sale_price            = $_POST['variable_sale_price'];
			$upload_image_id                = $_POST['upload_image_id'];
			$variable_download_limit        = $_POST['variable_download_limit'];
			$variable_download_expiry       = $_POST['variable_download_expiry'];
			$variable_shipping_class        = $_POST['variable_shipping_class'];
			$variable_tax_class             = isset( $_POST['variable_tax_class'] ) ? $_POST['variable_tax_class'] : array();
			$variable_menu_order            = $_POST['variation_menu_order'];
			$variable_sale_price_dates_from = $_POST['variable_sale_price_dates_from'];
			$variable_sale_price_dates_to   = $_POST['variable_sale_price_dates_to'];

			$variable_weight                = isset( $_POST['variable_weight'] ) ? $_POST['variable_weight'] : array();
			$variable_length                = isset( $_POST['variable_length'] ) ? $_POST['variable_length'] : array();
			$variable_width                 = isset( $_POST['variable_width'] ) ? $_POST['variable_width'] : array();
			$variable_height                = isset( $_POST['variable_height'] ) ? $_POST['variable_height'] : array();
			$variable_enabled               = isset( $_POST['variable_enabled'] ) ? $_POST['variable_enabled'] : array();
			$variable_is_virtual            = isset( $_POST['variable_is_virtual'] ) ? $_POST['variable_is_virtual'] : array();
			$variable_is_downloadable       = isset( $_POST['variable_is_downloadable'] ) ? $_POST['variable_is_downloadable'] : array();

			$variable_manage_stock          = isset( $_POST['variable_manage_stock'] ) ? $_POST['variable_manage_stock'] : array();
			$variable_stock                 = isset( $_POST['variable_stock'] ) ? $_POST['variable_stock'] : array();
			$variable_backorders            = isset( $_POST['variable_backorders'] ) ? $_POST['variable_backorders'] : array();
			$variable_stock_status          = isset( $_POST['variable_stock_status'] ) ? $_POST['variable_stock_status'] : array();

			$variable_description           = isset( $_POST['variable_description'] ) ? $_POST['variable_description'] : array();

			$max_loop = max( array_keys( $_POST['variable_post_id'] ) );

			for ( $i = 0; $i <= $max_loop; $i ++ ) {

				if ( ! isset( $variable_post_id[ $i ] ) ) {
					continue;
				}

				$variation_id = absint( $variable_post_id[ $i ] );

				// Checkboxes
				$is_virtual      = isset( $variable_is_virtual[ $i ] ) ? 'yes' : 'no';
				$is_downloadable = isset( $variable_is_downloadable[ $i ] ) ? 'yes' : 'no';
				$post_status     = isset( $variable_enabled[ $i ] ) ? 'publish' : 'private';
				$manage_stock    = isset( $variable_manage_stock[ $i ] ) ? 'yes' : 'no';

				// Generate a useful post title
				$variation_post_title = sprintf( __( 'Variation #%s of %s', 'frozr' ), absint( $variation_id ), esc_html( get_the_title( $post_id ) ) );

				// Update or Add post
				if ( ! $variation_id ) {

					$variation = array(
						'post_title'   => $variation_post_title,
						'post_content' => '',
						'post_status'  => $post_status,
						'post_author'  => get_current_user_id(),
						'post_parent'  => $post_id,
						'post_type'    => 'product_variation',
						'menu_order'   => $variable_menu_order[ $i ]
					);

					$variation_id = wp_insert_post( $variation );

					do_action( 'woocommerce_create_product_variation', $variation_id );

				} else {

					$wpdb->update( $wpdb->posts, array( 'post_status' => $post_status, 'post_title' => $variation_post_title, 'menu_order' => $variable_menu_order[ $i ] ), array( 'ID' => $variation_id ) );

					do_action( 'woocommerce_update_product_variation', $variation_id );

				}

				// Only continue if we have a variation ID
				if ( ! $variation_id ) {
					continue;
				}

				// Unique SKU
				$sku     = get_post_meta( $variation_id, '_sku', true );
				$new_sku = wc_clean( stripslashes( $variable_sku[ $i ] ) );

				if ( '' == $new_sku ) {
					update_post_meta( $variation_id, '_sku', '' );
				} elseif ( $new_sku !== $sku ) {

					if ( ! empty( $new_sku ) ) {
						$unique_sku = wc_product_has_unique_sku( $variation_id, $new_sku );

						if ( ! $unique_sku ) {
						    $message = sprintf( '<div class="alert alert-success">%s</div>', __( 'Variation SKU must be unique.', 'frozr' ) );
							wp_send_json_error( $message );
						} else {
							update_post_meta( $variation_id, '_sku', $new_sku );
						}
					} else {
						update_post_meta( $variation_id, '_sku', '' );
					}
				}

				// Update post meta
				update_post_meta( $variation_id, '_thumbnail_id', absint( $upload_image_id[ $i ] ) );
				update_post_meta( $variation_id, '_virtual', wc_clean( $is_virtual ) );
				update_post_meta( $variation_id, '_downloadable', wc_clean( $is_downloadable ) );

				if ( isset( $variable_weight[ $i ] ) ) {
					update_post_meta( $variation_id, '_weight', ( '' === $variable_weight[ $i ] ) ? '' : wc_format_decimal( $variable_weight[ $i ] ) );
				}

				if ( isset( $variable_length[ $i ] ) ) {
					update_post_meta( $variation_id, '_length', ( '' === $variable_length[ $i ] ) ? '' : wc_format_decimal( $variable_length[ $i ] ) );
				}

				if ( isset( $variable_width[ $i ] ) ) {
					update_post_meta( $variation_id, '_width', ( '' === $variable_width[ $i ] ) ? '' : wc_format_decimal( $variable_width[ $i ] ) );
				}

				if ( isset( $variable_height[ $i ] ) ) {
					update_post_meta( $variation_id, '_height', ( '' === $variable_height[ $i ] ) ? '' : wc_format_decimal( $variable_height[ $i ] ) );
				}

				// Stock handling
				update_post_meta( $variation_id, '_manage_stock', $manage_stock );

				// Only update stock status to user setting if changed by the user, but do so before looking at stock levels at variation level
				if ( ! empty( $variable_stock_status[ $i ] ) ) {
					wc_update_product_stock_status( $variation_id, $variable_stock_status[ $i ] );
				}

				if ( 'yes' === $manage_stock ) {
					update_post_meta( $variation_id, '_backorders', wc_clean( $variable_backorders[ $i ] ) );
					wc_update_product_stock( $variation_id, wc_stock_amount( $variable_stock[ $i ] ) );
				} else {
					delete_post_meta( $variation_id, '_backorders' );
					delete_post_meta( $variation_id, '_stock' );
				}

				// Price handling
				$regular_price = wc_format_decimal( $variable_regular_price[ $i ] );
				$sale_price    = $variable_sale_price[ $i ] === '' ? '' : wc_format_decimal( $variable_sale_price[ $i ] );
				$date_from     = wc_clean( $variable_sale_price_dates_from[ $i ] );
				$date_to       = wc_clean( $variable_sale_price_dates_to[ $i ] );

				update_post_meta( $variation_id, '_regular_price', $regular_price );
				update_post_meta( $variation_id, '_sale_price', $sale_price );

				// Save Dates
				update_post_meta( $variation_id, '_sale_price_dates_from', $date_from ? strtotime( $date_from ) : '' );
				update_post_meta( $variation_id, '_sale_price_dates_to', $date_to ? strtotime( $date_to ) : '' );

				if ( $date_to && ! $date_from ) {
					update_post_meta( $variation_id, '_sale_price_dates_from', strtotime( 'NOW', current_time( 'timestamp' ) ) );
				}

				// Update price if on sale
				if ( '' !== $sale_price && '' === $date_to && '' === $date_from ) {
					update_post_meta( $variation_id, '_price', $sale_price );
				} else {
					update_post_meta( $variation_id, '_price', $regular_price );
				}

				if ( '' !== $sale_price && $date_from && strtotime( $date_from ) < strtotime( 'NOW', current_time( 'timestamp' ) ) ) {
					update_post_meta( $variation_id, '_price', $sale_price );
				}

				if ( $date_to && strtotime( $date_to ) < strtotime( 'NOW', current_time( 'timestamp' ) ) ) {
					update_post_meta( $variation_id, '_price', $regular_price );
					update_post_meta( $variation_id, '_sale_price_dates_from', '' );
					update_post_meta( $variation_id, '_sale_price_dates_to', '' );
				}

				if ( isset( $variable_tax_class[ $i ] ) && $variable_tax_class[ $i ] !== 'parent' ) {
					update_post_meta( $variation_id, '_tax_class', wc_clean( $variable_tax_class[ $i ] ) );
				} else {
					delete_post_meta( $variation_id, '_tax_class' );
				}

				if ( 'yes' == $is_downloadable ) {
					update_post_meta( $variation_id, '_download_limit', wc_clean( $variable_download_limit[ $i ] ) );
					update_post_meta( $variation_id, '_download_expiry', wc_clean( $variable_download_expiry[ $i ] ) );

					$files              = array();
					$file_names         = isset( $_POST['_wc_variation_file_names'][ $variation_id ] ) ? array_map( 'wc_clean', $_POST['_wc_variation_file_names'][ $variation_id ] ) : array();
					$file_urls          = isset( $_POST['_wc_variation_file_urls'][ $variation_id ] ) ? array_map( 'wc_clean', $_POST['_wc_variation_file_urls'][ $variation_id ] ) : array();
					$file_url_size      = sizeof( $file_urls );
					$allowed_file_types = get_allowed_mime_types();

					for ( $ii = 0; $ii < $file_url_size; $ii ++ ) {
						if ( ! empty( $file_urls[ $ii ] ) ) {
							// Find type and file URL
							if ( 0 === strpos( $file_urls[ $ii ], 'http' ) ) {
								$file_is  = 'absolute';
								$file_url = esc_url_raw( $file_urls[ $ii ] );
							} elseif ( '[' === substr( $file_urls[ $ii ], 0, 1 ) && ']' === substr( $file_urls[ $ii ], -1 ) ) {
								$file_is  = 'shortcode';
								$file_url = wc_clean( $file_urls[ $ii ] );
							} else {
								$file_is = 'relative';
								$file_url = wc_clean( $file_urls[ $ii ] );
							}

							$file_name = wc_clean( $file_names[ $ii ] );
							$file_hash = md5( $file_url );

							// Validate the file extension
							if ( in_array( $file_is, array( 'absolute', 'relative' ) ) ) {
								$file_type  = wp_check_filetype( strtok( $file_url, '?' ) );
								$parsed_url = parse_url( $file_url, PHP_URL_PATH );
								$extension  = pathinfo( $parsed_url, PATHINFO_EXTENSION );

								if ( ! empty( $extension ) && ! in_array( $file_type['type'], $allowed_file_types ) ) {
									$message = sprintf( __( '#%s &ndash; The downloadable file %s cannot be used as it does not have an allowed file type. Allowed types include: %s', 'frozr' ), $variation_id, '<code>' . basename( $file_url ) . '</code>', '<code>' . implode( ', ', array_keys( $allowed_file_types ) ) . '</code>' );
									wp_send_json_error( $message );
									continue;
								}
							}

							// Validate the file exists
							if ( 'relative' === $file_is && ! apply_filters( 'woocommerce_downloadable_file_exists', file_exists( $file_url ), $file_url ) ) {
									$message = sprintf( __( '#%s &ndash; The downloadable file %s cannot be used as it does not exist on the server.', 'frozr' ), $variation_id, '<code>' . $file_url . '</code>' );
									wp_send_json_error( $message );
								continue;
							}

							$files[ $file_hash ] = array(
								'name' => $file_name,
								'file' => $file_url
							);
						}
					}

					// grant permission to any newly added files on any existing orders for this product prior to saving
					do_action( 'woocommerce_process_product_file_download_paths', $post_id, $variation_id, $files );

					update_post_meta( $variation_id, '_downloadable_files', $files );
				} else {
					update_post_meta( $variation_id, '_download_limit', '' );
					update_post_meta( $variation_id, '_download_expiry', '' );
					update_post_meta( $variation_id, '_downloadable_files', '' );
				}

				update_post_meta( $variation_id, '_variation_description', wp_kses_post( $variable_description[ $i ] ) );

				// Save shipping class
				$variable_shipping_class[ $i ] = ! empty( $variable_shipping_class[ $i ] ) ? (int) $variable_shipping_class[ $i ] : '';
				wp_set_object_terms( $variation_id, $variable_shipping_class[ $i ], 'product_shipping_class');

				// Update Attributes
				$updated_attribute_keys = array();
				foreach ( $attributes as $attribute ) {
					if ( $attribute['is_variation'] ) {
						$attribute_key            = 'attribute_' . sanitize_title( $attribute['name'] );
						$updated_attribute_keys[] = $attribute_key;

						if ( $attribute['is_taxonomy'] ) {
							// Don't use wc_clean as it destroys sanitized characters
							$value = isset( $_POST[ $attribute_key ][ $i ] ) ? sanitize_title( stripslashes( $_POST[ $attribute_key ][ $i ] ) ) : '';
						} else {
							$value = isset( $_POST[ $attribute_key ][ $i ] ) ? wc_clean( stripslashes( $_POST[ $attribute_key ][ $i ] ) ) : '';
						}

						update_post_meta( $variation_id, $attribute_key, $value );
					}
				}

				// Remove old taxonomies attributes so data is kept up to date - first get attribute key names
				$delete_attribute_keys = $wpdb->get_col( $wpdb->prepare( "SELECT meta_key FROM {$wpdb->postmeta} WHERE meta_key LIKE 'attribute_%%' AND meta_key NOT IN ( '" . implode( "','", $updated_attribute_keys ) . "' ) AND post_id = %d;", $variation_id ) );

				foreach ( $delete_attribute_keys as $key ) {
					delete_post_meta( $variation_id, $key );
				}

				do_action( 'woocommerce_save_product_variation', $variation_id, $i );
			}
		}

		// Update parent if variable so price sorting works and stays in sync with the cheapest child
		WC_Product_Variable::sync( $post_id );

		// Update default attribute options setting
		$default_attributes = array();

		foreach ( $attributes as $attribute ) {

			if ( $attribute['is_variation'] ) {
				$value = '';

				if ( isset( $_POST[ 'default_attribute_' . sanitize_title( $attribute['name'] ) ] ) ) {
					if ( $attribute['is_taxonomy'] ) {
						// Don't use wc_clean as it destroys sanitized characters
						$value = sanitize_title( trim( stripslashes( $_POST[ 'default_attribute_' . sanitize_title( $attribute['name'] ) ] ) ) );
					} else {
						$value = wc_clean( trim( stripslashes( $_POST[ 'default_attribute_' . sanitize_title( $attribute['name'] ) ] ) ) );
					}
				}

				if ( $value ) {
					$default_attributes[ sanitize_title( $attribute['name'] ) ] = $value;
				}
			}
		}

	update_post_meta( $post_id, '_default_attributes', $default_attributes );
}
//filter product archive loop
function frozr_filter_product_archive() {
	global $wp_query;
	if (isset($_COOKIE['frozr_user_location']) ) {
		$utermtwo = get_term_by( 'slug', $_COOKIE['frozr_user_location'], 'location');
		$utresulttwo = get_objects_in_term( (int) $utermtwo->term_id, 'location');
	}
	if (isset($_GET['dish'])) {
		if (isset($_COOKIE['frozr_user_location'])) {
			if ( $_GET['dish'] == 'veg') {
				$args = array_merge( $wp_query->query_vars, array('author__in' => array_unique($utresulttwo),'meta_query' => array(array('key' => '_dish_veg','value' => 'veg','compare' => 'IN'))) );
			} elseif ($_GET['dish'] == 'nonveg') {
				$args = array_merge( $wp_query->query_vars, array('author__in' => array_unique($utresulttwo), 'meta_query' => array(array('key' => '_dish_veg','value' => 'nonveg','compare' => 'IN'))) );
			} elseif ($_GET['dish'] == 'spicy') {
				$args = array_merge( $wp_query->query_vars, array('author__in' => array_unique($utresulttwo), 'meta_query' => array(array('key' => '_dish_spicy','value' => 'yes','compare' => 'IN'))) );
			} elseif ($_GET['dish'] == 'vegspicy') {
				$args = array_merge( $wp_query->query_vars, array('author__in' => array_unique($utresulttwo), 'meta_query' => array(array('key' => '_dish_spicy','value' => 'yes','compare' => 'IN'), array('key' => '_dish_veg','value' => 'veg'))) );
			} elseif ($_GET['dish'] == 'nonvegspicy') {
				$args = array_merge( $wp_query->query_vars, array('author__in' => array_unique($utresulttwo), 'meta_query' => array(array('key' => '_dish_spicy','value' => 'yes','compare' => 'IN'), array('key' => '_dish_veg','value' => 'nonveg'))) );
			}
		} else {
		if ( $_GET['dish'] == 'veg') {
			$args = array_merge( $wp_query->query_vars, array('meta_query' => array(array('key' => '_dish_veg','value' => 'veg','compare' => 'IN'))) );
		} elseif ($_GET['dish'] == 'nonveg') {
			$args = array_merge( $wp_query->query_vars, array('meta_query' => array(array('key' => '_dish_veg','value' => 'nonveg','compare' => 'IN'))) );
		} elseif ($_GET['dish'] == 'spicy') {
			$args = array_merge( $wp_query->query_vars, array('meta_query' => array(array('key' => '_dish_spicy','value' => 'yes','compare' => 'IN'))) );
		} elseif ($_GET['dish'] == 'vegspicy') {
			$args = array_merge( $wp_query->query_vars, array('meta_query' => array(array('key' => '_dish_spicy','value' => 'yes','compare' => 'IN'), array('key' => '_dish_veg','value' => 'veg'))) );
		} elseif ($_GET['dish'] == 'nonvegspicy') {
			$args = array_merge( $wp_query->query_vars, array('meta_query' => array(array('key' => '_dish_spicy','value' => 'yes','compare' => 'IN'), array('key' => '_dish_veg','value' => 'nonveg'))) );
		}
		}
	query_posts( $args );
	} elseif (isset($_COOKIE['frozr_user_location'])) {
		$args = array_merge( $wp_query->query_vars, array('author__in' => array_unique($utresulttwo)) );
		query_posts( $args );
	}
}
add_action('before_product_archive_loop','frozr_filter_product_archive');

//add the location notice to items loops
function frozr_dish_location_notice() {
	if(!isset($_COOKIE['frozr_user_location'])) {
		frozr_location_not_set('dish');
	}
}
add_action('before_dishes_list','frozr_dish_location_notice');