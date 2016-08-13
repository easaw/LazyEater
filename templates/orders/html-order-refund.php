<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * @var object $refund The refund object.
 */
$who_refunded = new WP_User( $refund->post->post_author );
?>
<tr class="order_refund <?php echo ( ! empty( $class ) ) ? $class : ''; ?>">
	<?php if (!isset( $_GET['print'] )) { ?>
	<td class="order_thumb"><div></div></td>
	<?php } ?>
	<td class="order_name">
		<?php
			echo esc_attr__( 'Refund', 'frozr' ) . ' #' . absint( $refund->id ) . ' - ' . esc_attr( date_i18n( get_option( 'date_format' ) . ', ' . get_option( 'time_format' ), strtotime( $refund->post->post_date ) ) );

			if ( $who_refunded->exists() ) {
				echo ' ' . esc_attr_x( 'by', 'Ex: Refund - $date >by< $username', 'frozr' ) . ' ' . '<abbr class="refund_by" title="' . esc_attr__( 'ID: ', 'frozr' ) . absint( $who_refunded->ID ) . '">' . esc_attr( $who_refunded->display_name ) . '</abbr>' ;
			}
		?>
		<?php if ( $refund->get_refund_reason() ) : ?>
			<p class="order_description"><?php echo esc_html( $refund->get_refund_reason() ); ?></p>
		<?php endif; ?>
	</td>

	<td class="item_cost" width="1%">&nbsp;</td>
	<td class="order_quantity" width="1%">&nbsp;</td>

	<td class="line_cost" width="1%">
		<div class="order_view">
			<?php echo wc_price( '-' . $refund->get_refund_amount() ); ?>
		</div>
	</td>

	<?php if ( ( ! isset( $legacy_order ) || ! $legacy_order ) && wc_tax_enabled() ) : for ( $i = 0;  $i < count( $order_taxes ); $i++ ) : ?>

	<td class="line_tax" width="1%"></td>

	<?php endfor; endif; ?>
</tr>