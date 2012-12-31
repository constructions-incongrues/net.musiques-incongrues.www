class net::musiquesincongrues::www {
  exec { 'apt-get update':
    command => '/usr/bin/apt-get update'
  }
  Exec["apt-get update"] -> Package <| |>

  exec { 'a2enmod rewrite':
    command => '/usr/sbin/a2enmod rewrite',
    require => Package['apache2'],
    notify => Service['apache2']
  }

  exec { 'mysql load dumps': 
    command => '/usr/bin/mysql -uroot -e "create database if not exists main default charset utf8 collate utf8_general_ci; create database if not exists net_musiquesincongrues_www_asaph default charset utf8 collate utf8_general_ci" && /usr/bin/mysql -uroot main < /vagrant/data/sql/vanilla.sql && /usr/bin/mysql -uroot main < /vagrant/data/sql/asaph.sql',
    require => Package['mysql-server']
  }

  package { ["ant", "apache2", "libapache2-mod-php5", "php5-cli", "php5-curl", "php5-mysqlnd", "php-apc", "php5-intl", "vim-tiny", "mysql-server"]:
    ensure => present,
  }

  service { ["mysql"]:
    ensure => running,
    require => Package["mysql-server"],
  }
  
  service { ["apache2"]:
    ensure => running,
    require => Package["apache2"],
  }

  file { '/etc/apache2/sites-available/default':
    ensure => present,
    source => "puppet:///vagrant_puppet_files/etc/apache2/sites-available/default",
    notify => Service['apache2'],
    require => Package["apache2"],
  }

  file { '/etc/apache2/envvars':
    ensure => present,
    source => "puppet:///vagrant_puppet_files/etc/apache2/envvars",
    require => Package["apache2"],
  }

  file { '/etc/php5/conf.d/9900-local.ini':
    ensure => present,
    source => "puppet:///vagrant_puppet_files/etc/php5/conf.d/9900-local.ini",
    require => Package["apache2"],
    notify => Service['apache2'],
  }

}

include net::musiquesincongrues::www