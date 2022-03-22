#!/usr/bin/env bash

it checkout yourBranch
git reset $(git merge-base master $(git branch --show-current))
git add -A
git commit -m "one commit on yourBranch"
