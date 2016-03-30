<?php

class ModuleRenderer {
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

class ModulesRenderer {
  public $modules = array();

  public function __construct($field) {
    $data = is_array($field->value()) ? $field->value() : $field->yaml();

    foreach($data as $v) {
      if(!isset($v['type'])) {
        throw new Exception('Missing field type for module: ' . $v);
      }
      $this->modules[] = new ModuleRenderer($v['type'], $v, $field->page);
    }
  }

  public function render(){
    $output = array();

    foreach($this->modules as $m){
      $output[] = $m->render();
    }

    return implode("\n", $output);
  }
}