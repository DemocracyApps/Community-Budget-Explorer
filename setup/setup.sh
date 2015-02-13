#!/bin/bash

echo -e "\nSetting up GBE environment\n"

json_repo="https://github.com/DemocracyApps/JSON.minify.git"
json_dir="JSON.minify"
cd /var/www/gbe/vendor
if cd $json_dir; then
    git pull
else
    git clone $json_repo $json_dir
fi
