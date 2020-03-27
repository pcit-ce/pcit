set -x

curl -sSL https://github.com/$INPUT_REPO/archive/$INPUT_REF.tar.gz -o $INPUT_REPO.archive.tar.gz

rm -rf archive

mkdir -p archive

tar -zxvf $INPUT_REPO.archive.tar.gz -C archive

dir=`ls archive`

cp -a archive/$dir/. .

pwd

ls -la

rm -rf archive
