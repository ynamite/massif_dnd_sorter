<?php
$action = $this->getVar('action', 'sort-entries');
$isDataListHeader = $action === 'sort-entries';
$data_id = $this->getVar('data_id', 0);
$table_name = $this->getVar('table_name', '');
$reloadWindowOnClose = $this->getVar('reloadWindowOnClose', false);
$label = $this->getVar('label', 'Anordnen');
?>
<div class="btn-group massif-dnd-btn-group<?php if ($isDataListHeader) echo ' yform-data-list-header'; ?>">
  <div class="action-buttons">
    <button type="button" class="btn btn-primary" data-massif-dnd-modal="#modal-massif-dnd" data-action="<?= $action ?>" data-id="<?= $data_id ?>" data-table-name="<?= $table_name ?>" <?php if ($reloadWindowOnClose) echo ' data-reload-window-on-close="true"'; ?>>
      <?= $label ?>
    </button>
  </div>
</div>