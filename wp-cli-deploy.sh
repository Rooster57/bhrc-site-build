#!/bin/bash
# ─────────────────────────────────────────────────────────────
# BHRC WordPress WP-CLI Deployment Script
# Run via SSH on Bluehost:
#   ssh your-bluehost-user@behappyretired.com
#   cd public_html
#   bash wp-cli-deploy.sh
#
# Requires WP-CLI installed (Bluehost has it at /usr/local/bin/wp)
# ─────────────────────────────────────────────────────────────

WP="wp --allow-root"
SITE_URL="https://behappyretired.com"

echo "=== BHRC Site Deploy via WP-CLI ==="
echo "Site: $SITE_URL"
echo ""

# ── Verify WP is loaded ──
$WP core version || { echo "ERROR: WP-CLI not found or WP not installed"; exit 1; }

# ── Plugin installations ──
echo "--- Installing plugins ---"
$WP plugin install contact-form-7 --activate
$WP plugin install google-site-kit --activate
$WP plugin install classic-widgets --activate

# ── Set site title/tagline ──
echo "--- Site settings ---"
$WP option update blogname "Be Happy Retired"
$WP option update blogdescription "Letters from the other side of a full life"
$WP option update permalink_structure "/%postname%/"

# ── Categories ──
echo "--- Categories ---"
$WP term create category "Emotional Wellness" --slug="emotional-wellness" --description="Honest writing about the inner landscape of retirement."
$WP term create category "Joyful Living" --slug="joyful-living" --description="Finding genuine joy in ordinary moments."
$WP term create category "AgeTech" --slug="agetech" --description="Technology that adds time, connection, and ease to later life."

# ── Upload deploy script ──
echo ""
echo "--- Upload deploy-bhrc.php to site root, then run: ---"
echo "    $SITE_URL/deploy-bhrc.php?key=BHRC2026"
echo ""
echo "--- After deploying, add to functions.php: ---"
echo "    cat functions-additions.php >> wp-content/themes/\$($WP theme list --status=active --field=name)/functions.php"
echo ""

# ── Flush rewrite rules ──
$WP rewrite flush

echo "=== WP-CLI setup complete ==="
echo ""
echo "NEXT: Run the deployer at $SITE_URL/deploy-bhrc.php?key=BHRC2026"
echo "THEN: Add functions-additions.php contents to your theme's functions.php"
echo "THEN: Create GA4 property and replace G-XXXXXXXXXX"
echo "THEN: Wire Kit.com and replace [bhrc_kit_form] placeholder"
echo "THEN: DELETE deploy-bhrc.php from server"
