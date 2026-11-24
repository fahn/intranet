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

use Badtra\Intranet\Libs\Router;

require_once __DIR__ .'/vendor/autoload.php';
define('__BASE_DIR__', __DIR__);


// Router initialisieren
$router = new Router('routes.yaml');

// URI abrufen
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Router ausführen
$router->dispatch($uri);
