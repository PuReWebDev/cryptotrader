#!/bin/bash

folder_name=$1
vagrant_ip=$2
db_user=$3
db_password=$4

now=$(date +"%T")
echo $'\n\033[33;33m '$now' ========> Provisioning virtual machine...'
export LANGUAGE=en_US.UTF-8
export LANG=en_US.UTF-8
export LC_ALL=en_US.UTF-8

locale-gen en_US.UTF-8
dpkg-reconfigure locales

usermod -a -G www-data vagrant

now=$(date +"%T")
echo $'\n\033[33;33m '$now' ========> Adding ppa:ondrej/php...'
add-apt-repository ppa:ondrej/php -y > /dev/null 2>&1
apt-get update -y > /dev/null 2>&1

now=$(date +"%T")
echo $'\n\033[33;33m '$now' ========> Checking PHP...'
phppresent=`which php`
if [ "$phppresent" = "" ]; then
    echo $'\n\033[33;33m '$now' ========> Adding php...'
    apt-get install -y python-software-properties build-essential > /dev/null 2>&1
    apt-get install -y php7.1 > /dev/null 2>&1
    apt-get remove -y apache2 > /dev/null 2>&1
    apt-get install -y php-common php-dev php-cli php-fpm curl php-curl php-gd php-mcrypt php-mysql php-imap nginx > /dev/null 2>&1
    apt-add-repository ppa:brightbox/ruby-ng-experimental > /dev/null 2>&1
    apt-get update -y > /dev/null 2>&1
    apt-get autoremove -y > /dev/null 2>&1
else
    echo $'\n\033[33;33m '$now' ========> php installed... moving on...'
fi

now=$(date +"%T")
echo $'\n\033[33;33m '$now' ========> Checking GIT...'
gitpresent=`which git`
if [ "$gitpresent" = "" ]; then
    echo $'\n\033[33;33m '$now' ========> Installing Git...'
    apt-get install git -y > /dev/null 2>&1
else
    echo $'\n\033[33;33m '$now' ========> GIT installed... moving on...'
fi

postfix=`which postfix`

now=$(date +"%T")
if [ "$postfix" = "" ]; then
    echo $'\n\033[33;33m '$now' ========> Installing Postfix, mailutils...'
    echo postfix postfix/mailname string $7 | debconf-set-selections
    echo postfix postfix/main_mailer_type string 'Internet Site' | debconf-set-selections
    apt-get -qq install -y postfix > /dev/null 2>&1
    service postfix reload > /dev/null 2>&1
else
    echo $'\n\033[33;33m '$now' ========> Postfix installed... moving on...'
fi

mailcatcher=`which mailcatcher`

now=$(date +"%T")
if [ "$mailcatcher" = "" ]; then
    echo $'\n\033[33;33m '$now' ========> Installing Mailcatcher...'
    apt-get -qq -f -y install build-essential software-properties-common > /dev/null 2>&1
    apt-get -qq -f -y install libsqlite3-dev ruby1.9.1-dev > /dev/null 2>&1

    sudo gem install mime-types --version "< 3" > /dev/null 2>&1
    sudo gem install --conservative mailcatcher > /dev/null 2>&1
    sudo sh -c "echo '@reboot root $(which mailcatcher) --ip=0.0.0.0' >> /etc/crontab"
    sudo update-rc.d cron defaults > /dev/null 2>&1
    sudo sh -c "echo 'sendmail_path = /usr/bin/env $(which catchmail)' >> /etc/php/7.1/mods-available/mailcatcher.ini"
    sudo phpenmod -v ALL -s ALL mailcatcher
    sudo cp /var/www/$folder_name/setup/mailcatcher.conf /etc/init/mailcatcher.conf > /dev/null 2>&1
    sudo service php7.0-fpm restart > /dev/null 2>&1
else
    echo $'\n\033[33;33m '$now' ========> Mailcatcher installed... moving on'
fi

now=$(date +"%T")
echo $'\n\033[33;33m '$now' ========> Installing Mysql...'
debconf-set-selections <<< "mysql-server mysql-server/root_password password $db_password"

debconf-set-selections <<< "mysql-server mysql-server/root_password_again password $db_password"

apt-get install mysql-server -y > /dev/null 2>&1

now=$(date +"%T")
echo $'\n\033[33;33m '$now' ========> Configuring Nginx...'
cp /var/www/$folder_name/setup/config/nginx_vhost /etc/nginx/sites-available/$folder_name > /dev/null

sed -i "s/folder_name/$folder_name/g" /etc/nginx/sites-available/$folder_name

ln -s /etc/nginx/sites-available/$folder_name /etc/nginx/sites-enabled/

rm -rf /etc/nginx/sites-available/default

service nginx restart > /dev/null 2>&1

xdebug=`dpkg -l | grep -i php-xdebug`

now=$(date +"%T")
if [ "$xdebug" = "" ]; then
    echo $'\n\033[33;33m '$now' ========> Installing Xdebug...'
    sudo apt-get -qq install -y php-xdebug > /dev/null 2>&1

cat << EOF | sudo tee -a /etc/php/7.1/mods-available/xdebug.ini
xdebug.scream=1
xdebug.cli_color=1
xdebug.show_local_vars=1
xdebug.remote_enable=1
xdebug.remote_handler=dbgp
xdebug.remote_mode=req
xdebug.remote_host=127.0.0.1
xdebug.remote_port=9000
xdebug.remote_autostart=0
xdebug.remote_connect_back=0
xdebug.max_nesting_level = 5000
EOF
else
    echo $'\n\033[33;33m '$now' ========> Xdebug installed... moving on'
fi

chmod -R o+w /var/www/$folder_name/storage

cd /var/www/$folder_name

cp /var/www/$folder_name/setup/config/.env.dev /var/www/$folder_name/.env

sed -i "s/folder_name/$folder_name/g" /var/www/$folder_name/.env
sed -i "s/vagrant_ip/$vagrant_ip/g" /var/www/$folder_name/.env
sed -i "s/db_user/$db_user/g" /var/www/$folder_name/.env
sed -i "s/db_password/$db_password/g" /var/www/$folder_name/.env

sudo php artisan key:generate > /dev/null 2>&1

sed -i '/memory_limit/c\memory_limit = -1' /etc/php/7.1/cli/php.ini
sed -i '/max_execution_time/c\max_execution_time = 0' /etc/php/7.1/cli/php.ini

mysql -uroot -p1234 -e"CREATE DATABASE $folder_name;"

sed -i "s/vagrant_ip/$vagrant_ip/g" /var/www/$folder_name/setup/config/db_setup.sql
sed -i "s/db_user/$db_user/g" /var/www/$folder_name/setup/config/db_setup.sql
sed -i "s/db_password/$db_password/g" /var/www/$folder_name/setup/config/db_setup.sql

mysql -uroot -p1234 "mysql" < /var/www/$folder_name/setup/config/db_setup.sql

sudo perl -pi -w -e 's/bind-address/#bind-address/g;' /etc/mysql/my.cnf
sudo service mysql restart > /dev/null 2>&1

/usr/bin/env $(which mailcatcher) --ip=0.0.0.0 > /dev/null 2>&1

swapsize=4000
grep -q "swapfile" /etc/fstab
if [ $? -ne 0 ]; then
    echo $'\n\033[33;33m '$now' ========> Swapfile not found. Adding swapfile.n'
    fallocate -l ${swapsize}M /swapfile
    chmod 600 /swapfile
    mkswap /swapfile
    swapon /swapfile
    echo '/swapfile none swap defaults 0 0' >> /etc/fstab
else
    echo $'\n\033[33;33m '$now' ========> Swapfile found... moving on'
fi

df -h
cat /proc/swaps
cat /proc/meminfo | grep Swap

now=$(date +"%T")
echo $'\n\033[33;33m '$now' ========> Vagrant has been setup successfully'

