<?php

use Ynamite\DndSorter;

$table_name = $this->getVar('table_name', '');

if (!$table_name) return;

$DndSorter = new DndSorter($table_name);
if (!$DndSorter) return;


$editMode = $this->getVar('editMode', true);

$action = $this->getVar('action', 'sort-entries');
if ($action === 'sort-media') {
  $config = $DndSorter->getValue('mediaConfig');
} else {
  $config = $DndSorter->getValue('config');
}

$heading = isset($config['heading']) ? $config['heading'] : 'Anordnen';
$labelCol = isset($config['labelCol']) ? $config['labelCol'] : 'name';
$imgCol = isset($config['imgCol']) ? $config['imgCol'] : 'preview_img';
$numCols = isset($config['numCols']) ? $config['numCols'] : 12;
$defaultCols = isset($config['defaultCols']) ? $config['defaultCols'] : 4;
$maxCols = isset($config['maxCols']) ? $config['maxCols'] : 12;
$minCols = isset($config['minCols']) ? $config['minCols'] : $defaultCols;
$colSizeStep = isset($config['colSizeStep']) ? $config['colSizeStep'] : 2;
$collection = $this->getVar('collection', []);

?>

<h3 class="massif-dnd-heading"><?= $heading ?></h3>
<br />
<div class="massif-dnd-wrapper" style="--massif-dnd-num-cols: <?= $numCols ?>; --massif-dnd-default-cols: <?= $defaultCols ?>">
  <div class="input-group" id="js-grid-container">
    <ul class="massif-dnd grid-list">
      <?php
      foreach ($collection as $dataset) {
        $filename = '';
        if ($dataset->{$imgCol}) {
          $imgArray = explode(',', $dataset->{$imgCol});
          $filename = $imgArray[0];
          $img = class_exists('massif_img') ? massif_img::get($filename, ['loading' => 'eager']) : '<img src="' . rex_url::media($filename) . '" />';
        } else {
          $img = '<div class="img-placeholder"></div>';
        }
        $label = isset($dataset->{$labelCol}) ? $dataset->{$labelCol} : $filename;
      ?>
        <li class="list-entry project-entry" data-prio="<?= $dataset->prio ?>" data-id="<?= $dataset->getId() ?>" data-cols="<?= $dataset->cols ?>">
          <div class="thumbnail">
            <div class="label-wrapper">
              <?php if ($label) { ?>
                <div class="label"><?= $label ?></div>
              <?php } ?>
              <?php if ($editMode) { ?>
                <div class="controls">
                  <div data-decrease title="Spalte verkleinern">
                    <i class="rex-icon fa-circle-minus"></i>
                  </div>
                  <div data-increase title="Spalte vergrössern">
                    <i class="rex-icon fa-circle-plus"></i>
                  </div>
                  <div class="dragdrop-handle" title="Drag & Drop">
                    <i class="rex-icon fa-up-down-left-right"></i>
                  </div>
                </div>
              <?php } ?>
            </div>
            <?= $img ?>
          </div>
        </li>
      <?php } ?>
    </ul>
  </div>
  <input name="massif_dnd_sorter_input" type="hidden" value="" />

</div>
<script>
  <?php
  if ($editMode) {
    echo rex_file::get($DndSorter->getAddon()->getAssetsPath('js/Sortable.1.15.6.min.js'));
  }
  ?>
</script>
<script>
  (async function() {

    const numCols = <?= $numCols ?>;
    const minCols = <?= $minCols ?>;
    const maxCols = <?= $maxCols ?>;
    const colSizeStep = <?= $colSizeStep ?>;
    const massifDnd = document.querySelector('.massif-dnd-wrapper');
    const grid = massifDnd.querySelector('.massif-dnd');

    const projectsContentInput = massifDnd.querySelector('input[name="massif_dnd_sorter_input"]');


    const buildGrid = () => {
      const container = document.querySelector('#js-grid-container');
      if (!container) return;

      const wrapper = document.createElement('div');
      wrapper.classList.add('js-grid-wrapper');
      container.appendChild(wrapper);

      for (let i = 1; i <= numCols; i++) {
        const col = document.createElement('div');
        col.classList.add('js-grid-col');
        col.innerHTML = `<div class="js-grid-col-label">${i}</div>`;
        wrapper.appendChild(col);
      }
    }

    const updateResult = () => {
      let projectContents = [];
      [...grid.children].forEach((el, index) => {
        let prio = index + 1;
        projectContents.push({
          "id": el.dataset.id,
          "prio": prio,
          "cols": el.dataset.cols
        });
      });

      projectsContentInput.value = JSON.stringify(projectContents);

    }

    const handleResize = (entry, cols) => {
      const btnDecrease = entry.querySelector('[data-decrease]');
      const btnIncrease = entry.querySelector('[data-increase]');
      cols = Math.max(minCols, Math.min(maxCols, cols));
      entry.dataset.cols = cols;
      entry.style.gridColumn = `span ${cols}`;
      updateResult();

      const controls = entry.querySelector('.controls');
      if (!controls) return;

      if (cols <= minCols) {
        btnDecrease.classList.add('disabled');
      } else {
        btnDecrease.classList.remove('disabled');
      }
      if (cols >= maxCols) {
        btnIncrease.classList.add('disabled');
      } else {
        btnIncrease.classList.remove('disabled');
      }
    }

    const handleControls = () => {
      [...grid.children].forEach((entry) => {
        const cols = parseInt(entry.dataset.cols);
        handleResize(entry, cols);
      })
      grid.addEventListener('click', function(e) {
        const target = e.target;
        const btnDecrease = target.closest('[data-decrease]');
        const btnIncrease = target.closest('[data-increase]');
        const entry = target.closest('.project-entry');
        if (btnDecrease) {
          const cols = parseInt(entry.dataset.cols) - colSizeStep;
          handleResize(entry, cols);
        }

        if (btnIncrease) {
          const cols = parseInt(entry.dataset.cols) + colSizeStep;
          handleResize(entry, cols);
        }
      });
    }

    async function init() {
      buildGrid();
      handleControls();
      updateResult();

      if (typeof Sortable === 'undefined') {
        return;
      }
      Sortable.create(grid, {
        group: {
          name: 'project',
          pull: false,
          put: true
        },
        animation: 100,
        onEnd: updateResult,
        handle: '.dragdrop-handle'
      });


    }

    init();

  })();
</script>