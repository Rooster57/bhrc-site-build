# BHRC Change Audit Log
Started: 2026-06-02

Track every site modification. Reference: page ID, section, what changed, why, how to verify.

---

## 2026-06-02

### #001 — Page ID System
- **Scope**: ALL pages and posts (14 total)
- **What**: Added amber tag (white text, #C4762A background) at top-right corner of every page showing unique code
- **Codes**: Homepage=BHR-H1, About Farook=BHR-A1, About Site=BHR-A2, Blog=BHR-B1, Free Guide=BHR-G1, Art of Becoming=BHR-P1, Resources=BHR-R1, Contact=BHR-C1, Thank You=BHR-T1, Posts=BHR-X1 through X5
- **Method**: CSS `::before` pseudo-elements + content rules, pushed via global-styles/25 POST
- **Map**: `E:\CCWS\PROJECTS\BHRC\site-build\page-id-map.md`
- **Verify**: Look for amber "BHR-XX" tag at top-right of any page

### #002 — About Farook Section Spacing
- **Scope**: Homepage (BHR-H1) only — navy (#1A1F2E) section near bottom
- **What**: Added 32px left and right padding to prevent text touching edges
- **Method**: Edited page content.raw via PUT /pages/8
- **Verify**: About Farook text is indented from both edges on mobile and desktop

### #003 — Font System (Cross-Platform)
- **Scope**: ALL pages (global)
- **What**: Replaced Google Fonts `<link>` injection with `@font-face` rules loading directly from fonts.gstatic.com CDN
- **Fonts installed**: Cormorant Garamond (10 variants: 300–700 normal + italic), Lora (8 variants: 400–700 normal + italic)
- **Method**: Extracted font data from WordPress Font Library (google-fonts collection), generated @font-face CSS, pushed via global-styles/25 POST
- **Why**: `<link>` tags in body content only work on pages where explicitly added. @font-face in global CSS works on EVERY page. fonts.gstatic.com CDN works on all devices/platforms.
- **Fallback**: Georgia (universally available serif)
- **Verify**: Headings render in Cormorant Garamond, body text in Lora — on any device. Ctrl+Shift+R to force reload.

### #004 — Global Styles CSS Management Rule
- **What**: Established rule that ALL CSS pushes to global-styles/25 must be the COMPLETE combined file (fonts + design + page IDs). Partial pushes replace everything.
- **Canonical CSS file**: `E:\CCWS\PROJECTS\BHRC\site-build\bhrc-design-fix.css` — must be kept in sync with what's on the site.

---

## CSS Push Checklist (use before every push)
- [ ] Read current full CSS from `bhrc-design-fix.css`
- [ ] Add @font-face block at top (Cormorant Garamond + Lora)
- [ ] Add page ID block at bottom (amber boxes for all 14 pages/posts)
- [ ] POST complete CSS to `/wp/v2/global-styles/25`
- [ ] Verify: @font-face present, design CSS present, all 14 page IDs present
- [ ] Update this audit log

---

### #005 — Author Name Fix
- **Scope**: ALL pages/posts (global user setting)
- **What**: Changed displayed author name from `farook.maricar@hotmail.com` to `Farook Maricar`
- **Method**: PUT /wp/v2/users/me with name, nickname, first_name, last_name
- **Verify**: Post pages show "Farook Maricar" not email address

### #006 — AI Metadata Removal (Posts)
- **Scope**: All 5 blog posts (IDs 40–44)
- **What**: Removed from each post:
  - Redundant `<h1>` title in content (template already shows title via wp-block-post-title)
  - AI metadata line: `*Blog post — BeHappyRetired.com | Pillar: X | ~Y words*`
  - Internal workflow notes: `Content Pillar:`, `Status: PENDING`, `Nova SEO`, `Blake re-tone`, etc.
  - Replaced with clean pillar tag: small sage-green uppercase label at article top
  - Set human excerpts from the actual opening line of each article
- **Method**: PUT /wp/v2/posts/{id} with cleaned content + crafted excerpt
- **Result**: 
  - Duplicate titles eliminated (1 H1 per page, down from 2)
  - Zero AI metadata on homepage or post pages
  - Excerpts are actual story hooks, not database records
- **Verify**: Homepage Latest Writing cards show genuine teasers. Post pages show title once, author name (not email), clean pillar tag.

### #007 — Navigation Cleanup
- **Scope**: Navigation menu (ID=4) + Blog page (ID=7)
- **What**:
  - Renamed "Articles" → "Blog Posts" in main nav
  - Rebuilt Blog page: removed raw HTML "The Blog" heading, "Articles coming soon" stale text, redundant font link
  - Blog page now has proper WordPress query block showing all posts (12 per page, 3 columns, pagination)
  - Page title changed from "Blog" to "Latest Posts"
- **Verify**: Nav shows "Blog Posts" dropdown. Clicking it goes to a clean post listing. Category pages (Joyful Living, Emotional Wellness, AgeTech) accessible from dropdown.

### #008 — Category Archive Pages Cleanup
- **Scope**: Archive template (twentytwentyfive//archive)
- **What**:
  - Removed "Category:" prefix from archive titles (now shows just "Joyful Living" not "Category: Joyful Living")
  - Changed posts-per-page from 3 to 12 (scales for 50+ articles)
  - Replaced full post-content with post-excerpt in archive listing (lighter, cleaner)
  - Added CSS to hide "Category:" text prefix
- **Verify**: Go to /category/joyful-living/ — shows clean title, excerpts (not full content), 12 posts per page

### #009 — Homepage Title Cleanup
- **Scope**: Homepage (BHR-H1)
- **What**: Added CSS to hide redundant page-title H1 ("Be Happy Retired") — the hero section already has the real headline
- **Method**: CSS in both global-styles AND homepage inline style block (for reliability)
- **Verify**: Homepage shows only the hero H1 ("Retirement isn't an ending..."), not "Be Happy Retired" above it

### #010 — Hardcoded CSS Fix (Brand Colors Now Work)
- **Scope**: ALL pages (global CSS)
- **What**: Replaced ALL 55 `var(--*)` references with hardcoded hex values
- **Why**: WordPress strips `:root { --parchment: #F5EFE0; }` CSS variable definitions from global-styles. Every `var(--parchment)` resolved to nothing (transparent), so backgrounds appeared white. Without variables, the values were never applied.
- **Impact**: Body background, content areas, headings, links, buttons — all now render with actual brand colors (#F5EFE0 parchment, #1A1F2E navy, #C4762A amber, #7A9977 sage)
- **Verify**: Sub-pages no longer have white backgrounds. Brand colors visible everywhere.

### #011 — Slogan Added
- **Scope**: Homepage (BHR-H1)
- **What**: Added "Finding the Happiness of Pursuit" as a thin strip above the navy hero section. Amber italic text on parchment background.
- **Verify**: Slogan visible between nav and hero on homepage.

### #012 — Page ID CSS Fix
- **Scope**: ALL pages
- **What**: Rewrote page ID CSS rules to be self-contained. Previous version split positioning and content across separate rules, which meant neither worked (CSS ::before needs content+position in same rule to generate)
- **Added**: `body.blog::before` rule for Blog page (uses `blog` body class instead of `page-id-7`)
- **Verify**: Amber BHR-XX tag visible at top-right of every page

### #013 — Page IDs: Hardcoded HTML (Guaranteed Fix)
- **Scope**: ALL 14 pages and posts
- **What**: Replaced CSS `::before` pseudo-element approach with actual HTML `<div>` injected into each page's content. CSS ::before on `<body>` was unreliable across pages.
- **Method**: Prepended `wp:html` block with fixed-position amber div to every page/post content
- **Verify**: Amber tag at top-right of EVERY page. Guaranteed.

### #014 — WordPress Color Presets Override
- **Scope**: Global
- **What**: Pushed color palette settings to override WordPress theme presets:
  - `base` = #F5EFE0 (Parchment)
  - `contrast` = #1A1F2E (Navy)
  - `accent` = #C4762A (Amber)
  - `accent-2` = #7A9977 (Sage)
  - `accent-3` = #EDE5D0 (Parchment Mid)
- **Also**: Added aggressive CSS to force parchment on elements that might use WordPress white/base background classes
- **Verify**: All pages use brand palette. No white content areas.

### #015 — System-Wide Human-Centered Audit
- **Scope**: All 13 pages (homepage, pages, posts, category archives)
- **Method**: Audited every page against 6 standards: font size, contrast, visual weight, warmth, consistency, machine-text
- **Fixed**:
  - Category tags: 11px/0.7rem → 13px/0.85rem (seniors can read them now)
  - Post dates: 0.75rem → 0.85rem
  - Article pillar tags in post content: 0.75rem → 0.85rem
  - Low-contrast muted text: #9A8A7A → #6B5D4F (darker on parchment)
  - Excerpt text: 15px → 16px
  - Substack link: redesigned from footnote to proper amber-bordered callout section
- **Confirmed clean**: All machine-text patterns removed, all page IDs visible, no white content areas, brand colors flowing
- **Standard enforced**: No visitor-facing text below 0.85rem (13px). The only exception is page ID tags (11px) which are admin tools, not for visitors.

## App Password (verified 2026-06-02)
- File: `E:\Keys-SECURE\bhrc-wp-api-credentials.md`
- Username: farook.maricar@hotmail.com
- App password: [REDACTED — stored in E:\Keys-SECURE\bhrc-wp-api-credentials.md]
- WP-Admin login: farook.maricar@hotmail.com / (see bhrc-wp-live-credentials.md)
