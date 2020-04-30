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
    git checkout -B "${version}" "asd"
    git clean -fdx
    echo "${version} -> ${versions[$version]}"
    echo ${versions[$version]} > SOURCE
    git add .
    git commit -am "Update version ${version}"
    git push --set-upstream origin "${version}"
done

git checkout master
git clean -fdx
