<?php
require_once implode(DS, array(__DIR__, 'form', 'builder.php'));

return function($type, $entry, $field) {
  if($type === 'hasOptions'){
    return (new FormBuilder($entry, $field))->hasOptions();
  }
  echo (new FormBuilder($entry, $field))->render($type);
};