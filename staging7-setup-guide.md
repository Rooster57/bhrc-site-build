# staging7.behappyretired.com — Setup Guide
*Larry | 2026-06-04*

---

## What This Is

A staging subdomain on Hostinger to demo the expanded BHR vision: seniors + caregivers, three content pillars, AgeTech section, lead magnet funnel. Uses the existing WordPress framework. Does NOT touch the live site.

---

## Step 1 — Farook Creates the Subdomain in Hostinger (5 minutes)

This is the one step only Farook can do. Everything else Larry handles.

1. Log in to hPanel (hostinger.com → Log In)
2. Go to **Hosting → Manage → Subdomains**
3. Click **Create Subdomain**
4. Enter: `staging7`
5. Domain: `behappyretired.com`
6. Directory: `/public_html/staging7` (Hostinger will auto-suggest this — accept it)
7. Click **Create**

That's it. Farook is done.

**Send Larry:** "staging7 created" — Larry handles the rest.

---

## Step 2 — Larry Deploys (after subdomain confirmed)

### Option A: Install fresh WordPress on staging7 directory
- Hostinger hPanel → Auto Installer → WordPress
- Install to /staging7 subdirectory
- Use same credentials as live site for convenience

### Option B: Copy existing WordPress install
- Use Hostinger's Clone Site feature (hPanel → Clone)
- Clone behappyretired.com → staging7.behappyretired.com
- This copies all plugins, theme, and settings instantly
- Then deploy staging7 content via WP REST API

**Recommendation: Option B (Clone).** Takes 10 minutes, preserves all existing framework.

---

## Step 3 — Deploy Staging7 Content via WP REST API

Larry holds the Application Password at `E:\Keys-SECURE\bhrc-wp-api-credentials.md`.

After clone, Larry will:
1. Update site URL to staging7.behappyretired.com
2. Create the new homepage with the staging7 design blocks
3. Create the Caregiver Corner page (new — not on live site)
4. Create the AgeTech category landing page
5. Add the lead magnet section to homepage
6. Apply any CSS enhancements (larger body text, enhanced contrast)

All of this happens via the REST API — no SSH needed.

---

## What Staging7 Demonstrates (vs Live Site)

| Element | Live Site | Staging7 |
|---------|-----------|----------|
| Hero copy | Existing | Senior-specific voice, larger type |
| Body font size | 16px | 18px minimum |
| Caregiver section | Not present | Full section, 4 resource cards |
| AgeTech | Category only | Tool spotlight cards with verdicts |
| Lead magnet | Not wired | 5 Keys form integrated in homepage |
| Navigation | Current | Adds "For Caregivers" with NEW badge |
| Substack strip | Footer | Dedicated strip with subscribe CTA |

---

## HTML Prototype (Available Now)

Before the subdomain is live, Farook can preview the full design at:
`E:\CCWS\STAGING\other\2026-06-04-staging7-prototype.html`

Open in any browser. This is the complete design in static HTML — every section, full brand styling, mobile responsive.

---

## Timeline

| Step | Who | Time |
|------|-----|------|
| Create subdomain in hPanel | Farook | 5 min |
| Clone site + set staging7 URL | Larry | 15 min |
| Deploy content via REST API | Larry | 45 min |
| QA and send preview URL | Larry | 15 min |

**Total from "staging7 created": ~75 minutes.**

---

*Larry*
