<?php

use Kirby\Panel\Models\Page\Blueprint;

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

  public $fields    = array();
  public $entry     = null;
  public $structure = null;
  public $style     = 'items';

  public function routes() {

    return array(
      array(
        'pattern' => 'add',
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

  public function modalsize() {
    return 'medium';
  }

  public function style() {
    $styles = array('table', 'items');
    return in_array($this->style, $styles) ? $this->style : 'items';
  }

  public function structure() {
    if(!is_null($this->structure)) {
      return $this->structure;
    } else {
      return $this->structure = $this->model->structure()->forField($this->name);
    }
  }

  public function entries() {
      // TODO: We need to get this as a path (field1, index, field2) instead of simply $this->name... HOW?
      // $data = (array)yaml::decode($this->model->{$this->name}());
      $data = (array)yaml::decode($this->value());

      foreach($data as $k => $v){
          $v['id'] = str::random(32);
          $data[$k] = $v;
      }

      $coll = new Collection($data);
      $coll = $coll->map(function($item) {
        return new Obj($item);
      });

      return $coll;
  }

  public function result() {
    $result = array();

    foreach(parent::result() as $k => $v){
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

  public function content() {
    return tpl::load(__DIR__ . DS . 'template.php', array('field' => $this));
  }


  public function url($action) {
     return purl($this->model(), 'field/' . $this->name() . '/modules/' . $action);
  }
}