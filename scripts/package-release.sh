#!/usr/bin/env bash
set -euo pipefail

if [[ $# -ne 1 ]]; then
  echo "Usage: $0 <version>" >&2
  exit 1
fi

VERSION="$1"
ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
DIST_DIR="${ROOT_DIR}/dist"
STAGE_ROOT="${DIST_DIR}/stage"
PACKAGE_DIR="${STAGE_ROOT}/rubicon-maps"
ZIP_PATH="${DIST_DIR}/rubicon-maps-${VERSION}.zip"
CHECKSUM_PATH="${DIST_DIR}/rubicon-maps-${VERSION}.sha256"

mkdir -p "${DIST_DIR}"
rm -rf "${STAGE_ROOT}" "${ZIP_PATH}" "${CHECKSUM_PATH}"
mkdir -p "${PACKAGE_DIR}"

(
  cd "${ROOT_DIR}"
  composer install --no-dev --optimize-autoloader
  (
    cd divi-5/visual-builder
    npm ci
    npm run build
  )
)

rsync -a \
  --exclude='.git/' \
  --exclude='.github/' \
  --exclude='.vscode/' \
  --exclude='.DS_Store' \
  --exclude='dist/' \
  --exclude='docs/' \
  --exclude='node_modules/' \
  --exclude='tests/' \
  --exclude='scripts/' \
  --exclude='divi-5/visual-builder/node_modules/' \
  --exclude='divi-5/visual-builder/package.json' \
  --exclude='divi-5/visual-builder/package-lock.json' \
  --exclude='divi-5/visual-builder/webpack.config.js' \
  "${ROOT_DIR}/" "${PACKAGE_DIR}/"

(
  cd "${STAGE_ROOT}"
  zip -rq "${ZIP_PATH}" "rubicon-maps"
)

shasum -a 256 "${ZIP_PATH}" > "${CHECKSUM_PATH}"

echo "Release package created:"
echo "  ${ZIP_PATH}"
echo "  ${CHECKSUM_PATH}"
