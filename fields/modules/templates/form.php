<?php
require_once implode(DS, array(__DIR__, 'form', 'builder.php'));

return function($type, $entry, $field) {
  switch ($type){
  case 'has-options':
    return (new FormBuilder($entry, $field))->hasOptions();
    break;
  case 'editor-state':
    echo (new FormBuilder($entry, $field))->editorState();
    break;
  default:
    echo (new FormBuilder($entry, $field))->render($type);
    break;
  }
};
