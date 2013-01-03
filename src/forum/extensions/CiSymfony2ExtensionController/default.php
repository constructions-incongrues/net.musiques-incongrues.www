<?php
/*
Extension Name: CiSymfony2ExtensionController
Extension Url: http://github.com/constructions-incongrues
Description: TODO
Version: 0.1.0
Author: Constructions Incongrues
Author Url: http://github.com/constructions-incongrues
*/

use \Symfony\Component\Routing\RouteCollection;
use \Symfony\Component\Routing\Matcher\UrlMatcher;
use \Symfony\Component\Routing\RequestContext;
use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;
use \Symfony\Component\HttpKernel\HttpKernel;
use \Symfony\Component\HttpKernel\EventListener\RouterListener;
use \Symfony\Component\HttpKernel\EventListener\ExceptionListener;
use \Symfony\Component\HttpKernel\Controller\ControllerResolver;
use \Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Finder\Finder;

// Setup autoloading
require(__DIR__.'/vendor/autoload.php');

// Create request object
$request = Request::createFromGlobals();

// // Gather extensions controllers 
$routes = new RouteCollection();
$finder = new Finder();
$iterator = $finder
  ->files()
  ->name('controller.php')
  ->in(__DIR__.'/..');
foreach ($iterator as $file) {
    include($file->getRealpath());
}

// Handle request
$context = new RequestContext();
$context->fromRequest($request);

$matcher = new UrlMatcher($routes, $context);

$dispatcher = new EventDispatcher();
$dispatcher->addSubscriber(new RouterListener($matcher));
// TODO : bad, this will silence ALL exception, we should only trap ResourceNotFoundException exceptions
// TODO : implement custom ExceptionListener
$dispatcher->addSubscriber(new ExceptionListener(function (Request $request) {
    $msg = 'Something went wrong! ('.$request->get('exception')->getMessage().')';
    return new Response(null, 200);
}));

$resolver = new ControllerResolver();

$kernel = new HttpKernel($dispatcher, $resolver);

$kernel->handle($request)->send();
