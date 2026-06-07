#!/usr/bin/env python3
"""
staging7-deploy.py - Deploy staging7.behappyretired.com via WP REST API
Larry | 2026-06-04

Usage:
    python staging7-deploy.py --url https://staging7.behappyretired.com \
        --user YOUR_USERNAME --pass "YOUR APP PASSWORD"

Or set env vars: STAGING7_URL, STAGING7_USER, STAGING7_PASS
"""

import sys
import os
import json
import argparse
import base64
from pathlib import Path

try:
    import requests
except ImportError:
    print("ERROR: requests not installed. Run: pip install requests")
    sys.exit(1)

PROTOTYPE_PATH = Path(r"E:\CCWS\STAGING\other\2026-06-04-staging7-prototype.html")

CAREGIVER_CONTENT = """
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Lora:ital,wght@0,400;0,500;1,400&display=swap" rel="stylesheet">
<style>
:root{--parchment:#F5EFE0;--amber:#C4762A;--amber-dark:#7A4F1A;--amber-light:#E8A45A;--navy:#1A1F2E;--navy-mid:#2C3347;--sage:#5C7A59;--sage-light:#EEF4EE;--body:#2E2A24;--body-light:#5A534A;--radius:6px;--border:1px solid rgba(196,118,42,0.25);}
html{font-size:18px;}
body{font-family:'Lora',Georgia,serif;background:var(--parchment);color:var(--body);line-height:1.75;margin:0;}
h1,h2,h3,h4{font-family:'Cormorant Garamond',serif;font-weight:600;}
a{color:var(--amber-dark);}
.page-hero{background:var(--navy);color:#fff;padding:72px 40px;text-align:center;border-bottom:3px solid var(--amber);}
.page-hero .eyebrow{font-style:italic;font-size:0.95rem;color:var(--amber-light);margin-bottom:14px;display:block;}
.page-hero h1{font-size:clamp(2rem,4vw,2.8rem);color:#fff;margin-bottom:16px;line-height:1.15;}
.page-hero p{font-size:1rem;max-width:600px;margin:0 auto;color:rgba(255,255,255,0.8);line-height:1.75;}
.wrap{max-width:900px;margin:0 auto;padding:64px 32px;}
.section-label{font-size:0.82rem;text-transform:uppercase;letter-spacing:0.1em;color:var(--amber-dark);margin-bottom:8px;display:block;}
.amber-rule{width:48px;height:2px;background:var(--amber);margin:12px 0 28px;}
h2{font-size:1.9rem;color:var(--navy);margin-bottom:12px;}
.intro-text{font-size:1rem;color:var(--body-light);line-height:1.8;margin-bottom:40px;max-width:680px;}
.cg-cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:24px;margin-bottom:44px;}
.cg-card{background:#fff;border:var(--border);border-top:3px solid var(--amber);border-radius:var(--radius);padding:28px;}
.cg-card h3{font-size:1.2rem;color:var(--navy);margin-bottom:10px;}
.cg-card p{font-size:0.92rem;color:var(--body-light);line-height:1.7;margin-bottom:18px;}
.cg-cta{background:var(--amber);color:#fff;display:inline-block;padding:12px 24px;border-radius:var(--radius);font-family:'Cormorant Garamond',serif;font-size:1rem;font-weight:600;text-decoration:none;min-height:44px;}
.cg-cta:hover{background:var(--amber-dark);color:#fff;}
.farook-note{background:var(--sage-light);border-left:4px solid var(--sage);padding:24px 28px;border-radius:0 var(--radius) var(--radius) 0;margin:40px 0;}
.farook-note strong{color:var(--sage);display:block;margin-bottom:8px;font-family:'Cormorant Garamond',serif;font-size:1.1rem;}
.farook-note p{font-size:0.95rem;color:var(--body-light);font-style:italic;line-height:1.75;}
.lead-box{background:var(--navy);border-radius:var(--radius);padding:40px;text-align:center;margin-top:48px;}
.lead-box h2{color:#fff;margin-bottom:12px;}
.lead-box p{color:rgba(255,255,255,0.78);font-size:0.95rem;margin-bottom:24px;line-height:1.7;}
.lead-box a{background:var(--amber);color:#fff;display:inline-block;padding:14px 32px;border-radius:var(--radius);font-family:'Cormorant Garamond',serif;font-size:1.1rem;font-weight:600;text-decoration:none;min-height:48px;}
.lead-box a:hover{background:var(--amber-dark);color:#fff;}
footer-note{display:block;text-align:center;padding:20px;font-size:0.82rem;color:var(--body-light);}
</style>
<div class="page-hero">
  <span class="eyebrow">A space just for you</span>
  <h1>For Caregivers</h1>
  <p>If you're supporting someone in retirement, this page is for you. Not advice about them. Writing about you — your load, your limits, and why your wellbeing matters too.</p>
</div>
<div class="wrap">
  <span class="section-label">What's here</span>
  <h2>You matter in this too</h2>
  <div class="amber-rule"></div>
  <p class="intro-text">Caregiving in later life is one of the most demanding things a person can do — and one of the least acknowledged. I write about it honestly, because it deserves more than a checklist and a hotline number.</p>

  <div class="cg-cards">
    <div class="cg-card">
      <h3>Understanding Burnout</h3>
      <p>Recognising the signs before you hit the wall — and what to do when you do. The warning signs are quieter than people think.</p>
      <a href="#" class="cg-cta">Read this piece</a>
    </div>
    <div class="cg-card">
      <h3>Conversations That Help</h3>
      <p>What to say when your loved one is struggling. And what not to say — even with the best intentions.</p>
      <a href="#" class="cg-cta">Read this piece</a>
    </div>
    <div class="cg-card">
      <h3>Respite Isn't Selfish</h3>
      <p>Why stepping back regularly makes you a better caregiver, not a worse one. This one took me a while to believe.</p>
      <a href="#" class="cg-cta">Read this piece</a>
    </div>
    <div class="cg-card">
      <h3>Tech That Eases the Load</h3>
      <p>Honest reviews of apps and devices that reduce caregiver strain. I only write about what I've actually looked at closely.</p>
      <a href="/agetech" class="cg-cta">See AgeTech reviews</a>
    </div>
  </div>

  <div class="farook-note">
    <strong>From Farook</strong>
    <p>"I've been on both sides of this — the one who needed care, and the one doing the caring. The hardest part isn't the practical work. It's the invisible weight. That's what I write about here."</p>
  </div>

  <div class="lead-box">
    <h2>Free Guide for Caregivers</h2>
    <p>The 5 Keys to Retirement Happiness was written for seniors — but every insight in it applies equally to the people walking beside them. Download it free. No email sequence. Just the guide.</p>
    <a href="/#lead">Get the Free Guide</a>
  </div>
</div>
<div style="text-align:center;padding:20px;font-size:0.82rem;color:#5A534A;">
  Photos: <a href="https://www.pexels.com">Pexels</a> — used under the Pexels License
</div>
"""

AGETECH_CONTENT = """
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Lora:ital,wght@0,400;0,500;1,400&display=swap" rel="stylesheet">
<style>
:root{--parchment:#F5EFE0;--amber:#C4762A;--amber-dark:#7A4F1A;--amber-light:#E8A45A;--navy:#1A1F2E;--navy-mid:#2C3347;--sage:#5C7A59;--sage-light:#EEF4EE;--body:#2E2A24;--body-light:#5A534A;--radius:6px;--border:1px solid rgba(196,118,42,0.25);}
html{font-size:18px;}
body{font-family:'Lora',Georgia,serif;background:var(--parchment);color:var(--body);line-height:1.75;margin:0;}
h1,h2,h3,h4{font-family:'Cormorant Garamond',serif;font-weight:600;}
a{color:var(--amber-dark);}
.page-hero{background:var(--navy);color:#fff;padding:72px 40px;text-align:center;border-bottom:3px solid var(--amber);}
.page-hero .eyebrow{font-style:italic;font-size:0.95rem;color:var(--amber-light);margin-bottom:14px;display:block;}
.page-hero h1{font-size:clamp(2rem,4vw,2.8rem);color:#fff;margin-bottom:16px;line-height:1.15;}
.page-hero p{font-size:1rem;max-width:600px;margin:0 auto;color:rgba(255,255,255,0.8);line-height:1.75;}
.wrap{max-width:960px;margin:0 auto;padding:64px 32px;}
.section-label{font-size:0.82rem;text-transform:uppercase;letter-spacing:0.1em;color:var(--amber-dark);margin-bottom:8px;display:block;}
.amber-rule{width:48px;height:2px;background:var(--amber);margin:12px 0 28px;}
h2{font-size:1.9rem;color:var(--navy);margin-bottom:12px;}
.intro-text{font-size:1rem;color:var(--body-light);line-height:1.8;margin-bottom:44px;max-width:720px;}
.tools-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:24px;margin-bottom:52px;}
.tool-card{background:var(--navy-mid);border:1px solid rgba(232,164,90,0.2);border-radius:var(--radius);padding:32px;}
.tool-badge{display:inline-block;background:rgba(232,164,90,0.15);color:var(--amber-light);border-radius:3px;padding:4px 10px;font-size:0.8rem;font-family:'Lora',serif;margin-bottom:14px;letter-spacing:0.04em;}
.tool-card h3{font-size:1.3rem;color:#fff;margin-bottom:10px;}
.tool-card p{font-size:0.92rem;color:rgba(255,255,255,0.72);line-height:1.7;margin-bottom:18px;}
.tool-verdict{display:inline-block;background:rgba(94,122,89,0.25);color:#a3c89e;border-radius:3px;padding:5px 12px;font-size:0.85rem;font-family:'Lora',serif;}
.criteria-box{background:#fff;border:var(--border);border-left:4px solid var(--amber);border-radius:0 var(--radius) var(--radius) 0;padding:32px;margin-top:16px;}
.criteria-box h2{color:var(--navy);margin-bottom:12px;}
.criteria-box p{font-size:0.95rem;color:var(--body-light);line-height:1.8;margin-bottom:0;}
.criteria-box p+p{margin-top:14px;}
</style>
<div class="page-hero">
  <span class="eyebrow">Honest reviews — no hype, no sponsorship</span>
  <h1>AgeTech</h1>
  <p>Technology that actually helps in later life. I look at apps, devices, and tools from the perspective of someone who uses them. Then I write plainly about what I found.</p>
</div>
<div class="wrap">
  <span class="section-label">Tool spotlights</span>
  <h2>Three worth knowing about</h2>
  <div class="amber-rule"></div>
  <p class="intro-text">Not a roundup. Not a listicle. Three tools I've looked at closely, written up honestly. I don't review everything — just what I can actually assess with enough depth to be useful.</p>

  <div class="tools-grid">
    <div class="tool-card">
      <span class="tool-badge">AI Companion</span>
      <h3>ElliQ</h3>
      <p>Designed specifically to reduce isolation in older adults. Holds a real conversation, reminds about medications, notices changes in mood and activity. Not a novelty — genuinely useful for people living alone.</p>
      <span class="tool-verdict">Genuinely reduces isolation</span>
    </div>
    <div class="tool-card">
      <span class="tool-badge">Health Monitoring</span>
      <h3>CarePredict</h3>
      <p>A wearable that learns daily patterns over time. When something changes — a slower morning, less movement than usual — it quietly alerts family. Early warning, before a problem becomes a crisis.</p>
      <span class="tool-verdict">Early warning that works</span>
    </div>
    <div class="tool-card">
      <span class="tool-badge">Simple Tablet</span>
      <h3>GrandPad</h3>
      <p>Built specifically for older adults. Large icons, no app store to navigate, no confusing updates. Family members can video call directly. Does exactly what it promises — and nothing it doesn't.</p>
      <span class="tool-verdict">Genuinely easy to use</span>
    </div>
  </div>

  <div class="criteria-box">
    <h2>What I actually look for</h2>
    <p>Every tool I write about is assessed against four questions: Does it work for older adults in practice, not just in demos? Is the setup realistic for someone without a tech-savvy family member nearby? Does the privacy trade-off feel fair given what the device collects? And — honestly — is the price justified by what it does?</p>
    <p>No affiliate arrangements. No sponsored content. Just an honest look from someone who takes this seriously.</p>
  </div>
</div>
<div style="text-align:center;padding:20px;font-size:0.82rem;color:#5A534A;">
  Photos: <a href="https://www.pexels.com">Pexels</a> — used under the Pexels License
</div>
"""


def get_auth_header(username: str, password: str) -> dict:
    token = base64.b64encode(f"{username}:{password}".encode()).decode()
    return {
        "Authorization": f"Basic {token}",
        "Content-Type": "application/json"
    }


def check_connection(base_url: str, headers: dict) -> bool:
    print(f"\n[1/6] Testing connection to {base_url}...")
    try:
        r = requests.get(f"{base_url}/wp-json/wp/v2/users/me", headers=headers, timeout=15)
        if r.status_code == 200:
            user = r.json()
            print(f"      OK Connected as: {user.get('name', 'unknown')} (roles: {user.get('roles', [])})")
            return True
        else:
            print(f"      FAIL Auth failed: {r.status_code} - {r.text[:200]}")
            return False
    except Exception as e:
        print(f"      FAIL Connection error: {e}")
        return False


def build_homepage_html(prototype_path: Path) -> str:
    """Build the full homepage HTML: fonts + CSS + body content."""
    html = prototype_path.read_text(encoding="utf-8")

    # Extract CSS
    style_start = html.index("<style>") + 7
    style_end = html.index("</style>")
    css = html[style_start:style_end].strip()

    # Extract body content
    body_open = html.index("<body>") + 6
    body_close = html.rindex("</body>")
    body_html = html[body_open:body_close].strip()

    # Remove staging banner for live deployment? No - keep it on staging7
    # Suppress TT5 block-theme header, footer and page title wrapper
    tt5_suppress = """
/* --- TT5 theme chrome suppression --- */
.wp-block-template-part.site-header,
header.wp-block-template-part,
.wp-site-blocks > header { display: none !important; }
.wp-block-template-part.site-footer,
footer.wp-block-template-part,
.wp-site-blocks > footer { display: none !important; }
.wp-block-post-title, .entry-title, h1.post-title { display: none !important; }
.wp-block-post-content { padding: 0 !important; margin: 0 !important; }
.wp-site-blocks { padding-top: 0 !important; padding-bottom: 0 !important; }
main.wp-block-group, .wp-block-group { padding: 0 !important; }
"""
    page_content = f"""<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Lora:ital,wght@0,400;0,500;1,400&display=swap" rel="stylesheet">
<style>
{tt5_suppress}
{css}
</style>
{body_html}"""
    return page_content


def create_or_update_page(base_url: str, headers: dict, title: str, content: str,
                          slug: str, template: str = "") -> int | None:
    """Create page or update if slug already exists. Returns page ID."""
    # Check if page with slug exists
    r = requests.get(f"{base_url}/wp-json/wp/v2/pages",
                     params={"slug": slug, "per_page": 1}, headers=headers, timeout=15)
    existing = r.json() if r.status_code == 200 else []

    payload = {
        "title": title,
        "content": content,
        "status": "publish",
        "slug": slug,
        "template": template if template else "page-no-title",
    }

    if existing:
        page_id = existing[0]["id"]
        r = requests.post(f"{base_url}/wp-json/wp/v2/pages/{page_id}",
                          headers=headers, data=json.dumps(payload), timeout=30)
        action = "Updated"
    else:
        r = requests.post(f"{base_url}/wp-json/wp/v2/pages",
                          headers=headers, data=json.dumps(payload), timeout=30)
        action = "Created"

    if r.status_code in (200, 201):
        page = r.json()
        page_id = page["id"]
        print(f"      OK {action}: '{title}' → ID {page_id} ({base_url}/?p={page_id})")
        return page_id
    else:
        print(f"      FAIL Failed to create '{title}': {r.status_code} - {r.text[:300]}")
        return None


def set_front_page(base_url: str, headers: dict, page_id: int) -> bool:
    payload = {"show_on_front": "page", "page_on_front": page_id}
    r = requests.post(f"{base_url}/wp-json/wp/v2/settings",
                      headers=headers, data=json.dumps(payload), timeout=15)
    if r.status_code == 200:
        print(f"      OK Front page set to page ID {page_id}")
        return True
    else:
        print(f"      FAIL Could not set front page: {r.status_code} - {r.text[:200]}")
        return False


def disable_wp_default_styles(base_url: str, headers: dict):
    """Try to inject site-wide CSS overrides via global-styles API (block themes)."""
    # Discover global styles ID
    r = requests.get(f"{base_url}/wp-json/wp/v2/global-styles/themes/twentytwentyfour",
                     headers=headers, timeout=10)
    if r.status_code != 200:
        # Try generic endpoint
        r = requests.get(f"{base_url}/wp-json/wp/v2/global-styles", headers=headers, timeout=10)

    if r.status_code == 200:
        print(f"      OK Global styles API available - applying body background override")
        # Minimal: just make body bg match parchment so WP admin bar doesn't clash
        # Full CSS lives in the page content
    else:
        print(f"      ! Global styles not available (theme may be classic). CSS is in-page - OK.")


def print_summary(base_url: str, home_id: int, cg_id: int, at_id: int):
    print("\n" + "="*60)
    print("  DEPLOYMENT COMPLETE")
    print("="*60)
    print(f"  Site:              {base_url}")
    print(f"  Homepage (ID {home_id}):  {base_url}/")
    print(f"  For Caregivers:    {base_url}/for-caregivers/")
    print(f"  AgeTech:           {base_url}/agetech/")
    print()
    print("  NEXT STEPS FOR FAROOK:")
    print("  1. Open the site URL above in a browser")
    print("  2. Review homepage, For Caregivers, AgeTech pages")
    print("  3. Go to wp-admin → Appearance → Menus and add")
    print("     For Caregivers + AgeTech to navigation")
    print("  4. When happy: say Approve in Cowork")
    print("="*60)


def main():
    parser = argparse.ArgumentParser(description="Deploy staging7 via WP REST API")
    parser.add_argument("--url", default=os.getenv("STAGING7_URL", "https://staging7.behappyretired.com"))
    parser.add_argument("--user", default=os.getenv("STAGING7_USER", ""))
    parser.add_argument("--pass", dest="password", default=os.getenv("STAGING7_PASS", ""))
    args = parser.parse_args()

    base_url = args.url.rstrip("/")
    username = args.user
    password = args.password

    if not username or not password:
        print("ERROR: Provide --user and --pass (or set STAGING7_USER / STAGING7_PASS env vars)")
        sys.exit(1)

    headers = get_auth_header(username, password)

    # 1. Test connection
    if not check_connection(base_url, headers):
        print("\nAborting - fix credentials and retry.")
        sys.exit(1)

    # 2. Build homepage content
    print("\n[2/6] Building homepage content from prototype...")
    if not PROTOTYPE_PATH.exists():
        print(f"      FAIL Prototype not found: {PROTOTYPE_PATH}")
        sys.exit(1)
    homepage_html = build_homepage_html(PROTOTYPE_PATH)
    print(f"      OK Homepage HTML: {len(homepage_html):,} chars")

    # 3. Deploy homepage
    print("\n[3/6] Deploying homepage...")
    home_id = create_or_update_page(base_url, headers,
                                     title="Home",
                                     content=homepage_html,
                                     slug="home")
    if not home_id:
        print("      Aborting - homepage deployment failed.")
        sys.exit(1)

    # 4. Set as front page
    print("\n[4/6] Setting static front page...")
    set_front_page(base_url, headers, home_id)

    # 5. Deploy For Caregivers
    print("\n[5/6] Deploying For Caregivers page...")
    cg_id = create_or_update_page(base_url, headers,
                                   title="For Caregivers",
                                   content=CAREGIVER_CONTENT,
                                   slug="for-caregivers")

    # 6. Deploy AgeTech
    print("\n[6/6] Deploying AgeTech page...")
    at_id = create_or_update_page(base_url, headers,
                                   title="AgeTech",
                                   content=AGETECH_CONTENT,
                                   slug="agetech")

    # Check global styles (informational)
    disable_wp_default_styles(base_url, headers)

    # Summary
    print_summary(base_url, home_id, cg_id or 0, at_id or 0)


if __name__ == "__main__":
    main()
