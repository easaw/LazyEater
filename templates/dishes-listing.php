<?php
/**
* Products - Listing
*
* @package FrozrCoreLibrary
* @subpackage FrozrmarketTemplates
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

get_header();
?>

<?php frozr_get_template( FROZR_WOO_TMP . '/dashboard-nav.php', array( 'active_menu' => 'dishes', 'title' => __('Dishes','frozr'), 'desc' => __('Your menu dishes list.','frozr') ) ); ?>

	<div id="products_list" class="content-area-product-list">

		<div class="dashboard-widgets">

			<?php do_action( 'frozr_before_listing_product' ); ?>

			<div class="product-listing-header">
				<?php frozr_product_listing_status_filter(); ?>
			</div>

			<table class="product-listing-table ui-responsive table-stroke" data-role="table">
				<thead>
					<tr class="table_collumns">
						<th data-priority="8"><?php _e( 'Image', 'frozr' ); ?></th>
						<th data-priority="1"><?php _e( 'Name', 'frozr' ); ?></th>
						<th data-priority="4"><?php _e( 'Status', 'frozr' ); ?></th>
						<th data-priority="9"><?php _e( 'SKU', 'frozr' ); ?></th>
						<th data-priority="5"><?php _e( 'Stock', 'frozr' ); ?></th>
						<th data-priority="2"><?php _e( 'Price', 'frozr' ); ?></th>
						<th data-priority="7"><?php _e( 'Type', 'frozr' ); ?></th>
						<th data-priority="3"><?php _e( 'Views', 'frozr' ); ?></th>
						<th data-priority="6"><?php _e( 'Date', 'frozr' ); ?></th>

						<?php do_action('frozr_after_dishes_listing_table_header'); ?>

					</tr>
				</thead>
				<tbody>
					<?php
					global $post, $product;
					$paged = (get_query_var( 'paged' )) ? get_query_var( 'paged' ) : 1;
					$post_statuses = apply_filters('frozr_dishes_listing_post_status',array('publish', 'draft', 'pending'));
					$args = apply_filters('frozr_dishes_listing_post_args',array(
						'post_type' => 'product',
						'post_status' => $post_statuses,
						'posts_per_page' => 10,
						'author' => get_current_user_id(),
						'orderby' => 'post_date',
						'order' => 'DESC',
						'paged' => $paged
					));

					if ( isset( $_GET['post_status']) && in_array( $_GET['post_status'], $post_statuses ) ) {
						$args['post_status'] = wc_clean($_GET['post_status']);
					}

					$original_post = $post;
					$product_query = new WP_Query( apply_filters( 'frozr_product_listing_query', $args ) );

					if ( $product_query->have_posts() ) {
						while ($product_query->have_posts()) {
							$product_query->the_post();

							$tr_class = ($post->post_status == 'pending' ) ? ' class="pending_review"' : '';
							$product = wc_get_product( $post->ID );
							?>
							<tr <?php echo $tr_class; ?>>
								<td>
									<?php $large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), array(50,50));
									echo '<div class="list_product_image" style="background: url( '.$large_image_url[0].') no-repeat center center #E1E1E1;">'; ?>
									<a href="<?php the_permalink() ?>" title="<?php _e('edit product', 'frozr'); ?>"></a>
									<?php echo '</div>'; ?>
								</td>
								<td>
									<p><a href="<?php echo frozr_edit_dish_url( $post->ID ); ?>"><?php echo $product->get_title(); ?></a></p>

									<div class="row-actions">
									<span class="edit"><a href="<?php echo frozr_edit_dish_url( $post->ID ); ?>"><?php _e( 'Edit', 'frozr' ); ?></a> | </span>
									<span class="delete_dish" data-dish="<?php echo $post->ID; ?>"><?php _e( 'Delete Permanently', 'frozr' ); ?> | </span>
									<span class="view"><a href="<?php echo get_permalink( $post->ID ); ?>" rel="permalink"><?php _e( 'View', 'frozr' ); ?></a></span>
									</div>
								</td>
								<td class="post-status">
									<label class="label <?php echo $post->post_status; ?>"><?php echo frozr_get_post_status( $post->post_status ); ?></label>
								</td>
								<td>
								<?php
									if ( $product->get_sku() ) {
									echo $product->get_sku();
									} else {
									echo '<span class="na">&ndash;</span>';
									}
									?>
								</td>
								<td>
									<?php
									if ( $product->is_in_stock() ) {
									echo '<mark class="instock">' . __( 'In stock', 'frozr' ) . '</mark>';
									} else {
									echo '<mark class="outofstock">' . __( 'Out of stock', 'frozr' ) . '</mark>';
									}

									if ( $product->managing_stock() ) :
									echo ' &times; ' . $product->get_total_stock();
									endif;
									?>
								</td>
								<td>
									<?php
									if ( $product->get_price_html() ) {
									echo $product->get_price_html();
									} else {
									echo '<span class="na">&ndash;</span>';
									}
									?>
								</td>
								<td>
									<?php
									if( $product->product_type == 'grouped' ):
									echo '<span class="product-type grouped" title="' . __( 'Grouped', 'frozr' ) . '"><i class="fs-icon-briefcase"></i></span>';
									elseif ( $product->product_type == 'external' ):
									echo '<span class="product-type external" title="' . __( 'External/Affiliate', 'frozr' ) . '"><i class="fs-icon-external-link"></i></span>';
									elseif ( $product->product_type == 'simple' ):

									if ( $product->is_virtual() ) {
									echo '<span class="product-type virtual" title="' . __( 'Virtual', 'frozr' ) . '"><i class="fs-icon-hdd"></i></span>';
									} // elseif ( $product->is_downloadable() ) {
									// echo '<span class="product-type downloadable" title="' . __( 'Downloadable', 'frozr' ) . '"><i class="fs-icon-download-alt"></i></span>';
									//}
									else {
									echo '<span class="product-type simple" title="' . __( 'Simple', 'frozr' ) . '"><i class="fs-icon-reorder"></i></span>';
									}

									elseif ( $product->product_type == 'variable' ):
									echo '<span class="product-type variable" title="' . __( 'Variable', 'frozr' ) . '"><i class="fs-icon-th"></i></span>';
									endif;
									?>
								</td>
								<td>
									<?php echo (int) get_post_meta( $post->ID, 'pageview', true ); ?>
								</td>
								<td class="post-date">
									<?php
									$t_time = get_the_time( __( 'Y/m/d g:i:s A', 'frozr' ) );
									$m_time = $post->post_date;
									$time = get_post_time( 'G', true, $post );

									$time_diff = time() - $time;

									if ( $time_diff > 0 && $time_diff < 24 * 60 * 60 ) {
										$h_time = sprintf( __( '%s ago', 'frozr' ), human_time_diff( $time ) );
									} else {
										$h_time = mysql2date( __( 'Y/m/d', 'frozr' ), $m_time );
									}

									echo '<abbr title="' . $t_time . '">' . apply_filters( 'post_date_column_time', $h_time, $post, 'date', 'all' ) . '</abbr>';
									echo '<br />';
									if ( 'publish' == $post->post_status ) {
										_e( 'Published', 'frozr' );
									} else {
										_e( 'Last Modified', 'frozr' );
									} ?>
								</td>
								
								<?php do_action('frozr_after_dishes_listing_table_body'); ?>
								
							</tr>

						<?php } ?>

					<?php } else { ?>
					<tr>
						<td colspan="9"><div class="pl_not_found"><?php _e( 'No product found', 'frozr' ); ?></div></td>
					</tr>
					<?php } ?>

				</tbody>

			</table>
			<?php
			wp_reset_postdata();

			if ( $product_query->max_num_pages > 1 ) {
			echo '<div class="pagination-wrap">';
			$page_links = paginate_links( array(
			'current' => max( 1, get_query_var( 'paged' ) ),
			'total' => $product_query->max_num_pages,
			'base' => str_replace( $post->ID, '%#%', esc_url( get_pagenum_link( $post->ID ) ) ),
			'type' => 'array',
			'prev_text' => __( '&laquo; Previous', 'frozr' ),
			'next_text' => __( 'Next &raquo;', 'frozr' )
			) );

			echo '<ul class="pagination"><li>';
			echo join("</li>\n\t<li>", $page_links);
			echo "</li>\n</ul>\n";
			echo '</div>';
			}

			do_action( 'frozr_after_listing_product' ); ?>

		</div>

	</div><!-- #products_list .content-area_product_list -->
</div><!-- #dokkan-wrapper -->

	<?php

	// action hook for placing content below #container
	frozr_belowcontainer();

	//calling sidebar
	frozr_sidebar();

	// calling footer.php
	get_footer();