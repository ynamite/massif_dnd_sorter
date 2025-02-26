<?php

use Ynamite\DndSorter;

$table_name = $this->getVar('table_name', '');
$action = $this->getVar('action', '');
$data_id = $this->getVar('data_id', 0);
$collection = $this->getVar('collection', []);
$save_msg = $this->getVar('save_msg', '');

$queryParams = [
  'table_name' => $table_name,
];
?>
<div id="form-massif-dnd-sorter" data-pjax-container="#form-massif-dnd-sorter">
  <form enctype="multipart/form-data" method="post" action="<?= rex_url::backendPage('yform/manager/data_edit', $queryParams) ?>">
    <input name="rex-api-call" type="hidden" value="massif_dnd" />
    <input name="action" type="hidden" value="<?= $action ?>" />
    <input name="table_name" type="hidden" value="<?= $table_name ?>" />
    <input name="data_id" type="hidden" value="<?= $data_id ?>" />

    <?php
    echo DndSorter::parse('massif-dnd-sorter', ['collection' => $collection, 'table_name' => $table_name, 'action' => $action]);
    ?>

    <div class="btn-group massif-dnd-btn-group">
      <?php
      if ($save_msg) {
        echo rex_view::info($save_msg, 'auto-hide');
      }
      ?>
      <div class="action-buttons">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Schliessen</button>
        <button type="submit" name="massif-dnd-sorter-modal-save" class="btn btn-primary" value="1">Speichern</button>
      </div>
    </div>
  </form>
</div>