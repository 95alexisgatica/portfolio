#!/bin/bash
set -e
SERVER=u922781733@185.173.109.123
PORT=65002
PUB=domains/alexisgatica.dev/public_html

if [ -n "$(git status --porcelain)" ]; then
  echo "Tenés cambios sin commitear. Hacé commit primero."; exit 1
fi

echo "==> Push a GitHub"
git push

echo "==> Compilando assets"
rm -f public/hot
npm run build

echo "==> Subiendo build"
ssh -p $PORT $SERVER "rm -rf $PUB/build.new"
scp -P $PORT -r public/build $SERVER:$PUB/build.new
ssh -p $PORT $SERVER "rm -rf $PUB/build && mv $PUB/build.new $PUB/build"

echo "==> Desplegando en el servidor"
ssh -p $PORT $SERVER 'bash ~/deploy.sh'
