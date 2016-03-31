<?php
$form = require implode(DS, array(__DIR__, 'form.php'));

foreach($field->entries() as $i => $entry): ?>

<div class="modules-entry" id="modules-entry-<?php echo $entry->id() ?>">
  <h4 class="modules-type accordion-toggle accordion--open"><?php echo ucfirst($entry->type()); ?> Module</h4>
    <div class="accordion-content">

    <?php if($form('hasOptions', $entry, $field)): ?>
    <ul class="tabs">
      <li><a href="#modules-entry-fields-<?php echo $entry->id() ?>">Content</a></li>
      <li><a href="#modules-entry-options-<?php echo $entry->id() ?>">Settings</a></li>
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

        <?php if($form('hasOptions', $entry, $field)): ?>
          <div class="modules-entry-options" id="modules-entry-options-<?php echo $entry->id() ?>">
            <?php $form('options', $entry, $field); ?>
          </div>
        <?php endif; ?>
      <?php require implode(DS, array(__DIR__, 'form.php')); ?>
     <?php endif ?>
  </div>
  </div>
</div>
<?php endforeach ?>