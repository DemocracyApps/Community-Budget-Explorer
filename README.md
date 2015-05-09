# Government Budget Explorer

A SaaS platform for local budget sites like the [Asheville budget site](http://avlbudget.org) we built last year.

See the [wiki](https://github.com/DemocracyApps/GBE/wiki) for more details.

To track any upcoming or recent changes that may impact other developers, follow the [Changes page](Changes). 

If you are using Vagrant, the following commands will install and start an instance of a server seeded with the 2014 Asheville data:

    git clone https://github.com/DemocracyApps/GBE.git
    cd GBE
    vagrant up
    vagrant ssh
    cd /var/www/
    sudo ./setup/setup.sh
    cd gbe
    cp .env.example .env
    composer update
    npm install
    bower install
    ./artisan migrate --seed
    
The server will be at 192.168.33.27, but that can of course be changed in the Vagrantfile. You can also find the 
configuration there for a Digital Ocean provider.

You will probably need to enter your Github credentials while running 'composer update' since you'll 
exceed their anonymous rate limits.


    
