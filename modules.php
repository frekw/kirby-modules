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
    return $this->structure()->data();
  }

  public function result() {
    $result = parent::result();
    if(isset($result)) {
      foreach($result as $id => $data) {
        $this->structure()->update($id, $data);
      }
    }

    return $this->structure()->toYaml();
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
