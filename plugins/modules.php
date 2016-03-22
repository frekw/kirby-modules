<?php

kirby()->roots->modules = kirby()->roots->site() . DS . 'modules';


class Modules extends Brick {
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
    return glob(implode(DS, array(kirby()->roots->modules,
                                  '*',
                                  'assets',
                                  'js',
                                  '**.js')));
  }

  static public function css() {
    return glob(implode(DS, array(kirby()->roots->modules,
                                  '*',
                                  'assets',
                                  'css',
                                  '**.css')));
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
