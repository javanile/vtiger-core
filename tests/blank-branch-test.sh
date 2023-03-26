#!/usr/bin/env bash

rm -fr blank
git clone --single-branch --branch blank https://github.com/javanile/vtiger-core.git blank && true

cd blank

blank_hash=$(git rev-parse blank)
blank_hash=0d898121baddf3b3e93acccd4aeba21538f1ab2d

git config credential.helper cache
git config credential.helper 'cache --timeout=3600'
git checkout -B "blank-test" "${blank_hash}"

date > TIMESTAMP
git add .
git commit -am "Blank Branch Test"

git pull --no-rebase
git pull --no-rebase --no-edit -X ours origin "blank-test" && true
git push --set-upstream origin "blank-test"
git push origin --delete "blank-test"
git tag -fa "blank-test" -m "blank-test"
git push origin --tags -f
