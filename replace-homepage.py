"""
Replace homepage content with clean WordPress blocks.
Removes the raw HTML dump (old mockup) that was overriding all CSS.
"""
import urllib.request, urllib.error, base64, json

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
        return {'_error': e.code, '_body': e.read().decode()[:500)}
    except Exception as e:
        return {'_error': str(e)}

# Clean WordPress block content for homepage
homepage_content = """
<!-- wp:group {"className":"hero-section","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group hero-section">

<!-- wp:paragraph {"style":{"typography":{"fontSize":"13px","letterSpacing":"2px","textTransform":"uppercase"}},"textColor":"contrast"} -->
<p class="has-contrast-color" style="font-size:13px;letter-spacing:2px;text-transform:uppercase;opacity:0.5">Honest writing about retirement</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"style":{"typography":{"fontStyle":"italic","fontWeight":"300"}}} -->
<h1 style="font-style:italic;font-weight:300">Finding the Happiness of Pursuit</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"typography":{"fontSize":"clamp(1rem,3vw,1.2rem)"}}} -->
<p style="font-size:clamp(1rem,3vw,1.2rem);max-width:560px">Letters from the other side of a full life — honest writing about retirement, identity, and the joy that is available right now.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons">
<!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/free-guide">Get the Free Guide &rarr;</a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->

</div>
<!-- /wp:group -->

<!-- wp:separator {"className":"is-style-wide","style":{"color":{"background":"#DDD3BE"}}} -->
<hr class="wp-block-separator has-alpha-channel-opacity has-background is-style-wide" style="background-color:#DDD3BE;height:1px;border:none"/>
<!-- /wp:separator -->

<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"},"blockGap":"var:preset|spacing|40"}},"backgroundColor":"contrast-3","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-contrast-3-background-color has-background">

<!-- wp:heading {"level":2,"textAlign":"center"} -->
<h2 class="wp-block-heading has-text-align-center">Three things I write about</h2>
<!-- /wp:heading -->

<!-- wp:columns {"style":{"spacing":{"blockGap":"var:preset|spacing|40"}}} -->
<div class="wp-block-columns">

<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"1.4rem"}}} -->
<h3 class="wp-block-heading" style="font-size:1.4rem">Emotional Wellness</h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>The quiet work of staying connected to yourself. Identity after the career ends. Courage to feel things fully at this stage of life.</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p><a href="/blog">Read &rarr;</a></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"1.4rem"}}} -->
<h3 class="wp-block-heading" style="font-size:1.4rem">Joyful Living</h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>Not performed happiness. The genuine, sometimes surprising discovery that joy is available right now — in this ordinary moment.</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p><a href="/blog">Read &rarr;</a></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"1.4rem"}}} -->
<h3 class="wp-block-heading" style="font-size:1.4rem">AgeTech</h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>Technology that genuinely adds time, connection, and ease to later life. Honest reviews — nothing here just for the commission.</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p><a href="/blog">Read &rarr;</a></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

</div>
<!-- /wp:columns -->

</div>
<!-- /wp:group -->

<!-- wp:separator {"style":{"color":{"background":"#DDD3BE"}}} -->
<hr class="wp-block-separator has-alpha-channel-opacity has-background" style="background-color:#DDD3BE;height:1px;border:none"/>
<!-- /wp:separator -->

<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group">

<!-- wp:heading {"level":2,"textAlign":"center"} -->
<h2 class="wp-block-heading has-text-align-center">Latest writings</h2>
<!-- /wp:heading -->

<!-- wp:latest-posts {"postsToShow":3,"displayPostExcerpt":true,"excerptLength":28,"displayFeaturedImage":true,"featuredImageAlign":"top","featuredImageSizeSlug":"medium","displayPostDate":false,"layout":{"type":"grid","columnCount":3}} /-->

<!-- wp:paragraph {"textAlign":"center","style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}}} -->
<p class="has-text-align-center" style="margin-top:var(--wp--preset--spacing--40)"><a href="/blog">Read all articles &rarr;</a></p>
<!-- /wp:paragraph -->

</div>
<!-- /wp:group -->

<!-- wp:separator {"style":{"color":{"background":"#DDD3BE"}}} -->
<hr class="wp-block-separator has-alpha-channel-opacity has-background" style="background-color:#DDD3BE;height:1px;border:none"/>
<!-- /wp:separator -->

<!-- wp:group {"className":"newsletter-section","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}},"color":{"background":"#1A1F2E","text":"#FDFAF5"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group newsletter-section has-background" style="background-color:#1A1F2E;color:#FDFAF5">

<!-- wp:heading {"level":2,"textAlign":"center","style":{"color":{"text":"#FDFAF5"}}} -->
<h2 class="wp-block-heading has-text-align-center" style="color:#FDFAF5">Join the journey</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"textAlign":"center","style":{"color":{"text":"rgba(253,250,245,0.85)"}}} -->
<p class="has-text-align-center" style="color:rgba(253,250,245,0.85)">The 5 Keys to Retirement Happiness — a free guide from someone living it.<br>Yours when you join the list.</p>
<!-- /wp:paragraph -->

<!-- wp:shortcode -->
[bhrc_kit_form]
<!-- /wp:shortcode -->

<!-- wp:paragraph {"textAlign":"center","style":{"typography":{"fontSize":"13px"},"color":{"text":"rgba(253,250,245,0.5)"}}} -->
<p class="has-text-align-center" style="font-size:13px;color:rgba(253,250,245,0.5)">No spam. Unsubscribe any time.</p>
<!-- /wp:paragraph -->

</div>
<!-- /wp:group -->

<!-- wp:separator {"style":{"color":{"background":"#DDD3BE"}}} -->
<hr class="wp-block-separator has-alpha-channel-opacity has-background" style="background-color:#DDD3BE;height:1px;border:none"/>
<!-- /wp:separator -->

<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group">

<!-- wp:columns {"verticalAlignment":"center"} -->
<div class="wp-block-columns are-vertically-aligned-center">

<!-- wp:column {"width":"60%"} -->
<div class="wp-block-column" style="flex-basis:60%">
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">There is a particular quiet that settles in after the career ends.</h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>It is not unhappiness. It is something more complicated — waking up on a Tuesday morning with nowhere to be, wondering what this season of life is actually for.</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p>If you have felt that quiet, you are in the right place.</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p><a href="/about">About this site &rarr;</a>&nbsp; &nbsp;<a href="/about-farook">About Farook &rarr;</a></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column {"width":"40%"} -->
<div class="wp-block-column" style="flex-basis:40%">
<!-- wp:paragraph {"style":{"typography":{"fontSize":"clamp(3rem,8vw,5rem)","lineHeight":"1"},"color":{"text":"#DDD3BE"}}} -->
<p style="font-size:clamp(3rem,8vw,5rem);line-height:1;color:#DDD3BE;text-align:center">&#x2708;</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

</div>
<!-- /wp:columns -->

</div>
<!-- /wp:group -->
"""

# Update homepage
r = req('PUT', f'{BASE}/wp-json/wp/v2/pages/8',
        {'content': homepage_content, 'status': 'publish', 'title': 'Be Happy Retired'})

if '_error' not in r:
    new_len = len(r.get('content', {}).get('rendered', ''))
    print(f'OK  Homepage replaced with clean blocks ({new_len} chars rendered)')
    print(f'    Title: {r.get("title",{}).get("rendered","?")}')
    print(f'    Status: {r.get("status")}')
    print(f'    URL: {r.get("link")}')
else:
    print(f'ERR Homepage update failed: {r.get("_body","")[:300]}')
