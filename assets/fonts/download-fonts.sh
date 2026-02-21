#!/usr/bin/env bash
# ================================================================
# Hirsch Mägerkingen — Download self-hosted web fonts
# Source: Google Web Fonts Helper (gwfh.mranftl.com)
# Run from the theme root:  bash assets/fonts/download-fonts.sh
# ================================================================

set -euo pipefail

DEST="$(dirname "$0")"
echo "Downloading fonts to: $DEST"

# ── Playfair Display 700 ─────────────────────────────────────
echo "→ Playfair Display 700..."
curl -fsSL \
  "https://gwfh.mranftl.com/api/fonts/playfair-display?download=zip&subsets=latin&variants=700&formats=woff2" \
  -o /tmp/playfair.zip
unzip -p /tmp/playfair.zip "*.woff2" > "$DEST/playfair-display-v36-latin-700.woff2" 2>/dev/null || \
  unzip /tmp/playfair.zip -d /tmp/playfair_tmp && \
  find /tmp/playfair_tmp -name "*700*woff2" -exec cp {} "$DEST/playfair-display-v36-latin-700.woff2" \;
rm -f /tmp/playfair.zip
echo "   ✓ playfair-display-v36-latin-700.woff2"

# ── Lato Regular ─────────────────────────────────────────────
echo "→ Lato Regular..."
curl -fsSL \
  "https://gwfh.mranftl.com/api/fonts/lato?download=zip&subsets=latin&variants=regular&formats=woff2" \
  -o /tmp/lato_regular.zip
unzip /tmp/lato_regular.zip -d /tmp/lato_regular_tmp
find /tmp/lato_regular_tmp -name "*regular*woff2" -exec cp {} "$DEST/lato-v24-latin-regular.woff2" \;
rm -rf /tmp/lato_regular.zip /tmp/lato_regular_tmp
echo "   ✓ lato-v24-latin-regular.woff2"

# ── Lato 700 ─────────────────────────────────────────────────
echo "→ Lato 700..."
curl -fsSL \
  "https://gwfh.mranftl.com/api/fonts/lato?download=zip&subsets=latin&variants=700&formats=woff2" \
  -o /tmp/lato_700.zip
unzip /tmp/lato_700.zip -d /tmp/lato_700_tmp
find /tmp/lato_700_tmp -name "*700*woff2" -exec cp {} "$DEST/lato-v24-latin-700.woff2" \;
rm -rf /tmp/lato_700.zip /tmp/lato_700_tmp
echo "   ✓ lato-v24-latin-700.woff2"

echo ""
echo "All fonts downloaded successfully!"
ls -lh "$DEST"/*.woff2
