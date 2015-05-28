#!/bin/bash

echo -e "\nSetting up GBE environment\n"

json_repo="https://github.com/DemocracyApps/JSON.minify.git"
json_dir="JSON.minify"

if cd /var/www/gbe/vendor; then
    echo "Vendor directory exists"
else
    mkdir /var/www/gbe/vendor
fi

cd /var/www/gbe/vendor
if cd $json_dir; then
    git pull
else
    git clone $json_repo $json_dir
fi

if [ ! -e "/var/www/gbe/.env" ];
    then
  cp /var/www/gbe/.env.example /var/www/gbe/.env
fi



# Install and configure queueing system and supervisor
echo 'Configure and start the queueing system'
sudo sed -i "s/#START=yes/START=yes/" /etc/default/beanstalkd
sudo service beanstalkd start
sudo cp /vagrant/setup/queue.conf /etc/supervisor/conf.d
sudo unlink /var/run/supervisor.sock
sudo service supervisor start
echo 'Running supervisorctl'
sudo supervisorctl reread
sudo supervisorctl add queue
sudo supervisorctl start queue

echo -e "\nFinished\n"
