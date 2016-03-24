<div class="modules <?php e($field->readonly(), ' modules-readonly') ?>"
  data-field="modules"
  data-api="<?php __($field->url('sort')) ?>"
  data-sortable="<?php e($field->readonly(), 'false', 'true') ?>">

  <?php echo $field->headline() ?>

  <div class="modules-entries">

    <?php if(!$field->entries()->count()): ?>
    <div class="modules-empty">
      <p>
        No modules yet.
        <?php if(!$field->reaonly()): ?>
        <br />
        <a data-modal class="modules-empty-add-button btn btn-rounded" href="<?php __($field->url('add')) ?>">+ Add module</a>
      </p>
    <?php endif; ?>
    </div>
    <?php else: ?>
    <?php require implode(DS, array(__DIR__, 'templates', 'items.php')); ?>

    <div class="modules-actions">
      <a data-modal class="btn btn-rounded modules-add-button" href="<?php echo $field->url('add'); ?>">+ Add Module</a>
    </div>
    <?php endif ?>
  </div>
</div>
