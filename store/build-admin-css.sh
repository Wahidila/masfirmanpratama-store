#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")"
./tools/tailwindcss-v4 -i resources/css/admin.css -o public/admin/admin.css --minify "$@"
