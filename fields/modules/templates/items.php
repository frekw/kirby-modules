<?php
$form = require implode(DS, array(__DIR__, 'form.php'));

foreach($field->entries() as $i => $entry):
  $state = isset($entry->_editor_state) ? $entry->_editor_state : array('active_tab' => '', 'collapsed' => '');
?>

<div class="modules-entry" id="modules-entry-<?php echo $entry->id() ?>">
  <h4 class="modules-type accordion-toggle<?php e($state['collapsed'] === 'true', ' accordion--closed') ?>"><?php echo ucfirst($entry->type()); ?> Module</h4>
    <div class="accordion-content">

    <?php if($form('has-options', $entry, $field)): ?>
    <ul class="tabs">
      <li><a href="#modules-entry-fields-<?php echo $entry->id() ?>" <?php e($state['active_tab'] !== 'options', 'data-active="data-active"') ?>">Content</a></li>
      <li><a href="#modules-entry-options-<?php echo $entry->id() ?>" <?php e($state['active_tab'] === 'options', 'data-active="data-active"') ?>">Settings</a></li>
    </ul>
    <?php endif; ?>

    <?php if(!$field->readonly()): ?>
      <a data-modal
         class="btn btn-icon btn-rounded btn-negative modules-entry-delete"
         href="<?php __($field->url('delete') . '/' . $entry->id()) ?>">
      <?php i('trash-o', 'left') . _l('fields.structure.delete') ?>
    </a>
    <?php endif; ?>

    <div class="modules-entry-content">
      <?php if(!$field->readonly()): ?>
        <div class="modules-entry-fields" id="modules-entry-fields-<?php echo $entry->id() ?>">
          <?php $form('fields', $entry, $field); ?>
        </div>

        <?php if($form('has-options', $entry, $field)): ?>
          <div class="modules-entry-options" id="modules-entry-options-<?php echo $entry->id() ?>">
            <?php $form('options', $entry, $field); ?>
          </div>
        <?php endif; ?>
      <?php $form('editor-state', $entry, $field); ?>
      <?php require implode(DS, array(__DIR__, 'form.php')); ?>
     <?php endif ?>
  </div>
  </div>
</div>
<?php endforeach ?>
