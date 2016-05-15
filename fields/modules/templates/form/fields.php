<?php

class FormFields extends Brick {
  public $fields = array();
  public $values = array();
  public $tag    = 'fieldset';
  public $parent = null;
  public $page = null;
  public $prefix = '';

  public function __construct($parent, $fields = array(), $values = array(), $prefix = null) {
    $this->fields = new Collection;
    $this->parent = $parent;

    $this->page = $parent->page;
    $this->prefix = $prefix;

    $this->values($values);
    $this->fields($fields);
  }

  public function path($name){
    $path = isset($this->parent) ? $this->parent->path() : array();

    // The options tab has no ID set
    if(isset($this->values()['id'])){
      $path[] = $this->values()['id'];
    }

    $path[] = $name;
    return $path;
  }

  public function fields($fields = null) {
    if(is_null($fields)) return $this->fields;
    foreach($fields as $name => $field) {
      $name = str_replace('-','_', str::lower($name));
      $prefixedName = isset($this->prefix) ? $this->prefix . '[' . $name . ']' : $name;


      $field['name']    = $prefixedName;
      $field['page'] = $this->page;
      $field['model'] = $this->page;
      $field['path'] = $this->path($name);
      $field['default'] = a::get($field, 'default', null);

      $value = a::get($this->values(), $name, $field['default']);
      // Ugly hack to ensure list field plugin works
      if (is_array($value) && $field['type'] == 'list') {
        $value = yaml::encode($value);
      }

      $field['value'] = $value;

      $this->fields->append($prefixedName, static::field(isset($field['type']) ? $field['type'] : null, $field));
    }
    return $this;
  }

  public function values($values = null) {
    if(is_null($values)) return array_merge($this->values, r::data());
    $this->values = array_merge($this->values, $values);
    return $this;
  }

  static public function field($type, $options = array()) {
    if (!empty($options['extends'])) {
      $extends = f::resolve(kirby()->roots()->blueprints() . DS . 'fields' . DS . $options['extends'], array('yml', 'php', 'yaml'));
      if (empty($extends)) {
        throw new Exception(l('fields.error.extended'));
      }
      $options = a::merge(data::read($extends, 'yaml'), $options);
      if (empty($type)) $type = $options['type'];
    }

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

