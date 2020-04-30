#!/bin/bash
set -e

source versions.sh

git add .
git commit -am "Update at $(date)"
git push

for version in "${!versions[@]}"; do
    echo "${version} -> ${versions[$version]}"
done
