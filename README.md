# Government Budget Explorer

_Don't tell me what you value, show me your budget, and I'll tell you what you value._ - Joe Biden

The Government Budget Explorer is a SaaS platform for creating municipal and county budget sites that
let citizens explore, understand and then engage with their government's plans for spending and revenue. The
first site to use the platform is [Asheville, NC](http://avlbudget.org). The software may be used to create a
standalone server operating a single site or a platform for hosting many different communities. 



## Installation

### Using Vagrant
If you are using Vagrant, the following commands will install and start an instance of a server seeded with the
current Asheville data and site:

    git clone https://github.com/DemocracyApps/GBE.git
    cd GBE
    vagrant up
    vagrant ssh
    cd /var/www/
    sudo ./setup/setup.sh
    cd gbe
    cp .env.example .env
    composer install
    npm install
    bower install
    gulp
    ./artisan migrate --seed
    
The Vagrant file configures the server IP to be 192.168.33.27, but that can of course be changed. You can also find the 
configuration there for a Digital Ocean provider.

When  will probably need to enter your Github credentials while running 'composer update' since you'll 
exceed their anonymous rate limits.

### Installing on an Existing Server

You can also install the software on an existing Ubuntu 12.04 64-bit Precise server (we have not testing on other OS 
configurations). In this case, use the following procedure after logging into the server:

    sudo apt-get install git-core
    cd /var
    git clone https://github.com/DemocracyApps/GBE.git www
    cd www
    sudo ./setup/install.sh
    sudo ./setup/setup.sh
    cd gbe
    cp .env.example .env
    composer install
    npm install
    bower install
    gulp
    ./artisan migrate --seed
    
It is possible that you'll need to run
    
    sudo rm -f ./storage/logs/laravel*

before running the migration due to permission problems.

