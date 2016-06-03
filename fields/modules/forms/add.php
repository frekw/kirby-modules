<?php

return function($model, $store, $types) {
  $form = new Kirby\Panel\Form([
    'type' => [
      'required' => true,
      'name' => 'Module Type',
      'type' => 'select',
      'options' => $types,
      'autofocus' => true
    ]
  ]);
  $form->cancel($model);
  $form->buttons->submit->value = l('add');

  return $form;
};
