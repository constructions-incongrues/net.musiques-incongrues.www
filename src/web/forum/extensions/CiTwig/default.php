<?php
// Composer autoload
require($Context->Configuration['APPLICATION_PATH'].'/../../../vendor/autoload.php');

// TODO : PSR-0
require($Context->Configuration['LIBRARY_PATH'].'Vanilla/Vanilla.Class.CategoryManager.php');
require(__DIR__.'/src/ConstructionsIncongrues/Vanilla/CategoryManager.php');

/**
 * Helper function to be used in templates.
 * eg. in menu.php : CiTwigRender(__FILE__, $this);
 */
function CiTwigRender($path, $control, array $data = array())
{
    echo $control->Context->Twig->render(basename($path), array_merge(array('self' => $control), $data));
}

// Instanciate and configure Twig loader
// TODO : handle debug mode
// TODO : inject common context values (request, etc)
// TODO : rename "views" folder to "templates/twig" or similar
// TODO : allow extensions to declare more twig paths for cascading
$twig = new \Twig_Environment(new \Twig_Loader_Filesystem(__DIR__.'/themes/vanilla/views'));

// Add functions
// TODO : make list configurable
$twigImportFunctions = array(
    'AppendUrlParameters',
    'FlipBool',
    'ForceBool',
    'ForceIncomingBool',
    'ForceIncomingString',
    'FormatHyperlink',
    'GetDynamicCheckBox',
    'GetEmail',
    'GetRequestUri',
    'GetUrl',
    'ReturnNonEmpty',
    'TimeDiff',
);
foreach ($twigImportFunctions as $functionName) {
    $twig->addFunction(new Twig_SimpleFunction($functionName, function () use ($functionName) {
        return call_user_func_array($functionName, func_get_args());
    }));
}

// Add Twig instance to context object
$Context->Twig = $twig;

// Delegate functions

/**
 * Injects custom CategoryManager.
 */
function CiTwig_CategoryList_PreDataLoad(\CategoryList $categoryList)
{
    // Inject our own CategoryManager
    $categoryList->DelegateParameters['CategoryManager'] = $categoryList->Context->ObjectFactory->NewContextObject(
        $categoryList->Context,
        "\ConstructionsIncongrues\Vanilla\CategoryManager"
    );
}

// Register delegates
$Context->AddToDelegate('CategoryList', 'PreDataLoad', 'CiTwig_CategoryList_PreDataLoad');
