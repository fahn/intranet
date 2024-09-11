<?php
/**
 * Badminton Intranet System
 * Copyright 2017-2024
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
 * @copyright 2017-2024 Badtra
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      https://www.badtra.de
 **/
namespace Badtra\Intranet;

// load require
// require_once './vendor/autoload.php';
 
// use Symfony\Component\Routing\RequestContext;
// use Symfony\Component\Routing\Router;
// use Symfony\Component\HttpFoundation\Request;
// use Symfony\Component\Routing\Generator\UrlGenerator;
// use Symfony\Component\Config\FileLocator;
// use Symfony\Component\Routing\Loader\YamlFileLoader;
// use Symfony\Component\Routing\Exception\ResourceNotFoundException;
// use Symfony\Component\Routing\Matcher\UrlMatcher;
// use Symfony\Component\Debug\Debug;


use \Badtra\Intranet\Html\BrdbHtmlPage;
use \Badtra\Intranet\Html\TournamentPage;


// start session
session_start();

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

# Development stage
$stage = getenv('INTRANET_STAGE', true) ?: getenv('INTRANET_STAGE');
if ($stage == "development") {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    // define Debug
    Debug::enable();
}

# define base dir
if (defined("BASE_DIR") === false) {
    define("BASE_DIR", $_SERVER['DOCUMENT_ROOT']);
}



spl_autoload_register(function ($className) {
    if (strpos($className, "Badtra") === false) {
        return;
    }
    try {
        $arr = explode("\\", $className);
        $className = end($arr);
        array_pop($arr);
        $dir = strtolower(end($arr));
        unset($arr);

        $className = str_replace("\\", DIRECTORY_SEPARATOR, $className);
        require_once $_SERVER['DOCUMENT_ROOT'] ."/inc/". $dir ."/". $className . ".php";
    } catch (\Exception $e) {
        echo "could not load". $className; 
        echo $e->getMessage();
    }
});

 

try
{
    
    // Load routes from the yaml file
    //$fileLocator = new FileLocator(array(__DIR__));
    //$loader = new YamlFileLoader($fileLocator);
    //$routes = $loader->load('routes.yaml');
 
    // Init RequestContext object
    $requestContext = new RequestContext();
    $requestContext->fromRequest(Request::createFromGlobals());

    $router = new Router(
        new YamlFileLoader($fileLocator),
        './routes.yaml',
        array('cache_dir' => __DIR__.'/cache'),
        $requestContext
    );
    #echo "<pre>";
    #var_dump($routes);

    #var_dump($requestContext);

    #var_dump($router);
    #return $router;

    // How to generate a SEO URL
    $routes = $router->getRouteCollection();
    $generator = new UrlGenerator($routes, $requestContext);
    $matcher = new UrlMatcher($routes, $requestContext);


    $parameters = $matcher->match($requestContext->getPathInfo());
    
    #var_dump($parameters);
    #print_r($parameters);
    $parts = explode("::", $parameters['_controller']);
    $class = $parts[0];
    $function = $parts[1];

    $controller = new ${"class"}();
    echo $controller->${"function"}();
    #var_dump($callYa);
    #$response = call_user_func_array(array($callYa[0], $callYa[1]), array());#, array_slice($parameters, 1, -1));
    #echo $response;

    #echo '<pre>';
    #print_r($parameters);

    #echo $route = $router->getRouteCollection()->get('matcher');

    exit;
} catch (ResourceNotFoundException $e) {
    echo '<pre>';
    echo "ERROR\n";
    var_dump($e);
    echo $e->getMessage();
}
exit;