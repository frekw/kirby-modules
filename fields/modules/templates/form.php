<?php
require_once implode(DS, array(__DIR__, 'form', 'builder.php'));

return function($type, $entry, $field) {
  $builder = new FormBuilder($entry, $field);
  switch ($type){
  case 'has-options':
    return $builder->hasOptions();
    break;
  case 'metadata':
    echo $builder->metadata();
    echo $builder->editorState();
    break;
  default:
    echo $builder->render($type);
    break;
  }
};
