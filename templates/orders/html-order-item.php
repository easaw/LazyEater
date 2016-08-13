<?php
/**
 * Shows an order item
 *
 * @var object $item The item being displayed
 * @var int $item_id The id of the item being displayed
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<tr class="order_item <?php echo apply_filters( 'woocommerce_admin_html_order_item_class', ( ! empty( $class ) ? $class : '' ), $item ); ?>" data-order_item_id="<?php echo $item_id; ?>">
	<?php if (!isset( $_GET['print'] )) { ?>
	<td class="order_thumb">
		<?php if ( $_product ) : ?>
			<div class="order_tips"><?php echo apply_filters( 'woocommerce_admin_order_item_thumbnail', $_product->get_image( 'shop_thumbnail', array( 'title' => '' ) ), $item_id, $item ); ?></div>
		<?php else : ?>
			<?php echo wc_placeholder_img( 'shop_thumbnail' ); ?>
		<?php endif; ?>
	</td>
	<?php } ?>
	<td class="order_name">

		<?php echo ( $_product && $_product->get_sku() ) ? esc_html( $_product->get_sku() ) . ' &ndash; ' : ''; ?>
		<?php echo esc_html( $item['name'] ); ?>
		<?php echo '&nbsp;|&nbsp;<strong>' . __( 'Product ID:', 'frozr' ) . absint( $item['product_id'] ) . '</strong>';

				if ( ! empty( $item['variation_id'] ) && 'product_variation' === get_post_type( $item['variation_id'] ) ) {
					echo '&nbsp;|&nbsp;<strong>' . __( 'Variation ID:', 'frozr' ) . absint( $item['variation_id'] ) . '</strong> ';
				} elseif ( ! empty( $item['variation_id'] ) ) {
					echo '&nbsp;|&nbsp;<strong>' . __( 'Variation ID:', 'frozr' ) . absint( $item['variation_id'] ) . ' (' . __( 'No longer exists', 'frozr' ) . ')</strong> ';
				}

				if ( $_product && $_product->get_sku() ) {
					echo '&nbsp;|&nbsp;<strong>' . __( 'Product SKU:', 'frozr' ) . esc_html( $_product->get_sku() ) .'</strong> ';
				}

				if ( $_product && isset($_product->variation_data) ) {
					echo '&nbsp;|&nbsp;' . wc_get_formatted_variation( $_product->variation_data, true );
				}
		?>
		<?php do_action( 'woocommerce_before_order_itemmeta', $item_id, $item, $_product ) ?>

		<div class="order_meta_view">
			<?php
				global $wpdb;

				if ( $metadata = $order->has_meta( $item_id ) ) {
					echo '<table cellspacing="0" class="display_meta">';
					foreach ( $metadata as $meta ) {

						// Skip hidden core fields
						if ( in_array( $meta['meta_key'], apply_filters( 'woocommerce_hidden_order_itemmeta', array(
							'_qty',
							'_tax_class',
							'_product_id',
							'_variation_id',
							'_line_subtotal',
							'_line_subtotal_tax',
							'_line_total',
							'_line_tax',
						) ) ) ) {
							continue;
						}

						// Skip serialised meta
						if ( is_serialized( $meta['meta_value'] ) ) {
							continue;
						}

						// Get attribute data
						if ( taxonomy_exists( wc_sanitize_taxonomy_name( $meta['meta_key'] ) ) ) {
							$term               = get_term_by( 'slug', $meta['meta_value'], wc_sanitize_taxonomy_name( $meta['meta_key'] ) );
							$meta['meta_key']   = wc_attribute_label( wc_sanitize_taxonomy_name( $meta['meta_key'] ) );
							$meta['meta_value'] = isset( $term->name ) ? $term->name : $meta['meta_value'];
						} else {
							$meta['meta_key']   = apply_filters( 'woocommerce_attribute_label', wc_attribute_label( $meta['meta_key'], $_product ), $meta['meta_key'] );
						}

						echo '<tr><th>' . wp_kses_post( rawurldecode( $meta['meta_key'] ) ) . ':</th><td>' . wp_kses_post( wpautop( make_clickable( rawurldecode( $meta['meta_value'] ) ) ) ) . '</td></tr>';
					}
					echo '</table>';
				}
			?>
		</div>

		<?php do_action( 'woocommerce_after_order_itemmeta', $item_id, $item, $_product ) ?>

	</td>

	<?php do_action( 'woocommerce_admin_order_item_values', $_product, $item, absint( $item_id ) ); ?>

	<td class="item_cost" width="1%">
		<div class="order_view">
			<?php
				if ( isset( $item['line_total'] ) ) {
					if ( isset( $item['line_subtotal'] ) && $item['line_subtotal'] != $item['line_total'] ) {
						echo '<del>' . wc_price( $order->get_item_subtotal( $item, false, true ), array( 'currency' => $order->get_order_currency() ) ) . '</del> ';
					}
					echo wc_price( $order->get_item_total( $item, false, true ), array( 'currency' => $order->get_order_currency() ) );
				}
			?>
		</div>
	</td>

	<td class="order_quantity" width="1%">
		<div class="order_view">
			<?php
				echo ( isset( $item['qty'] ) ) ? esc_html( $item['qty'] ) : '';

				if ( $refunded_qty = $order->get_qty_refunded_for_item( $item_id ) ) {
					echo '<small class="refunded">-' . $refunded_qty . '</small>';
				}
			?>
		</div>
	</td>

	<td class="line_cost" width="1%">
		<div class="order_view">
			<?php
				if ( isset( $item['line_total'] ) ) {
					if ( isset( $item['line_subtotal'] ) && $item['line_subtotal'] != $item['line_total'] ) {
						echo '<del>' . wc_price( $item['line_subtotal'], array( 'currency' => $order->get_order_currency() ) ) . '</del> ';
					}
					echo wc_price( $item['line_total'], array( 'currency' => $order->get_order_currency() ) );
				}

				if ( $refunded = $order->get_total_refunded_for_item( $item_id ) ) {
					echo '<small class="refunded">-' . wc_price( $refunded, array( 'currency' => $order->get_order_currency() ) ) . '</small>';
				}
			?>
		</div>
	</td>

	<?php
		if ( empty( $legacy_order ) && wc_tax_enabled() ) :
			$line_tax_data = isset( $item['line_tax_data'] ) ? $item['line_tax_data'] : '';
			$tax_data      = maybe_unserialize( $line_tax_data );

			foreach ( $order_taxes as $tax_item ) :
				$tax_item_id       = $tax_item['rate_id'];
				$tax_item_total    = isset( $tax_data['total'][ $tax_item_id ] ) ? $tax_data['total'][ $tax_item_id ] : '';
				$tax_item_subtotal = isset( $tax_data['subtotal'][ $tax_item_id ] ) ? $tax_data['subtotal'][ $tax_item_id ] : '';

				?>
					<td class="line_tax" width="1%">
						<div class="order_view">
							<?php
								if ( '' != $tax_item_total ) {
									if ( isset( $tax_item_subtotal ) && $tax_item_subtotal != $tax_item_total ) {
										echo '<del>' . wc_price( wc_round_tax_total( $tax_item_subtotal ), array( 'currency' => $order->get_order_currency() ) ) . '</del> ';
									}

									echo wc_price( wc_round_tax_total( $tax_item_total ), array( 'currency' => $order->get_order_currency() ) );
								} else {
									echo '&ndash;';
								}

								if ( $refunded = $order->get_tax_refunded_for_item( $item_id, $tax_item_id ) ) {
									echo '<small class="refunded">-' . wc_price( $refunded, array( 'currency' => $order->get_order_currency() ) ) . '</small>';
								}
							?>
						</div>
					</td>
				<?php
			endforeach;
		endif;
	?>
</tr>