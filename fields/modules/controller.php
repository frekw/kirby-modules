<?php

require_once __DIR__ . DS . 'cache.php';

function last(&$arr){
  $x = end($arr);
  reset($arr);
  return $x;
}

function first(&$arr){
  reset($arr);
  return $arr[0];
}

class ModulesFieldController extends Kirby\Panel\Controllers\Field {
  public function add($path) {
    $path = explode('/', $path);
    $root = first($path);
    $field = last($path);
    $model = $this->model();
    $cache = new ModulesPageCache($model);

    if($root !== $field){
      $data = $cache->parent($path);
      $type = $data['type'];
      $modules = $this->modulesForType($type, $field);
    } else {
      $modules = $this->modulesForRootField($this->model());
    }

    $self = $this;
    $model = $this->model();
    $modalsize = 'medium';
    $types = $this->modulesToOptions($modules);
    $form = $this->form('add', array($model, $cache, $types), function($form) use($model, $cache, $self, $path) {

      $form->validate();

      if(!$form->isValid()) {
        return false;
      }

      $cache->add($path, $form->serialize());
      $self->redirect($model);
    });

    return $this->modal('add', compact('form', 'modalsize'));
  }

  protected function modulesToOptions($modules){
    $moduleTypes = array();
    foreach($modules as $t) {
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

  protected function modulesForRootField($model) {
    return $this->model->getBlueprintFields()->get($this->fieldname)->modules()['types'];
  }

  protected function modulesForType($type, $field) {
    $blueprint = $this->blueprintForType($type);

    if(isset($blueprint)){
      return $blueprint['fields'][$field]['modules']['types'];
    }
  }

  protected function blueprintForType($type) {
    if($path = $this->blueprintPath($type)){
      return yaml::decode(f::read($path));
    }
  }

  protected function blueprintPath($type){
    $extensions = array('.php', '.yml', '.yaml');
    foreach($extensions as $ext) {
      $path = implode(DS, array(kirby()->roots()->blueprints(),
                                'modules',
                                $type . $ext));
      if(file_exists($path)) return $path;
    }
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
}
