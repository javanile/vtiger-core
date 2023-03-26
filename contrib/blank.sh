#!/usr/bin/env bash

rm -fr blank
git clone --single-branch --branch main https://github.com/javanile/vtiger-core.git blank && true

cd blank

## Create empty "blank" branch
git push origin --delete blank
git checkout --orphan blank
git rm -rf .
git add -A
git commit --allow-empty -m "Initial commit"
git push --set-upstream origin blank

## Create empty "tmp" branch
git push origin --delete tmp
git checkout --orphan tmp
git rm -rf .
git add -A
git commit --allow-empty -m "Initial commit"
git push --set-upstream origin tmp
