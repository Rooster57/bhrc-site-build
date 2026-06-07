"""
Fix 2:
1. Remove bhrc-nav orphan from /homepage page
2. Check published posts — if none, create stubs from STAGING content
3. Verify front page setting points to /homepage
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
        return {'_error': e.code, '_body': e.read().decode()[:500]}
    except Exception as e:
        return {'_error': str(e)}

log = []

# ── 1. FIX HOMEPAGE (correct slug = 'homepage') ─────────────
home = req('GET', f'{BASE}/wp-json/wp/v2/pages?slug=homepage&status=any')
if isinstance(home, list) and home:
    page = home[0]
    page_id = page['id']
    raw_content = page.get('content', {}).get('raw', '')
    rendered    = page.get('content', {}).get('rendered', '')
    content     = raw_content or rendered

    log.append(f'INFO Homepage ID: {page_id}, content length: {len(content)} chars')

    # Check for bhrc-nav
    has_bhrc_nav = 'bhrc-nav' in content
    log.append(f'INFO bhrc-nav in homepage content: {has_bhrc_nav}')

    if has_bhrc_nav:
        # Remove it
        content_clean = re.sub(
            r'<!-- wp:html -->\s*<[^>]*class="bhrc-nav[^"]*".*?<!-- /wp:html -->',
            '', content, flags=re.DOTALL
        )
        content_clean = re.sub(
            r'<nav class="bhrc-nav[^"]*">.*?</nav>', '', content_clean, flags=re.DOTALL
        )
        upd = req('PUT', f'{BASE}/wp-json/wp/v2/pages/{page_id}',
                  {'content': content_clean, 'status': 'publish'})
        if '_error' not in upd:
            log.append(f'OK  bhrc-nav removed from homepage')
        else:
            log.append(f'ERR homepage update failed: {upd.get("_body","")[:150]}')

    # Show first 600 chars of content for inspection
    log.append(f'INFO Homepage content preview:\n{content[:600]}')
else:
    log.append(f'WARN Homepage not found: {home}')

# ── 2. CHECK PUBLISHED POSTS ─────────────────────────────────
posts = req('GET', f'{BASE}/wp-json/wp/v2/posts?status=publish,draft&per_page=20')
if isinstance(posts, list):
    log.append(f'\nINFO Posts ({len(posts)} total):')
    for p in posts:
        log.append(f'  [{p.get("status")}] /{p.get("slug")} — "{p.get("title",{}).get("rendered","?")}"')
else:
    log.append(f'WARN Posts query failed: {posts}')

# ── 3. CHECK FRONT PAGE SETTING ──────────────────────────────
settings = req('GET', f'{BASE}/wp-json/wp/v2/settings')
if '_error' not in settings:
    log.append(f'\nINFO show_on_front: {settings.get("show_on_front")}')
    log.append(f'INFO page_on_front: {settings.get("page_on_front")} (should be homepage ID)')
    log.append(f'INFO page_for_posts: {settings.get("page_for_posts")}')
else:
    log.append(f'WARN settings: {settings}')

print('\n'.join(log))
