<?php 

return function($model) {

  $form = new Kirby\Panel\Form(array(
    'entry' => array(
      'label' => 'Do you really want to delete this module?',
      'type'  => 'info',
    )
  ));

  $form->style('delete');
  $form->cancel($model);

  return $form;

};