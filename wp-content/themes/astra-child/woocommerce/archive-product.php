<?php
/**
 * 商品列表頁模板
 * 覆寫自 WooCommerce/templates/archive-product.php
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );
?>

<main id="primary" class="site-main studio-shop">

    <?php do_action( 'woocommerce_before_main_content' ); ?>

    <?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
        <div class="studio-shop__header">
            <div class="studio-shop__header-inner">
                <div class="studio-shop__eyebrow">
                    <?php
                    $current_term = get_queried_object();
                    echo esc_html( is_product_category() ? '商品分類' : '所有商品' );
                    ?>
                </div>
                <h1 class="studio-shop__title">
                    <?php if ( is_product_category() && $current_term ) : ?>
                        <?php echo esc_html( $current_term->name ); ?>
                    <?php else : ?>
                        All <em>Products</em>
                    <?php endif; ?>
                </h1>
                <div class="studio-shop__meta">
                    <?php if ( woocommerce_result_count() ) : ?>
                        <div class="studio-shop__count">
                            <?php woocommerce_result_count(); ?>
                        </div>
                    <?php endif; ?>
                    <div class="studio-shop__ordering">
                        <?php woocommerce_catalog_ordering(); ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php /* 分類篩選標籤（只在主 shop 頁顯示） */ ?>
    <?php if ( ! is_product_category() ) : ?>
        <div class="studio-shop__filters">
            <?php
            $shop_url   = get_permalink( wc_get_page_id( 'shop' ) );
            $categories = get_terms( [
                'taxonomy'   => 'product_cat',
                'hide_empty' => true,
                'exclude'    => [ get_option( 'default_product_cat' ) ],
                'number'     => 8,
            ] );
            $current_cat = is_product_category() ? get_queried_object() : null;
            ?>
            <a href="<?php echo esc_url( $shop_url ); ?>"
               class="studio-filter-chip <?php echo ! is_product_category() ? 'is-active' : ''; ?>">
                全部
            </a>
            <?php if ( ! is_wp_error( $categories ) ) : ?>
                <?php foreach ( $categories as $cat ) : ?>
                    <a href="<?php echo esc_url( get_term_link( $cat ) ); ?>"
                       class="studio-filter-chip <?php echo ( $current_cat && $current_cat->term_id === $cat->term_id ) ? 'is-active' : ''; ?>">
                        <?php echo esc_html( $cat->name ); ?>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if ( woocommerce_product_loop() ) : ?>

        <?php do_action( 'woocommerce_before_shop_loop' ); ?>

        <?php woocommerce_product_loop_start(); ?>

        <?php if ( wc_get_loop_prop( 'total' ) ) : ?>
            <?php while ( have_posts() ) : ?>
                <?php the_post(); ?>
                <?php do_action( 'woocommerce_shop_loop' ); ?>
                <?php wc_get_template_part( 'content', 'product' ); ?>
            <?php endwhile; ?>
        <?php endif; ?>

        <?php woocommerce_product_loop_end(); ?>

        <?php do_action( 'woocommerce_after_shop_loop' ); ?>

    <?php else : ?>

        <?php do_action( 'woocommerce_no_products_found' ); ?>

    <?php endif; ?>

    <?php do_action( 'woocommerce_after_main_content' ); ?>

</main>

<?php
get_footer( 'shop' );
