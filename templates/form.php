<?php
require_once implode(DS, array(__DIR__, 'form', 'builder.php'));

return function($type, $entry, $field) {
  echo (new FormBuilder($entry, $field))->render($type);
};