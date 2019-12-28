<?php 

if (!defined("BASE_DIR")) {
  define("BASE_DIR", "/var/www/html/");
}


// stage
$stage = getenv('INTRANET_STAGE', true) ?: getenv('INTRANET_STAGE');


if($stage == "production") {
  #die(print_r($_SERVER));
  $url = "/pages";
  header('Location: '. $url);
  exit();
} 



/************************ TESTING AREA  */
require_once BASE_DIR .'/vendor/autoload.php';
use App\Controller\BlogController;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

// init route
$routes = new RouteCollection();

// route Torunament
require_once (BASE_DIR .'/inc/html/brdbHtmlTournamentPage.inc.php');
$routeTournament = new Route('/ttt',
  ['_controller' => BrdbHtmlTournamentPage::class ]/*,
  [ 'action'      => '' ],
  [ 'id'          =>  '[0-9]'] */
);
$routes->add('tournament', $routeTournament);


// context
$context = new RequestContext('/');

// Routing can match routes with incoming requests
$matcher = new UrlMatcher($routes, $context);


#print_r($matcher);
print_r($context);
?>