<?php
require_once implode(DS, array(__DIR__, 'fields.php'));

class FormBuilder {
    public static $cache = array();

    public $entry = null;
    public $type = null;
    public $parent = null;
    public $path = null;

    function __construct($entry, $parent) {
        $this->entry = $entry;

        $this->type = str_replace(' ', '-', $this->entry->type);

        $this->parent = $parent;
    }

    public function blueprint() {
        if(isset(self::$cache[$this->type])) {
            return self::$cache[$this->type];
        }

        $path = f::resolve(implode(DS, array(kirby()->roots()->blueprints(), 'modules', $this->type)),
            array('yml', 'php', 'yaml'));

        return self::$cache[$this->type] = data::read($path, 'yaml');
    }

    public function hasOptions() {
        return isset($this->blueprint()['options']);
    }

    public function metadata() {
      $fields = array(
        'module_name' => array(
          'name' => 'module_name',
          'type' => 'hidden'
        ),
      );

      return new FormFields($this->parent, $fields, $this->values(), $this->prefix());
    }

    public function editorState() {
      $fields = array(
        'active_tab' => array(
          'name' => 'active_tab',
          'type' => 'hidden'
        ),
        'collapsed' => array(
          'name' => 'collapsed',
          'type' => 'hidden'
        )
      );

      $values = $this->values();
      $values = isset($values['_editor_state']) ? $values['_editor_state'] : array();

      return new FormFields($this->parent, $fields, $values, $this->prefix('_editor_state'));
    }

    public function render($type = 'fields') {
        switch($type){
        case 'fields':
            return $this->fields();
            break;
        case 'options':
            return $this->options();
            break;
        }
    }

    public function prefix($subtree = '') {
        if($subtree === '') {
            return $this->parent->name . '[' . $this->entry->id . ']';
        }

        return $this->parent->name . '[' . $this->entry->id . '][' . $subtree . ']';
    }

    public function fields() {
        $blueprint = $this->blueprint();
        $fields = $blueprint['fields'];

        $fields['type'] = array(
            'type' => 'hidden',
            'name' => 'type'
        );

        $fields['id'] = array(
            'type' => 'hidden',
            'name' => 'type'
        );

        return new FormFields($this->parent, $fields, $this->values($fields), $this->prefix());
    }

    public function options() {
        $blueprint = $this->blueprint();
        $values = $this->values();
        $values = isset($values['options']) ? $values['options'] : array();

        return new FormFields($this->parent, $blueprint['options'], $values, $this->prefix('options'));
    }

    public function values($fields = array()) {
        // set default values for the provided fields from the blueprint.
        $values = (array)$this->entry;

        foreach($fields as $fieldName => $field) {
            if(!isset($values[$fieldName])){
                $values[$fieldName] = '';
            }
        }

        $values['_modules'] = 'true';

        return $values;
    }
}
