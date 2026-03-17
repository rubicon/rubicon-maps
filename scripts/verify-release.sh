#!/usr/bin/env bash
set -euo pipefail

if [[ $# -ne 1 ]]; then
  echo "Usage: $0 <version>" >&2
  exit 1
fi

VERSION="$1"
ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
ZIP_PATH="${ROOT_DIR}/dist/rubicon-maps-${VERSION}.zip"
CHECKSUM_PATH="${ROOT_DIR}/dist/rubicon-maps-${VERSION}.sha256"

if [[ ! -f "${ZIP_PATH}" ]]; then
  echo "Missing release zip: ${ZIP_PATH}" >&2
  exit 1
fi

if [[ ! -f "${CHECKSUM_PATH}" ]]; then
  echo "Missing checksum file: ${CHECKSUM_PATH}" >&2
  exit 1
fi

TMP_DIR="$(mktemp -d)"
trap 'rm -rf "${TMP_DIR}"' EXIT

shasum -a 256 -c "${CHECKSUM_PATH}"
unzip -q "${ZIP_PATH}" -d "${TMP_DIR}"

test -f "${TMP_DIR}/rubicon-maps/rubicon-maps.php"
test -f "${TMP_DIR}/rubicon-maps/vendor/autoload.php"
test -f "${TMP_DIR}/rubicon-maps/divi-5/visual-builder/build/rubicon-maps-divi5.js"

echo "Release artifact verified: ${ZIP_PATH}"
