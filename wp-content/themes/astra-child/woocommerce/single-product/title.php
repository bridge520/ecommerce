<?php
/**
 * 商品標題模板
 * 覆寫自 WooCommerce/templates/single-product/title.php
 */

defined( 'ABSPATH' ) || exit;

global $product;

$categories = wc_get_product_category_list( $product->get_id(), ' · ' );
?>

<?php if ( $categories ) : ?>
    <div class="studio-product-single__cats">
        <?php echo wp_kses_post( strip_tags( $categories, '<a>' ) ); ?>
        <?php if ( $product->is_featured() ) : ?>
            <span class="studio-badge studio-badge--new"><?php esc_html_e( 'New', 'astra-child' ); ?></span>
        <?php endif; ?>
    </div>
<?php endif; ?>

<h1 class="studio-product-single__title product_title entry-title">
    <?php echo wp_kses_post( get_the_title() ); ?>
</h1>
