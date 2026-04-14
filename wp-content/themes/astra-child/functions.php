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
    // 載入 Google Fonts：Barlow Condensed + Playfair Display + Noto Sans TC
    wp_enqueue_style(
        'astra-child-google-fonts',
        'https://fonts.googleapis.com/css2?family=Barlow+Condensed:ital,wght@0,700;0,900;1,700&family=Playfair+Display:ital,wght@0,700;1,700&family=Noto+Sans+TC:wght@300;400;500&display=swap',
        array(),
        null
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

// ============================================================
// 安全強化
// ============================================================

// 關閉 XML-RPC（防止暴力破解與 DDoS 攻擊）
add_filter( 'xmlrpc_enabled', '__return_false' );

// 移除 XML-RPC 相關的 <head> 連結
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );

// 防止使用者列舉（?author=N 探測帳號）
add_action( 'template_redirect', 'astra_child_block_author_enum' );
function astra_child_block_author_enum() {
    if ( ! is_admin() && isset( $_GET['author'] ) ) {
        wp_redirect( home_url( '/' ), 301 );
        exit;
    }
}

// 登入錯誤訊息模糊化（不透露帳號是否存在）
add_filter( 'login_errors', 'astra_child_obscure_login_errors' );
function astra_child_obscure_login_errors() {
    return '帳號或密碼錯誤，請重試。';
}

// 移除登入頁面的 WordPress 版本 hint
add_filter( 'login_headerurl', function() { return home_url(); } );

// 停用檔案編輯器（防止後台直接編輯主題/插件程式碼）
if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) {
    define( 'DISALLOW_FILE_EDIT', true );
}
