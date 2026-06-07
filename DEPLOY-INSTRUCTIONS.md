# BHRC WordPress Site — Deployment Instructions
**Built by Dev — 2026-05-24**
**Target: BeHappyRetired.com (Bluehost)**

---

## What's in this folder

| File | Purpose |
|------|---------|
| `deploy-bhrc.php` | One-shot deployer — creates all pages, posts, categories, nav, CSS, widgets |
| `functions-additions.php` | Code to add to your theme's functions.php (GA4, Kit.com shortcode, post CTA) |
| `wp-cli-deploy.sh` | Optional SSH script for plugin installs and basic setup |
| `DEPLOY-INSTRUCTIONS.md` | This file |

---

## Blocker: WordPress Password

The credential file at `E:\Keys-SECURE\bhrc-wp-credentials.md` records username `claudedevbhr` but the password field says it was "provided by Farook 2026-05-03" — the actual password was not written down.

**Before deploying, Farook must confirm the WordPress admin password**, or we use Bluehost cPanel to reset it:
- Bluehost cPanel → WordPress → Manage → Reset Password
- Or: cPanel → MySQL Databases → phpMyAdmin → wp_users → update user_pass with MD5 hash

---

## Deployment Steps (30–45 minutes total)

### Step 1: Confirm WordPress is installed
- Log in to Bluehost cPanel
- Confirm WordPress is installed on behappyretired.com (Marketplace → My Applications)
- If not installed: Install WordPress via Bluehost's WordPress installer (1-click)

### Step 2: Upload deploy-bhrc.php
1. Bluehost cPanel → File Manager → public_html
2. Upload `deploy-bhrc.php` to `/public_html/` (the WordPress root)
3. Confirm the file is visible at the server root

### Step 3: Run the deployer
Open in browser: `https://behappyretired.com/deploy-bhrc.php?key=BHRC2026`

This creates:
- 3 content categories (Emotional Wellness, Joyful Living, AgeTech)
- 2 blog posts (Ikigai, The Warm Tofu Saturday)
- 10 pages: Home, Blog, Free Guide, The Art of Becoming, Resources, About, About Farook, Contact, Privacy Policy, Thank You
- Primary navigation menu with all 7 nav items
- BHRC custom CSS (Parchment + Amber + Cormorant Garamond + Lora)
- Newsletter sidebar widgets (placeholder)
- Site title/tagline settings

### Step 4: Add functions.php additions
1. WP Admin → Appearance → Theme File Editor → functions.php
   OR: cPanel File Manager → wp-content/themes/[active-theme]/functions.php
2. Paste the full contents of `functions-additions.php` at the end of the file
3. Save

This activates:
- GA4 tracking code (placeholder — needs real Measurement ID)
- [bhrc_kit_form] shortcode (placeholder — needs Kit.com embed)
- Post-content free guide CTA on every blog post
- Google Fonts enqueue (Cormorant Garamond + Lora)

### Step 5: Install required plugins
Via WP Admin → Plugins → Add New, install and activate:
- **Contact Form 7** — for the Contact page form
- **Google Site Kit** — for GA4 setup (recommended over manual code)
- **WP Super Cache** — performance (optional but recommended on Bluehost)

### Step 6: WordPress settings
WP Admin → Settings → Reading:
- Your homepage displays: A static page
- Homepage: Home
- Posts page: Blog

WP Admin → Settings → Permalinks:
- Select "Post name" (/%postname%/)
- Click Save Changes (flushes rewrite rules)

### Step 7: Set up GA4 (MANDATORY — do not skip)
**Option A (recommended): Google Site Kit plugin**
1. WP Admin → Site Kit → Start Setup
2. Connect your Google account
3. Site Kit auto-creates GA4 property and installs tracking
4. Verify data is flowing: Site Kit → Analytics → Real-time report

**Option B: Manual**
1. Go to analytics.google.com
2. Admin → Create Property → Property name: "Be Happy Retired" → Web
3. Add data stream: behappyretired.com
4. Copy Measurement ID (format: G-XXXXXXXXXX)
5. In functions-additions.php: replace both instances of `G-XXXXXXXXXX` with your real ID

### Step 8: Wire Kit.com
1. Log in to Kit.com
2. Forms → Create Form → Inline → name it "BHRC Newsletter"
3. Forms → [your form] → Embed → copy the JavaScript script tag
4. In functions-additions.php: find the `[bhrc_kit_form]` shortcode function
5. Replace the placeholder `return '<div class="bhrc-kit-placeholder"...` with:
   `return '<script src="https://f.convertkit.com/YOUR_FORM_ID/YOUR_FORM_ID.js"></script>';`
6. Save functions.php
7. Test on /free-guide — form should appear

### Step 9: Update placeholder URLs
- `/the-art-of-becoming` → replace Gumroad URL with real product link
- Resources page → replace Trip.com/Booking.com/Amazon links with real affiliate URLs
- About Farook page → optionally add photo (WP Admin → Media → upload)

### Step 10: Navigation menu
WP Admin → Appearance → Menus:
- Select "Primary Navigation"
- Assign to your theme's "Primary" menu location
- Save

### Step 11: Pre-launch checks
- [ ] All 10 pages exist (Home, Blog, Free Guide, The Art of Becoming, Resources, About, About Farook, Contact, Privacy Policy, Thank You)
- [ ] Both blog posts published under correct categories
- [ ] Navigation shows: Home | Blog | Free Guide | The Art of Becoming | Resources | About | Contact
- [ ] Mobile nav works (check on phone)
- [ ] GA4 data is flowing (Site Kit or GA4 real-time report shows at least 1 active user — yourself)
- [ ] Kit.com form visible on homepage, sidebar, and /free-guide
- [ ] About page uses verbatim approved copy
- [ ] About Farook page uses verbatim approved copy
- [ ] Gumroad button present on /the-art-of-becoming (placeholder URL acceptable for Phase 1)
- [ ] Affiliate links present on /resources (placeholder URLs acceptable for Phase 1)
- [ ] Post-content CTA appears on both blog posts linking to /free-guide
- [ ] Privacy policy live at /privacy-policy

### Step 12: Delete deploy-bhrc.php
**Delete `deploy-bhrc.php` from your server immediately after confirming everything works.**
cPanel File Manager → public_html → deploy-bhrc.php → Delete

---

## Pages Built — Slugs and Purpose

| # | Page | Slug | Status |
|---|------|------|--------|
| 1 | Homepage | / (home) | Draft — set as static front page |
| 2 | Blog index | /blog | Draft — set as posts page |
| 3 | Free Guide | /free-guide | Draft |
| 4 | The Art of Becoming | /the-art-of-becoming | Draft |
| 5 | Resources | /resources | Draft |
| 6 | About Be Happy Retired | /about | Draft — verbatim approved copy |
| 7 | About Farook | /about-farook | Draft — verbatim approved copy |
| 8 | Contact | /contact | Draft |
| 9 | Privacy Policy | /privacy-policy | Draft |
| 10 | Thank You (confirmation) | /thank-you | Draft |

## Blog Posts — Pillars

| Post | Slug | Category |
|------|------|----------|
| Ikigai — The Japanese Word I Wish I'd Known Before I Retired | /ikigai-purpose-joyful-living | Emotional Wellness |
| The Warm Tofu Saturday | /the-warm-tofu-saturday | Joyful Living |

---

## GA4 Conversion Events Configured

Once the real Measurement ID is in functions.php, these events fire automatically:

| Event | Trigger |
|-------|---------|
| `email_signup` | Kit.com form submission |
| `gumroad_click` | Click on any Gumroad buy button |
| `affiliate_click` | Click on Trip.com, Booking.com, Amazon affiliate links |

All events tagged with category and label for segmentation in GA4.

---

## Outstanding (blockers before Phase 1 sign-off)

1. **WordPress password** — confirm with Farook or reset via Bluehost
2. **GA4 Measurement ID** — create property and replace G-XXXXXXXXXX
3. **Kit.com embed** — paste real embed code into functions.php
4. **Gumroad product URL** — Farook to provide/create on Gumroad
5. **Affiliate accounts** — Trip.com, Booking.com, Amazon Associates (all placeholder now)

---

*Dev — BHRC Phase 1 Site Build — 2026-05-24*
