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
    git checkout -B "${version}" "372a78b32bc53ca813fa4e6a51e64473bc42aa46"
    git clean -fdx
    echo "${version} -> ${versions[$version]}"
    echo ${versions[$version]} > SOURCE
    git add .
    git commit -am "Update version ${version}"
    git push --set-upstream origin "${version}"
done

git checkout master
git clean -fdx
