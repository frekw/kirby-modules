<?php

kirby()->roots->modules = kirby()->roots->site() . DS . 'modules';

// TODO: if c::get yadda yadda
require_once __DIR__ . DS . 'modules' . DS . 'router.php';
require_once __DIR__ . DS . 'modules' . DS . 'renderer.php';

field::$methods['modules'] = function($field) {
  $field->value = (new ModulesRenderer($field))->render();
  return $field;
};
