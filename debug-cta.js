// Deep CTA diagnostic
const { chromium } = require('playwright');
(async () => {
  const browser = await chromium.launch({ headless: true });
  const page = await browser.newPage({ viewport: { width: 1280, height: 900 } });
  await page.goto('https://behappyretired.com', { timeout: 20000, waitUntil: 'domcontentloaded' });

  const result = await page.evaluate(() => {
    const cta = document.querySelector('li.nav-cta');
    if (!cta) return { error: 'CTA not found' };

    const a = cta.querySelector('a');
    const label = cta.querySelector('.wp-block-navigation-item__label');

    // Get ALL matched CSS rules for the anchor element
    const matchedRules = [];
    if (a) {
      for (const sheet of document.styleSheets) {
        try {
          for (const rule of sheet.cssRules) {
            if (rule.selectorText) {
              try {
                if (a.matches(rule.selectorText)) {
                  matchedRules.push({
                    selector: rule.selectorText.slice(0, 100),
                    bg: rule.style.backgroundColor,
                    color: rule.style.color,
                    priority: rule.style.getPropertyPriority('background-color'),
                    parentSheet: (sheet.href || 'inline').slice(-50)
                  });
                }
              } catch(e) { /* selector not valid for matches() */ }
            }
          }
        } catch(e) { /* CORS */ }
      }
    }

    // Check stylesheets for nav-cta rules specifically
    const navCtaRules = [];
    for (const sheet of document.styleSheets) {
      try {
        for (const rule of sheet.cssRules) {
          if (rule.selectorText && rule.selectorText.includes('nav-cta')) {
            navCtaRules.push({
              selector: rule.selectorText,
              bg: rule.style.backgroundColor,
              color: rule.style.color,
              cssText: rule.cssText.slice(0, 200)
            });
          }
        }
      } catch(e) {}
    }

    return {
      ctaHTML: cta.outerHTML.slice(0, 300),
      a_computed_bg: a ? getComputedStyle(a).backgroundColor : 'no a',
      a_computed_color: a ? getComputedStyle(a).color : 'no a',
      matchedRules: matchedRules.slice(0, 12),
      navCtaRules: navCtaRules,
      globalStyleInline: document.querySelector('#global-styles-inline-css') ? 'present' : 'missing'
    };
  });

  console.log(JSON.stringify(result, null, 2));
  await browser.close();
})();
