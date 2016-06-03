<?php
$form = require implode(DS, array(__DIR__, 'form.php'));

foreach($field->entries() as $i => $entry):
  $moduleName = ucfirst(str_replace('-', ' ', $entry->type()));
  if (!empty($entry->module_name)) $moduleName .= ' - ' . $entry->module_name;
?>

<div class="modules-entry" id="modules-entry-<?php echo $entry->id() ?>">
  <div class="modules-header">
    <h4 class="modules-type"><?= esc($moduleName) ?></h4>
    <ul class="tabs">
      <li class="tab"><a href="#modules-entry-fields-<?php echo $entry->id() ?>"><?php i('pencil') ?></a></li>
      <?php if($form('has-options', $entry, $field)): ?>
        <li class="tab"><a href="#modules-entry-options-<?php echo $entry->id() ?>"><?php i('cog') ?></a></li>
      <?php endif; ?>
        <li><a data-modal
          class="modules-entry-rename"
          title="Rename module"
          href="<?php __($field->url('rename') . '/' . $entry->id()) ?>">
          <?php i('i-cursor') ?>
        </a></li>
      <?php if(!$field->readonly()): ?>
        <li><a data-modal
          class="modules-entry-delete"
          title="Delete module"
          href="<?php __($field->url('delete') . '/' . $entry->id()) ?>">
          <?php i('trash') ?>
        </a></li>
      <?php endif; ?>
    </ul>
  </div>

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
    <?php $form('metadata', $entry, $field); ?>
    <?php endif ?>
  </div>
</div>
<?php endforeach ?>
