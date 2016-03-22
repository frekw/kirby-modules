<?php
class ModulesFieldController extends Kirby\Panel\Controllers\Field {
  public function add() {
    $self      = $this;
    $model     = $this->model();
    $structure = $this->structure($model);
    $modalsize = $this->field()->modalsize();
    $types = $this->modulesFields($model);
    $form      = $this->form('add', array($model, $structure, $types), function($form) use($model, $structure, $self) {

      $form->validate();

      if(!$form->isValid()) {
        return false;
      }

      $structure->add($form->serialize());
      $self->redirect($model);

    });

    return $this->modal('add', compact('form', 'modalsize'));

  }

  public function delete($entryId) {

    $self      = $this;
    $model     = $this->model();
    $structure = $this->structure($model);
    $entry     = $structure->find($entryId);

    if(!$entry) {
      return $this->modal('error', array(
        'text' => l('fields.structure.entry.error')
      ));
    }

    $form = $this->form('delete', $model, function() use($self, $model, $structure, $entryId) {
      $structure->delete($entryId);
      $self->redirect($model);
    });

    return $this->modal('delete', compact('form'));

  }

  public function sort() {
    $model     = $this->model();
    $structure = $this->structure($model);
    $structure->sort(get('ids'));
    $this->redirect($model);
  }

  protected function structure($model) {
    return $model->structure()->forField($this->fieldname());
  }

  protected function modules($model) {
    return $this->model->getBlueprintFields()->get($this->fieldname)->modules()['types'];
  }

  protected function modulesFields($model) {
    $moduleTypes = array();
    foreach($this->modules($model) as $t) {
      $moduleTypes[strtolower($t)] = $t;
    }

    return array(
      'type' => array(
        'required' => true,
        'name' => 'Module Type',
        'type' => 'select',
        'options' => $moduleTypes
      )
    );
  }
}
