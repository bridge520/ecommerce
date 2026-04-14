<?php
/**
 * My Account - Downloads
 * 覆寫 WooCommerce 下載頁面，加入多語系支援
 */
defined( 'ABSPATH' ) || exit;

$heading = apply_filters( 'woocommerce_endpoint_downloads_title', __( '我的數位商品', 'astra-child' ) );
?>

<h2><?php echo esc_html( $heading ); ?></h2>

<?php if ( empty( $downloads ) ) : ?>

	<div class="woocommerce-Message woocommerce-Message--info woocommerce-info">
		<?php echo esc_html( __( '目前尚無可下載的商品。', 'astra-child' ) ); ?>
	</div>

<?php else : ?>

	<table class="woocommerce-table woocommerce-MyAccount-downloads shop_table shop_table_responsive">
		<thead>
			<tr>
				<th><?php echo esc_html( __( '商品', 'astra-child' ) ); ?></th>
				<th><?php echo esc_html( __( '剩餘下載次數', 'astra-child' ) ); ?></th>
				<th><?php echo esc_html( __( '下載期限', 'astra-child' ) ); ?></th>
				<th><?php echo esc_html( __( '下載', 'astra-child' ) ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $downloads as $download ) : ?>
			<tr>
				<td><?php echo esc_html( $download['product_name'] ); ?></td>
				<td>
					<?php
					if ( '' === $download['downloads_remaining'] ) {
						echo esc_html( __( '無限制', 'astra-child' ) );
					} else {
						echo esc_html( $download['downloads_remaining'] );
					}
					?>
				</td>
				<td>
					<?php
					if ( empty( $download['access_expires'] ) ) {
						echo esc_html( __( '無期限', 'astra-child' ) );
					} else {
						echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $download['access_expires'] ) ) );
					}
					?>
				</td>
				<td>
					<a href="<?php echo esc_url( $download['download_url'] ); ?>" class="woocommerce-MyAccount-downloads-file button alt">
						<?php echo esc_html( __( '下載', 'astra-child' ) ); ?>
					</a>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

<?php endif; ?>
