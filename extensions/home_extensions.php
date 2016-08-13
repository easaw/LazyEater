<?php 
/**
 * Home page Eater loops Extensions
 *
 *
 * @package FrozrCoreLibrary
 * @subpackage FrozrExtensions
 */
 
/**
 * TODO: Get featured products
 */
if (!function_exists ('frozr_get_featured_products') ) {
	function frozr_get_featured_products( $per_page = 9) {
		$featured_query = new WP_Query( apply_filters( 'frozr_get_featured_products', array(
			'posts_per_page' => $per_page,
			'post_type' => 'product',
			'ignore_sticky_posts' => 1,
			'meta_query' => array(
				array(
					'key' => '_visibility',
					'value' => array('catalog', 'visible'),
					'compare' => 'IN'
				),
				array(
					'key' => '_featured',
					'value' => 'yes'
				)
			)
		) ) );

		return $featured_query;
	}
}

/**
 * TODO: Get top rated products
 */
if (!function_exists ('frozr_get_top_rated_products') ) {
	function frozr_get_top_rated_products( $per_page = 8 ) {

		$args = array(
			'post_type'             => 'product',
			'post_status'           => 'publish',
			'ignore_sticky_posts'   => 1,
			'posts_per_page'        => $per_page,
			'meta_query'            => array(
				array(
					'key'           => '_visibility',
					'value'         => array('catalog', 'visible'),
					'compare'       => 'IN'
				)
			)
		);

		add_filter( 'posts_clauses', array( 'WC_Shortcodes', 'order_by_rating_post_clauses' ) );

		$top_rated_query = new WP_Query( apply_filters( 'frozr_top_rated_products_query', $args ) );

		remove_filter( 'posts_clauses', array( 'WC_Shortcodes', 'order_by_rating_post_clauses' ) );

		return $top_rated_query;
	}
}

//Best Selling Products
if (get_theme_mod('show_top_dishes',true) == true) {
	add_action('frozr_below_indexloop', 'frozr_best_selling_dishes', 10);
}
if (!function_exists ('frozr_best_selling_dishes') ) {
	function frozr_best_selling_dishes() {
		global $post, $products;
		$posts_per_page = 3;

		$atts = array(
			'orderby' => 'title',
			'order'   => 'asc');
			
		$args = array(
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'posts_per_page'      => $posts_per_page,
			'meta_key'            => 'total_sales',
			'orderby'             => 'meta_value_num',
			'meta_query'          => WC()->query->get_meta_query()
		);		

		$products = new WP_Query(apply_filters('frozr_best_selling_dishes', $args, $atts));
		$theme_layout = get_theme_mod('theme_layout','left');
		$top_dishes_title = get_theme_mod('top_dish_loop_title', 'Top Selling Items');
		$top_dishes_desc = get_theme_mod('top_dish_desc', 'Top selling items this week!');
		$top_dish_title_icon = get_theme_mod('top_dish_icon','none');

		if ( $products->have_posts() ) : ?>

			<div id="top_dishes_wrapper">
				<div class="st_posts_list_home">
					<div class="st-header-home hm_dish_header">
						<?php do_action('frozr_before_top_dishes_header'); ?>
						<div class="st-posts-title-home hm_dish_title <?php if ($theme_layout == 'right') {echo ' right_hand_st';} ?>"><?php if ($top_dish_title_icon != 'none' && $theme_layout == 'left') { ?> <i class="<?php echo $top_dish_title_icon; ?>"></i> <?php } ?><span><?php echo apply_filters( 'frozr_top_dishes_title_text', $top_dishes_title ); ?></span><?php if ($top_dish_title_icon != 'none' && $theme_layout == 'right') { ?> <i class="<?php echo $top_dish_title_icon; ?>"></i> <?php } ?></div>
						<?php if ( !empty($top_dishes_desc) ) { echo apply_filters( 'frozr_top_dishes_desc_text', '<div class="st-description-home hm_dish_desc"><span>' . $top_dishes_desc . '</span></div>' );} ?>
						<?php do_action('frozr_after_top_dishes_header'); ?>
					</div>
					<div class="st-body-home hm_dish_body">
						<?php while ( $products->have_posts() ) : $products->the_post();
						$store_name = frozr_get_store_info( $post->post_author );
						$pro_cat = get_the_term_list( $post->ID, 'product_cat','',', ' );
						$pro_sales = get_post_meta( $post->ID, 'total_sales', true );
						$dish_veg = get_post_meta( $post->ID, '_dish_veg', true );
						$dish_spicy = get_post_meta( $post->ID, '_dish_spicy', true ); 
						$large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'large');
						?>
						
							<article class="hm_dish_products" style="background: url('<?php echo $large_image_url[0]; ?>') no-repeat center center #fff;background-size:cover;">
								<a class="top_pro_title" href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'frozr' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
								<a class="top_pro_vendor" style="background-image: url('<?php echo frozr_avatar_url($post->post_author); ?>');" href="<?php echo frozr_get_store_url(get_the_author_meta( 'ID' )); ?>" title="<?php echo $store_name['store_name']; ?>"><?php echo __('Served by ','frozr') . $store_name['store_name']; ?></a>
								<div class="top_pro_details">
									<?php if (!empty ($dish_veg)) { ?><span class="top_pro_type <?php echo $dish_veg; ?>"></span> <?php } ?>
									<span class="top_pro_cat"><?php echo $pro_cat; ?></span>
									<span class="top_pro_sales"><?php echo $pro_sales; ?>&nbsp;<?php _e('Sales','frozr'); ?></span>
									<?php if (!empty ($dish_spicy)) { ?><span class="top_pro_spicy dish-is-spicy"></span> <?php } ?>
									<?php do_action('frozr_after_top_product_details'); ?>
								</div>
								<?php do_action('frozr_after_top_product'); ?>
							</article>
						<?php endwhile; // end of the loop. ?>
					</div>
				</div>
			</div>

		<?php endif;

		wp_reset_postdata();
	}
}

//Latest Restaurant
if (get_theme_mod('show_latest_rests', true) == true) {
add_action('frozr_below_indexloop', 'frozr_latest_resturants', 10);
}
if (!function_exists ('frozr_latest_resturants') ) {
	function frozr_latest_resturants() {

		$args = array(
			'role'         => 'seller',
			'meta_key'     => 'frozr_enable_selling',
			'meta_value'   => 'yes',
			'orderby'      => 'registered',
			'order' 		=> 'DESC',
			'number'       => 3,
			'count_total'  => false,
			'fields'       => 'ID'
		 );

		$user_query = new WP_User_Query( apply_filters('frozr_latest_restaurants_query',$args) );
		$theme_layout = get_theme_mod('theme_layout','left');
		$latest_rests_title = get_theme_mod('latest_rests_title', 'Latest Restaurants');
		$latest_rests_desc = get_theme_mod('latest_rests_desc', 'Newest Restaurants Joined The List!');
		$latest_rests_icon = get_theme_mod('latest_rests_icon','none');
		// User Loop
		if ( ! empty( $user_query->results ) ) { ?>
			<div id="new_rest_wrapper">
				<div class="st_posts_list_home">
					<div class="st-header-home hm_dish_header">
						<div class="st-posts-title-home hm_dish_title <?php if ($theme_layout == 'right') {echo ' right_hand_st';} ?>"><?php if ($latest_rests_icon != 'none' && $theme_layout == 'left') { ?> <i class="<?php echo $latest_rests_icon; ?>"></i> <?php } ?><span><?php echo apply_filters( 'frozr_latest_restaurants_title_text', $latest_rests_title ); ?></span><?php if ($latest_rests_icon != 'none' && $theme_layout == 'right') { ?> <i class="<?php echo $latest_rests_icon; ?>"></i> <?php } ?></div>
						<?php if ( !empty($latest_rests_desc) ) { echo apply_filters( 'frozr_latest_restaurants_desc_text', '<div class="st-description-home hm_dish_desc"><span>' . $latest_rests_desc . '</span></div>' );} ?>
					</div>
					<div class="st-body-home hm_dish_body">
					<?php foreach ( $user_query->results as $user ) {
						frozr_restaurants_inloops($user);
					} ?>
					</div>
				</div>
			</div>
		<?php } ?>
	<?php
	}
}

//home advance search
if (!function_exists ('frozr_home_advance_search') ) {
	function frozr_home_advance_search() {
		
	//messages
	$msgone = get_theme_mod('front_rest_adv_search_title_one', __('Order your food online from local restaurants.','frozr'));
	$msgtwo = get_theme_mod('front_rest_adv_search_title_two', __('Order your food online from local restaurants.','frozr'));

	if (get_theme_mod('show_rest_adv_search', true) == true) { ?>
	<div id="resturants_search_box">
		<?php if (isset($_COOKIE['frozr_user_location']) && is_super_admin() ) { ?>
		<div id="restaurant_search_box_trash">
		<h3 class="trash_box_title"><?php _e('Filters Bench','frozr'); ?><i class="trash_btn fs-icon-filter"></i></h3>
		<?php $front_src_trash_ordr = array('none'); if (isset($_COOKIE['frozr_user_src_type']) && $_COOKIE['frozr_user_src_type'] == "restaurants") {
			$front_src_trash_ordr = ('' != get_theme_mod('front_trash_sort_objects')) ? explode(',',get_theme_mod('front_trash_sort_objects')) : array('none');
			} elseif (isset($_COOKIE['frozr_user_src_type']) && $_COOKIE['frozr_user_src_type'] == "delivery" ) {
			$front_src_trash_ordr = ('' != get_theme_mod('front_trash_delv_sort_objects')) ? explode(',',get_theme_mod('front_trash_delv_sort_objects')) : array('none');
			}
			if (!in_array('none',$front_src_trash_ordr)) {
			foreach($front_src_trash_ordr as $srcordr) {
			frozr_home_advance_search_body($srcordr);
			}
			} else {
				echo '<span class="rest_trash_empty">' . __('Drop filters you don\'t want to show here.','frozr') .'</span>';
			}
		?></div>
		<?php } ?>
		<div class="rsb_wrapper">
		<?php if (!isset($_COOKIE['frozr_user_location']) ) { ?>
		<div class="rsbs_box">
			<div class="rsb-boxes">
				<?php $cnt_btn = '';
				if (is_super_admin()) {
					$cnt_btn = ' control_edit';
					echo '<div class="front_inputs_wrap"><input data-tlt="rest_adv_search_title_one" class="front_inputs" value="'. $msgone .'" placeholder="'. $msgone .'">'. frozr_front_texts_edit_btns() .'</div>';
				} ?>
				<h1 class="lei_h1 hom_src_main_title<?php echo $cnt_btn; ?>"><span><?php echo $msgone; ?></span></h1>
			</div>
			<div id="home_loc_box" class="rsb-boxes">
				<?php frozr_user_type_option(); ?>		
				<?php frozr_user_location_form('refresh'); ?>
				<?php do_action('frozr_after_home_advance_search_location'); ?>			
			</div>
		</div>
		<?php } else { ?>
		<div class="src_box_two_header_wrap">
			<?php $cnt_btn = '';
			if (is_super_admin()) {
				$cnt_btn = ' control_edit';
				echo '<div class="front_inputs_wrap"><input data-tlt="rest_adv_search_title_two" class="front_inputs" value="'. $msgtwo .'" placeholder="'. $msgtwo .'">'. frozr_front_texts_edit_btns() .'</div>';
			} ?>
			<h1 class="lei_h1 hom_src_main_title<?php echo $cnt_btn; ?>"><span><?php echo $msgtwo; ?></span></h1>
		</div>
		<div <?php if (is_super_admin()) { echo 'id="resturants_advance_search_box_fst"'; } ?> class="rsbs_box">
		<?php $front_src_ordr = array('cusearch','restd'); if (isset($_COOKIE['frozr_user_src_type']) && $_COOKIE['frozr_user_src_type'] == "restaurants") {
			$front_src_ordr = explode(',',get_theme_mod('front_sort_objects','popu,reco,type'));
			} elseif (isset($_COOKIE['frozr_user_src_type']) && $_COOKIE['frozr_user_src_type'] == "delivery" ) {
			$front_src_ordr = explode(',',get_theme_mod('front_delv_sort_objects','cusearch,restd'));
			}
			foreach($front_src_ordr as $src_ordr) {
				frozr_adv_src_sort($src_ordr);
			} ?>
			<?php do_action('frozr_after_home_advance_search_body_fst'); ?>
		</div>
		<?php } ?>
		</div>
	</div>
	<?php } ?>
	<?php if (isset($_COOKIE['frozr_user_location']) && get_theme_mod('show_rest_adv_filter_accord', true) == true) { ?>
	<div id="resturants_advance_search_box" <?php if (is_super_admin()) { echo 'class="sort_adv_box"'; } ?> >
	<?php $front_src_ordr_snd = array('catsearch','ingsearch','spysearch'); if (isset($_COOKIE['frozr_user_src_type']) && $_COOKIE['frozr_user_src_type'] == "restaurants" ) {
		$front_src_ordr_snd = explode(',',get_theme_mod('front_sort_objects_two','cusearch,restsearch,adlocsearch'));
		} elseif (isset($_COOKIE['frozr_user_src_type']) && $_COOKIE['frozr_user_src_type'] == "delivery" ) {
		$front_src_ordr_snd = explode(',',get_theme_mod('front_delv_sort_objects_two','catsearch,ingsearch,spysearch'));
		}
		foreach($front_src_ordr_snd as $src_ordr) {
			frozr_home_advance_search_body($src_ordr);
		} ?>
		<?php do_action('frozr_after_home_advance_search_body'); ?>
	</div>
	<?php }
	}
}
add_action('before_first_sec','frozr_home_advance_search');

//search text edit buttons
function frozr_front_texts_edit_btns() {

	return '<span class="front_edit_btns texts"><i class="front_inputs_save fs-icon-check"></i><i class="front_inputs_cncl fs-icon-close"></i></span>';

}
//front scripts texts function
function frozr_front_texts($xn) {
	switch($xn) {
		case 1:
			$txt = get_theme_mod('front_txt_restsearch', __('Restaurants by name.','frozr'));
		break;
		case 2:
			$txt = get_theme_mod('front_txt_cusearch', __('Restaurants by Cuisine.','frozr'));
		break;
		case 3:
			$txt = get_theme_mod('front_txt_adlocsearch', __('Restaurants by location.','frozr'));
		break;
		case 4:
			$txt = get_theme_mod('front_txt_restd', __('Restaurants deliver to you.','frozr'));
		break;
		case 5:
			$txt = get_theme_mod('front_txt_catsearch', __('Get me some..','frozr'));
		break;
		case 6:
			$txt = get_theme_mod('front_txt_ingsearch', __('Get me an item with my favourite ingredient!','frozr'));
		break;
		case 7:
			$txt = get_theme_mod('front_txt_spysearch', __('Get me something..','frozr'));
		break;
		case 8:
			$txt = __('Popular Restaurants.','frozr');
		break;
		case 9:
			$txt = __('Recommended Restaurants.','frozr');
		break;
		case 10:
			$txt = __('Restaurants by food served.','frozr');
		break;
	}
	return apply_filters('frozr_home_advance_front_texts',$txt);
}
//sort front body items
function frozr_adv_src_sort($xn) {
	if ($xn == 'popu') {
		frozr_filter_one_header('popu',frozr_front_texts(8), 'rest_hide_title');
		frozr_adv_src_itm('popu');
		frozr_filter_one_footer();
	} elseif ($xn == 'reco') {
		frozr_filter_one_header('reco',frozr_front_texts(9), 'rest_hide_title');
		frozr_adv_src_itm('reco');
		frozr_filter_one_footer();
	} elseif ($xn == 'type') {
		frozr_filter_one_header('type',frozr_front_texts(10),'rest_hide_title');
		echo '<div class="rest_typ_list_box">';
		frozr_adv_src_itm('vegb');
		frozr_adv_src_itm('nonvegb');
		frozr_adv_src_itm('sefb');
		echo '</div>';
		frozr_filter_one_footer();
	} elseif ($xn == 'cusearch') {
		frozr_filter_one_header('cusearch',frozr_front_texts(2),'cusrc_box');
		frozr_cuisine_search_body();
		frozr_filter_one_footer();
	} elseif ($xn == 'restsearch') {
		frozr_filter_one_header('restsearch',frozr_front_texts(1),'cusrc_box');
		frozr_resturant_search_body();
		frozr_filter_one_footer();
	} elseif ($xn == 'adlocsearch') {
		frozr_filter_one_header('adlocsearch',frozr_front_texts(3),'cusrc_box');
		frozr_address_location_search_body();
		frozr_filter_one_footer();
	} elseif ($xn == 'restd') {
		frozr_filter_one_header('restd',frozr_front_texts(4),'cusrc_box');
		frozr_adv_src_itm('rsb_loc_link');
		frozr_filter_one_footer();
	} elseif ($xn == 'catsearch') {
		frozr_filter_one_header('catsearch',frozr_front_texts(5),'cusrc_box');
		echo do_shortcode('[product_categories number="12" parent="0"]');
		frozr_filter_one_footer();
	} elseif ($xn == 'ingsearch') {
		frozr_filter_one_header('ingsearch',frozr_front_texts(6),'cusrc_box');
		frozr_ingredient_search_body();
		frozr_filter_one_footer();
	} elseif ($xn == 'spysearch') {
		frozr_filter_one_header('spysearch',frozr_front_texts(7),'cusrc_box');
		frozr_type_filter_body();
		frozr_filter_one_footer();
	}
}
//advance search body items edit form
function frozr_adv_src_itm($item) {

	$box_icon_array = array ('none' => __('No Icon','frozr'),'fs-icon-glass' => 'glass','fs-icon-music' => 'music','fs-icon-map' => 'Map','fs-icon-search' => 'search','fs-icon-envelope-o' => 'envelope','fs-icon-heart' => 'heart','fs-icon-star' => 'star','fs-icon-star-o' => 'star empty','fs-icon-user' => 'user','fs-icon-film' => 'film','fs-icon-th-large' => 'th-large','fs-icon-th' => 'th','fs-icon-th-list' => 'th-list','fs-icon-check' => 'check','fs-icon-remove' => 'remove','fs-icon-close' => 'close','fs-icon-times' => 'times','fs-icon-search-plus' => 'search-plus','fs-icon-search-minus' => 'search-minus','fs-icon-power-off' => 'power-off','fs-icon-signal' => 'signal','fs-icon-gear' => 'gear','fs-icon-cog' => 'cog','fs-icon-trash-o' => 'trash','fs-icon-home' => 'home','fs-icon-file-o' => 'file','fs-icon-clock-o' => 'clock','fs-icon-road' => 'road','fs-icon-download' => 'download','fs-icon-arrow-circle-o-down' => 'circle down','fs-icon-arrow-circle-o-up' => 'circle up','fs-icon-inbox' => 'inbox','fs-icon-play-circle-o' => 'circle','fs-icon-rotate-right' => 'rotate right','fs-icon-repeat' => 'repeat','fs-icon-refresh' => 'refresh','fs-icon-list-alt' => 'list-alt','fs-icon-lock' => 'lock','fs-icon-flag' => 'flag','fs-icon-headphones' => 'headphones','fs-icon-volume-off' => 'volume-off','fs-icon-volume-down' => 'volume-down','fs-icon-volume-up' => 'volume-up','fs-icon-qrcode' => 'qrcode','fs-icon-barcode' => 'barcode','fs-icon-tag' => 'tag','fs-icon-tags' => 'tags','fs-icon-book' => 'book','fs-icon-bookmark' => 'bookmark','fs-icon-print' => 'print','fs-icon-camera' => 'camera','fs-icon-font' => 'font','fs-icon-bold' => 'bold','fs-icon-italic' => 'italic','fs-icon-text-height' => 'text-height','fs-icon-text-width' => 'text-width','fs-icon-align-left' => 'align-left','fs-icon-align-center' => 'align-center','fs-icon-align-right' => 'align-right','fs-icon-align-justify' => 'align-justify','fs-icon-list' => 'list','fs-icon-dedent' => 'dedent','fs-icon-outdent' => 'outdent','fs-icon-indent' => 'indent','fs-icon-video-camera' => 'video-camera','fs-icon-photo' => 'photo','fs-icon-image' => 'image','fs-icon-picture-o' => 'picture','fs-icon-pencil' => 'pencil','fs-icon-map-marker' => 'map-marker','fs-icon-adjust' => 'adjust','fs-icon-tint' => 'tint','fs-icon-edit' => 'edit','fs-icon-pencil-square-o' => 'pencil-square','fs-icon-share-square-o' => 'share-square','fs-icon-check-square-o' => 'check-square','fs-icon-arrows' => 'arrows','fs-icon-step-backward' => 'step-backward','fs-icon-fast-backward' => 'fast-backward','fs-icon-backward' => 'backward','fs-icon-play' => 'play','fs-icon-pause' => 'pause','fs-icon-stop' => 'stop','fs-icon-forward' => 'forward','fs-icon-fast-forward' => 'fast-forward','fs-icon-step-forward' => 'step-forward','fs-icon-eject' => 'eject','fs-icon-chevron-left' => 'chevron-left','fs-icon-chevron-right' => 'chevron-right','fs-icon-plus-circle' => 'plus-circle','fs-icon-minus-circle' => 'minus-circle','fs-icon-times-circle' => 'times-circle','fs-icon-check-circle' => 'check-circle','fs-icon-question-circle' => 'question-circle','fs-icon-info-circle' => 'info-circle','fs-icon-crosshairs' => 'crosshairs','fs-icon-times-circle-o' => 'times-circle','fs-icon-check-circle-o' => 'check-circle','fs-icon-ban' => 'ban','fs-icon-arrow-left' => 'arrow-left','fs-icon-arrow-right' => 'arrow-right','fs-icon-arrow-up' => 'arrow-up','fs-icon-arrow-down' => 'arrow-down','fs-icon-mail-forward' => 'mail-forward','fs-icon-share' => 'share','fs-icon-expand' => 'expand','fs-icon-compress' => 'compress','fs-icon-plus' => 'plus','fs-icon-minus' => 'minus','fs-icon-asterisk' => 'asterisk','fs-icon-exclamation-circle' => 'exclamation-circle','fs-icon-gift' => 'gift','fs-icon-leaf' => 'leaf','fs-icon-fire' => 'fire','fs-icon-eye' => 'eye','fs-icon-eye-slash' => 'eye-slash','fs-icon-warning' => 'warning','fs-icon-exclamation-triangle' => 'exclamation-triangle','fs-icon-plane' => 'plane','fs-icon-calendar' => 'calendar','fs-icon-random' => 'random','fs-icon-comment' => 'comment','fs-icon-magnet' => 'magnet','fs-icon-chevron-up' => 'chevron-up','fs-icon-chevron-down' => 'chevron-down','fs-icon-retweet' => 'retweet','fs-icon-shopping-cart' => 'shopping-cart','fs-icon-folder' => 'folder','fs-icon-folder-open' => 'folder-open','fs-icon-arrows-v' => 'arrows-vertical','fs-icon-arrows-h' => 'arrows-horizontal','fs-icon-bar-chart-o' => 'bar-chart','fs-icon-bar-chart' => 'bar-chart','fs-icon-twitter-square' => 'twitter-square','fs-icon-facebook-square' => 'facebook-square','fs-icon-camera-retro' => 'camera-retro','fs-icon-key' => 'key','fs-icon-gears' => 'gears','fs-icon-cogs' => 'cogs','fs-icon-comments' => 'comments','fs-icon-thumbs-o-up' => 'thumbs-up','fs-icon-thumbs-o-down' => 'thumbs-down','fs-icon-star-half' => 'star-half','fs-icon-heart-o' => 'heart','fs-icon-sign-out' => 'sign-out','fs-icon-linkedin-square' => 'linkedin-square','fs-icon-thumb-tack' => 'thumb-tack','fs-icon-external-link' => 'external-link','fs-icon-sign-in' => 'sign-in','fs-icon-trophy' => 'trophy','fs-icon-github-square' => 'github-square','fs-icon-upload' => 'upload','fs-icon-lemon-o' => 'lemon','fs-icon-phone' => 'phone','fs-icon-square-o' => 'square','fs-icon-bookmark-o' => 'bookmark','fs-icon-phone-square' => 'phone-square','fs-icon-twitter' => 'twitter','fs-icon-facebook' => 'facebook','fs-icon-github' => 'github','fs-icon-unlock' => 'unlock','fs-icon-credit-card' => 'credit-card','fs-icon-rss' => 'rss','fs-icon-hdd-o' => 'hdd','fs-icon-bullhorn' => 'bullhorn','fs-icon-bell' => 'bell','fs-icon-certificate' => 'certificate','fs-icon-hand-o-right' => 'hand right','fs-icon-hand-o-left' => 'hand left','fs-icon-hand-o-up' => 'hand up','fs-icon-hand-o-down' => 'hand down','fs-icon-arrow-circle-left' => 'arrow-circle-left','fs-icon-arrow-circle-right' => 'arrow-circle-right','fs-icon-arrow-circle-up' => 'arrow-circle-up','fs-icon-arrow-circle-down' => 'arrow-circle-down','fs-icon-globe' => 'globe','fs-icon-wrench' => 'wrench','fs-icon-tasks' => 'tasks','fs-icon-filter' => 'filter','fs-icon-briefcase' => 'briefcase','fs-icon-arrows-alt' => 'arrows-alt','fs-icon-users' => 'users','fs-icon-link' => 'link','fs-icon-cloud' => 'cloud','fs-icon-flask' => 'flask','fs-icon-scissors' => 'scissors','fs-icon-copy' => 'copy','fs-icon-paperclip' => 'paperclip','fs-icon-save' => 'save','fs-icon-square' => 'square','fs-icon-navicon' => 'navicon','fs-icon-list-ul' => 'list-ul','fs-icon-list-ol' => 'list-ol','fs-icon-strikethrough' => 'strikethrough','fs-icon-underline' => 'underline','fs-icon-table' => 'table','fs-icon-magic' => 'magic','fs-icon-truck' => 'truck','fs-icon-pinterest' => 'pinterest','fs-icon-pinterest-square' => 'pinterest-square','fs-icon-google-plus-square' => 'google-plus-square','fs-icon-google-plus' => 'google-plus','fs-icon-money' => 'money','fs-icon-caret-down' => 'caret-down','fs-icon-caret-up' => 'caret-up','fs-icon-caret-left' => 'caret-left','fs-icon-caret-right' => 'caret-right','fs-icon-columns' => 'columns','fs-icon-unsorted' => 'unsorted','fs-icon-sort-down' => 'sort-down','fs-icon-sort-up' => 'sort-up','fs-icon-envelope' => 'envelope','fs-icon-linkedin' => 'linkedin','fs-icon-rotate-left' => 'rotate-left','fs-icon-legal' => 'legal','fs-icon-dashboard' => 'dashboard','fs-icon-comment-o' => 'comment','fs-icon-comments-o' => 'comments','fs-icon-flash' => 'flash','fs-icon-sitemap' => 'sitemap','fs-icon-umbrella' => 'umbrella','fs-icon-paste' => 'paste','fs-icon-lightbulb-o' => 'lightbulb','fs-icon-exchange' => 'exchange','fs-icon-cloud-download' => 'cloud-download','fs-icon-cloud-upload' => 'cloud-upload','fs-icon-user-md' => 'user','fs-icon-stethoscope' => 'stethoscope','fs-icon-suitcase' => 'suitcase','fs-icon-bell-o' => 'bell','fs-icon-coffee' => 'coffee','fs-icon-cutlery' => 'cutlery','fs-icon-file-text-o' => 'file-text','fs-icon-building-o' => 'building','fs-icon-hospital-o' => 'hospital','fs-icon-ambulance' => 'ambulance','fs-icon-medkit' => 'medkit','fs-icon-fighter-jet' => 'fighter-jet','fs-icon-beer' => 'beer','fs-icon-h-square' => 'square','fs-icon-plus-square' => 'plus-square','fs-icon-angle-double-left' => 'angle-double-left','fs-icon-angle-double-right' => 'angle-double-right','fs-icon-angle-double-up' => 'angle-double-up','fs-icon-angle-double-down' => 'angle-double-down','fs-icon-angle-left' => 'angle-left','fs-icon-angle-right' => 'angle-right','fs-icon-angle-up' => 'angle-up','fs-icon-angle-down' => 'angle-down','fs-icon-desktop' => 'desktop','fs-icon-laptop' => 'laptop','fs-icon-tablet' => 'tablet','fs-icon-mobile-phone' => 'mobile-phone','fs-icon-circle-o' => 'circle old','fs-icon-quote-left' => 'quote-left','fs-icon-quote-right' => 'quote-right','fs-icon-spinner' => 'spinner','fs-icon-circle' => 'circle','fs-icon-mail-reply' => 'mail-reply','fs-icon-github-alt' => 'github-alt','fs-icon-folder-o' => 'folder','fs-icon-folder-open-o' => 'folder-open','fs-icon-smile-o' => 'smile','fs-icon-frown-o' => 'frown','fs-icon-meh-o' => 'meh','fs-icon-gamepad' => 'gamepad','fs-icon-keyboard-o' => 'keyboard','fs-icon-flag-o' => 'flag','fs-icon-flag-checkered' => 'flag-checkered','fs-icon-terminal' => 'terminal','fs-icon-code' => 'code','fs-icon-reply-all' => 'reply-all','fs-icon-star-half-empty' => 'star-half-empty','fs-icon-location-arrow' => 'location-arrow','fs-icon-crop' => 'crop','fs-icon-code-fork' => 'code-fork','fs-icon-unlink' => 'unlink','fs-icon-question' => 'question','fs-icon-info' => 'info','fs-icon-exclamation' => 'exclamation','fs-icon-superscript' => 'superscript','fs-icon-subscript' => 'subscript','fs-icon-eraser' => 'eraser','fs-icon-puzzle-piece' => 'puzzle-piece','fs-icon-microphone' => 'microphone','fs-icon-microphone-slash' => 'microphone-slash','fs-icon-shield' => 'shield','fs-icon-calendar-o' => 'calendar','fs-icon-fire-extinguisher' => 'fire-extinguisher','fs-icon-rocket' => 'rocket','fs-icon-maxcdn' => 'maxcdn','fs-icon-chevron-circle-left' => 'chevron-circle-left','fs-icon-chevron-circle-right' => 'chevron-circle-right','fs-icon-chevron-circle-up' => 'chevron-circle-up','fs-icon-chevron-circle-down' => 'chevron-circle-down','fs-icon-html5' => 'html5','fs-icon-css3' => 'css3','fs-icon-anchor' => 'anchor','fs-icon-unlock-alt' => 'unlock-alt','fs-icon-bullseye' => 'bullseye','fs-icon-ellipsis-h' => 'ellipsis-horizontal','fs-icon-ellipsis-v' => 'ellipsis-vertical','fs-icon-rss-square' => 'rss-square','fs-icon-play-circle' => 'play-circle','fs-icon-ticket' => 'ticket','fs-icon-minus-square' => 'minus-square','fs-icon-minus-square-o' => 'minus-square-old','fs-icon-level-up' => 'level-up','fs-icon-level-down' => 'level-down','fs-icon-check-square' => 'check-square','fs-icon-pencil-square' => 'pencil-square','fs-icon-external-link-square' => 'external-link-square','fs-icon-share-square' => 'share-square','fs-icon-compass' => 'compass','fs-icon-toggle-down' => 'toggle-down','fs-icon-toggle-up' => 'toggle-up','fs-icon-toggle-right' => 'toggle-right','fs-icon-euro' => 'euro','fs-icon-gbp' => 'gbp','fs-icon-dollar' => 'dollar','fs-icon-rupee' => 'rupee','fs-icon-cny' => 'cny','fs-icon-ruble' => 'ruble','fs-icon-won' => 'won','fs-icon-bitcoin' => 'bitcoin','fs-icon-file' => 'file','fs-icon-file-text' => 'file-text','fs-icon-sort-alpha-asc' => 'sort-alpha-asc','fs-icon-sort-alpha-desc' => 'sort-alpha-desc','fs-icon-sort-amount-asc' => 'sort-amount-asc','fs-icon-sort-amount-desc' => 'sort-amount-desc','fs-icon-sort-numeric-asc' => 'sort-numeric-asc','fs-icon-sort-numeric-desc' => 'sort-numeric-desc','fs-icon-thumbs-up' => 'thumbs-up','fs-icon-thumbs-down' => 'thumbs-down','fs-icon-youtube-square' => 'youtube-square','fs-icon-youtube' => 'youtube','fs-icon-xing' => 'xing','fs-icon-xing-square' => 'xing-square','fs-icon-youtube-play' => 'youtube-play','fs-icon-dropbox' => 'dropbox','fs-icon-stack-overflow' => 'stack-overflow','fs-icon-instagram' => 'instagram','fs-icon-flickr' => 'flickr','fs-icon-adn' => 'adn','fs-icon-bitbucket' => 'bitbucket','fs-icon-bitbucket-square' => 'bitbucket-square','fs-icon-tumblr' => 'tumblr','fs-icon-tumblr-square' => 'tumblr-square','fs-icon-long-arrow-down' => 'long-arrow-down','fs-icon-long-arrow-up' => 'long-arrow-up','fs-icon-long-arrow-left' => 'long-arrow-left','fs-icon-long-arrow-right' => 'long-arrow-right','fs-icon-apple' => 'apple','fs-icon-windows' => 'windows','fs-icon-android' => 'android','fs-icon-linux' => 'linux','fs-icon-dribbble' => 'dribbble','fs-icon-skype' => 'skype','fs-icon-foursquare' => 'foursquare','fs-icon-trello' => 'trello','fs-icon-female' => 'female','fs-icon-male' => 'male','fs-icon-gittip' => 'gittip','fs-icon-sun-o' => 'sun','fs-icon-moon-o' => 'moon','fs-icon-archive' => 'archive','fs-icon-bug' => 'bug','fs-icon-vk' => 'vk','fs-icon-weibo' => 'weibo','fs-icon-renren' => 'renren','fs-icon-pagelines' => 'pagelines','fs-icon-stack-exchange' => 'stack-exchange','fs-icon-arrow-circle-o-right' => 'arrow-circle-right','fs-icon-arrow-circle-o-left' => 'arrow-circle-left','fs-icon-toggle-left' => 'toggle-left','fs-icon-dot-circle-o' => 'dot-circle','fs-icon-wheelchair' => 'wheelchair','fs-icon-vimeo-square' => 'vimeo-square','fs-icon-turkish-lira' => 'turkish-lira','fs-icon-plus-square-o' => 'plus-square','fs-icon-space-shuttle' => 'space-shuttle','fs-icon-slack' => 'slack','fs-icon-envelope-square' => 'envelope-square','fs-icon-wordpress' => 'wordpress','fs-icon-openid' => 'openid','fs-icon-institution' => 'institution','fs-icon-mortar-board' => 'mortar-board','fs-icon-yahoo' => 'yahoo','fs-icon-google' => 'google','fs-icon-reddit' => 'reddit','fs-icon-reddit-square' => 'reddit-square','fs-icon-stumbleupon-circle' => 'stumbleupon-circle','fs-icon-stumbleupon' => 'stumbleupon','fs-icon-delicious' => 'delicious','fs-icon-digg' => 'digg','fs-icon-pied-piper' => 'pied-piper','fs-icon-pied-piper-alt' => 'pied-piper-alt','fs-icon-drupal' => 'drupal','fs-icon-joomla' => 'joomla','fs-icon-language' => 'language','fs-icon-fax' => 'fax','fs-icon-building' => 'building','fs-icon-child' => 'child','fs-icon-paw' => 'paw','fs-icon-spoon' => 'spoon','fs-icon-cube' => 'cube','fs-icon-cubes' => 'cubes','fs-icon-behance' => 'behance','fs-icon-behance-square' => 'behance-square','fs-icon-steam' => 'steam','fs-icon-steam-square' => 'steam-square','fs-icon-recycle' => 'recycle','fs-icon-automobile' => 'automobile','fs-icon-taxi' => 'taxi','fs-icon-tree' => 'tree','fs-icon-spotify' => 'spotify','fs-icon-deviantart' => 'deviantart','fs-icon-soundcloud' => 'soundcloud','fs-icon-database' => 'database','fs-icon-file-pdf-o' => 'file-pdf','fs-icon-file-word-o' => 'file-word','fs-icon-file-excel-o' => 'file-excel','fs-icon-file-powerpoint-o' => 'file-powerpoint','fs-icon-file-photo-o' => 'file-photo','fs-icon-file-zip-o' => 'file-zip','fs-icon-file-sound-o' => 'file-sound','fs-icon-file-movie-o' => 'file-movie','fs-icon-file-code-o' => 'file-code','fs-icon-vine' => 'vine','fs-icon-codepen' => 'codepen','fs-icon-jsfiddle' => 'jsfiddle','fs-icon-life-ring' => 'life-ring','fs-icon-circle-o-notch' => 'circle-notch','fs-icon-rebel' => 'rebel','fs-icon-empire' => 'empire','fs-icon-git-square' => 'git-square','fs-icon-git' => 'git','fs-icon-hacker-news' => 'hacker-news','fs-icon-tencent-weibo' => 'tencent-weibo','fs-icon-qq' => 'qq','fs-icon-wechat' => 'wechat','fs-icon-send' => 'send','fs-icon-paper-plane-o' => 'paper-plane','fs-icon-history' => 'history','fs-icon-genderless' => 'genderless','fs-icon-header' => 'header','fs-icon-paragraph' => 'paragraph','fs-icon-sliders' => 'sliders','fs-icon-share-alt' => 'share-alt','fs-icon-share-alt-square' => 'share-alt-square','fs-icon-bomb' => 'bomb','fs-icon-soccer-ball-o' => 'soccer-ball','fs-icon-tty' => 'tty','fs-icon-binoculars' => 'binoculars','fs-icon-plug' => 'plug','fs-icon-slideshare' => 'slideshare','fs-icon-twitch' => 'twitch','fs-icon-yelp' => 'yelp','fs-icon-newspaper-o' => 'newspaper','fs-icon-wifi' => 'wifi','fs-icon-calculator' => 'calculator','fs-icon-paypal' => 'paypal','fs-icon-google-wallet' => 'google-wallet','fs-icon-cc-visa' => 'visa','fs-icon-cc-mastercard' => 'mastercard','fs-icon-cc-discover' => 'discover','fs-icon-cc-amex' => 'amex','fs-icon-cc-paypal' => 'paypal','fs-icon-cc-stripe' => 'stripe','fs-icon-bell-slash' => 'bell-slash','fs-icon-bell-slash-o' => 'bell-slash-old','fs-icon-trash' => 'trash','fs-icon-copyright' => 'copyright','fs-icon-at' => 'at','fs-icon-eyedropper' => 'eyedropper','fs-icon-paint-brush' => 'paint-brush','fs-icon-birthday-cake' => 'birthday-cake','fs-icon-area-chart' => 'area-chart','fs-icon-pie-chart' => 'pie-chart','fs-icon-line-chart' => 'line-chart','fs-icon-lastfm' => 'lastfm','fs-icon-lastfm-square' => 'lastfm-square','fs-icon-toggle-off' => 'toggle-off','fs-icon-toggle-on' => 'toggle-on','fs-icon-bicycle' => 'bicycle','fs-icon-bus' => 'bus','fs-icon-ioxhost' => 'ioxhost','fs-icon-angellist' => 'angellist','fs-icon-cc' => 'cc','fs-icon-shekel' => 'shekel','fs-icon-meanpath' => 'meanpath','fs-icon-buysellads' => 'buysellads','fs-icon-connectdevelop' => 'connectdevelop','fs-icon-dashcube' => 'dashcube','fs-icon-forumbee' => 'forumbee','fs-icon-leanpub' => 'leanpub','fs-icon-sellsy' => 'sellsy','fs-icon-shirtsinbulk' => 'shirtsinbulk','fs-icon-simplybuilt' => 'simplybuilt','fs-icon-skyatlas' => 'skyatlas','fs-icon-cart-plus' => 'cart-plus','fs-icon-cart-arrow-down' => 'cart-arrow-down','fs-icon-diamond' => 'diamond','fs-icon-ship' => 'ship','fs-icon-user-secret' => 'user-secret','fs-icon-motorcycle' => 'motorcycle','fs-icon-street-view' => 'street-view','fs-icon-heartbeat' => 'heartbeat','fs-icon-venus' => 'venus','fs-icon-mars' => 'mars','fs-icon-mercury' => 'mercury','fs-icon-transgender' => 'transgender','fs-icon-transgender-alt' => 'transgender-alt','fs-icon-venus-double' => 'venus-double','fs-icon-mars-double' => 'mars-double','fs-icon-venus-mars' => 'venus-mars','fs-icon-mars-stroke' => 'mars-stroke','fs-icon-mars-stroke-v' => 'mars-stroke-vertical','fs-icon-mars-stroke-h' => 'mars-stroke-horizontal','fs-icon-neuter' => 'neuter','fs-icon-facebook-official' => 'facebook-official','fs-icon-pinterest-p' => 'pinterest','fs-icon-whatsapp' => 'whatsapp','fs-icon-server' => 'server','fs-icon-user-plus' => 'user-plus','fs-icon-user-times' => 'user-times','fs-icon-hotel' => 'hotel','fs-icon-viacoin' => 'viacoin','fs-icon-train' => 'train','fs-icon-subway' => 'subway','fs-icon-medium' => 'medium');
	
	if($item == 'popu') {
		$mod_text_code = 'frozr_popu_text';
		$mod_color_code = 'frozr_popu_color';
		$mod_txt_color_code = 'frozr_popu_txt_color';
		$mod_icon_code = 'frozr_popu_icon';
		$default_text = __('Popular Restaurants','frozr');
		$default_color = 'rgba(251, 23, 23, 0.7)';
		$default_txt_color = '#fff';
		$default_icon = 'fs-icon-star';
		$permalink = home_url('/restaurants/?by=top');
	} elseif ($item == 'reco') {
		$mod_text_code = 'frozr_reco_text';
		$mod_color_code = 'frozr_reco_color';
		$mod_txt_color_code = 'frozr_reco_txt_color';
		$mod_icon_code = 'frozr_reco_icon';
		$default_text = __('Restaurants We Recommend','frozr');
		$default_color = 'rgba(65, 99, 243, 0.6)';
		$default_txt_color = '#fff';
		$default_icon = 'fs-icon-heart';
		$permalink = home_url('/restaurants/?by=recommended');
	} elseif ($item == 'vegb') {
		$mod_text_code = 'frozr_vegb_text';
		$mod_color_code = 'frozr_vegb_color';
		$mod_txt_color_code = 'frozr_vegb_txt_color';
		$mod_icon_code = 'frozr_vegb_icon';
		$default_text = __('Veg.','frozr');
		$default_color = 'rgba(44, 206, 44, 0.72)';
		$default_txt_color = '#fff';
		$default_icon = 'none';
		$permalink = home_url('/restaurants/?by=veg');
	} elseif ($item == 'nonvegb') {
		$mod_text_code = 'frozr_nonvegb_text';
		$mod_color_code = 'frozr_nonvegb_color';
		$mod_txt_color_code = 'frozr_nonvegb_txt_color';
		$mod_icon_code = 'frozr_nonvegb_icon';
		$default_text = __('Non-Veg.','frozr');
		$default_color = 'rgba(245, 81, 81, 0.8)';
		$default_txt_color = '#fff';
		$default_icon = 'none';
		$permalink = home_url('/restaurants/?by=nonveg');
	} elseif ($item == 'sefb') {
		$mod_text_code = 'frozr_sefb_text';
		$mod_color_code = 'frozr_sefb_color';
		$mod_txt_color_code = 'frozr_sefb_txt_color';
		$mod_icon_code = 'frozr_sefb_icon';
		$default_text = __('Sea Food','frozr');
		$default_color = 'rgba(108, 108, 246, 0.88)';
		$default_txt_color = '#fff';
		$default_icon = 'none';
		$permalink = home_url('/restaurants/?by=sea-food');
	} elseif ($item == 'rsb_loc_link') {
		$mod_text_code = 'frozr_rsb_loc_link_text';
		$mod_color_code = 'frozr_rsb_loc_link_color';
		$mod_txt_color_code = 'frozr_rsb_loc_link_txt_color';
		$mod_icon_code = 'frozr_rsb_loc_link_icon';
		$default_text = __('Go!','frozr');
		$default_color = 'rgba(108, 108, 246, 0.88)';
		$default_txt_color = '#fff';
		$default_icon = 'none';
		$permalink = home_url('/location/') . $_COOKIE['frozr_user_location'];
	} ?>
	<div class="front_btn_wrapper">
	<a class="rest_list_btn <?php echo $item; ?>" title="<?php echo get_theme_mod($mod_text_code, $default_text); ?>" href="<?php echo $permalink; ?>"><?php if ('none' != get_theme_mod($mod_icon_code, $default_icon)) { echo '<i class="' . get_theme_mod($mod_icon_code, $default_icon) .'"></i> '; } echo get_theme_mod($mod_text_code, $default_text); ?></a>				
	<?php if (is_super_admin()) { ?>
	<a class="front_btns_edit" href="#front_edit_pop_<?php echo $item; ?>" data-transition="fade" data-rel="popup" data-position-to="window" title="<?php __('Edit this!','frozr'); ?>" ><i class="fs-icon-edit"></i></a>
	<?php } ?>
	<div id="front_edit_pop_<?php echo $item; ?>" class="common_pop" data-history="false" data-role="popup">
		<form id="frozr_front_edit_pop_<?php echo $item; ?>" action="" method="post" class="front_edit_form clearfix" data-modtype="<?php echo $item; ?>">
			<label><?php _e( 'Text:', 'frozr' ); ?>
				<input name="<?php echo $mod_text_code; ?>" value="<?php echo get_theme_mod($mod_text_code, $default_text); ?>" placeholder="<?php $default_text; ?>" minlength="5" >
			</label>
			<label><?php _e( 'Icon:', 'frozr' ); ?>
				<select name="<?php echo $mod_icon_code; ?>" data-role="none">
				<?php foreach($box_icon_array as $icons => $vl) { ?>
					<option value="<?php echo $icons; ?>" <?php selected(get_theme_mod($mod_icon_code, $default_icon), $icons); ?> ><?php echo $vl; ?></option>
				<?php } ?>
				</select>
			</label>
			<label><?php _e( 'Background Color:', 'frozr' ); ?>
				<input name="<?php echo $mod_color_code; ?>" value="<?php echo get_theme_mod($mod_color_code, $default_color); ?>" placeholder="<?php $default_color; ?>" minlength="5" >
			</label>
			<label><?php _e( 'Text Color:', 'frozr' ); ?>
				<input name="<?php echo $mod_txt_color_code; ?>" value="<?php echo get_theme_mod($mod_txt_color_code, $default_txt_color); ?>" placeholder="<?php $default_txt_color; ?>" minlength="5" >
			</label>
			
			<?php do_action('frozr_after_home_advance_search_edit_form', $item); ?>

			<input class="adv_src_edit_submit" type="submit" name="adv_src_edit_submit" value="<?php _e( 'Save', 'frozr' ); ?>" >
		</form>
	</div>
	</div>
<?php
}
//filter one header
function frozr_filter_one_header($ord,$txt='',$class='') {
	
	$cnt_btn = '';
	echo '<div class="sortable_front_boxes rsb-boxes '.$class.'" ord="srt_'.$ord.'">';
	if (is_super_admin()) { $cnt_btn = 'control_edit';}
	if ('' != $txt) {
		if (is_super_admin()) {
			echo '<div class="front_inputs_wrap"><input data-tlt="txt_'. $ord .'" class="front_inputs" value="'. $txt .'" placeholder="'. $txt .'">'. frozr_front_texts_edit_btns() .'</div>';
		}
		echo '<h2 class="lei_h2 '. $cnt_btn .'"<span>'. $txt .'</span></h2>';
	}
}
//filter one header
function frozr_filter_one_footer() {
	echo '</div>';
}
//filter two header
function frozr_filter_two_header($cls,$ord,$txt='') {
	$edit_btn = '<a href="#" class="src_adv_wrp_img" data-uploader_title="'. __('Set filter box image','frozr') .'" data-uploader_button_text="'. __('Set','frozr') .'" title="'. __('Edit Image','frozr') .'"><i class="fs-icon-edit"></i></a>';
	$cnt_btn = '';
	$clss = '';
	if ($ord == 'popu' || $ord == 'reco' || $ord == 'type') {
	$clss = 'rest_hide_title';
	}
	echo '<div class="sortable_front_boxes search_adv_wrapper '.$clss.'" ord="srt_'. $ord .'">';
		echo '<span class="src_adv_wrp_imgid ' . $cls .'" data-cls="'. $cls .'">'; if (is_super_admin()) { echo '<span class="front_edit_btns">' . $edit_btn .'</span>'; } echo '</span>';
		if ('' != $txt) {
		if (is_super_admin()) {
			echo '<div class="front_inputs_wrap"><input data-tlt="txt_'. $ord .'" class="front_inputs" value="'. $txt .'" placeholder="'. $txt .'">'. frozr_front_texts_edit_btns() .'</div>';
			$cnt_btn = 'control_edit';
		}
		echo '<h2 class="alei_h2 '. $cnt_btn .'">'. $txt .'</h2>';
		}
}
//filter two footer
function frozr_filter_two_footer() {
	echo '</div>';
}
//advance search body-home
function frozr_home_advance_search_body($xn) {
		
	if ($xn == 'cusearch') {
		frozr_filter_two_header('cusearchimg','cusearch', frozr_front_texts(2));
		frozr_cuisine_search_body();
		frozr_filter_two_footer();
	} elseif ($xn == 'restsearch') {
		frozr_filter_two_header('restsearchimg','restsearch', frozr_front_texts(1));
		frozr_resturant_search_body();
		frozr_filter_two_footer();
	} elseif ($xn == 'adlocsearch') {
		frozr_filter_two_header('adlocsearchimg','adlocsearch', frozr_front_texts(3));
		frozr_address_location_search_body();
		frozr_filter_two_footer();
	} elseif ($xn == 'popu') {
		frozr_filter_two_header('popuimg','popu', frozr_front_texts(8));
		frozr_adv_src_itm('popu');
		frozr_filter_two_footer();
	} elseif ($xn == 'reco') {
		frozr_filter_two_header('recoimg','reco', frozr_front_texts(9));
		frozr_adv_src_itm('reco');
		frozr_filter_two_footer();
	} elseif ($xn == 'type') {
		frozr_filter_two_header('typeimg','type', frozr_front_texts(10));
		echo '<div class="rest_typ_list_box">';
		frozr_adv_src_itm('vegb');
		frozr_adv_src_itm('nonvegb');
		frozr_adv_src_itm('sefb');
		echo '</div>';
		frozr_filter_two_footer();
	} elseif ($xn == 'catsearch') {
		frozr_filter_two_header('catsearchimg','catsearch', frozr_front_texts(5));
		echo do_shortcode('[product_categories number="12" parent="0"]');
		frozr_filter_two_footer();
	} elseif ($xn == 'ingsearch') {
		frozr_filter_two_header('ingsearchimg','ingsearch', frozr_front_texts(6));
		frozr_ingredient_search_body();
		frozr_filter_two_footer();
	} elseif ($xn == 'spysearch') {
		frozr_filter_two_header('spysearchimg','spysearch', frozr_front_texts(7));
		frozr_type_filter_body();
		frozr_filter_two_footer();
	} elseif ($xn == 'restd') {
		frozr_filter_two_header('restdimg','restd', frozr_front_texts(4));
		frozr_adv_src_itm('rsb_loc_link');
		frozr_filter_two_footer();
	}
}
//type filter body
function frozr_type_filter_body() {
?>

	<a class="searchveg" href="<?php echo add_query_arg( array( 'dish' => 'veg' ), wc_get_page_permalink( 'shop' ) ); ?>"><?php _e( 'Vegetarian', 'frozr' ); ?></a>
	<a class="searchnonveg" href="<?php echo add_query_arg( array( 'dish' => 'nonveg' ), wc_get_page_permalink( 'shop' ) ); ?>"><?php _e( 'Non-Veg', 'frozr' ); ?></a>
	<a class="searchspicy" href="<?php echo add_query_arg( array( 'dish' => 'spicy' ), wc_get_page_permalink( 'shop' ) ); ?>"><?php _e( 'Spicy', 'frozr' ); ?></a>
	<a class="searchvegspicy" href="<?php echo add_query_arg( array( 'dish' => 'vegspicy' ), wc_get_page_permalink( 'shop' ) ); ?>"><?php _e( 'Veg + Spicy', 'frozr' ); ?></a>
	<a class="searchnonvegspicy" href="<?php echo add_query_arg( array( 'dish' => 'nonvegspicy' ), wc_get_page_permalink( 'shop' ) ); ?>"><?php _e( 'Non-Veg + Spicy', 'frozr' ); ?></a>
	<?php do_action('frozr_after_home_advance_type_filter'); ?>

<?php
}
