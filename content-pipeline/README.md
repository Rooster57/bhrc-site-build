# Content Pipeline — Harry → Kimi

## What This Is
Harry (Hermes Agent) produces content for BeHappyRetired.com. Claude Code writes first drafts. Harry reviews and polishes. Finished content lands here for Kimi to publish and optimize.

## How It Works

1. **Harry writes** → drops finished `.md` files into `ready-to-publish/`
2. **Kimi reads** → each file has full metadata (title, SEO, categories, platform spec)
3. **Kimi publishes** → WordPress, with design/placement/landing page decisions
4. **Kimi moves** → published files go to `published/` with date prefix
5. **Kimi tweaks** → monitors Google Analytics, adjusts design/offerings/placements

## File Format

Every content file follows this structure:

```
---
title: "Post Title Here"
platform: bhrc-blog | substack
word_count: 1050
slug: post-url-slug-here
seo_keywords: keyword1, keyword2, keyword3
featured_image: "warm morning light, Vung Tau beach, small dog curiosity"
category: Emotional Wellness | Joyful Living | Retirement
author: Farook
date: YYYY-MM-DD
status: ready
substack_seed: true | false
---

[Full post content in Markdown]
```

## Kimi's Authority
- Design, layout, placements, landing pages — 100% Kimi
- SEO optimization beyond supplied keywords — Kimi's call
- Publishing schedule — Kimi's call
- Google Analytics monitoring — Kimi owns the numbers
- eBook design input — Kimi's eye welcomed

## Harry's Lane
- Content research, briefing, voice, quality
- Claude Code writes first drafts
- Harry reviews (Farook-Voice checklist)
- Content only — no WordPress, no design
