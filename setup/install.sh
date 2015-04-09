#!/bin/bash
echo "Starting Provision"

# PRC Start
# Review arguments
if [ "$1" != "" ]; then
	echo "Args : $1"
	www_user=$1
else
	echo "No args"
	www_user="vagrant"
fi

echo "Apache User: $www_user"
# End PRC 

sudo apt-get update

sudo debconf-set-selections <<< 'mysql-server mysql-server/root_password password root'
sudo debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password root'

sudo apt-get install -y vim curl python-software-properties
sudo add-apt-repository -y ppa:ondrej/php5
sudo apt-get update

sudo apt-get install -y php5 apache2 libapache2-mod-php5 php5-curl php5-gd php5-mcrypt php5-readline mysql-server-5.5 php5-mysql git-core php5-xdebug

cat << EOF | sudo tee -a /etc/php5/mods-available/xdebug.ini
xdebug.scream=1
xdebug.cli_color=1
xdebug.show_local_vars=1
EOF

sudo a2enmod rewrite

sed -i "s/error_reporting = .*/error_reporting = E_ALL/" /etc/php5/apache2/php.ini
sed -i "s/display_errors = .*/display_errors = On/" /etc/php5/apache2/php.ini
sed -i "s/disable_functions = .*/disable_functions = /" /etc/php5/cli/php.ini

sudo service apache2 restart

curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Above is from original install.sh by Jeffrey Way. 
# The following complete the set-up for a gbe test/dev machine.

sudo add-apt-repository -y ppa:ubuntugis/ubuntugis-unstable
sudo apt-get update

sudo rm -rf /var/www/html
sudo chmod -R 777 /var/www/gbe/storage

# Change both APACHE_RUN_USER and APACHE_RUN_GROUP to vagrant from www-data to avoid getting frequent permission-denied
# problems with the Laravel storage subdirectory

# PRC Start
# On DigitalOcean this caused issues, as there is no vagrant user, so we make this configurable
if [ "$www_user" != "" ]; then
	echo "Editing Apache User" 
	sed -i "s/www-data/$www_user/" /etc/apache2/envvars
fi
# End PRC

if [ ! -e "/etc/apache2/sites-available/gbe.conf" ];
    then

    # PRC Revise Host Here
    sudo cp /var/www/setup/gbe.conf /etc/apache2/sites-available
    sudo a2ensite gbe
    sudo service apache2 reload
fi

sudo apt-get install -y postgresql-client-common postgresql postgresql-contrib php5-pgsql php5-dev
sudo apt-get install -y postgis postgresql-server-dev-9.1 postgresql-9.1-postgis
sudo apt-get install -y postgresql-9.1-postgis-scripts

#
# In a production machine these should be limited more than here.
#
sudo sed -i "s/#listen_addresses = 'localhost'/listen_addresses='*'/" /etc/postgresql/9.1/main/postgresql.conf
sudo sed -i "\
99a\
host	all	ga	0.0.0.0/0	md5" /etc/postgresql/9.1/main/pg_hba.conf

sudo apt-get install -y beanstalkd supervisor

# Install and configure postgres and postgis
echo 'Setting up database and GIS extensions'
#sudo su postgres -c 'psql -c "create user vagrant with CREATEDB PASSWORD vagrant;"'
# sudo su postgres -c 'createuser -d -R -S vagrant'
# sudo su postgres -c 'psql -c "ALTER USER vagrant WITH PASSWORD vagrant;"'
sudo su postgres -c '/var/www/setup/create_postgres_users.sh'
sudo su postgres -c 'createdb gbe'
sudo su postgres -c 'psql -d gbe -c "CREATE EXTENSION postgis;"'
sudo su postgres -c 'psql -d gbe -c "CREATE EXTENSION postgis_topology;"'

# PRC Start
# Disable the default site, since we're now serving up gbe for all Vhosts or direct IP Access
sudo a2dissite 000-default

# Update the permissions on the web root to match the apache user
sudo chown $www_user:$www_user -R /var/www/gbe
# End PRC

sudo service apache2 restart  # Needed to load pgsql driver.

sudo apt-get install -y default-jdk

# Now let's get Node
cd /var/www
curl -sL https://deb.nodesource.com/setup | sudo bash -
sudo apt-get install -y nodejs
sudo npm install -g npm@next
sudo npm install -g bower
sudo npm install -g gulp


#sudo apt-get -y purge nodejs npm
#sudo apt-get -y install python-software-properties
#sudo apt-get -y autoremove
#sudo apt-add-repository -y ppa:chris-lea/node.js
#sudo apt-get -y update
#sudo apt-get -y install nodejs
#sudo npm install -g gulp
#sudo npm install -g bower
#sudo npm install -g react


