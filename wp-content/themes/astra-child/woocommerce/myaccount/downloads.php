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
		<?php esc_html_e( '目前尚無可下載的商品。', 'astra-child' ); ?>
	</div>

<?php else : ?>

	<table class="woocommerce-table woocommerce-MyAccount-downloads shop_table shop_table_responsive">
		<thead>
			<tr>
				<th scope="col"><?php esc_html_e( '商品', 'astra-child' ); ?></th>
				<th scope="col"><?php esc_html_e( '剩餘下載次數', 'astra-child' ); ?></th>
				<th scope="col"><?php esc_html_e( '下載期限', 'astra-child' ); ?></th>
				<th scope="col"><?php esc_html_e( '下載', 'astra-child' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $downloads as $download ) : ?>
			<tr>
				<td><?php echo esc_html( $download['product_name'] ); ?></td>
				<td>
					<?php
					if ( '' === $download['downloads_remaining'] ) {
						esc_html_e( '無限制', 'astra-child' );
					} else {
						echo esc_html( $download['downloads_remaining'] );
					}
					?>
				</td>
				<td>
					<?php
					if ( empty( $download['access_expires'] ) || '0000-00-00 00:00:00' === $download['access_expires'] ) {
						esc_html_e( '無期限', 'astra-child' );
					} else {
						echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $download['access_expires'] ) ) );
					}
					?>
				</td>
				<td>
					<?php // $download['download_url'] is WooCommerce-generated and nonce-signed — do not construct manually. ?>
					<a href="<?php echo esc_url( $download['download_url'] ); ?>" class="woocommerce-MyAccount-downloads-file button alt">
						<?php esc_html_e( '下載', 'astra-child' ); ?>
					</a>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

<?php endif; ?>
