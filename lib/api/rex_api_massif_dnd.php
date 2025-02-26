<?php

use Ynamite\DndSorter;

class rex_api_massif_dnd extends rex_api_function
{

  protected $published = true;

  protected $action = '';
  protected $table_name = '';
  protected $main_table_name = '';
  protected $data_id = 0;
  protected $save = false;
  protected $save_msg = '';
  protected $params = [];
  protected $DndSorter = null;

  function execute()
  {

    $this->action = rex_request('action', 'string', '');
    $this->table_name = rex_request('table_name', 'string', '');
    $this->data_id = rex_request('data_id', 'int', 0);
    $this->save = rex_post('massif-dnd-sorter-modal-save', 'int', 0) ? true : false;

    $this->DndSorter = new DndSorter($this->table_name);
    if (!$this->DndSorter) return;


    $this->params = [
      'action' => $this->action,
      'table_name' => $this->table_name,
      'data_id' => $this->data_id,
      'reloadWindowOnClose' => false,
    ];

    $this->handleSave();

    switch ($this->action) {
      case 'sort-entries':
        $this->sortEntries();
        break;
      case 'sort-media':
        $this->sortMedia();
        break;
    }

    if ($this->save) {
      $this->params['save_msg'] = $this->save_msg;
    }

    echo DndSorter::parse('be-massif-dnd-sorter-modal', $this->params);

    exit();
  }

  protected function sortEntries(): void
  {
    $this->save_msg = 'Projekte-Anordnung gespeichert!';
    $this->params['reloadWindowOnClose'] = true;
    $this->params['collection'] = rex_yform_manager_table::get($this->table_name)->query()->where('status', 1)->find();
  }

  protected function sortMedia(): void
  {

    $mediaConfig = $this->DndSorter->getValue('mediaConfig');
    $this->save_msg = 'Medien-Anordnung gespeichert!';
    $this->params['collection'] = rex_yform_manager_table::get($mediaConfig['table_name'])->query()->where($mediaConfig['relation_column'], $this->data_id, '=')->find();
  }

  protected function handleSave(): void
  {

    if ($this->save) {

      $saveTable = $this->action === 'sort-entries' ? $this->table_name : $this->DndSorter->getValue('mediaConfig')['table_name'];

      $entries = rex_post('massif_dnd_sorter_input', 'string', '') ? rex_var::toArray(rex_post('massif_dnd_sorter_input', 'string', '')) : [];

      foreach ($entries as $data) {
        $entry = rex_yform_manager_dataset::get($data['id'], $saveTable);
        $entry->cols = $data['cols'];
        $entry->prio = $data['prio'];
        $entry->save();
      }
    }
  }
}
