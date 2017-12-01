# Installation d'un environnement de développement

```bash
sudo apt-get install virtualbox resolvconf dnsmasq
wget https://dl.bintray.com/mitchellh/vagrant/vagrant_1.9.6_x86_64.deb
sudo dpkg -i vagrant_1.9.6_x86_64.deb
vagrant plugin install vagrant-vbguest
vagrant plugin install vagrant-share
vagrant plugin install landrush
vagrant up

sudo sh -c 'echo "server=/vagrant.dev/127.0.0.1#10053" > /etc/dnsmasq.d/vagrant-landrush'
sudo service dnsmasq restart
```

# Déploiement des sources

```bash
# Compilation du bookmarklet
# si nécessaire : npm -g install bookmarklet
ant configure -Dprofile=pastishosting
bookmarklet ./src/web/forum/extensions/constructions-incongrues/vanilla-ext-bookmarklet/assets/bookmarklet.js > ./src/web/forum/extensions/constructions-incongrues/vanilla-ext-bookmarklet/assets/bookmarklet.compiled.js

# Test
ant deploy -Dprofile=pastishosting

# Déploiement effectif
ant deploy -Dprofile=pastishosting -Drsync.options=--delete-after -Drelease.tag=$(date +"%s")
```

# Création d'une extension

```sh
EXT_NAME=monext
EXT_HOMEPAGE="https://github.com/constructions-incongrues/net.musiques-incongrues.www/tree/master/src/web/forum/extensions/constructions-incongrues/vanilla-ext-${EXT_NAME}"
cd src/web/forum/extensions/constructions-incongrues/
mkdir vanilla-ext-${EXT_NAME}
cd vanilla-ext-${EXT_NAME}
composer init \
    --author="Constructions Incongrues <contact@constructions-incongrues.net>" \
    --homepage="${EXT_HOMEPAGE}" \
    --license=AGPL-3.0 \
    --name=constructions-incongrues/vanilla-ext-${EXT_NAME} \
    --type=project
```

```sh
cat << EOT > ./default.php
<?php
/*
Extension Name: constructions-incongrues.net/vanilla-ext-${EXT_NAME}
Extension Url: ${EXT_HOMEPAGE}
Description:
Version:
Author: Constructions Incongrues
Author Url: http://www.constructions-incongrues.net
*/

require_once(__DIR__.'/vendor/autoload.php');
EOT
```
