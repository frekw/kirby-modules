<?php

return function($model, $store, $types) {
  $form = new Kirby\Panel\Form($types);
  $form->cancel($model);
  $form->buttons->submit->value = l('add');

  return $form;
};
