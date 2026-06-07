"""
BHRC Comprehensive Fix
1. Background: push styles.color.background via structured API
2. CSS: override --wp--preset--color--base CSS variable
3. Homepage: remove bhrc-nav orphan block, hide front-page post title
4. Posts: publish any drafts so Latest Writings works
5. Left padding fix for mobile seniors
"""
import urllib.request, urllib.error, base64, json, re

BASE = 'https://behappyretired.com'
user = 'farook.maricar@hotmail.com'
pwd  = 'sz8FAtOvv1wL3H82JxxLgQEo'
token = base64.b64encode(f'{user}:{pwd}'.encode()).decode()
headers = {'Authorization': f'Basic {token}', 'Content-Type': 'application/json'}

def req(method, url, data=None):
    body = json.dumps(data).encode() if data else None
    r = urllib.request.Request(url, data=body, headers=headers, method=method)
    try:
        resp = urllib.request.urlopen(r, timeout=25)
        return json.loads(resp.read())
    except urllib.error.HTTPError as e:
        return {'_error': e.code, '_body': e.read().decode()[:400]}
    except Exception as e:
        return {'_error': str(e)}

log = []

# ── 1. READ CURRENT CSS FROM ID 6 ───────────────────────────
gs = req('GET', f'{BASE}/wp-json/wp/v2/global-styles/6')
current_css = gs.get('styles', {}).get('css', '')

# Build updated CSS — prepend critical variable overrides, add mobile padding
css_prefix = """
/* OVERRIDE TT25 color vars — must come first */
:root,
body,
.wp-site-blocks {
  --wp--preset--color--base: #F5EFE0 !important;
  --wp--preset--color--contrast: #2C2416 !important;
  --wp--preset--color--accent: #C4762A !important;
  --wp--preset--color--accent-2: #1A1F2E !important;
  background-color: #F5EFE0 !important;
}

/* MOBILE: safe left/right padding for seniors (16px minimum touch buffer) */
.wp-site-blocks,
.wp-block-post-content,
.entry-content,
.wp-block-group__inner-container,
.wp-block-query,
.wp-block-latest-posts {
  padding-left: 20px !important;
  padding-right: 20px !important;
}
@media (min-width: 768px) {
  .wp-site-blocks,
  .wp-block-group__inner-container {
    padding-left: 40px !important;
    padding-right: 40px !important;
  }
}

/* HIDE post title on front page (it duplicates the site header title) */
.home .wp-block-post-title,
.front-page .wp-block-post-title,
body.home h1.wp-block-post-title {
  display: none !important;
}

/* SECTION CONTRAST — alternate backgrounds so sections are distinct */
.wp-block-group:not([class*="hero"]):not([class*="newsletter"]):not([class*="footer"]):nth-of-type(even) {
  background-color: #EDE5D0 !important;
}
.wp-block-group:not([class*="hero"]):not([class*="newsletter"]):not([class*="footer"]):nth-of-type(odd) {
  background-color: #F5EFE0 !important;
}

/* EXPLICIT section backgrounds for named sections */
.wp-block-group.newsletter-section,
.wp-block-group[style*="1A1F2E"],
section.newsletter-section {
  background-color: #1A1F2E !important;
}

/* POST CARD backgrounds — warm white not pure white */
.wp-block-post,
.wp-block-latest-posts__list-item {
  background-color: #FDFAF5 !important;
  border: 1px solid #DDD3BE !important;
}

/* SENIOR-FRIENDLY: larger tap targets, better contrast */
a { color: #A35F1F !important; }  /* darker amber = better contrast */
.wp-block-navigation a { color: #FDFAF5 !important; }
nav a { color: #FDFAF5 !important; }

/* Larger body text for seniors */
@media (max-width: 600px) {
  body, p, li { font-size: 18px !important; line-height: 1.9 !important; }
  h1 { font-size: clamp(1.9rem, 8vw, 2.8rem) !important; }
  h2 { font-size: clamp(1.4rem, 6vw, 2rem) !important; }
  .wp-block-navigation a { font-size: 16px !important; }
}

"""

# Append prefix to existing CSS (keep existing rules, add overrides at top)
# Remove any old @import line from the beginning first
clean_css = re.sub(r'^@import[^\n]*\n?', '', current_css, flags=re.MULTILINE).strip()
final_css = css_prefix + clean_css

# Also handle the Google Fonts import properly — move to end
fonts_import = "@import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;0,700;1,300;1,400;1,600&family=Lora:ital,wght@0,400;0,500;1,400;1,500&display=swap');\n\n"
final_css = fonts_import + final_css

# ── 2. PUSH CSS + STRUCTURED COLOR BACKGROUND ───────────────
payload = {
    'styles': {
        'css': final_css,
        'color': {
            'background': '#F5EFE0',
            'text': '#2C2416'
        }
    }
}
r = req('PUT', f'{BASE}/wp-json/wp/v2/global-styles/6', payload)
if '_error' not in r:
    stored_css = r.get('styles', {}).get('css', '')
    bg = r.get('styles', {}).get('color', {}).get('background', '?')
    log.append(f'OK  CSS pushed: {len(stored_css)} chars | background: {bg}')
else:
    log.append(f'ERR CSS push failed: {r}')

# ── 3. FIX HOMEPAGE: remove bhrc-nav orphan block ───────────
home = req('GET', f'{BASE}/wp-json/wp/v2/pages?slug=home&status=any')
if isinstance(home, list) and home:
    page = home[0]
    page_id = page['id']
    content = page.get('content', {}).get('raw', '') or page.get('content', {}).get('rendered', '')

    # Remove bhrc-nav block (it's a raw HTML block inserted by old deploy)
    content_clean = re.sub(
        r'<!-- wp:html -->.*?class="bhrc-nav".*?<!-- /wp:html -->',
        '',
        content,
        flags=re.DOTALL
    )
    # Also strip any bare bhrc-nav HTML if not wrapped in wp:html comment
    content_clean = re.sub(
        r'<nav class="bhrc-nav">.*?</nav>',
        '',
        content_clean,
        flags=re.DOTALL
    )

    if content_clean != content:
        upd = req('PUT', f'{BASE}/wp-json/wp/v2/pages/{page_id}', {'content': content_clean})
        if '_error' not in upd:
            log.append(f'OK  Homepage bhrc-nav removed (page ID {page_id})')
        else:
            log.append(f'ERR Homepage update failed: {upd}')
    else:
        log.append(f'INFO Homepage: bhrc-nav not found in raw content (may be in template part)')
else:
    log.append(f'WARN Could not fetch homepage: {home}')

# ── 4. PUBLISH DRAFT POSTS ───────────────────────────────────
drafts = req('GET', f'{BASE}/wp-json/wp/v2/posts?status=draft&per_page=20')
if isinstance(drafts, list):
    log.append(f'INFO Found {len(drafts)} draft posts')
    for post in drafts:
        pid = post['id']
        title = post.get('title', {}).get('rendered', '?')
        r2 = req('PUT', f'{BASE}/wp-json/wp/v2/posts/{pid}', {'status': 'publish'})
        if '_error' not in r2:
            log.append(f'OK  Published: "{title}" (ID {pid})')
        else:
            log.append(f'ERR Could not publish "{title}": {r2.get("_body","")[:100]}')
else:
    log.append(f'WARN Draft query failed: {drafts}')

# ── 5. CHECK ALL PAGES STATUS ────────────────────────────────
pages = req('GET', f'{BASE}/wp-json/wp/v2/pages?status=any&per_page=20')
if isinstance(pages, list):
    log.append(f'\nINFO Pages ({len(pages)} total):')
    for pg in pages:
        log.append(f'  [{pg.get("status")}] /{pg.get("slug")} — {pg.get("title",{}).get("rendered","?")}')

# ── REPORT ───────────────────────────────────────────────────
print('\n'.join(log))
