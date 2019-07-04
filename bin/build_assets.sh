#!/usr/bin/env bash

# get from repository, clone, download and build paths

# 1. move to new design
cd new-design

# 2. install dependencies
# might be needed: "apt-get install libpng-dev"
#sudo apt-get install libpng-dev

#npm install --no-optional # due to bug on Xubuntu: https://stackoverflow.com/a/37645484/1348344

# 3. generate app.css + app.js files
npm run production

# 4. get back to the root directory
cd ..

# 5. copy build assets to "/public" in Symfony application
cp new-design/dist/app.css public/assets/css/app.css
cp new-design/dist/app.js public/assets/js/app.js
cp -rf new-design/src/icons public/assets
cp -rf new-design/src/images public/assets
