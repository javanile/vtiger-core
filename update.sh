#!/bin/bash
set -e

source versions.sh

git config credential.helper cache
git config credential.helper 'cache --timeout=3600'

date > RELEASE

git add .
git commit -am "Update at $(date)"
git push

for version in "${!versions[@]}"; do
    git checkout -B ${version}
    git clean -fdx
    echo "${version} -> ${versions[$version]}"
    echo ${versions[$version]} > SOURCE
    git commit -am "Update at $(date)"
    git push
done
