<?php
/**
 * BHRC WordPress Site Deployer
 * Upload this file to your WordPress root directory via Bluehost cPanel File Manager.
 * Access it once at: https://behappyretired.com/deploy-bhrc.php?key=BHRC2026
 * DELETE THIS FILE immediately after running.
 *
 * Built by Dev for BeHappyRetired.com — 2026-05-24
 */

// Security key — change this before uploading
define('DEPLOY_KEY', 'BHRC2026');

if (!isset($_GET['key']) || $_GET['key'] !== DEPLOY_KEY) {
    die('Unauthorised.');
}
$deploy_mode = (isset($_GET['content']) && $_GET['content'] === '1')
    ? 'framework_plus_content'
    : 'framework_only';
$framework_only = $deploy_mode === 'framework_only';

// Load WordPress
define('ABSPATH', dirname(__FILE__) . '/');
require_once(ABSPATH . 'wp-load.php');

if (!function_exists('wp_insert_post')) {
    die('WordPress not loaded.');
}
$results = [
    'deployment_mode' => $deploy_mode,
    'categories' => [],
    'posts' => [],
    'pages' => [],
];
$errors = [];

// ─────────────────────────────────────────────────────────────
// HELPER FUNCTIONS
// ─────────────────────────────────────────────────────────────

function bhrc_create_page($title, $slug, $content, $status = 'draft') {
    // Check if page exists
    $existing = get_page_by_path($slug, OBJECT, 'page');
    if ($existing) {
        $updated = wp_update_post([
            'ID'           => $existing->ID,
            'post_title'   => $title,
            'post_content' => $content,
            'post_status'  => $status,
            'post_name'    => $slug,
        ]);
        return ['action' => 'updated', 'id' => $updated, 'slug' => $slug];
    }

    $id = wp_insert_post([
        'post_title'   => $title,
        'post_name'    => $slug,
        'post_content' => $content,
        'post_status'  => $status,
        'post_type'    => 'page',
        'post_author'  => 1,
    ]);
    return ['action' => 'created', 'id' => $id, 'slug' => $slug];
}

function bhrc_create_post($title, $slug, $content, $category_slug, $status = 'draft') {
    $existing = get_page_by_path($slug, OBJECT, 'post');
    if ($existing) {
        wp_update_post(['ID' => $existing->ID, 'post_content' => $content, 'post_status' => $status]);
        return ['action' => 'updated', 'id' => $existing->ID, 'slug' => $slug];
    }

    // Ensure category exists
    $cat = get_term_by('slug', $category_slug, 'category');
    $cat_id = $cat ? $cat->term_id : 1;

    $id = wp_insert_post([
        'post_title'    => $title,
        'post_name'     => $slug,
        'post_content'  => $content,
        'post_status'   => $status,
        'post_type'     => 'post',
        'post_author'   => 1,
        'post_category' => [$cat_id],
        'post_excerpt'  => wp_trim_words(strip_tags($content), 30),
    ]);
    return ['action' => 'created', 'id' => $id, 'slug' => $slug];
}

function bhrc_ensure_category($name, $slug, $description = '') {
    $existing = get_term_by('slug', $slug, 'category');
    if ($existing) return $existing->term_id;
    $result = wp_insert_term($name, 'category', ['slug' => $slug, 'description' => $description]);
    return is_wp_error($result) ? 1 : $result['term_id'];
}

function bhrc_set_theme_option($option, $value) {
    update_option($option, $value);
}

// ─────────────────────────────────────────────────────────────
// STEP 1: CATEGORIES
// ─────────────────────────────────────────────────────────────

$results['categories'] = [];
$results['categories'][] = bhrc_ensure_category('Emotional Wellness', 'emotional-wellness', 'Honest writing about the inner landscape of retirement — identity, connection, and the courage to feel things fully.');
$results['categories'][] = bhrc_ensure_category('Joyful Living', 'joyful-living', 'Finding genuine joy in the ordinary moments of this season of life. Not performed happiness — the real thing.');
$results['categories'][] = bhrc_ensure_category('AgeTech', 'agetech', 'Technology that genuinely adds time, connection, and ease to later life. Honest reviews and recommendations.');

// ─────────────────────────────────────────────────────────────
// STEP 2: BLOG POSTS
// ─────────────────────────────────────────────────────────────
if ($framework_only) {
    $results['posts_skipped'] = 'Framework-only mode active. Starter blog content was skipped.';
} else {

// Blog Post 1: Ikigai — Emotional Wellness
$ikigai_content = <<<'POSTCONTENT'
<!-- wp:paragraph {"className":"post-intro"} -->
<p class="post-intro">You've probably heard the word before. It circulates in wellness circles, appears in retirement guides, gets printed on motivational posters. If you're like me, you encountered it and thought: another Japanese concept that sounds profound and means less than it promises.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>I want to tell you why I changed my mind.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2} -->
<h2>What ikigai actually means</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><em>Ikigai</em> (生き甲斐) translates, roughly, as "reason for being" — or more precisely, the thing that makes getting up in the morning feel worthwhile. Not a mission statement. Not a five-year plan. The quiet, specific, personal thing that gives a day its shape.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>The way it's usually explained, ikigai lives at the centre of four questions:</p>
<!-- /wp:paragraph -->

<!-- wp:list -->
<ul>
<li>What do you love?</li>
<li>What are you good at?</li>
<li>What does the world need?</li>
<li>What can you be paid for?</li>
</ul>
<!-- /wp:list -->

<!-- wp:paragraph -->
<p>Find the intersection, the diagram tells you, and you've found your ikigai. It's a tidy model. It's also, I think, slightly wrong — or at least misleading for people in this season of life.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2} -->
<h2>The problem with the four-circle diagram</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>The diagram was designed for people building careers. It assumes that purpose is something you do for money, for utility, for the world's approval. That's a reasonable framework when you're thirty-two and figuring out what to do with your working life.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>But when you've retired — when the career is behind you, when "what can you be paid for" is no longer the organising question — the diagram starts to feel like a tool borrowed from the wrong drawer.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>I spent months after I retired trying to find my "mission" — the grand thing I was supposed to do with this freedom. It produced a lot of ceiling-staring and not much else.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>What actually shifted things was a much simpler question: <em>What makes this particular morning feel worth having?</em></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2} -->
<h2>What purpose actually feels like in retirement</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Viktor Frankl — who knew something about finding meaning in impossible circumstances — wrote that meaning cannot be pursued directly. It arises as a byproduct of giving yourself to something or someone.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>I've found that to be true. On the mornings when I'm writing — genuinely writing, not editing or planning — I feel purposeful. Not because writing is my mission, but because I'm entirely inside it. The same thing happens when I'm walking, when I'm cooking a meal for Hau with real attention, when I'm reading something that opens a door I didn't know was there.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>None of these things look like purpose from the outside. None of them would make it into a motivational poster. But they are — for me, on those mornings — exactly what ikigai points at.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2} -->
<h2>A different way to use the concept</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>If you're trying to find your ikigai in retirement, I'd suggest setting the four-circle diagram aside. Instead, ask yourself this:</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><em>When did you last lose track of time? What were you doing?</em></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>That's not a trick question. It's a diagnostic. The things that absorb you fully — that pull you in without demanding that you perform or produce or justify yourself — those are where your ikigai lives in this season. Not in a grand mission. In the texture of an ordinary morning done well.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>This is joyful living. Not as a concept. As a practice, available right now, in the day you're already in.</p>
<!-- /wp:paragraph -->

<!-- wp:separator -->
<hr class="wp-block-separator"/>
<!-- /wp:separator -->

<!-- wp:group {"className":"post-cta-block"} -->
<div class="post-cta-block">
<!-- wp:paragraph -->
<p><strong>If this landed, you might find the free guide useful.</strong> Five things I've learned about finding happiness in retirement — not as a checklist, but as a conversation. <a href="/free-guide">Get it here &rarr;</a></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
POSTCONTENT;

$results['posts'][] = bhrc_create_post(
    'Ikigai — The Japanese Word I Wish I\'d Known Before I Retired',
    'ikigai-purpose-joyful-living',
    $ikigai_content,
    'emotional-wellness'
);

// Blog Post 2: The Warm Tofu Saturday — Joyful Living
$tofu_content = <<<'POSTCONTENT'
<!-- wp:paragraph {"className":"post-intro"} -->
<p class="post-intro">It was a cloudy Saturday morning in Vung Tau. Cool, with that particular saltiness in the air that tells you the sea is close before you've seen it.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Hau and I headed out early to Back Beach with Rieu — our poodle. His full name is Bun Rieu, after the Vietnamese crab noodle soup. Which tells you something about how we make decisions in our house.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>He knew where we were going before we did.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Back Beach was already busy. Holiday weekend, out-of-towners everywhere — families, children who'd been awake since five, umbrellas going up across the sand. That particular buzz of people who've been looking forward to something and have finally arrived at it.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>We had had a week. I won't go into it. You probably know the kind.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>We took our shoes off at the edge of the sand — I always do on a beach. There's something about bare feet on natural ground, something the body quietly appreciates. Whether that's what the earthing people are right about, or just the simple pleasure of it, I've stopped needing to know.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Rieu hit the water without breaking stride. Hau and I watched him dart between people's legs across the wet sand — small, white, absolutely certain this whole beach belonged to him. We kept an eye on him. You always do, on a crowded beach with a small dog.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>On the way back along the waterfront we stopped at an uncle's stall. We see him often enough that no words were needed. Just the nod. Two warm bowls of curd tofu, silken and just sweet enough. We stood right at the water's edge to eat them. The air there always feels different — cleaner, the kind that makes you breathe a little deeper without noticing you're doing it.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Warm bowl. Salt in the air. Rieu still darting somewhere between us and the sea.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2} -->
<h2>What I know about joyful living in this season</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>It is rarely where you expect it. Not the special occasion. Not the grand plan. It is a cloudy Saturday before the heat arrives. A familiar uncle who hands you a bowl without a word. A poodle absolutely convinced the world was made for him.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>The week loosened its grip somewhere in there. I only noticed after. The way you notice when a headache lifts.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Then Hau said she was getting too hot. And honestly, it was — the cloud burning off, the sun finding its way through. We hadn't been there long. Not as long as I'd have liked. Rieu wasn't ready either.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>But that was that.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>We finished the tofu, called Rieu away from whatever he was investigating, and turned for home.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>A perfect morning would have lasted longer. But we got the important parts. The bare feet on the sand. The warm bowl. The familiar uncle. The dog who was — without question — completely happy.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Sometimes that is enough.</p>
<!-- /wp:paragraph -->

<!-- wp:separator -->
<hr class="wp-block-separator"/>
<!-- /wp:separator -->

<!-- wp:group {"className":"post-cta-block"} -->
<div class="post-cta-block">
<!-- wp:paragraph -->
<p><strong>Joy shows up in the ordinary.</strong> I've been thinking about this a lot — what it actually takes to notice it, hold it, not chase it away. The free guide is where I've written it down. <a href="/free-guide">Get it here &rarr;</a></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
POSTCONTENT;

$results['posts'][] = bhrc_create_post(
    'The Warm Tofu Saturday',
    'the-warm-tofu-saturday',
    $tofu_content,
    'joyful-living'
);
}

// ─────────────────────────────────────────────────────────────
// STEP 3: PAGES
// ─────────────────────────────────────────────────────────────

// PAGE 1: Homepage
$homepage_content = <<<'CONTENT'
<!-- wp:group {"className":"hero-section","style":{"spacing":{"padding":{"top":"80px","bottom":"80px"}}}} -->
<div class="hero-section">

<!-- wp:heading {"level":1,"className":"hero-heading","textAlign":"center"} -->
<h1 class="has-text-align-center hero-heading">Finding the Happiness of Pursuit</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"hero-subheading","textAlign":"center"} -->
<p class="has-text-align-center hero-subheading">Letters from the other side of a full life — honest writing about retirement, identity, and the joy that's available right now.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons">
<!-- wp:button {"className":"btn-primary-amber"} -->
<div class="wp-block-button btn-primary-amber"><a class="wp-block-button__link" href="/free-guide">Get the Free Guide &rarr;</a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->

</div>
<!-- /wp:group -->

<!-- wp:separator {"className":"section-divider"} -->
<hr class="wp-block-separator section-divider"/>
<!-- /wp:separator -->

<!-- wp:heading {"level":2,"textAlign":"center"} -->
<h2 class="has-text-align-center">Latest from the blog</h2>
<!-- /wp:heading -->

<!-- wp:latest-posts {"postsToShow":3,"displayPostExcerpt":true,"excerptLength":25,"displayPostDate":true,"layout":{"type":"grid","columnCount":3}} /-->

<!-- wp:separator {"className":"section-divider"} -->
<hr class="wp-block-separator section-divider"/>
<!-- /wp:separator -->

<!-- wp:group {"className":"newsletter-section","style":{"spacing":{"padding":{"top":"60px","bottom":"60px"}}}} -->
<div class="newsletter-section">

<!-- wp:heading {"level":2,"textAlign":"center"} -->
<h2 class="has-text-align-center">Letters worth opening</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"textAlign":"center"} -->
<p class="has-text-align-center">No noise. No schedule. Just occasional letters — honest, unhurried, worth the read.</p>
<!-- /wp:paragraph -->

<!-- wp:shortcode -->
[bhrc_kit_form]
<!-- /wp:shortcode -->

<!-- wp:paragraph {"textAlign":"center","style":{"typography":{"fontSize":"13px"}}} -->
<p class="has-text-align-center" style="font-size:13px">No spam. Unsubscribe at any time.</p>
<!-- /wp:paragraph -->

</div>
<!-- /wp:group -->

<!-- wp:separator {"className":"section-divider"} -->
<hr class="wp-block-separator section-divider"/>
<!-- /wp:separator -->

<!-- wp:group {"className":"about-snippet"} -->
<div class="about-snippet">

<!-- wp:columns -->
<div class="wp-block-columns">
<!-- wp:column {"width":"60%"} -->
<div class="wp-block-column" style="flex-basis:60%">

<!-- wp:heading {"level":3} -->
<h3>There's a particular quiet that settles in after the career ends.</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>It's not unhappiness. It's something more complicated — waking up on a Tuesday morning with nowhere to be, wondering what this season of life is actually for. If you've felt that quiet, you're in the right place.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Be Happy Retired is honest writing about the inside of retirement: the emotional landscape, the identity questions, the small joys hiding in ordinary days.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><a href="/about">Read more about this site &rarr;</a></p>
<!-- /wp:paragraph -->

</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->

</div>
<!-- /wp:group -->
CONTENT;

$results['pages'][] = bhrc_create_page('Home', 'home', $homepage_content);

// PAGE 2: Free Guide
$free_guide_content = <<<'CONTENT'
<!-- wp:group {"className":"page-hero"} -->
<div class="page-hero">

<!-- wp:heading {"level":1} -->
<h1>The 5 Keys to Retirement Happiness</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"page-lead"} -->
<p class="page-lead">A free guide for anyone who's discovered that retirement is more complicated — and more interesting — than anyone told them.</p>
<!-- /wp:paragraph -->

</div>
<!-- /wp:group -->

<!-- wp:columns -->
<div class="wp-block-columns">

<!-- wp:column {"width":"60%"} -->
<div class="wp-block-column" style="flex-basis:60%">

<!-- wp:paragraph -->
<p>When I retired, I assumed the hard part was over. I was wrong — but not in the way I expected. The challenge wasn't practical. It was internal.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Over three years of living this, writing about it, and hearing from others who are in it too, I've noticed five things that make the real difference between a retirement that feels like loss and one that feels like arrival.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2} -->
<h2>What's in the guide</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul>
<li><strong>Key 1:</strong> Why identity doesn't retire when you do — and what to do about it</li>
<li><strong>Key 2:</strong> The difference between happiness and joy (and why one is available right now)</li>
<li><strong>Key 3:</strong> How to rebuild structure without turning retirement into a job</li>
<li><strong>Key 4:</strong> What connection actually looks like in this season</li>
<li><strong>Key 5:</strong> The practice of noticing — the skill that makes everything else work</li>
</ul>
<!-- /wp:list -->

<!-- wp:paragraph -->
<p>It's not a programme. It's not a checklist. It's an honest conversation about the things that matter — written from the inside of retirement, not from a distance.</p>
<!-- /wp:paragraph -->

</div>
<!-- /wp:column -->

<!-- wp:column {"width":"40%"} -->
<div class="wp-block-column" style="flex-basis:40%">

<!-- wp:group {"className":"guide-opt-in-box"} -->
<div class="guide-opt-in-box">

<!-- wp:heading {"level":3,"textAlign":"center"} -->
<h3 class="has-text-align-center">Get the free guide</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"textAlign":"center"} -->
<p class="has-text-align-center">Enter your name and email. The guide comes straight to your inbox.</p>
<!-- /wp:paragraph -->

<!-- wp:shortcode -->
[bhrc_kit_form]
<!-- /wp:shortcode -->

<!-- wp:paragraph {"textAlign":"center","style":{"typography":{"fontSize":"13px"}}} -->
<p class="has-text-align-center" style="font-size:13px">No spam. Unsubscribe at any time.</p>
<!-- /wp:paragraph -->

</div>
<!-- /wp:group -->

</div>
<!-- /wp:column -->

</div>
<!-- /wp:columns -->

<!-- wp:separator -->
<hr class="wp-block-separator"/>
<!-- /wp:separator -->

<!-- wp:paragraph {"textAlign":"center"} -->
<p class="has-text-align-center"><em>After you subscribe, you'll see a confirmation page. Check your inbox — the guide arrives within a few minutes.</em></p>
<!-- /wp:paragraph -->
CONTENT;

$results['pages'][] = bhrc_create_page('Free Guide — The 5 Keys to Retirement Happiness', 'free-guide', $free_guide_content);

// PAGE 3: The Art of Becoming
$art_content = <<<'CONTENT'
<!-- wp:group {"className":"page-hero"} -->
<div class="page-hero">

<!-- wp:heading {"level":1} -->
<h1>The Art of Becoming</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"page-lead"} -->
<p class="page-lead">A book about the second half of life — what it asks of us, and what it offers in return.</p>
<!-- /wp:paragraph -->

</div>
<!-- /wp:group -->

<!-- wp:columns -->
<div class="wp-block-columns">

<!-- wp:column {"width":"40%"} -->
<div class="wp-block-column" style="flex-basis:40%">

<!-- wp:image {"className":"book-cover-placeholder","sizeSlug":"medium"} -->
<figure class="wp-block-image size-medium book-cover-placeholder">
<img src="/wp-content/uploads/2026/05/art-of-becoming-cover-placeholder.jpg" alt="The Art of Becoming — cover" style="border-radius:4px;box-shadow:0 8px 32px rgba(0,0,0,0.15);"/>
</figure>
<!-- /wp:image -->

</div>
<!-- /wp:column -->

<!-- wp:column {"width":"60%"} -->
<div class="wp-block-column" style="flex-basis:60%">

<!-- wp:paragraph -->
<p>Most books about retirement tell you what to do. This one is about who to be.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><em>The Art of Becoming</em> is an honest exploration of what happens when the career ends, the children are grown, and the calendar is suddenly, startlingly, yours. Not a self-help programme — more like a long conversation with someone who's been through it and kept honest notes.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Inside:</p>
<!-- /wp:paragraph -->

<!-- wp:list -->
<ul>
<li>Why identity is the first thing retirement tests — and how to rebuild it on your own terms</li>
<li>The difference between leisure and genuine rest</li>
<li>What joyful living actually looks like when you stop performing it</li>
<li>How to stay curious when the structures that kept you curious are gone</li>
</ul>
<!-- /wp:list -->

<!-- wp:paragraph -->
<p>Written by Farook — retired, living in Vietnam, figuring it out one honest morning at a time.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons">
<!-- wp:button {"className":"btn-primary-amber"} -->
<div class="wp-block-button btn-primary-amber"><a class="wp-block-button__link" href="https://gumroad.com/l/the-art-of-becoming" target="_blank" rel="noopener noreferrer" data-track="gumroad-click">Get the book on Gumroad &rarr;</a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->

<!-- wp:paragraph {"style":{"typography":{"fontSize":"14px"}}} -->
<p style="font-size:14px"><em>Available as a digital download. Secure checkout via Gumroad.</em></p>
<!-- /wp:paragraph -->

</div>
<!-- /wp:column -->

</div>
<!-- /wp:columns -->
CONTENT;

$results['pages'][] = bhrc_create_page('The Art of Becoming', 'the-art-of-becoming', $art_content);

// PAGE 4: Resources
$resources_content = <<<'CONTENT'
<!-- wp:group {"className":"page-hero"} -->
<div class="page-hero">

<!-- wp:heading {"level":1} -->
<h1>Resources</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"page-lead"} -->
<p class="page-lead">Things I actually use and recommend — nothing here for the commission, everything here because it's genuinely good.</p>
<!-- /wp:paragraph -->

</div>
<!-- /wp:group -->

<!-- wp:paragraph -->
<p>I only list things I've used myself or researched carefully. Where a link earns a small commission, I say so. It doesn't change my view — if anything, I hold recommended things to a higher standard because my name is attached to them.</p>
<!-- /wp:paragraph -->

<!-- wp:separator -->
<hr class="wp-block-separator"/>
<!-- /wp:separator -->

<!-- wp:heading {"level":2} -->
<h2>Travel</h2>
<!-- /wp:heading -->

<!-- wp:group {"className":"resource-item"} -->
<div class="resource-item">

<!-- wp:heading {"level":3} -->
<h3>Trip.com — Flights, Hotels, Trains</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Trip.com has become my first stop for travel in Asia — particularly flights and high-speed train bookings across the region. The interface is clear, pricing is competitive, and the customer service has been responsive when I've needed it. For retirees travelling in Southeast and East Asia, it's genuinely worth bookmarking.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><a href="https://www.trip.com" target="_blank" rel="noopener noreferrer sponsored" data-track="affiliate-trip">Browse Trip.com &rarr;</a> <em>(affiliate link — commission at no cost to you)</em></p>
<!-- /wp:paragraph -->

</div>
<!-- /wp:group -->

<!-- wp:group {"className":"resource-item"} -->
<div class="resource-item">

<!-- wp:heading {"level":3} -->
<h3>Booking.com — Accommodation Worldwide</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>For accommodation, Booking.com remains the most reliable option I've found — particularly for finding guesthouses and smaller properties that don't make it onto the big hotel platforms. The free cancellation filtering is worth its weight in gold when plans are uncertain, as they often are in retirement travel.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><a href="https://www.booking.com" target="_blank" rel="noopener noreferrer sponsored" data-track="affiliate-booking">Browse Booking.com &rarr;</a> <em>(affiliate link — commission at no cost to you)</em></p>
<!-- /wp:paragraph -->

</div>
<!-- /wp:group -->

<!-- wp:separator -->
<hr class="wp-block-separator"/>
<!-- /wp:separator -->

<!-- wp:heading {"level":2} -->
<h2>Books — Wellness &amp; Retirement</h2>
<!-- /wp:heading -->

<!-- wp:group {"className":"resource-item"} -->
<div class="resource-item">

<!-- wp:heading {"level":3} -->
<h3>Recommended Reading — Amazon</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>These are the books I return to, recommend in conversation, and find myself pressing into people's hands. Honest accounts of ageing well, finding meaning, and living with intention in the second half. The list grows slowly — I only add books I've actually read and found useful, not books that look right from the cover.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><a href="https://www.amazon.com/shop/behappyretired" target="_blank" rel="noopener noreferrer sponsored" data-track="affiliate-amazon">See the reading list on Amazon &rarr;</a> <em>(affiliate link — commission at no cost to you)</em></p>
<!-- /wp:paragraph -->

</div>
<!-- /wp:group -->

<!-- wp:separator -->
<hr class="wp-block-separator"/>
<!-- /wp:separator -->

<!-- wp:paragraph {"style":{"typography":{"fontSize":"14px"}}} -->
<p style="font-size:14px"><em>Affiliate disclosure: Some links on this page earn a small commission when you use them. This costs you nothing and supports the running of this site. I only recommend things I would recommend anyway.</em></p>
<!-- /wp:paragraph -->
CONTENT;

$results['pages'][] = bhrc_create_page('Resources', 'resources', $resources_content);

// PAGE 5: About (verbatim from approved file)
$about_content = <<<'CONTENT'
<!-- wp:paragraph {"className":"page-intro"} -->
<p class="page-intro">There's a particular quiet that settles in after the career ends, the children are grown, and the calendar no longer fills itself.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>It's not unhappiness. It's something more complicated than that. It's waking up on a Tuesday morning — nowhere to be, no meeting, no deadline — and wondering what, exactly, this season of life is actually for.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>If you've felt that quiet, you're in the right place.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Be Happy Retired exists because I couldn't find what I was looking for. I found plenty of retirement planning advice — investment portfolios, travel bucket lists, fitness programmes for the "young at heart." What I didn't find was someone talking honestly about the <em>inside</em> of retirement. The emotional landscape. The identity questions. The strange mix of freedom and uncertainty that this season brings.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>So I started writing about it.</p>
<!-- /wp:paragraph -->

<!-- wp:separator -->
<hr class="wp-block-separator"/>
<!-- /wp:separator -->

<!-- wp:heading {"level":2} -->
<h2>What You'll Find Here</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>This is a site about three things — because they're the three things I've found matter most.</p>
<!-- /wp:paragraph -->

<!-- wp:group {"className":"pillar-block"} -->
<div class="pillar-block">
<!-- wp:heading {"level":3} -->
<h3>Emotional Wellness</h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>The quiet work of staying connected to yourself. The courage to feel things fully at this stage of life. The practice of honesty — with yourself, with the people you love — without turning it into a project.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"pillar-block"} -->
<div class="pillar-block">
<!-- wp:heading {"level":3} -->
<h3>Joyful Living</h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>Not cheerful. Not performing happiness. The genuine, sometimes surprising discovery that joy is available right now, in this day, in this ordinary moment. You don't have to manufacture it. You have to notice it.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"pillar-block"} -->
<div class="pillar-block">
<!-- wp:heading {"level":3} -->
<h3>Technology for Retirees</h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>Not because I love gadgets — but because the right tools can genuinely add time, connection and ease to later life. I've done the testing. I'll tell you honestly what's worth your attention and what isn't.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:separator -->
<hr class="wp-block-separator"/>
<!-- /wp:separator -->

<!-- wp:heading {"level":2} -->
<h2>What This Is Not</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>This is not a tips blog. I'm not going to give you five steps to a happier retirement and send you on your way.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>I'm going to write about what I'm actually living — the good mornings and the hard ones, the small discoveries and the stubborn confusions — and trust that you'll find something in it that helps. That's the only contract I can honestly make.</p>
<!-- /wp:paragraph -->

<!-- wp:separator -->
<hr class="wp-block-separator"/>
<!-- /wp:separator -->

<!-- wp:paragraph -->
<p><strong>Start here:</strong> <a href="/free-guide">Get the free guide — The 5 Keys to Retirement Happiness &rarr;</a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Or if you'd rather just read first: <a href="/blog">Browse the latest articles &rarr;</a></p>
<!-- /wp:paragraph -->
CONTENT;

$results['pages'][] = bhrc_create_page('About Be Happy Retired', 'about', $about_content);

// PAGE 6: About Farook (verbatim from approved file)
$about_farook_content = <<<'CONTENT'
<!-- wp:paragraph {"className":"page-intro"} -->
<p class="page-intro">I am sixty-nine years old, retired, and living in Vietnam with my wife Hau.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>That sentence still surprises me when I read it back. Sixty-nine. Vietnam. Retired.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>I spent most of my working life in corporate roles that took me across Asia and beyond — the kind of career where you measure your worth in results and your time in flights. I was good at it. I believed in it. And then, like most things, it ended.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Retirement, I assumed, would be easy. I'd earned it. How difficult could it be to simply stop?</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Harder than I expected. And richer, too.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>The first thing I learned was that I had built my identity almost entirely around being useful in a particular way. Take that away, and you spend a fair amount of time staring at the ceiling, wondering who exactly is staring back.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>The second thing I learned was that this is completely normal — and almost nobody talks about it.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>So I started writing. Not advice. Not a programme. Just honest letters from this side of a working life — the confusions and the small discoveries, the mornings that feel like loss and the afternoons that feel, unexpectedly, like grace.</p>
<!-- /wp:paragraph -->

<!-- wp:separator -->
<hr class="wp-block-separator"/>
<!-- /wp:separator -->

<!-- wp:paragraph -->
<p>I live simply. We eat well. I walk most days. I write and I read. I stay curious — about this country, about the people in my life, about what this season still has to teach me.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>I am Singaporean, of Indian, Sri Lankan and Arab heritage. I have lived in Vietnam for over twenty years, and it has shaped me in ways I'm still figuring out.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Be Happy Retired exists because I wanted to write the thing I couldn't find to read. I hope you find something useful in it. Or at least something honest.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>— Farook</p>
<!-- /wp:paragraph -->

<!-- wp:separator -->
<hr class="wp-block-separator"/>
<!-- /wp:separator -->

<!-- wp:paragraph -->
<p><a href="/blog">Read the latest articles &rarr;</a> &nbsp;|&nbsp; <a href="/free-guide">Get the free guide &rarr;</a></p>
<!-- /wp:paragraph -->
CONTENT;

$results['pages'][] = bhrc_create_page('About Farook', 'about-farook', $about_farook_content);

// PAGE 7: Contact
$contact_content = <<<'CONTENT'
<!-- wp:group {"className":"page-hero"} -->
<div class="page-hero">

<!-- wp:heading {"level":1} -->
<h1>Get in touch</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"page-lead"} -->
<p class="page-lead">I read every message. I can't always reply quickly, but I do reply.</p>
<!-- /wp:paragraph -->

</div>
<!-- /wp:group -->

<!-- wp:paragraph -->
<p>Whether you want to share something the writing stirred up, ask a question, or simply say hello — I'd like to hear from you.</p>
<!-- /wp:paragraph -->

<!-- wp:shortcode -->
[contact-form-7 id="contact" title="Contact Form"]
<!-- /wp:shortcode -->

<!-- wp:paragraph {"style":{"typography":{"fontSize":"14px"}}} -->
<p style="font-size:14px"><em>For partnership or collaboration enquiries, please include a brief description of what you have in mind.</em></p>
<!-- /wp:paragraph -->
CONTENT;

$results['pages'][] = bhrc_create_page('Contact', 'contact', $contact_content);

// PAGE 8: Privacy Policy
$privacy_content = <<<'CONTENT'
<!-- wp:heading {"level":1} -->
<h1>Privacy Policy</h1>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><em>Last updated: 24 May 2026</em></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>This Privacy Policy explains how Be Happy Retired (behappyretired.com) collects, uses, and protects your personal information. This site is operated by Farook (the "Site Owner").</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2} -->
<h2>1. Information We Collect</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><strong>Email subscribers:</strong> When you subscribe to receive the free guide or newsletter, we collect your name and email address. This information is stored and managed by Kit.com (formerly ConvertKit). You can unsubscribe at any time using the link in any email.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong>Contact form submissions:</strong> If you contact us via the contact form, we receive your name, email address, and message. These are stored in our WordPress database and are not shared with third parties.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong>Analytics:</strong> We use Google Analytics 4 to understand how visitors use this site. This collects anonymised data including pages viewed, time on site, and general location (country/region). No personally identifiable information is collected via analytics. You can opt out using your browser settings or a Google Analytics opt-out extension.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong>Cookies:</strong> This site uses cookies for analytics (Google Analytics) and standard WordPress functionality. No advertising cookies are used.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2} -->
<h2>2. How We Use Your Information</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul>
<li>To send you the free guide and occasional newsletter (Kit.com email list subscribers)</li>
<li>To respond to your contact form messages</li>
<li>To understand how the site is used and improve its content (analytics)</li>
</ul>
<!-- /wp:list -->

<!-- wp:paragraph -->
<p>We do not sell, rent, or share your personal data with third parties for marketing purposes.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2} -->
<h2>3. Affiliate Links</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>This site contains affiliate links — primarily on the Resources page and within relevant blog posts. When you click an affiliate link and make a purchase, we may earn a small commission at no additional cost to you. Affiliate relationships are always disclosed clearly at the point of the link.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Current affiliate partnerships include: Trip.com, Booking.com, and Amazon Associates.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2} -->
<h2>4. Third-Party Services</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul>
<li><strong>Kit.com</strong> — email list management. <a href="https://kit.com/privacy" target="_blank" rel="noopener noreferrer">Kit.com Privacy Policy</a></li>
<li><strong>Google Analytics 4</strong> — site analytics. <a href="https://policies.google.com/privacy" target="_blank" rel="noopener noreferrer">Google Privacy Policy</a></li>
<li><strong>Gumroad</strong> — digital product sales. <a href="https://gumroad.com/privacy" target="_blank" rel="noopener noreferrer">Gumroad Privacy Policy</a></li>
</ul>
<!-- /wp:list -->

<!-- wp:heading {"level":2} -->
<h2>5. Your Rights</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>You have the right to access, correct, or delete your personal data. To exercise any of these rights, contact us via the <a href="/contact">contact page</a>. Email subscribers can also manage their subscription directly via the unsubscribe link in any email.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2} -->
<h2>6. Changes to This Policy</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>This policy may be updated occasionally. The "last updated" date at the top of this page will reflect any changes. Continued use of the site after changes constitutes acceptance of the updated policy.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2} -->
<h2>7. Contact</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>For any privacy-related questions, please use the <a href="/contact">contact page</a>.</p>
<!-- /wp:paragraph -->
CONTENT;

$results['pages'][] = bhrc_create_page('Privacy Policy', 'privacy-policy', $privacy_content);

// ─────────────────────────────────────────────────────────────
// STEP 4: NAVIGATION MENU
// ─────────────────────────────────────────────────────────────

$menu_name = 'Primary Navigation';
$menu_exists = wp_get_nav_menu_object($menu_name);
if (!$menu_exists) {
    $menu_id = wp_create_nav_menu($menu_name);
} else {
    $menu_id = $menu_exists->term_id;
    // Clear existing items
    $items = wp_get_nav_menu_items($menu_id);
    if ($items) {
        foreach ($items as $item) {
            wp_delete_post($item->ID, true);
        }
    }
}

$menu_items = [
    ['title' => 'Home',                  'url' => home_url('/'),                    'order' => 1],
    ['title' => 'Blog',                  'url' => home_url('/blog'),                'order' => 2],
    ['title' => 'Free Guide',            'url' => home_url('/free-guide'),          'order' => 3],
    ['title' => 'The Art of Becoming',   'url' => home_url('/the-art-of-becoming'), 'order' => 4],
    ['title' => 'Resources',             'url' => home_url('/resources'),           'order' => 5],
    ['title' => 'About',                 'url' => home_url('/about'),               'order' => 6],
    ['title' => 'Contact',               'url' => home_url('/contact'),             'order' => 7],
];

foreach ($menu_items as $item) {
    wp_update_nav_menu_item($menu_id, 0, [
        'menu-item-title'   => $item['title'],
        'menu-item-url'     => $item['url'],
        'menu-item-status'  => 'publish',
        'menu-item-type'    => 'custom',
        'menu-item-position' => $item['order'],
    ]);
}

// Assign menu to primary location
$locations = get_theme_mod('nav_menu_locations');
$locations['primary'] = $menu_id;
set_theme_mod('nav_menu_locations', $locations);

$results['navigation'] = ['menu_id' => $menu_id, 'items' => count($menu_items)];

// ─────────────────────────────────────────────────────────────
// STEP 5: SITE SETTINGS
// ─────────────────────────────────────────────────────────────

// Set homepage
$home_page = get_page_by_path('home', OBJECT, 'page');
if ($home_page) {
    update_option('show_on_front', 'page');
    update_option('page_on_front', $home_page->ID);
}

// Blog page (posts page) - create a blog index page
$blog_page_content = <<<'CONTENT'
<!-- wp:heading {"level":1} -->
<h1>From the blog</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"page-lead"} -->
<p class="page-lead">Writing from the inside of retirement — honest, unhurried, and never a listicle.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2} -->
<h2>Emotional Wellness</h2>
<!-- /wp:heading -->

<!-- wp:query {"query":{"perPage":5,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false,"taxQuery":{"category":[1]}}} -->
<div class="wp-block-query">
<!-- wp:post-template -->
<!-- wp:post-title {"isLink":true} /-->
<!-- wp:post-excerpt {"excerptLength":25} /-->
<!-- /wp:post-template -->
</div>
<!-- /wp:query -->

<!-- wp:heading {"level":2} -->
<h2>Joyful Living</h2>
<!-- /wp:heading -->

<!-- wp:query {"query":{"perPage":5,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false,"taxQuery":{"category":[2]}}} -->
<div class="wp-block-query">
<!-- wp:post-template -->
<!-- wp:post-title {"isLink":true} /-->
<!-- wp:post-excerpt {"excerptLength":25} /-->
<!-- /wp:post-template -->
</div>
<!-- /wp:query -->

<!-- wp:heading {"level":2} -->
<h2>AgeTech</h2>
<!-- /wp:heading -->

<!-- wp:query {"query":{"perPage":5,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false,"taxQuery":{"category":[3]}}} -->
<div class="wp-block-query">
<!-- wp:post-template -->
<!-- wp:post-title {"isLink":true} /-->
<!-- wp:post-excerpt {"excerptLength":25} /-->
<!-- /wp:post-template -->
</div>
<!-- /wp:query -->
CONTENT;

$blog_page_result = bhrc_create_page('Blog', 'blog', $blog_page_content);
$results['pages'][] = $blog_page_result;

$blog_page = get_page_by_path('blog', OBJECT, 'page');
if ($blog_page) {
    update_option('page_for_posts', $blog_page->ID);
}

// Site title and tagline
update_option('blogname', 'Be Happy Retired');
update_option('blogdescription', 'Letters from the other side of a full life');

$results['site_settings'] = 'configured';

// ─────────────────────────────────────────────────────────────
// STEP 6: SIDEBAR WIDGET — Newsletter Opt-in
// ─────────────────────────────────────────────────────────────

// Register newsletter widget in sidebar-1 (default WordPress sidebar)
$newsletter_widget_content = [
    '_multiwidget' => 1,
    2 => [
        'title' => 'Letters worth opening',
        'text'  => '<p>No noise. No schedule. Occasional letters worth the read.</p>' . "\n" . '[bhrc_kit_form]' . "\n" . '<p style="font-size:13px;margin-top:8px;"><em>No spam. Unsubscribe any time.</em></p>',
        'filter' => 'content',
        'visual' => false,
    ],
];

$current_text_widgets = get_option('widget_text');
if (!$current_text_widgets || !is_array($current_text_widgets)) {
    $current_text_widgets = ['_multiwidget' => 1];
}
$current_text_widgets[2] = $newsletter_widget_content[2];
update_option('widget_text', $current_text_widgets);

// Add CTA widget to sidebar
$cta_widget = [
    '_multiwidget' => 1,
    3 => [
        'title' => 'Free Guide',
        'text'  => '<p>Five things I\'ve learned about finding happiness in retirement.</p><p><a href="/free-guide" style="display:inline-block;background:#C4762A;color:#fff;padding:10px 20px;border-radius:3px;text-decoration:none;font-weight:600;">Get it free &rarr;</a></p>',
        'filter' => 'content',
        'visual' => false,
    ],
];
$current_text_widgets[3] = $cta_widget[3];
update_option('widget_text', $current_text_widgets);

// Set sidebar widget order
$sidebar_widgets = get_option('sidebars_widgets');
if (!is_array($sidebar_widgets)) $sidebar_widgets = [];
$sidebar_widgets['sidebar-1'] = ['text-2', 'text-3', 'search-2', 'recent-posts-2'];
update_option('sidebars_widgets', $sidebar_widgets);

$results['widgets'] = 'newsletter and CTA widgets configured in sidebar-1';

// ─────────────────────────────────────────────────────────────
// STEP 7: GA4 TRACKING CODE INJECTION
// ─────────────────────────────────────────────────────────────

// Inject GA4 measurement ID placeholder via wp_head
// The actual Measurement ID (G-XXXXXXXXXX) needs to be inserted once GA4 property is created
$ga4_code = <<<'GACODE'
<!-- BHRC GA4 — BeHappyRetired.com -->
<!-- PLACEHOLDER: Replace G-XXXXXXXXXX with your actual GA4 Measurement ID -->
<!-- To get your ID: analytics.google.com → Admin → Create Property → Web stream → Measurement ID -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-XXXXXXXXXX', {
    'page_title': document.title,
    'page_location': window.location.href
  });

  // Conversion event: Email opt-in form submit
  document.addEventListener('DOMContentLoaded', function() {
    // Kit.com form submissions
    document.querySelectorAll('form.formkit-form, form[data-sv-form]').forEach(function(form) {
      form.addEventListener('submit', function() {
        gtag('event', 'email_signup', {
          'event_category': 'engagement',
          'event_label': 'kit_form',
          'value': 1
        });
      });
    });

    // Gumroad button click
    document.querySelectorAll('a[href*="gumroad.com"], a[data-track="gumroad-click"]').forEach(function(el) {
      el.addEventListener('click', function() {
        gtag('event', 'gumroad_click', {
          'event_category': 'conversion',
          'event_label': el.textContent.trim() || 'gumroad_button',
          'value': 1
        });
      });
    });

    // Affiliate link clicks
    document.querySelectorAll('a[data-track^="affiliate-"]').forEach(function(el) {
      el.addEventListener('click', function() {
        var partner = el.getAttribute('data-track').replace('affiliate-', '');
        gtag('event', 'affiliate_click', {
          'event_category': 'monetisation',
          'event_label': partner,
          'value': 1
        });
      });
    });
  });
</script>
GACODE;

update_option('bhrc_ga4_head_code', $ga4_code);

// Hook the GA4 code into wp_head (store as option, applied by functions.php addition)
$results['ga4'] = 'GA4 code stored — needs Measurement ID. See functions.php step below.';

// ─────────────────────────────────────────────────────────────
// STEP 8: CUSTOM CSS
// ─────────────────────────────────────────────────────────────

$custom_css = <<<'CSS'
/* ================================================
   BHRC — Be Happy Retired Custom CSS
   Design spec: Parchment (#F5EFE0) + Amber (#C4762A)
   Fonts: Cormorant Garamond (headings), Lora (body)
   ================================================ */

/* Google Fonts import */
@import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;1,300;1,400&family=Lora:ital,wght@0,400;0,500;1,400&display=swap');

/* Base */
:root {
    --bhrc-parchment: #F5EFE0;
    --bhrc-amber: #C4762A;
    --bhrc-amber-dark: #a35f1f;
    --bhrc-text: #2C2416;
    --bhrc-text-muted: #6B5D4F;
    --bhrc-border: #DDD3BE;
}

body {
    background-color: var(--bhrc-parchment);
    color: var(--bhrc-text);
    font-family: 'Lora', Georgia, serif;
    font-size: 18px;
    line-height: 1.75;
}

/* Paper grain texture overlay */
body::before {
    content: '';
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='300' height='300'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='300' height='300' filter='url(%23noise)' opacity='0.04'/%3E%3C/svg%3E");
    pointer-events: none;
    z-index: 0;
    opacity: 0.5;
}

/* Headings */
h1, h2, h3, h4, h5, h6 {
    font-family: 'Cormorant Garamond', Georgia, serif;
    font-weight: 500;
    color: var(--bhrc-text);
    line-height: 1.25;
    letter-spacing: -0.01em;
}

h1 { font-size: clamp(2.2rem, 5vw, 3.5rem); }
h2 { font-size: clamp(1.6rem, 3vw, 2.4rem); }
h3 { font-size: clamp(1.2rem, 2.5vw, 1.8rem); }

/* Links */
a {
    color: var(--bhrc-amber);
    text-decoration: underline;
    text-underline-offset: 3px;
}
a:hover { color: var(--bhrc-amber-dark); }

/* Primary amber button */
.btn-primary-amber .wp-block-button__link,
.wp-block-button__link {
    background-color: var(--bhrc-amber);
    color: #fff !important;
    font-family: 'Lora', serif;
    font-size: 16px;
    font-weight: 500;
    padding: 14px 28px;
    border-radius: 3px;
    border: none;
    text-decoration: none;
    letter-spacing: 0.02em;
    transition: background-color 0.2s ease;
}
.btn-primary-amber .wp-block-button__link:hover,
.wp-block-button__link:hover {
    background-color: var(--bhrc-amber-dark);
}

/* Hero section */
.hero-section {
    padding: 80px 20px;
    text-align: center;
    max-width: 800px;
    margin: 0 auto;
}

.hero-heading {
    font-size: clamp(2.5rem, 6vw, 4rem);
    font-weight: 300;
    letter-spacing: -0.02em;
    margin-bottom: 20px;
}

.hero-subheading {
    font-size: clamp(1.1rem, 2vw, 1.3rem);
    color: var(--bhrc-text-muted);
    max-width: 600px;
    margin: 0 auto 32px;
    font-style: italic;
}

/* Page intro/lead */
.page-intro, .page-lead, .post-intro {
    font-size: 1.2rem;
    font-style: italic;
    color: var(--bhrc-text-muted);
    border-left: 3px solid var(--bhrc-amber);
    padding-left: 20px;
    margin-bottom: 32px;
}

/* Section divider */
.wp-block-separator.section-divider {
    border-color: var(--bhrc-border);
    margin: 60px auto;
    max-width: 200px;
}

/* Newsletter section */
.newsletter-section {
    background-color: rgba(196, 118, 42, 0.06);
    border: 1px solid var(--bhrc-border);
    border-radius: 6px;
    padding: 60px 40px;
    text-align: center;
}

/* Guide opt-in box */
.guide-opt-in-box {
    background-color: rgba(196, 118, 42, 0.06);
    border: 1px solid var(--bhrc-border);
    border-radius: 6px;
    padding: 32px;
    position: sticky;
    top: 40px;
}

/* Sidebar */
.widget {
    margin-bottom: 40px;
    padding-bottom: 40px;
    border-bottom: 1px solid var(--bhrc-border);
}
.widget-title {
    font-family: 'Cormorant Garamond', serif;
    font-size: 1.4rem;
    color: var(--bhrc-text);
}

/* Post CTA block */
.post-cta-block {
    background-color: rgba(196, 118, 42, 0.06);
    border: 1px solid var(--bhrc-border);
    border-radius: 4px;
    padding: 24px 28px;
    margin-top: 48px;
}

/* Pillar blocks */
.pillar-block {
    border-left: 3px solid var(--bhrc-amber);
    padding-left: 20px;
    margin-bottom: 32px;
}

/* Resource items */
.resource-item {
    padding: 24px 0;
    border-bottom: 1px solid var(--bhrc-border);
}

/* About snippet */
.about-snippet {
    max-width: 700px;
    margin: 0 auto;
    padding: 40px 20px;
}

/* Blog post cards (Latest Posts block) */
.wp-block-latest-posts__list li {
    padding: 16px 0;
    border-bottom: 1px solid var(--bhrc-border);
    list-style: none;
}
.wp-block-latest-posts__list a {
    font-family: 'Cormorant Garamond', serif;
    font-size: 1.3rem;
    font-weight: 500;
    color: var(--bhrc-text);
    text-decoration: none;
}
.wp-block-latest-posts__list a:hover { color: var(--bhrc-amber); }

/* Navigation */
.wp-block-navigation a {
    font-family: 'Lora', serif;
    font-size: 15px;
    letter-spacing: 0.03em;
    color: var(--bhrc-text);
    text-decoration: none;
}
.wp-block-navigation a:hover { color: var(--bhrc-amber); }

/* Site header */
.wp-site-blocks > header,
.site-header {
    border-bottom: 1px solid var(--bhrc-border);
    padding: 20px 0;
    background-color: var(--bhrc-parchment);
}

/* Footer */
.site-footer {
    border-top: 1px solid var(--bhrc-border);
    padding: 40px 0;
    text-align: center;
    font-size: 14px;
    color: var(--bhrc-text-muted);
}

/* Responsive */
@media (max-width: 768px) {
    body { font-size: 17px; }
    .hero-section { padding: 48px 20px; }
    .newsletter-section { padding: 40px 20px; }
    .guide-opt-in-box { position: static; }
}
CSS;

wp_update_custom_css_post($custom_css);
$results['custom_css'] = 'BHRC design CSS applied (Parchment + Amber + Cormorant Garamond + Lora)';

// ─────────────────────────────────────────────────────────────
// STEP 9: KIT.COM SHORTCODE REGISTRATION
// ─────────────────────────────────────────────────────────────

// NOTE: This registers a placeholder shortcode. When Kit.com form is wired,
// replace the placeholder HTML with the actual Kit.com embed script.
$kit_shortcode_snippet = <<<'PHP'

// BHRC: Kit.com opt-in form shortcode
// When Kit.com is set up, replace the placeholder div below with the actual embed code
// from Kit.com → Forms → Embed → Inline
add_shortcode('bhrc_kit_form', function() {
    // PLACEHOLDER — replace inner HTML with your Kit.com embed script
    return '<div class="bhrc-kit-placeholder" style="background:#f9f4eb;border:2px dashed #C4762A;border-radius:4px;padding:24px;text-align:center;">
        <p style="margin:0 0 12px;font-family:Lora,serif;color:#2C2416;"><strong>Newsletter sign-up form</strong></p>
        <p style="margin:0 0 16px;font-size:14px;color:#6B5D4F;">Kit.com embed goes here — paste your embed code to activate</p>
        <input type="text" placeholder="Your name" style="display:block;width:100%;margin-bottom:8px;padding:10px;border:1px solid #DDD3BE;border-radius:3px;font-family:Lora,serif;" disabled/>
        <input type="email" placeholder="Your email" style="display:block;width:100%;margin-bottom:12px;padding:10px;border:1px solid #DDD3BE;border-radius:3px;font-family:Lora,serif;" disabled/>
        <button style="background:#C4762A;color:#fff;padding:12px 24px;border:none;border-radius:3px;font-family:Lora,serif;cursor:not-allowed;" disabled>Get the free guide</button>
    </div>';
});
PHP;

update_option('bhrc_kit_shortcode_snippet', $kit_shortcode_snippet);
$results['kit_shortcode'] = 'Kit.com shortcode template stored — paste into functions.php';

// ─────────────────────────────────────────────────────────────
// STEP 10: CONFIRMATION PAGE
// ─────────────────────────────────────────────────────────────
if ($framework_only) {
$confirm_content = <<<'CONTENT'
<!-- wp:group {"className":"confirmation-page","style":{"spacing":{"padding":{"top":"80px","bottom":"80px"}}}} -->
<div class="confirmation-page" style="text-align:center;max-width:600px;margin:0 auto;">
<!-- wp:heading {"level":1} -->
<h1>Check your inbox.</h1>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>The guide is on its way. It should arrive within a few minutes — check your spam folder if you don't see it.</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p>The BHRC framework is now in place. Next, you can add your content when ready.</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p><a href="/blog">Visit the blog framework &rarr;</a></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
CONTENT;
} else {

$confirm_content = <<<'CONTENT'
<!-- wp:group {"className":"confirmation-page","style":{"spacing":{"padding":{"top":"80px","bottom":"80px"}}}} -->
<div class="confirmation-page" style="text-align:center;max-width:600px;margin:0 auto;">

<!-- wp:heading {"level":1} -->
<h1>Check your inbox.</h1>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>The guide is on its way. It should arrive within a few minutes — check your spam folder if you don't see it.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>While you wait, you might like to read the first article:</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><a href="/ikigai-purpose-joyful-living">Ikigai — The Japanese Word I Wish I'd Known Before I Retired &rarr;</a></p>
<!-- /wp:paragraph -->

</div>
<!-- /wp:group -->
CONTENT;
}

$results['pages'][] = bhrc_create_page('Thank You — Check Your Inbox', 'thank-you', $confirm_content);

// ─────────────────────────────────────────────────────────────
// OUTPUT RESULTS
// ─────────────────────────────────────────────────────────────

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>BHRC Deployer Results</title>
<style>
body { font-family: monospace; background: #1a1a1a; color: #e0e0e0; padding: 40px; }
h1 { color: #C4762A; }
h2 { color: #aaa; }
.ok { color: #4caf50; }
.warn { color: #ff9800; }
.err { color: #f44336; }
pre { background: #111; padding: 16px; border-radius: 4px; white-space: pre-wrap; }
</style>
</head>
<body>
<h1>BHRC Site Deploy Results</h1>
<p>Run at: <?php echo date('Y-m-d H:i:s'); ?></p>
<p>Deployment mode: <strong><?php echo $results['deployment_mode']; ?></strong></p>
<?php if (!empty($results['posts_skipped'])): ?>
<p class="warn"><?php echo $results['posts_skipped']; ?> Re-run with <code>&content=1</code> when you are ready to load content.</p>
<?php endif; ?>

<h2>Categories</h2>
<?php foreach ($results['categories'] as $cat_id): ?>
<p class="ok">Category ID: <?php echo $cat_id; ?></p>
<?php endforeach; ?>

<h2>Blog Posts</h2>
<?php if (empty($results['posts'])): ?>
<p class="warn">No blog posts were deployed in this run.</p>
<?php endif; ?>
<?php foreach ($results['posts'] as $post): ?>
<p class="<?php echo $post['id'] ? 'ok' : 'err'; ?>">
<?php echo $post['action']; ?>: <?php echo $post['slug']; ?> (ID: <?php echo $post['id']; ?>)
— <a href="<?php echo home_url('/?p=' . $post['id']); ?>" target="_blank" style="color:#C4762A">preview</a>
</p>
<?php endforeach; ?>

<h2>Pages</h2>
<?php foreach ($results['pages'] as $page): ?>
<p class="<?php echo $page['id'] ? 'ok' : 'err'; ?>">
<?php echo $page['action']; ?>: /<?php echo $page['slug']; ?> (ID: <?php echo $page['id']; ?>)
— <a href="<?php echo home_url('/' . $page['slug']); ?>" target="_blank" style="color:#C4762A">view page</a>
| <a href="<?php echo admin_url('post.php?post=' . $page['id'] . '&action=edit'); ?>" target="_blank" style="color:#aaa">edit in WP</a>
</p>
<?php endforeach; ?>

<h2>Navigation</h2>
<p class="ok">Menu "Primary Navigation" created with <?php echo $results['navigation']['items']; ?> items (ID: <?php echo $results['navigation']['menu_id']; ?>)</p>

<h2>Design</h2>
<p class="ok"><?php echo $results['custom_css']; ?></p>

<h2>GA4 Tracking</h2>
<p class="warn"><?php echo $results['ga4']; ?></p>

<h2>Kit.com / Newsletter</h2>
<p class="warn"><?php echo $results['kit_shortcode']; ?></p>

<h2>Next Steps Required</h2>
<ol>
<?php if ($results['deployment_mode'] === 'framework_only'): ?>
<li class="warn"><strong>Content phase:</strong> When the framework is approved, run <code><?php echo home_url('/deploy-bhrc.php?key=' . DEPLOY_KEY . '&content=1'); ?></code> to deploy starter pages copy + initial blog posts.</li>
<?php endif; ?>
<li class="warn"><strong>GA4 Measurement ID:</strong> Create GA4 property at analytics.google.com, get your G-XXXXXXXXXX ID, then install Site Kit by Google plugin OR add the GA4 snippet with real ID to your theme's functions.php</li>
<li class="warn"><strong>Kit.com embed:</strong> Go to Kit.com → Forms → your form → Embed → copy the JavaScript snippet, then add it to functions.php replacing the placeholder in the [bhrc_kit_form] shortcode</li>
<li class="warn"><strong>Add to functions.php:</strong> The Kit.com shortcode snippet (stored in option bhrc_kit_shortcode_snippet) must be added to your active theme's functions.php</li>
<li class="warn"><strong>GA4 head code:</strong> Add action: add_action('wp_head', function(){ echo get_option('bhrc_ga4_head_code'); }, 1); to functions.php</li>
<li class="warn"><strong>Homepage:</strong> In WP Admin → Settings → Reading → set "Your homepage displays" to "A static page" → Homepage: Home, Posts page: Blog</li>
<li class="warn"><strong>Navigation assignment:</strong> In WP Admin → Appearance → Menus → assign "Primary Navigation" to your theme's primary menu location</li>
<li class="warn"><strong>Gumroad URL:</strong> Update /the-art-of-becoming page with real Gumroad product URL</li>
<li class="warn"><strong>DELETE this file</strong> after confirming everything is working</li>
</ol>

<p style="color:#f44336;font-weight:bold;margin-top:40px;">SECURITY: Delete deploy-bhrc.php from your server NOW.</p>
</body>
</html>
<?php
