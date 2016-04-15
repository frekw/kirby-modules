<?php

class ModuleContent {
  public $page = null;
  public $data = array();

  public function __construct($page, $data) {
    $this->page = $page;
      
    foreach($data as $k => $v){
      $this->data[$k] = $v instanceof ModuleContent ? $v : new Field($page, $k, $v);
    }
  }

  public function get($key, $arguments = null) {
    // case-insensitive data fetching    
    $key = strtolower($key);

    if(isset($this->data[$key])) {
      return $this->data[$key];
    } else {
      // return an empty field as default
      return new Field($this->page, $key);
    }
  }

  public function __call($method, $arguments = null) {
    return $this->get($method, $arguments);
  }
}

class ModuleRenderer {
  public $type = null;
  public $data = null;
  public $modules = null;

  public function __construct($type, $data, $page, $modules){
    $this->type = str_replace(' ', '-', $type);

    if(isset($data['options'])){
      $data['options'] = new ModuleContent($page, $data['options']);
    }

    $this->data = new ModuleContent($page, $data);
    $this->page = $page;
    $this->modules = $modules;
  }

  public function args() {
    $path = $this->controller();

    if(file_exists($path)) {
      $controller = require $path;
      return $controller($this->data, $this->page);
    }

    return array(
      'module' => $this->data,
      'page' => $this->page,
      'modules' => $this->modules
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
      $this->modules[] = new ModuleRenderer($v['type'], $v, $field->page, $data);
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
