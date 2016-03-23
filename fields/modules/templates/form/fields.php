<?php

class FormFields extends Brick {
  public $fields = array();
  public $values = array();
  public $tag    = 'fieldset';
  public $parent = '';
  public $page = null;

  public function __construct($page = array(), $fields = array(), $values = array(), $parent = null) {
    $this->fields = new Collection;
    $this->parent = $parent;
    $this->page = $page;

    $this->values($values);
    $this->fields($fields);
  }

  public function fields($fields = null) {
    if(is_null($fields)) return $this->fields;
    foreach($fields as $name => $field) {
      $name = str_replace('-','_', str::lower($name));
      $prefixedName = isset($this->parent) ? $this->parent . '[' . $name . ']' : $name;

      $field['name']    = $prefixedName;
      $field['default'] = a::get($field, 'default', null);
      $field['value']   = a::get($this->values(), $name, $field['default']);
      $field['page'] = $this->page;
      $field['model'] = $this->page;

      $this->fields->append($prefixedName, static::field($field['type'], $field));
    }
    return $this;
  }

  public function values($values = null) {
    if(is_null($values)) return array_merge($this->values, r::data());
    $this->values = array_merge($this->values, $values);
    return $this;
  }

  static public function field($type, $options = array()) {
    $class = $type . 'field';
    if(!class_exists($class)) {
      throw new Exception('The ' . $type . ' field is missing. Please add it to your installed fields or remove it from your blueprint');
    }
    $field = new $class;
    foreach($options as $key => $value) {
      $field->$key = $value;
    }

    return $field;
  }

  public function toHTML() {
    $this->addClass('fieldset field-grid cf');
    foreach($this->fields() as $field) $this->append($field);

    return $this;
  }

  public function __toString() {
    $this->toHTML();
    return parent::__toString();
  }
}

