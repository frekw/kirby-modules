<?php
$form = require implode(DS, array(__DIR__, 'form.php'));

foreach($field->entries() as $i => $entry):
  $state = isset($entry->_editor_state) ? $entry->_editor_state : array('active_tab' => '', 'collapsed' => '');
?>

<div class="modules-entry" id="modules-entry-<?php echo $entry->id() ?>">
  <div class="modules-header">
    <h4 class="modules-type"><?php echo ucfirst($entry->type()); ?> Module</h4>
    <ul class="tabs">
      <li class="tab"><a href="#modules-entry-fields-<?php echo $entry->id() ?>"><?php i('pencil') ?></a></li>
      <?php if($form('has-options', $entry, $field)): ?>
      <li class="tab"><a href="#modules-entry-options-<?php echo $entry->id() ?>"><?php i('cog') ?></a></li>
      <?php endif; ?>
      <?php if(!$field->readonly()): ?>
        <li>
          <a data-modal
             class="modules-entry-delete"
             href="<?php __($field->url('delete') . '/' . $entry->id()) ?>">
            <?php i('trash') ?>
          </a>
        </li>
      <?php endif; ?>
    </ul>
  </div>

  <div class="accordion-content">
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
