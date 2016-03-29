<?php

use Kirby\Panel\Models\Page\Blueprint;
require_once __DIR__ . DS . 'cache.php';

function d($x) {
  echo '<br /><br /><pre>';
  print_r($x);
  echo '</pre>';
}


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

  public function collection($path){
    $data = $this->get($path);
    $coll = new Collection($data);

    $coll = $coll->map(function($item) {
      return new Obj($item);
    });

    return $coll;
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

  public function update($path, $data){
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
       !isset($blueprint['fields'][$field])){
      return null;
    }

    return $blueprint['fields'][$field]['type'];
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
    $result = array('_modules' => true);

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

  function debug(){
    echo '<pre>';
    var_dump($this->collection(array('page_modules', 'VQ9vDAee1JcA10ykDnVPsbpYnYSo58uf', 'content')));
    echo '</pre>';
  }
}

class ModulesField extends BaseField {
  static public $assets = array(
    'js' => array(
      'modules.js'
    ),
    'css' => array(
      'modules.css'
    )
  );

  public $entry = null;
  public $cache = null;
  public $path = null;

  public function routes() {
    return array(
      array(
        'pattern' => 'add/(:all)',
        'method'  => 'get|post',
        'action'  => 'add'
      ),
      array(
        'pattern' => 'sort',
        'method'  => 'post',
        'action'  => 'sort',
      ),
      array(
        'pattern' => '(:any)/delete',
        'method'  => 'get|post',
        'action'  => 'delete',
      )
    );
  }


  public function style() {
    $styles = array('table', 'items');
    return in_array($this->style, $styles) ? $this->style : 'items';
  }

  public function cache() {
    if(isset($this->cache)){
      return $this->cache;
    }

    $this->cache = new ModulesPageCache($this->model());
    return $this->cache;
  }

  public function entries() {
    return $this->cache()->collection($this->path());
  }

  public function result() {
    $parent = parent::result();
    $this->cache()->update($parent);

    $result = array();

    foreach($parent as $k => $v){
      if(isset($v['id'])){
        unset($v['id']);
      }
      $result[] = $v;
    }

    return trim(yaml::encode($result));
  }

  public function label() {
    return null;
  }

  public function headline() {
    $label = parent::label();
    $label->addClass('modules-label');

    return $label;
  }

  public function path(){
    if(isset($this->path)) return $this->path;

    if(!isset($this->parent)){
      $this->path = array($this->name);
    } else {
      $this->path = $this->parent->path();
      $this->path[] = $this->name;
    }

    return $this->path;
  }

  public function content() {
    (new ModulesPageCache($this->model()))->debug();
    return tpl::load(__DIR__ . DS . 'template.php', array('field' => $this));
  }


  public function url($action) {
    $path = join('/', $this->path());
    $root = $this->path()[0];
    return purl($this->model(),
                join('/', array('field',
                                $root,
                                'modules',
                                $action,
                                $path)));
  }
}