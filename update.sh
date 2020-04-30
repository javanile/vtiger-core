#!/bin/bash
set -e

source versions.sh

git config credential.helper cache
git config credential.helper 'cache --timeout=3600'

git add .
git commit -am "Update at $(date)"
git push

for version in "${!versions[@]}"; do
    echo "${version} -> ${versions[$version]}"
done
