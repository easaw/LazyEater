<?php
/**
 * All Registration Functions
 *
 * @package FrozrCoreLibrary
 * @subpackage Frozrmarketlibrary
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
function frozr_seller_reg_form_fields() {
    $role = isset( $_POST['role'] ) ? $_POST['role'] : 'customer';
    $role_style = ( $role == 'customer' ) ? ' style="display:none"' : '';
	$fle_option = get_option( 'fro_settings' );
	$new_seller_status = (! empty( $fle_option['fro_new_seller_status']) ) ? $fle_option['fro_new_seller_status'] : false;
	$seller_tos = (! empty( $fle_option['fro_tos_sellers']) ) ? $fle_option['fro_tos_sellers'] : 0;
	$customers_tos = (! empty( $fle_option['fro_tos_customers']) ) ? $fle_option['fro_tos_customers'] : 0;
    ?>
	
	<?php do_action('frozr_before_register_form'); ?>
	
	<div class="show_if_seller"<?php echo $role_style; ?>>
	
	<?php if ($new_seller_status == false) {echo '<div class="reg_note_box style_box fs-icon-warning"><p>' . __('Please note that your selling privileges will be activated after an admin approval.','frozr') . '</p></div>';}?>
		
		<?php do_action('frozr_before_seller_register_form'); ?>
		
		<div class="split-row form-row-wide">
			<p class="form-row">
				<label for="first-name"><?php _e( 'First Name', 'frozr' ); ?> <span class="required">*</span></label>
				<input type="text" class="input-text form-control" name="fname" id="first-name" value="<?php if ( ! empty( $_POST['fname'] ) ) echo esc_attr($_POST['fname']); ?>" required disabled />
			</p>

			<p class="form-row">
				<label for="last-name"><?php _e( 'Last Name', 'frozr' ); ?> <span class="required">*</span></label>
				<input type="text" class="input-text form-control" name="lname" id="last-name" value="<?php if ( ! empty( $_POST['lname'] ) ) echo esc_attr($_POST['lname']); ?>" required disabled />
			</p>
        </div>

		<p class="form-row form-row-wide">
			<label for="company-name"><?php _e( 'Shop Name', 'frozr' ); ?> <span class="required">*</span></label>
			<input type="text" class="input-text form-control" name="shopname" id="company-name" value="<?php if ( ! empty( $_POST['shopname'] ) ) echo esc_attr($_POST['shopname']); ?>" required disabled />
		</p>

		<p class="form-row form-row-wide">
			<label for="seller-url" class="pull-left"><?php _e( 'Shop URL', 'frozr' ); ?> <span class="required">*</span></label>
			<strong id="url-alart-mgs" class="pull-right"></strong>
			<input type="text" class="input-text form-control" name="shopurl" id="seller-url" value="<?php if ( ! empty( $_POST['shopurl'] ) ) echo esc_attr($_POST['shopurl']); ?>" required disabled />
			<small><?php echo home_url('/restaurant/'); ?><strong id="url-alart"></strong></small>
		</p>

		<p class="form-row form-row-wide">
			<label for="shop-phone"><?php _e( 'Phone', 'frozr' ); ?><span class="required">*</span></label>
			<input type="text" class="input-text form-control" name="lephone" id="shop-phone" value="<?php if ( ! empty( $_POST['lephone'] ) ) echo esc_attr($_POST['lephone']); ?>" required disabled />
		</p>

		<?php if ($seller_tos) { ?>
		<div data-history="false" data-role="popup" id="pop_fro_seller_tos" class="pop_tos">
			<?php echo $seller_tos; ?> 
		</div>

		<p class="form-row form-row-wide">
			<label for="fro_sel_tos"><?php _e('I Accept','frozr'); ?>&nbsp;<a class="fro_sel_tos_btn" data-transition="fade" data-rel="popup" data-position-to="window" href="#pop_fro_seller_tos" title="<?php _e('View Terms of Service','frozr'); ?>"><?php _e('Terms of Service.','frozr'); ?></a></label>
			<input type="checkbox" id="fro_sel_tos" required="required" name="fro_sel_tos" value="0">
		</p>
		<?php } ?>

		<?php do_action('frozr_after_seller_register_form'); ?>
    </div>

	<?php if ($customers_tos) { ?>
	<div class="frozr_tos_customers fro_cus_tos_wrapper">
		<div data-history="false" data-role="popup" id="pop_fro_customer_tos" class="pop_tos">
			<?php echo $customers_tos; ?> 
		</div>
		
		<label for="fro_cus_tos"><?php _e('I Accept','frozr'); ?>&nbsp;<a class="fro_cus_tos_btn" data-transition="fade" data-rel="popup" data-position-to="window" href="#pop_fro_customer_tos" title="<?php _e('View Terms of Service','frozr'); ?>"><?php _e('Terms of Service.','frozr'); ?></a></label>
		<input type="checkbox" id="fro_cus_tos" required="required" name="fro_cus_tos" value="0">

	</div>
	<?php } ?>

    <p class="form-row user-role">
        <label class="radio">
            <input type="radio" name="role" value="customer"<?php checked( $role, 'customer' ); ?>>
            <?php _e( "I'm a customer", 'frozr' ); ?>
        </label>

        <label class="radio">
            <input type="radio" name="role" value="seller"<?php checked( $role, 'seller' ); ?>>
            <?php _e( "I Have a Restaurant", 'frozr' ); ?>
        </label>
    </p>

	<?php do_action('frozr_after_register_form');
}

add_action( 'woocommerce_register_form', 'frozr_seller_reg_form_fields' );
/**
 * Redirect users from standard WordPress register page to woocommerce
 * my account page
 *
 * @global string $action
 */
add_filter( 'woocommerce_registration_redirect', 'frozr_redirect_to_register' );
function frozr_redirect_to_register($redirect){
	$fle_option = get_option( 'fro_settings' );
	$new_seller_status = (! empty( $fle_option['fro_new_seller_status']) ) ? $fle_option['fro_new_seller_status'] : false;

    if ( $new_seller_status == false ) {
        $redirect =  get_permalink( wc_get_page_id( 'myaccount' ));
    } else {
        $redirect =  home_url( '/dashboard/settings/' );	
	}
	return $redirect;
}
/**
 * Enable/Disable selling capability by default once a seller is registered
 *
 * @param int $user_id
 */
function frozr_admin_user_register( $user_id ) {
    $user = new WP_User( $user_id );
    $role = reset( $user->roles );
	$restaurant = get_user_by( 'id', $user_id );
	$fle_option = get_option( 'fro_settings' );
	$new_seller_status = (! empty( $fle_option['fro_new_seller_status']) ) ? $fle_option['fro_new_seller_status'] : false;

	$msg_args = apply_filters('frozr_user_register_msg',array (
		'id' => $user_id,
		'uemail' => sanitize_email($restaurant->user_email),
		'fname' => sanitize_text_field($restaurant->first_name),
		'lname' => sanitize_text_field($restaurant->last_name),
		'shopname' => sanitize_text_field($_POST['shopname']),
		'shopurl' => frozr_get_store_url($user_id),
		'shopphone' => intval($_POST['lephone']),
	));

   if ( $role == 'seller' )   {
		if ($new_seller_status == false ) {
			update_user_meta( $user_id, 'frozr_enable_selling', 'no' );
			$msg_args['type'] = 'new_seller';
			frozr_send_msgs($msg_args, 'registration');
			$msg_args['to'] = sanitize_email($restaurant->user_email);
			$msg_args['type'] = 'to_new_seller';
			frozr_send_msgs($msg_args, 'registration');
		} else {
			update_user_meta( $user_id, 'frozr_enable_selling', 'yes' );
			$msg_args['type'] = 'new_seller_auto';
			frozr_send_msgs($msg_args, 'registration');
			$msg_args['to'] = sanitize_email($restaurant->user_email);
			$msg_args['type'] = 'to_new_seller_auto';
			frozr_send_msgs($msg_args, 'registration');
		}
	$frozr_settings = apply_filters('frozr_user_register_fields',array(
		'store_name' => sanitize_text_field($_POST['shopname']),
		'social' => array(),
		'payment' => array(),
		'phone' => intval($_POST['lephone']),
		'show_email' => 'no',
		'shipping_fee' => '',
		'shipping_pro_adtl_cost' => '',
		'processing_time' => '',
		'resturant_type' => '',
		'banner' => 0,
	));
	
	update_user_meta( $user_id, 'frozr_profile_settings', $frozr_settings );

    } else {
		$msg_args['type'] = 'new_customer';
		frozr_send_msgs($msg_args, 'registration');
	}
}
add_action( 'user_register', 'frozr_admin_user_register' );
/**
 * Inject first and last name to WooCommerce for new seller registration
 *
 * @param array $data
 * @return array
 */
function frozr_new_customer_data( $data ) {
    $allowed_roles = array( 'customer', 'seller' );
    $role = ( isset( $_POST['role'] ) && in_array( $_POST['role'], $allowed_roles ) ) ? $_POST['role'] : 'customer';

    $data['role'] = $role;

    if ( $role == 'seller' ) {
        $data['first_name'] = sanitize_text_field( $_POST['fname'] );
        $data['last_name'] = sanitize_text_field( $_POST['lname'] );
        $data['user_nicename'] = sanitize_text_field( $_POST['shopurl'] );
	}

    return $data;
}

add_filter( 'woocommerce_new_customer_data', 'frozr_new_customer_data');