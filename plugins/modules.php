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

class Modules {
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

  public function __construct($field) {
    $data = is_array($field->value()) ? $field->value() : $field->yaml();

    foreach($data as $v) {
      if(!isset($v['type'])) {
        throw new Exception('Missing field type for module: ' . $v);
      }
      $this->modules[] = new Module($v['type'], $v, $field->page);
    }
  }

  public function render(){
    $output = array();

    foreach($this->modules as $m){
      $output[] = $m->render();
    }

    return implode("\n", $output);
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
    $this->data = new stdClass();

    foreach($data as $k => $v){
      if($k === 'options') {
        $this->options($page, $v);
      } else {
        $this->data->$k = new Field($page, $k, $v);
      }
    }

    $this->page = $page;
  }

  public function options($page, $options) {
    $this->data->options = new stdClass();

    foreach($options as $k => $v) {
      $this->data->options->$k = new Field($page, $k, $v);
    }
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
    return tpl::load($this->template(), $this->args());
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
