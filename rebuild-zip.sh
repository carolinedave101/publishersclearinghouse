#!/usr/bin/env bash
set -e
cd "$(dirname "$0")"

if [ "$1" != "--no-npm" ]; then
    echo "→ Building frontend assets..."
    npm run build
fi

echo "→ Rebuilding deploy zip..."
TMPDIR=$(mktemp -d)

# Build into a staging directory
STAGING="$TMPDIR/staging"
mkdir -p "$STAGING"

# Copy public/ contents excluding storage symlink
for item in public/* public/.htaccess; do
    [ -L "$item" ] && continue
    [ -e "$item" ] || continue
    cp -r "$item" "$STAGING/" 2>/dev/null || true
done

cp favicon.png logo.png "$STAGING/" 2>/dev/null || true
cp Trump.csv "Blank 4.csv" winners.csv "$STAGING/" 2>/dev/null || true
cp -r css/ js/ "$STAGING/" 2>/dev/null || true

mkdir -p "$STAGING/pch"
cp -r app bootstrap config database resources routes storage tests vendor \
    artisan composer.json composer.lock package.json phpunit.xml \
    "$STAGING/pch/" 2>/dev/null

cp .env.example "$STAGING/pch/.env" 2>/dev/null || true
cp pch_database.sql "$STAGING/"
cp INSTRUCTIONS.txt "$STAGING/" 2>/dev/null || true

# Remove dev vendor dirs
rm -rf "$STAGING/pch/vendor/phpunit" "$STAGING/pch/vendor/mockery" \
       "$STAGING/pch/vendor/fakerphp" "$STAGING/pch/vendor/nunomaduro" 2>/dev/null || true

# Zip from inside staging so files are at zip root
(cd "$STAGING" && zip -r "$TMPDIR/pch-single-deploy-working.zip" .)
cp "$TMPDIR/pch-single-deploy-working.zip" pch-single-deploy-working.zip

rm -rf "$TMPDIR"

echo "✅ Zip rebuilt: pch-single-deploy-working.zip ($(du -sh pch-single-deploy-working.zip | cut -f1))"
