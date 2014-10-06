# Installation

```json
{
    "require": {
        "twig/twig": "1.*"
    }
}
```

# Notes

Les fichiers de theme (appelés par ThemeFilePath()) appellent une méthode de l'extension qui appelle, interprète et affiche le résultat de la template.

eg.

```php
// themes/twig-vanilla/discussion.php

$loader = new Twig_Loader_Filesystem('/path/to/templates');
$twig = new Twig_Environment($loader, array(
    'cache' => '/path/to/compilation_cache',
));

echo $twig->render('index.html', array('name' => 'Fabien'));
```

# Utilisation par une extension

```php
// extensions/MonExtension/default.php

// Declare Twig templates path
$Context->Twig->getLoader()->addPath(__DIR__.'/templates/twig');
```
