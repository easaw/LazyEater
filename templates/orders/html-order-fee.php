<?php
/**
 * Shows an order item fee
 *
 * @var object $item The item being displayed
 * @var int $item_id The id of the item being displayed
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<tr class="order_fee <?php echo ( ! empty( $class ) ) ? $class : ''; ?>">
	<?php if (!isset( $_GET['print'] )) { ?>
	<td class="order_thumb"><div></div></td>
	<?php } ?>
	<td class="order_name">
		<div class="order_view">
			<?php echo ! empty( $item['name'] ) ? esc_html( $item['name'] ) : __( 'Fee', 'frozr' ); ?>
		</div>
	</td>

	<td class="line_cost" colspan="3">
		<div class="order_view">
			<?php
				echo ( isset( $item['line_total'] ) ) ? wc_price( wc_round_tax_total( $item['line_total'] ) ) : '';

				if ( $refunded = $order->get_total_refunded_for_item( $item_id, 'fee' ) ) {
					echo '<small class="order_refunded">-' . wc_price( $refunded ) . '</small>';
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
				?>
					<td class="line_tax" width="1%">
						<div class="order_view">
							<?php
								echo ( '' != $tax_item_total ) ? wc_price( wc_round_tax_total( $tax_item_total ) ) : '&ndash;';

								if ( $refunded = $order->get_tax_refunded_for_item( $item_id, $tax_item_id, 'fee' ) ) {
									echo '<small class="order_refunded">-' . wc_price( $refunded ) . '</small>';
								}
							?>
						</div>
					</td>

				<?php
			endforeach;
		endif;
	?>
</tr>