<?php
$id = $this->getVar('id', 'modal-massif');
$content = $this->getVar('content', '');
?>
<!-- Modal -->
<div class="modal fade" id="<?= $id ?>" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Schliessen">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body"><?= $content ?></div>
    </div>
  </div>
</div>