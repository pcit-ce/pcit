set -x

curl -sSL https://github.com/$INPUT_REPO/archive/$INPUT_REF.tar.gz -o archive.tar.gz

rm -rf archive

mkdir -p archive

tar -zxvf archive.tar.gz -C archive

dir=`ls archive`

cp -a archive/$dir/* .

pwd

ls -la
