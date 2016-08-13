<?php
/**
 * Frozr Coupons Class
 *
 *
 * @package FrozrCoreLibrary
 * @subpackage Frozrmarketlibrary
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//Coupons page header
function frozr_coupons_header_nav() {

$permalink = home_url( '/dashboard/coupons/');
$is_edit_page = isset( $_GET['view'] ) && $_GET['view'] == 'add_coupons';
	if ( !$is_edit_page && !is_super_admin() || $is_edit_page ) { ?>
	<div class="coupons-listing-header">
		
		<?php do_action('frozr_before_coupons_header_list'); ?>

		<span class="coupons_title">
			<?php if ( $is_edit_page ) { ?>
			<a href="<?php echo $permalink; ?>" class="ol_coupons_title"><?php _e( '&larr; Coupons', 'frozr' ); ?></a> <?php
			} ?>
		</span>
		<?php if ( !$is_edit_page ) { ?>
		<span class="add_coupons">
			<a href="<?php echo add_query_arg( array( 'view' => 'add_coupons'), $permalink ); ?>" class="pull-left"><i class="fs-icon-gift">&nbsp;</i> <?php _e( 'Add new Coupon', 'frozr' ); ?></a>
		</span>
		<?php } ?>

		<?php do_action('frozr_after_coupons_header_list'); ?>

	</div>
	<?php }

}
//list coupons
function frozr_list_user_coupons() {
	global $post;
	
	$permalink = home_url( '/dashboard/coupons/');
	
	$get_curnt_user = (is_super_admin()) ? '' : get_current_user_id();
	$paged = (get_query_var( 'paged' )) ? get_query_var( 'paged' ) : 1;
	$args = apply_filters('frozr_list_user_coupons_args',array(
	'post_type' => 'shop_coupon',
	'post_status' => array('publish'),
	'posts_per_page' => 10,
	'author' => $get_curnt_user,
	'paged' => $paged
	));
	
	$coupon_query = new WP_Query( $args );

	if ( $coupon_query->have_posts() ) { ?>

	<table class="table_coupons_list ui-responsive table-stroke" data-role="table">
		<thead>
			<tr class="table_collumns">
			<th data-priority="1"><?php _e('Code', 'frozr'); ?></th>
			<th data-priority="5"><?php _e('Coupon type', 'frozr'); ?></th>
			<th data-priority="3"><?php _e('Coupon amount', 'frozr'); ?></th>
			<th data-priority="4"><?php _e('Product IDs', 'frozr'); ?></th>
			<th data-priority="6"><?php _e('Usage / Limit', 'frozr'); ?></th>
			<th data-priority="2"><?php _e('Expiry date', 'frozr'); ?></th>
			<?php if(is_super_admin()) { ?>
			<th data-priority="7"><?php _e('Author', 'frozr'); ?></th>
			<?php } ?>
			<?php do_action('frozr_after_list_user_coupons_table_header'); ?>
			</tr>
		</thead>
		
		<tbody>
		<?php
		while ($coupon_query->have_posts()) { $coupon_query->the_post();
			$coupon_author = frozr_get_store_info($post->post_author); ?>
			<tr>
				<td class="coupon-code">
					<?php $edit_url = add_query_arg( array('post' => $post->ID, 'action' => 'edit', 'view' => 'add_coupons'), $permalink ); ?>
					<div class="code">
						<a href="<?php echo $edit_url; ?>"><span><?php echo esc_attr( $post->post_title ); ?></span></a>
					</div>

					<div class="row-actions">
						<span class="edit"><a href="<?php echo $edit_url; ?>"><?php _e( 'Edit', 'frozr' ); ?></a> | </span>
						<span class="delete_coupon" data-coupid="<?php echo $post->ID; ?>"><?php _e('delete', 'frozr'); ?></span>
					</div>
				</td>
				<td>
					<?php echo esc_html( wc_get_coupon_type( get_post_meta( $post->ID, 'discount_type', true ) ) ); ?>
				</td>
				<td>
					<?php echo esc_attr( get_post_meta( $post->ID, 'coupon_amount', true ) ); ?>
				</td>
				<td>
					<?php
					$product_ids = get_post_meta( $post->ID, 'product_ids', true );
					$product_ids = $product_ids ? array_map( 'absint', explode( ',', $product_ids ) ) : array();

					if ( sizeof( $product_ids ) > 0 ) {
						echo esc_html( implode( ', ', $product_ids ) );
					} else {
						echo '&ndash;';
					} ?>
				</td>
				<td>
					<?php

					$usage_count = absint( get_post_meta( $post->ID, 'usage_count', true ) );
					$usage_limit = esc_html( get_post_meta($post->ID, 'usage_limit', true) );

					if ( $usage_limit ) {
						printf( '%s / %s', $usage_count, $usage_limit );
					} else {
						printf( '%s / &infin;', $usage_count );
					}
					?>
				</td>
				<td>
					<?php
					$expiry_date = get_post_meta($post->ID, 'expiry_date', true);

					if ( $expiry_date ) {
						echo esc_html( date_i18n( 'F j, Y', strtotime( $expiry_date ) ) );
					} else {
						echo '&ndash;';
					}
					?>
				</td>
				<?php if (is_super_admin()) { ?>
					<td>
						<?php echo get_the_author_meta('login', $post->post_author) . ' (' . $coupon_author['store_name'] . ')'; ?>
					</td>
				<?php } ?>
			<?php do_action('frozr_after_list_user_coupons_table_body'); ?>
			</tr>
		<?php }
		if ( $coupon_query->max_num_pages > 1 ) {
				echo '<div class="pagination-wrap">';
				$page_links = paginate_links( array(
				'current' => max( 1, get_query_var( 'paged' ) ),
				'total' => $coupon_query->max_num_pages,
				'base' => str_replace( $post->ID, '%#%', esc_url( get_pagenum_link( $post->ID ) ) ),
				'type' => 'array',
				'prev_text' => __( '&laquo; Previous', 'frozr' ),
				'next_text' => __( 'Next &raquo;', 'frozr' )
				) );

			echo '<ul class="pagination"><li>';
			echo join("</li>\n\t<li>", $page_links);
			echo "</li>\n</ul>\n";
			echo '</div>';
		} ?>
 		</tbody>
	</table>

	<?php } else { ?>
		<div class="style_box alert alert-warning fs-icon-warning-sign">
			<p><?php _e( 'Sorry, no coupons found!', 'frozr' ); ?></p>
		</div>
	<?php
	}
}
//coupon form
function frozr_add_coupons_form() {
	global $post;
	$button_name = __( 'Create Coupon', 'frozr' );

	if ( isset( $_GET['post'] ) && $_GET['action'] == 'edit' ) {
		
		if ( !frozr_is_author( intval($_GET['post']) ) && !is_super_admin() ) {
			wp_die( __( 'Are you cheating?', 'frozr' ) );
		}

		$post = get_post( intval($_GET['post']) );
		$button_name = __( 'Update Coupon', 'frozr' );

		$discount_type = get_post_meta( $post->ID, 'discount_type', true );
		$amount = get_post_meta( $post->ID, 'coupon_amount', true );

		$products = get_post_meta( $post->ID, 'product_ids', true );
		$exclude_products = get_post_meta( $post->ID, 'exclude_product_ids', true );
		$product_categories = get_post_meta( $post->ID, 'product_categories', true );
		$exclude_product_categories = get_post_meta( $post->ID, 'exclude_product_categories', true );
		$usage_limit = get_post_meta( $post->ID, 'usage_limit', true );
		$usage_limit_per_user = get_post_meta( $post->ID, 'usage_limit_per_user', true );
		$limit_usage_to_x_items = get_post_meta( $post->ID, 'limit_usage_to_x_items', true );
		$expire = get_post_meta( $post->ID, 'expiry_date', true );
		$apply_before_tax = get_post_meta( $post->ID, 'apply_before_tax', true );
		$show_cp_inshop = get_post_meta( $post->ID, 'show_cp_inshop', true );
		$show_cp_inshop_txt = get_post_meta( $post->ID, 'show_cp_inshop_txt', true );
		$free_shipping = get_post_meta( $post->ID, 'free_shipping', true );
		$individual_uses = get_post_meta( $post->ID, 'individual_use', true );
		$exclude_sale_item = get_post_meta( $post->ID, 'exclude_sale_items', true );
		$minimum_amount = get_post_meta( $post->ID, 'minimum_amount', true );
		$maximum_amount = get_post_meta( $post->ID, 'maximum_amount', true );
		$customer_email = get_post_meta( $post->ID, 'customer_email', true );
		
		$post_id = isset( $post->ID ) ? $post->ID : 0;
		$post_title = isset( $post->post_title ) ? $post->post_title : '';
		$description = isset( $post->post_content ) ? $post->post_content : '';
	} else {
		$post_id = 0;
		$post_title = '';
		$description = '';
	}

	$discount_type = isset( $discount_type ) ? $discount_type : '';
	if ( isset( $discount_type ) ) {
		if ( $discount_type == 'coupon_percent_product' ) {
		$discount_type = 'selected';
		}
	}

	$amount = isset( $amount ) ? $amount : '';
	$products = isset( $products ) ? $products : '';
	$exclude_products = isset( $exclude_products ) ? $exclude_products : '';
	$product_categories = isset( $product_categories ) ? $product_categories : '';
	$exclude_product_categories = isset( $exclude_product_categories ) ? $exclude_product_categories : '';
	$usage_limit = isset( $usage_limit ) ? $usage_limit : '';
	$usage_limit_per_user = isset( $usage_limit_per_user ) ? $usage_limit_per_user : '';
	$limit_usage_to_x_items = isset( $limit_usage_to_x_items ) ? $limit_usage_to_x_items : '';
	$expire = isset( $expire ) ? $expire : '';

	if ( isset( $show_cp_inshop ) && $show_cp_inshop == 'yes' ) {
		$show_cp_inshop = 'checked';
	} else {
		$show_cp_inshop = '';
	}

	if ( isset( $free_shipping ) && $free_shipping == 'yes' ) {
		$free_shipping = 'checked';
	} else {
		$free_shipping = '';
	}

	if ( isset( $individual_uses ) && $individual_uses == 'yes' ) {
		$individual_uses = 'checked';
	} else {
		$individual_uses = '';
	}

	if ( isset( $apply_before_tax ) && $apply_before_tax == 'yes' ) {
		$apply_before_tax = 'checked';
	} else {
		$apply_before_tax = '';
	}

	if ( isset( $exclude_sale_item ) && $exclude_sale_item == 'yes' ) {
		$exclude_sale_item = 'checked';
	} else {
		$exclude_sale_item = '';
	}

	$minimum_amount = isset( $minimum_amount ) ? $minimum_amount : '';
	$maximum_amount = isset( $maximum_amount ) ? $maximum_amount : '';
	$customer_email = isset( $customer_email ) ? implode( ',', $customer_email ) : '';

?>

	<form id="coupons_form" method="post" action="" class="coupons_form">
		<div class="form-group form-group coupons-form-group">
			<label class="coupons-control-label" for="title"><?php _e( 'Coupon Title', 'frozr' ); ?><span class="required"> *</span></label>
			<input id="title" name="title" required value="<?php echo esc_attr( $post_title ); ?>" placeholder="Title" class="form-control input-md" type="text">
		</div>
		<div class="form-group coupons-form-group">
			<label class="coupons-control-label" for="description"><?php _e( 'Description', 'frozr' ); ?></label>
			<div class="coupons-control-input">
				<textarea class="form-control" id="description" name="description"><?php echo esc_textarea( $description ); ?></textarea>
			</div>
		</div>
		<div class="form-group coupons-form-group">
			<label class="coupons-control-label" for="discount_type"><?php _e( 'Discount Type', 'frozr' ); ?></label>
			<div class="coupons-control-input">
				<select id="discount_type" name="discount_type" class="form-control" data-role="none">
					<option value="fixed_product"><?php _e( 'Product Discount', 'frozr' ); ?></option>
					<option value="percent_product"><?php _e( 'Product % Discount', 'frozr' ); ?></option>
				</select>
			</div>
		</div>
		<div class="form-group coupons-form-group">
			<label class="coupons-control-label" for="amount"><?php _e( 'Amount', 'frozr' ); ?><span class="required"> *</span></label>
			<input id="amount" required value="<?php echo esc_attr( $amount ); ?>" name="amount" placeholder="Amount" class="form-control input-md" type="text">
		</div>
		<div class="form-group">
			<label class="control-label" for="email_restrictions"><?php _e( 'Email Restrictions', 'frozr' ); ?></label>
			<input id="email_restrictions" value="<?php echo esc_attr( $customer_email ); ?>" name="email_restrictions" placeholder="<?php _e( 'Email restrictions', 'frozr' ); ?>" class="form-control input-md" type="text">
		</div>
		<div class="form-group">
			<label class="control-label" for="usage_limit"><?php _e( 'Usage Limit per coupon', 'frozr' ); ?><a href="#" title="<?php _e('How many times this coupon can be used before it is void.','frozr'); ?>"> [?]</a></label>
			<input id="usage_limit" value="<?php echo esc_attr( $usage_limit ); ?>" name="usage_limit" placeholder="<?php _e( 'Unlimited usage', 'frozr' ); ?>" class="form-control input-md" type="number">
		</div>
		<div class="form-group">
			<label class="control-label" for="limit_usage_to_x_items"><?php _e( 'Limit usage to X items', 'frozr' ); ?><a href="#" title="<?php _e('The maximum number of individual items this coupon can apply to when using product discounts. Leave blank to apply to all qualifying items in cart.','frozr'); ?>"> [?]</a></label>
			<input id="limit_usage_to_x_items" value="<?php echo esc_attr( $limit_usage_to_x_items ); ?>" name="limit_usage_to_x_items" placeholder="<?php _e( 'Apply to all qualifying items in cart', 'frozr' ); ?>" class="form-control input-md" type="number">
		</div>
		<div class="form-group">
			<label class="control-label" for="usage_limit_per_user"><?php _e( 'Usage limit per user', 'frozr' ); ?><a href="#" title="<?php _e('How many times this coupon can be used by an invidual user. Uses billing email for guests, and user ID for logged in users.','frozr'); ?>"> [?]</a></label>
			<input id="usage_limit_per_user" value="<?php echo esc_attr( $usage_limit_per_user ); ?>" name="usage_limit_per_user" placeholder="<?php _e( 'Unlimited usage', 'frozr' ); ?>" class="form-control input-md" type="number">
		</div>
		<div class="form-group">
			<label class="control-label" for="frozr-expire"><?php _e( 'Expire Date', 'frozr' ); ?></label>
			<input id="frozr-expire" value="<?php echo esc_attr( $expire ); ?>" name="expire" placeholder="<?php _e( 'Expire Date', 'frozr' ); ?>" class="form-control input-md" type="text">
		</div>

		<?php
		$user = is_super_admin() ? $post->post_author : get_current_user_id();
		$args = apply_filters('frozr_coupon_products_list_args',array(
		'post_type' => 'product',
		'post_status' => array('publish'),
		'posts_per_page' => -1,
		'author' => $user,
		));

		$query = new WP_Query( $args );
		$products_id = str_replace( ' ', '', $products );
		$products_id = explode( ',', $products_id );
		?>

		<div class="form-group coupons-form-group">
			<label class="coupons-control-label" for="product"><?php _e( 'Products', '' ); ?><span class="required"> *</span></label>
			<div class="coupons-control-input">
				<select id="product" required name="product_drop_down[]" class="form-control" multiple data-role="none">
				<?php
				foreach ($query->posts as $key => $object) {
					if ( in_array( $object->ID, $products_id ) ) {
						$select = 'selected';
					} else {
						$select = '';
					} ?>
					<option <?php echo $select; ?>  value="<?php echo $object->ID; ?>"><?php _e( $object->post_title, 'frozr' ); ?></option>

				<?php } ?>
				</select><img class="help_tip" title='<?php _e( 'Products which need to be in the cart to use this coupon or, for "Product Discounts", which products are discounted.', 'frozr' ); ?>' src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" />
			</div>
		</div>
		<div class="form-group">
			<span class="coupons-control-label"><?php _e( 'Enable Free Shipping', 'frozr' ); ?></span>
			<div class="coupons-control-input">
				<label class="control-label" for="checkboxes-0" ><?php _e( 'Check this box if the coupon grants free delivery.', 'frozr' ); ?></label>
				<input id="checkboxes-0" <?php echo $free_shipping; ?> name="enable_free_ship" class="form-control input-md" value="yes" type="checkbox">
			</div>
		</div>
		<div class="form-group">
			<span class="coupons-control-label"><?php _e( 'Individual use only', 'frozr' ); ?></span>
			<div class="coupons-control-input">
				<label class="control-label" for="checkboxes-1" ><?php _e( 'Check this box if the coupon cannot be used in conjunction with other coupons.', 'frozr' ); ?></label>
				<input id="checkboxes-1" <?php echo $individual_uses; ?> name="individual_use" class="form-control input-md" value="yes" type="checkbox">
			</div>
		</div>
		<div class="form-group">
			<span class="coupons-control-label"><?php _e( 'Exclude Sale Items', 'frozr' ); ?></span>
			<div class="coupons-control-input">
				<label class="control-label" for="checkboxes-2" ><?php _e( 'Check this box if the coupon should not apply to items on sale. Per-item coupons will only work if the item is not on sale. Per-cart coupons will only work if there are no sale items in the cart.', 'frozr' ); ?></label>
				<input id="checkboxes-2" <?php echo $exclude_sale_item; ?> name="exclude_sale_items" class="form-control input-md" value="yes" type="checkbox">
			</div>
		</div>
		<div class="form-group coupons-form-group">
			<label class="coupons-control-label" for="minium_ammount"><?php _e( 'Minimum spend', 'frozr' ); ?><a href="#" title="<?php _e('This field allows you to set the minimum subtotal needed to use the coupon.','frozr'); ?>"> [?]</a></label>
			<input id="minium_ammount" value="<?php echo $minimum_amount; ?>" name="minium_ammount" placeholder="<?php _e('No Minimum', 'frozr'); ?>" class="form-control input-md" type="text">
		</div>
		<div class="form-group">
			<label class="control-label" for="maxum_ammount"><?php _e( 'Maximum spend', 'frozr' ); ?><a href="#" title="<?php _e('This field allows you to set the maximum subtotal allowed when using the coupon.','frozr'); ?>"> [?]</a></label>
			<input id="maxum_ammount" value="<?php echo $maximum_amount; ?>" name="maxum_ammount" placeholder="<?php _e('No Maximum', 'frozr'); ?>" class="form-control input-md" type="text">
		</div>
		<div class="form-group">
			<span class="coupons-control-label"><?php _e( 'Go public', 'frozr' ); ?></span>
			<div class="coupons-control-input">
				<label class="control-label" for="enable_free_ship" ><?php _e( 'Show the Coupon in the Restaurant page?', 'frozr' ); ?></label>
				<input id="enable_free_ship" <?php echo $show_cp_inshop; ?> name="show_cp_inshop" class="form-control input-md" value="yes" type="checkbox">
			</div>
		</div>
		<div class="form-group">
			<label class="control-label" for="show_cp_inshop_txt" ><?php _e( 'Coupon Text to show in restaurant page.', 'frozr' ); ?></label>
			<input id="show_cp_inshop_txt" value="<?php echo esc_attr( $show_cp_inshop_txt ); ?>" name="show_cp_inshop_txt" placeholder="<?php _e( 'Use (example) coupon in check out to get 10% OFF our items?', 'frozr' ); ?>" class="form-control input-md" type="text">
		</div>
		<?php do_action('frozr_after_add_coupon_form'); ?>
		<input type="hidden" value="<?php echo $post_id; ?>" name="post_id">
		<input type="submit" name="coupon_creation" value="<?php echo $button_name; ?>" class="update_coupon">
	</form>
<?php
}
//Save coupon
add_action( 'wp_ajax_frozr_coupons_create', 'frozr_coupons_create' );
function frozr_coupons_create() {
	ob_start();

	check_ajax_referer( 'coupon_nonce_field', 'security' );
	
	// Check permissions and make sure we have what we need
	if ( !current_user_can( 'frozer' ) || !frozr_is_seller_enabled(get_current_user_id()) || !isset( $_POST['product_drop_down'] ) ) {
		echo $message = __('Something Went Wrong!','frozr');
		die( -1 );
	}
	global $post;
	$seller_id = get_current_user_id();

	$product_ids_query = get_posts( array(
		'posts_per_page'	=> -1,
		'post_type'			=> 'product',
		'post_status'		=> array( 'publish' ),
		'author'			=> $user,
		'fields'			=> 'ids',
	));

	// Check permissions again and make sure we have what we need
	if ( count(array_intersect(array_map( 'intval', (array) $_POST['product_drop_down'] ), $product_ids_query)) == count(array_map( 'intval', (array) $_POST['product_drop_down'] )) && !is_super_admin() ) {
		echo $message = __('Something Went Wrong!','frozr');
		die( -1 );
	}

	$product_ids = implode( ',', array_filter( array_map( 'intval', (array) $_POST['product_drop_down'] ) ) );

	if ( empty( $_POST['post_id'] ) ) {

		if ( is_super_admin() ) {
			echo $message = __('You\'re the site admin, you cant post coupons!','frozr');
			die( -1 );
		}

		$post = apply_filters('frozr_coupons_create_args',array(
		'post_title' => sanitize_title($_POST['title']),
		'post_content' => sanitize_text_field($_POST['description']),
		'post_status' => 'publish',
		'post_type' => 'shop_coupon',
		));

		$post_id = wp_insert_post( $post );

		$message = __('Coupon has been saved successfully!','frozr');

	} else {

		if ( ! frozr_is_author( $_POST['post_id'] ) && ! is_super_admin() ) {
			echo $message = __('Something Went Wrong!','frozr');
			die( -1 );
		}
		$post = apply_filters('frozr_coupons_update_args',array(
		'ID' => intval($_POST['post_id']),
		'post_title' => sanitize_title($_POST['title']),
		'post_content' => sanitize_text_field($_POST['description']),
		'post_status' => 'publish',
		'post_type' => 'shop_coupon',
		));
		
		$post_id = wp_update_post( $post );
		$message = __('Coupon has been updated successfully!','frozr');
	}

	if ( !$post_id ) {
		echo $message = __('Something Went Wrong!','frozr');
		die( -1 );
	}
	
	$customer_email = array_filter( array_map( 'trim', explode( ',', sanitize_text_field( $_POST['email_restrictions'] ) ) ) );
	$type = sanitize_text_field( $_POST['discount_type'] );
	$amount = sanitize_text_field( $_POST['amount'] );
	$usage_limit = empty( $_POST['usage_limit'] ) ? '' : absint( $_POST['usage_limit'] );
	$usage_limit_per_user   = empty( $_POST['usage_limit_per_user'] ) ? '' : absint( $_POST['usage_limit_per_user'] );
	$limit_usage_to_x_items = empty( $_POST['limit_usage_to_x_items'] ) ? '' : absint( $_POST['limit_usage_to_x_items'] );
	$expiry_date = sanitize_text_field( $_POST['expire'] );
	$individual_uses = isset( $_POST['individual_use'] ) ? 'yes' : 'no';
	$apply_before_tax = isset( $_POST['apply_before_tax'] ) ? 'yes' : 'no';
	$show_cp_inshop = isset( $_POST['show_cp_inshop'] ) ? 'yes' : 'no';
	$show_cp_inshop_txt = empty( $_POST['show_cp_inshop_txt'] ) ? '' : sanitize_text_field( $_POST['show_cp_inshop_txt'] );
	$free_shipping = isset( $_POST['enable_free_ship'] ) ? 'yes' : 'no';
	$exclude_sale_items = isset( $_POST['exclude_sale_items'] ) ? 'yes' : 'no';
	$minimum_amount = wc_format_decimal( $_POST['minium_ammount'] );
	$maximum_amount = wc_format_decimal( $_POST['maxum_ammount'] );

	update_post_meta( $post_id, 'discount_type', $type );
	update_post_meta( $post_id, 'coupon_amount', $amount );
	update_post_meta( $post_id, 'product_ids', $product_ids );
	update_post_meta( $post_id, 'individual_use', $individual_uses );
	update_post_meta( $post_id, 'usage_limit', $usage_limit );
	update_post_meta( $post_id, 'usage_limit_per_user', $usage_limit_per_user );
	update_post_meta( $post_id, 'limit_usage_to_x_items', $limit_usage_to_x_items );
	update_post_meta( $post_id, 'expiry_date', $expiry_date );
	update_post_meta( $post_id, 'apply_before_tax', $apply_before_tax );
	update_post_meta( $post_id, 'free_shipping', $free_shipping );
	update_post_meta( $post_id, 'show_cp_inshop', $show_cp_inshop );
	update_post_meta( $post_id, 'show_cp_inshop_txt', $show_cp_inshop_txt );
	update_post_meta( $post_id, 'exclude_sale_items', $exclude_sale_items );
	update_post_meta( $post_id, 'minimum_amount', $minimum_amount );
	update_post_meta( $post_id, 'maximum_amount', $maximum_amount );
	update_post_meta( $post_id, 'customer_email', $customer_email );
	
	do_action('frozr_seller_coupons_created');
	
	//send WC notices
	echo $message;
	die();
}
//Delete Coupon
add_action( 'wp_ajax_frozr_coupun_delete', 'frozr_coupun_delete' );
function frozr_coupun_delete() {
	ob_start();
	
	check_ajax_referer( 'coupon_del_nonce', 'security' );

	$seller_id = get_current_user_id();
	$post_id = intval($_POST['post_id']);
	if ( ! frozr_is_author( $post_id ) && !is_super_admin() || ! frozr_is_seller_enabled($seller_id) && ! is_super_admin()) {
		echo $message = __('Something Went Wrong!','frozr');
		die( -1 );
	}

	wp_delete_post( $post_id , true );

	//send WC notices
	$message = __( 'Coupon has been deleted successfully!', 'frozr' );
	
	echo $message;
}