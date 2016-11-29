# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure(2) do |config|
    # host manager plugin for vagrant
    config.hostmanager.enabled = true
    config.hostmanager.manage_host = true
    config.hostmanager.manage_guest = true
    config.hostmanager.ignore_private_ip = false
    config.hostmanager.include_offline = true
    config.hostmanager.aliases = %w(payroll.dev)

    config.vm.box = "blinkreaction/boot2docker"

    # Network config & shared folders
    config.vm.network "private_network", ip: "192.168.33.70"
    config.vm.synced_folder ".", "/home/docker/payroll", id: "payroll-dev" , type: "nfs"
    # composer home dir where cache of packages exists, if you want to use composer on
    # the virtualbox
    config.vm.synced_folder "~/.composer/", "/home/docker/.composer", id: "payroll-composer" , type: "nfs"

    # VM definition
    config.vm.provider "virtualbox" do |vb|
        vb.name = "payroll"
        vb.memory = 1024
        vb.cpus = 1
    end

    # Bring up containers
    config.vm.provision "shell", run: "always", inline: "cd /home/docker/payroll && docker-compose up -d 1>&2"

    # Redirect webserver port down 80, etc
    config.vm.provision "shell", run: "always", inline: "/usr/local/sbin/iptables -i eth1 -t nat -A PREROUTING -p tcp --dport 80 -j REDIRECT --to-port 8080 1>&2"
    config.vm.provision "shell", run: "always", inline: "/usr/local/sbin/iptables -i eth1 -t nat -A PREROUTING -p tcp --dport 81 -j REDIRECT --to-port 8081 1>&2"

    # Disable guest additions auto update as it won't work on boot2docker, and slows vm boot down boot
    if Vagrant.has_plugin?("vagrant-vbguest")
        config.vbguest.auto_update = false
    end
end
