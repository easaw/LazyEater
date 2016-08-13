<?php
/**
 * Frozr maket admin functions
 *
 * @package FrozrCoreLibrary
 * @subpackage Frozrmarketlibrary
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly



/**
 * Remove child orders from WC reports
 *
 * @param array $query
 * @return array

/**
 * Change the columns shown in admin.
 * 
 * @param array $existing_columns
 * @return array
 */
function frozr_admin_shop_order_edit_columns( $existing_columns ) {
    $columns = array();

    $columns['cb']               = '<input type="checkbox" />';
    $columns['order_status']     = '<span class="status_head tips" data-tip="' . esc_attr__( 'Status', 'frozr' ) . '">' . esc_attr__( 'Status', 'frozr' ) . '</span>';
    $columns['order_title']      = __( 'Order', 'frozr' );
    $columns['order_items']      = __( 'Purchased', 'frozr' );
    $columns['shipping_address'] = __( 'Billing Address', 'frozr' );

    $columns['customer_message'] = '<span class="notes_head tips" data-tip="' . esc_attr__( 'Customer Message', 'frozr' ) . '">' . esc_attr__( 'Customer Message', 'frozr' ) . '</span>';
    $columns['order_notes']      = '<span class="order-notes_head tips" data-tip="' . esc_attr__( 'Order Notes', 'frozr' ) . '">' . esc_attr__( 'Order Notes', 'frozr' ) . '</span>';
    $columns['order_date']       = __( 'Date', 'frozr' );
    $columns['order_total']      = __( 'Total', 'frozr' );
    $columns['order_actions']    = __( 'Actions', 'frozr' );
    $columns['seller']        = __( 'Seller', 'frozr' );
    $columns['suborder']        = __( 'Sub Order', 'frozr' );

    return apply_filters('frozr_admin_shop_order_edit_columns',$columns);
}

add_filter( 'manage_edit-shop_order_columns', 'frozr_admin_shop_order_edit_columns', 11 );

/**
 * Adds custom column on frozr admin shop order table
 *
 * @global type $post
 * @global type $woocommerce
 * @global WC_Order $the_order
 * @param type $col
 */
function frozr_shop_order_custom_columns( $col ) {
    global $post, $woocommerce, $the_order;

    if ( empty( $the_order ) || $the_order->id != $post->ID ) {
        $the_order = new WC_Order( $post->ID );
    }

    switch ($col) {
        case 'order_title':
            if ($post->post_parent !== 0) {
                echo '<strong>';
                echo __( 'Sub Order of', 'frozr' );
                printf( ' <a href="%s">#%s</a>', admin_url( 'post.php?action=edit&post=' . $post->post_parent ), $post->post_parent );
                echo '</strong>';
            }
            break;

        case 'suborder':
            $has_sub = get_post_meta( $post->ID, 'has_sub_order', true );

            if ( $has_sub == '1' ) {
                printf( '<a href="#" class="show-sub-orders" data-class="parent-%1$d" data-show="%2$s" data-hide="%3$s">%2$s</a>', $post->ID, __( 'Show Sub-Orders', 'frozr' ), __( 'Hide Sub-Orders', 'frozr' ));
            }
            break;

        case 'seller':
            $has_sub = get_post_meta( $post->ID, 'has_sub_order', true );

            if ( $has_sub != '1' ) {
                $seller = get_user_by( 'id', $post->post_author );
                printf( '<a href="%s">%s</a>', admin_url( 'edit.php?post_type=shop_order&author=' . $seller->ID ), $seller->display_name );
            }

            break;
    }
}

add_action( 'manage_shop_order_posts_custom_column', 'frozr_shop_order_custom_columns', 11 );

/**
 * Adds css classes on admin shop order table
 *
 * @global WP_Post $post
 * @param array $classes
 * @param int $post_id
 * @return array
 */
function frozr_admin_shop_order_row_classes( $classes, $post_id ) {
    global $post;

    if ( $post->post_type == 'shop_order' && $post->post_parent != 0 ) {
        $classes[] = 'sub-order parent-' . $post->post_parent;
    }

    return $classes;
}

add_filter( 'post_class', 'frozr_admin_shop_order_row_classes', 10, 2);

/**
 * Show/hide sub order css/js
 *
 * @return void
 */
function frozr_admin_shop_order_scripts() {
    ?>
    <script type="text/javascript">
    jQuery(function($) {
        $('tr.sub-order').hide();

        $('a.show-sub-orders').on('click', function(e) {
            e.preventDefault();

            var $self = $(this),
                el = $('tr.' + $self.data('class') );

            if ( el.is(':hidden') ) {
                el.show();
                $self.text( $self.data('hide') );
            } else {
                el.hide();
                $self.text( $self.data('show') );
            }
        });

        $('button.toggle-sub-orders').on('click', function(e) {
            e.preventDefault();

            $('tr.sub-order').toggle();
        });
    });
    </script>

    <style type="text/css">
        tr.sub-order {
            background: #ECFFF2;
        }
    </style>
    <?php
}

add_action( 'admin_footer-edit.php', 'frozr_admin_shop_order_scripts' );

/**
 * Display a help tip.
 *
 * @param  string $tip	Help tip text
 * @param  bool   $allow_html Allow sanitized HTML if true or escape
 * @return string
 */
function frozr_help_tip( $tip, $allow_html = false ) {
	if ( $allow_html ) {
		$tip = wc_sanitize_tooltip( $tip );
	} else {
		$tip = esc_attr( $tip );
	}

	return '<span class="frozr_tooltip_wrapper"><span class="frozr_help_tip">' . $tip . '</span></span>';
}

/**
 * Minus balance if order changed from completed to any other status
 *
 * @param int $order_id
 * @param string $old_status
 * @param string $new_status
 */
function frozr_on_order_refund( $order_id, $old_status, $new_status ) {
	
	$order_post = get_post( $order_id );
	$order = new WC_Order( $order_id );
	$restaurant = get_user_by( 'id', (int) $order_post->post_author );
	$seller_current_balance = floatval(get_user_meta($order_post->post_author,"_restaurant_balance", true));

	if ($old_status == 'completed' && get_post_meta($order_id, '_payment_method', true) != 'cod') {
		$seller_profit = floatval(get_post_meta( $order_id, 'frozr_order_seller_profit', true ));		

		$seller_new_balance = $seller_current_balance - $seller_profit;

		update_user_meta($order_post->post_author, "_restaurant_balance", $seller_new_balance);

		$msg_args = apply_filters('frozr_minus_user_balance_message_args',array (
			'to' => sanitize_email($restaurant->user_email),
			'restaurant_name' => sanitize_text_field($restaurant->first_name),
			'order_id' => $order_id,
			'order_amount' => frozr_get_seller_total_order($order),
			'amount' => wc_price($seller_profit),
			'new_sts' => $new_status,
			'current_balance' => wc_price(get_user_meta($order_post->post_author,"_restaurant_balance", true))
		));

		frozr_send_msgs($msg_args, 'minus_seller_balance');
	}
}

add_action( 'woocommerce_order_status_changed', 'frozr_on_order_refund', 10, 3 );

/**
 * Show a toggle button to toggle all the sub orders
 *
 * @global WP_Query $wp_query
 */
function frozr_admin_shop_order_toggle_sub_orders() {
    global $wp_query;

    if ( isset( $wp_query->query['post_type'] ) && $wp_query->query['post_type'] == 'shop_order' ) {
        echo '<button class="toggle-sub-orders button">' . __( 'Toggle Sub-orders', 'frozr' ) . '</button>';
    }
}

add_action( 'restrict_manage_posts', 'frozr_admin_shop_order_toggle_sub_orders');

//Add fields to user profile
add_action( 'show_user_profile','frozr_add_meta_fields', 20 );
add_action( 'edit_user_profile','frozr_add_meta_fields', 20 );
function frozr_add_meta_fields( $user ) {

	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		return;
	}

	if ( !user_can( $user, 'frozer' ) ) {
		return;
	}

	$selling = get_user_meta( $user->ID, 'frozr_enable_selling', true ); ?>
	
	<h3><?php _e( 'Lazy Eater Options', 'frozr' ); ?></h3>

		<table class="form-table">
			<tbody>
				<tr>
					<th><?php _e( 'Selling', 'frozr' ); ?></th>
					<td>
						<label for="frozr_enable_selling">
							<input type="hidden" name="frozr_enable_selling" value="no">
							<input name="frozr_enable_selling" type="checkbox" id="frozr_enable_selling" value="yes" <?php checked( $selling, 'yes' ); ?> />
							<?php _e( 'Enable Selling', 'frozr' ); ?>
						</label>

						<p class="description"><?php _e( 'Enable or disable product selling capability', 'frozr' ) ?></p>
					</td>
				</tr>
			</tbody>
		</table>
<?php
}
/**
* Save user data
*
* @param int $user_id
* @return void
*/
add_action( 'personal_options_update','frozr_save_user_meta_fields' );
add_action( 'edit_user_profile_update','frozr_save_user_meta_fields' );
function frozr_save_user_meta_fields( $user_id ) {
	
	if ( ! is_super_admin() ) {
	return;
	}
	$restaurant = get_user_by( 'id', $user_id );
	$msg_args = apply_filters('frozr_save_user_meta_fields_msg_args',array (
		'id' => $user_id,
		'to' => sanitize_email($restaurant->user_email),
		'shopname' => frozr_get_store_url($user_id),
	));
	
	$selling = esc_attr( $_POST['frozr_enable_selling'] );

	if ($selling != get_user_meta($user_id, 'frozr_enable_selling', true)) {
		$msg_args['type'] = 'privileges';
		frozr_send_msgs($msg_args, 'registration');
	}

	update_user_meta( $user_id, 'frozr_enable_selling', $selling );
	
}
/**
 * Adds additional columns to admin user table
 *
 * @param array $columns
 * @return array
 */
function frozr_admin_product_columns( $columns ) {
    $columns['author'] = __( 'Author', 'frozr' );

    return $columns;
}

add_filter( 'manage_edit-product_columns', 'frozr_admin_product_columns' );