#!/bin/bash
# Lighthouse M1 mobile audit driver.
# Runs all 7 routes, mobile preset, all 4 categories, JSON+HTML output.
set -uo pipefail

OUT_DIR="$(dirname "$0")"
CHROME_PATH=/root/.hermes/profiles/mc-qc/home/.cache/ms-playwright/chromium-1223/chrome-linux64/chrome
export CHROME_PATH

ROUTES=(
  "home|/"
  "produk-list|/produk"
  "produk-buku|/produk/10-keajaiban-pikiran"
  "produk-kelas|/produk/kelas-amc-reguler"
  "cart|/cart"
  "checkout|/checkout"
  "checkout-success|/checkout/success/MFP-20260516-ABC123"
)

LABEL="${1:-pre}"

for entry in "${ROUTES[@]}"; do
  name="${entry%%|*}"
  path="${entry##*|}"
  url="http://localhost:3001${path}"
  echo ">>> ${LABEL} ${name} ${url}"
  # Lighthouse 12: mobile is the DEFAULT form-factor (no --preset=mobile exists).
  # Presets available: perf | experimental | desktop. We omit preset for mobile.
  npx --yes lighthouse "$url" \
    --quiet \
    --form-factor=mobile \
    --screenEmulation.mobile=true \
    --only-categories=performance,accessibility,best-practices,seo \
    --output=json --output=html \
    --output-path="${OUT_DIR}/${LABEL}-${name}" \
    --chrome-flags="--headless=new --no-sandbox --disable-dev-shm-usage" \
    --max-wait-for-load=45000 \
    2>&1 | tail -3
done

echo ">>> Done."
