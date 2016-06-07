<?php

return function($model, $data) {
  $form = new Kirby\Panel\Form([
    'module_name' => [
      'required' => true,
      'label' => 'Module name',
      'type' => 'text',
      'autofocus' => true,
    ]
  ], $data);
  $form->cancel($model);

  return $form;
};
