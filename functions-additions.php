<?php
/**
 * BHRC Theme Functions Additions
 * ─────────────────────────────────────────────
 * Add this code to your active theme's functions.php
 * via Bluehost cPanel → File Manager → public_html/wp-content/themes/[your-theme]/functions.php
 * OR via WP Admin → Appearance → Theme File Editor → functions.php
 *
 * REQUIRED BEFORE SITE GOES LIVE:
 * 1. Replace G-XXXXXXXXXX with your real GA4 Measurement ID
 * 2. Replace the [bhrc_kit_form] placeholder HTML with your real Kit.com embed script
 */

// ─────────────────────────────────────────────────────────────
// 1. GA4 TRACKING — Replace G-XXXXXXXXXX with your real ID
// ─────────────────────────────────────────────────────────────
add_action('wp_head', function() {
    ?>
    <!-- BHRC GA4 Tracking — BeHappyRetired.com -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'G-XXXXXXXXXX');

      // Conversion: Email opt-in submit
      document.addEventListener('DOMContentLoaded', function() {
        // Kit.com forms
        document.querySelectorAll('form.formkit-form, form[data-sv-form], .seva-form').forEach(function(form) {
          form.addEventListener('submit', function() {
            gtag('event', 'email_signup', { event_category: 'engagement', event_label: 'kit_form', value: 1 });
          });
        });
        // Gumroad button click
        document.querySelectorAll('a[href*="gumroad.com"], a[data-track="gumroad-click"]').forEach(function(el) {
          el.addEventListener('click', function() {
            gtag('event', 'gumroad_click', { event_category: 'conversion', event_label: el.textContent.trim(), value: 1 });
          });
        });
        // Affiliate link clicks
        document.querySelectorAll('a[data-track^="affiliate-"]').forEach(function(el) {
          el.addEventListener('click', function() {
            gtag('event', 'affiliate_click', { event_category: 'monetisation', event_label: el.getAttribute('data-track').replace('affiliate-',''), value: 1 });
          });
        });
      });
    </script>
    <?php
}, 1);

// ─────────────────────────────────────────────────────────────
// 2. KIT.COM NEWSLETTER SHORTCODE [bhrc_kit_form]
// ─────────────────────────────────────────────────────────────
// REPLACE the placeholder div below with your actual Kit.com embed script.
// Get it from: Kit.com → Forms → [your form] → Embed → Inline/Script
// ─────────────────────────────────────────────────────────────
add_shortcode('bhrc_kit_form', function() {
    // ── PLACEHOLDER ── Replace everything inside this return with your Kit.com script
    return '<div class="bhrc-kit-placeholder" style="background:#f9f4eb;border:2px dashed #C4762A;border-radius:4px;padding:24px;text-align:center;margin:16px 0;">
        <p style="margin:0 0 8px;font-family:Lora,serif;font-weight:600;color:#2C2416;">Join the list</p>
        <p style="margin:0 0 16px;font-size:14px;color:#6B5D4F;font-family:Lora,serif;">Paste your Kit.com embed script here to activate</p>
        <p style="margin:0;font-size:12px;color:#999;">[bhrc_kit_form placeholder — update functions.php]</p>
    </div>';
    // ── When Kit.com is ready, replace above with:
    // return '<script src="https://f.convertkit.com/xxxxxxxxxx/xxxxxxxxxx.js"></script>';
    // or the inline form HTML from Kit.com
});

// ─────────────────────────────────────────────────────────────
// 3. POST SIDEBAR CTA — Add free guide CTA after post content
// ─────────────────────────────────────────────────────────────
add_filter('the_content', function($content) {
    if (is_single()) {
        $cta = '<div class="post-cta-block" style="background:rgba(196,118,42,0.06);border:1px solid #DDD3BE;border-radius:4px;padding:24px 28px;margin-top:48px;">
            <p style="margin:0 0 12px;font-family:Cormorant Garamond,Georgia,serif;font-size:1.3rem;font-weight:500;color:#2C2416;">Found this useful?</p>
            <p style="margin:0 0 16px;font-family:Lora,serif;color:#2C2416;">The free guide goes deeper — five things I\'ve learned about finding happiness in retirement. Honest, not cheerful.</p>
            <a href="/free-guide" style="display:inline-block;background:#C4762A;color:#fff;padding:12px 24px;border-radius:3px;text-decoration:none;font-family:Lora,serif;font-weight:500;">Get the free guide &rarr;</a>
        </div>';
        $content .= $cta;
    }
    return $content;
});

// ─────────────────────────────────────────────────────────────
// 4. SIDEBAR NEWSLETTER WIDGET (fallback registration)
// ─────────────────────────────────────────────────────────────
add_action('widgets_init', function() {
    register_sidebar([
        'name'          => 'Blog Sidebar',
        'id'            => 'sidebar-1',
        'description'   => 'Newsletter opt-in and blog sidebar widgets.',
        'before_widget' => '<div class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ]);
});

// ─────────────────────────────────────────────────────────────
// 5. ENQUEUE GOOGLE FONTS
// ─────────────────────────────────────────────────────────────
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style(
        'bhrc-google-fonts',
        'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;1,300;1,400&family=Lora:ital,wght@0,400;0,500;1,400&display=swap',
        [],
        null
    );
});

// ─────────────────────────────────────────────────────────────
// 6. THEME SUPPORT
// ─────────────────────────────────────────────────────────────
add_action('after_setup_theme', function() {
    add_theme_support('custom-logo');
    add_theme_support('post-thumbnails');
    add_theme_support('title-tag');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script']);

    register_nav_menus([
        'primary' => 'Primary Navigation',
        'footer'  => 'Footer Navigation',
    ]);
});
