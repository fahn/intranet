<?php
/*******************************************************************************
 * Badminton Intranet System
 * Copyright 2017-2020
 * All Rights Reserved
 *
 * Copying, distribution, usage in any form is not
 * allowed without  written permit.
 *
 * PHP versions 7
 *
 * @category  BadtraIntranet
 * @package   BadtraIntranet
 * @author    Stefan Metzner <stmetzner@gmail.com>
 * @copyright 2017-2020 Badtra
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      https://www.badtra.de
 ******************************************************************************/
require_once './vendor/autoload.php';

require_once './vendor/autoload.php';
 
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
 
$fileLocator = new FileLocator(array(__DIR__));

$requestContext = new RequestContext();
$requestContext->fromRequest(Request::createFromGlobals());

$router = new Router(
    new YamlFileLoader($fileLocator),
    'routes.yaml',
    array('cache_dir' => __DIR__.'/cache'),
    $requestContext
);

// Find the current route
$parameters = $router->match($requestContext->getPathInfo());

// How to generate a SEO URL
$routes = $router->getRouteCollection();
$generator = new UrlGenerator($routes, $requestContext);
$url = $generator->generate('foo_placeholder_route', array(
  'id' => 123,
));

echo '<pre>';
print_r($parameters);

echo 'Generated URL: ' . $url;
exit;
