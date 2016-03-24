<?php
$form = require implode(DS, array(__DIR__, 'form.php'));

foreach($field->entries() as $i => $entry): ?>

<div class="modules-entry" id="modules-entry-<?php echo $entry->id() ?>">
  <h4 class="modules-type"><?php echo ucfirst($entry->type()); ?> Module</h4>
  <ul class="tabs">
    <li><a href="#modules-entry-fields-<?php echo $entry->id() ?>">Content</a></li>
    <li><a href="#modules-entry-options-<?php echo $entry->id() ?>">Settings</a></li>
  </ul>
  <?php if(!$field->readonly()): ?>
  <a data-modal class="btn btn-icon btn-rounded btn-negative modules-entry-delete" href="<?php __($field->url($entry->id() . '/delete')) ?>">
    <?php i('trash-o', 'left') . _l('fields.structure.delete') ?>
  </a>
  <?php endif; ?>

  <div class="modules-entry-content">
    <?php if(!$field->readonly()): ?>
      <div class="modules-entry-fields" id="modules-entry-fields-<?php echo $entry->id() ?>"><?php $form('fields', $entry, $field); ?></div>
<div class="modules-entry-options" id="modules-entry-options-<?php echo $entry->id() ?>"><?php $form('options', $entry, $field); ?></div>
    <?php require implode(DS, array(__DIR__, 'form.php')); ?>
    <?php endif ?>
  </div>
</div>
<?php endforeach ?>