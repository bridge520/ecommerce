<?php
/**
 * 商品卡片模板
 * 覆寫自 WooCommerce/templates/content-product.php
 */

defined( 'ABSPATH' ) || exit;

global $product;

// 確保 $product 是有效物件
if ( ! is_a( $product, 'WC_Product' ) ) {
    return;
}

// 取得商品標籤資訊
$is_new     = $product->is_featured();
$is_digital = $product->is_downloadable() || $product->is_virtual();
$is_on_sale = $product->is_on_sale();
?>

<li <?php post_class( 'studio-product-card' ); ?>>

    <a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="studio-product-card__image-link">
        <div class="studio-product-card__image-wrap">

            <?php echo $product->get_image( 'woocommerce_thumbnail', [ 'class' => 'studio-product-card__img' ] ); ?>

            <?php /* 標籤 */ ?>
            <div class="studio-product-card__badges">
                <?php if ( $is_new ) : ?>
                    <span class="studio-badge studio-badge--new">New</span>
                <?php endif; ?>
                <?php if ( $is_digital ) : ?>
                    <span class="studio-badge studio-badge--digital">Digital</span>
                <?php endif; ?>
                <?php if ( $is_on_sale ) : ?>
                    <span class="studio-badge studio-badge--sale">Sale</span>
                <?php endif; ?>
            </div>

        </div>
    </a>

    <div class="studio-product-card__info">

        <a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="studio-product-card__title-link">
            <div class="studio-product-card__name"><?php echo esc_html( $product->get_name() ); ?></div>
        </a>

        <div class="studio-product-card__bottom">
            <div class="studio-product-card__price">
                <?php echo wp_kses_post( $product->get_price_html() ); ?>
            </div>

            <?php if ( $product->is_in_stock() ) : ?>
                <?php
                $add_to_cart_url = $product->is_type( 'simple' )
                    ? esc_url( $product->add_to_cart_url() )
                    : esc_url( $product->get_permalink() );
                $add_to_cart_text = $product->is_type( 'simple' ) ? '+ 加入' : '選購';
                ?>
                <a href="<?php echo $add_to_cart_url; ?>"
                   class="studio-product-card__add-btn <?php echo $product->is_type( 'simple' ) ? 'add_to_cart_button ajax_add_to_cart' : ''; ?>"
                   data-product_id="<?php echo esc_attr( $product->get_id() ); ?>"
                   data-product_sku="<?php echo esc_attr( $product->get_sku() ); ?>"
                   aria-label="<?php echo esc_attr( sprintf( __( '將「%s」加入購物車', 'astra-child' ), $product->get_name() ) ); ?>">
                    <?php echo esc_html( $add_to_cart_text ); ?>
                </a>
            <?php else : ?>
                <span class="studio-product-card__sold-out">售完</span>
            <?php endif; ?>
        </div>

    </div>

</li>
