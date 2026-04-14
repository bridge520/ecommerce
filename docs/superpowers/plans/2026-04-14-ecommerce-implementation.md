# 電商網站 Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** 建立一個小型工作室 WooCommerce 電商網站，支援實體 + 數位混合商品銷售、台灣及國際金流、超商 / 宅配物流、以及繁中 / 英 / 日三語。

**Architecture:** WordPress 6.x + WooCommerce 8.x 部署於台灣主機，搭配 WPML 多語言、ECPay 金流物流、Stripe/PayPal 國際金流、WP Rocket 效能優化。所有設定透過 WP-CLI 與 wp-config.php 版本控制，插件設定以 JSON 匯出備份。

**Tech Stack:** WordPress 6.x, WooCommerce 8.x, PHP 8.2, MySQL 8.0, Nginx, WPML, ECPay, NewebPay, Stripe, PayPal, WP Rocket, Cloudflare, Yoast SEO, Astra Theme

---

## 檔案結構

```
wp-content/
├── themes/
│   └── astra-child/              ← 子佈景主題（客製化用）
│       ├── style.css
│       ├── functions.php
│       └── woocommerce/          ← WooCommerce 模板覆寫
│           ├── checkout/
│           │   └── thankyou.php
│           └── myaccount/
│               └── downloads.php
├── plugins/
│   ├── woocommerce/              ← 核心（安裝後勿手動修改）
│   ├── wpml-multilingual-cms/    ← 多語言（購買授權）
│   ├── woocommerce-multilingual/ ← WooCommerce + WPML 橋接
│   ├── ecpay-for-woocommerce/    ← ECPay 金流
│   ├── ecpay-logistics-for-woocommerce/ ← ECPay 物流
│   ├── woo-stripe-payment/       ← Stripe 金流
│   ├── woocommerce-paypal-payments/ ← PayPal 金流
│   ├── wp-rocket/                ← 速度優化（購買授權）
│   ├── wordpress-seo/            ← Yoast SEO
│   └── limit-login-attempts-reloaded/ ← 安全
└── uploads/                      ← 商品圖片、數位商品檔案
    └── woocommerce_uploads/      ← 數位商品（受保護目錄）
```

**版控注意：** `wp-content/uploads/` 與 `wp-config.php` 加入 `.gitignore`，只版控 `astra-child/` 子佈景主題程式碼。

---

## Task 1：主機環境建置

**目標：** 在台灣主機商上完成 Linux + Nginx + PHP 8.2 + MySQL 環境，安裝 WordPress。

**Files:**
- Create: `wp-config.php`（從 wp-config-sample.php 複製）
- Create: `/etc/nginx/sites-available/yourdomain.com`
- Create: `.gitignore`

- [ ] **Step 1：購買主機與網域**

  前往台灣主機商（推薦：速博 `supo.tw` 或 網路中文 `net-chinese.com.tw`）購買：
  - Linux 主機方案（PHP 8.2 支援）月費約 NT$400
  - `.com` 網域，約 NT$400/年
  - 申請完成後取得：FTP 帳號、MySQL 帳號、主機 IP

- [ ] **Step 2：設定 SSL（Let's Encrypt）**

  登入主機控制台（cPanel 或 DirectAdmin），找到「SSL/TLS」或「Let's Encrypt」功能，為網域申請免費 SSL 憑證，確認 `https://yourdomain.com` 可正常載入。

- [ ] **Step 3：下載並上傳 WordPress**

  ```bash
  # 本機下載最新 WordPress（繁體中文版）
  wget https://tw.wordpress.org/latest-zh_TW.zip
  unzip latest-zh_TW.zip
  # 透過 FTP 或主機控制台上傳所有檔案到網站根目錄
  ```

- [ ] **Step 4：建立資料庫**

  登入主機控制台 → MySQL 資料庫 → 建立新資料庫：
  - 資料庫名稱：`shop_db`
  - 使用者名稱：`shop_user`
  - 密碼：使用強密碼（記下來）
  - 將使用者加入資料庫，權限選「所有權限」

- [ ] **Step 5：設定 wp-config.php**

  複製 `wp-config-sample.php` 為 `wp-config.php`，填入資料庫資訊：

  ```php
  define( 'DB_NAME', 'shop_db' );
  define( 'DB_USER', 'shop_user' );
  define( 'DB_PASSWORD', '你的強密碼' );
  define( 'DB_HOST', 'localhost' );
  define( 'DB_CHARSET', 'utf8mb4' );

  // 從 https://api.wordpress.org/secret-key/1.1/salt/ 取得並貼入
  define('AUTH_KEY',         '...');
  define('SECURE_AUTH_KEY',  '...');
  // ... 其餘 salt keys

  // 強制 HTTPS
  define('FORCE_SSL_ADMIN', true);
  if (strpos($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') !== false) {
      $_SERVER['HTTPS'] = 'on';
  }
  ```

- [ ] **Step 6：執行 WordPress 安裝精靈**

  瀏覽器前往 `https://yourdomain.com/wp-admin/install.php`，填入：
  - 網站標題：工作室名稱
  - 管理員帳號：`admin`（建議改為非預設名稱）
  - 管理員密碼：強密碼
  - 管理員信箱：你的 Email
  
  按「安裝 WordPress」，確認可登入 `https://yourdomain.com/wp-admin/`

- [ ] **Step 7：建立 .gitignore 並初始化 git**

  ```bash
  # 在 wp-content/ 目錄下初始化
  git init
  cat > .gitignore << 'EOF'
  # WordPress 核心（不版控）
  /wp-admin/
  /wp-includes/
  /index.php
  /wp-*.php
  /xmlrpc.php
  /wp-config.php

  # 上傳檔案與快取
  uploads/
  upgrade/
  cache/

  # 插件（透過 composer 或手動安裝，不版控）
  plugins/

  # 只版控子佈景主題
  !themes/astra-child/
  EOF
  git add .gitignore
  git commit -m "init: add .gitignore for WordPress project"
  ```

- [ ] **Step 8：驗證**

  - 前往 `https://yourdomain.com` → 看到 WordPress 預設首頁
  - 瀏覽器網址列顯示綠色鎖頭（SSL 正常）
  - 可正常登入 `/wp-admin/`

---

## Task 2：WooCommerce 安裝與基本設定

**目標：** 安裝 WooCommerce，完成台灣電商基本設定（貨幣 TWD、時區、稅務）。

**Files:**
- Modify: `wp-admin` 設定介面（無程式碼）

- [ ] **Step 1：安裝 WooCommerce**

  後台 → 插件 → 新增插件 → 搜尋「WooCommerce」→ 安裝並啟用。
  跳出安裝精靈，依序填入：
  - 商店位置：台灣
  - 幣別：新台幣（TWD）
  - 商品類型：勾選「實體商品」和「下載商品」

- [ ] **Step 2：設定時區與語言**

  後台 → 設定 → 一般：
  - 站點語言：繁體中文
  - 時區：Taipei（UTC+8）
  - 日期格式：`Y 年 n 月 j 日`
  - 幣別：NT$，千分位符號：`,`，小數點：`.`，小數位數：`0`

- [ ] **Step 3：設定稅務**

  後台 → WooCommerce → 設定 → 一般：
  - 啟用稅務：勾選
  - 稅務設定：後台 → WooCommerce → 設定 → 稅務 → 新增稅率
    - 國家/地區：TW
    - 州/省：留空
    - 稅率：5（%）
    - 稅務名稱：營業稅
    - 優先順序：1
    - 複合稅：否
    - 運費：勾選

- [ ] **Step 4：設定商品圖片尺寸**

  後台 → WooCommerce → 設定 → 商品 → 商品圖片：
  - 主要圖片：800 × 800
  - 商品縮圖：300 × 300
  - 商品藝廊：150 × 150

- [ ] **Step 5：設定商店電子郵件**

  後台 → WooCommerce → 設定 → 電子郵件：
  - 「寄件人」名稱：工作室名稱
  - 「寄件人」電子郵件地址：your@email.com
  - 確認各信件模板（訂單確認、出貨通知）已啟用

- [ ] **Step 6：驗證**

  後台 → WooCommerce → 首頁 → 確認沒有紅色錯誤警告。
  前往 `https://yourdomain.com/shop/` → 看到空商店頁面（正常）。

- [ ] **Step 7：Commit**

  此步驟無程式碼變更，記錄設定完成：
  ```bash
  git commit --allow-empty -m "config: WooCommerce basic setup complete (TWD, UTC+8, 5% tax)"
  ```

---

## Task 3：子佈景主題建立與 Astra 安裝

**目標：** 安裝 Astra 佈景主題，建立子佈景主題供客製化使用。

**Files:**
- Create: `themes/astra-child/style.css`
- Create: `themes/astra-child/functions.php`

- [ ] **Step 1：安裝 Astra 佈景主題**

  後台 → 外觀 → 佈景主題 → 新增 → 搜尋「Astra」→ 安裝（先不要啟用）

- [ ] **Step 2：建立子佈景主題目錄**

  在主機 `wp-content/themes/` 下新增 `astra-child/` 資料夾。

- [ ] **Step 3：建立 style.css**

  建立 `wp-content/themes/astra-child/style.css`：

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

  /* 在此加入客製化 CSS */
  ```

- [ ] **Step 4：建立 functions.php**

  建立 `wp-content/themes/astra-child/functions.php`：

  ```php
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
  ```

- [ ] **Step 5：啟用子佈景主題**

  後台 → 外觀 → 佈景主題 → 找到「Astra Child」→ 啟用。
  前往前台 `https://yourdomain.com` → 確認網站外觀正常。

- [ ] **Step 6：安裝 Astra 佈景主題設定（Starter Templates）**

  後台 → 外觀 → Astra Options → 安裝「Starter Templates」插件 →
  選擇電商模板（搜尋「Shop」或「eCommerce」）→ 匯入示範內容（選擇性）。

- [ ] **Step 7：Commit**

  ```bash
  git add themes/astra-child/
  git commit -m "feat: add Astra child theme with WooCommerce support"
  ```

---

## Task 4：WPML 多語言安裝設定

**目標：** 安裝 WPML，設定繁體中文（預設）、英文、日文三語切換。

**Files:**
- Modify: `themes/astra-child/functions.php`（加入語言切換器 shortcode 呼叫）

- [ ] **Step 1：購買並下載 WPML**

  前往 `wpml.org` 購買「Multilingual CMS」方案（$99/年），下載：
  - `wpml-multilingual-cms.zip`
  - `woocommerce-multilingual.zip`

- [ ] **Step 2：安裝 WPML**

  後台 → 插件 → 新增插件 → 上傳插件 → 上傳 `wpml-multilingual-cms.zip` → 安裝並啟用。
  依精靈步驟輸入授權碼，完成啟用。

- [ ] **Step 3：設定語言**

  後台 → WPML → 語言：
  - 預設語言：繁體中文（zh-hant）
  - 新增語言：英文（en）、日文（ja）
  - 語言切換器：勾選「在導覽列顯示」，樣式選「下拉選單」

- [ ] **Step 4：安裝 WooCommerce Multilingual**

  後台 → 插件 → 新增插件 → 上傳 `woocommerce-multilingual.zip` → 安裝並啟用。
  後台 → WooCommerce Multilingual → 依精靈完成設定（貨幣：TWD 對應所有語言）。

- [ ] **Step 5：設定 hreflang（SEO）**

  後台 → WPML → 設定 → SEO → 確認「自動新增 hreflang 標記」已啟用。
  每個語言的頁面 URL 結構：
  - 繁中：`yourdomain.com/zh/`（或根目錄）
  - 英文：`yourdomain.com/en/`
  - 日文：`yourdomain.com/ja/`

- [ ] **Step 6：測試語言切換**

  前往前台 → 確認導覽列出現語言切換下拉選單。
  點擊「English」→ 確認 URL 切換為 `/en/`。
  點擊「日本語」→ 確認 URL 切換為 `/ja/`。

- [ ] **Step 7：Commit**

  ```bash
  git commit --allow-empty -m "config: WPML multilingual setup (zh-TW, en, ja)"
  ```

---

## Task 5：台灣金流整合（ECPay）

**目標：** 安裝並設定 ECPay 綠界金流插件，支援信用卡、ATM、超商代碼、LINE Pay。

**Files:**
- 無程式碼（插件設定）

- [ ] **Step 1：申請 ECPay 商家帳號**

  前往 `ecpay.com.tw` → 申請特店帳號（需提供身分證、銀行帳戶）。
  申請完成後取得：
  - 特店編號（MerchantID）
  - HashKey
  - HashIV
  - 測試環境與正式環境各一組

- [ ] **Step 2：安裝 ECPay WooCommerce 插件**

  前往 ECPay GitHub 或後台下載中心下載最新版 `ecpay-for-woocommerce.zip`。
  後台 → 插件 → 新增插件 → 上傳插件 → 安裝並啟用。

- [ ] **Step 3：設定 ECPay 金流（測試模式）**

  後台 → WooCommerce → 設定 → 付款 → ECPay 付款 → 管理：
  - 啟用：勾選
  - 標題：信用卡 / ATM / 超商代碼 / LINE Pay
  - 操作模式：測試（上線前改為正式）
  - 特店編號：填入測試 MerchantID
  - HashKey：填入測試 HashKey
  - HashIV：填入測試 HashIV
  - 付款方式：全選（信用卡、ATM、超商代碼、LINE Pay）
  - 儲存設定

- [ ] **Step 4：測試付款流程（測試模式）**

  新增一個測試商品（NT$100），前往結帳頁，選擇「信用卡付款（ECPay）」，使用 ECPay 提供的測試信用卡號：
  ```
  卡號：4311-9522-2222-2222
  有效月年：任意未來日期
  CVV：任意 3 碼
  ```
  確認跳轉至 ECPay 付款頁面 → 測試付款成功 → 跳回網站訂單確認頁 → 後台訂單狀態變為「處理中」。

- [ ] **Step 5：安裝並設定 NewebPay 藍新（街口支付）**

  ECPay 不支援街口支付，需另外安裝 NewebPay：
  1. 前往 `newebpay.com` 申請特店帳號，取得 MerchantID、HashKey、HashIV
  2. 前往 NewebPay GitHub 下載 WooCommerce 插件，上傳安裝並啟用
  3. 後台 → WooCommerce → 設定 → 付款 → NewebPay → 管理：
     - 操作模式：測試
     - 特店編號、HashKey、HashIV：填入測試值
     - 付款方式：勾選「街口支付」（JKO Pay）
  4. 使用 NewebPay 測試帳號進行街口支付測試交易，確認成功

- [ ] **Step 6：Commit**

  ```bash
  git commit --allow-empty -m "config: ECPay + NewebPay payment gateways integrated (test mode)"
  ```

---

## Task 6：國際金流整合（Stripe + PayPal）

**目標：** 安裝 Stripe 與 PayPal 插件，支援國際信用卡付款。

**Files:**
- 無程式碼（插件設定）

- [ ] **Step 1：安裝 Stripe for WooCommerce**

  後台 → 插件 → 新增插件 → 搜尋「WooCommerce Stripe Payment Gateway」（官方插件）→ 安裝並啟用。

- [ ] **Step 2：設定 Stripe**

  申請 Stripe 帳號（`stripe.com`），取得：
  - Publishable Key（測試 & 正式）
  - Secret Key（測試 & 正式）

  後台 → WooCommerce → 設定 → 付款 → Stripe → 管理：
  - 啟用：勾選
  - 測試模式：開啟
  - 測試 Publishable Key：貼入 `pk_test_...`
  - 測試 Secret Key：貼入 `sk_test_...`
  - 儲存設定

- [ ] **Step 3：測試 Stripe 付款**

  結帳頁選擇「信用卡（Stripe）」，使用 Stripe 測試卡號：
  ```
  卡號：4242 4242 4242 4242
  有效月年：任意未來日期
  CVV：任意 3 碼
  郵遞區號：任意
  ```
  確認付款成功，後台訂單狀態更新。

- [ ] **Step 4：安裝 PayPal Payments**

  後台 → 插件 → 新增插件 → 搜尋「WooCommerce PayPal Payments」（官方插件）→ 安裝並啟用。
  後台 → WooCommerce → 設定 → 付款 → PayPal → 依精靈連結 PayPal 商家帳號（沙盒模式測試）。

- [ ] **Step 5：Commit**

  ```bash
  git commit --allow-empty -m "config: Stripe and PayPal payment gateways integrated (test mode)"
  ```

---

## Task 7：物流設定（ECPay 物流 + 自取）

**目標：** 設定超商取貨（7-11 / 全家）、黑貓宅配、自取三種物流選項。

**Files:**
- 無程式碼（插件設定）

- [ ] **Step 1：安裝 ECPay 物流插件**

  前往 ECPay 後台下載中心，下載 `ecpay-logistics-for-woocommerce.zip`。
  後台 → 插件 → 上傳安裝並啟用。

- [ ] **Step 2：設定超商取貨（7-11）**

  後台 → WooCommerce → 設定 → 運送 → 運送區域 → 台灣 → 新增運送方式 → ECPay 物流：
  - 物流類型：超商取貨（C2C）
  - 物流商：7-11
  - 操作模式：測試
  - 運費：NT$60（或依你設定）
  - 重量限制：5 kg

- [ ] **Step 3：設定超商取貨（全家）**

  同上步驟，物流商改選「全家」，儲存。

- [ ] **Step 4：設定黑貓宅配**

  後台 → WooCommerce → 設定 → 運送 → 台灣 → 新增運送方式 → WooCommerce 內建「固定費率」：
  - 方式標題：黑貓宅配
  - 費用：NT$120
  - 說明：2-3 個工作天到貨

- [ ] **Step 5：設定自取**

  後台 → WooCommerce → 設定 → 運送 → 台灣 → 新增運送方式 → 「本地取貨」（Local Pickup）：
  - 方式標題：到店自取
  - 費用：免費
  - 地址：工作室地址

- [ ] **Step 6：測試**

  結帳頁商品為實體商品時，確認出現 3 種運送選項：超商取貨（7-11）、超商取貨（全家）、黑貓宅配、到店自取。
  選擇超商取貨 → 確認出現「選擇門市」按鈕（ECPay 門市地圖）。

- [ ] **Step 7：Commit**

  ```bash
  git commit --allow-empty -m "config: ECPay logistics (711, FamilyMart, home delivery, self-pickup)"
  ```

---

## Task 8：數位商品設定

**目標：** 設定 WooCommerce 數位下載保護，包含下載次數限制、連結過期時間、信箱通知。

**Files:**
- Create: `themes/astra-child/woocommerce/myaccount/downloads.php`

- [ ] **Step 1：確認 WooCommerce 數位下載設定**

  後台 → WooCommerce → 設定 → 商品 → 可下載商品：
  - 檔案下載方法：「強制下載」（Force Downloads）— 保護直接連結，透過 PHP 串流
  - 需登入才能下載：勾選
  - 訪客下載需輸入購買時的 Email：勾選

- [ ] **Step 2：設定預設下載限制**

  後台 → WooCommerce → 設定 → 商品 → 可下載商品：
  - 預設下載次數限制：5
  - 下載過期天數：365

  > 個別商品可覆寫此設定

- [ ] **Step 3：確認 woocommerce_uploads 目錄保護**

  確認 `wp-content/uploads/woocommerce_uploads/.htaccess` 存在且內容為：
  ```
  Options -Indexes
  deny from all
  ```
  若使用 Nginx 而非 Apache，確認 Nginx 設定包含：
  ```nginx
  location ~* /wp-content/uploads/woocommerce_uploads {
      deny all;
  }
  ```

- [ ] **Step 4：客製化下載頁面（會員帳號）**

  建立 `wp-content/themes/astra-child/woocommerce/myaccount/downloads.php`，複製 WooCommerce 原始模板後修改標題為三語：

  ```php
  <?php
  /**
   * My Account - Downloads
   * 覆寫 WooCommerce 下載頁面，加入多語系支援
   */
  defined( 'ABSPATH' ) || exit;
  ?>

  <h2><?php echo esc_html( apply_filters( 'woocommerce_endpoint_downloads_title',
      __( '我的數位商品', 'astra-child' ) ) ); ?></h2>

  <?php if ( ! $downloads ) : ?>
      <div class="woocommerce-Message woocommerce-Message--info woocommerce-info">
          <?php esc_html_e( '目前尚無可下載的商品。', 'astra-child' ); ?>
      </div>
  <?php else : ?>
      <table class="woocommerce-table woocommerce-MyAccount-downloads shop_table shop_table_responsive">
          <thead>
              <tr>
                  <th><?php esc_html_e( '商品', 'astra-child' ); ?></th>
                  <th><?php esc_html_e( '剩餘下載次數', 'astra-child' ); ?></th>
                  <th><?php esc_html_e( '下載期限', 'astra-child' ); ?></th>
                  <th><?php esc_html_e( '下載', 'astra-child' ); ?></th>
              </tr>
          </thead>
          <tbody>
              <?php foreach ( $downloads as $download ) : ?>
                  <tr>
                      <td><?php echo esc_html( $download['product_name'] ); ?></td>
                      <td><?php echo esc_html( 0 === (int) $download['downloads_remaining']
                          ? __( '無限制', 'astra-child' )
                          : $download['downloads_remaining'] ); ?></td>
                      <td><?php echo esc_html( $download['access_expires']
                          ? date_i18n( get_option( 'date_format' ), strtotime( $download['access_expires'] ) )
                          : __( '無期限', 'astra-child' ) ); ?></td>
                      <td>
                          <a href="<?php echo esc_url( $download['download_url'] ); ?>"
                             class="woocommerce-MyAccount-downloads-file button alt">
                              <?php esc_html_e( '下載', 'astra-child' ); ?>
                          </a>
                      </td>
                  </tr>
              <?php endforeach; ?>
          </tbody>
      </table>
  <?php endif; ?>
  ```

- [ ] **Step 5：測試數位商品購買流程**

  1. 後台 → 新增商品 → 商品名稱：「測試數位商品」
  2. 商品類型：可下載（Downloadable）+ 虛擬（Virtual）
  3. 上傳測試檔案（小型 PDF）
  4. 設定價格：NT$100，下載次數：3，過期天數：30
  5. 前往前台購買 → 付款（測試模式）→ 成功後：
     - 確認信箱收到含下載連結的訂單確認信
     - 登入會員帳號 → 前往「我的數位商品」→ 確認出現下載按鈕
     - 點擊下載 → 確認檔案成功下載
     - 下載超過 3 次 → 確認顯示「下載次數已達上限」

- [ ] **Step 6：Commit**

  ```bash
  git add themes/astra-child/woocommerce/
  git commit -m "feat: custom digital downloads page with zh-TW/en/ja i18n support"
  ```

---

## Task 9：首頁與主要頁面建立

**目標：** 建立首頁（Hero + 精選商品）、關於我們、聯絡我們、隱私權政策頁面。

**Files:**
- Modify: `themes/astra-child/style.css`（首頁 Hero 樣式）

- [ ] **Step 1：設定靜態首頁**

  後台 → 設定 → 閱讀 → 首頁顯示 → 靜態頁面 → 新增「首頁」頁面並選取。

- [ ] **Step 2：設計首頁 Hero 區塊**

  後台 → 頁面 → 首頁 → 使用 WordPress 區塊編輯器（Gutenberg）：
  - 新增「封面圖片」區塊 → 上傳工作室品牌圖 → 標題文字：工作室名稱 + 標語
  - 新增「按鈕」區塊 → 文字：「立即選購」→ 連結至 `/shop/`

  在 `astra-child/style.css` 加入 Hero 樣式：
  ```css
  /* Hero Banner */
  .wp-block-cover.hero-banner {
      min-height: 500px;
  }
  .wp-block-cover.hero-banner .wp-block-cover__inner-container h1 {
      font-size: 2.5rem;
      font-weight: 700;
  }
  ```

- [ ] **Step 3：加入精選商品區塊**

  首頁繼續編輯 → 加入 WooCommerce「精選商品」區塊（需已有商品）→ 設定顯示 4–6 個商品。

- [ ] **Step 4：建立「關於我們」頁面**

  後台 → 頁面 → 新增：
  - 標題：關於我們（繁中）/ About Us（英）/ 私たちについて（日）
  - 內容：工作室簡介、品牌故事
  - 加入導覽列

- [ ] **Step 5：建立「聯絡我們」頁面**

  後台 → 頁面 → 新增 → 標題：聯絡我們。
  安裝「Contact Form 7」插件 → 建立聯絡表單（姓名、Email、訊息）→ 插入頁面。

- [ ] **Step 6：建立「隱私權政策」與「服務條款」頁面**

  後台 → 設定 → 隱私設定 → 產生預設隱私權政策頁面 → 依台灣個資法修改內容。
  後台 → 頁面 → 新增「服務條款」頁面 → 填入退換貨政策、數位商品不退款聲明。

  後台 → WooCommerce → 設定 → 進階 → 頁面設定：
  - 服務條款頁面：選取剛建立的「服務條款」

- [ ] **Step 7：設定導覽列**

  後台 → 外觀 → 選單 → 建立主要選單，加入：首頁、商店、關於我們、聯絡我們（含語言切換器）。

- [ ] **Step 8：Commit**

  ```bash
  git add themes/astra-child/style.css
  git commit -m "feat: homepage hero, about, contact, and policy pages"
  ```

---

## Task 10：SEO 設定（Yoast SEO + WPML hreflang）

**目標：** 安裝 Yoast SEO，設定站點地圖、每個語言版本的 hreflang 標記。

**Files:**
- 無程式碼（插件設定）

- [ ] **Step 1：安裝 Yoast SEO**

  後台 → 插件 → 新增 → 搜尋「Yoast SEO」→ 安裝並啟用 → 依精靈完成基本設定：
  - 網站類型：線上商店
  - 組織名稱：工作室名稱
  - 組織 Logo：上傳 Logo

- [ ] **Step 2：設定 XML Sitemap**

  後台 → Yoast SEO → 設定 → 內容類型：確認「商品」已啟用 sitemap。
  前往 `https://yourdomain.com/sitemap_index.xml` → 確認 sitemap 可存取。
  提交至 Google Search Console。

- [ ] **Step 3：確認 WPML hreflang 整合**

  後台 → WPML → 設定 → SEO → 確認「與 Yoast SEO 整合 hreflang」已啟用。
  前往繁中首頁 → 檢視原始碼 → 確認 `<head>` 內出現：
  ```html
  <link rel="alternate" hreflang="zh-Hant" href="https://yourdomain.com/" />
  <link rel="alternate" hreflang="en" href="https://yourdomain.com/en/" />
  <link rel="alternate" hreflang="ja" href="https://yourdomain.com/ja/" />
  ```

- [ ] **Step 4：設定各語言首頁 Meta 描述**

  後台 → 頁面 → 首頁（繁中版）→ Yoast SEO 區塊 → 填入 Meta 描述（繁中）。
  切換至英文版首頁 → 填入 Meta 描述（英文）。
  切換至日文版首頁 → 填入 Meta 描述（日文）。

- [ ] **Step 5：Commit**

  ```bash
  git commit --allow-empty -m "config: Yoast SEO with WPML hreflang for zh-TW/en/ja"
  ```

---

## Task 11：效能優化（WP Rocket + Cloudflare）

**目標：** 安裝 WP Rocket 快取，設定 Cloudflare CDN，Google PageSpeed Insights 分數 ≥ 80。

**Files:**
- 無程式碼（插件設定 + Cloudflare DNS）

- [ ] **Step 1：安裝 WP Rocket**

  前往 `wp-rocket.me` 購買授權（$49/年），下載 `wp-rocket.zip`。
  後台 → 插件 → 上傳安裝並啟用 → 輸入授權碼。
  WP Rocket 自動啟用推薦設定。

- [ ] **Step 2：WP Rocket 設定（WooCommerce 優化）**

  後台 → 設定 → WP Rocket → 快取：
  - 為已登入的 WordPress 使用者啟用快取：關閉（WooCommerce 購物車需關閉）
  - 行動版快取：啟用

  後台 → 設定 → WP Rocket → 檔案最佳化：
  - 縮小 CSS：啟用
  - 縮小 JavaScript：啟用
  - 延遲執行 JavaScript：啟用

  後台 → 設定 → WP Rocket → 排除快取的 URL（加入這幾個 WooCommerce 頁面）：
  ```
  /cart/
  /checkout/
  /my-account/
  ```

- [ ] **Step 3：設定 Cloudflare**

  前往 `cloudflare.com` 建立免費帳號 → 新增網域 → 依指示將網域 Nameserver 改為 Cloudflare。
  後台 → Cloudflare DNS → 確認 A Record 指向主機 IP（橘色雲朵 = Proxy 啟用）。

  Cloudflare 設定 → SSL/TLS → 選「Full (strict)」。
  Cloudflare 設定 → Speed → Auto Minify：勾選 JS、CSS、HTML。
  Cloudflare 設定 → Caching → Browser Cache TTL：4 hours。

- [ ] **Step 4：驗證效能**

  前往 `pagespeed.web.dev` → 輸入 `https://yourdomain.com` → 確認行動版分數 ≥ 80，桌機版 ≥ 90。
  
  若分數不足，常見修復：
  - 商品圖片改為 WebP 格式（使用「Imagify」插件批次轉換）
  - 確認所有圖片已設定 `width` 和 `height` 屬性（避免 CLS 分數扣分）

- [ ] **Step 5：Commit**

  ```bash
  git commit --allow-empty -m "config: WP Rocket cache + Cloudflare CDN, PageSpeed >= 80"
  ```

---

## Task 12：安全設定與上線前檢查

**目標：** 安裝安全插件、設定備份、完成上線前完整測試清單。

**Files:**
- 無程式碼（插件設定）

- [ ] **Step 1：安裝 Limit Login Attempts Reloaded**

  後台 → 插件 → 新增 → 搜尋「Limit Login Attempts Reloaded」→ 安裝並啟用。
  設定 → 登入失敗 4 次後鎖定 20 分鐘。

- [ ] **Step 2：設定自動備份**

  後台 → 插件 → 新增 → 搜尋「UpdraftPlus」→ 安裝並啟用。
  設定 → 排程：每週自動備份 → 備份目的地：Google Drive 或 Dropbox。

- [ ] **Step 3：將金流切換為正式模式**

  ECPay：後台 → 付款 → ECPay → 操作模式改為「正式」→ 填入正式 MerchantID / HashKey / HashIV。
  Stripe：後台 → 付款 → Stripe → 關閉測試模式 → 填入正式 Publishable Key / Secret Key。
  PayPal：後台 → 付款 → PayPal → 切換至正式模式。

- [ ] **Step 4：上線前完整測試清單**

  以下每項測試必須通過（用正式帳號，小額實際交易）：

  **實體商品流程：**
  - [ ] 新增商品至購物車
  - [ ] 結帳 → 選擇超商取貨（7-11）→ 選門市 → ECPay 信用卡付款成功
  - [ ] 後台確認訂單狀態為「處理中」
  - [ ] 後台手動更新訂單狀態為「已完成」→ 確認顧客收到出貨通知信

  **數位商品流程：**
  - [ ] 購買數位商品 → Stripe 信用卡付款成功
  - [ ] 確認信箱收到下載連結信
  - [ ] 登入會員帳號 → 確認「我的數位商品」頁面顯示下載按鈕
  - [ ] 下載成功

  **多語言：**
  - [ ] 切換英文 → 所有頁面（首頁、商店、結帳）正確顯示英文
  - [ ] 切換日文 → 所有頁面正確顯示日文
  - [ ] 英文版商品頁 `hreflang` 標記正確

  **安全：**
  - [ ] 故意輸入錯誤密碼 4 次 → 確認被鎖定
  - [ ] `https://yourdomain.com/wp-content/uploads/woocommerce_uploads/` → 應顯示 403 Forbidden

- [ ] **Step 5：最終 Commit**

  ```bash
  git add themes/astra-child/
  git commit -m "feat: complete ecommerce setup - production ready"
  ```

---

## 附錄：上線後第一週待辦

- Google Analytics 4 安裝（使用「Site Kit by Google」插件）
- 提交 sitemap 至 Google Search Console
- 建立 10 個以上商品（含各語言翻譯版本）
- 設定商店社群媒體 Open Graph 圖片（Yoast SEO 設定）
- 測試行動裝置購物體驗（iOS Safari + Android Chrome）
