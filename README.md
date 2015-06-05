# Government Budget Explorer

_Don't tell me what you value, show me your budget, and I'll tell you what you value._ - Joe Biden

The Government Budget Explorer is a SaaS platform for creating municipal and county budget sites that
let citizens explore, understand and then engage with their government's plans for spending and revenue. The
first site to use the platform is [Asheville, NC 2015-16 Budget Explorer](http://avlbudget.org). The
software may be used to create a standalone server operating a single site or a platform for hosting
many different communities.

The platform is designed to be highly customizable and extensible. 

Site creators have the ability to create pages, 
select from multiple available layouts or create their own on each page, and select from a menu of components
(visualizations, tables, navigation components, resource tables, slideshows, simple pages, etc.) for placement
within each layout. 

Developers have the ability to create new visualizations, storytelling components, whatever they like and make them 
available for inclusion in their own or others' sites. The platform currently has a small set of built-in components,
but we will be creating more over time. If you are interested in contributing to the platform, this would be a
great way to do it

## Development Status

This platform is under very active development as of June, 2015. We just released
the [Asheville budget site](http://avlbudget.org) as our first production site on the new platform. We will
begin adding several western NC county budgets over the next month or two, and are looking for other communities
interested in using the platform. 


## Documentation

There is documentation about the platform and the administrative interface, and guidance on developing new components
on the [wiki](https://github.com/DemocracyApps/GBE/wiki). This is a first draft - we will be improving on it and
better integrating it into the repository itself over the next few months.

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

You  will probably see something like the following while running 'composer install':


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

