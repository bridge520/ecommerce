<?php
/**
 * Astra Child Theme Functions
 */

// 載入父佈景主題樣式
add_action( 'wp_enqueue_scripts', 'astra_child_enqueue_styles' );
function astra_child_enqueue_styles() {
    wp_enqueue_style(
        'astra-child-style',
        get_stylesheet_uri(),
        array( 'astra-theme-css' ),
        wp_get_theme()->get( 'Version' )
    );
}

// ============================================================
// 效能優化：移除不必要的前端資源
// ============================================================

// 移除 WordPress Emoji 腳本與樣式（節省 HTTP request）
add_action( 'init', 'astra_child_disable_emojis' );
function astra_child_disable_emojis() {
    remove_action( 'wp_head',             'print_emoji_detection_script', 7 );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'wp_print_styles',     'print_emoji_styles' );
    remove_action( 'admin_print_styles',  'print_emoji_styles' );
    remove_filter( 'the_content_feed',    'wp_staticize_emoji' );
    remove_filter( 'comment_text_rss',    'wp_staticize_emoji' );
    remove_filter( 'wp_mail',             'wp_staticize_emoji_for_email' );
    add_filter( 'tiny_mce_plugins',       'astra_child_disable_emojis_tinymce' );
    add_filter( 'wp_resource_hints',      'astra_child_disable_emojis_dns_prefetch', 10, 2 );
}
function astra_child_disable_emojis_tinymce( $plugins ) {
    return is_array( $plugins ) ? array_diff( $plugins, array( 'wpemoji' ) ) : array();
}
function astra_child_disable_emojis_dns_prefetch( $urls, $relation_type ) {
    if ( 'dns-prefetch' === $relation_type ) {
        $urls = array_filter( $urls, function( $url ) {
            return strpos( $url, 'https://s.w.org/images/core/emoji/' ) === false;
        } );
    }
    return $urls;
}

// 移除 oEmbed 對外探索連結（減少 <head> 雜訊）
remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
remove_action( 'wp_head', 'wp_oembed_add_host_js' );

// 移除 RSD 與 WLW Manifest 連結
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );

// 移除 WordPress 版本資訊（安全考量）
remove_action( 'wp_head', 'wp_generator' );

// 調整 Heartbeat API 頻率（降低後台 AJAX 請求）
add_filter( 'heartbeat_settings', 'astra_child_heartbeat_settings' );
function astra_child_heartbeat_settings( $settings ) {
    $settings['interval'] = 120; // 預設 60 秒，改為 120 秒
    return $settings;
}

// 移除過大的預設圖片尺寸（1536w、2048w 對小型網站無必要）
add_filter( 'intermediate_image_sizes_advanced', 'astra_child_remove_extra_image_sizes' );
function astra_child_remove_extra_image_sizes( $sizes ) {
    unset( $sizes['1536x1536'] );
    unset( $sizes['2048x2048'] );
    return $sizes;
}
