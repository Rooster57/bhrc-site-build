<?php
/**
 * BHRC Design Fix Deployer
 * Fixes: white background, duplicate titles, duplicate header, mobile layout.
 *
 * Upload to WordPress root via Bluehost cPanel File Manager.
 * Run once at: https://behappyretired.com/deploy-design-fix.php?key=BHRCfix2026
 * DELETE immediately after running.
 */

define('DEPLOY_KEY', 'BHRCfix2026');
if (!isset($_GET['key']) || $_GET['key'] !== DEPLOY_KEY) {
    die('Unauthorised.');
}

define('ABSPATH', dirname(__FILE__) . '/');
require_once(ABSPATH . 'wp-load.php');

if (!function_exists('wp_update_custom_css_post')) {
    die('WordPress not loaded.');
}

$log = [];

// ── 1. PUSH CUSTOM CSS ──────────────────────────────────────
// Read the CSS (inline — avoids file dependency)
$css = <<<'ENDCSS'
@import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;0,700;1,300;1,400;1,600&family=Lora:ital,wght@0,400;0,500;1,400;1,500&display=swap');

:root {
  --parchment:     #F5EFE0;
  --parchment-mid: #EDE5D0;
  --parchment-dark:#DDD3BE;
  --amber:         #C4762A;
  --amber-dark:    #A35F1F;
  --amber-light:   #E8A45A;
  --navy:          #1A1F2E;
  --sage:          #7A9977;
  --text:          #2C2416;
  --text-muted:    #6B5D4F;
  --white:         #FDFAF5;
  --radius:        6px;
}

*,*::before,*::after{box-sizing:border-box;}

html,body,.site,.site-content,#page,#content,.wp-site-blocks,main,.main-content,article,.hentry,.entry-content{background-color:var(--parchment)!important;color:var(--text);}

body{font-family:'Lora',Georgia,serif;font-size:17px;line-height:1.8;-webkit-font-smoothing:antialiased;}

body::after{content:'';position:fixed;inset:0;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='200' height='200'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.75' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='200' height='200' filter='url(%23n)' opacity='0.035'/%3E%3C/svg%3E");pointer-events:none;z-index:9999;}

h1,h2,h3,h4,h5,h6,.wp-block-post-title,.entry-title,.page-title{font-family:'Cormorant Garamond',Georgia,serif!important;font-weight:600;color:var(--navy)!important;line-height:1.2;letter-spacing:-0.015em;}
h1,.entry-title{font-size:clamp(2rem,8vw,3.6rem);}
h2{font-size:clamp(1.5rem,5vw,2.4rem);}
h3{font-size:clamp(1.2rem,4vw,1.8rem);}
p{margin-bottom:1.4em;}
a{color:var(--amber);text-decoration-thickness:1px;text-underline-offset:3px;transition:color 0.15s;}
a:hover{color:var(--amber-dark);}

/* DUPLICATE TITLE FIX */
.page-header .page-title,
.single .page-header,
.blog-header .page-title,
.ast-archive-title,
.site-main > .page-header,
.entry-header ~ .entry-title,
.wp-block-post-title:not(:first-of-type){display:none!important;}

/* DUPLICATE SITE NAME FIX */
.site-description,
.wp-block-site-tagline{display:none!important;}

.wp-block-site-title a,.site-title a,.custom-logo-link{font-family:'Cormorant Garamond',serif!important;font-size:22px!important;font-weight:700!important;color:var(--amber)!important;text-decoration:none!important;letter-spacing:-0.02em;}

/* HEADER */
.site-header,header.wp-block-template-part,#masthead{background-color:var(--navy)!important;position:sticky;top:0;z-index:100;padding:14px 20px!important;box-shadow:0 2px 12px rgba(26,31,46,0.25);}

.main-navigation a,.wp-block-navigation a{color:var(--white)!important;font-family:'Lora',serif!important;font-size:14px!important;text-decoration:none!important;opacity:0.85;transition:opacity 0.15s;}
.main-navigation a:hover,.wp-block-navigation a:hover{opacity:1;}

.wp-block-navigation__responsive-container-open{color:var(--white)!important;display:flex!important;}
.wp-block-navigation__responsive-container.is-menu-open{background-color:var(--navy)!important;}
.wp-block-navigation__responsive-container.is-menu-open a{color:var(--white)!important;font-size:20px!important;}

/* HERO */
.hero-section,.wp-block-group.hero-section{padding:56px 24px 48px;text-align:center;background:var(--parchment)!important;}
.hero-heading,.hero-section h1{font-size:clamp(2.4rem,9vw,4rem)!important;font-weight:300!important;font-style:italic;color:var(--navy)!important;line-height:1.15;margin-bottom:20px;}
.hero-section h1::after{content:'';display:block;width:48px;height:3px;background:var(--amber);margin:18px auto 0;border-radius:2px;}
.hero-section::before{content:'';display:block;width:36px;height:36px;margin:0 auto 24px;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 36 36'%3E%3Cpath d='M2 18 L34 4 L22 32 L17 22 Z' fill='none' stroke='%23C4762A' stroke-width='1.5' stroke-linejoin='round'/%3E%3Cpath d='M17 22 L22 32 L22 22 Z' fill='%23C4762A' opacity='0.3'/%3E%3C/svg%3E");background-repeat:no-repeat;background-size:contain;opacity:0.7;}

/* BUTTONS */
.wp-block-button__link,.wp-block-button__link:visited{background:var(--amber)!important;color:var(--white)!important;font-family:'Lora',serif!important;font-size:15px!important;font-weight:500!important;padding:14px 28px!important;border-radius:3px!important;border:none!important;text-decoration:none!important;transition:background 0.2s,transform 0.15s;}
.wp-block-button__link:hover{background:var(--amber-dark)!important;transform:translateY(-1px);}
@media(max-width:480px){.wp-block-button__link{display:block!important;width:100%!important;text-align:center!important;}}

/* FEATURED IMAGES */
.post-thumbnail,.wp-post-image,.wp-block-post-featured-image,.wp-block-post-featured-image img,.post-thumbnail img{display:block!important;width:100%!important;height:auto!important;object-fit:cover;}
.single .wp-block-post-featured-image,.single .post-thumbnail{margin:0 0 32px 0!important;border-radius:var(--radius);overflow:hidden;box-shadow:0 4px 20px rgba(26,31,46,0.12);}
.single .wp-block-post-featured-image img{aspect-ratio:16/7;object-fit:cover;width:100%;}

/* POST CARDS */
.wp-block-latest-posts,.wp-block-query,.posts-grid{display:grid!important;grid-template-columns:1fr;gap:24px;padding:0;list-style:none;}
.wp-block-latest-posts__list-item,.wp-block-post{background:var(--white);border-radius:var(--radius);overflow:hidden;box-shadow:0 2px 12px rgba(26,31,46,0.07);transition:transform 0.2s,box-shadow 0.2s;}
.wp-block-latest-posts__list-item:hover,.wp-block-post:hover{transform:translateY(-3px);box-shadow:0 8px 28px rgba(26,31,46,0.12);}
.wp-block-post .wp-block-post-featured-image img,.wp-block-latest-posts__featured-image img{width:100%;aspect-ratio:16/9;object-fit:cover;display:block;}
.wp-block-latest-posts__list-item a,.wp-block-post-title a{font-family:'Cormorant Garamond',serif!important;font-size:1.3rem!important;font-weight:600!important;color:var(--navy)!important;text-decoration:none!important;line-height:1.3;display:block;padding:18px 18px 4px;}
.wp-block-post-title a:hover{color:var(--amber)!important;}
.wp-block-post-terms a,.cat-links a{display:inline-block;font-size:11px;font-weight:500;text-transform:uppercase;letter-spacing:0.9px;color:var(--sage)!important;text-decoration:none!important;padding:0 18px 4px;}
.wp-block-post-excerpt__excerpt{font-size:15px;color:var(--text-muted);line-height:1.65;padding:0 18px 18px;margin:0;}

/* SINGLE POST */
.entry-content,.wp-block-post-content{font-size:17px;line-height:1.85;color:var(--text);max-width:680px;margin:0 auto;padding:0 20px;}
.entry-content h2,.wp-block-post-content h2{margin-top:2em;padding-bottom:8px;border-bottom:1px solid var(--parchment-dark);}
.post-intro,.entry-content p:first-of-type{font-size:1.15rem;font-style:italic;color:var(--text-muted);border-left:3px solid var(--amber);padding-left:20px;margin-left:-23px;}
.wp-block-separator{border:none!important;border-top:1px solid var(--parchment-dark)!important;max-width:120px;margin:48px auto;opacity:1!important;}
.post-cta-block,.wp-block-group.post-cta-block{background:rgba(196,118,42,0.07);border:1px solid var(--parchment-dark);border-left:3px solid var(--amber);border-radius:0 var(--radius) var(--radius) 0;padding:22px 24px;margin-top:48px;}
blockquote{border-left:3px solid var(--amber);margin:2em 0;padding:0.5em 0 0.5em 24px;font-style:italic;color:var(--text-muted);font-size:1.1em;}

/* NEWSLETTER */
.newsletter-section,.wp-block-group.newsletter-section{background:var(--navy)!important;color:var(--white);padding:56px 24px;text-align:center;}
.newsletter-section h2,.newsletter-section h3{color:var(--white)!important;font-size:clamp(1.6rem,5vw,2.2rem)!important;}
.newsletter-section p{color:rgba(253,250,245,0.85);}

/* FOOTER */
.site-footer,#colophon,footer.wp-block-template-part{background:var(--navy)!important;color:rgba(253,250,245,0.6);padding:40px 24px;text-align:center;font-size:13px;}
.site-footer a,footer.wp-block-template-part a{color:rgba(253,250,245,0.6)!important;text-decoration:none;}
.site-footer a:hover,footer.wp-block-template-part a:hover{color:var(--amber-light)!important;}
.site-footer .wp-block-site-title a{color:var(--amber)!important;font-family:'Cormorant Garamond',serif!important;font-size:18px!important;font-weight:700!important;}

/* PAGE LAYOUT */
.page-lead,.page-intro{font-size:1.15rem;font-style:italic;color:var(--text-muted);border-left:3px solid var(--amber);padding-left:20px;margin-bottom:32px;}
.pillar-block{border-left:3px solid var(--amber);padding:4px 0 4px 20px;margin-bottom:28px;}
.pillar-block h3{color:var(--amber)!important;}

/* RESPONSIVE */
@media(min-width:600px){
  body{font-size:18px;}
  .wp-block-latest-posts,.wp-block-query,.posts-grid{grid-template-columns:1fr 1fr;}
  .entry-content,.wp-block-post-content{padding:0 32px;}
  .hero-section{padding:72px 40px 64px;}
}
@media(min-width:960px){
  .wp-block-latest-posts,.wp-block-query,.posts-grid{grid-template-columns:repeat(3,1fr);}
  .site-header,header.wp-block-template-part,#masthead{padding:16px 48px!important;}
  .entry-content,.wp-block-post-content{font-size:19px;padding:0;}
  .hero-section{padding:96px 48px 80px;}
  .newsletter-section{padding:80px 48px;}
}

/* ADMIN BAR */
.admin-bar .site-header,.admin-bar header.wp-block-template-part{top:32px!important;}
@media(max-width:782px){.admin-bar .site-header,.admin-bar header.wp-block-template-part{top:46px!important;}}
ENDCSS;

$result = wp_update_custom_css_post($css, ['stylesheet' => get_option('stylesheet')]);
if (is_wp_error($result)) {
    $log[] = ['status' => 'error', 'msg' => 'CSS failed: ' . $result->get_error_message()];
} else {
    $log[] = ['status' => 'ok', 'msg' => 'CSS deployed — parchment background, mobile-first layout, duplicate title fix'];
}

// ── 2. ENSURE SITE TAGLINE IS BLANK (stops it showing as a second title) ──
update_option('blogdescription', '');
$log[] = ['status' => 'ok', 'msg' => 'Site tagline cleared (was appearing as second header title)'];

// ── 3. SET SHOW_ON_FRONT IF NOT ALREADY STATIC ──────────────
if (get_option('show_on_front') !== 'page') {
    $home = get_page_by_path('home', OBJECT, 'page');
    if ($home) {
        update_option('show_on_front', 'page');
        update_option('page_on_front', $home->ID);
        $log[] = ['status' => 'ok', 'msg' => 'Homepage set to static front page (ID: ' . $home->ID . ')'];
    } else {
        $log[] = ['status' => 'warn', 'msg' => 'Homepage page not found — set manually in WP Admin → Settings → Reading'];
    }
}

// ── 4. REPORT ────────────────────────────────────────────────
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width">
<title>BHRC Design Fix Results</title>
<style>
  body{font-family:monospace;background:#1A1F2E;color:#e0e0e0;padding:32px 20px;font-size:14px;}
  h1{color:#C4762A;margin-bottom:4px;}
  .ok{color:#4caf50;margin:8px 0;}
  .warn{color:#ff9800;margin:8px 0;}
  .error{color:#f44336;margin:8px 0;}
  .next{background:#111;border-left:3px solid #C4762A;padding:16px;margin-top:24px;border-radius:0 4px 4px 0;}
  .next li{margin:8px 0;}
</style>
</head>
<body>
<h1>BHRC Design Fix</h1>
<p>Run: <?php echo date('Y-m-d H:i:s'); ?></p>

<?php foreach ($log as $entry): ?>
<p class="<?php echo $entry['status']; ?>">
  <?php echo $entry['status'] === 'ok' ? '✓' : ($entry['status'] === 'warn' ? '⚠' : '✗'); ?>
  <?php echo htmlspecialchars($entry['msg']); ?>
</p>
<?php endforeach; ?>

<div class="next">
  <strong style="color:#C4762A">Next steps:</strong>
  <ol>
    <li>Check behappyretired.com on your phone (vertical) — background should be warm parchment, no white</li>
    <li>Open any blog post — confirm title appears only once</li>
    <li>Check header — "Be Happy Retired" should appear once only, on dark navy bar</li>
    <li>If any issue persists, check WP Admin → Appearance → Themes and confirm the active theme name, then tell Larry so CSS selectors can be tuned</li>
    <li style="color:#f44336"><strong>DELETE this file now: behappyretired.com/deploy-design-fix.php</strong></li>
  </ol>
</div>
</body>
</html>
<?php
