<?php

use Kirby\Panel\Models\Page\Blueprint;
require_once __DIR__ . DS . 'cache.php';

function d($x) {
  echo '<br /><br /><pre>';
  print_r($x);
  echo '</pre>';
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

    $field = $this->path()[0];
    $this->cache = new ModulesCache($this->page()->id(), $field, $this->value());
    return $this->cache;
  }

  public function entries() {
    echo '<pre>';
        print_r($this->cache()->collection($this->path())->data);
    echo '</pre>';
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