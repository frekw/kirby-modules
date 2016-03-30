<?php

require_once __DIR__ . DS . 'controller.php';

$router = new Router(ModulesAssetsController::$routes);
$route = $router->run(kirby()->path());
if(is_null($route)) return;

$response = call($route->action(), $route->arguments());

if(is_a($response, 'Response')) {
  echo $response;
} else {
  echo new Response($response);
}

exit;