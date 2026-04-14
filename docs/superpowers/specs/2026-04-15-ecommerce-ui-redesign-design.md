# 電商平台 UI 改版設計文件

**日期：** 2026-04-15
**版本：** 1.0
**範疇：** Astra Child Theme 視覺改版（CSS + WooCommerce 模板覆寫）

---

## 1. 設計方向總結

### 風格定位
「**大膽現代 × 雜誌感**」—— 深色底調、強烈字體對比、編輯排版結構。
靈感來源：潮流品牌雜誌 × 選品店視覺。

### 設計關鍵字
- 深色背景（非全黑，帶暖咖調）
- 混搭字體（超粗無襯線 + 斜體襯線，製造視覺張力）
- 陶土橘重點色
- 雜誌式版型（主打大格 + 小格環繞）
- 中英雙語標籤（符合台灣 × 國際市場定位）

---

## 2. 設計系統

### 色彩 Token

| Token | 色值 | 用途 |
|-------|------|------|
| `--bg-deep` | `#181614` | 頁面底色 |
| `--bg-card` | `#242220` | 商品卡片、區塊背景 |
| `--bg-hover` | `#2e2a26` | Hover 狀態、商品圖背景 |
| `--text-main` | `#f2ede8` | 主要文字（暖白） |
| `--text-muted` | `#8a8480` | 次要文字、標籤、說明 |
| `--accent` | `#e8845a` | 重點色（陶土橘）：CTA、標籤、強調文字 |
| `--accent-dim` | `#b86540` | Hover 狀態重點色 |
| `--border` | `#2e2a26` | 線條、分隔 |

### 字體系統

| 層級 | 字體 | 規格 | 用途 |
|------|------|------|------|
| 大標（混搭） | Barlow Condensed Black + Playfair Display Italic | 32–48px | Hero 標題、頁面標題 |
| 中標 | Barlow Condensed Bold | 18–28px | 商品名稱、區塊標題 |
| 斜體強調 | Playfair Display Italic | 與中標搭配 | 副標、英文名稱、品牌感文字 |
| 內文 | Noto Sans TC Light/Regular | 11–13px | 商品描述、說明文字 |
| 小標籤 | Barlow Condensed（全大寫 + 字距） | 9–10px | Eyebrow、標籤、按鈕 |

Google Fonts 載入：
```
Barlow Condensed: 700, 900, italic 700
Playfair Display: 700, italic 700
Noto Sans TC: 300, 400, 500
```

---

## 3. 頁面設計規格

### 3.1 導覽列（Navigation）

- 背景：`--bg-deep`，底部 1px `--border` 線
- Logo：混搭字體 —— 「STUDIO」用 Barlow Condensed Black，「*Objects*」用 Playfair Italic `--accent` 色
- 右側連結：Shop、About（`--text-muted`）+ Cart 用 `--accent` 框線按鈕
- 語言切換器（WPML）：放導覽列最右側，`--text-muted` 色
- Sticky 固定於頂部

### 3.2 首頁 Hero 區塊

版型：**全版衝擊感 + 左右分割**（A+C 混合）

```
┌─────────────────────────────────────────┐
│ [左半] 文字區                [右半] 商品圖 │
│                                          │
│  EYEBROW（陶土橘小標籤）    ┌──────────┐ │
│  STUDIO                     │  商品圖  │ │
│         *Objects*           │  佔位    │ │
│  副標文字                   └──────────┘ │
│  [EXPLORE NOW] ← CTA 實心按鈕            │
│                                          │
│                ── SCROLL ↓ ──            │
└─────────────────────────────────────────┘
```

- 高度：`min-height: 80vh`（桌機）、`min-height: 60vh`（手機）
- 左側：`padding: 10% 5%`，flex column，justify-content: flex-end
- 右側：`background: --bg-hover`，商品主圖 object-fit: cover
- Hero 大標：`font-size: clamp(48px, 8vw, 96px)`，Barlow Black + Playfair Italic 混搭
- CTA 按鈕：`background: --accent`、`color: --bg-deep`、字距 3px、大寫

### 3.3 跑馬燈條（Marquee Strip）

- Hero 下方緊接，`background: --accent`
- 文字：`color: --bg-deep`，Barlow Condensed Black，全大寫，字距 4px
- 內容：免運資訊、新品通知、配送說明
- CSS `animation: marquee 20s linear infinite`（純 CSS，無 JS 依賴）

### 3.4 首頁精選商品區

```
┌──────────────────────────────────────────┐
│ EYEBROW                          View All │
│ New *Arrivals*                            │
├──────────────────────────────────────────┤
│ ┌─────────────────────────────────────┐  │
│ │  主打商品（佔全寬 2:1 橫幅）        │  │
│ └─────────────────────────────────────┘  │
│ ┌──────────────┐  ┌──────────────┐      │
│ │  商品 02     │  │  商品 03     │      │
│ └──────────────┘  └──────────────┘      │
└──────────────────────────────────────────┘
```

- 主打商品：`grid-column: span 2`，aspect-ratio 2:1
- 角落標籤：New（陶土橘實色）、Digital（深色底 + 陶土橘文字）、限定（深灰底）
- 卡片間距：`gap: 2px`（緊密雜誌感）

### 3.5 關於工作室 Strip

- 左右 1:1 分割
- 左：eyebrow + 混搭標題 + 1-2 行簡述
- 右：工作室圖片佔位
- 底部加「了解更多 →」文字連結

### 3.6 頁尾（Footer）

- 背景：`#0d0b0a`（比頁面底色更深）
- 中央對齊：Logo（混搭字體）+ 頁面連結 + 語言切換 + 版權
- 連結色：`--text-muted`，hover 變 `--accent`

---

## 4. 商品列表頁（Shop Archive）

### 頁面標題區
- Eyebrow：「所有商品 / COLLECTION」
- 混搭大標：`All *Products*`
- 商品數量 + 排序下拉（右對齊）

### 篩選標籤列
水平捲動標籤：全部 / 實體商品 / 數位下載 / 新品 / 限定
- 未選：`border: 1px solid --border`，`color: --text-muted`
- 已選：`border-color: --accent`，`color: --accent`

### 商品卡片（`content-product.php` 覆寫）

```
┌────────────────────────┐
│  [標籤]     [♡願望清單] │  ← 商品圖，aspect-ratio: 1
│                        │
│      商品圖             │
└────────────────────────┘
│ 商品名稱（大寫）        │
│ Item Name（斜體橘色）   │
│ NT$1,200      [+ 加入] │  ← 快速加入購物車
└────────────────────────┘
```

- 卡片 hover：背景變 `--bg-hover`，輕微 transform: translateY(-2px)
- 「+ 加入」：框線小按鈕，hover 填滿陶土橘

### 分頁
「Load More」文字按鈕，替代預設分頁數字。

---

## 5. 商品詳細頁（Single Product）

### 商品圖區
- 佔頁面上半，`aspect-ratio: 4/3`
- 輪播點點指示（純 CSS 或 WooCommerce 內建 gallery）
- 背景：`--bg-hover`

### 商品資訊區
- 標籤列（分類 + New/Digital）
- 混搭標題（同上規格）
- 價格：Barlow Condensed 20px
- 描述：Noto Sans TC，13px，`--text-muted`
- 主 CTA：「加入購物車 — Add to Cart」陶土橘實心全寬按鈕
- 次要：「♡ 加入願望清單」線框按鈕

---

## 6. 實作方案

**方案二：CSS + WooCommerce 模板覆寫**

### 修改範圍

| 檔案 | 動作 | 說明 |
|------|------|------|
| `astra-child/style.css` | 改寫 | 全域 CSS 變數、Astra 覆蓋、WooCommerce 全域樣式 |
| `astra-child/functions.php` | 修改 | 載入 Google Fonts（enqueue_style）|
| `astra-child/woocommerce/content-product.php` | 新增 | 商品卡片模板覆寫 |
| `astra-child/woocommerce/archive-product.php` | 新增 | 商品列表頁模板覆寫 |
| `astra-child/woocommerce/single-product/` | 新增 | 商品詳細頁局部模板（title, price, add-to-cart）|

### 不動範圍
- WooCommerce 結帳流程（checkout/）：維持功能優先，僅套用配色
- 會員帳號頁（myaccount/）：已有覆寫，僅微調顏色

---

## 7. 不在範圍內（Out of Scope）

- Elementor 或頁面編輯器
- 動態滾動特效（GSAP、ScrollTrigger）
- 商品 3D 展示
- 深色 / 淺色模式切換
- 結帳頁大改版（維持 WooCommerce 預設流程）
