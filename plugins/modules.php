<?php

kirby()->roots->modules = kirby()->roots->site() . DS . 'modules';

class ModulesAssetsController {
  public static function js(){
    return new Response(Modules::js(), 'text/javascript');
  }

  public static function css(){
    return new Response(Modules::css(), 'text/css');
  }
}

class Modules extends Brick {
  public static $routes = array(
    array(
      'pattern' => 'modules.js',
      'action' => 'ModulesAssetsController::js',
      'method' => 'GET'
    ),
    array(
      'pattern' => 'modules.css',
      'action' => 'ModulesAssetsController::css',
      'method' => 'GET'
    )
  );

  public $modules = array();
  public $tag = 'div';

  public function __construct($field) {
    foreach($field->yaml() as $v) {
      if(!isset($v['type'])) {
        throw new Exception('Missing field type for module: ' . $v);
      }
      $this->modules[] = new Module($v['type'], $v, $field->page);
    }
  }

  public function render(){
    foreach($this->modules as $m){
      $this->append($m->render());
    }
    return $this;
  }

  static public function js() {
    return self::respond(implode(DS, array(kirby()->roots->modules,
                                  '*',
                                  'assets',
                                  'js',
                                  '**.js')));
  }

  static public function css() {
    return self::respond(implode(DS, array(kirby()->roots->modules,
                                   '*',
                                   'assets',
                                   'css',
                                   '**.css')));

  }

  static function respond($glob) {
    $output = array();
    $paths = glob($glob);

    foreach($paths as $file){
      $output[] = f::read($file);
    }

    return implode(PHP_EOL . PHP_EOL, $output);
  }
}

class Module {
  public $type = null;
  public $data = null;

  public function __construct($type, $data, $page){
    $this->type = $type;
    $this->data = $data;
    $this->page = $page;
  }

  public function args() {
    $path = $this->controller();

    if(file_exists($path)) {
      $controller = require $path;
      return $controller($this->data, $this->page);
    }

    return array(
      'module' => $this->data,
      'page' => $this->page
    );
  }

  public function path($which) {
    return implode(DS, array(kirby()->roots()->modules, $this->type, $which . '.php'));
  }

  public function template() {
    return $this->path('template');
  }

  public function controller() {
    return $this->path('controller');
  }

  public function render(){
    return tpl::load($this->template(), array('foo' => 'bar'));
  }
}

field::$methods['modules'] = function($field) {
  $field->value = (new Modules($field))->render();
  return $field;
};


ob_start();

$router = new Router(Modules::$routes);

$route = $router->run(kirby()->path());

if(is_null($route)) return;


$response = call($route->action(), $route->arguments());

if(is_a($response, 'Response')) {
  echo $response;
} else {
  echo new Response($response);
}

ob_end_flush();

exit;
