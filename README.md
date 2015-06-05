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

You  will probably something like the following while running 'composer install':


    Could not fetch https://api.github.com/repos/sebastianbergmann/php-text-template/zipball/206dfefc0ffe9cebf65c413e3d0e809c82fbf00a, please create a GitHub OAuth token to go over the API rate limit
    Head to https://github.com/settings/tokens/new?scopes=repo&description=Composer+on+ip-172-31-24-138+2015-06-05+1547
    to retrieve a token. It will be stored in "/home/ubuntu/.composer/auth.json" for future use by Composer.
    Token (hidden): 

Go to the specified URL, enter your password, then click the button to generate a token. Copy and paste the resulting
token after the prompt and the installation will continue.

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

