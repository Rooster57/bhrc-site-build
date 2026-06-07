"""
BHRC Site Diagnostics
Fetches live pages and reports on: CSS loading, duplicate titles, link targets, background color
"""
import urllib.request, urllib.error, re, json, base64

BASE = 'https://behappyretired.com'
user = 'farook.maricar@hotmail.com'
pwd  = 'sz8FAtOvv1wL3H82JxxLgQEo'
auth_token = base64.b64encode(f'{user}:{pwd}'.encode()).decode()

def fetch(url, auth=False):
    hdrs = {'User-Agent': 'Mozilla/5.0 (Linux; Android 14) AppleWebKit/537.36'}
    if auth:
        hdrs['Authorization'] = f'Basic {auth_token}'
    req = urllib.request.Request(url, headers=hdrs)
    try:
        resp = urllib.request.urlopen(req, timeout=20)
        return resp.read().decode('utf-8', errors='replace')
    except Exception as e:
        return f'ERROR: {e}'

# ── 1. CHECK CSS IS IN OUTPUT ─────────────────────────────────
print('=' * 60)
print('DIAGNOSIS: BeHappyRetired.com')
print('=' * 60)

html = fetch(BASE)
if html.startswith('ERROR'):
    print(f'Homepage fetch failed: {html}')
else:
    # Check for our CSS markers
    has_parchment = '#F5EFE0' in html or 'F5EFE0' in html
    has_cormorant = 'Cormorant' in html
    has_lora      = 'Lora' in html
    css_blocks    = re.findall(r'<style[^>]*>(.*?)</style>', html, re.DOTALL)
    our_css_found = any('F5EFE0' in b or '--parchment' in b for b in css_blocks)

    print(f'\n[CSS STATUS]')
    print(f'  Parchment color in page: {has_parchment}')
    print(f'  Cormorant font reference: {has_cormorant}')
    print(f'  Lora font reference: {has_lora}')
    print(f'  Our CSS block found in <style> tags: {our_css_found}')
    print(f'  Total <style> blocks: {len(css_blocks)}')
    if our_css_found:
        for i, b in enumerate(css_blocks):
            if 'F5EFE0' in b or '--parchment' in b:
                print(f'  -> Found in style block #{i}, length: {len(b)} chars')

    # ── 2. FIND BODY INLINE BACKGROUND ───────────────────────
    body_match = re.search(r'<body[^>]*>', html)
    if body_match:
        print(f'\n[BODY TAG]: {body_match.group()[:200]}')

    # ── 3. DUPLICATE TITLE ANALYSIS ──────────────────────────
    print(f'\n[TITLE/HEADER STRUCTURE]')
    # Find site title occurrences
    site_title_hits = [(m.start(), html[max(0,m.start()-50):m.end()+100])
                       for m in re.finditer(r'Be Happy Retired', html)]
    print(f'  "Be Happy Retired" appears {len(site_title_hits)} times in HTML')
    for i, (pos, ctx) in enumerate(site_title_hits[:6]):
        # Clean whitespace
        ctx_clean = re.sub(r'\s+', ' ', ctx).strip()
        print(f'  [{i+1}] pos={pos}: ...{ctx_clean}...')

    # ── 4. LATEST WRITINGS LINKS ─────────────────────────────
    print(f'\n[LATEST WRITINGS LINKS]')
    # Find links near "Latest Writings" section
    lw_match = re.search(r'Latest Writings?.{0,3000}', html, re.DOTALL | re.IGNORECASE)
    if lw_match:
        section = lw_match.group()[:3000]
        links = re.findall(r'href=["\']([^"\']+)["\']', section)
        print(f'  Links found in Latest Writings section:')
        for lnk in links[:10]:
            print(f'    {lnk}')
    else:
        # Try finding post links generally
        post_links = re.findall(r'href=["\'](' + BASE.replace('.', r'\.') + r'/[a-z0-9-]+/)["\']', html)
        print(f'  Post-like links on page: {post_links[:10]}')

    # ── 5. BODY BACKGROUND FROM INLINE STYLE ─────────────────
    print(f'\n[BACKGROUND COLOR CHECK]')
    # Check for inline background on body/main wrapper
    inline_bg = re.findall(r'style="[^"]*background[^"]*"', html[:5000])
    for bg in inline_bg[:5]:
        print(f'  Inline bg: {bg}')

    # Check theme.json color in page source
    if 'var(--wp--preset--color--base)' in html or 'var(--wp--global--color' in html:
        print('  WP theme color vars found — FSE theme is setting background via CSS vars')
        # Find the --base var definition
        base_var = re.search(r'--wp--preset--color--base\s*:\s*([^;]+)', html)
        if base_var:
            print(f'  --wp--preset--color--base = {base_var.group(1).strip()}')

    # Save raw HTML for deeper inspection
    with open(r'E:\CCWS\PROJECTS\BHRC\site-build\homepage-raw.html', 'w', encoding='utf-8') as f:
        f.write(html)
    print(f'\n[Raw HTML saved to homepage-raw.html — {len(html)} chars]')

# ── 6. CHECK GLOBAL STYLES API STATE ─────────────────────────
print(f'\n[GLOBAL STYLES API — ID 6]')
hdrs = {'Authorization': f'Basic {auth_token}', 'Content-Type': 'application/json'}
req2 = urllib.request.Request(f'{BASE}/wp-json/wp/v2/global-styles/6', headers=hdrs)
try:
    resp2 = urllib.request.urlopen(req2, timeout=15)
    gs = json.loads(resp2.read())
    css_stored = gs.get('styles', {}).get('css', '')
    color_bg = gs.get('styles', {}).get('color', {}).get('background', 'not set')
    print(f'  CSS stored: {len(css_stored)} chars')
    print(f'  styles.color.background: {color_bg}')
    print(f'  First 100 chars of CSS: {css_stored[:100]}')
except Exception as e:
    print(f'  Error: {e}')
