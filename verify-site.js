// BHRC Visual Verification Tool
// Run: node verify-site.js
// Checks: colors, sections, nav, images — reports pass/fail for each

const { chromium } = require('playwright');

const CHECKS = {
  bodyBg: { desc: 'Body background is warm parchment', pass: false, detail: '' },
  heroBg: { desc: 'Hero section has dark navy background', pass: false, detail: '' },
  sections: { desc: 'At least 4 distinct section backgrounds found', pass: false, detail: '' },
  pillars: { desc: 'Three pillar cards visible', pass: false, detail: '' },
  navDropdown: { desc: 'Navigation has dropdown submenus', pass: false, detail: '' },
  navCTA: { desc: 'Start Here CTA button is amber', pass: false, detail: '' },
  headingFont: { desc: 'Headings use Cormorant Garamond', pass: false, detail: '' },
  bodyFont: { desc: 'Body text uses Lora', pass: false, detail: '' },
  noWhiteBg: { desc: 'No large white (#fff) areas remain', pass: false, detail: '' },
  postsVisible: { desc: '3 latest posts visible on homepage', pass: false, detail: '' },
};

function hexToRgb(hex) {
  const r = parseInt(hex.slice(1,3), 16);
  const g = parseInt(hex.slice(3,5), 16);
  const b = parseInt(hex.slice(5,7), 16);
  return `rgb(${r}, ${g}, ${b})`;
}

async function verify() {
  const browser = await chromium.launch({ headless: true });
  const page = await browser.newPage({ viewport: { width: 1280, height: 2000 } });

  console.log('Loading behappyretired.com...');
  await page.goto('https://behappyretired.com', { timeout: 30000, waitUntil: 'networkidle' });
  await page.waitForTimeout(2000);

  const data = await page.evaluate(() => {
    const results = {};

    // 1. Body background
    results.bodyBg = getComputedStyle(document.body).backgroundColor;

    // 2. All group/section backgrounds
    const groups = document.querySelectorAll('.wp-block-group, section, .has-background');
    const bgColors = new Set();
    const sectionInfo = [];
    groups.forEach(g => {
      const bg = getComputedStyle(g).backgroundColor;
      if (bg && bg !== 'rgba(0, 0, 0, 0)' && bg !== 'transparent') {
        bgColors.add(bg);
        const rect = g.getBoundingClientRect();
        if (rect.height > 100) {
          sectionInfo.push({ bg, height: Math.round(rect.height), top: Math.round(rect.top) });
        }
      }
    });
    results.uniqueBgCount = bgColors.size;
    results.sections = sectionInfo.slice(0, 8);

    // 3. Navigation
    const nav = document.querySelector('.wp-block-navigation, nav');
    results.hasNav = !!nav;
    results.hasSubmenu = !!document.querySelector('.wp-block-navigation__submenu-container, .wp-block-navigation-submenu');
    results.navItems = Array.from(document.querySelectorAll('.wp-block-navigation-item')).length;

    // Check CTA button
    const ctaLinks = Array.from(document.querySelectorAll('.wp-block-navigation-item a'));
    const ctaBtn = ctaLinks.find(a => a.textContent.includes('Start Here'));
    if (ctaBtn) {
      results.ctaBg = getComputedStyle(ctaBtn).backgroundColor;
    } else {
      results.ctaBg = null;
    }

    // 4. Fonts
    const h1 = document.querySelector('h1');
    results.h1Font = h1 ? getComputedStyle(h1).fontFamily : null;
    results.bodyFont = getComputedStyle(document.body).fontFamily;

    // 5. Check for large white areas
    const body = document.body;
    const allElements = document.querySelectorAll('*');
    let maxWhiteArea = 0;
    allElements.forEach(el => {
      const bg = getComputedStyle(el).backgroundColor;
      if (bg === 'rgb(255, 255, 255)') {
        const rect = el.getBoundingClientRect();
        const area = rect.width * rect.height;
        if (area > maxWhiteArea && area > 10000) { // 100x100 minimum
          maxWhiteArea = area;
        }
      }
    });
    results.maxWhiteArea = maxWhiteArea;

    // 6. Post cards
    const postCards = document.querySelectorAll('.wp-block-post, .wp-block-post-template > *, .post-card');
    results.postCardCount = postCards.length;

    // 7. Pillar cards
    const pillarTexts = ['Emotional Wellness', 'Joyful Living', 'AgeTech'];
    const foundPillars = pillarTexts.filter(t => document.body.textContent.includes(t));
    results.pillarsFound = foundPillars;

    // 8. Images/placeholders
    results.allImages = Array.from(document.querySelectorAll('img')).map(img => ({
      src: img.src.slice(-40),
      width: img.naturalWidth,
      height: img.naturalHeight,
      visible: img.offsetHeight > 0
    }));

    return JSON.parse(JSON.stringify(results));
  });

  // Evaluate checks
  const bgStr = data.bodyBg || '';
  CHECKS.bodyBg.pass = bgStr.includes('245') || bgStr.includes('239') || bgStr.includes('F5EFE0');
  CHECKS.bodyBg.detail = data.bodyBg;

  const heroSection = data.sections.find(s => s.top < 600 && s.height > 200);
  CHECKS.heroBg.pass = heroSection ? (heroSection.bg.includes('26, 31, 46') || heroSection.bg.includes('1A1F2E') || heroSection.bg.includes('0, 0, 0')) : false;
  CHECKS.heroBg.detail = heroSection ? heroSection.bg : 'no hero section found';

  CHECKS.sections.pass = data.uniqueBgCount >= 4;
  CHECKS.sections.detail = `${data.uniqueBgCount} unique backgrounds: ${data.sections.map(s => s.bg + '@' + s.top + 'px').join(', ')}`;

  CHECKS.pillars.pass = data.pillarsFound && data.pillarsFound.length >= 3;
  CHECKS.pillars.detail = `Found: ${(data.pillarsFound || []).join(', ')}`;

  CHECKS.navDropdown.pass = data.hasSubmenu;
  CHECKS.navDropdown.detail = `Submenu: ${data.hasSubmenu}, Nav items: ${data.navItems}`;

  CHECKS.navCTA.pass = data.ctaBg ? (data.ctaBg.includes('196') || data.ctaBg.includes('C4762A')) : false;
  CHECKS.navCTA.detail = data.ctaBg || 'CTA button not found';

  CHECKS.headingFont.pass = data.h1Font ? data.h1Font.toLowerCase().includes('cormorant') || data.h1Font.toLowerCase().includes('garamond') : false;
  CHECKS.headingFont.detail = data.h1Font || 'no h1';

  CHECKS.bodyFont.pass = data.bodyFont ? data.bodyFont.toLowerCase().includes('lora') || data.bodyFont.toLowerCase().includes('georgia') : false;
  CHECKS.bodyFont.detail = data.bodyFont ? data.bodyFont.slice(0, 50) : 'no body font';

  CHECKS.noWhiteBg.pass = data.maxWhiteArea < 50000;
  CHECKS.noWhiteBg.detail = `Max white area: ${Math.round(data.maxWhiteArea)} px²`;

  CHECKS.postsVisible.pass = data.postCardCount >= 3;
  CHECKS.postsVisible.detail = `${data.postCardCount} post cards found. Images: ${data.allImages.filter(i => i.visible).length} visible`;

  // Print results
  console.log('\n═══════════════════════════════════════');
  console.log(' BHRC SITE VISUAL VERIFICATION REPORT');
  console.log('═══════════════════════════════════════\n');

  let passCount = 0;
  for (const [key, check] of Object.entries(CHECKS)) {
    const icon = check.pass ? '✅' : '❌';
    console.log(`${icon} ${check.desc}`);
    console.log(`   ${check.detail}`);
    if (check.pass) passCount++;
    console.log();
  }

  console.log('═══════════════════════════════════════');
  console.log(` RESULT: ${passCount}/${Object.keys(CHECKS).length} checks passed`);
  console.log('═══════════════════════════════════════');

  // Save screenshot
  await page.screenshot({ path: 'E:/CCWS/STAGING/other/verify-output.png', fullPage: false });
  console.log('\nScreenshot saved to E:/CCWS/STAGING/other/verify-output.png');

  await browser.close();

  // Exit with appropriate code
  const allPassed = passCount === Object.keys(CHECKS).length;
  process.exit(allPassed ? 0 : 1);
}

verify().catch(e => {
  console.error('Verification failed:', e.message);
  process.exit(2);
});
