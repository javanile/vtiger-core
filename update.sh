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
    git checkout -B "${version}" "4d2e80d4c82a84fecb6293b9df8589e04f08d9db"
    git clean -fdx
    echo "${version} -> ${versions[$version]}"
    echo ${versions[$version]} > SOURCE
    curl -o vtiger.tar.gz -sL "${versions[$version]}"
    tar -xzf vtiger.tar.gz
    cd vtigercrm
    tar -cvzf files.tar.gz .
    cd ..
    tar -xzf vtigercrm/files.tar.gz
    rm -fr vtigercrm vtiger.tar.gz
    git add .
    git commit -am "Update version ${version}"
    git pull --no-edit
    git push --set-upstream origin "${version}"
done

git checkout master
git clean -fdx
