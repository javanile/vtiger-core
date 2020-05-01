#!/bin/bash
set -e

source versions.sh

mkdir -p cache
git clone https://github.com/javanile/vtiger-core.git build && true

for version in "${!versions[@]}"; do
  echo "======[ ${version} ]======"
  echo "-> Downloading..."
  if [[ ! -f "cache/${version}.tar.gz" ]]; then
    curl -o "cache/${version}.tar.gz" -L# "${download_files}${versions[$version]}"
  fi
  cd build
  git config credential.helper cache
  git config credential.helper 'cache --timeout=3600'
  git checkout -B "v${version}" "2e7db593df0a9b755489c36db8fb6c706252bf3d"
  git clean -fdx
  echo "-> Extracting..."
  tar -xzf "../cache/${version}.tar.gz"
  cd vtigercrm
  tar -czf ../files.tar.gz .
  cd ..
  tar -xzf files.tar.gz
  echo "-> Cleaning..."
  rm -fr vtigercrm/
  rm files.tar.gz
  echo "-> Updating..."
  cp ../composer.json.tpl ./composer.json
  sed -e 's!%VERSION%!'"${version}"'!' -ri composer.json
  echo "-> Committing..."
  git add .
  git commit -am "Update version ${version}"
  echo "-> Pushing..."
  git pull --no-edit origin "v${version}" && true
  git push --set-upstream origin "v${version}"
  echo "-> Tagging..."
  git tag -fa "${version}" -m "${version}"
  git push origin --tags -f
  cd ..
done
