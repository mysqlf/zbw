set current_dir=%cd%
pushd %current_dir%
cd ../
webpack --config yasuo.config.js
pause