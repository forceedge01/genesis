#!/usr/bin/env bash

sudo -i

apt-get update
apt-get -y install apache2

apt-get -y build-dep php5-cli
apt-get -y -f install php5-cli

apt-get -y build-dep libapache2-mod-php5
apt-get -y -f install libapache2-mod-php5

service apache2 restart

apt-get -y build-dep mysql-server
debconf-set-selections <<< 'mysql-server-5.5 mysql-server/root_password password root'
debconf-set-selections <<< 'mysql-server-5.5 mysql-server/root_password_again password root'
apt-get -y -f install mysql-server

/etc/init.d/mysql restart

apt-get -y build-dep libapache2-mod-auth-mysql
apt-get -y -f install libapache2-mod-auth-mysql

apt-get -y build-dep php5-mysql
apt-get -y -f install php5-mysql

mysql --user=root --password=root -e "source /var/www/initVagrant/init.sql"

#echo -e "mysql_root_password=root controluser_password=root" > /etc/phpmyadmin.facts;
#apt-get -y build-dep phpmyadmin
#apt-get -y -f install phpmyadmin

service apache2 restart

/usr/sbin/apache2ctl restart

exit
