# 電商平台 UI 改版 Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** 將現有 Astra Child Theme 改版為「大膽現代 × 雜誌感」風格，套用深咖 × 陶土橘設計系統，並覆寫 WooCommerce 商品卡片、列表頁、詳細頁模板。

**Architecture:** 純 CSS + WooCommerce PHP 模板覆寫。所有樣式集中在 `style.css`（以 CSS 自訂屬性為設計 token），Google Fonts 透過 `functions.php` 正式 enqueue，不依賴任何新插件。WooCommerce 模板複製至 `astra-child/woocommerce/` 目錄後客製化，符合 WooCommerce 官方覆寫機制。

**Tech Stack:** WordPress 6.x, WooCommerce 8.x, Astra Theme, PHP 8.2, CSS Custom Properties, Google Fonts（Barlow Condensed, Playfair Display, Noto Sans TC）

**Design Spec:** `docs/superpowers/specs/2026-04-15-ecommerce-ui-redesign-design.md`

---

## 檔案結構

```
wp-content/themes/astra-child/
├── functions.php              ← 修改：新增 Google Fonts enqueue
├── style.css                  ← 改寫：全域設計 token + 所有頁面樣式
└── woocommerce/
    ├── myaccount/
    │   └── downloads.php      ← 已存在，不動
    ├── content-product.php    ← 新增：商品卡片模板覆寫
    ├── archive-product.php    ← 新增：商品列表頁模板覆寫
    └── single-product/
        ├── title.php          ← 新增：商品標題模板覆寫
        └── add-to-cart/
            └── simple.php     ← 新增：加入購物車按鈕模板覆寫
```

---

## Task 1：Google Fonts + CSS 設計 Token

**目標：** 在 `functions.php` 載入 Google Fonts，在 `style.css` 建立完整 CSS 自訂屬性與全域 reset。

**Files:**
- Modify: `wp-content/themes/astra-child/functions.php`
- Modify: `wp-content/themes/astra-child/style.css`

- [ ] **Step 1：在 functions.php 新增 Google Fonts enqueue**

  在 `astra_child_enqueue_styles()` 函式內，於 `wp_enqueue_style( 'astra-child-style', ... )` 之後新增：

  ```php
  // 載入 Google Fonts：Barlow Condensed + Playfair Display + Noto Sans TC
  wp_enqueue_style(
      'astra-child-google-fonts',
      'https://fonts.googleapis.com/css2?family=Barlow+Condensed:ital,wght@0,700;0,900;1,700&family=Playfair+Display:ital,wght@0,700;1,700&family=Noto+Sans+TC:wght@300;400;500&display=swap',
      array(),
      null
  );
  ```

- [ ] **Step 2：在 style.css 清空現有內容並寫入 Theme Header + 設計 Token**

  將 `style.css` 全部替換為以下內容（保留 Theme Header，加入 CSS 自訂屬性）：

  ```css
  /*
  Theme Name:   Astra Child
  Theme URI:    https://yourdomain.com
  Description:  Astra 子佈景主題，用於工作室電商客製化
  Author:       工作室名稱
  Author URI:   https://yourdomain.com
  Template:     astra
  Version:      1.0.0
  Text Domain:  astra-child
  */

  /* ================================================================
     DESIGN TOKENS
     ================================================================ */
  :root {
      --bg-deep:       #181614;
      --bg-card:       #242220;
      --bg-hover:      #2e2a26;
      --text-main:     #f2ede8;
      --text-muted:    #8a8480;
      --accent:        #e8845a;
      --accent-dim:    #b86540;
      --border:        #2e2a26;

      --font-condensed: 'Barlow Condensed', sans-serif;
      --font-serif:     'Playfair Display', serif;
      --font-body:      'Noto Sans TC', sans-serif;
  }

  /* ================================================================
     GLOBAL RESET
     ================================================================ */
  *, *::before, *::after {
      box-sizing: border-box;
  }

  body {
      background-color: var(--bg-deep);
      color: var(--text-main);
      font-family: var(--font-body);
      font-weight: 300;
      -webkit-font-smoothing: antialiased;
  }

  a {
      color: var(--accent);
      text-decoration: none;
      transition: color 0.2s;
  }
  a:hover {
      color: var(--accent-dim);
  }

  img {
      max-width: 100%;
      height: auto;
      display: block;
  }
  ```

- [ ] **Step 3：驗證字體載入**

  開啟 WordPress 網站首頁，按 F12 開啟 DevTools → Network → 篩選 `fonts.googleapis`。
  確認有一筆請求回應 200，且 URL 包含 `Barlow+Condensed`。

- [ ] **Step 4：PHP 語法檢查**

  ```bash
  php -l wp-content/themes/astra-child/functions.php
  ```
  預期輸出：`No syntax errors detected`

- [ ] **Step 5：Commit**

  ```bash
  git add wp-content/themes/astra-child/functions.php wp-content/themes/astra-child/style.css
  git commit -m "feat: load Google Fonts and add CSS design tokens"
  ```

---

## Task 2：導覽列 + 全域排版 CSS

**目標：** 覆寫 Astra 預設導覽列樣式，套用設計系統配色與混搭字體 Logo。

**Files:**
- Modify: `wp-content/themes/astra-child/style.css`（在 Task 1 基礎上追加）

- [ ] **Step 1：在 style.css 追加導覽列樣式**

  在 Task 1 的 CSS 末尾追加：

  ```css
  /* ================================================================
     NAVIGATION
     ================================================================ */

  /* 整體 header 背景 */
  .site-header,
  #masthead,
  .ast-header-wrap {
      background-color: var(--bg-deep) !important;
      border-bottom: 1px solid var(--border) !important;
  }

  /* Sticky nav */
  .ast-header-sticked {
      background-color: var(--bg-deep) !important;
  }

  /* 導覽連結顏色 */
  .main-header-menu .menu-item > a,
  .main-navigation .menu-item > a {
      color: var(--text-muted) !important;
      font-family: var(--font-condensed) !important;
      font-size: 11px !important;
      letter-spacing: 1px !important;
      text-transform: uppercase !important;
      transition: color 0.2s !important;
  }
  .main-header-menu .menu-item > a:hover,
  .main-navigation .menu-item > a:hover {
      color: var(--text-main) !important;
  }

  /* 購物車連結 */
  .ast-cart-menu-wrap .count,
  .woocommerce-mini-cart__buttons .button,
  header .ast-header-woo-cart {
      color: var(--accent) !important;
      border: 1px solid var(--accent) !important;
      border-radius: 2px !important;
      font-family: var(--font-condensed) !important;
      font-weight: 700 !important;
      letter-spacing: 1px !important;
      padding: 3px 10px !important;
  }

  /* Site Title（如使用文字 Logo） */
  .site-title a,
  .ast-site-identity .site-title a {
      font-family: var(--font-condensed) !important;
      font-weight: 900 !important;
      font-size: 20px !important;
      letter-spacing: -0.5px !important;
      text-transform: uppercase !important;
      color: var(--text-main) !important;
  }

  /* Site Tagline（副標，顯示斜體橘色） */
  .site-description,
  .ast-site-identity .site-description {
      font-family: var(--font-serif) !important;
      font-style: italic !important;
      font-size: 10px !important;
      color: var(--accent) !important;
      letter-spacing: 1px !important;
  }

  /* ================================================================
     FOOTER
     ================================================================ */
  .site-footer,
  #colophon {
      background-color: #0d0b0a !important;
      border-top: 1px solid var(--border) !important;
      color: var(--text-muted) !important;
  }

  .site-footer a {
      color: var(--text-muted) !important;
      font-family: var(--font-condensed) !important;
      font-size: 10px !important;
      letter-spacing: 1px !important;
      text-transform: uppercase !important;
  }
  .site-footer a:hover {
      color: var(--accent) !important;
  }

  .site-footer .ast-footer-copyright {
      font-size: 9px !important;
      color: #3a3530 !important;
      letter-spacing: 1px !important;
  }
  ```

- [ ] **Step 2：視覺驗證導覽列**

  重新整理前台網站頁面，確認：
  - 導覽列背景為深咖色 `#181614`
  - 導覽連結為灰色、大寫、字距正常
  - 購物車圖示/文字帶陶土橘框線

- [ ] **Step 3：Commit**

  ```bash
  git add wp-content/themes/astra-child/style.css
  git commit -m "feat: apply dark navigation and footer styles"
  ```

---

## Task 3：首頁 Hero + 跑馬燈 CSS

**目標：** 為首頁 Hero 區塊套用左右分割版型與全版衝擊感樣式，新增陶土橘跑馬燈條。

**Files:**
- Modify: `wp-content/themes/astra-child/style.css`（追加）

**WordPress 編輯器設定說明（實作前請先確認）：**
首頁 Hero 使用 WordPress Cover Block，需在 Block 設定「進階 > 追加 CSS 類別」欄位加入 `site-hero`。
跑馬燈使用 Custom HTML Block，插入以下 HTML（Task 3 Step 1 後執行）：
```html
<div class="marquee-strip">
  <span class="marquee-text">
    FREE SHIPPING OVER NT$1,500 &nbsp;·&nbsp; NEW ARRIVALS WEEKLY &nbsp;·&nbsp;
    DIGITAL DOWNLOADS &nbsp;·&nbsp; TAIWAN × WORLDWIDE &nbsp;·&nbsp;
    FREE SHIPPING OVER NT$1,500 &nbsp;·&nbsp; NEW ARRIVALS WEEKLY &nbsp;·&nbsp;
    DIGITAL DOWNLOADS &nbsp;·&nbsp; TAIWAN × WORLDWIDE &nbsp;·&nbsp;
  </span>
</div>
```

- [ ] **Step 1：在 style.css 追加 Hero CSS**

  ```css
  /* ================================================================
     HOMEPAGE HERO
     ================================================================ */

  /* Hero 容器：左右分割 */
  .site-hero.wp-block-cover {
      min-height: 80vh !important;
      display: grid !important;
      grid-template-columns: 1fr 1fr !important;
      align-items: stretch !important;
      background-color: var(--bg-card) !important;
      padding: 0 !important;
  }

  @media (max-width: 768px) {
      .site-hero.wp-block-cover {
          min-height: 60vh !important;
          grid-template-columns: 1fr !important;
      }
  }

  /* Hero 內容區（左側文字） */
  .site-hero.wp-block-cover .wp-block-cover__inner-container {
      display: flex !important;
      flex-direction: column !important;
      justify-content: flex-end !important;
      padding: 8% 6% !important;
      grid-column: 1 !important;
  }

  /* Hero eyebrow 小標籤（段落加 CSS class: hero-eyebrow） */
  .site-hero .hero-eyebrow,
  .site-hero p.hero-eyebrow {
      font-family: var(--font-condensed) !important;
      font-size: 10px !important;
      letter-spacing: 4px !important;
      color: var(--accent) !important;
      text-transform: uppercase !important;
      margin-bottom: 8px !important;
  }

  /* Hero 主標：H1 */
  .site-hero.wp-block-cover h1,
  .site-hero .wp-block-cover__inner-container h1 {
      font-family: var(--font-condensed) !important;
      font-weight: 900 !important;
      font-size: clamp(48px, 8vw, 96px) !important;
      letter-spacing: -2px !important;
      text-transform: uppercase !important;
      color: var(--text-main) !important;
      line-height: 0.9 !important;
      margin: 0 0 4px !important;
  }

  /* Hero 斜體強調（H1 內的 <em>） */
  .site-hero h1 em {
      font-family: var(--font-serif) !important;
      font-style: italic !important;
      font-weight: 700 !important;
      font-size: 0.8em !important;
      color: var(--accent) !important;
      letter-spacing: 0 !important;
  }

  /* Hero 副標（段落） */
  .site-hero .wp-block-cover__inner-container p:not(.hero-eyebrow) {
      font-size: 12px !important;
      color: var(--text-muted) !important;
      letter-spacing: 1px !important;
      margin-top: 10px !important;
  }

  /* Hero CTA 按鈕（Buttons Block） */
  .site-hero .wp-block-button__link,
  .site-hero .wp-block-buttons .wp-block-button__link {
      background-color: var(--accent) !important;
      color: var(--bg-deep) !important;
      font-family: var(--font-condensed) !important;
      font-weight: 700 !important;
      font-size: 11px !important;
      letter-spacing: 3px !important;
      text-transform: uppercase !important;
      border-radius: 0 !important;
      padding: 12px 24px !important;
      border: none !important;
      transition: background-color 0.2s !important;
  }
  .site-hero .wp-block-button__link:hover {
      background-color: var(--accent-dim) !important;
  }

  /* ================================================================
     MARQUEE STRIP
     ================================================================ */

  .marquee-strip {
      background-color: var(--accent);
      padding: 11px 0;
      overflow: hidden;
      white-space: nowrap;
  }

  .marquee-text {
      display: inline-block;
      font-family: var(--font-condensed);
      font-weight: 900;
      font-size: 12px;
      letter-spacing: 4px;
      text-transform: uppercase;
      color: var(--bg-deep);
      animation: marquee-scroll 20s linear infinite;
      will-change: transform;
  }

  @keyframes marquee-scroll {
      from { transform: translateX(0); }
      to   { transform: translateX(-50%); }
  }

  /* ================================================================
     HOMEPAGE SECTIONS
     ================================================================ */

  /* Section Header（精選商品標題列） */
  .home-section-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      padding: 32px 0 16px;
      border-bottom: 1px solid var(--border);
      margin-bottom: 2px;
  }

  .home-section-eyebrow {
      font-family: var(--font-condensed);
      font-size: 9px;
      letter-spacing: 4px;
      color: var(--accent);
      text-transform: uppercase;
      margin-bottom: 4px;
  }

  .home-section-title {
      font-family: var(--font-condensed);
      font-weight: 900;
      font-size: 32px;
      letter-spacing: -1px;
      text-transform: uppercase;
      color: var(--text-main);
      line-height: 1;
  }

  .home-section-title em {
      font-family: var(--font-serif);
      font-style: italic;
      font-size: 26px;
      color: var(--accent);
      letter-spacing: 0;
  }

  .home-section-viewall {
      font-family: var(--font-condensed);
      font-size: 10px;
      letter-spacing: 2px;
      color: var(--text-muted);
      text-transform: uppercase;
      border-bottom: 1px solid var(--accent);
      padding-bottom: 1px;
      transition: color 0.2s;
  }
  .home-section-viewall:hover {
      color: var(--accent);
  }
  ```

- [ ] **Step 2：視覺驗證 Hero**

  在 WordPress 編輯器首頁，對 Cover Block 加上 CSS class `site-hero`，儲存後前台確認：
  - Hero 呈現左右分割（桌機）
  - H1 標題為超粗大寫
  - H1 內的 `<em>` 文字為陶土橘斜體

- [ ] **Step 3：驗證跑馬燈**

  在首頁 Hero 下方插入跑馬燈 Custom HTML Block（見 Task 說明），確認：
  - 陶土橘背景條
  - 文字持續左向捲動

- [ ] **Step 4：Commit**

  ```bash
  git add wp-content/themes/astra-child/style.css
  git commit -m "feat: add hero split layout and accent marquee strip"
  ```

---

## Task 4：WooCommerce 全域 CSS

**目標：** 套用設計系統至 WooCommerce 全域按鈕、表單、訊息、分頁等元件。

**Files:**
- Modify: `wp-content/themes/astra-child/style.css`（追加）

- [ ] **Step 1：在 style.css 追加 WooCommerce 全域樣式**

  ```css
  /* ================================================================
     WOOCOMMERCE GLOBAL
     ================================================================ */

  /* WC 主按鈕 */
  .woocommerce a.button,
  .woocommerce button.button,
  .woocommerce input.button,
  .woocommerce #respond input#submit,
  .woocommerce .button.alt,
  .woocommerce button.button.alt,
  .woocommerce a.button.alt {
      background-color: var(--accent) !important;
      color: var(--bg-deep) !important;
      font-family: var(--font-condensed) !important;
      font-weight: 700 !important;
      font-size: 11px !important;
      letter-spacing: 3px !important;
      text-transform: uppercase !important;
      border-radius: 0 !important;
      border: none !important;
      padding: 12px 24px !important;
      transition: background-color 0.2s !important;
  }
  .woocommerce a.button:hover,
  .woocommerce button.button:hover,
  .woocommerce a.button.alt:hover,
  .woocommerce button.button.alt:hover {
      background-color: var(--accent-dim) !important;
      color: var(--bg-deep) !important;
  }

  /* 次要按鈕（輸出頁、我的帳號） */
  .woocommerce a.button.secondary {
      background-color: transparent !important;
      color: var(--text-muted) !important;
      border: 1px solid var(--border) !important;
  }
  .woocommerce a.button.secondary:hover {
      border-color: var(--accent) !important;
      color: var(--accent) !important;
  }

  /* WC 表單輸入 */
  .woocommerce form .input-text,
  .woocommerce input[type="text"],
  .woocommerce input[type="email"],
  .woocommerce input[type="tel"],
  .woocommerce input[type="password"],
  .woocommerce textarea,
  .woocommerce select {
      background-color: var(--bg-card) !important;
      color: var(--text-main) !important;
      border: 1px solid var(--border) !important;
      border-radius: 0 !important;
      font-family: var(--font-body) !important;
      font-size: 13px !important;
      padding: 10px 14px !important;
  }
  .woocommerce form .input-text:focus,
  .woocommerce input[type="text"]:focus,
  .woocommerce input[type="email"]:focus {
      border-color: var(--accent) !important;
      outline: none !important;
      box-shadow: none !important;
  }

  /* WC 訊息 / 通知 */
  .woocommerce-message,
  .woocommerce-info {
      background-color: var(--bg-card) !important;
      border-top-color: var(--accent) !important;
      color: var(--text-main) !important;
  }
  .woocommerce-error {
      background-color: var(--bg-card) !important;
      border-top-color: #cc2200 !important;
      color: var(--text-main) !important;
  }

  /* WC 麵包屑 */
  .woocommerce .woocommerce-breadcrumb {
      font-family: var(--font-condensed) !important;
      font-size: 10px !important;
      letter-spacing: 2px !important;
      color: var(--text-muted) !important;
      text-transform: uppercase !important;
  }
  .woocommerce .woocommerce-breadcrumb a {
      color: var(--text-muted) !important;
  }
  .woocommerce .woocommerce-breadcrumb a:hover {
      color: var(--accent) !important;
  }

  /* WC 分頁 */
  .woocommerce nav.woocommerce-pagination ul li a,
  .woocommerce nav.woocommerce-pagination ul li span {
      background-color: var(--bg-card) !important;
      color: var(--text-muted) !important;
      border-color: var(--border) !important;
      font-family: var(--font-condensed) !important;
      border-radius: 0 !important;
  }
  .woocommerce nav.woocommerce-pagination ul li a:hover,
  .woocommerce nav.woocommerce-pagination ul li span.current {
      background-color: var(--accent) !important;
      color: var(--bg-deep) !important;
  }

  /* 價格 */
  .woocommerce .price,
  .woocommerce .amount {
      color: var(--text-main) !important;
      font-family: var(--font-condensed) !important;
      font-weight: 700 !important;
  }
  .woocommerce del .amount {
      color: var(--text-muted) !important;
  }
  .woocommerce ins .amount {
      color: var(--accent) !important;
  }
  ```

- [ ] **Step 2：視覺驗證**

  前往 WooCommerce 商品頁，確認：
  - 「加入購物車」按鈕為陶土橘，文字深色
  - 表單輸入框為深卡片色，focus 時出現陶土橘外框

- [ ] **Step 3：Commit**

  ```bash
  git add wp-content/themes/astra-child/style.css
  git commit -m "feat: apply design system to WooCommerce global elements"
  ```

---

## Task 5：商品卡片模板覆寫（content-product.php）

**目標：** 覆寫 WooCommerce 商品卡片模板，加入設計稿的標籤系統、心願清單按鈕、快速加入購物車。

**Files:**
- Create: `wp-content/themes/astra-child/woocommerce/content-product.php`
- Modify: `wp-content/themes/astra-child/style.css`（追加商品卡片 CSS）

- [ ] **Step 1：建立 content-product.php**

  ```php
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
  ```

- [ ] **Step 2：在 style.css 追加商品卡片 CSS**

  ```css
  /* ================================================================
     PRODUCT CARD（content-product.php）
     ================================================================ */

  .products.columns-2,
  .products.columns-3,
  .products.columns-4,
  ul.products {
      display: grid !important;
      grid-template-columns: repeat(2, 1fr) !important;
      gap: 2px !important;
      padding: 0 !important;
      margin: 0 !important;
      list-style: none !important;
  }

  @media (min-width: 1024px) {
      ul.products {
          grid-template-columns: repeat(3, 1fr) !important;
      }
  }

  .studio-product-card {
      background-color: var(--bg-card);
      overflow: hidden;
      transition: background-color 0.2s;
      position: relative;
  }
  .studio-product-card:hover {
      background-color: var(--bg-hover);
  }

  /* 圖片區域 */
  .studio-product-card__image-link {
      display: block;
      text-decoration: none;
  }
  .studio-product-card__image-wrap {
      position: relative;
      aspect-ratio: 1;
      background-color: var(--bg-hover);
      overflow: hidden;
  }
  .studio-product-card__img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.4s ease;
  }
  .studio-product-card:hover .studio-product-card__img {
      transform: scale(1.03);
  }

  /* 標籤 Badges */
  .studio-product-card__badges {
      position: absolute;
      top: 8px;
      left: 8px;
      display: flex;
      flex-direction: column;
      gap: 4px;
  }

  .studio-badge {
      font-family: var(--font-condensed);
      font-weight: 700;
      font-size: 8px;
      letter-spacing: 2px;
      text-transform: uppercase;
      padding: 3px 8px;
      display: inline-block;
  }
  .studio-badge--new {
      background-color: var(--accent);
      color: var(--bg-deep);
  }
  .studio-badge--digital {
      background-color: #2a2220;
      color: var(--accent);
      border: 1px solid var(--accent);
  }
  .studio-badge--sale {
      background-color: #cc2200;
      color: #fff;
  }

  /* 商品資訊區 */
  .studio-product-card__info {
      padding: 10px 12px 14px;
  }

  .studio-product-card__title-link {
      text-decoration: none;
  }

  .studio-product-card__name {
      font-family: var(--font-condensed);
      font-weight: 700;
      font-size: 13px;
      letter-spacing: 0.5px;
      text-transform: uppercase;
      color: var(--text-main);
      margin-bottom: 8px;
      line-height: 1.2;
      transition: color 0.2s;
  }
  .studio-product-card:hover .studio-product-card__name {
      color: var(--accent);
  }

  /* 底部：價格 + 加入按鈕 */
  .studio-product-card__bottom {
      display: flex;
      justify-content: space-between;
      align-items: center;
  }

  .studio-product-card__price .price,
  .studio-product-card__price .amount {
      font-family: var(--font-condensed) !important;
      font-size: 13px !important;
      color: var(--text-main) !important;
  }

  .studio-product-card__add-btn {
      font-family: var(--font-condensed);
      font-size: 9px;
      letter-spacing: 1px;
      text-transform: uppercase;
      color: var(--accent);
      border: 1px solid var(--accent);
      padding: 4px 10px;
      background: transparent;
      cursor: pointer;
      transition: background-color 0.2s, color 0.2s;
      text-decoration: none;
      border-radius: 2px;
  }
  .studio-product-card__add-btn:hover {
      background-color: var(--accent);
      color: var(--bg-deep);
  }

  .studio-product-card__sold-out {
      font-family: var(--font-condensed);
      font-size: 9px;
      letter-spacing: 1px;
      color: var(--text-muted);
      text-transform: uppercase;
  }
  ```

- [ ] **Step 3：PHP 語法檢查**

  ```bash
  php -l wp-content/themes/astra-child/woocommerce/content-product.php
  ```
  預期輸出：`No syntax errors detected`

- [ ] **Step 4：視覺驗證商品卡片**

  前往 WooCommerce 商品列表頁，確認：
  - 商品圖片呈正方形，hover 有輕微放大
  - 商品名稱大寫 Barlow Condensed，hover 變陶土橘
  - 已設定「精選商品（Featured）」的商品顯示「New」標籤
  - 可下載商品顯示「Digital」標籤
  - 右側小按鈕「+ 加入」，hover 填滿陶土橘

- [ ] **Step 5：Commit**

  ```bash
  git add wp-content/themes/astra-child/woocommerce/content-product.php wp-content/themes/astra-child/style.css
  git commit -m "feat: add custom product card template with badge system"
  ```

---

## Task 6：商品列表頁模板覆寫（archive-product.php）

**目標：** 覆寫商品列表頁，加入混搭字體頁首、篩選標籤列、商品數量與排序列。

**Files:**
- Create: `wp-content/themes/astra-child/woocommerce/archive-product.php`
- Modify: `wp-content/themes/astra-child/style.css`（追加 Shop 頁 CSS）

- [ ] **Step 1：建立 archive-product.php**

  ```php
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
  ```

- [ ] **Step 2：在 style.css 追加 Shop 頁面 CSS**

  ```css
  /* ================================================================
     SHOP PAGE（archive-product.php）
     ================================================================ */

  .studio-shop {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 20px;
  }

  /* 頁首 */
  .studio-shop__header {
      padding: 32px 0 0;
      border-bottom: 1px solid var(--border);
      margin-bottom: 2px;
  }

  .studio-shop__eyebrow {
      font-family: var(--font-condensed);
      font-size: 9px;
      letter-spacing: 4px;
      color: var(--accent);
      text-transform: uppercase;
      margin-bottom: 4px;
  }

  .studio-shop__title {
      font-family: var(--font-condensed) !important;
      font-weight: 900 !important;
      font-size: clamp(28px, 5vw, 48px) !important;
      letter-spacing: -1px !important;
      text-transform: uppercase !important;
      color: var(--text-main) !important;
      line-height: 1 !important;
      margin: 0 0 12px !important;
  }
  .studio-shop__title em {
      font-family: var(--font-serif) !important;
      font-style: italic !important;
      font-size: 0.8em !important;
      color: var(--accent) !important;
      letter-spacing: 0 !important;
  }

  /* 商品數量 + 排序 */
  .studio-shop__meta {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 12px 0;
  }

  .studio-shop__count .woocommerce-result-count {
      font-family: var(--font-condensed);
      font-size: 10px;
      letter-spacing: 2px;
      color: var(--text-muted);
      text-transform: uppercase;
      margin: 0;
  }

  .studio-shop__ordering .woocommerce-ordering select {
      background-color: var(--bg-card) !important;
      color: var(--text-muted) !important;
      border: 1px solid var(--border) !important;
      border-radius: 0 !important;
      font-family: var(--font-condensed) !important;
      font-size: 10px !important;
      letter-spacing: 1px !important;
      padding: 6px 12px !important;
  }

  /* 篩選標籤列 */
  .studio-shop__filters {
      display: flex;
      gap: 6px;
      padding: 12px 0;
      border-bottom: 1px solid var(--border);
      overflow-x: auto;
      scrollbar-width: none;
      -ms-overflow-style: none;
  }
  .studio-shop__filters::-webkit-scrollbar {
      display: none;
  }

  .studio-filter-chip {
      font-family: var(--font-condensed);
      font-size: 9px;
      letter-spacing: 2px;
      text-transform: uppercase;
      padding: 5px 14px;
      border: 1px solid var(--border);
      color: var(--text-muted);
      white-space: nowrap;
      cursor: pointer;
      border-radius: 2px;
      text-decoration: none;
      transition: border-color 0.2s, color 0.2s;
  }
  .studio-filter-chip:hover,
  .studio-filter-chip.is-active {
      border-color: var(--accent);
      color: var(--accent);
  }
  ```

- [ ] **Step 3：PHP 語法檢查**

  ```bash
  php -l wp-content/themes/astra-child/woocommerce/archive-product.php
  ```
  預期輸出：`No syntax errors detected`

- [ ] **Step 4：視覺驗證商品列表頁**

  前往 `/shop` 頁面確認：
  - 頁首顯示混搭字體大標「All *Products*」
  - 分類篩選標籤水平排列，「全部」預設為陶土橘框線
  - 點選分類標籤後頁面篩選正常，被選標籤高亮

- [ ] **Step 5：Commit**

  ```bash
  git add wp-content/themes/astra-child/woocommerce/archive-product.php wp-content/themes/astra-child/style.css
  git commit -m "feat: add custom shop archive with filter chips and editorial header"
  ```

---

## Task 7：商品詳細頁模板覆寫

**目標：** 覆寫商品標題與加入購物車按鈕模板，套用混搭字體與設計系統按鈕。

**Files:**
- Create: `wp-content/themes/astra-child/woocommerce/single-product/title.php`
- Create: `wp-content/themes/astra-child/woocommerce/single-product/add-to-cart/simple.php`
- Modify: `wp-content/themes/astra-child/style.css`（追加單品頁 CSS）

- [ ] **Step 1：建立資料夾並新增 title.php**

  ```bash
  mkdir -p wp-content/themes/astra-child/woocommerce/single-product/add-to-cart
  ```

  建立 `woocommerce/single-product/title.php`：

  ```php
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
              <span class="studio-badge studio-badge--new">New</span>
          <?php endif; ?>
      </div>
  <?php endif; ?>

  <h1 class="studio-product-single__title product_title entry-title">
      <?php echo wp_kses_post( get_the_title() ); ?>
  </h1>
  ```

- [ ] **Step 2：建立 simple.php（加入購物車按鈕）**

  建立 `woocommerce/single-product/add-to-cart/simple.php`：

  ```php
  <?php
  /**
   * 簡單商品加入購物車按鈕
   * 覆寫自 WooCommerce/templates/single-product/add-to-cart/simple.php
   */

  defined( 'ABSPATH' ) || exit;

  global $product;

  if ( ! $product->is_purchasable() ) return;

  echo wc_get_stock_html( $product );

  if ( $product->is_in_stock() ) : ?>

      <?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>

      <form class="cart studio-product-single__cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data'>

          <?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

          <input type="hidden" name="add-to-cart" value="<?php echo absint( $product->get_id() ); ?>">

          <div class="quantity">
              <?php woocommerce_quantity_input( [ 'min_value' => 1, 'max_value' => $product->get_max_purchase_quantity() ] ); ?>
          </div>

          <button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="single_add_to_cart_button button alt studio-product-single__cta">
              加入購物車 — Add to Cart
          </button>

          <?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>

      </form>

      <?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>

  <?php endif; ?>
  ```

- [ ] **Step 3：在 style.css 追加單品頁 CSS**

  ```css
  /* ================================================================
     SINGLE PRODUCT PAGE
     ================================================================ */

  .woocommerce div.product {
      background-color: var(--bg-deep);
  }

  /* 商品圖片區 */
  .woocommerce div.product div.images {
      background-color: var(--bg-hover);
  }
  .woocommerce div.product div.images img {
      object-fit: cover;
  }

  /* 商品分類標籤 */
  .studio-product-single__cats {
      font-family: var(--font-condensed);
      font-size: 9px;
      letter-spacing: 4px;
      color: var(--accent);
      text-transform: uppercase;
      margin-bottom: 8px;
      display: flex;
      align-items: center;
      gap: 8px;
  }
  .studio-product-single__cats a {
      color: var(--accent);
  }

  /* 商品主標題 */
  .studio-product-single__title {
      font-family: var(--font-condensed) !important;
      font-weight: 900 !important;
      font-size: clamp(28px, 5vw, 48px) !important;
      letter-spacing: -1px !important;
      text-transform: uppercase !important;
      color: var(--text-main) !important;
      line-height: 1 !important;
      margin: 0 0 12px !important;
  }

  /* 價格 */
  .woocommerce div.product p.price,
  .woocommerce div.product span.price {
      font-family: var(--font-condensed) !important;
      font-weight: 700 !important;
      font-size: 22px !important;
      color: var(--text-main) !important;
      margin: 12px 0 !important;
  }

  /* 商品描述 */
  .woocommerce div.product .woocommerce-product-details__short-description,
  .woocommerce div.product .woocommerce-Tabs-panel--description {
      font-family: var(--font-body);
      font-size: 13px;
      color: var(--text-muted);
      line-height: 1.8;
  }

  /* 購物車區域 */
  .studio-product-single__cart {
      display: flex;
      gap: 10px;
      align-items: center;
      flex-wrap: wrap;
      margin-top: 16px;
  }

  /* 數量輸入 */
  .woocommerce div.product form.cart .quantity input.qty {
      background-color: var(--bg-card) !important;
      color: var(--text-main) !important;
      border: 1px solid var(--border) !important;
      border-radius: 0 !important;
      font-family: var(--font-condensed) !important;
      font-size: 14px !important;
      width: 64px !important;
      text-align: center !important;
      padding: 10px !important;
  }

  /* 加入購物車主按鈕 */
  .studio-product-single__cta {
      flex: 1;
      text-align: center;
      font-family: var(--font-condensed) !important;
      font-weight: 700 !important;
      font-size: 12px !important;
      letter-spacing: 3px !important;
      text-transform: uppercase !important;
      background-color: var(--accent) !important;
      color: var(--bg-deep) !important;
      border: none !important;
      border-radius: 0 !important;
      padding: 14px 24px !important;
      cursor: pointer;
      transition: background-color 0.2s !important;
  }
  .studio-product-single__cta:hover {
      background-color: var(--accent-dim) !important;
  }

  /* 商品 Tab（描述 / 評論） */
  .woocommerce div.product .woocommerce-tabs ul.tabs {
      border-bottom: 1px solid var(--border) !important;
  }
  .woocommerce div.product .woocommerce-tabs ul.tabs li {
      background-color: transparent !important;
      border: none !important;
      border-bottom: 2px solid transparent !important;
  }
  .woocommerce div.product .woocommerce-tabs ul.tabs li a {
      font-family: var(--font-condensed) !important;
      font-size: 11px !important;
      letter-spacing: 2px !important;
      text-transform: uppercase !important;
      color: var(--text-muted) !important;
  }
  .woocommerce div.product .woocommerce-tabs ul.tabs li.active {
      border-bottom-color: var(--accent) !important;
  }
  .woocommerce div.product .woocommerce-tabs ul.tabs li.active a {
      color: var(--text-main) !important;
  }
  ```

- [ ] **Step 4：PHP 語法檢查（兩個新檔案）**

  ```bash
  php -l wp-content/themes/astra-child/woocommerce/single-product/title.php
  php -l wp-content/themes/astra-child/woocommerce/single-product/add-to-cart/simple.php
  ```
  預期輸出：`No syntax errors detected`（各一次）

- [ ] **Step 5：視覺驗證單品頁**

  前往任一商品詳細頁，確認：
  - 頁首顯示分類標籤（陶土橘小標），主標題為大寫混搭字體
  - 「加入購物車 — Add to Cart」為陶土橘全寬按鈕
  - 商品 Tab 底線 hover 後為陶土橘

- [ ] **Step 6：Commit**

  ```bash
  git add wp-content/themes/astra-child/woocommerce/single-product/ wp-content/themes/astra-child/style.css
  git commit -m "feat: add single product title and add-to-cart template overrides"
  ```

---

## Task 8：最終整合驗證

**目標：** 全站走一遍購物流程，確認設計一致性與功能正常。

- [ ] **Step 1：視覺一致性檢查清單**

  逐頁確認以下項目：

  | 頁面 | 確認項目 |
  |------|----------|
  | 首頁 | Hero 左右分割，大標混搭字體，跑馬燈捲動，精選商品區顯示正確 |
  | 商品列表 | 篩選標籤、商品卡片 2 欄、Badge 標籤、hover 效果 |
  | 商品詳細 | 分類標籤、大標、陶土橘 CTA 按鈕 |
  | 購物車 | 深色背景、按鈕配色 |
  | 結帳頁 | 表單輸入 focus 陶土橘外框、按鈕配色 |
  | 我的帳號 | 連結 hover 陶土橘 |

- [ ] **Step 2：跑完整購物流程**

  測試商品加入購物車 → 前往結帳 → 確認所有 WooCommerce 按鈕樣式一致。

- [ ] **Step 3：RWD 驗證（手機尺寸）**

  DevTools 切換至 375px 寬度確認：
  - Hero 在手機改為單欄排版（`grid-template-columns: 1fr`）
  - 商品卡片維持 2 欄
  - 篩選標籤可水平捲動

- [ ] **Step 4：PHP 語法全掃**

  ```bash
  find wp-content/themes/astra-child -name "*.php" | xargs php -l
  ```
  預期：所有檔案均 `No syntax errors detected`

- [ ] **Step 5：最終 Commit**

  ```bash
  git add -A
  git commit -m "feat: complete UI redesign — dark editorial theme with terracotta accent"
  ```
