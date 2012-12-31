# Installation
## Récupération des sources
```bash
git clone git@github.com:constructions-incongrues/net.musiques-incongrues.www.git
```

## Démarrage et configuration de la machine virtuelle
```bash
vagrant up
ant configure build -Dprofile=vagrant
```

## Accès au site
Pour obtenir l'IP de la machine virtuelle, se placer dans le répertoire des sources et s'y connecter :
```bash
vagrant ssh
```
Une fois connecté, identifier l'ip de la machine : 
```bash
ifconfig
```

Le site est accessible à l'adresse : http://IP/forum
