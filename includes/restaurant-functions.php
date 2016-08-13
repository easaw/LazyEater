<?php
/**
 * All Restaurants Settings Functions
 *
 * @package FrozrCoreLibrary
 * @subpackage Frozrmarketlibrary
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Get restaurant info based on seller ID
 *
 * @param int $seller_id
 * @return array
 */
function frozr_get_store_info( $seller_id ) {
    $info = get_user_meta( $seller_id, 'frozr_profile_settings', true );
    $info = is_array( $info ) ? $info : array();

    $defaults = apply_filters('frozr_get_default_store_info',array(
        'store_name' => '',
        'socialfb' => '',
        'socialtwitter' => '',
        'socialgplus' => '',
        'sociallinkedin' => '',
        'socialyoutube' => '',
        'payment' => apply_filters('frozr_default_accepted_withdraw_payment',array( 'paypal' => array( 'email' ), 'bank' => array() )),
        'phone' => '',
        'shipping_fee' => '',
        'deliveryby' => 'order',
        'shipping_pro_adtl_cost' => '',
		'accpet_order_type' => frozr_default_accepted_orders_types(),
		'accpet_order_type_cl' => apply_filters('frozr_default_accepted_orders_types_while_closed',array('none')),
		'allow_ofline_orders' => 'yes',
		'show_rest_tables' => 'no',
 		'processing_time' => '',
		'show_email' => 'off',
        'banner' => 0,
		'gravatar' => 0,
		'min_order_amt' => 0
    ));

    $info = wp_parse_args( $info, $defaults );

    return $info;
}
/**
 * Get All Sellers
 *
 * @return array
 */
function frozr_get_all_sellers() {
	$args = apply_filters( 'frozr_fee_get_sellers_list_query', array(
		'role' => 'seller',
		'orderby' => 'registered',
		'order' => 'ASC',
		'meta_query' => array(
			array(
				'key' => 'frozr_enable_selling',
				'value' => 'yes',
				'compare' => '='
			)
		)));

	$user_query = new WP_User_Query( $args );
	$get_sellers = $user_query->get_results();
	$sellers = array();
	
	foreach($get_sellers as $seller) {
		$restaurant_name = frozr_get_store_info($seller->ID);
		$sellers[$seller->ID] = $restaurant_name['store_name'];
	}
	return $sellers;
}
/**
 * Get All customers
 *
 * @return array
 */
function frozr_get_all_customers() {
	$args = apply_filters( 'frozr_fee_get_customers_list_query', array(
		'role' => 'customer',
		'orderby' => 'registered',
		'order' => 'ASC',
		));

	$user_query = new WP_User_Query( $args );
	$get_customers = $user_query->get_results();
	$customers = array();
	
	foreach($get_customers as $customer) {
		$customers[$customer->ID] = $customer->display_name;
	}
	return $customers;
}
/**
 * Get restaurant page url of a seller
 *
 * @param int $user_id
 * @return string
 */
function frozr_get_store_url( $user_id ) {
    $userdata = get_userdata( $user_id );

    return sprintf( '%s/%s/', home_url( '/restaurant' ), $userdata->user_nicename );
}
/**
 * Restaurants cant see others media uploads.
 */
function frozr_media_uploader_filter( $args ) {
    // bail out for admin and editor
    if ( current_user_can( 'delete_pages' ) ) {
        return $args;
    }

    if ( current_user_can( 'frozer' ) ) {
        $args['author'] = get_current_user_id();

        return $args;
    }

    return $args;
}
add_filter( 'ajax_query_attachments_args', 'frozr_media_uploader_filter' );
/**
 * Get seller rating in a readable rating format
 *
 * @param int $seller_id
 * @return void
 */
function frozr_get_readable_seller_rating( $seller_id ) {

	$seller_ratings = get_user_meta($seller_id, 'rest_rating', true);
	if (empty($seller_ratings)) {
		
		return __('No Ratings Yet!','frozr');
	}
	
	$rc = array();
	$rv = array();
	foreach($seller_ratings as $n => $v) {
		$rc[] = $n;
		$rv[] = $v;
	}
	$nx = count($rc);
	$xx = (array_sum($rv) * 100) / ($nx * 5);
	
	return apply_filters('frozr_readable_seller_rating','%' . sprintf( __( '%1$s Based on ', 'frozr' ) . _n('%2$s Rating', '%2$s Ratings', $nx, 'frozr' ), $xx, $nx), $seller_id);
}
//restaurant rating form
function frozr_store_rating_form( $seller, $orderid ) {
$store_info = frozr_get_store_info( $seller );
?>
<div class="rest_rating_form_wrapper">
	<?php if ( ! is_user_logged_in() ) { ?>
	<form class="rest_rating_login" data-ajax="false" method="post" class="login">

		<h2><?php _e('Login to make your review!','frozr'); ?></h2>
		<p class="form-row form-row-wide">
			<label for="rat_username"><?php _e( 'Username or email address', 'frozr' ); ?> <span class="required">*</span></label>
			<input type="text" class="input-text" name="rat_username" id="rat_username" value="<?php if ( ! empty( $_POST['rat_username'] ) ) echo esc_attr( $_POST['rat_username'] ); ?>" />
		</p>
		<p class="form-row form-row-wide">
			<label for="rat_password"><?php _e( 'Password', 'frozr' ); ?> <span class="required">*</span></label>
			<input class="input-text" type="password" name="rat_password" id="rat_password" />
		</p>
		<p class="form-row">
			<input type="submit" class="button" name="rat_login" value="<?php _e( 'Login', 'frozr' ); ?>"  />
		</p>			
		<?php do_action( 'frozr_after_store_rating_form' ); ?>
	</form>
	<?php } ?>
	
	<form class="rest_rating_form" <?php if ( ! is_user_logged_in() ) { echo 'style="display:none;"'; } ?> method="post">
		<h2><?php _e('Rate','frozr'); ?>&nbsp;<?php echo $store_info['store_name']; ?></h2>
		<select name="restrating" id="restrating" required>
			<?php for ( $rating = 0; $rating <= 5; $rating++ ) {
				echo sprintf( '<option value="%1$s">%1$s</option>', $rating );
			} ?>
		</select>
		<input class="rest_rating_submit" type="submit" data-restid="<?php echo $seller; ?>" data-orderid="<?php echo $orderid; ?>" name="rest_rating_submit" value="<?php _e( 'Submit', 'frozr' ); ?>" >
	</form>	
</div>
<?php
}
/**
 * Get seller listing
 *
 * @param int $number
 * @param int $offset
 * @return array
 */
function frozr_get_sellers( $number = 10, $offset = 0, $type = '', $termone = 'all', $termtwo = 'all' ) {
	
	$utresult = '';
	if ($type == 'restaurant_src') {
		if ($termone == 'top') {
			$args = apply_filters( 'frozr_seller_list_query', array(
				'role'		=> 'seller',
				'number'	=> $number,
				'offset'	=> $offset,
				'meta_key'	=> '_restaurant_balance',
				'order'		=> 'DESC',
				'orderby'	=> 'meta_value_num',
			));
		} elseif($termone == 'recommended') {
			$seloption = get_option( 'fro_settings' );
			$args = apply_filters( 'frozr_seller_list_query', array(
				'role' => 'seller',
				'include' => $seloption['fro_reco_sellers'],
				'orderby' => 'registered',
				'order' => 'ASC',
				'meta_query' => array(
					array(
						'key' => 'frozr_enable_selling',
						'value' => 'yes',
						'compare' => '='
					)
				)
			));
		} elseif($termone == 'veg' || $termone == 'nonveg' || $termone == 'sea-food') {
			$args = apply_filters( 'frozr_seller_list_query', array(
				'role' => 'seller',
				'number' => $number,
				'offset' => $offset,
				'orderby' => 'registered',
				'order' => 'ASC',
				'meta_query' => array(
					array(
						'key' => 'frozr_enable_selling',
						'value' => 'yes',
						'compare' => '='
					),
					array(
						'key' => 'frozr_food_type',
						'value' => $termone,
						'compare' => 'LIKE'
					)
				)
			));
		}
	} elseif ($type == 'location_src') {
		$utermone = get_term_by( 'slug', esc_attr($termone), 'location');
		$utresult = get_objects_in_term( (int) $utermone->term_id, 'location');

		if (!empty ($utresult)) {
		$args = apply_filters( 'frozr_seller_list_query', array(
			'role' => 'seller',
			'number' => $number,
			'offset' => $offset,
			'include' => array_unique($utresult),
			'orderby' => 'registered',
			'order' => 'ASC',
			'meta_query' => array(
				array(
					'key' => 'frozr_enable_selling',
					'value' => 'yes',
					'compare' => '='
				)
			)
		));
		}	
	} elseif ($type == 'cusine_src') {
		if ($termtwo !== 'all' && $termone == 'all') {
			$utermtwo = get_term_by( 'slug', esc_attr($termtwo), 'cuisine');
			$utresult = get_objects_in_term( (int) $utermtwo->term_id, 'cuisine');
		} elseif ($termone !== 'all' && $termtwo !== 'all') {
			$utermtwo = get_term_by( 'slug', esc_attr($termtwo), 'cuisine');
			$utresulttwo = get_objects_in_term( (int) $utermtwo->term_id, 'cuisine');
			$utresultone = array();
			foreach ($utresulttwo as $usr) {
				$locs = wp_get_object_terms( $usr, 'location', array('fields' => 'slugs'));
				if (in_array($termone, $locs, true)) {
					$utresultone[] = $usr;
				}
			}
			$utresult = $utresultone;
		}
		if (!empty ($utresult)) {
		$args = apply_filters( 'frozr_seller_list_query', array(
			'role' => 'seller',
			'number' => $number,
			'offset' => $offset,
			'include' => array_unique($utresult),
			'orderby' => 'registered',
			'order' => 'ASC',
			'meta_query' => array(
				array(
					'key' => 'frozr_enable_selling',
					'value' => 'yes',
					'compare' => '='
				)
			)
		));
		}
	} elseif ($type == 'address_src') {
		$utermone = get_term_by( 'slug', esc_attr($termone), 'restaurant_addresses');
		$utresult = get_objects_in_term( (int) $utermone->term_id, 'restaurant_addresses');

		if (!empty ($utresult)) {
		$args = apply_filters( 'frozr_seller_list_query', array(
			'role' => 'seller',
			'number' => $number,
			'offset' => $offset,
			'include' => array_unique($utresult),
			'orderby' => 'registered',
			'order' => 'ASC',
			'meta_query' => array(
				array(
					'key' => 'frozr_enable_selling',
					'value' => 'yes',
					'compare' => '='
				)
			)
		));
		}	
	} else {
		return false;
	}

	$user_query = new WP_User_Query( $args );
	$sellers = $user_query->get_results();

	return array( 'users' => $sellers, 'count' => $user_query->total_users );
}
/**
 * Stores Listing
 */
function frozr_stores_listing($tit, $cont, $type = '', $trmone = 'all', $trmtwo = 'all' ) {
	
	$paged = max( 1, get_query_var( 'paged' ) );
    $limit = 12;
    $offset = ( $paged - 1 ) * $limit;
    $sellers = frozr_get_sellers( $limit, $offset, $type, $trmone, $trmtwo );
	$show_frozr_sidebar = get_theme_mod('froz_icon_sidebar', true);
	
	// User Loop
	if ( $sellers['users'] ) { ?>
	
	<div class="dokkan_page_title_content <?php echo $trmtwo; ?>">
		<span class="dokkan_page_title"><?php echo apply_filters('frozr_stores_listing_page_title',$tit); ?></span>
		<span class="dokkan_page_content"><?php echo apply_filters('frozr_stores_listing_page_title_content',$cont); ?></span>
		<?php do_action('frozr_after_stores_listing_page_title',$tit, $cont, $type, $trmone, $trmtwo); ?>
	</div>
	<div id="products-temp-wrapper" data-ajax="false">
		<div class="shop-sidebar <?php if ($show_frozr_sidebar == false) { echo 'no_froz_side'; } ?>">
			<?php get_sidebar("lazy-inset"); ?>
			<?php do_action('frozr_after_stores_listing_page_sidebar',$tit, $cont, $type, $trmone, $trmtwo); ?>
		</div>
		<div class="p_wrap">
			<?php if ($trmone == 'all') { frozr_location_not_set(); } ?>
			<div id="restaurant_lists_content">
				<?php foreach ( $sellers['users'] as $user ) {
					frozr_restaurants_inloops($user->ID);
				}
				
				$user_count = $sellers['count'];
				$num_of_pages = ceil( $user_count / $limit );

				if ( $num_of_pages > 1 ) {
					echo '<div class="pagination-container clearfix">';
					$page_links = paginate_links( array(
						'current' => $paged,
						'total' => $num_of_pages,
						'base' => str_replace( $post->ID, '%#%', esc_url( get_pagenum_link( $post->ID ) ) ),
						'type' => 'array',
						'prev_text' => __( '&larr; Previous', 'frozr' ),
						'next_text' => __( 'Next &rarr;', 'frozr' ),
					));

					echo "<ul class='pagination'>\n\t<li>";
					echo join("</li>\n\t<li>", $page_links);
					echo "</li>\n</ul>\n";
					echo '</div>';
				} ?>
			</div><?php
				do_action('frozr_after_stores_listing_page_content',$tit, $cont, $type, $trmone, $trmtwo);
			} else {
				echo 'No Restaurants found.';
			}
				do_action('frozr_after_stores_listing_page',$tit, $cont, $type, $trmone, $trmtwo); ?>
		</div>
	</div>
		<?php if ($show_frozr_sidebar == true) { ?>
		<script type="text/javascript">
				jQuery("body").on("click", function(e) {
				var target = jQuery( e.target );
					if (target.is(".widget-icon")) {
						var theWidget = target.parent(),
							prevallWidget = theWidget.prevAll(".active_woo_widget"),
							nextallWidget = theWidget.nextAll(".active_woo_widget");

						if (theWidget.hasClass("active_woo_widget")) {

							theWidget.removeClass("active_woo_widget");

						} else {			
							prevallWidget.removeClass("active_woo_widget");
							nextallWidget.removeClass("active_woo_widget");
							theWidget.addClass("active_woo_widget");
						}
					} 
					if (!target.is(".shop-sidebar *")) {
						jQuery(".woo-widget-wrapper").removeClass("active_woo_widget");
					}
				});
		</script>
		<?php }
}
add_action('frozr_frozr_list_restaurants','frozr_stores_listing',10,5);
//Restaurants in loops
function frozr_restaurants_inloops($u) {
	$profile_info = frozr_get_store_info( $u );?>

	<article class="restaurant_wrap">
		<a class="new_rest_name" style="background-image: url('<?php echo (frozr_avatar_url($u)) ? frozr_avatar_url($u) : plugins_url( 'assets/imgs/q-rest-bg.png', lAZY_EATER_FILE ) ; ?>');" href="<?php echo frozr_get_store_url($u); ?>" title="<?php _e('Visit Restaurant','frozr'); ?>"><span><?php echo $profile_info['store_name']; ?></span></a>
	<?php if(frozr_get_restaurant_address($u)) { ?>
		<span><i class="fs-icon-map-marker"></i>&nbsp;<?php echo frozr_get_restaurant_address($u); ?></span>
	<?php } ?>
		<span><i class="fs-icon-cutlery"></i>&nbsp;<?php
		$restypes = wp_get_object_terms( $u, 'cuisine', array("fields" => "names") );
		if (is_array($restypes)) {
			foreach ( $restypes as $restype ) {
			$link = get_term_link( $restype, 'cuisine' ); ?>
			<a href="<?php echo esc_url( $link ); ?>" title="<?php echo __('See Restaurants that also serve ','frozr') . $restype; ?>"><?php echo $restype; ?></a>
		<?php } } ?></span>
		<span><i class="fs-icon-shopping-basket"></i><?php _e(' Min. Order: ','frozr'); if($profile_info['min_order_amt'] == 0) { _e('Any.','frozr'); } elseif(!empty($profile_info['min_order_amt'])) { echo wc_price($profile_info['min_order_amt']); } else {echo "N/A";}; ?></span>
		<span><i class="fs-icon-money"></i><?php _e(' Delivery: ','frozr'); if($profile_info['shipping_fee'] == 0) { _e('Free Delivery.','frozr'); } elseif(!empty($profile_info['shipping_fee'])) { echo wc_price($profile_info['shipping_fee']); } else {echo "N/A";}; ?></span>
		<span class="rest-<?php echo (frozr_is_rest_open($u) == false) ? 'Closed' : 'Open'; ?>"><i class="fs-icon-institution"></i>&nbsp;<?php echo frozr_rest_status($u); ?></span>
		<?php do_action('frozr_after_restaurant_inloops_body',$u); ?>
	</article>
<?php }

//get_restaurant addresses
function frozr_get_restaurant_address($xn) {

	//get restaurant addresses
	$getallads= get_terms( 'restaurant_addresses', 'fields=names&hide_empty=0' );
	$restads = wp_get_object_terms( $xn, 'restaurant_addresses', array("fields" => "names") );
	$resad_slug = array();
	if (is_array($restads)) {
		foreach ( $restads as $restad ) {
			$resad_slug[] = $restad;
		}
		$restaddresses = join( ", ", $resad_slug );
	} elseif ( ! empty( $getallads ) && ! is_wp_error( $getallads )) {
		$restaddresses = $restads;
	}
	return apply_filters('frozr_get_restaurant_address',$restaddresses,$xn);
}

//Restaurants opening/closing time
function frozr_restaurants_open_close($u, $lyt = true) {

	$rund = get_user_meta( $u, '_rest_unavds',true );
	$runds = array();
	if (!empty($rund)) {
		foreach ($rund as $rn) {
			$runds[] = date('md', strtotime($rn));
		}
	}	
	$restime = get_user_meta( $u, 'rest_open_close_time',true );

	$tm = current_time('D');
	$nw = current_time('H:i');
	
	$rest_open = isset($restime[$tm]['restop']) ? $restime[$tm]['restop'] : '';
	$rest_open_2 = isset($restime[date('D', strtotime('+1 day'))]['restop']) ? $restime[date('D', strtotime('+1 day'))]['restop'] : '';
	$rest_open_3 = isset($restime[date('D', strtotime('+2 days'))]['restop']) ? $restime[date('D', strtotime('+2 days'))]['restop'] : '';
	$rest_open_4 = isset($restime[date('D', strtotime('+3 days'))]['restop']) ? $restime[date('D', strtotime('+3 days'))]['restop'] : '';
	$rest_open_5 = isset($restime[date('D', strtotime('+4 days'))]['restop']) ? $restime[date('D', strtotime('+4 days'))]['restop'] : '';
	$rest_open_6 = isset($restime[date('D', strtotime('+5 days'))]['restop']) ? $restime[date('D', strtotime('+5 days'))]['restop'] : '';
	$rest_open_7 = isset($restime[date('D', strtotime('+6 days'))]['restop']) ? $restime[date('D', strtotime('+6 days'))]['restop'] : '';
	$rest_shifts = isset($restime[$tm]['restshifts']) ? $restime[$tm]['restshifts'] : '';
	$rest_opening_one = isset($restime[$tm]['open_one']) ? $restime[$tm]['open_one'] : '';
	$rest_closing_one = isset($restime[$tm]['close_one']) ? $restime[$tm]['close_one'] : '';
	$rest_opening_two = isset($restime[$tm]['open_two']) ? $restime[$tm]['open_two'] : '';
	$rest_closing_two = isset($restime[$tm]['close_two']) ? $restime[$tm]['close_two'] : '';
	$txt_open_24 = __("Open 24 Hours Today", "frozr");
	$txt_open = __("Open till ", "frozr");
	if ($lyt == false) {
		$txt_opens_at = __(" at ", "frozr");
		$txt_opens_on = __(" on ", "frozr");
		$txt_opens_tmro = __(" tomorrow ", "frozr");
		$txt_close_tdy = $txt_opens_tmro;
		$txt_close = __(" another day ", "frozr");
		$txt_at = __(" at ", "frozr");
	} else {
		$txt_opens_at = __("Opens at ", "frozr");
		$txt_opens_on = __("Opens on ", "frozr");
		$txt_opens_tmro = __("Opens Tomorrow ", "frozr");
		$txt_close_tdy = __("Closed Today", "frozr");
		$txt_close = __("Closed", "frozr");
		$txt_at = __(" at ", "frozr");
	}
	
	if (!empty($runds) && in_array(current_time('md'),$runds, true) ) {
		$rsts = $txt_close_tdy;
		$rstss = false;
	} elseif ($rest_open) {
		if (strtotime($rest_opening_one) == strtotime($rest_closing_one)) {
			$rsts = $txt_open_24;
			$rstss = true;
		} elseif (strtotime($nw) > strtotime($rest_opening_one) && strtotime($nw) < strtotime($rest_closing_one)) {
			$rsts = $txt_open . date('h:i a', strtotime($rest_closing_one));
			$rstss = true;
		} elseif ($rest_shifts && strtotime($nw) > strtotime($rest_opening_two) && strtotime($nw) < strtotime($rest_closing_two)) {
			$rsts = $txt_open . date('h:i a', strtotime($rest_closing_two));
			$rstss = true;
		} elseif (strtotime($nw) < strtotime($rest_opening_one)) {
			$rsts = $txt_opens_at . date('h:i a', strtotime($rest_opening_one));
			$rstss = false;
		} elseif ($rest_shifts && strtotime($nw) > strtotime($rest_opening_one) && strtotime($nw) < strtotime($rest_opening_two) ) {
			$rsts = $txt_opens_at . date('h:i a', strtotime($rest_opening_two));
			$rstss = false;
		} else {
			if ($rest_open_2) {
				$rsts = $txt_opens_tmro;
				$rstss = false;
			} elseif ($rest_open_3) {
				$rsts = $txt_opens_on . date('l', strtotime('+2 days')) . $txt_at . date('h:i a', strtotime($restime[date('D', strtotime('+2 days'))]['open_one']));
				$rstss = false;
			} elseif ($rest_open_4) {
				$rsts = $txt_opens_on . date('l', strtotime('+3 days')) . $txt_at . date('h:i a', strtotime($restime[date('D', strtotime('+3 days'))]['open_one']));
				$rstss = false;
			} elseif ($rest_open_5) {
				$rsts = $txt_opens_on . date('l', strtotime('+4 days')) . $txt_at . date('h:i a', strtotime($restime[date('D', strtotime('+4 days'))]['open_one']));
				$rstss = false;
			} elseif ($rest_open_6) {
				$rsts = $txt_opens_on . date('l', strtotime('+5 days')) . $txt_at . date('h:i a', strtotime($restime[date('D', strtotime('+5 days'))]['open_one']));
				$rstss = false;
			} elseif ($rest_open_7) {
				$rsts = $txt_opens_on . date('l', strtotime('+6 days')) . $txt_at . date('h:i a', strtotime($restime[date('D', strtotime('+6 days'))]['open_one']));
				$rstss = false;
			} else {
				$rsts = $txt_close;
				$rstss = false;
			}
		}
	} else {
		if ($rest_open_2) {
			$rsts = $txt_opens_tmro;
			$rstss = false;
		} elseif ($rest_open_3) {
			$rsts = $txt_opens_on . date('l', strtotime('+2 days')) . $txt_at . date('h:i a', strtotime($restime[date('D', strtotime('+2 days'))]['open_one']));
			$rstss = false;
		} elseif ($rest_open_4) {
			$rsts = $txt_opens_on . date('l', strtotime('+3 days')) . $txt_at . date('h:i a', strtotime($restime[date('D', strtotime('+3 days'))]['open_one']));
			$rstss = false;
		} elseif ($rest_open_5) {
			$rsts = $txt_opens_on . date('l', strtotime('+4 days')) . $txt_at . date('h:i a', strtotime($restime[date('D', strtotime('+4 days'))]['open_one']));
			$rstss = false;
		} elseif ($rest_open_6) {
			$rsts = $txt_opens_on . date('l', strtotime('+5 days')) . $txt_at . date('h:i a', strtotime($restime[date('D', strtotime('+5 days'))]['open_one']));
			$rstss = false;
		} elseif ($rest_open_7) {
			$rsts = $txt_opens_on . date('l', strtotime('+6 days')) . $txt_at . date('h:i a', strtotime($restime[date('D', strtotime('+6 days'))]['open_one']));
			$rstss = false;
		} else {
			$rsts = $txt_close;
			$rstss = false;
		}
	}
	
	return apply_filters('frozr_restaurant_open_close_timing',array($rsts, $rstss), $u, $lyt);
}
//show restaurant is open
function frozr_rest_status($u, $lyt = true) {
	$nx = frozr_restaurants_open_close($u, $lyt);
	return $nx[0];
}
//check if restaurant is open
function frozr_is_rest_open($u) {
	$nx = frozr_restaurants_open_close($u);
	
	if ($nx[1] == true) {
		return true;
	} else {
		return false;
	}
}
//restaurant loop
function frozr_store_loop($userid) {

	global $product, $post;

	$categories = get_terms( array('taxonomy' => 'product_cat') );
	
	foreach ($categories as $category ) {

	$args = apply_filters('frozr_store_cats_loop_args',array (
		'author' => $userid,
		'posts_per_page' => -1,
		'post_type'		=> 'product',
		'product_cat'	=> $category->slug,
		'post_status'	=> 'publish'
	));

	// The Query
	$query = new WP_Query( $args );
    
	// The Loop
    if ( $query->have_posts() ) { ?>
	<div class="seller-items">
		<span class="resturant_cats_title"><?php echo $category->name;?></span>
		<table id="store-table-drink" class="ui-body-d ui-shadow table-stripe">
		<thead>
			<tr class="ui-bar-d">
				<th class="store_dish_title" data-priority="1"><?php _e('Item Title','frozr'); ?></th>
				<th class="store_dish_price" data-priority="3"><?php _e('Price','frozr'); ?></th>
				<?php do_action('frozr_after_store_items_table_header',$userid); ?>
			</tr>
     	</thead>
		<tbody>
	<?php
        while ( $query->have_posts() ) { $query->the_post();
		$product = wc_get_product( $post->ID );
		$rating_count = $product->get_rating_count();
		$large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'large');
		$dish_type = get_post_meta($post->ID, '_dish_veg', true);
		$dish_spicy = get_post_meta($post->ID, '_dish_spicy', true);
		$dish_fat = get_post_meta($post->ID, '_dish_fat', true);
		$dish_fat_rate = get_post_meta($post->ID, '_dish_fat_rate', true);
		if ($dish_type == 'nonveg') {
			$dish_type_text = __('Non-Veg','frozr');
		} else {		
			$dish_type_text = __('Vegetarian','frozr');
		}
		if($dish_spicy == 'yes') {
			$dish_spicy_text = __('Item is Spicy','frozr');
		} else {
			$dish_spicy_text = __('Item is not Spicy','frozr');		
		}
		?>
		    <tr>
				<td class="dish-title">
					<h6><a href="#item-<?php echo $post->ID; ?>" title="View <?php _e('Quick View','frozr'); ?>" data-transition="fade" data-rel="popup"><?php the_title(); ?></a></h6>
					<span class="product_detalis"><?php frozr_limit_info( apply_filters( 'frozr_product_popup_short_description', $post->post_excerpt),70); ?></span>
					<?php do_action('frozr_after_store_product_table_header',$userid,$query); ?>
					<div data-history="false" data-role="popup" id="item-<?php echo $post->ID; ?>" class="dish-info <?php if ( $product->is_on_sale() ) {echo "del_pe";} ?>" data-position-to="window">
						<a href="#" data-rel="back" class="ui-btn ui-corner-all ui-shadow ui-btn-a ui-icon-delete ui-btn-icon-notext ui-btn-right"><?php _e('Close','frozr'); ?></a>
						<div class="rest-cont-thumbnail" style="background: url('<?php echo $large_image_url[0]; ?>') no-repeat center center;background-size:cover;"><div class="dish_pop_thumb"><?php the_title(); ?></br><?php woocommerce_template_loop_price(); ?></div></div>
						<div class="dish_quick_info">
							<?php do_action('frozr_before_product_quick_info'); ?>
							<div class="dish_veg_sp">
								<span><span class="<?php echo ($dish_type) ? $dish_type : 'veg'; ?>"></span><?php echo $dish_type_text;?></span>
								<span><span class="dish-is-spicy"></span><?php echo $dish_spicy_text; ?></span>
							</div>
							<div class="dish_det"><?php the_title(); echo apply_filters( 'frozr_product_popup_description', $post->post_excerpt); ?>&nbsp;<a href="<?php the_permalink() ?>" title="View <?php the_title_attribute(); ?>"><?php _e('View full details.','frozr'); ?></a></div>
							<?php if($dish_fat == 'yes') {?>
							<div class="dish-fat"><i class="fs-icon-heartbeat"></i>&nbsp;<?php echo __('Fat','frozr') . ' &cong; ' . $dish_fat_rate . __(' Grams','frozr'); ?></div>
							<?php } ?>
							<?php if (get_the_term_list( $product->ID, 'ingredient' )) { echo '<div class="dish_ing_single"><i class="fs-icon-leaf"></i>&nbsp;';
							the_terms( $product->ID, 'ingredient', __('ingredients: ','frozr'), ', ' );
							echo '</div>'; } ?>
							<?php if ( $rating_count > 0 ) { ?>
							<div class="woocommerce">
								<i class="fs-icon-thumbs-up"></i>&nbsp;<span class="dish_pop_titls"><?php _e('Item Rating','frozr'); ?></span>
								<?php woocommerce_template_single_rating(); ?>
							</div>
							<?php } ?>
							<?php do_action('frozr_after_product_quick_info'); ?>
						</div>
						<div class="dish_pop_makeaorder">
							<button class="pop_make_order_btn"><i class="fs-icon-shopping-cart"></i>&nbsp;<span><?php _e('Make Order!','frozr'); ?></span></button>
							<div class="pop_make_order_wrapper">
								<?php frozr_add_tocart($post); ?>
							</div>
						</div>
					</div>
				</td>
				<td class="dish-price">
					<?php woocommerce_template_loop_price(); ?>
				</td>
				<?php do_action('frozr_after_store_product_table_body',$userid,$query); ?>
			</tr>
		<?php
        }
	?>
		</tbody>
		</table>
	</div>
	<?php
    }
	// Restore original Post Data
    wp_reset_postdata();
	}
}
// show shop coupons in restaurant page
function frozr_show_shop_coupons($u) {
    $args = apply_filters('frozr_show_shop_coupons_args',array(
        'post_type' => 'shop_coupon',
        'post_status' => array('publish'),
        'posts_per_page' => 4,
        'author' => $u,
        'meta_query' => array(
            array(
                'key' => 'show_cp_inshop',
                'value' => 'yes',
                'compare' => '='
            )
        )
    ),$u);

    $coupon_query = new WP_Query( $args );
    $all_coupons = $coupon_query->get_posts();

    if ( $all_coupons ) {
		
		foreach($coupon_query->posts as $key => $post) { ?>

			<span class="rest_cops"><?php echo esc_attr (get_post_meta( $post->ID, 'show_cp_inshop_txt', true )); ?></span>

		<?php }
	}
}
//get the time settings for the restaurant
function frozr_restaurant_timing( $xn ) {
	
	$current_user = get_current_user_id();
	$restime = get_user_meta( $current_user, 'rest_open_close_time',true );
	
	$rest_open = isset($restime[$xn]['restop']) ? $restime[$xn]['restop'] : '';
	$rest_shifts = isset($restime[$xn]['restshifts']) ? $restime[$xn]['restshifts'] : '';
	$rest_opening_one = isset($restime[$xn]['open_one']) ? $restime[$xn]['open_one'] : '';
	$rest_closing_one = isset($restime[$xn]['close_one']) ? $restime[$xn]['close_one'] : '';
	$rest_opening_two = isset($restime[$xn]['open_two']) ? $restime[$xn]['open_two'] : '';
	$rest_closing_two = isset($restime[$xn]['close_two']) ? $restime[$xn]['close_two'] : '';
	
	return apply_filters('frozr_restaurant_timing',array ($rest_open, $rest_shifts, $rest_opening_one, $rest_closing_one, $rest_opening_two, $rest_closing_two), $xn);

}
// Restaurant profile settings output
function frozr_output_restaurant_settings() {
        
	$current_user = get_current_user_id();
	$profile_info = frozr_get_store_info( $current_user );

	$banner = isset( $profile_info['banner'] ) ? absint( $profile_info['banner'] ) : 0;
	$storename = isset( $profile_info['store_name'] ) ? esc_attr( $profile_info['store_name'] ) : '';
	$gravatar = isset( $profile_info['gravatar'] ) ? absint( $profile_info['gravatar'] ) : 0;

	$fb = isset( $profile_info['socialfb']) ? esc_url( $profile_info['socialfb']) : '';
	$twitter = isset( $profile_info['socialtwitter']) ? esc_url( $profile_info['socialtwitter']) : '';
	$gplus = isset( $profile_info['socialgplus']) ? esc_url ( $profile_info['socialgplus']) : '';
	$linkedin = isset( $profile_info['sociallinkedin']) ? esc_url( $profile_info['sociallinkedin']) : '';
	$youtube = isset( $profile_info['socialyoutube']) ? esc_url( $profile_info['socialyoutube']) : '';

	$phone = isset( $profile_info['phone'] ) ? esc_attr( $profile_info['phone'] ) : '';
	$show_email = isset( $profile_info['show_email'] ) ? esc_attr( $profile_info['show_email'] ) : 'no';
	$allow_email = isset( $profile_info['allow_email'] ) ? esc_attr( $profile_info['allow_email'] ) : 'yes';
	$allow_ofline_orders = isset( $profile_info['allow_ofline_orders'] ) ? esc_attr( $profile_info['allow_ofline_orders'] ) : 'yes';
	$show_rest_tables = isset( $profile_info['show_rest_tables'] ) ? esc_attr( $profile_info['show_rest_tables'] ) : 'no';
	//shipping
	$shipping_fee = isset( $profile_info['shipping_fee'] ) ? $profile_info['shipping_fee'] : '';
	$deliveryby = isset( $profile_info['deliveryby'] ) ? $profile_info['deliveryby'] : 'order';
	$shipping_pro_adtl_cost = isset( $profile_info['shipping_pro_adtl_cost'] ) ? $profile_info['shipping_pro_adtl_cost'] : '';
	$processing_time = isset( $profile_info['processing_time'] ) ? $profile_info['processing_time'] : '';
	$min_order_amt = isset( $profile_info['min_order_amt'] ) ? $profile_info['min_order_amt'] : '';
	
	$orders_accept = ! empty ($profile_info['accpet_order_type']) ? $profile_info['accpet_order_type'] : frozr_default_accepted_orders_types();
	$orders_accept_cl = ! empty ($profile_info['accpet_order_type_cl']) ? $profile_info['accpet_order_type_cl'] : apply_filters('frozr_default_accepted_orders_types_while_closed',array('none'));
	
	$rest_food_type = '' != get_user_meta($current_user, 'frozr_food_type', true) ? get_user_meta($current_user, 'frozr_food_type', true) : array('veg','nonveg','sea-food');
	
	//get all addresses
	$getallads= get_terms( 'restaurant_addresses', 'fields=names&hide_empty=0' );
	$addresses_slug = array();
	if ( ! empty( $getallads ) && ! is_wp_error( $getallads ) ){
		 foreach ( $getallads as $term ) {
		   $addresses_slug[] = $term;
	}
	$alladdresses = "'".join( "',' ", $addresses_slug )."'";
	}

	//get restaurant type
	$getalltyps= get_terms( 'cuisine', 'fields=names&hide_empty=0' );
	$restypes = wp_get_object_terms( $current_user, 'cuisine', array("fields" => "names") );
	$restype_slug = array();
	if (is_array($restypes)) {
	foreach ( $restypes as $restype ) {
		$restype_slug[] = $restype;
	}
	$grestypes = join( ", ", $restype_slug );
	} elseif ( ! empty( $getalltyps ) && ! is_wp_error( $getalltyps )) {
	$grestypes = $restypes;
	}
	//get all types
	$rtys_slug = array();
	if ( ! empty( $getalltyps ) && ! is_wp_error( $getalltyps ) ){
		 foreach ( $getalltyps as $term ) {
		   $rtys_slug[] = $term;
	}
	$allgrestypes = "'".join( "',' ", $rtys_slug )."'";
	}

	//get user locations
	$getallocs = get_terms( 'location', 'fields=names&hide_empty=0' );
	$locations = wp_get_object_terms( $current_user, 'location', array("fields" => "names") );
	$locations_slug = array();
	if (is_array($locations)) {
	foreach ( $locations as $location ) {
		$locations_slug[] = $location;
	}
	$locs = join( ", ", $locations_slug );
	} elseif ( ! empty( $getallocs ) && ! is_wp_error( $getallocs )) {
	$locs = $locations;
	}
	//get all locations
	$locs_slug = array();
	if ( ! empty( $getallocs ) && ! is_wp_error( $getallocs ) ){
		 foreach ( $getallocs as $term ) {
		   $locs_slug[] = $term;
	}
	$allocs = "'".join( "',' ", $locs_slug )."'";
	}
	?>
	<form method="post" id="settings-form"  action="" class="user-settings-form">

		<div class="user-setting-header">
			<div class="frozr-banner">
				<div class="image-wrap<?php echo $banner ? '' : ' frozr-hide'; ?>">
					<?php $banner_url = $banner ? wp_get_attachment_url( $banner ) : ''; ?>
					<input type="hidden" name="frozr_banner" class="frozr-banner-field" value="<?php echo $banner; ?>" >
					<div class="frozr-banner-img" style="background-image: url(<?php echo esc_url( $banner_url ); ?>);"></div>
					<a class="close frozr-remove-banner-image"><i class="fs-icon-camera"></i><?php _e('Change banner','frozr'); ?></a>
				</div>

				<div class="button-area<?php echo $banner ? ' frozr-hide' : ''; ?>">
					<i class="fs-icon-cloud-upload"></i>
					<a href="#" class="frozr-banner-drag btn btn-info"><?php _e( 'Upload banner', 'frozr' ); ?></a>
					<p class="help-block"><?php _e( '(Upload a banner for your store. Banner size is (825x300) pixel. )', 'frozr' ); ?></p>
				</div>
			</div> <!-- .frozr-banner -->

			<div class="pro_img">
				<div class="frozr-gravatar">
					<div class="gravatar-wrap<?php echo $gravatar ? '' : ' frozr-hide'; ?>">
						<?php $gravatar_url = $gravatar ? wp_get_attachment_url( $gravatar ) : ''; ?>
						<input type="hidden" class="frozr-gravatar-field" value="<?php echo $gravatar; ?>" name="frozr_gravatar">
						<div class="frozr-gravatar-img" style="background-image: url(<?php echo esc_url( $gravatar_url ); ?>);"></div>
						<a class="close frozr-remove-gravatar-image"><i class="fs-icon-camera"></i><?php _e('Change Photo','frozr'); ?></a>
					</div>
					<div class="gravatar-button-area<?php echo $gravatar ? ' frozr-hide' : ''; ?>">
						<i class="fs-icon-cloud-upload"></i>
						<a href="#" class="frozr-gravatar-drag btn btn-info"><?php _e( 'Upload Photo', 'frozr' ); ?></a>
					</div>
				</div>
				<a class="settings-store-name" href="<?php echo frozr_get_store_url( $current_user ); ?>"><?php echo $storename; ?></a>
			</div>
		</div>
		<div data-role="tabs">
			<ul data-role="listview" data-inset="true" class="tablist-left">
			  <?php do_action('frozr_before_rest_set_tabs'); ?>
			  <li><a href="#usr_gen_opts" class="ui-icon-gear" data-ajax="false"><?php _e('General Settings','frozr'); ?></a></li>
			  <li><a href="#usr_delivery_opts" class="ui-icon-gear" data-ajax="false"><?php _e( 'Delivery Settings', 'frozr' ); ?></a></li>
			  <li><a href="#usr_orders_opts" class="ui-icon-gear" data-ajax="false"><?php _e( 'Orders Settings', 'frozr' ); ?></a></li>
			  <li><a href="#usr_social_profile" class="ui-icon-user" data-ajax="false"><?php _e( 'Social Profile', 'frozr' ); ?></a></li>
			  <li><a href="#usr_opcl_opts" class="ui-icon-clock" data-ajax="false"><?php _e( 'Opening/Closing timings', 'frozr' ); ?></a></li>
			  <li><a href="#usr_with_opts" class="ui-icon-alert" data-ajax="false"><?php _e( 'Withdraw Method', 'frozr' ); ?></a></li>
			  <li><a href="#usr_tables_opts" class="ui-icon-gear" data-ajax="false"><?php _e( 'Restaurant Tables', 'frozr' ); ?></a></li>
			  <?php do_action('frozr_after_rest_set_tabs'); ?>
			</ul>
			<?php do_action('frozr_before_user_front_options'); ?>
			<div id="usr_gen_opts" class="group-opts">
				<span class="form-group-label"><?php _e( 'General Info', 'frozr' ); ?></span>
				<?php do_action('frozr_before_user_general_options'); ?>
				<div class="form-group col-1">
					<label class="form-group col-1 control-label" for="frozr_store_name"><?php _e( 'Restaurant Name', 'frozr' ); ?></label>
					<input id="frozr_store_name" required value="<?php echo $storename; ?>" name="frozr_store_name" placeholder="<?php _e('e.g. BOB\'s Pizza','frozr');  ?>" class="form-control input-md" type="text">
				</div>
				<div class="form-group col-1">
					<label class="form-group col-1 control-label" for="setting_phone"><?php _e( 'Contact Number', 'frozr' ); ?></label>
					<input id="setting_phone" value="<?php echo $phone; ?>" name="setting_phone" placeholder="<?php _e('e.g. +967 771 232 977','frozr'); ?>" class="form-control input-md" type="text">
				</div>
				<div class="form-group col-1">
					<label class="form-group col-1 control-label" for="setting_address"><?php _e( 'Restaurant Address/Addresses', 'frozr' ); ?>&nbsp;<a href="#" title="<?php _e( 'Type address and hit the comma button. If your restaurant have branches, enter all branches addresses separated with commas.', 'frozr' ); ?>">[?]</a></label>
					<input class="form-control" rows="4" id="setting_address" value= "<?php echo frozr_get_restaurant_address($current_user); ?>" name="setting_address" placeholder="<?php _e('e.g. Seasons Mall, Town Mall','frozr'); ?>">
				</div>			
				<div class="form-group col-1">
					<label class="control-label" for="rest_type"><?php _e( 'Restaurant Cuisine', 'frozr' ); ?>&nbsp;<a href="#" title="<?php _e( 'i.e Italian, Indian, Fast Food, Fine Dining .. etc. Type first two/three letters and choose from list, if the list doesn\'t appear then complete typing and hit the comma button.', 'frozr' ); ?>">[?]</a></label>
					<input id="rest_type" required name="rest_type" value="<?php echo $grestypes; ?>">
				</div>
				<div class="form-group col-1">
					<span class="control-label"><?php echo __( 'Food Types Served', 'frozr' ); ?></span>
					<div>
						<label><?php _e( 'Veg.', 'frozr' ); ?>
							<input type="checkbox" name="rest_food_type[0]" value="veg" <?php checked( $rest_food_type[0], 'veg' ); ?>>
						</label>
						<label><?php _e( 'Non-Veg.', 'frozr' ); ?>
							<input type="checkbox" name="rest_food_type[1]" value="nonveg" <?php checked( $rest_food_type[1], 'nonveg' ); ?>>
						</label>
						<label><?php _e( 'Sea Food.', 'frozr' ); ?>
							<input type="checkbox" name="rest_food_type[2]" value="sea-food" <?php checked( $rest_food_type[2], 'sea-food' ); ?>>
						</label>
					</div>
				</div>
				<div class="form-group col-1">
					<label class="control-label" for="delivery_locations"><?php _e( 'Delivery Locations', 'frozr' ); ?>&nbsp;<a href="#" title="<?php _e( 'Road/Street names. Type first two/three letters and choose from list, if the list doesn\'t appear then complete typing and hit the comma button.', 'frozr' ); ?>">[?]</a></label>
					<input id="delivery_locations" required name="delivery_locations" value="<?php echo $locs; ?>">
				</div>	
				<div class="form-group col-1 checkbox">
					<label class="control-label" for="rest_show_email"><?php _e( 'Show email address in restaurant page.', 'frozr' ); ?></label>
					<input id="rest_show_email" type="checkbox" name="setting_show_email" value="yes"<?php checked( $show_email, 'yes' ); ?>>
				</div>
				<div class="form-group col-1 checkbox">
					<label class="control-label" for="rest_allow_email" ><?php _e( 'Allow receiving emails form your store page.', 'frozr' ); ?></label>
					<input id="rest_allow_email" type="checkbox" name="setting_allow_email" value="yes"<?php checked( $allow_email, 'yes' ); ?>>
				</div>
				<?php do_action('frozr_after_user_general_options'); ?>
			</div>
			<div id="usr_delivery_opts" class="group-opts">
				<span class="form-group-label"><?php _e( 'Delivery Settings', 'frozr' ); ?></span>
				
				<?php do_action('frozr_before_user_delivery_options'); ?>
				
				<div class="form-group col-2">
					<label class="control-label" for="shipping_fee"><?php echo __( 'Delivery Fee ', 'frozr' ) . get_woocommerce_currency_symbol(); ?></label>
					<input id="shipping_fee" value="<?php echo $shipping_fee; ?>" name="shipping_fee" class="form-control" placeholder="0.0" type="number">
				</div>
				<div class="form-group">
					<span class="control-label"><?php echo __( 'Calculate Delivery by', 'frozr' ); ?></span>
					<div class="delivey_by_options">
						<label>
							<input name="deliveryby" value="order" <?php checked( $deliveryby, 'order' ); ?> type="radio">
							<?php _e( 'Order.', 'frozr' ); ?>
						</label>
						<label>
							<input name="deliveryby" value="item" <?php checked( $deliveryby, 'item' ); ?> type="radio">
							<?php _e( 'Item.', 'frozr' ); ?>
						</label>
					</div>
				</div>
				<div class="form-group col-2">
					<label class="control-label" for="shipping_pro_adtl_cost"><?php echo __( 'Per additional item fee ', 'frozr' ) . get_woocommerce_currency_symbol(); ?></label>
					<input id="shipping_pro_adtl_cost" value="<?php echo $shipping_pro_adtl_cost; ?>" name="shipping_pro_adtl_cost" class="form-control" placeholder="0.0" type="number">
				</div>
				<div class="form-group col-2">
					<label class="control-label" for="processing_time"><?php _e( 'Delivery Duration (In Minutes)', 'frozr' ); ?></label>
					<input id="processing_time" value="<?php echo $processing_time; ?>" name="processing_time" class="form-control" placeholder="0.0" type="number">
				</div>
				<div class="form-group col-2">
					<label class="control-label" for="min_order_amt"><?php echo __( 'Minimum order amount ', 'frozr' ) . get_woocommerce_currency_symbol(); ?></label>
					<input id="min_order_amt" value="<?php echo $min_order_amt; ?>" name="min_order_amt" class="form-control" placeholder="0.0" type="number">
				</div>
				
				<?php do_action('frozr_after_user_delivery_options'); ?>
			</div>
			<div id="usr_orders_opts" class="group-opts">
				
				<?php do_action('frozr_before_user_orders_options'); ?>
				
				<div class="form-group col-1 checkbox">
					<label class="control-label" for="rest_allow_ords"><?php _e( 'Allow Orders Even while closed.', 'frozr' ); ?></label>
					<input id="rest_allow_ords" type="checkbox" name="setting_allow_ofline_orders" value="yes"<?php checked( $allow_ofline_orders, 'yes' ); ?>>
				</div>
				<div class="form-group">
					<label class="control-label" for="accept_order_types"><?php _e('Accepted Order Types','frozr'); ?></label>
					<select name="accept_order_types[]" id="accept_order_types" multiple="multiple" data-native-menu="false">
						<?php $frozr_accepted_orders = frozr_default_accepted_orders_types();
						foreach ($frozr_accepted_orders as $val) {
							echo '<option value="'.$val.'"' . ( in_array( $val, $orders_accept ) ? 'selected="selected"' : '' ) . '>' . esc_attr( $val ) . '</option>';
						} ?>
						<?php do_action('frozr_after_accepted_order_types_select_option', $orders_accept); ?>
					</select>
				</div>
				<div class="form-group">
					<label class="control-label" for="accept_order_types_cl"><?php _e('Accepted Order Types when Closed','frozr'); ?></label>
					<select name="accept_order_types_cl[]" id="accept_order_types_cl" multiple="multiple" data-native-menu="false">
						<option value="none" <?php echo ( in_array( 'none', $orders_accept_cl ) ? 'selected="selected"' : '' ); ?>><?php _e('None','frozr'); ?></option>
						<?php $frozr_accepted_orders = frozr_default_accepted_orders_types();
						foreach ($frozr_accepted_orders as $val) {
							echo '<option value="'.$val.'"' . ( in_array( $val, $orders_accept_cl ) ? 'selected="selected"' : '' ) . '>' . esc_attr( $val ) . '</option>';
						} ?>
						<?php do_action('frozr_after_accepted_order_types_while_closed_select_option', $orders_accept_cl); ?>
					</select>
				</div>

				<?php do_action('frozr_after_user_orders_options'); ?>

			</div>
			<div id="usr_social_profile" class="group-opts">
				<span class="form-group-label"><?php _e( 'Social Profile', 'frozr' ); ?></span>
				
				<?php do_action('frozr_before_user_social_profile_options'); ?>
				
				<div class="form-group col-2">
					<label class="control-label" for="socialfb"><i class="fs-icon-facebook"></i>&nbsp;</span><?php echo __( 'Facebook Account ', 'frozr' ); ?></label>
					<input id="socialfb" value="<?php echo $fb; ?>" name="socialfb" class="form-control" placeholder="http://" type="url">
				</div>
				<div class="form-group col-2">
					<label class="control-label" for="socialgplus"><i class="fs-icon-google-plus"></i>&nbsp;</span><?php echo __( 'Google plus', 'frozr' ); ?></label>
					<input id="socialgplus" value="<?php echo $gplus; ?>" name="socialgplus" class="form-control" placeholder="http://" type="url">
				</div>
				<div class="form-group col-2">
					<label class="control-label" for="socialtwitter"><i class="fs-icon-twitter"></i>&nbsp;</span><?php echo __( 'Twitter Account', 'frozr' ); ?></label>
					<input id="socialtwitter" value="<?php echo $twitter; ?>" name="socialtwitter" class="form-control" placeholder="http://" type="url">
				</div>
				<div class="form-group col-2">
					<label class="control-label" for="sociallinkedin"><i class="fs-icon-linkedin"></i>&nbsp;</span><?php echo __( 'Linkedin', 'frozr' ); ?></label>
					<input id="sociallinkedin" value="<?php echo $linkedin; ?>" name="sociallinkedin" class="form-control" placeholder="http://" type="url">
				</div>
				<div class="form-group col-2">
					<label class="control-label" for="socialyoutube"><i class="fs-icon-youtube"></i>&nbsp;</span><?php echo __( 'Youtube', 'frozr' ); ?></label>
					<input id="socialyoutube" value="<?php echo $youtube; ?>" name="socialyoutube" class="form-control" placeholder="http://" type="url">
				</div>

				<?php do_action('frozr_after_user_social_profile_options'); ?>

			</div>
			<div id="usr_opcl_opts" class="group-opts">
				<span class="form-group-label"><?php _e( 'Opening/Closing timings', 'frozr' ); ?></span>
				<div class="style_box"><i class="fs-icon-info-circle"></i>&nbsp;<p><?php _e( 'Set your timing in 24 hour format. for example, if your restaurant opens from 01:00 pm till 06:00 pm, enter 13:00 in the opening input and 18:00 in the closing input. If your restaurant opens 24 hours, enter 00:00 in both, opening and closing inputs.', 'frozr' ); ?></p></div>
				
				<?php do_action('frozr_before_user_opening_options'); ?>
				
				<?php $opxlar = apply_filters('frozr_store_timing_week_array',array(
					'sat' => __( 'Saturday', 'frozr' ),
					'sun' => __( 'Sunday', 'frozr' ),
					'mon' => __( 'Monday', 'frozr' ),
					'tue' => __( 'Tuesday', 'frozr' ),
					'wed' => __( 'Wednesday', 'frozr' ),
					'thu' => __( 'Thursday', 'frozr' ),
					'fri' => __( 'Friday', 'frozr' ),
					));
					$opxlarx = apply_filters('frozr_store_timing_week_args',array('Sat','Sun','Mon','Tue','Wed','Thu','Fri'));
					$opxlnum = 0;
				foreach ($opxlar as $k => $vk) {
				$opxlxx = frozr_restaurant_timing($opxlarx[$opxlnum]); ?>
				<div class="form-group col-2 opcl_settings">
					<div class="control_label_group">
						<label class="control-label" for="rest_<?php echo $k; ?>_opening"><strong><?php echo $vk; ?></strong></label>
						<label for="rest_<?php echo $k; ?>_open">
							<input name="rest_<?php echo $k; ?>_open" type="checkbox" class="rest_open" value="yes" <?php checked( $opxlxx[0], 'yes' ); ?> />
							<?php _e( 'Open', 'frozr' ); ?>
						</label>
						<label class="rest_shifts_cont <?php if($opxlxx[0] != 'yes') { echo 'frozr-hide';} ?>" for="rest_<?php echo $k; ?>_shifts">
							<input name="rest_<?php echo $k; ?>_shifts" type="checkbox" class="rest_shifts" value="yes" <?php checked( $opxlxx[1], 'yes' ); ?> />
							<?php _e( 'Two Shifts', 'frozr' ); ?>
						</label>
						<?php do_action('frozr_after_store_timing_checkboxes', $k ,$vk); ?>
					</div>
					<div class="opt_opts">
						<div class="rest_time_inputs <?php if($opxlxx[0] != 'yes') { echo 'frozr-hide';} ?>">
							<div class="rest_one">
								<label class="control-label" for="rest_<?php echo $k; ?>_opening_one"><?php _e( 'Shift One Opening', 'frozr' ); ?></label>
								<input id="rest_<?php echo $k; ?>_opening_one" value="<?php echo $opxlxx[2]; ?>" name="rest_<?php echo $k; ?>_opening_one" class="form-control" type="time">
								<label class="control-label" for="rest_<?php echo $k; ?>_closing_one"><?php _e( 'Shift One Closing', 'frozr' ); ?></label>
								<input id="rest_<?php echo $k; ?>_closing_one" value="<?php echo $opxlxx[3]; ?>" name="rest_<?php echo $k; ?>_closing_one" class="form-control" type="time">
							</div>
							<div class="rest_two <?php if($opxlxx[1] != 'yes') { echo 'frozr-hide';} ?>">
								<label class="control-label" for="rest_<?php echo $k; ?>_opening_two"><?php _e( 'Shift two Opening', 'frozr' ); ?></label>
								<input id="rest_<?php echo $k; ?>_opening_two"  value="<?php echo $opxlxx[4]; ?>" name="rest_<?php echo $k; ?>_opening_two" class="form-control" type="time">
								<label class="control-label" for="rest_<?php echo $k; ?>_closing_two"><?php _e( 'Shift two Closing', 'frozr' ); ?></label>
								<input id="rest_<?php echo $k; ?>_closing_two" value="<?php echo $opxlxx[5]; ?>" name="rest_<?php echo $k; ?>_closing_two" class="form-control" type="time">
							</div>
						</div>
						<?php do_action('frozr_after_store_timing_dates', $k ,$vk); ?>
					</div>
				</div>
				<?php $opxlnum++; } ?>
				<div class="form-group col-2 opcl_settings">
					<label class="control-label" for="rest_unad_one"><?php _e( 'Unavailable Dates (mm/dd/yyyy)', 'frozr' ); ?></label>
					<div class="input-group">
						<div class="multi-field-wrapper">
							<div class="multi-fields">
								<?php $unds = get_user_meta($current_user, '_rest_unavds', true);
								if (!empty($unds)) { foreach ($unds as $und){ ?>
								<div class="multi-field">
									<input value="<?php echo $und; ?>" name="rest_unads[]" class="rest_unad form-control" type="date">
									<i class="remove-field fs-icon-close"></i>
								</div>
								<?php } } else { ?>
								<div class="multi-field">
									<input value="" name="rest_unads[]" class="rest_unad form-control" type="date">
									<i class="remove-field fs-icon-close"></i>
								</div>
								<?php } ?>
							</div>
							<button type="button" class="add-field"><?php _e('Add new unavailable date','frozr'); ?></button>
						</div>
					</div>
				</div>
				
				<?php do_action('frozr_after_user_opening_options'); ?>

			</div>
			<div id="usr_with_opts" class="group-opts">
				<span class="form-group-label"><?php _e( 'Withdraw Payment Method', 'frozr' ); ?></span>
				<?php $methods = frozr_withdraw_get_active_methods(); ?>
				<?php do_action('frozr_before_user_withdraw_options'); ?>
				<?php foreach ($methods as $method_key) {
					$method = frozr_withdraw_get_method( $method_key ); ?>
					<div class="form-group col-2 opcl_settings">
						<label class="control-label" for="rest_fri_opening"><strong><?php echo frozr_withdraw_get_method_title( $method_key ); ?></strong></label>
						<div id="frozr-payment-<?php echo $method_key; ?>">
							<?php if ( is_callable( $method['callback']) ) {
								call_user_func( $method['callback'], $profile_info );
							} ?>
						</div>
					</div>
				<?php } ?>
				<?php do_action('frozr_after_user_withdraw_options'); ?>
			</div>
			<div id="usr_tables_opts" class="group-opts">
				<span class="form-group-label"><?php _e( 'Restaurant Tables', 'frozr' ); ?></span>
		
				<div class="form-group col-1 checkbox">
					<label class="control-label" for="show_rest_tables"><?php _e( 'Show Restaurant Tables.', 'frozr' ); ?></label>
					<input id="show_rest_tables" type="checkbox" name="show_rest_tables" value="yes"<?php checked( $show_rest_tables, 'yes' ); ?>>
				</div>

				<?php do_action('frozr_before_user_tables_options'); ?>

				<div class="multi-field-wrapper <?php if ($show_rest_tables != "yes") { echo "frozr-hide"; } ?>">
					<div class="multi-fields">
					<?php $rest_tbls = '' != get_user_meta($current_user, '_rest_tables', true) ? get_user_meta($current_user, '_rest_tables', true) : array('0' => array('img' => 0, 'title' => '', 'seats' => '', 'shape' => array('circle' => 'yes', 'square' => 'no'), 'notes' => '', 'like' => ''));
					foreach ($rest_tbls as $rest_tbl) { ?>
						<div class="multi-field rest_tables_fields">
							<i class="remove-field fs-icon-close"></i>
							<div class="form-group col-2">
								<span class="control-label"><?php echo __( 'Number of Seats', 'frozr' ); ?><a href="#" title="<?php _e('Enter the maximum number of people the table fits, so if the table fits 4 or 6 people, write 6.','frozr'); ?>"> [?]</a></span>
								<input value="<?php echo $rest_tbl['seats']; ?>" name="rest_tables[][seats]" class="form-control" placeholder="4" type="number">
							</div>
							<div class="form-group col-2">
								<span class="control-label"><?php echo __( 'Tables Numbers/Titles', 'frozr' ); ?><a href="#" title="<?php _e('Separate by commas. i.e. n20, z22, x32','frozr'); ?>"> [?]</a></span>
								<input value="<?php echo $rest_tbl['title']; ?>" name="rest_tables[][title]" class="form-control" placeholder="<?php _e('n20, z22, x32,','frozr'); ?>">
							</div>
							<div class="form-group">
								<span class="control-label"><?php echo __( 'Available Table Shapes', 'frozr' ); ?></span>
								<div class="tables_shape_options">
									<label>
										<input data-role="none" name="rest_tables[][shape][]" value="circle" <?php checked( $rest_tbl['shape'][0], 'circle' ); ?> type="checkbox">
										<?php _e( 'Circle.', 'frozr' ); ?>
									</label>
									<label>
										<input data-role="none" name="rest_tables[][shape][]" value="square" <?php checked( $rest_tbl['shape'][1], 'square' ); ?> type="checkbox">
										<?php _e( 'Square.', 'frozr' ); ?>
									</label>
								</div>
							</div>
							<div class="form-group col-2">
								<span class="control-label"><?php echo __( 'Notes', 'frozr' ); ?><a href="#" title="<?php _e('Write some notes about this group of tables. i.e. Table n20 has a clear view on the beach.','frozr'); ?>"> [?]</a></span>
								<input value="<?php echo $rest_tbl['notes']; ?>" name="rest_tables[][notes]" class="form-control" placeholder="<?php _e('Table n20 has a clear view on the beach.','frozr'); ?>">
							</div>
							<?php do_action('frozr_after_user_tables_inputs',$rest_tbl); ?>
						</div>
					<?php } ?>
					</div>
					<button type="button" class="add-field"><?php _e('Add New Tables Group','frozr'); ?></button>
				</div>
				
				<?php do_action('frozr_after_user_tables_options'); ?>
				
			</div>
			
			<?php do_action('frozr_after_user_front_options'); ?>
			
		</div>
		<div class="form-group-settings submit-frozr-settings">
			<button id="frozr_update_profile"><?php esc_attr_e('Update Settings','frozr'); ?></button>
		</div>
	</form>
	<script type="text/javascript">
		jQuery(function($) {
			$('#delivery_locations').tagator({
				autocomplete: [<?php echo $allocs; ?>]
			});
 			$('#rest_type').tagator({
				autocomplete: [<?php echo $allgrestypes; ?>]
			});
 			$('#setting_address').tagator({
				autocomplete: [<?php echo $alladdresses; ?>]
			});
		});
	</script>
<?php
}
//get available seats
function frozr_rest_search_tables($xn, $xnx) {
	$rest_tbls = apply_filters('frozr_user_tables_search',get_user_meta($xn, '_rest_tables', true), $xn, $xnx);
	$store_info = frozr_get_store_info( $xn );
	$nxnx = 0;
	$xxnn = 0;
	$sts_srry = array();
	foreach ($rest_tbls as $rest_tbl) {
		$sts_srry[] = $rest_tbl['seats'];
	}
	if (in_array($xnx, $sts_srry)) { $vk = array_search($xnx,$sts_srry); } else {$closest_table = frozr_get_closest_arry_val($sts_srry, $xnx); $vk = array_search($closest_table,$sts_srry);}
	if ($rest_tbls[$vk]['seats'] > $xnx) {
		echo '<div class="tables_search_notice">' . apply_filters('frozr_more_tables_search_text',__('We did not find a table with an exact seats number you requested but you can also check these tables. ','frozr')) . '</div>';
	} elseif ($rest_tbls[$vk]['seats'] < $xnx) {
		echo '<div class="tables_search_notice">' . apply_filters('frozr_less_tables_search_text',__('We did not find a table with an exact seats number you requested but we will manage to add extra seats on the table.','frozr')) . '</div>';			
	} ?>
	<div class="tables_seats_numbers">
		<i class="fs-icon-users"></i>
		<div><span class="table_info_span"><?php echo __('Seats:','frozr'); ?></span>&nbsp;<?php echo $rest_tbls[$vk]['seats']; ?></div>
	</div>
	<?php $tnss = explode(',', $rest_tbls[$vk]['title']); $tns = array_filter( $tnss, 'strlen' ); ?>
	<div class="tables_seats_numbers">
		<i class="fs-icon-book"></i>
		<div class="seats_no_cont"><span class="table_info_span"><?php echo __('Tables Titles/Numbers:','frozr'); ?></span><div><?php foreach ($tns as $tn) { echo "<span class=\"tbls_numbrs\">$tn</span>";} ?></div></div>
	</div>
	<div class="tables_shape">
		<i class="fs-icon-circle-o"></i>
		<div><span class="table_info_span"><?php echo __('Available Shapes:','frozr'); ?></span>&nbsp;<?php if(is_array($rest_tbls[$vk]['shape']) && !empty($rest_tbls[$vk]['shape'])) { foreach ($rest_tbls[$vk]['shape'] as $xx => $xxx) { $shcom = ($xxnn == 0 && !empty($rest_tbls[$vk]['shape'][1])) ? ', ' : ''; echo "<span class=\"tbls_shapes\">$xxx$shcom</span>"; $xxnn++;} } else { echo "<span class=\"tbls_shapes\">". __('Other','frozr') ."</span>"; } ?></div>
	</div>
	<div class="tables_notes">
		<i class="fs-icon-info-circle"></i>
		<div><span class="table_info_span"><?php echo __('Notes:','frozr'); ?></span>&nbsp;<?php echo $rest_tbls[$vk]['notes']; ?></div>
	</div>
	
	<?php do_action('frozr_after_user_tables_search', $xn, $xnx); ?>
	
	<div class="tables_reservation">
		<span><i class="fs-icon-phone"></i>&nbsp;<?php _e('Call now to reserve:','frozr'); ?>&nbsp;<a href="tel:<?php echo esc_html( $store_info['phone'] ); ?>"><?php echo esc_html( $store_info['phone'] ); ?></a></span>
	</div>
<?php
}
//restaurant email form
function frozr_restaurant_email_form($nx, $admin = false) { ?>

	<form id="frozr-form-contact-seller" action="" method="post" class="seller-form clearfix">
		<div class="ajax-response"></div>
		<ul>
			<?php if (! $admin) { ?>
			
			<?php do_action('frozr_befor_restaurant_email_form', $nx, $admin); ?>
			
			<li>
			<input type="text" name="name" value="" placeholder="<?php esc_attr_e( 'Your Name', 'frozr' ); ?>" class="form-control" minlength="5" required="required">
			</li>
			<li>
			<input type="email" name="email" value="" placeholder="<?php esc_attr_e( 'you@example.com', 'frozr' ); ?>" class="form-control" required="required">
			</li>
			<?php } else { ?>
			<li>
			<input type="text" name="subject" value="" placeholder="<?php esc_attr_e( 'Email Subject', 'frozr' ); ?>" class="form-control">
			</li>
			<?php } ?>
			<li>
			<textarea name="message" maxlength="1000" cols="25" rows="6" value="" placeholder="<?php esc_attr_e( 'Type your message...', 'frozr' ); ?>" class="form-control" required="required"></textarea>
			</li>
			
			<?php do_action('frozr_after_restaurant_email_form', $nx, $admin); ?>
			
		</ul>

		<?php wp_nonce_field( 'frozr_contact_seller' ); ?>
		<input type="hidden" class="frozr_seller_id_msg" name="seller_id" value="<?php echo intval($nx); ?>">
		<input type="hidden" name="action" value="frozr_contact_seller">
		<input type="submit" name="store_message_send" value="<?php esc_attr_e( 'Send Message', 'frozr' ); ?>" class="pull-right btn btn-theme">
	</form>
<?php
}