echo off

echo %1

cd ../

cd fusion-code

git status

git add -A 

git commit -a -m %1

git push

git status

cd ../

cd fusion-site

git status

git add -A 

git commit -a -m %1

git push

git status

cd ../

cd fusion-plugin

git status

git add -A 

git commit -a -m %1

git push

git status

cd ../

cd fusion-theme

git status

git add -A 

git commit -a -m %1

git push

git status

cd ../

exit
