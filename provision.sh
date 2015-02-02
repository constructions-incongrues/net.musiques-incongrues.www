#!/usr/bin/env bash

# Mise à jour des dépots
apt-get -qq update

# Utils
apt-get -y install rpl

# Configuration de la timezone
echo "Europe/Paris" > /etc/timezone 
apt-get install -y tzdata
dpkg-reconfigure -f noninteractive tzdata

# Installation de Apache et PHP
apt-get -y install libapache2-mod-php5 php5-cli php5-curl
rpl "AllowOverride None" "AllowOverride All" /etc/apache2/sites-available/default
a2enmod rewrite
service apache2 restart

# Installation de MySQL
echo "mysql-server mysql-server/root_password password root" | debconf-set-selections
echo "mysql-server mysql-server/root_password_again password root" | debconf-set-selections
apt-get install -y mysql-server

# Installation de PhpMyAdmin
echo "phpmyadmin phpmyadmin/dbconfig-install boolean true" | debconf-set-selections
echo "phpmyadmin phpmyadmin/app-password-confirm password root" | debconf-set-selections
echo "phpmyadmin phpmyadmin/mysql/admin-pass password root" | debconf-set-selections
echo "phpmyadmin phpmyadmin/mysql/app-pass password root" | debconf-set-selections
echo "phpmyadmin phpmyadmin/reconfigure-webserver multiselect apache2" | debconf-set-selections
apt-get install -y phpmyadmin

# Création des bases de données
# -- forum
mysql --defaults-file=/etc/mysql/debian.cnf -e "drop database if exists net_musiquesincongrues_www_forum"
mysql --defaults-file=/etc/mysql/debian.cnf -e "create database net_musiquesincongrues_www_forum default charset utf8 collate utf8_general_ci"
gunzip -c /vagrant/src/data/net_musiquesincongrues_www_forum.dump.sql.gz | mysql --defaults-file=/etc/mysql/debian.cnf net_musiquesincongrues_www_forum
# -- asaph
mysql --defaults-file=/etc/mysql/debian.cnf -e "drop database if exists net_musiquesincongrues_www_asaph"
mysql --defaults-file=/etc/mysql/debian.cnf -e "create database net_musiquesincongrues_www_asaph default charset utf8 collate utf8_general_ci"

# Configuration du projet
apt-get install -y ant 
cd /vagrant
./composer.phar install --prefer-dist --no-progress
ant configure build -Dprofile=vagrant
chmod -R 777 /tmp/symfony/musiques-incongrues
/vagrant/src/symfony/symfony cache:clear

# Mise à disposition du projet dans Apache
ln -sf /vagrant/src/web/* /var/www/
rm -f /var/www/index.html
