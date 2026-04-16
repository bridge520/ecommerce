# 上線前檢查清單

> 本機開發完成後，上線至正式伺服器前必須逐項確認。

## ✅ 已完成（本機）

- [x] Astra child theme 建立並啟用
- [x] WooCommerce 安裝設定（TWD、UTC+8、5% 稅率）
- [x] 3 個示範商品（實體 × 1、數位 × 1、實體 × 1）
- [x] 數位下載頁面 `myaccount/downloads.php` 客製化
- [x] 首頁 Hero Banner + 商品列表
- [x] 關於我們、聯絡我們、隱私權政策、服務條款 頁面
- [x] Yoast SEO 設定（sitemap、meta 描述）
- [x] 效能優化（移除 emoji/oEmbed、Heartbeat 降速、圖片尺寸精簡）
- [x] XML-RPC 關閉
- [x] WordPress 版本資訊隱藏
- [x] 使用者列舉防護（?author=N redirect）
- [x] 管理員帳號不使用 'admin'（已改為 studio_admin）
- [x] Limit Login Attempts 插件啟用
- [x] WP_DEBUG 關閉
- [x] GitHub Actions CI（PHP 語法檢查）

---

## 🔲 上線前必做

### 主機 & 網域
- [ ] 購買主機（建議：SiteGround / Cloudways / 台灣遠振）
- [ ] 購買網域並指向主機 IP
- [ ] 申請 SSL 憑證（Let's Encrypt 免費）並啟用 HTTPS

### WordPress 設定
- [ ] 將資料表前綴從 `wp_` 改為隨機前綴（例如 `ws7k_`）
  ```sql
  -- 需在移轉前執行，或使用 Better Search Replace 插件
  ```
- [ ] `wp-config.php` 加入安全金鑰（從 https://api.wordpress.org/secret-key/1.1/salt/ 產生）
- [ ] 確認 `WP_DEBUG = false`
- [ ] 更新 siteurl 和 home 為正式網址

### WooCommerce
- [ ] 後台 → WooCommerce → 設定 → 進階 → 勾選「強制安全結帳」（Force SSL）
- [ ] 設定訂單確認、出貨通知 Email 範本
- [ ] 測試完整結帳流程（加入購物車 → 結帳 → 付款 → 確認信）

### 金流 & 物流（需商家帳號）
- [ ] ECPay 綠界：申請商家帳號 → 安裝插件 → 填入 API 金鑰
  - 信用卡、ATM、超商代碼、LINE Pay
- [ ] ECPay 物流：申請帳號 → 設定超商取貨（711、全家）、宅配
- [ ] NewebPay 街口支付（選配）
- [ ] Stripe 國際信用卡（選配）
- [ ] PayPal 國際付款（選配）

### 多語言（需授權）
- [ ] 購買 WPML Multilingual CMS 授權（$99/年）
- [ ] 安裝 WPML + WooCommerce Multilingual
- [ ] 設定繁中（預設）、英文、日文
- [ ] 翻譯首頁、商品頁、結帳流程

### 效能
- [ ] 購買 WP Rocket 授權（$49/年）並安裝設定
- [ ] 設定 Cloudflare CDN（免費方案即可）
  - DNS Proxy 啟用
  - SSL/TLS → Full (strict)
  - Auto Minify JS/CSS/HTML
- [ ] Google PageSpeed Insights 行動版 ≥ 80 分

### SEO & 分析
- [ ] 提交 sitemap 至 Google Search Console
  `https://yourdomain.com/sitemap_index.xml`
- [ ] 安裝 Google Analytics 4（GA4）
- [ ] 設定各語言版本 hreflang（WPML 自動處理）

### 伺服器安全
- [ ] nginx 設定關閉目錄列表：`autoindex off;`
- [ ] 設定 `X-Frame-Options`, `X-Content-Type-Options`, `Strict-Transport-Security` headers
- [ ] 設定自動備份（UpdraftPlus 或主機內建）
- [ ] 安裝 Wordfence 並執行首次掃描

### 上線後 24 小時內
- [ ] 瀏覽各頁面確認顯示正常
- [ ] 用真實信用卡測試一筆小額訂單
- [ ] 確認訂單通知 Email 正常寄達
- [ ] 確認 Google Search Console 無爬蟲錯誤

---

## 📋 插件清單（上線版）

| 插件 | 用途 | 費用 |
|------|------|------|
| WooCommerce | 電商核心 | 免費 |
| Astra | 佈景主題 | 免費 |
| Yoast SEO | SEO | 免費 |
| WPML | 多語言 | $99/年 |
| ECPay | 台灣金流+物流 | 免費（按交易抽成） |
| Stripe | 國際金流 | 免費（2.9%+$0.3） |
| WP Rocket | 快取效能 | $49/年 |
| UpdraftPlus | 自動備份 | 免費（基本版） |
| Limit Login Attempts | 防暴力破解 | 免費 |
| Wordfence | 防火牆+掃描 | 免費（基本版） |

---

*最後更新：2026-04-14*
