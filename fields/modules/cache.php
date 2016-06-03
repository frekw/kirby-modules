<?php

class ModulesPageCache extends Obj {
  public $page = null;
  public $key = '';
  public $data = null;

  function __construct($page){
    $this->page = $page;
    $this->key = 'modules::' . $page->id();
    $this->blueprint = $this->page->blueprint()->yaml();

    $data = s::get($this->key);

    if(!isset($data)){
      $data = $this->data();
    }

    $this->data = $data;
    $this->save();
  }

  public function update($path, $data){
    $node = &$this->data;

    foreach($path as $p){
      $node = &$node[$p];
    }
    $node = $data;
    $this->save();
  }

  public function add($path, $data){
    $node = &$this->data;
    foreach($path as $p){
      if(!isset($node[$p])){
        $node[$p] = array();
      }

      $node = &$node[$p];
    }

    if(is_array($node)){
      if(!isset($data['id'])){
        $id = $this->id();
        $data['id'] = $id;
      }

      $node[$data['id']] = $data;

      $this->save();
    }
  }

  public function delete($path){
    $id = array_pop($path);
    $node = &$this->data;

    foreach($path as $p){
      $node = &$node[$p];
    }

    unset($node[$id]);
    $this->save();
  }

  public function collection($path){
    $data = $this->get($path);
    $coll = new Collection($data);

    $coll = $coll->map(function($item) {
      return new Obj($item);
    });

    return $coll;
  }

  public function parent($path){
    $p = $path;
    array_pop($p);
    return $this->get($p);
  }

  public function get($path, $default = null){
    $result = $this->data;

    foreach($path as $p){
      if(!isset($result[$p])){
        return $default;
      }

      $result = $result[$p];
    }
    return $result;
  }

  public function save(){
    s::set($this->key, $this->data);
  }

  function typeForField($field, $blueprint) {
    if(!isset($field)) {
      return null;
    }

    if(!isset($blueprint) ||
       !isset($blueprint['fields']) ||
       !isset($blueprint['fields'][$field]) ||
       !isset($blueprint['fields'][$field]['type'])){
      return null;
    }

    return str_replace(' ', '-', $blueprint['fields'][$field]['type']);
  }

  function isModulesField($field, $blueprint){
    return $this->typeForField($field, $blueprint) === 'modules';
  }


  function data() {
    if(isset($this->data)) {
      return $this->data;
    }

    $data = array();
    $content = $this->page()->content();

    foreach($content->fields() as $field){
      $type = $this->typeForField($field, $this->blueprint);
      if($this->isModulesField($field, $this->blueprint)){
        $data[$field] = $this->toModulesField($type, yaml::decode($content->get($field)));
      }
    }

    $data['module_name'] = yaml::decode($content->get('module_name'));

    return $this->data = $data;
  }

  function moduleBlueprint($type){
    $path = f::resolve(implode(DS, array(kirby()->roots()->blueprints(), 'modules', $type)), array('php', 'yaml', 'yml'));

    if($path){
      return yaml::decode(f::read($path));
    }

    return array();
  }

  function toModulesField($type, $data){
    $result = array();

    foreach($data as $k => $v){
      $id = $this->id();
      $v['id'] = $id;
      $result[$id] = $this->extractField($v['type'], $v);
    }

    return $result;
  }


  function extractField($type, $data){
    $blueprint = $this->moduleBlueprint($type);

    foreach($data as $k => $v){
      $type = $this->typeForField($k, $blueprint);

      if($this->isModulesField($k, $blueprint)){
        $data[$k] = $this->toModulesField($type, $v);
      }
    }

    return $data;
  }

  function id(){
    return str::random(32);
  }


  function serialize($data){
    if(count($data) < 1) return $data;
    if(!is_array($data)) return $data;

    if(isset(reset($data)['id'])){
      $result = array();
      foreach($data as $k => $v) {
        unset($v['id']);
        unset($v['_editor_state']);
        $result[]= $this->serialize($v);
      }

      return $result;
    }

    foreach($data as $k => $v){
      $data[$k] = $this->serialize($v);
    }

    return $data;
  }
}
