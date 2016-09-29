set current_dir=%cd%
pushd %current_dir%
cd ../
webpack -w -d -p
pause