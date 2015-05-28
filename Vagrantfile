# -*- mode: ruby -*-
# vi: set ft=ruby :

# Vagrantfile API/syntax version. Don't touch unless you know what you're doing!
# CHANGE the config.vm.define line to create a new DigitalOcean droplet
VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
	config.vm.define "avlgbeserver"
	config.vm.box = "precise64"
	config.vm.box_url = "http://files.vagrantup.com/precise64.box"
	config.vm.synced_folder ".", "/var/www"
	config.vm.network :private_network, ip: "192.168.33.27"

	# Digital Ocean Provider Setup - overrides certain configuraiton options to support 
	# a hosted setup via a Digital Ocean droplet
	# 
	# Install:
	# 	You need to add a SSH private key to Digital Ocean, then specify it under 
	# 	override.ssh.private_key_path below.
	#
	# Usage: 
	# 	vagrant up --provider=digital_ocean
    config.vm.provider :digital_ocean do |provider, override|
    	override.vm.box = "digital_ocean"
		override.ssh.private_key_path = "~/.ssh/id_do_rsa"

		# args[0]: specifies Apache User
		override.vm.provision :shell, :path => "setup/install.sh", :args => ["www-data"]

		# This disables the private networking directive from the default config
		override.vm.network :private_network, ip: "192.168.33.27", disabled: true

		# These are the DigitalOcean provider values 
		provider.token = ENV["DIGITAL_OCEAN_ACCESS_TOKEN"]
		provider.image = "ubuntu-12-04-x64"
#		provider.image = "12.04.5 x64"
		provider.region = "nyc2"
	end

	# PRC Start
	# Creating a separate virtualbox configuration 
	# For some reaosn, the provision override above wasn't working properly
	config.vm.provider :virtualbox do |vb, override|
		# args[0]: specifies Apache User
		override.vm.provision :shell, :path => "setup/install.sh", :args => ["vagrant"]
	end
	# PRC End

end
