<?php
/**
 * The Template for displaying a single restaurants.
 *
 * @package FrozrCoreLibrary
 * @subpackage Frozrmarketlibrary
 */
 
require_once FROZR_WOO_TMP . '/restaurant/restaurant-header.php'; ?>

<div id="primary_store" class="content-area">

	<?php do_action('frozr_before_restaurant_store'); ?>

    <div class="site-content store-page-wrap woocommerce transitions-enabled masonry js-masonry" data-masonry-options='{ "isAnimated": true, "itemSelector": ".seller-items", "isOriginLeft": <?php echo frozr_theme_layout(); ?> }'>

		<?php frozr_store_loop($store_user->ID); ?>

    </div><!-- #content .site-content -->

	<?php do_action('frozr_after_restaurant_store'); ?>

</div><!-- #primary .content-area -->

<?php get_footer(); ?>
