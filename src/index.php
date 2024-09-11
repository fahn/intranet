<?php
/**
 * Badminton Intranet System
 * Copyright 2017-2024
 * All Rights Reserved
 *
 * Copying, distribution, usage in any form is not
 * allowed without  written permit.
 *
 * Stefan Metzner <stefan@weinekind.de>
 * Philipp M. Fischer <phil.m.fischer@googlemail.com>
 *
 **/
namespace Badtra\Intranet;

require_once __DIR__ .'/vendor/autoload.php';

// use \Badtra\Intranet\Html\BrdbHtmlPage;

use \Badtra\Intranet\Controller\HomeController;


// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// ini_set('error_log', '/var/log/php_errors.log');
// error_reporting(E_ALL);
// error_reporting(-1);
// //phpinfo();

// // load require
// $page = new BrdbHtmlPage();
// $page->processPage();


namespace Badtra\Intranet;
require_once __DIR__ .'/vendor/autoload.php';

define('__BASE_DIR__', __DIR__);


use Badtra\Intranet\Libs\Router;

// Router initialisieren
$router = new Router('routes.yaml');

// URI abrufen
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Router ausführen
$router->dispatch($uri);

/*
$router->add('/', function() use ($pdo, $smarty) {
    $controller = new HomeController($pdo, $smarty);
    $controller->index();
});
*/
/*
$router->add('/user/details/(\d+)', function($id) use ($pdo, $smarty) {
    $controller = new HomeController($pdo, $smarty);
    $controller->userDetails($id);
});
*/




?>