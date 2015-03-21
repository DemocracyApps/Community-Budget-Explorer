# Government Budget Explorer

A SaaS platform for local budget sites like the [Asheville budget site](http://avlbudget.org) we built last year.

See the [wiki](https://github.com/DemocracyApps/GBE/wiki) for more details or come join the discussion at 
the [Open Budgets Project Discussion list](https://groups.google.com/forum/?hl=en#!forum/open-budgets-project).

If you are using Vagrant, the following commands will install and start an instance of a server seeded with the 2014 Asheville data:

    git clone https://github.com/DemocracyApps/GBE.git
    cd GBE
    vagrant up
    vagrant ssh
    cd /var/www/
    sudo ./setup/setup.sh
    cd gbe
    ./artisan migrate --seed
    
The server will be at 192.168.33.27, but that can of course be changed in the Vagrantfile.

    
