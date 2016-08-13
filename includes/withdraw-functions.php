<?php
/**
 * Get default withdraw methods
 *
 * @package FrozrCoreLibrary
 * @subpackage Frozrmarketlibrary
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Register withdraw requests
function frozr_lazyeater_withdraw() {

	$rewrite = array(
		'slug'                  => 'withdraws',
		'with_front'            => true,
		'pages'                 => true,
		'feeds'                 => false,
	);
	$args = array(
		'label'                 => __( 'Withdraw', 'frozr' ),
		'description'           => __( 'Withdraw Requests Post Types', 'frozr' ),
		'supports'              => array(),
		'hierarchical'          => false,
		'public'                => false,
		'show_ui'               => false,
		'show_in_menu'          => false,
		'show_in_admin_bar'     => false,
		'show_in_nav_menus'     => false,
		'can_export'            => true,
		'has_archive'           => 'withdraws_archives',
		'exclude_from_search'   => true,
		'publicly_queryable'    => true,
		'rewrite'               => $rewrite,
		'capability_type'       => 'post',
	);
	register_post_type( 'frozr_withdraw', apply_filters( 'frozr_users_withdraw_post_type',$args ));

}
add_action( 'init', 'frozr_lazyeater_withdraw', 0 );

// Register Completed Status
function frozr_custom_post_status() {

	$args = array(
		'label'                     => _x( 'Completed', 'Status General Name', 'frozr' ),
		'label_count'               => _n_noop( 'Completed (%s)',  'Completed (%s)', 'frozr' ), 
		'public'                    => true,
		'show_in_admin_all_list'    => false,
		'show_in_admin_status_list' => false,
		'exclude_from_search'       => true,
	);
	register_post_status( 'completed', apply_filters( 'frozr_withdraw_custom_post_status',$args ));

}
add_action( 'init', 'frozr_custom_post_status', 0 );

//withdraw nav
function frozr_withdraws_page_nav() {
    $permalink = home_url( '/dashboard/withdraw/');
    $status_class = isset( $_GET['withdraw_status'] ) ? sanitize_key($_GET['withdraw_status']) : 'withdraw';
	if (is_super_admin()) { 
		$post_counts = wp_count_posts( 'frozr_withdraw' );
	} else {
		$post_counts = frozr_count_posts( 'frozr_withdraw', get_current_user_id() );
	}
	if (frozr_mobile()) { $active_icon='fs-icon-caret-right'; } else {  $active_icon='fs-icon-caret-up'; }
    ?>
	<div class="withdraw-listing-header">
	<ul class="withdraw-statuses-filter">

		<?php if (!is_super_admin()) { ?>
        <li <?php echo $status_class == 'withdraw' ? "class=\"active $active_icon\"" : ''; ?> >
            <a href="<?php echo $permalink; ?>"><?php _e( 'Withdraw Request', 'frozr' ); ?></a>
        </li>
		<?php } ?>
        <li <?php echo $status_class == 'pending' ? "class=\"active $active_icon\"" : ''; ?> >
            <a href="<?php echo add_query_arg( array( 'withdraw_status' => 'pending' ), $permalink ); ?>"><?php printf( __( 'Pending (%d)', 'frozr' ), $post_counts->pending ); ?></a>
        </li>
        <li <?php echo $status_class == 'completed' ? "class=\"active $active_icon\"" : ''; ?> >
            <a href="<?php echo add_query_arg( array( 'withdraw_status' => 'completed' ), $permalink ); ?>"><?php printf( __( 'Completed (%d)', 'frozr' ), $post_counts->completed ); ?></a>
        </li>
        <li <?php echo $status_class == 'trash' ? "class=\"active $active_icon\"" : ''; ?> >
            <a href="<?php echo add_query_arg( array( 'withdraw_status' => 'trash' ), $permalink ); ?>"><?php printf( __( 'Rejected (%d)', 'frozr' ), $post_counts->trash ); ?></a>
        </li>
		
		<?php do_action('frozr_after_withdraw_page_filter'); ?>
		
    </ul> <!-- .post-statuses-filter -->
	</div>
    <?php
}
//withdraws lists body
function frozr_withdraws_page_body() { 
	
	global $post; ?>
	<?php if (!is_super_admin()) { ?>
	<div class="style_box withdraw-current-balance fs-icon-money">
		<strong><?php _e('Current Balance:','frozr'); ?>&nbsp;<span class="amount"><?php echo get_woocommerce_currency_symbol() . floatval(get_user_meta(get_current_user_id(),"_restaurant_balance", true)); ?></span></strong>
	</div>
	<?php }
	frozr_withdraws_page_nav();
	$withdraw_status = array('completed', 'trash', 'pending');
	if ( isset( $_GET['withdraw_status']) && in_array( $_GET['withdraw_status'], $withdraw_status ) ) {
	
	$paged = (get_query_var( 'paged' )) ? get_query_var( 'paged' ) : 1;
	$get_curnt_user = (is_super_admin()) ? '' : get_current_user_id();
	$args = array(
		'post_type' => 'frozr_withdraw',
		'post_status' => sanitize_key($_GET['withdraw_status']),
		'posts_per_page' => 10,
		'author' => $get_curnt_user,
		'orderby' => 'post_date',
		'order' => 'DESC',
		'paged' => $paged
	);
	$withdraw_query = new WP_Query( apply_filters( 'frozr_withdraw_listing_query', $args ) );
	
	if ( $withdraw_query->have_posts() ) { ?>

	<table data-role="table" id="withdraws-table" data-mode="reflow" class="ui-responsive">
		<thead>
			<tr>

				<?php do_action('frozr_before_withdraw_table_header'); ?>

				<th data-priority="2"><?php _e('Request ID','frozr'); ?></th>
				<th data-priority="1"><?php _e('Amount','frozr'); ?></th>
				<th data-priority="3"><?php _e('Via','frozr'); ?></th>
				<th data-priority="4"><?php _e('Date','frozr'); ?></th>
				<?php if (is_super_admin()) { ?>
				<th data-priority="5"><?php _e('Restaurant ID','frozr'); ?></th>
				<?php } ?>
				<?php if ($_GET['withdraw_status'] == 'trash') { ?>
				<th data-priority="6"><?php _e('Reject Note','frozr'); ?></th>
				<?php } ?>

				<?php do_action('frozr_after_withdraw_table_header'); ?>

			</tr>
		</thead>
		<tbody>
		<?php
		
		while ($withdraw_query->have_posts()) { $withdraw_query->the_post();
		?>
		<tr>
			<?php do_action('before_withdraw_table_loop'); ?>
			<td class="withdraw_summary"><?php if ( has_post_thumbnail( $post->ID ) && $post->post_status == 'completed' ) { echo '<div data-history="false" data-role="popup" id="'.$post->ID.'_wid_invoive_img" class="withdraw_invoice_img out">'.get_the_post_thumbnail($post->ID, 'large').'</div><a href="#'.$post->ID.'_wid_invoive_img" data-transition="fade" data-rel="popup" data-position-to="window" title="'. __('View Invoice','frozr') . '">'. $post->ID .'</a>'; } else { echo '<span class="wid_in_id">'.$post->ID.'</span>'; } ?>
				<?php if ($post->post_status == 'pending' || is_super_admin()) { echo '<span class="delete_wid"><a req_id="'. $post->ID .'" onclick="return confirm('. __('Are you sure?', 'frozr') . ');" href="#">'. __( 'Delete Request', 'frozr' ) . '</a></span> | <a href="#'.$post->ID.'_wid_pop" data-transition="fade" data-rel="popup" data-position-to="window" class="edit_wid_btn">' . __( 'Edit', 'frozr' ) . '</a><div id="'.$post->ID.'_wid_pop" data-role="popup" data-history="false" class="edit_wid">'; frozr_withdraw_form(false, $post->ID); echo '</div>'; } ?>
			</td>
			<td><?php echo wc_price(get_post_meta($post->ID, 'wid_req_amount', true)); ?></td>
			<td><div data-history="false" data-role="popup" id="<?php echo $post->ID; ?>_wid_details"><?php echo frozr_get_seller_withdraw_email($post->post_author, get_post_meta($post->ID, 'wid_req_via', true)); ?></div><a href="#<?php echo $post->ID; ?>_wid_details" data-transition="fade" data-rel="popup" data-position-to="window" title="<?php _e('View Via Details','frozr'); ?>"><?php echo get_post_meta($post->ID, 'wid_req_via', true); ?></a></td>
			<td><?php echo date_i18n( 'M j, Y g:ia', strtotime( $post->post_date ) ); ?></td>
			<?php if (is_super_admin()) { ?>
			<td><div data-history="false" data-role="popup" id="<?php echo $post->ID; ?>_rest_balance"><?php echo 'Restaurant ID: ' . $post->post_author . ' Restaurant Balance: ' .floatval(get_user_meta($post->post_author,"_restaurant_balance", true)); ?></div><a href="#<?php echo $post->ID; ?>_rest_balance" data-transition="fade" data-rel="popup" data-position-to="window" title="<?php _e('View Restaurant Balance','frozr'); ?>"><?php the_author(); ?></a></td>
			<?php } if ($post->post_status == 'trash') { ?>
			<td class="withdraw_reject_note"><?php if ( get_post_meta($post->ID, 'wid_req_del_note', true) ) { echo get_post_meta($post->ID, 'wid_req_del_note', true); } else { echo 'N/A'; } ?></td>
			<?php } ?>

			<?php do_action('frozr_after_withdraw_table_loop'); ?>

		</tr>
		<?php
		}
		if ( $withdraw_query->max_num_pages > 1 ) {
				echo '<div class="pagination-wrap">';
				$page_links = paginate_links( array(
				'current' => max( 1, get_query_var( 'paged' ) ),
				'total' => $withdraw_query->max_num_pages,
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
			<p><?php _e( 'Sorry, no transactions found!', 'frozr' ); ?></p>
		</div>
	<?php } ?>

	<?php do_action('frozr_after_withdraw_table'); ?>

	<?php } else {
		
		$fle_option = get_option( 'fro_settings' );
		$minimum_withdraw_balance = (! empty( $fle_option['fro_minimum_withdraw_balance']) ) ? $fle_option['fro_minimum_withdraw_balance'] : 50;
		$args = array(
			'post_type' => 'frozr_withdraw',
			'post_status' => 'pending',
			'author' => get_current_user_id(),
		);
		$withdraw_query = new WP_Query( apply_filters( 'frozr_withdraw_listing_query', $args ) );
		
		if ( $withdraw_query->have_posts() ) {
		
			echo '<div class="style_box alert alert-warning fs-icon-warning-sign"><p>' . __('You already have a pending withdraw request. Until it\'s been rejected or approved, you can\'t submit any new request.','frozr') . '</p></div>';
			return;
		}
		if ( $minimum_withdraw_balance > floatval(get_user_meta(get_current_user_id(),"_restaurant_balance", true)) ) {
			
			echo '<div class="style_box alert alert-danger fs-icon-exclamation-sign"><p>'. __( 'You don\'t have sufficient balance for a withdraw request!', 'frozr' ) . '</p></div>';
			return;
		}
		frozr_withdraw_form();
	}
}
/**
 * Get active withdraw methods.
 * 
 * Default is paypal 
 * 
 * @return array
 */
function frozr_withdraw_get_active_methods() {
	$fle_option = get_option( 'fro_settings' );
	$methods_opt = (! empty( $fle_option['fro_withdraw_methods']) ) ? $fle_option['fro_withdraw_methods'] : 'paypal';
	$methods = !is_array( $methods_opt ) ? array( $methods_opt ) : $methods_opt;

    return apply_filters('frozr_get_withdraw_active_methods',$methods);
}
//withdraw form
function frozr_withdraw_form($new = true, $id = 0) {
	global $post;
	$payment_methods = frozr_withdraw_get_active_methods();
	if ($new != true) {
		$ogp = get_post($id);
		$wid_status = $ogp->post_status;
	} else {
		$fle_option = get_option( 'fro_settings' );
		$wid_status = (! empty( $fle_option['fro_withdraw_order_status']) ) ? $fle_option['fro_withdraw_order_status'] : 'pending';
	}
?>
	<form id="<?php echo $id . '_form'; ?>" class="form-horizontal withdraw" role="form" method="post">

		<?php do_action('frozr_before_withdraw_form'); ?>

		<?php if ($ogp->post_status != 'completed') { ?>
		<div class="wid_gen_info <?php if ($wid_status != "pending") { echo "frozr-hide"; } ?>">
		<label for="withdraw_amount"><?php _e( 'Withdraw Amount ', 'frozr' ); ?><?php echo get_woocommerce_currency_symbol(); ?></label>
		<input name="withdraw_amount" required type="number" <?php if (!is_super_admin()) { ?> min="<?php echo esc_attr( get_theme_mod( 'minimum_withdraw_balance', '50' ) ); ?>" max="<?php echo floatval(get_user_meta(get_current_user_id(),"_restaurant_balance", true)); ?>" <?php } ?> class="form-control<?php if ($new == true) { echo ' new_wid_req'; } ?>" id="withdraw_amount" placeholder="<?php echo esc_attr( get_theme_mod( 'minimum_withdraw_balance', '50' ) ); ?>" value="<?php echo get_post_meta($id, 'wid_req_amount', true); ?>"/>

		<label for="withdraw_method"><?php _e( 'Payment Method', 'frozr' ); ?></label>
		<select class="form-control" required name="withdraw_method" id="withdraw_method">
			<?php foreach ($payment_methods as $method) { ?>
				<option value="<?php echo $method; ?>" <?php selected( get_post_meta($id, 'wid_req_via', true), $method); ?>><?php echo $method; ?></option>
			<?php } ?>
		</select>
		</div>
		<?php } if ( is_super_admin() && $new != true ) { ?>
		<div class="withdraw_invoice <?php if ($wid_status != "completed") { echo "frozr-hide"; } ?>">
			<?php
			$wrap_class = ' frozr-hide';
			$instruction_class = '';
			$wid_image_id = 0;
			if ( has_post_thumbnail( $id ) ) {
				$wrap_class = '';
				$instruction_class = ' frozr-hide';
				$wid_image_id = get_post_thumbnail_id( $id );
			} ?>
			<div class="image-wrap<?php echo $wrap_class; ?>">
				<input type="hidden" name="wid_image_id" class="frozr-wid-image-id" value="<?php echo $wid_image_id; ?>">
				<div class="withdraw_img" <?php if ( $wid_image_id ) { $large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id($id), 'thumbnail'); echo 'style="background-image: url( '.$large_image_url[0].');"'; } ?>></div>
				<a class="close frozr-remove-wid-image"><i class="fs-icon-camera"></i><?php _e('Change Invoice Image','frozr'); ?></a>
			</div>
			<div class="instruction-inside<?php echo $instruction_class; ?>">
				<i class="fs-icon-cloud-upload"></i>
				<a href="#" class="frozr-wid-image-btn btn btn-sm"><?php _e( 'Upload Invoice Image', 'frozr' ); ?></a>
			</div>
		</div>
		<?php if ($id != 0 && $ogp->post_status != 'completed') { ?>
		<fieldset class="wid_req_sts" data-role="controlgroup">
			<legend><?php _e('Update Request Status','frozr'); ?></legend>
			<input type="radio" class="pend_wid_req" name="withdraw_status" id="<?php echo $id . '_withdraw_pending'; ?>" value="pending" <?php checked( $wid_status, 'pending' ); ?> >
			<label for="<?php echo $id . '_withdraw_pending'; ?>"><?php _e('Pending','frozr'); ?></label>
			<input type="radio" class="reject_wid_req" name="withdraw_status" id="<?php echo $id . '_withdraw_trash'; ?>" value="trash" <?php checked( $wid_status, 'trash' ); ?> >
			<label for="<?php echo $id . '_withdraw_trash'; ?>"><?php _e('Rejected','frozr'); ?></label>
			<input type="radio" class="com_wid_req" name="withdraw_status" id="<?php echo $id . '_withdraw_completed'; ?>" value="completed" <?php checked( $wid_status, 'completed' ); ?> >
			<label for="<?php echo $id . '_withdraw_completed'; ?>"><?php _e('Paid','frozr'); ?></label>
		</fieldset>
		<div class="wid_reject_div <?php if ($wid_status != "trash") { echo "frozr-hide"; } ?>">
			<label for="wid_reject_note"><?php _e( 'Withdraw Reject Note', 'frozr' ); ?></label>
			<input name="wid_reject_note" required class="form-control" id="wid_reject_note" placeholder="<?php _e('Few lines on why you\'ve rejected the witdraw request','frozr'); ?>" value="<?php echo get_post_meta($id, 'wid_req_del_note', true); ?>"/>
		</div>
		<?php } } ?>
		<?php do_action('frozr_after_withdraw_form'); ?>
		<input type="hidden" name="withdraw_id" value="<?php echo $id; ?>">
		<input type="submit" id="withdraw_submit" name="withdraw_submit" value="<?php if ($new == true) { _e( 'Submit Request', 'frozr' ); } else { _e('Update Request','frozr'); } ?>" >
	</form>
<?php
}
// get total withdraws
function frozr_print_total_withdraws($seller = '') {

	if (is_super_admin()) {
		$user = ($seller == 'all') ? '' : $seller;
	} else {
		$user = get_current_user_id();
	}
	$totals = array();
	$args = array(
		'post_type' => 'frozr_withdraw',
		'post_status' => 'completed',
		'posts_per_page' => -1,
		'author' => $user,
		'fields' => 'ID',
	);
	$withdraws = get_posts( apply_filters( 'frozr_print_total_withdraws', $args ) );
	foreach ($withdraws as $withdraw) {
		$totals[] = floatval(get_post_meta($withdraw, 'wid_req_amount', true));
	}
	
	return array_sum($totals);
}
// redirect if admin
function frozr_wid_redirect_if_admin() {

	if (is_super_admin() && ! isset($_GET['withdraw_status'])) {
		wp_redirect( add_query_arg( array( 'withdraw_status' => 'pending' ), home_url( '/dashboard/withdraw/') ));
	}
}
// delete withdraw request
add_action( 'wp_ajax_frozr_delete_withdraw', 'frozr_delete_withdraw' );
function frozr_delete_withdraw() {
	ob_start();

	check_ajax_referer( 'delete_fro_withdraw', 'security' );
	
	$withdraw_post = get_post( intval($_POST['withdraw_id']) );
	$author = $withdraw_post->post_author;
	// Check permissions again and make sure we have what we need
	if ( !current_user_can( 'frozer' ) && !frozr_is_seller_enabled(get_current_user_id()) || empty( $withdraw_post->ID ) || $author != get_current_user_id() && !is_super_admin() || $withdraw_post->post_status != 'pending' && !is_super_admin() || $withdraw_post->post_status == 'completed' ) {
		die( -1 );
	}
	wp_delete_post( $withdraw_post->ID );
	
	die();
}
add_action( 'wp_ajax_frozr_save_withdraw', 'frozr_save_withdraw' );
// Save withdraw request
function frozr_save_withdraw() {
	global $post;
	
	ob_start();

	check_ajax_referer( 'save_fro_withdraw', 'security' );

	if (empty ($_POST['withdraw_id'])) {
		$args = array(
			'post_type' => 'frozr_withdraw',
			'post_status' => 'pending',
			'author' => get_current_user_id(),
		);
		$withdraw_query = new WP_Query( apply_filters( 'frozr_withdraw_listing_query', $args ) );

		// Check permissions again and make sure we have what we need
		if ( is_super_admin() || !current_user_can( 'frozer' ) && !frozr_is_seller_enabled(get_current_user_id()) || get_theme_mod('fro_minimum_withdraw_balance','50') > floatval(get_user_meta(get_current_user_id(),"_restaurant_balance", true)) || $withdraw_query->have_posts()) {
			die( -1 );
		}
		$withdraw_info = apply_filters('frozr_save_new_withdraw_data',array(
			'post_type' => 'frozr_withdraw',
			'post_status' => 'pending',
		));

		$withdraw_id = wp_insert_post( $withdraw_info );
		$withdraw_post = get_post( $withdraw_id );
		$restaurant = get_user_by( 'id', $withdraw_post->post_author );
		
		if ( isset( $_POST['withdraw_amount'] ) ) {
			update_post_meta( $withdraw_post->ID, 'wid_req_amount', ( $_POST['withdraw_amount'] === '' ? '' : wc_format_decimal( $_POST['withdraw_amount'] ) ) );
		}
		if ( isset( $_POST['withdraw_method'] ) ) {
			update_post_meta( $withdraw_post->ID, 'wid_req_via', ( $_POST['withdraw_method'] === '' ? '' : wc_clean( $_POST['withdraw_method'] ) ) );
		}

	} else {

		$withdraw_post = get_post( intval($_POST['withdraw_id']) );
		$author = $withdraw_post->post_author;
		$restaurant = get_user_by( 'id', $author );
		
		// Check permissions again and make sure we have what we need
		if ( !current_user_can( 'frozer' ) && !frozr_is_seller_enabled(get_current_user_id()) || empty( $withdraw_post->ID ) || $author != get_current_user_id() && !is_super_admin() || $withdraw_post->post_status == 'completed' && !is_super_admin() || $withdraw_post->post_status == 'trash' && !is_super_admin() ) {
			die( -1 );
		}
		if (is_super_admin() && $withdraw_post->post_status == 'completed') {
			/** set images **/
			if (isset($_POST['wid_image_id']) && is_super_admin()) {
				$wid_invoice = absint( $_POST['wid_image_id'] );
			}
			if ( $wid_invoice ) {
				set_post_thumbnail( $withdraw_post->ID, $wid_invoice );
			}
		} else {
		if (isset($_POST['withdraw_status']) && is_super_admin()) {
			$wid_stat = wc_clean($_POST['withdraw_status']);
		} else {
			$wid_stat = 'pending';
		}
		$withdraw_info = apply_filters('frozr_save_withdraw_data',array(
			'ID' => $withdraw_post->ID,
			'post_status' => $wid_stat,
		));

		wp_update_post( $withdraw_info );
		
		/** set images **/
		if (isset($_POST['wid_image_id']) && is_super_admin()) {
			$wid_invoice = absint( $_POST['wid_image_id'] );
		}
		if ( $wid_invoice ) {
			set_post_thumbnail( $withdraw_post->ID, $wid_invoice );
		}

		if ( isset( $_POST['withdraw_amount'] ) ) {
			update_post_meta( $withdraw_post->ID, 'wid_req_amount', ( $_POST['withdraw_amount'] === '' ? '' : wc_format_decimal( $_POST['withdraw_amount'] ) ) );
		}
		if ( isset( $_POST['withdraw_method'] ) ) {
			update_post_meta( $withdraw_post->ID, 'wid_req_via', ( $_POST['withdraw_method'] === '' ? '' : wc_clean( $_POST['withdraw_method'] ) ) );
		}
		if ( isset( $_POST['wid_reject_note']) && is_super_admin() ) {
			update_post_meta( $withdraw_post->ID, 'wid_req_del_note', ( $_POST['wid_reject_note'] === '' ? '' : wc_clean( $_POST['wid_reject_note'] ) ) );
		}
			
		}
	}
	
	do_action('frozr_withdraw_saved');
	
	$msg_args = apply_filters('frozr_save_withdraw_message',array (
		'to' => $restaurant->user_email,
		'num' => $withdraw_post->ID,
		'restaurant_name' => $restaurant->user_login,
		'sts' => wc_clean($_POST['withdraw_status']),
		'amt' => wc_format_decimal( $_POST['withdraw_amount']),
		'via' => wc_clean( $_POST['withdraw_method'] ),
		'note' => wc_clean( $_POST['wid_reject_note'] ),	
	));
	frozr_send_msgs($msg_args, 'withdraw');
	die();
}

function frozr_withdraw_register_methods() {
    $methods = array(
        'paypal' => array(
            'title' =>  __( 'PayPal', 'frozr' ),
            'callback' => 'frozr_withdraw_method_paypal'
        ),
        'bank' => array(
            'title' => __( 'Bank Transfer', 'frozr' ),
            'callback' => 'frozr_withdraw_method_bank'
        ),
        'skrill' => array(
            'title' => __( 'Skrill', 'frozr' ),
            'callback' => 'frozr_withdraw_method_skrill'
        ),
    );

    return apply_filters( 'frozr_withdraw_register_methods', $methods );
}
/**
 * Get registered withdraw methods suitable for Settings Api
 * 
 * @return array
 */
function frozr_withdraw_get_methods() {
    $methods = array();
    $registered = frozr_withdraw_register_methods();

    foreach ($registered as $key => $value) {
        $methods[$key] = $value['title'];
    }

    return $methods;
}
/**
 * Get a single withdraw method based on key
 * 
 * @param string $method_key
 * @return boolean|array
 */
function frozr_withdraw_get_method( $method_key ) {
    $methods = frozr_withdraw_register_methods();

    if ( isset( $methods[$method_key] ) ) {
        return $methods[$method_key];
    }

    return false;
}
/**
 * Get title from a withdraw method
 * 
 * @param string $method_key
 * @return string
 */
function frozr_withdraw_get_method_title( $method_key ) {
    $registered = frozr_withdraw_register_methods();

    if ( isset( $registered[$method_key]) ) {
        return $registered[$method_key]['title'];
    }

    return '';
}
/**
 * Callback for PayPal in restaurant settings
 * 
 * @global WP_User $current_user
 * @param array $store_settings
 */
function frozr_withdraw_method_paypal( $store_settings ) {
    global $current_user;

    $email = isset( $store_settings['payment']['paypal']['email'] ) ? esc_attr( $store_settings['payment']['paypal']['email'] ) : $current_user->user_email ;
    ?>
	<span class="input-group-addon"><?php _e( 'E-mail', 'frozr' ); ?></span>
	<input value="<?php echo $email; ?>" name="settings[paypal][email]" class="form-control" placeholder="example@domain.com" type="text">
	<?php do_action('frozr_after_withdraw_paypal_method_input', $store_settings); ?>
    <?php
}
/**
 * Callback for Skrill in restaurant settings
 * 
 * @global WP_User $current_user
 * @param array $store_settings
 */
function frozr_withdraw_method_skrill( $store_settings ) {
    global $current_user;

    $email = isset( $store_settings['payment']['skrill']['email'] ) ? esc_attr( $store_settings['payment']['skrill']['email'] ) : $current_user->user_email ;
    ?>
	<span class="input-group-addon"><?php _e( 'E-mail', 'frozr' ); ?></span>
	<input value="<?php echo $email; ?>" name="settings[skrill][email]" class="form-control" placeholder="you@domain.com" type="text">

	<?php do_action('frozr_after_withdraw_skrill_method_input', $store_settings);
}
/**
 * Callback for Bank in restaurant settings
 * 
 * @global WP_User $current_user
 * @param array $store_settings
 */
function frozr_withdraw_method_bank( $store_settings ) {

	$account_name = isset( $store_settings['payment']['bank']['ac_name'] ) ? esc_attr( $store_settings['payment']['bank']['ac_name'] ) : '';
	$account_number = isset( $store_settings['payment']['bank']['ac_number'] ) ? esc_attr( $store_settings['payment']['bank']['ac_number'] ) : '';
	$bank_name = isset( $store_settings['payment']['bank']['bank_name'] ) ? esc_attr( $store_settings['payment']['bank']['bank_name'] ) : '';
	$bank_addr = isset( $store_settings['payment']['bank']['bank_addr'] ) ? esc_textarea( $store_settings['payment']['bank']['bank_addr'] ) : '';
	$swift_code = isset( $store_settings['payment']['bank']['swift'] ) ? esc_attr( $store_settings['payment']['bank']['swift'] ) : '';
    ?>
	<input name="settings[bank][ac_name]" value="<?php echo $account_name; ?>" class="form-control" placeholder="<?php esc_attr_e( 'Your bank account name', 'frozr' ); ?>" type="text">
	<input name="settings[bank][ac_number]" value="<?php echo $account_number; ?>" class="form-control" placeholder="<?php esc_attr_e( 'Your bank account number', 'frozr' ); ?>" type="text">
	<input name="settings[bank][bank_name]" value="<?php echo $bank_name; ?>" class="form-control" placeholder="<?php _e( 'Name of bank', 'frozr' ) ?>" type="text">
	<textarea name="settings[bank][bank_addr]" class="form-control" placeholder="<?php esc_attr_e( 'Address of your bank', 'frozr' ) ?>"><?php echo $bank_addr; ?></textarea>
	<input value="<?php echo $swift_code; ?>" name="settings[bank][swift]" class="form-control" placeholder="<?php esc_attr_e( 'Swift code', 'frozr' ); ?>" type="text">

	<?php do_action('frozr_after_withdraw_bank_method_input', $store_settings);
}
/**
 * Get withdraw email method based on seller ID and type
 *
 * @param int $seller_id
 * @param string $type
 * @return string
 */
function frozr_get_seller_withdraw_email( $seller_id, $type = 'paypal' ) {
    $info = frozr_get_store_info( $seller_id );

    return apply_filters('frozr_get_seller_withdraw_email',$info['payment'][$type]['email'], $seller_id, $type);
}
/**
 * Get seller bank details
 *
 * @param int $seller_id
 * @return string
 */
function frozr_get_seller_bank_details( $seller_id ) {
    $info = frozr_get_store_info( $seller_id );
    $payment = $info['payment']['bank'];
    $details = array();

    if ( isset( $payment['ac_name'] ) ) {
        $details[] = sprintf( __( 'Account Name: %s', 'frozr' ), $payment['ac_name'] );
    }
    if ( isset( $payment['ac_number'] ) ) {
        $details[] = sprintf( __( 'Account Number: %s', 'frozr' ), $payment['ac_number'] );
    }
    if ( isset( $payment['bank_name'] ) ) {
        $details[] = sprintf( __( 'Bank Name: %s', 'frozr' ), $payment['bank_name'] );
    }
    if ( isset( $payment['bank_addr'] ) ) {
        $details[] = sprintf( __( 'Address: %s', 'frozr' ), $payment['bank_addr'] );
    }
    if ( isset( $payment['swift'] ) ) {
        $details[] = sprintf( __( 'SWIFT: %s', 'frozr' ), $payment['swift'] );
    }

    return nl2br( implode( "\n", $details ) );
}