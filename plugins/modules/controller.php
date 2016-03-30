<?php

class ModulesAssetsController {
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

  public static function js(){
    return new Response(self::concatjs(), 'text/javascript');
  }

  public static function css(){
    return new Response(self::concatcss(), 'text/css');
  }

  static public function concatjs() {
    return self::respond(implode(DS, array(kirby()->roots->modules,
                                         '*',
                                         'assets',
                                         'js',
                                         '**.js')));
  }

  static public function concatcss() {
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

