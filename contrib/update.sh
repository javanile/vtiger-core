#!/bin/bash
set -e

update_tag=$1
[ -z "$1" ] && update_message="Update all tags" || update_message="Updating tag $1"

git add .
git commit -am "$update_message" && true
git push

source versions.sh

rm -fr build
mkdir -p cache/download cache/package
git clone --branch blank https://github.com/javanile/vtiger-core.git build && true

build_tag () {
  local version=$1
  local archive=$2
  local is_zip=
  local download_archive=cache/download/${version}.tar.gz
  local package_archive=cache/package/${version}.tar.gz
  local tmp_dir=tmp/$version
  local blank_hash=$(git rev-parse blank)

  if [[ "$archive" == *zip ]]; then
    is_zip=1
    cache_archive=cache/${version}.zip
  fi

  echo "======[ ${version} ]======"

  echo "-> Downloading..."
  if [[ ! -f "${download_archive}" ]]; then
    curl -o "${download_archive}" -L# "${download_url}${archive}"
  fi

  echo "-> Extracting..."
  if [[ ! -f "${package_archive}" ]]; then
    [ -d "$tmp_dir" ] && rm -fr "$tmp_dir"
    mkdir -p "$tmp_dir"
    cd "$tmp_dir"
    if [ -n "${is_zip}" ]; then
      unzip -q -o "../../${download_archive}"
    else
      tar -xzf "../cache/${version}.tar.gz"
    fi
    cd vtigercrm || cd vtigerCRM
    tar -czf ../../../${package_archive} .
    cd ../../..
    exit
  fi

  cd build
  git config credential.helper cache
  git config credential.helper 'cache --timeout=3600'
  git fetch "${blank_hash}"
  git checkout -B "v${version}" "${blank_hash}"
  tar -xzf ${package_archive}

  exit

  echo "-> Cleaning..."
  rm -fr vtigercrm/ vtigerCRM/
  rm files.tar.gz
  echo "-> Updating..."
  cp ../composer.json.tpl ./composer.json
  sed -e 's!%VERSION%!'"${version}"'!g' -ri composer.json
  echo "-> Committing..."
  git add .
  git commit -am "Update version ${version}"
  echo "-> Pushing..."
  git pull --no-edit -X ours origin "v${version}" && true
  git push --set-upstream origin "v${version}"
  git push origin --delete "v${version}"
  echo "-> Tagging..."
  git tag -fa "${version}" -m "${version}"
  git push origin --tags -f
  cd ..
}

for version in "${versions[@]}"; do
  tag="${version%%=*}"
  archive="${version##*=}"
  echo $tag
  if [ -z "$update_tag" ] || [ "$update_tag" = "$tag" ]; then
    build_tag $tag $archive
  fi
done
