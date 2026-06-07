# BHRC Site Standards
*Every agreed convention. Nothing drifts. Last updated: 2026-06-03*

---

## HUMAN-CENTERED REVIEW — MANDATORY BEFORE REPORTING ANY CHANGE AS DONE

**This site serves seniors. Every change is measured against their experience.**

Before reporting any task as complete, I run this checklist silently. If any check fails, I fix it BEFORE telling Farook it's done. He should never have to catch these.

| Check | Standard |
|-------|----------|
| **Font size** | Body text ≥ 1rem (16px). Key CTAs and important links ≥ 1.1rem. Nothing below 0.85rem except page IDs and category tags. Seniors read at arm's length. |
| **Contrast** | Text must clearly stand out from its background. Light text on light backgrounds fails. Dark text on dark backgrounds fails. No #EDE5D0 on #F5EFE0 (too close). No tiny pale links that blend into surrounding text. |
| **Visual weight** | The most important element in any section must be the most visually prominent. If a CTA or link is important, it must LOOK important — not be a footnote. |
| **Warmth** | Does this feel like a human made it? Or like a machine placed it? No sterile labels, no database-field formatting, no inline metadata. Every element passes the "would Farook write this?" test. |
| **Consistency** | Does this match what's already on the site? Same font sizes, same spacing rhythm, same color usage. No one-off styling. |
| **Mobile** | How does this look on a phone held at reading distance? Stacked? Readable? Tap targets ≥ 44px? |

**The rule**: If I wouldn't confidently show this to a 68-year-old reader and have them understand it immediately, I haven't finished the task.

---

## Brand Identity

| Element | Standard | Notes |
|---------|----------|-------|
| **Font — Headings** | Cormorant Garamond | Google Fonts, loaded via @font-face from fonts.gstatic.com |
| **Font — Body** | Lora | Google Fonts, fallback: Georgia |
| **Palette** | Parchment `#F5EFE0`, Amber `#C4762A`, Navy `#1A1F2E`, Sage `#7A9977` | Hardcoded hex only — never `var()` in global CSS |
| **Slogan** | "Finding the Happiness of Pursuit" | Above hero on homepage, amber italic on parchment |
| **CTA color** | Amber `#C4762A` background, white text | All buttons, nav CTA |
| **Design philosophy** | Purposeful minimalism — every element earns its place or is removed | See `E:\CCWS\BHR\autoresearch\design-philosophy.md` |

## Page Identification

| Feature | Standard |
|---------|----------|
| **Static pages** | Amber tag `BHR-XX` at top-right, injected as HTML div in page content |
| **Category pages** | Amber tag `BHR-CAT-XX` via archive template (CSS on #bhr-page-id div) |
| **Search page** | `BHR-SRCH` |
| **404 page** | `BHR-404` |
| **Map file** | `E:\CCWS\PROJECTS\BHRC\site-build\page-id-map.md` |

## Article Presentation

| Feature | Standard |
|---------|----------|
| **Excerpt** | Opening hook of the article, no metadata, no title repeat. Invitation to click. |
| **Reading time** | Appended to excerpt: `· X,XXX words · X min read` (200 wpm) |
| **Pillar tag** | Small sage-green uppercase label at article top (not in excerpt) |
| **Author** | "Farook Maricar" — never an email address |
| **Duplicate titles** | Never. Content must not include its own H1 — the template renders it. |

## Navigation

| Feature | Standard |
|---------|----------|
| **Main nav label** | "Blog Posts" (not "Articles") |
| **Dropdown items** | Emotional Wellness, Joyful Living, AgeTech → link to category pages |
| **Start Here CTA** | Amber button, links to /free-guide |
| **Category pages** | 12 posts per page, excerpts (not full content), pagination |

## Search

| Feature | Standard |
|---------|----------|
| **Homepage** | Compact search bar between pillars and Latest Writing sections |
| **Category pages** | Search bar below category title/description |
| **Empty categories** | Friendly "Nothing here yet" + search bar + browse links |
| **No-results** | Never show the default WordPress "Sorry, nothing was found" message |

## Cross-Platform Links

| Feature | Standard |
|---------|----------|
| **Substack** | Link in About Farook: `behappyretired.substack.com` |
| **LinkedIn** | Pending |
| **Kit.com** | Newsletter signup on Free Guide page |

## CSS Rules

| Rule | Why |
|------|-----|
| **Never use CSS variables in global-styles** | WordPress strips `:root` definitions — var() resolves to nothing |
| **CRITICAL: Always push settings + styles together** | POST to global-styles/25 replaces the ENTIRE resource. Pushing only `styles.css` wipes `settings.typography.fontFamilies`. Pushing only `settings` wipes all CSS. Always include both in one call. |
| **Always push complete CSS** | POST to global-styles/25 replaces everything — never push a snippet alone |
| **Inline style blocks are backup** | Add critical CSS to homepage's wp:html block for reliability |
| **@import never works in global-styles** | WordPress prepends its CSS — @import must be first in a stylesheet |
| **Use @font-face with direct gstatic URLs** | Google Fonts loading that works on all devices |
| **`body::before` is unreliable** | For page IDs, use actual HTML divs in content |
| **Font settings must include fontFamilies.custom** | Without this, theme reverts to default Manrope sans-serif |

## Content Workflow

| Step | Standard |
|------|----------|
| **Drafts** | Written in Farook voice, humanized, no AI metadata traces |
| **Review** | Placed in STAGING as PENDING, preview URL shared |
| **Approval** | Farook says "Approve" → file renamed to APPROVED, published |
| **Audit** | Every change logged in AUDIT-LOG.md with scope, method, verification |

## Future (Agreed but not yet implemented)

| Feature | Direction |
|---------|-----------|
| **AgeTech content** | Start with "AI for Seniors" post. Sweet spot: tech that earns its place. Research Amazon affiliate products with strong reviews. |
| **Affiliate sales** | AgeTech → Amazon products with proven sales/reviews as starter |
| **Substack traffic redirect** | Substack posts will point readers to BHRC for full articles |
