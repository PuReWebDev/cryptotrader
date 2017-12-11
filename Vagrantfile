# -*- mode: ruby -*-
#vi: set ft=ruby :

require 'yaml'
require 'fileutils'

$CONFIG_ERRORS = File.join(File.dirname(__FILE__), "setup/scripts/errors.rb")

if File.exist?($CONFIG_ERRORS)
    require $CONFIG_ERRORS
end

$CONFIG_HANDLER = File.join(File.dirname(__FILE__), "setup/scripts/config_handler.rb")

if File.exist?($CONFIG_HANDLER)
    require $CONFIG_HANDLER
end

case ARGV[0]
when "provision", "up"
  system "./setup/pre-provision.sh"
else
  # do nothing
end

Vagrant.configure("2") do |config|

  config.ssh.shell = "bash -c 'BASH_ENV=/etc/profile exec bash'"

  config.vm.box = "ubuntu/trusty64"

  config.vm.network :forwarded_port, guest: 80, host: CONFIG['vagrant_port'], auto_correct: true
  config.vm.network "private_network", ip: CONFIG['vagrant_ip']

  config.vm.synced_folder "./", CONFIG['folder'], create: true, group: "www-data", owner: "www-data"

  config.vm.provision "file", source: "~/.gitconfig", destination: ".gitconfig"
  config.vm.provision "file", source: "~/.ssh/id_rsa", destination: ".ssh/id_rsa"
  config.vm.provision "file", source: "~/.ssh/id_rsa.pub", destination: ".ssh/id_rsa.pub"
  config.vm.provision "file", source: "~/.ssh/known_hosts", destination: ".ssh/known_hosts"

  config.vm.provider :virtualbox do |vb|
    # Use VBoxManage to customize the VM. For example to change memory:
    vb.customize ["modifyvm", :id, "--memory", "2024"]
    vb.name = CONFIG['vagrant_name']
    vb.memory = 2024
  end

  config.vm.provision :shell,
    #if there a line that only consists of 'mesg n' in /root/.profile, replace it with 'tty -s && mesg n'
    :inline => "(grep -q -E '^mesg n$' /root/.profile && sed -i 's/^mesg n$/tty -s \\&\\& mesg n/g' /root/.profile && echo 'Ignore the previous error about stdin not being a tty. Fixing it now...') || exit 0;"

  config.vm.provision "shell" do |s|
      s.path = "setup/provision.sh"
      s.args = [
          CONFIG['app_name'],
          CONFIG['vagrant_ip'],
          CONFIG['db_user'],
          CONFIG['db_password'],
      ]
  end
end
