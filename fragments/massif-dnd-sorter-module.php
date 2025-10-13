<?php

use Ynamite\DndSorter\DndSorter;

$valueId = $this->getVar('valueId', 1);
$values = $this->getVar('values', '');
$editMode = $this->getVar('editMode', true);

$heading = $this->getVar('heading', 'Anordnen');
$labelCol = $this->getVar('labelCol', 'name');
$imgCol = $this->getVar('imgCol', 'preview_img');
$numCols = $this->getVar('numCols', 12);
$defaultCols = $this->getVar('defaultCols', 4);
$maxCols = $this->getVar('maxCols', 12);
$minCols = $this->getVar('minCols', $defaultCols);
$colSizeStep = $this->getVar('colSizeStep', 2);
$data = $this->getVar('data', []);

$data = DndSorter::sortDataByIds($data, $values);
?>

<h3 class="massif-dnd-heading"><?= $heading ?></h3>
<br />
<div class="massif-dnd-wrapper" style="--massif-dnd-num-cols: <?= $numCols ?>; --massif-dnd-default-cols: <?= $defaultCols ?>">
  <div class="input-group" id="js-grid-container">
    <masonry-list class="grid-list massif-dnd">
      <?php
      foreach ($data as $item) {
        $filename = '';
        if ($item[$imgCol]) {
          $imgArray = explode(',', $item[$imgCol]);
          $filename = $imgArray[0];
          $img = class_exists('Ynamite\Massif\Media\Image') ? Ynamite\Massif\Media\Image::get(src: $filename, loading: 'eager', maxWidth: 500) : '<img src="' . rex_url::media($filename) . '" />';
        } else {
          $img = '<div class="img-placeholder"></div>';
        }
        $label = isset($item[$labelCol]) ? $item[$labelCol] : $filename;
      ?>
        <masonry-item class="list-entry project-entry" data-id="<?= $item['id'] ?>" data-cols="<?= $item['cols'] ?? 4 ?>">
          <div class="thumbnail">
            <div class="label-wrapper">
              <?php if ($label) { ?>
                <div class="label"><?= $label ?></div>
              <?php } ?>
              <?php if ($editMode) { ?>
                <div class="controls">
                  <?php if ($minCols < $maxCols) { ?>
                    <div data-decrease title="Spalte verkleinern">
                      <i class="rex-icon fa-circle-minus"></i>
                    </div>
                    <div data-increase title="Spalte vergrössern">
                      <i class="rex-icon fa-circle-plus"></i>
                    </div>
                  <?php } ?>
                  <div class="dragdrop-handle" title="Drag & Drop">
                    <i class="fa-up-down-left-right rex-icon"></i>
                  </div>
                </div>
              <?php } ?>
            </div>
            <?= $img ?>
          </div>
          <?php if ($item['description'] ?? false) { ?>
            <div class="description">
              <?= $item['description'] ?>
            </div>
          <?php } ?>
        </masonry-item>
      <?php } ?>
    </masonry-list>
  </div>
  <input name="REX_INPUT_VALUE[<?= $valueId ?>]" type="hidden" value="" />

</div>
<script>
  <?php
  if ($editMode) {
    echo rex_file::get(rex_addon::get('massif_dnd_sorter')->getAssetsPath('Sortable.1.15.6.min.js'));
  }
  ?>
</script>
<style>
  masonry-list {
    --cols: 3;
    --gap: clamp(0.625rem, -0.035211rem + 2.816901vw, 2.5rem);

    display: grid;
    grid-template-columns: repeat(var(--cols), 1fr);
    grid-auto-flow: dense;
    gap: var(--gap);
    /* correct last item adding extra size when masonry is patched */
    margin-block-end: calc(var(--gap) * -1px);

  }

  masonry-item {
    align-self: start;
  }
</style>
<script>
  const getCustomPropertyValue = (target, propName) => {
    const probe = document.createElement('div')
    probe.style.cssText = `
    position:fixed;visibility:hidden;contain:strict;pointer-events:none;
    padding-left: var(${propName});
  `
    target.appendChild(probe)
    const px = parseFloat(getComputedStyle(probe).paddingLeft)
    target.removeChild(probe)
    return px
  }
  class MasonryList extends HTMLElement {
    #patched = false
    #observer = null

    get patched() {
      return this.#patched
    }

    #layout = () => {
      const rowGap = getCustomPropertyValue(this, '--gap')
      this.style.setProperty('--masonry-list-row-gap', `${Math.round(rowGap)}`)
    }

    connectedCallback() {
      const style = getComputedStyle(this)
      if (style.gridTemplateRows === 'masonry') return
      this.#patched = true

      this.style.gridAutoRows = '0px'
      this.style.setProperty('row-gap', '1px', 'important')

      this.#observer = new ResizeObserver(this.#layout)
      this.#observer.observe(this)
      this.#layout()
    }

    disconnectedCallback() {
      this.#observer?.disconnect()
    }
  }

  class MasonryItem extends HTMLElement {
    #observer = null

    #layout = () => {
      const {
        height
      } = this.getBoundingClientRect()
      this.style.gridRowEnd = `span calc(${Math.round(
      height
    )} + var(--masonry-list-row-gap))`
    }

    connectedCallback() {
      const masonry = this.closest('masonry-list')
      if (!masonry?.patched) return

      this.#observer = new ResizeObserver(this.#layout)
      this.#observer.observe(this)
      this.#layout()
    }

    disconnectedCallback() {
      this.#observer?.disconnect()
    }
  }

  if (typeof window !== 'undefined') {
    customElements.define('masonry-list', MasonryList)
    customElements.define('masonry-item', MasonryItem)
  }
</script>
<script>
  (async function() {

    const numCols = <?= $numCols ?>;
    const minCols = <?= $minCols ?>;
    const maxCols = <?= $maxCols ?>;
    const colSizeStep = <?= $colSizeStep ?>;
    const massifDnd = document.querySelector('.massif-dnd-wrapper');
    const grid = massifDnd.querySelector('.massif-dnd');

    const projectsContentInput = massifDnd.querySelector('input[name="REX_INPUT_VALUE[<?= $valueId ?>]"]');


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
        projectContents.push(el.dataset.id);
      });

      projectsContentInput.value = projectContents.join(',');

    }

    const handleResize = (entry, cols) => {
      const btnDecrease = entry.querySelector('[data-decrease]');
      const btnIncrease = entry.querySelector('[data-increase]');
      cols = Math.max(minCols, Math.min(maxCols, cols));
      entry.dataset.cols = cols;
      entry.style.gridColumn = `span ${cols}`;
      updateResult();

      const controls = entry.querySelector('.controls');
      if (!controls || !btnDecrease || !btnIncrease) return;

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