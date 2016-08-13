<?php
/**
 * Lazy Eater Plugin Options for Frozr Theme
 * 
 */

//Main Action Hooks
add_action( 'admin_menu', 'frozr_add_admin_menu' );
add_action( 'admin_init', 'frozr_settings_init' );

function frozr_add_admin_menu() { 
	
	add_menu_page( 'LazyEater Settings', 'LazyEater', 'manage_options', 'lazyeater', 'frozr_options_page', plugins_url( 'assets/imgs/admin_icon.png', lAZY_EATER_FILE ), 11 );
	add_submenu_page( 'lazyeater', 'Delivery Locations', 'Delivery Locations', 'manage_options', 'edit-tags.php?taxonomy=location');
	add_submenu_page( 'lazyeater', 'Restaurants Addresses', 'Restaurants Addresses', 'manage_options', 'edit-tags.php?taxonomy=restaurant_addresses');
	add_submenu_page( 'lazyeater', 'Restaurants Cuisine', 'Restaurants Cuisine', 'manage_options', 'edit-tags.php?taxonomy=cuisine');
	
	do_action('frozr_after_lazyeater_options_menu');
}

function frozr_settings_init() {

	register_setting( 'lazyeater_page_general', 'frozr_settings' );
	register_setting( 'lazyeater_page_withdraw', 'frozr_settings' );
	register_setting( 'lazyeater_page_fees', 'frozr_settings' );
	register_setting( 'lazyeater_page_tos', 'frozr_settings' );

	do_action('frozr_before_lazyeater_options');
	
	// Sections
	add_settings_section(
		'frozr_general_options_section',
		__( 'General Settings', 'frozr' ),
		'',
		'lazyeater_page_general');

	add_settings_section(
		'frozr_withdraw_options_section',
		__( 'Withdraw Settings', 'frozr' ),
		'',
		'lazyeater_page_withdraw');

	add_settings_section(
		'frozr_fees_options_section',
		__( 'Fees/Commission Settings', 'frozr' ),
		'',
		'lazyeater_page_fees');

	add_settings_section(
		'frozr_tos_options_section',
		__( 'ToS Settings', 'frozr' ),
		'',
		'lazyeater_page_tos');
	
	// General Settings
	add_settings_field( 
		'frozr_lazy_auto_updates',
		__( 'Enable Auto updates for LazyEater?', 'frozr' ), 
		'frozr_lazy_auto_updates_render',
		'lazyeater_page_general',
		'frozr_general_options_section' );

	add_settings_field( 
		'frozr_allow_user_admin_access',
		__( 'Allow Sellers to Access Admin Panel?', 'frozr' ),
		'frozr_allow_user_admin_access_render',
		'lazyeater_page_general',
		'frozr_general_options_section' );

	add_settings_field( 
		'frozr_new_seller_status',
		__( 'Auto Enable Selling on Restaurant Registration?', 'frozr' ),
		'frozr_new_seller_status_render',
		'lazyeater_page_general',
		'frozr_general_options_section' );
		
	add_settings_field( 
		'frozr_reco_sellers',
		__( 'Recommended Restaurants.', 'frozr' ),
		'frozr_reco_sellers_render',
		'lazyeater_page_general',
		'frozr_general_options_section' );


	// Withdraws
	add_settings_field( 
		'frozr_minimum_withdraw_balance',
		__( 'The Minimum user balance to make a withdraw.', 'frozr' ), 
		'frozr_minimum_withdraw_balance_render',
		'lazyeater_page_withdraw',
		'frozr_withdraw_options_section' );

	add_settings_field( 
		'frozr_withdraw_methods',
		__( 'Withdraw Methods.', 'frozr' ),
		'frozr_withdraw_methods_render',
		'lazyeater_page_withdraw',
		'frozr_withdraw_options_section' );

	add_settings_field( 
		'frozr_withdraw_order_status',
		__( 'Withdraw Request Status.', 'frozr' ),
		'frozr_withdraw_order_status_render',
		'lazyeater_page_withdraw',
		'frozr_withdraw_options_section' );

	// Sales Fees/Commission Settings
	add_settings_field( 
		'frozr_lazy_fees',
		'',
		'frozr_lazy_fees_render',
		'lazyeater_page_fees',
		'frozr_fees_options_section' );
	
	// Terms of Service
	add_settings_field( 
		'frozr_tos_sellers',
		__( 'Terms of Service for Sellers', 'frozr' ),
		'frozr_tos_sellers_render',
		'lazyeater_page_tos',
		'frozr_tos_options_section' );

	add_settings_field( 
		'frozr_tos_customers',
		__( 'Terms of Service for Customers', 'frozr' ),
		'frozr_tos_customers_render',
		'lazyeater_page_tos',
		'frozr_tos_options_section' );

	do_action('frozr_after_lazyeater_options');
}
function frozr_lazy_fees_render() {
	$option = get_option( 'frozr_settings' );
	$fees_options = (! empty( $option['frozr_lazy_fees']) ) ? $option['frozr_lazy_fees'] : '';
	$fees_count = 0;
	$hide_empty_notice = 'style="display:none;"';
	$amount_effected_options = apply_filters('frozr_fee_amount_effected_options', array(
		'full' => __('Order total and delivery','frozr'),
		'order_total' => __('Only on order total','frozr'),
		'delivery' => __('Only on order delivery','frozr'),
	));
	?>
	<div class="frozr_fee_settings">
	<?php if ($fees_options) { ?>
	<table class="frozr_sellers_fee_table">
		<thead>
			<tr>
				<th><?php _e( 'Fee&nbsp;Name', 'frozr' ); ?></th>
				<th><?php _e( 'Fee Rate', 'frozr' ); ?></th>
				<th><?php _e( 'Fee Description', 'frozr' ); ?></th>
				<th><?php _e( 'Applied on', 'frozr' ); ?></th>
				<th><?php _e( 'Action', 'frozr' ); ?></th>
			</tr>
		</thead>
		<tbody id="rates">
			<?php foreach ($fees_options as $fee) {
				
				echo '<tr class="frozr_fee_rule">';
				echo '<td>'.$fee['fee_title'].'</td>';
				echo '<td>%'.$fee['rate'].'</td>';
				echo '<td><span class="frozr_fee_description">'.$fee['description'].'</span></td>';
				echo '<td>'.$amount_effected_options[$fee['amount_effect']].'</td>';
				echo '<td><a data-rule="fee_rule_'.$fees_count.'" href="#" class="frozr_edit_rule" title="'.__('Edit','frozr').'">'.__('Edit','frozr').'</a>&nbsp;<a data-rule="fee_rule_'.$fees_count.'" href="#" class="frozr_delete_rule" title="'.__('Delete','frozr').'">'.__('Delete','frozr').'</a></td>';
				echo '</tr>';
				$fees_count++;
			} ?>
		</tbody>
	</table>
	<?php frozr_get_fees_rules_body($fees_options); ?>
	<?php } else {
		$hide_empty_notice = '';
	} ?>
	<h3 class="frozr_fee_empty_notice" <?php echo $hide_empty_notice; ?>><?php esc_html_e( 'You do not charge sellers any fees.', 'frozr' ); ?></h3>
	<a href="#" class="frozr_add_new_rule button-primary"><?php _e('Add New Rule','frozr'); ?></a>
	</div>
	<?php
}
function frozr_get_fees_rules_body($fees_opts = array()) {
	
	// General Rule Args
	$args = array();

	$default_args = apply_filters('frozr_default_seller_fee_row', array( "0" => array(
		'customers_effected' => '',
		'customers' => '',
		'sellers_effected' => '',
		'sellers' => '',
		'order_amount' => '',
		'amount_effect' => '',
		'rate' => '',
		'fee_title' => '',
		'description' => '',
	)));
	$rows_title = apply_filters('frozr_default_fee_rows_titles', array(
		'customers_effected' => __( 'Apply this fee on', 'frozr' ) . frozr_help_tip( __( 'Identify on which customer orders this rule should be applied, Example: If selected customers, This rule will only be applied on orders made by those customers.', 'frozr' ) ),
		'customers' => __( 'Select Customers', 'frozr' ),
		'sellers_effected' => __( 'Apply this fee on', 'frozr' ) . frozr_help_tip( __( 'Identify on which restaurant orders this rule should be applied, Example: If selected restaurants, This rule will only be applied on orders made for those restaurant.', 'frozr' ) ),
		'sellers' => __( 'Select Restaurants', 'frozr' ),
		'order_amount' => __( 'Order Sub-Total', 'frozr' ) . ' ' . get_woocommerce_currency_symbol() . frozr_help_tip( __( 'A float value which if the order sub-total amount exceeds, this rule is applied.', 'frozr' ) ),
		'amount_effect' => __( 'Apply this rule on', 'frozr' ) . frozr_help_tip( __( 'Wither to apply this rule on order items total or on order total delivery or on both.', 'frozr' ) ),
		'rate' => __( 'Rate&nbsp;%', 'frozr' ) . frozr_help_tip( __( 'This rule fee rate that will be used on orders. Enter a rate (percentage) to 4 decimal places.', 'frozr' ) ),
		'fee_title' => __( 'Fee&nbsp;Name', 'frozr' ) . frozr_help_tip( __( 'Enter a name for this fee rate.', 'frozr' ) ),
		'description' => __( 'Fee Description', 'frozr' ) . frozr_help_tip( __( 'Explain why you are applying this rule. Example: Website Fee', 'frozr' ) ),
	));
	
	$customers_effected_options = apply_filters('frozr_fee_customers_effected_options', array(
		'all' => __('All Customers','frozr'),
		'all_but' => __('All Customers, Expect...','frozr'),
		'specific' => __('Select Customers','frozr'),
	));
	$sellers_effected_options = apply_filters('frozr_fee_sellers_effected_options', array(
		'all' => __('All Restaurant','frozr'),
		'all_but' => __('All Restaurants, Expect...','frozr'),
		'specific' => __('Select Restaurants','frozr'),
	));
	$amount_effected_options = apply_filters('frozr_fee_amount_effected_options', array(
		'full' => __('Order Total and Delivery','frozr'),
		'order_total' => __('Only on Order Total','frozr'),
		'delivery' => __('Only on Order Delivery','frozr'),
	));
	
	if ($fees_opts) {
		foreach ($fees_opts as $rule) {
			$args[] = apply_filters('frozr_saved_seller_fee_rows', array(
			'customers_effected' => $rule['customers_effected'],
			'customers' => $rule['customers'],
			'sellers_effected' => $rule['sellers_effected'],
			'sellers' => $rule['sellers'],
			'order_amount' => $rule['order_amount'],
			'amount_effect' => $rule['amount_effect'],
			'rate' => $rule['rate'],
			'fee_title' => $rule['fee_title'],
			'description' => $rule['description'],
			));
		}
	}
	
	$args_vals = ($fees_opts) ? $args : $default_args;
	$array_count = ($fees_opts) ? 0 : 'new';
	$required = $data_type = $min = $step = '';
	
	// sellers option cannot be empty if seller_effected is specific
	foreach ($args_vals as $rule) {
		echo "<div id=\"fee_rule_$array_count\" class=\"frozr_seller_fee_rule\" style=\"display:none;\"><span class=\"frozr_back_to_fee_rules button-primary\">".__('Back','frozr')."</span><table><tbody>";
		foreach ($rule as $field_label => $field_value) {
		echo "<tr class=\"$field_label\">";
			if ($field_label == "rate") {
				$required = array('required' => 'required');
			}
			if ($field_label == "rate" || $field_label == "order_amount") {
				$input_type = 'number';
				$data_type = 'decimal';
				$min = '0';
				$step = 'any';
			} else {
				$input_type = 'text';
			}
			echo '<td>'.$rows_title[$field_label].'</td>';
			echo '<td>';
			if ($field_label == "customers_effected") {
				frozr_wp_select(array("id" => "frozr_settings[frozr_lazy_fees][$array_count][$field_label]", "class" => "", "value" => $field_value, "options" => $customers_effected_options ));
			} elseif ($field_label == "sellers_effected") {
				frozr_wp_select(array("id" => "frozr_settings[frozr_lazy_fees][$array_count][$field_label]", "class" => "", "value" => $field_value, "options" => $sellers_effected_options ));
			} elseif ($field_label == "sellers") {
				frozr_wp_select(array("id" => "frozr_settings[frozr_lazy_fees][$array_count][$field_label]", "class" => "", "value" => $field_value, "options" => frozr_get_all_sellers(), "custom_attributes" => array("multiple" => "multiple", "required" => "required") ));
			} elseif ($field_label == "customers") {
				frozr_wp_select(array("id" => "frozr_settings[frozr_lazy_fees][$array_count][$field_label]", "class" => "", "value" => $field_value, "options" => frozr_get_all_customers(), "custom_attributes" => array("multiple" => "multiple", "required" => "required") ));
			} elseif ($field_label == "amount_effect") {
				frozr_wp_select(array("id" => "frozr_settings[frozr_lazy_fees][$array_count][$field_label]", "class" => "", "value" => $field_value, "options" => $amount_effected_options ));
			} else {
				frozr_wp_text_input(array("id" => "frozr_settings[frozr_lazy_fees][$array_count][$field_label]", "class" => "", "value" => $field_value, "type" => $input_type, "data_type" => $data_type, "custom_attributes" => array($required, "step" => $step, "min" => $min) ));
			}
			echo '</td>';
		echo '</tr>';
		}
		$array_count++;
		echo "</tbody></table></div>";
	}
}
function frozr_reco_sellers_render() { 

	$option = get_option( 'frozr_settings' );
	$options = (! empty( $option['frozr_reco_sellers']) ) ? $option['frozr_reco_sellers'] : array('0');
	
	$args = apply_filters('frozr_reco_sellers_args', array(
		'role'			=> 'seller',
		'meta_key'		=> 'frozr_enable_selling',
		'meta_value'	=> 'yes',
		'order'			=> 'DESC',
		'orderby'		=> 'registered',
		'fields'		=> 'ID',
	));
	$sellers_query = new WP_User_Query( $args );
	$sellers = $sellers_query->get_results();

	?>
	<span class="description"><?php _e('This is used for the recommended restaurants filter in the homepage restaurants search filters.','frozr'); ?></span></br>
	<select name='frozr_settings[frozr_reco_sellers][]' multiple="multiple">
		<?php foreach($sellers as $seller ) {
			$user_store = frozr_get_store_info($seller);
			$user_info = get_userdata($seller);
			$seller_store = (!empty ($user_store['store_name'])) ? ' (' . $user_store['store_name'] . ')' : '';
			?>
			<option value="<?php echo $seller; ?>" <?php echo (in_array($seller, $options )) ? "selected" : ""; ?> ><?php echo $user_info->user_login . $seller_store; ?></option>
			<?php
		} ?>
	</select>
	<?php
}
function frozr_lazy_auto_updates_render() { 

	$option = get_option( 'frozr_settings' );
	$options = (! empty( $option['frozr_lazy_auto_updates']) ) ? $option['frozr_lazy_auto_updates'] : 0;
	?>
	<input type='checkbox' name='frozr_settings[frozr_lazy_auto_updates]' <?php checked( $options, 1 ); ?> value='0'>
	<?php
}

function frozr_allow_user_admin_access_render() { 

	$option = get_option( 'frozr_settings' );
	$options = (! empty( $option['frozr_allow_user_admin_access']) ) ? $option['frozr_allow_user_admin_access'] : 0;
	?>
	<input type='checkbox' name='frozr_settings[frozr_allow_user_admin_access]' <?php checked( $options, 1 ); ?> value='1'>
	<?php
}

function frozr_new_seller_status_render() { 

	$option = get_option( 'frozr_settings' );
	$options = (! empty( $option['frozr_new_seller_status']) ) ? $option['frozr_new_seller_status'] : 0;
	?>
	<input type='checkbox' name='frozr_settings[frozr_new_seller_status]' <?php checked( $options, 1 ); ?> value='1'>
	<?php
}

function frozr_minimum_withdraw_balance_render() { 

	$options = get_option( 'frozr_settings' );
	?>
	<input type='number' name='frozr_settings[frozr_minimum_withdraw_balance]' value='<?php echo (! empty( $options['frozr_minimum_withdraw_balance'])) ? $options['frozr_minimum_withdraw_balance'] : 50; ?>'>
	<?php
}

function frozr_tos_sellers_render() { 

	$options = get_option( 'frozr_settings');
	?>
	<span class="description"><?php _e('This will be used in the new seller (restaurant) registration form. Leave empty to not use.','frozr'); ?></span></br>
	<textarea name="frozr_settings[frozr_tos_sellers]" class="frozr_tos"><?php echo $options['frozr_tos_sellers']; ?></textarea>
	<?php
}

function frozr_tos_customers_render() { 

	$options = get_option( 'frozr_settings');
	?>
	<span class="description"><?php _e('This will be used in the new customer registration form. Leave empty to not use.','frozr'); ?></span></br>
	<textarea name="frozr_settings[frozr_tos_customers]" class="frozr_tos"><?php echo $options['frozr_tos_customers']; ?></textarea>
	<?php
}

function frozr_withdraw_methods_render() { 

	$option = get_option( 'frozr_settings');
	if (!empty ($option['frozr_withdraw_methods'])) {
		$option_array = ( is_array( $option['frozr_withdraw_methods']) ) ? $option['frozr_withdraw_methods'] : array($option['frozr_withdraw_methods']);
	} else {
		$option_array = '';
	}
	$options = (! empty( $option_array ) ) ? $option_array : array('paypal');
	$default_withdraws = frozr_withdraw_get_methods();
	?>
	<span class="description"><?php _e('Withdraw methods for sellers.','frozr'); ?></span></br>
	<select name="frozr_settings[frozr_withdraw_methods][]" multiple="multiple">
		<?php foreach($default_withdraws as $default_withdraw => $val) {
			$sel = in_array($default_withdraw, $options ) ? 'selected="selected"' : '';
			echo '<option value="'.$default_withdraw.'"'. $sel .'>'.$val.'</option>';
		} ?>
	</select>
	<?php
}

function frozr_withdraw_order_status_render() { 

	$option = get_option( 'frozr_settings' );
	$options = (! empty( $option['frozr_withdraw_order_status']) ) ? $option['frozr_withdraw_order_status'] : 'pending';
	?>
	<select name='frozr_settings[frozr_withdraw_order_status]'>
		<option value="completed" <?php selected( $options, 'completed' ); ?>><?php _e('Completed','frozr'); ?></option>
		<option value="processing" <?php selected( $options, 'processing' ); ?>><?php _e('Processing','frozr'); ?></option>
		<option value="pending" <?php selected( $options, 'pending' ); ?>><?php _e('Pending','frozr'); ?></option>
	</select>
	<?php
}

function frozr_options_page() {
	
	$current_tab = empty( $_GET['tab'] ) ? 'general' : sanitize_title( $_GET['tab'] );

	$tabs = apply_filters('frozr_lazyeater_settings_page_tabs', array(
		'general' => __('General','frozr'),
		'withdraw' => __('Withdraw','frozr'),
		'fees' => __('Fees/Commission','frozr'),
		'tos' => __('Terms of Service','frozr'),
	));
?>
		
	<div class="wrap lazyeater">
		<form method="<?php echo esc_attr( apply_filters( 'lazyeater_settings_form_method_tab_' . $current_tab, 'post' ) ); ?>" id="mainform" action="options.php" enctype="multipart/form-data">
			<nav class="nav-tab-wrapper">
				<?php
					foreach ( $tabs as $name => $label ) {
						echo '<a href="' . admin_url( 'admin.php?page=lazyeater&tab=' . $name ) . '" class="nav-tab ' . ( $current_tab == $name ? 'nav-tab-active' : '' ) . '">' . $label . '</a>';
					}
					do_action( 'lazyeater_settings_tabs' );
				?>
			</nav>
			<h1 class="screen-reader-text"><?php echo esc_html( $tabs[ $current_tab ] ); ?></h1>
			<?php

				settings_fields( 'lazyeater_page_' . $current_tab );
				
				if ($current_tab == 'fees') {
				
				do_settings_fields( 'lazyeater_page_fees', 'frozr_fees_options_section' );
				
				} else {
				
				do_settings_sections( 'lazyeater_page_' . $current_tab );
					
				}
				submit_button();
			?>
		</form>
	</div>
			
	<?php
}

// Customizer options
function frozr_lazyeater_options ($wp_customize) {
	$box_icon_array = array ('none' => __('No Icon','frozr'),'fs-icon-glass' => 'glass','fs-icon-music' => 'music','fs-icon-map' => 'Map','fs-icon-search' => 'search','fs-icon-envelope-o' => 'envelope','fs-icon-heart' => 'heart','fs-icon-star' => 'star','fs-icon-star-o' => 'star empty','fs-icon-user' => 'user','fs-icon-film' => 'film','fs-icon-th-large' => 'th-large','fs-icon-th' => 'th','fs-icon-th-list' => 'th-list','fs-icon-check' => 'check','fs-icon-remove' => 'remove','fs-icon-close' => 'close','fs-icon-times' => 'times','fs-icon-search-plus' => 'search-plus','fs-icon-search-minus' => 'search-minus','fs-icon-power-off' => 'power-off','fs-icon-signal' => 'signal','fs-icon-gear' => 'gear','fs-icon-cog' => 'cog','fs-icon-trash-o' => 'trash','fs-icon-home' => 'home','fs-icon-file-o' => 'file','fs-icon-clock-o' => 'clock','fs-icon-road' => 'road','fs-icon-download' => 'download','fs-icon-arrow-circle-o-down' => 'circle down','fs-icon-arrow-circle-o-up' => 'circle up','fs-icon-inbox' => 'inbox','fs-icon-play-circle-o' => 'circle','fs-icon-rotate-right' => 'rotate right','fs-icon-repeat' => 'repeat','fs-icon-refresh' => 'refresh','fs-icon-list-alt' => 'list-alt','fs-icon-lock' => 'lock','fs-icon-flag' => 'flag','fs-icon-headphones' => 'headphones','fs-icon-volume-off' => 'volume-off','fs-icon-volume-down' => 'volume-down','fs-icon-volume-up' => 'volume-up','fs-icon-qrcode' => 'qrcode','fs-icon-barcode' => 'barcode','fs-icon-tag' => 'tag','fs-icon-tags' => 'tags','fs-icon-book' => 'book','fs-icon-bookmark' => 'bookmark','fs-icon-print' => 'print','fs-icon-camera' => 'camera','fs-icon-font' => 'font','fs-icon-bold' => 'bold','fs-icon-italic' => 'italic','fs-icon-text-height' => 'text-height','fs-icon-text-width' => 'text-width','fs-icon-align-left' => 'align-left','fs-icon-align-center' => 'align-center','fs-icon-align-right' => 'align-right','fs-icon-align-justify' => 'align-justify','fs-icon-list' => 'list','fs-icon-dedent' => 'dedent','fs-icon-outdent' => 'outdent','fs-icon-indent' => 'indent','fs-icon-video-camera' => 'video-camera','fs-icon-photo' => 'photo','fs-icon-image' => 'image','fs-icon-picture-o' => 'picture','fs-icon-pencil' => 'pencil','fs-icon-map-marker' => 'map-marker','fs-icon-adjust' => 'adjust','fs-icon-tint' => 'tint','fs-icon-edit' => 'edit','fs-icon-pencil-square-o' => 'pencil-square','fs-icon-share-square-o' => 'share-square','fs-icon-check-square-o' => 'check-square','fs-icon-arrows' => 'arrows','fs-icon-step-backward' => 'step-backward','fs-icon-fast-backward' => 'fast-backward','fs-icon-backward' => 'backward','fs-icon-play' => 'play','fs-icon-pause' => 'pause','fs-icon-stop' => 'stop','fs-icon-forward' => 'forward','fs-icon-fast-forward' => 'fast-forward','fs-icon-step-forward' => 'step-forward','fs-icon-eject' => 'eject','fs-icon-chevron-left' => 'chevron-left','fs-icon-chevron-right' => 'chevron-right','fs-icon-plus-circle' => 'plus-circle','fs-icon-minus-circle' => 'minus-circle','fs-icon-times-circle' => 'times-circle','fs-icon-check-circle' => 'check-circle','fs-icon-question-circle' => 'question-circle','fs-icon-info-circle' => 'info-circle','fs-icon-crosshairs' => 'crosshairs','fs-icon-times-circle-o' => 'times-circle','fs-icon-check-circle-o' => 'check-circle','fs-icon-ban' => 'ban','fs-icon-arrow-left' => 'arrow-left','fs-icon-arrow-right' => 'arrow-right','fs-icon-arrow-up' => 'arrow-up','fs-icon-arrow-down' => 'arrow-down','fs-icon-mail-forward' => 'mail-forward','fs-icon-share' => 'share','fs-icon-expand' => 'expand','fs-icon-compress' => 'compress','fs-icon-plus' => 'plus','fs-icon-minus' => 'minus','fs-icon-asterisk' => 'asterisk','fs-icon-exclamation-circle' => 'exclamation-circle','fs-icon-gift' => 'gift','fs-icon-leaf' => 'leaf','fs-icon-fire' => 'fire','fs-icon-eye' => 'eye','fs-icon-eye-slash' => 'eye-slash','fs-icon-warning' => 'warning','fs-icon-exclamation-triangle' => 'exclamation-triangle','fs-icon-plane' => 'plane','fs-icon-calendar' => 'calendar','fs-icon-random' => 'random','fs-icon-comment' => 'comment','fs-icon-magnet' => 'magnet','fs-icon-chevron-up' => 'chevron-up','fs-icon-chevron-down' => 'chevron-down','fs-icon-retweet' => 'retweet','fs-icon-shopping-cart' => 'shopping-cart','fs-icon-folder' => 'folder','fs-icon-folder-open' => 'folder-open','fs-icon-arrows-v' => 'arrows-vertical','fs-icon-arrows-h' => 'arrows-horizontal','fs-icon-bar-chart-o' => 'bar-chart','fs-icon-bar-chart' => 'bar-chart','fs-icon-twitter-square' => 'twitter-square','fs-icon-facebook-square' => 'facebook-square','fs-icon-camera-retro' => 'camera-retro','fs-icon-key' => 'key','fs-icon-gears' => 'gears','fs-icon-cogs' => 'cogs','fs-icon-comments' => 'comments','fs-icon-thumbs-o-up' => 'thumbs-up','fs-icon-thumbs-o-down' => 'thumbs-down','fs-icon-star-half' => 'star-half','fs-icon-heart-o' => 'heart','fs-icon-sign-out' => 'sign-out','fs-icon-linkedin-square' => 'linkedin-square','fs-icon-thumb-tack' => 'thumb-tack','fs-icon-external-link' => 'external-link','fs-icon-sign-in' => 'sign-in','fs-icon-trophy' => 'trophy','fs-icon-github-square' => 'github-square','fs-icon-upload' => 'upload','fs-icon-lemon-o' => 'lemon','fs-icon-phone' => 'phone','fs-icon-square-o' => 'square','fs-icon-bookmark-o' => 'bookmark','fs-icon-phone-square' => 'phone-square','fs-icon-twitter' => 'twitter','fs-icon-facebook' => 'facebook','fs-icon-github' => 'github','fs-icon-unlock' => 'unlock','fs-icon-credit-card' => 'credit-card','fs-icon-rss' => 'rss','fs-icon-hdd-o' => 'hdd','fs-icon-bullhorn' => 'bullhorn','fs-icon-bell' => 'bell','fs-icon-certificate' => 'certificate','fs-icon-hand-o-right' => 'hand right','fs-icon-hand-o-left' => 'hand left','fs-icon-hand-o-up' => 'hand up','fs-icon-hand-o-down' => 'hand down','fs-icon-arrow-circle-left' => 'arrow-circle-left','fs-icon-arrow-circle-right' => 'arrow-circle-right','fs-icon-arrow-circle-up' => 'arrow-circle-up','fs-icon-arrow-circle-down' => 'arrow-circle-down','fs-icon-globe' => 'globe','fs-icon-wrench' => 'wrench','fs-icon-tasks' => 'tasks','fs-icon-filter' => 'filter','fs-icon-briefcase' => 'briefcase','fs-icon-arrows-alt' => 'arrows-alt','fs-icon-users' => 'users','fs-icon-link' => 'link','fs-icon-cloud' => 'cloud','fs-icon-flask' => 'flask','fs-icon-scissors' => 'scissors','fs-icon-copy' => 'copy','fs-icon-paperclip' => 'paperclip','fs-icon-save' => 'save','fs-icon-square' => 'square','fs-icon-navicon' => 'navicon','fs-icon-list-ul' => 'list-ul','fs-icon-list-ol' => 'list-ol','fs-icon-strikethrough' => 'strikethrough','fs-icon-underline' => 'underline','fs-icon-table' => 'table','fs-icon-magic' => 'magic','fs-icon-truck' => 'truck','fs-icon-pinterest' => 'pinterest','fs-icon-pinterest-square' => 'pinterest-square','fs-icon-google-plus-square' => 'google-plus-square','fs-icon-google-plus' => 'google-plus','fs-icon-money' => 'money','fs-icon-caret-down' => 'caret-down','fs-icon-caret-up' => 'caret-up','fs-icon-caret-left' => 'caret-left','fs-icon-caret-right' => 'caret-right','fs-icon-columns' => 'columns','fs-icon-unsorted' => 'unsorted','fs-icon-sort-down' => 'sort-down','fs-icon-sort-up' => 'sort-up','fs-icon-envelope' => 'envelope','fs-icon-linkedin' => 'linkedin','fs-icon-rotate-left' => 'rotate-left','fs-icon-legal' => 'legal','fs-icon-dashboard' => 'dashboard','fs-icon-comment-o' => 'comment','fs-icon-comments-o' => 'comments','fs-icon-flash' => 'flash','fs-icon-sitemap' => 'sitemap','fs-icon-umbrella' => 'umbrella','fs-icon-paste' => 'paste','fs-icon-lightbulb-o' => 'lightbulb','fs-icon-exchange' => 'exchange','fs-icon-cloud-download' => 'cloud-download','fs-icon-cloud-upload' => 'cloud-upload','fs-icon-user-md' => 'user','fs-icon-stethoscope' => 'stethoscope','fs-icon-suitcase' => 'suitcase','fs-icon-bell-o' => 'bell','fs-icon-coffee' => 'coffee','fs-icon-cutlery' => 'cutlery','fs-icon-file-text-o' => 'file-text','fs-icon-building-o' => 'building','fs-icon-hospital-o' => 'hospital','fs-icon-ambulance' => 'ambulance','fs-icon-medkit' => 'medkit','fs-icon-fighter-jet' => 'fighter-jet','fs-icon-beer' => 'beer','fs-icon-h-square' => 'square','fs-icon-plus-square' => 'plus-square','fs-icon-angle-double-left' => 'angle-double-left','fs-icon-angle-double-right' => 'angle-double-right','fs-icon-angle-double-up' => 'angle-double-up','fs-icon-angle-double-down' => 'angle-double-down','fs-icon-angle-left' => 'angle-left','fs-icon-angle-right' => 'angle-right','fs-icon-angle-up' => 'angle-up','fs-icon-angle-down' => 'angle-down','fs-icon-desktop' => 'desktop','fs-icon-laptop' => 'laptop','fs-icon-tablet' => 'tablet','fs-icon-mobile-phone' => 'mobile-phone','fs-icon-circle-o' => 'circle old','fs-icon-quote-left' => 'quote-left','fs-icon-quote-right' => 'quote-right','fs-icon-spinner' => 'spinner','fs-icon-circle' => 'circle','fs-icon-mail-reply' => 'mail-reply','fs-icon-github-alt' => 'github-alt','fs-icon-folder-o' => 'folder','fs-icon-folder-open-o' => 'folder-open','fs-icon-smile-o' => 'smile','fs-icon-frown-o' => 'frown','fs-icon-meh-o' => 'meh','fs-icon-gamepad' => 'gamepad','fs-icon-keyboard-o' => 'keyboard','fs-icon-flag-o' => 'flag','fs-icon-flag-checkered' => 'flag-checkered','fs-icon-terminal' => 'terminal','fs-icon-code' => 'code','fs-icon-reply-all' => 'reply-all','fs-icon-star-half-empty' => 'star-half-empty','fs-icon-location-arrow' => 'location-arrow','fs-icon-crop' => 'crop','fs-icon-code-fork' => 'code-fork','fs-icon-unlink' => 'unlink','fs-icon-question' => 'question','fs-icon-info' => 'info','fs-icon-exclamation' => 'exclamation','fs-icon-superscript' => 'superscript','fs-icon-subscript' => 'subscript','fs-icon-eraser' => 'eraser','fs-icon-puzzle-piece' => 'puzzle-piece','fs-icon-microphone' => 'microphone','fs-icon-microphone-slash' => 'microphone-slash','fs-icon-shield' => 'shield','fs-icon-calendar-o' => 'calendar','fs-icon-fire-extinguisher' => 'fire-extinguisher','fs-icon-rocket' => 'rocket','fs-icon-maxcdn' => 'maxcdn','fs-icon-chevron-circle-left' => 'chevron-circle-left','fs-icon-chevron-circle-right' => 'chevron-circle-right','fs-icon-chevron-circle-up' => 'chevron-circle-up','fs-icon-chevron-circle-down' => 'chevron-circle-down','fs-icon-html5' => 'html5','fs-icon-css3' => 'css3','fs-icon-anchor' => 'anchor','fs-icon-unlock-alt' => 'unlock-alt','fs-icon-bullseye' => 'bullseye','fs-icon-ellipsis-h' => 'ellipsis-horizontal','fs-icon-ellipsis-v' => 'ellipsis-vertical','fs-icon-rss-square' => 'rss-square','fs-icon-play-circle' => 'play-circle','fs-icon-ticket' => 'ticket','fs-icon-minus-square' => 'minus-square','fs-icon-minus-square-o' => 'minus-square-old','fs-icon-level-up' => 'level-up','fs-icon-level-down' => 'level-down','fs-icon-check-square' => 'check-square','fs-icon-pencil-square' => 'pencil-square','fs-icon-external-link-square' => 'external-link-square','fs-icon-share-square' => 'share-square','fs-icon-compass' => 'compass','fs-icon-toggle-down' => 'toggle-down','fs-icon-toggle-up' => 'toggle-up','fs-icon-toggle-right' => 'toggle-right','fs-icon-euro' => 'euro','fs-icon-gbp' => 'gbp','fs-icon-dollar' => 'dollar','fs-icon-rupee' => 'rupee','fs-icon-cny' => 'cny','fs-icon-ruble' => 'ruble','fs-icon-won' => 'won','fs-icon-bitcoin' => 'bitcoin','fs-icon-file' => 'file','fs-icon-file-text' => 'file-text','fs-icon-sort-alpha-asc' => 'sort-alpha-asc','fs-icon-sort-alpha-desc' => 'sort-alpha-desc','fs-icon-sort-amount-asc' => 'sort-amount-asc','fs-icon-sort-amount-desc' => 'sort-amount-desc','fs-icon-sort-numeric-asc' => 'sort-numeric-asc','fs-icon-sort-numeric-desc' => 'sort-numeric-desc','fs-icon-thumbs-up' => 'thumbs-up','fs-icon-thumbs-down' => 'thumbs-down','fs-icon-youtube-square' => 'youtube-square','fs-icon-youtube' => 'youtube','fs-icon-xing' => 'xing','fs-icon-xing-square' => 'xing-square','fs-icon-youtube-play' => 'youtube-play','fs-icon-dropbox' => 'dropbox','fs-icon-stack-overflow' => 'stack-overflow','fs-icon-instagram' => 'instagram','fs-icon-flickr' => 'flickr','fs-icon-adn' => 'adn','fs-icon-bitbucket' => 'bitbucket','fs-icon-bitbucket-square' => 'bitbucket-square','fs-icon-tumblr' => 'tumblr','fs-icon-tumblr-square' => 'tumblr-square','fs-icon-long-arrow-down' => 'long-arrow-down','fs-icon-long-arrow-up' => 'long-arrow-up','fs-icon-long-arrow-left' => 'long-arrow-left','fs-icon-long-arrow-right' => 'long-arrow-right','fs-icon-apple' => 'apple','fs-icon-windows' => 'windows','fs-icon-android' => 'android','fs-icon-linux' => 'linux','fs-icon-dribbble' => 'dribbble','fs-icon-skype' => 'skype','fs-icon-foursquare' => 'foursquare','fs-icon-trello' => 'trello','fs-icon-female' => 'female','fs-icon-male' => 'male','fs-icon-gittip' => 'gittip','fs-icon-sun-o' => 'sun','fs-icon-moon-o' => 'moon','fs-icon-archive' => 'archive','fs-icon-bug' => 'bug','fs-icon-vk' => 'vk','fs-icon-weibo' => 'weibo','fs-icon-renren' => 'renren','fs-icon-pagelines' => 'pagelines','fs-icon-stack-exchange' => 'stack-exchange','fs-icon-arrow-circle-o-right' => 'arrow-circle-right','fs-icon-arrow-circle-o-left' => 'arrow-circle-left','fs-icon-toggle-left' => 'toggle-left','fs-icon-dot-circle-o' => 'dot-circle','fs-icon-wheelchair' => 'wheelchair','fs-icon-vimeo-square' => 'vimeo-square','fs-icon-turkish-lira' => 'turkish-lira','fs-icon-plus-square-o' => 'plus-square','fs-icon-space-shuttle' => 'space-shuttle','fs-icon-slack' => 'slack','fs-icon-envelope-square' => 'envelope-square','fs-icon-wordpress' => 'wordpress','fs-icon-openid' => 'openid','fs-icon-institution' => 'institution','fs-icon-mortar-board' => 'mortar-board','fs-icon-yahoo' => 'yahoo','fs-icon-google' => 'google','fs-icon-reddit' => 'reddit','fs-icon-reddit-square' => 'reddit-square','fs-icon-stumbleupon-circle' => 'stumbleupon-circle','fs-icon-stumbleupon' => 'stumbleupon','fs-icon-delicious' => 'delicious','fs-icon-digg' => 'digg','fs-icon-pied-piper' => 'pied-piper','fs-icon-pied-piper-alt' => 'pied-piper-alt','fs-icon-drupal' => 'drupal','fs-icon-joomla' => 'joomla','fs-icon-language' => 'language','fs-icon-fax' => 'fax','fs-icon-building' => 'building','fs-icon-child' => 'child','fs-icon-paw' => 'paw','fs-icon-spoon' => 'spoon','fs-icon-cube' => 'cube','fs-icon-cubes' => 'cubes','fs-icon-behance' => 'behance','fs-icon-behance-square' => 'behance-square','fs-icon-steam' => 'steam','fs-icon-steam-square' => 'steam-square','fs-icon-recycle' => 'recycle','fs-icon-automobile' => 'automobile','fs-icon-taxi' => 'taxi','fs-icon-tree' => 'tree','fs-icon-spotify' => 'spotify','fs-icon-deviantart' => 'deviantart','fs-icon-soundcloud' => 'soundcloud','fs-icon-database' => 'database','fs-icon-file-pdf-o' => 'file-pdf','fs-icon-file-word-o' => 'file-word','fs-icon-file-excel-o' => 'file-excel','fs-icon-file-powerpoint-o' => 'file-powerpoint','fs-icon-file-photo-o' => 'file-photo','fs-icon-file-zip-o' => 'file-zip','fs-icon-file-sound-o' => 'file-sound','fs-icon-file-movie-o' => 'file-movie','fs-icon-file-code-o' => 'file-code','fs-icon-vine' => 'vine','fs-icon-codepen' => 'codepen','fs-icon-jsfiddle' => 'jsfiddle','fs-icon-life-ring' => 'life-ring','fs-icon-circle-o-notch' => 'circle-notch','fs-icon-rebel' => 'rebel','fs-icon-empire' => 'empire','fs-icon-git-square' => 'git-square','fs-icon-git' => 'git','fs-icon-hacker-news' => 'hacker-news','fs-icon-tencent-weibo' => 'tencent-weibo','fs-icon-qq' => 'qq','fs-icon-wechat' => 'wechat','fs-icon-send' => 'send','fs-icon-paper-plane-o' => 'paper-plane','fs-icon-history' => 'history','fs-icon-genderless' => 'genderless','fs-icon-header' => 'header','fs-icon-paragraph' => 'paragraph','fs-icon-sliders' => 'sliders','fs-icon-share-alt' => 'share-alt','fs-icon-share-alt-square' => 'share-alt-square','fs-icon-bomb' => 'bomb','fs-icon-soccer-ball-o' => 'soccer-ball','fs-icon-tty' => 'tty','fs-icon-binoculars' => 'binoculars','fs-icon-plug' => 'plug','fs-icon-slideshare' => 'slideshare','fs-icon-twitch' => 'twitch','fs-icon-yelp' => 'yelp','fs-icon-newspaper-o' => 'newspaper','fs-icon-wifi' => 'wifi','fs-icon-calculator' => 'calculator','fs-icon-paypal' => 'paypal','fs-icon-google-wallet' => 'google-wallet','fs-icon-cc-visa' => 'visa','fs-icon-cc-mastercard' => 'mastercard','fs-icon-cc-discover' => 'discover','fs-icon-cc-amex' => 'amex','fs-icon-cc-paypal' => 'paypal','fs-icon-cc-stripe' => 'stripe','fs-icon-bell-slash' => 'bell-slash','fs-icon-bell-slash-o' => 'bell-slash-old','fs-icon-trash' => 'trash','fs-icon-copyright' => 'copyright','fs-icon-at' => 'at','fs-icon-eyedropper' => 'eyedropper','fs-icon-paint-brush' => 'paint-brush','fs-icon-birthday-cake' => 'birthday-cake','fs-icon-area-chart' => 'area-chart','fs-icon-pie-chart' => 'pie-chart','fs-icon-line-chart' => 'line-chart','fs-icon-lastfm' => 'lastfm','fs-icon-lastfm-square' => 'lastfm-square','fs-icon-toggle-off' => 'toggle-off','fs-icon-toggle-on' => 'toggle-on','fs-icon-bicycle' => 'bicycle','fs-icon-bus' => 'bus','fs-icon-ioxhost' => 'ioxhost','fs-icon-angellist' => 'angellist','fs-icon-cc' => 'cc','fs-icon-shekel' => 'shekel','fs-icon-meanpath' => 'meanpath','fs-icon-buysellads' => 'buysellads','fs-icon-connectdevelop' => 'connectdevelop','fs-icon-dashcube' => 'dashcube','fs-icon-forumbee' => 'forumbee','fs-icon-leanpub' => 'leanpub','fs-icon-sellsy' => 'sellsy','fs-icon-shirtsinbulk' => 'shirtsinbulk','fs-icon-simplybuilt' => 'simplybuilt','fs-icon-skyatlas' => 'skyatlas','fs-icon-cart-plus' => 'cart-plus','fs-icon-cart-arrow-down' => 'cart-arrow-down','fs-icon-diamond' => 'diamond','fs-icon-ship' => 'ship','fs-icon-user-secret' => 'user-secret','fs-icon-motorcycle' => 'motorcycle','fs-icon-street-view' => 'street-view','fs-icon-heartbeat' => 'heartbeat','fs-icon-venus' => 'venus','fs-icon-mars' => 'mars','fs-icon-mercury' => 'mercury','fs-icon-transgender' => 'transgender','fs-icon-transgender-alt' => 'transgender-alt','fs-icon-venus-double' => 'venus-double','fs-icon-mars-double' => 'mars-double','fs-icon-venus-mars' => 'venus-mars','fs-icon-mars-stroke' => 'mars-stroke','fs-icon-mars-stroke-v' => 'mars-stroke-vertical','fs-icon-mars-stroke-h' => 'mars-stroke-horizontal','fs-icon-neuter' => 'neuter','fs-icon-facebook-official' => 'facebook-official','fs-icon-pinterest-p' => 'pinterest','fs-icon-whatsapp' => 'whatsapp','fs-icon-server' => 'server','fs-icon-user-plus' => 'user-plus','fs-icon-user-times' => 'user-times','fs-icon-hotel' => 'hotel','fs-icon-viacoin' => 'viacoin','fs-icon-train' => 'train','fs-icon-subway' => 'subway','fs-icon-medium' => 'medium');
	//front page settings
	$wp_customize->add_setting( 'front_sort_objects', array('default' => 'popu,reco,type','capability' => 'edit_theme_options','transport' => 'refresh','sanitize_callback' => 'esc_attr'));
	$wp_customize->add_setting( 'front_delv_sort_objects', array('default' => 'cusearch,restd','capability' => 'edit_theme_options','transport' => 'refresh','sanitize_callback' => 'esc_attr'));
	$wp_customize->add_setting( 'front_sort_objects_two', array('default' => 'cusearch,restsearch,adlocsearch','capability' => 'edit_theme_options','transport' => 'refresh','sanitize_callback' => 'esc_attr'));
	$wp_customize->add_setting( 'front_delv_sort_objects_two', array('default' => 'catsearch,ingsearch,spysearch','capability' => 'edit_theme_options','transport' => 'refresh','sanitize_callback' => 'esc_attr'));
	$wp_customize->add_setting( 'front_trash_sort_objects', array('default' => 'none','capability' => 'edit_theme_options','transport' => 'refresh','sanitize_callback' => 'esc_attr'));
	$wp_customize->add_setting( 'front_trash_delv_sort_objects', array('default' => 'none','capability' => 'edit_theme_options','transport' => 'refresh','sanitize_callback' => 'esc_attr'));
	$wp_customize->add_setting( 'front_rest_adv_search_title_one', array('default' => __( "Order your food online from local restaurants.", "frozr"),'capability' => 'edit_theme_options','transport' => 'postMessage','sanitize_callback' => 'esc_attr'));
	$wp_customize->add_setting( 'front_rest_adv_search_title_two', array('default' => __( "Order your food online from local restaurants.", "frozr"),'capability' => 'edit_theme_options','transport' => 'postMessage','sanitize_callback' => 'esc_attr'));
	$wp_customize->add_setting( 'front_txt_cusearch', array('default' => frozr_front_texts(2),'capability' => 'edit_theme_options','transport' => 'refresh','sanitize_callback' => 'esc_attr'));
	$wp_customize->add_setting( 'front_txt_restsearch', array('default' => frozr_front_texts(1),'capability' => 'edit_theme_options','transport' => 'refresh','sanitize_callback' => 'esc_attr'));
	$wp_customize->add_setting( 'front_txt_adlocsearch', array('default' => frozr_front_texts(3),'capability' => 'edit_theme_options','transport' => 'refresh','sanitize_callback' => 'esc_attr'));
	$wp_customize->add_setting( 'front_txt_restd', array('default' => frozr_front_texts(4),'capability' => 'edit_theme_options','transport' => 'refresh','sanitize_callback' => 'esc_attr'));
	$wp_customize->add_setting( 'front_txt_catsearch', array('default' => frozr_front_texts(5),'capability' => 'edit_theme_options','transport' => 'refresh','sanitize_callback' => 'esc_attr'));
	$wp_customize->add_setting( 'front_txt_ingsearch', array('default' => frozr_front_texts(6),'capability' => 'edit_theme_options','transport' => 'refresh','sanitize_callback' => 'esc_attr'));
	$wp_customize->add_setting( 'front_txt_spysearch', array('default' => frozr_front_texts(7),'capability' => 'edit_theme_options','transport' => 'refresh','sanitize_callback' => 'esc_attr'));
	//Restaurants Search box
	$wp_customize->add_section( 'rest_adv_search', array('title' => __( 'Restaurants Search Section', 'frozr' ),'panel' => 'front_page','priority' => 35,'capability' => 'edit_theme_options'));
	$wp_customize->add_setting( 'show_rest_adv_search', array('default' => true,'capability' => 'edit_theme_options','transport' => 'refresh','sanitize_callback' => 'esc_attr'));
	$wp_customize->add_control( new Add_class ( $wp_customize, 'frozr_show_rest_adv_search', array('label' => __( "Show Restaurant Search Section?", "frozr"),'section' => 'rest_adv_search','settings' => 'show_rest_adv_search', 'sclass' => 'frozr_checkbox_option', 'frozr_attrs' => array('data-amount'=>'11'), 'active_callback' => 'is_front_page','type' => 'checkbox')));
	frozr_background_bund( $wp_customize, 'Section ', 'rest_adv_search', 'is_front_page', '', 'rads_bg_color', 'rads_bg_image', 'rads_bg_repeat', 'rads_bg_position', 'rads_bg_attachment', '#725827');
	//Restaurants Search filters options
	$wp_customize->add_setting( 'rest_adv_filter_accord', array('default' => '','capability' => 'edit_theme_options','transport' => 'postMessage','sanitize_callback' => 'esc_attr'));
	$wp_customize->add_control( new Add_class ( $wp_customize,'frozr_rest_adv_filter_accord', array('label' => __( ' Search Filters Options', 'frozr'),'section' => 'rest_adv_search','settings' => 'rest_adv_filter_accord','sclass'=>'adv_options','adv'=>true,'frozr_attrs'=>array('data-amount'=>'16'),'active_callback' => 'is_front_page')));	
	$wp_customize->add_setting( 'show_rest_adv_filter_accord', array('default' => true,'capability' => 'edit_theme_options','transport' => 'refresh','sanitize_callback' => 'esc_attr'));
	$wp_customize->add_control( new Add_class ( $wp_customize, 'frozr_show_rest_adv_filter_accord', array('label' => __( "Show Restaurant Search Filters Section?", "frozr"),'section' => 'rest_adv_search','settings' => 'show_rest_adv_filter_accord', 'sclass' => 'frozr_checkbox_option', 'frozr_attrs' => array('data-amount'=>'12'), 'active_callback' => 'is_front_page','type' => 'checkbox')));
	frozr_background_bund( $wp_customize, 'Filters Section ', 'rest_adv_search', 'is_front_page', '', 'radfs_bg_color', 'radfs_bg_image', 'radfs_bg_repeat', 'radfs_bg_position', 'radfs_bg_attachment', '#f89d1a');
	$wp_customize->add_setting( 'rest_adv_filters_ty_color', array('default' => '#4f3d1b','capability' => 'edit_theme_options','transport' => 'postMessage','sanitize_callback' => 'sanitize_hex_color'));
	$wp_customize->add_control( new WP_Customize_Color_Control ( $wp_customize,'frozr_rest_adv_filters_ty_color', array('label' => __( 'Search Filters Titles Color', 'frozr' ),'section' => 'rest_adv_search','settings' => 'rest_adv_filters_ty_color','active_callback' => 'is_front_page')));
	//Top Selling items
	$wp_customize->add_section( 'top_selling_dish', array('title' => __( 'Top Selling Items', 'frozr' ),'panel' => 'front_page','priority' => 35,'capability' => 'edit_theme_options'));
	$wp_customize->add_setting( 'show_top_dishes', array('default' => true,'capability' => 'edit_theme_options','transport' => 'refresh','sanitize_callback' => 'esc_attr'));
	$wp_customize->add_control( new Add_class ( $wp_customize, 'frozr_show_top_dishes', array('label' => __( "Show Top Selling Items?", "frozr"),'section' => 'top_selling_dish','settings' => 'show_top_dishes', 'sclass' => 'frozr_checkbox_option', 'frozr_attrs' => array('data-amount'=>'23'), 'active_callback' => 'is_front_page','type' => 'checkbox')));
	$wp_customize->add_setting( 'top_dish_loop_title', array('default' => 'Top Selling Items','capability' => 'edit_theme_options','transport' => 'postMessage','sanitize_callback' => 'sanitize_text_field'));
	$wp_customize->add_control( 'frozr_top_dish_loop_title', array('label' => __( "Loop title", "frozr"),'section' => 'top_selling_dish','settings' => 'top_dish_loop_title','active_callback' => 'is_front_page'));
	$wp_customize->add_setting( 'top_dish_desc', array('default' => 'Top selling items this week!','capability' => 'edit_theme_options','transport' => 'postMessage','sanitize_callback' => 'sanitize_text_field'));
	$wp_customize->add_control( 'frozr_top_dish_desc', array('label' => __( "Loop description", "frozr"),'section' => 'top_selling_dish','settings' => 'top_dish_desc','active_callback' => 'is_front_page'));
	//Top Selling layout options
	$wp_customize->add_setting( 'top_dish_lyt_accord', array('default' => '','capability' => 'edit_theme_options','transport' => 'postMessage','sanitize_callback' => 'esc_attr'));
	$wp_customize->add_control( new Add_class ( $wp_customize,'frozr_top_dish_lyt_accord', array('label' => __( ' Layout Options', 'frozr'),'section' => 'top_selling_dish','settings' => 'top_dish_lyt_accord','sclass'=>'adv_options','adv'=>true,'frozr_attrs'=>array('data-amount'=>'8'),'active_callback' => 'is_front_page')));	
	frozr_background_bund($wp_customize, 'Loop', 'top_selling_dish', 'is_front_page', '', 'top_dish_bg_color', 'top_dish_bg_image', 'top_dish_bg_repeat', 'top_dish_bg_position', 'top_dish_bg_attchment');
	$wp_customize->add_setting( 'top_dish_border', array('default' => true,'capability' => 'edit_theme_options','transport' => 'postMessage','sanitize_callback' => 'esc_attr'));
	$wp_customize->add_control( 'frozr_top_dish_border', array('label' => __( "Show the loop top border", "frozr"),'section' => 'top_selling_dish','settings' => 'top_dish_border', 'active_callback' => 'is_front_page','type' => 'checkbox'));
	$wp_customize->add_setting( 'top_dish_icon', array('default' => 'none','capability' => 'edit_theme_options','transport' => 'postMessage','sanitize_callback' => 'esc_attr'));
	$wp_customize->add_control( 'frozr_top_dish_icon', array('label' => __( "Loop Icon", "frozr"),'section' => 'top_selling_dish','settings' => 'top_dish_icon','active_callback' => 'is_front_page','type' => 'select', 'choices' => $box_icon_array));
	//Top Selling typography options
	$wp_customize->add_setting( 'top_dish_typo_accord', array('default' => '','capability' => 'edit_theme_options','transport' => 'postMessage','sanitize_callback' => 'esc_attr'));
	$wp_customize->add_control( new Add_class ( $wp_customize,'frozr_top_dish_typo_accord', array('label' => __( ' Typography Options', 'frozr'),'section' => 'top_selling_dish','settings' => 'top_dish_typo_accord','sclass'=>'adv_options','adv'=>true,'frozr_attrs'=>array('data-amount'=>'4'),'active_callback' => 'is_front_page')));	
	$wp_customize->add_setting( 'top_dish_title_ty_color', array('default' => '#4a515a','capability' => 'edit_theme_options','transport' => 'postMessage','sanitize_callback' => 'sanitize_hex_color'));
	$wp_customize->add_control( new WP_Customize_Color_Control ( $wp_customize,'frozr_top_dish_title_ty_color', array('label' => __( 'Loop title color', 'frozr' ),'section' => 'top_selling_dish','settings' => 'top_dish_title_ty_color','active_callback' => 'is_front_page')));
	$wp_customize->add_setting( 'top_dish_desc_ty_color', array('default' => '#4a515a','capability' => 'edit_theme_options','transport' => 'postMessage','sanitize_callback' => 'sanitize_hex_color'));
	$wp_customize->add_control( new WP_Customize_Color_Control ( $wp_customize,'frozr_top_dish_desc_ty_color', array('label' => __( 'Loop description color', 'frozr' ),'section' => 'top_selling_dish','settings' => 'top_dish_desc_ty_color','active_callback' => 'is_front_page')));
	//latest restaurants
	$wp_customize->add_section( 'latest_rests', array('title' => __( 'Latest Restaurants', 'frozr' ),'panel' => 'front_page','priority' => 35,'capability' => 'edit_theme_options'));
	$wp_customize->add_setting( 'show_latest_rests', array('default' => true,'capability' => 'edit_theme_options','transport' => 'refresh','sanitize_callback' => 'esc_attr'));
	$wp_customize->add_control( new Add_class ( $wp_customize, 'frozr_show_latest_rests', array('label' => __( "Show Latest Rests?", "frozr"),'section' => 'latest_rests','settings' => 'show_latest_rests', 'sclass' => 'frozr_checkbox_option', 'frozr_attrs' => array('data-amount'=>'23'), 'active_callback' => 'is_front_page','type' => 'checkbox')));
	$wp_customize->add_setting( 'latest_rests_title', array('default' => 'Latest Restaurants','capability' => 'edit_theme_options','transport' => 'postMessage','sanitize_callback' => 'sanitize_text_field'));
	$wp_customize->add_control( 'frozr_latest_rests_title', array('label' => __( "Loop title", "frozr"),'section' => 'latest_rests','settings' => 'latest_rests_title','active_callback' => 'is_front_page'));
	$wp_customize->add_setting( 'latest_rests_desc', array('default' => 'Newest Restaurants Joined The List!','capability' => 'edit_theme_options','transport' => 'postMessage','sanitize_callback' => 'sanitize_text_field'));
	$wp_customize->add_control( 'frozr_latest_rests_desc', array('label' => __( "Loop description", "frozr"),'section' => 'latest_rests','settings' => 'latest_rests_desc','active_callback' => 'is_front_page'));
	//latest restaurants layout options
	$wp_customize->add_setting( 'latest_rests_lyt_accord', array('default' => '','capability' => 'edit_theme_options','transport' => 'postMessage','sanitize_callback' => 'esc_attr'));
	$wp_customize->add_control( new Add_class ( $wp_customize,'frozr_latest_rests_lyt_accord', array('label' => __( ' Layout Options', 'frozr'),'section' => 'latest_rests','settings' => 'latest_rests_lyt_accord','sclass'=>'adv_options','adv'=>true,'frozr_attrs'=>array('data-amount'=>'8'),'active_callback' => 'is_front_page')));	
	frozr_background_bund($wp_customize, 'Loop', 'latest_rests', 'is_front_page', '', 'latest_rests_bg_color', 'latest_rests_bg_image', 'latest_rests_bg_repeat', 'latest_rests_bg_position', 'latest_rests_bg_attchment');
	$wp_customize->add_setting( 'latest_rests_border', array('default' => true,'capability' => 'edit_theme_options','transport' => 'postMessage','sanitize_callback' => 'esc_attr'));
	$wp_customize->add_control( 'frozr_latest_rests_border', array('label' => __( "Show the loop top border", "frozr"),'section' => 'latest_rests','settings' => 'latest_rests_border', 'active_callback' => 'is_front_page','type' => 'checkbox'));
	$wp_customize->add_setting( 'latest_rests_icon', array('default' => 'none','capability' => 'edit_theme_options','transport' => 'postMessage','sanitize_callback' => 'esc_attr'));
	$wp_customize->add_control( 'frozr_latest_rests_icon', array('label' => __( "Loop Icon", "frozr"),'section' => 'latest_rests','settings' => 'latest_rests_icon','active_callback' => 'is_front_page','type' => 'select', 'choices' => $box_icon_array));
	//latest restaurants typography options
	$wp_customize->add_setting( 'latest_rests_typo_accord', array('default' => '','capability' => 'edit_theme_options','transport' => 'postMessage','sanitize_callback' => 'esc_attr'));
	$wp_customize->add_control( new Add_class ( $wp_customize,'frozr_latest_rests_typo_accord', array('label' => __( ' Typography Options', 'frozr'),'section' => 'latest_rests','settings' => 'latest_rests_typo_accord','sclass'=>'adv_options','adv'=>true,'frozr_attrs'=>array('data-amount'=>'4'),'active_callback' => 'is_front_page')));	
	$wp_customize->add_setting( 'latest_rests_title_ty_color', array('default' => '#4a515a','capability' => 'edit_theme_options','transport' => 'postMessage','sanitize_callback' => 'sanitize_hex_color'));
	$wp_customize->add_control( new WP_Customize_Color_Control ( $wp_customize,'frozr_latest_rests_title_ty_color', array('label' => __( 'Loop title color', 'frozr' ),'section' => 'latest_rests','settings' => 'latest_rests_title_ty_color','active_callback' => 'is_front_page')));
	$wp_customize->add_setting( 'latest_rests_desc_ty_color', array('default' => '#4a515a','capability' => 'edit_theme_options','transport' => 'postMessage','sanitize_callback' => 'sanitize_hex_color'));
	$wp_customize->add_control( new WP_Customize_Color_Control ( $wp_customize,'frozr_latest_rests_desc_ty_color', array('label' => __( 'Loop description color', 'frozr' ),'section' => 'latest_rests','settings' => 'latest_rests_desc_ty_color','active_callback' => 'is_front_page')));
}
add_action('customize_register','frozr_lazyeater_options');

function frozr_lazyeater_output_css() {
	
	$theme_layout = get_theme_mod('theme_layout','left');
	$cusine_filter_image = (wp_get_attachment_url( get_theme_mod('cusine_filter_image'))) ? wp_get_attachment_url( get_theme_mod('cusine_filter_image')) : get_theme_mod('cusine_filter_image', plugins_url( 'assets/imgs/cus_pic.png', lAZY_EATER_FILE ));
	$category_filter_image = (wp_get_attachment_url( get_theme_mod('category_filter_image'))) ? wp_get_attachment_url( get_theme_mod('category_filter_image')) : get_theme_mod('category_filter_image', plugins_url( 'assets/imgs/burger_pic.png', lAZY_EATER_FILE ));
	$rests_filter_image = (wp_get_attachment_url( get_theme_mod('rests_filter_image'))) ? wp_get_attachment_url( get_theme_mod('rests_filter_image')) : get_theme_mod('rests_filter_image', plugins_url( 'assets/imgs/resn_pic.png', lAZY_EATER_FILE ));
	$addressloc_filter_image = (wp_get_attachment_url( get_theme_mod('addressloc_filter_image'))) ? wp_get_attachment_url( get_theme_mod('addressloc_filter_image')) : get_theme_mod('addressloc_filter_image', plugins_url( 'assets/imgs/loc_pic.png', lAZY_EATER_FILE ));
	$search_type_filter_image = (wp_get_attachment_url( get_theme_mod('search_type_filter_image'))) ? wp_get_attachment_url( get_theme_mod('search_type_filter_image')) : get_theme_mod('search_type_filter_image', plugins_url( 'assets/imgs/spicy_pic.png', lAZY_EATER_FILE ));
	$ingredient_filter_image = (wp_get_attachment_url( get_theme_mod('ingredient_filter_image'))) ? wp_get_attachment_url( get_theme_mod('ingredient_filter_image')) : get_theme_mod('ingredient_filter_image', plugins_url( 'assets/imgs/ing_pic.png', lAZY_EATER_FILE ));
	$typeimg_filter_image = (wp_get_attachment_url( get_theme_mod('typeimg_filter_image'))) ? wp_get_attachment_url( get_theme_mod('typeimg_filter_image')) : get_theme_mod('typeimg_filter_image', plugins_url( 'assets/imgs/spicy_pic.png', lAZY_EATER_FILE ));
	$recoimg_filter_image = (wp_get_attachment_url( get_theme_mod('recoimg_filter_image'))) ? wp_get_attachment_url( get_theme_mod('recoimg_filter_image')) : get_theme_mod('recoimg_filter_image', plugins_url( 'assets/imgs/reco_pic.png', lAZY_EATER_FILE ));
	$popuimg_filter_image = (wp_get_attachment_url( get_theme_mod('popuimg_filter_image'))) ? wp_get_attachment_url( get_theme_mod('popuimg_filter_image')) : get_theme_mod('popuimg_filter_image', plugins_url( 'assets/imgs/pop_pic.png', lAZY_EATER_FILE ));
	$restdimg_filter_image = (wp_get_attachment_url( get_theme_mod('restdimg_filter_image'))) ? wp_get_attachment_url( get_theme_mod('restdimg_filter_image')) : get_theme_mod('restdimg_filter_image', plugins_url( 'assets/imgs/loc_pic.png', lAZY_EATER_FILE ));
?>
	<style type="text/css">
	div#resturants_search_box {
		<?php if (get_theme_mod('rads_bg_image', plugins_url( 'assets/imgs/last.png', lAZY_EATER_FILE )) != null) { ?>
		background-image: <?php echo ' url("' . get_theme_mod('rads_bg_image', plugins_url( 'assets/imgs/last.png', lAZY_EATER_FILE )) . '")'; ?>;
		background-color: <?php echo get_theme_mod('rads_bg_color', '#725827'); ?>;
		background-position: <?php if (frozr_mobile()) { echo'50% 50%'; } else { echo get_theme_mod('rads_bg_position'); } ?>;
		background-repeat: <?php echo get_theme_mod('rads_bg_repeat', 'no-repeat'); ?>;
		background-attachment: <?php echo get_theme_mod('rads_bg_attachment'); ?>;
		background-size: cover;
		<?php } else { ?>
		background-color: <?php echo get_theme_mod('rads_bg_color', '#725827'); ?>;
		<?php } ?>
	}
	#resturants_advance_search_box {
		<?php if (get_theme_mod('radfs_bg_image') != null) { ?>
		background-image: <?php echo ' url("' . get_theme_mod('radfs_bg_image') . '")'; ?>;
		background-color: <?php echo get_theme_mod('radfs_bg_color', '#f89d1a'); ?>;
		background-position: <?php if (frozr_mobile()) { echo'50% 50%'; } else { echo get_theme_mod('radfs_bg_position'); } ?>;
		background-repeat: <?php echo get_theme_mod('radfs_bg_repeat', 'no-repeat'); ?>;
		background-attachment: <?php echo get_theme_mod('radfs_bg_attachment'); ?>;
		background-size: cover;
		<?php } else { ?>
		background-color: <?php echo get_theme_mod('radfs_bg_color', '#f89d1a'); ?>;
		<?php } ?>
	}
	h2.alei_h2, #resturants_advance_search_box .control_edit {
		color: <?php echo get_theme_mod('rest_adv_filters_ty_color', '#4f3d1b'); ?>;
	}
	.ingsearchimg {
		background-image: url(<?php echo $ingredient_filter_image; ?>);
	}
	.spysearchimg {
		background-image: url(<?php echo $search_type_filter_image; ?>);
	}
	.catsearchimg {
		background-image: url(<?php echo $category_filter_image; ?>);
	}
	.cusearchimg {
		background-image: url(<?php echo $cusine_filter_image; ?>);
	}
	.adlocsearchimg {
		background-image: url(<?php echo $addressloc_filter_image; ?>);
	}
	.restsearchimg {
		background-image: url(<?php echo $rests_filter_image; ?>);
	}
	.restdimg {
		background-image: url(<?php echo $restdimg_filter_image; ?>);
	}
	.typeimg {
		background-image: url(<?php echo $typeimg_filter_image; ?>);
	}
	.recoimg {
		background-image: url(<?php echo $recoimg_filter_image; ?>);
	}
	.popuimg {
		background-image: url(<?php echo $popuimg_filter_image; ?>);
	}
	.rest_list_btn.popu {
		background-color:<?php echo get_theme_mod('frozr_popu_color', 'rgba(251, 23, 23, 0.7)'); ?>;
		color:<?php echo get_theme_mod('frozr_popu_txt_color', '#fff'); ?>;
		display: block;
		width: 70%;
		margin: 16px auto 0;
	}
	.rest_list_btn.reco {
		background-color:<?php echo get_theme_mod('frozr_reco_color', 'rgba(65, 99, 243, 0.6)'); ?>;
		color:<?php echo get_theme_mod('frozr_reco_txt_color', '#fff'); ?>;
		display: block;
		width: 70%;
		margin: 16px auto 0;
	}
	.rest_list_btn.vegb {
		background-color:<?php echo get_theme_mod('frozr_vegb_color', 'rgba(44, 206, 44, 0.72)'); ?>;
		color:<?php echo get_theme_mod('frozr_vegb_txt_color', '#fff'); ?>;
		display: block;
	}
	.rest_list_btn.nonvegb {
		background-color:<?php echo get_theme_mod('frozr_nonvegb_color', 'rgba(245, 81, 81, 0.8)'); ?>;
		color:<?php echo get_theme_mod('frozr_nonvegb_txt_color', '#fff'); ?>;
		display: block;
	}
	.rest_list_btn.sefb {
		background-color:<?php echo get_theme_mod('frozr_sefb_color', 'rgba(108, 108, 246, 0.88)'); ?>;
		color:<?php echo get_theme_mod('frozr_sefb_txt_color', '#fff'); ?>;
		display: block;
	}
	a.rsb_loc_link {
		background-color:<?php echo get_theme_mod('frozr_rsb_loc_link_color', 'rgba(108, 108, 246, 0.88)'); ?>;
		color:<?php echo get_theme_mod('frozr_rsb_loc_link_txt_color', '#fff'); ?>;
		display: block;
		width: 20%;
		margin: 16px auto 0;
	}
	<?php if (is_super_admin()) { echo '.search_adv_wrapper:hover,.rsb-boxes:hover{cursor:move;}';} ?>
	<?php if ($theme_layout == 'right') { ?>
	.dish_veg_sp > span:first-child {border-left: 1px dotted;}
	ul.list-inline.rest-info { left:0; }
	.tablist-left, .frozr-edit-sidebar, .rest-title > div {float: right;}
	.send_rest_invit_link, .new_product_pl {float: left;}
	.row-actions, .product_listing_status_filter,.order-statuses-filter,.reports_nav,.reviews-listing-filter,.withdraw-statuses-filter, .sellers-filter {text-align: right;}
	.frozr-remove-banner-image {left: 30px;}
	.settings-store-name {right: 100%;}
	.user-setting-header .pro_img {right:30px;}
	.dash_title, .dash_content { text-align: right;}
	.rsb-boxes:nth-child(n+2), .adv_loc_src_checkbox:nth-child(n+2){border-right: 1px solid #eee;}
	<?php } else { ?> 
	.dish_veg_sp > span:first-child {border-right: 1px dotted;}
	.send_rest_invit_link, .new_product_pl {float: right;}
	ul.list-inline.rest-info { right:0; }
	.tablist-left, .frozr-edit-sidebar, .rest-title > div {float: left;}
	.frozr-remove-banner-image {right: 30px;}
	.settings-store-name {left: 100%;}
	.row-actions, .product_listing_status_filter,.order-statuses-filter,.reports_nav,.reviews-listing-filter,.withdraw-statuses-filter, .sellers-filter {text-align: left;}
	.user-setting-header .pro_img {left:30px;}
	.dash_title, .dash_content { text-align: left;}
	.rsb-boxes:nth-child(n+2), .adv_loc_src_checkbox:nth-child(n+2) {border-left: 1px solid #eee;}
	<?php } ?>

	</style>
<?php
}
// Output custom CSS to live site
add_action( 'wp_head' , 'frozr_lazyeater_output_css' );