<?php
require_once implode(DS, array(__DIR__, 'fields.php'));

// TODO: Static module cache.
class FormBuilder {
    public $entry = null;
    public $type = null;
    public $cache = array();
    public $parent = null;
    public $path = null;

    function __construct($entry, $parent) {
        $this->entry = $entry;
        $this->type = $this->entry->type;

        $this->parent = $parent;
    }

    public function blueprint() {
        if(isset($this->cache['blueprint'])) {
            return $this->cache['blueprint'];
        }

        $path = f::resolve(implode(DS, array(kirby()->roots()->blueprints(), 'modules', $this->type)),
                           array('yml', 'php', 'yaml'));

        return $this->cache['blueprint'] = data::read($path, 'yaml');
    }

    public function hasOptions() {
      return isset($this->blueprint()['options']);
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
