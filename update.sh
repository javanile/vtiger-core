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
    echo "======[ ${version} ]======"
    git checkout -B "v${version}" "2e7db593df0a9b755489c36db8fb6c706252bf3d"
    git clean -fdx
    echo "-> Downloading..."
    curl -o vtiger.tar.gz -L# "${download_files}${versions[$version]}"
    echo "-> Extracting..."
    tar -xzf vtiger.tar.gz
    cd vtigercrm
    tar -czf ../files.tar.gz .
    cd ..
    tar -xzf files.tar.gz
    echo "-> Cleaning..."
    rm -fr vtigercrm/
    rm vtiger.tar.gz
    rm files.tar.gz
    echo "-> Committing..."
    git add .
    git commit -am "Update version ${version}"
    git pull --no-edit origin blank
    echo "-> Pushing..."
    git push --set-upstream origin "v${version}"
    echo "-> Tagging..."
    git tag -fa "${version}" -m "${version}"
    git push origin --tags
done

git checkout master
git clean -fdx
