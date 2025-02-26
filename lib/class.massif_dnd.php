<?php

namespace Ynamite;

use rex_fragment;
use rex_sql;
use rex_yform_manager_field;
use rex_addon;
use rex_extension;
use rex_extension_point;

class DndSorter
{

  protected $addon = null;
  protected $config = [];
  protected $mediaConfig = [];
  protected $table_name = '';
  protected $data_id = 0;

  public function __construct(string $table_name)
  {
    $this->addon = rex_addon::get('massif_dnd_sorter');
    $addonConfig = $this->addon->getProperty('config');
    if (!isset($addonConfig[$table_name])) return null;

    $this->config = $addonConfig[$table_name];
    $this->table_name = $table_name;
    $this->init();
  }

  protected function init()
  {

    if (isset($this->config['sortMedia'])) {
      $this->mediaConfig = $this->config['sortMedia'];
    }
    $this->data_id = rex_get('data_id', 'int', 0);
  }

  public function addExtensions()
  {
    rex_extension::register(
      'PAGE_TITLE_SHOWN',
      function (rex_extension_point $ep) {
        $this->addEntriesSort($ep);
      }
    );

    if ($this->mediaConfig) {
      rex_extension::register(
        "YFORM_DATA_LIST",
        function ($ep) {
          $this->addMediaSortButton($ep);
        },
        rex_extension::LATE
      );
    }
  }

  protected function addEntriesSort(rex_extension_point $ep)
  {

    $modal = self::parse('be-bs-modal-massif-dnd', ['id' => 'modal-massif-dnd']);
    $btn = self::getButton([
      'action' => 'sort-entries',
      'table_name' => $this->table_name,
      'label' => isset($this->config['heading']) ? $this->config['heading'] : '',
      'reloadWindowOnClose' => true,
    ]);
    $subject = $ep->getSubject();

    if (!$this->data_id) {
      $subject .= $btn;
    }
    $subject .= $modal;
    $ep->setSubject($subject);
  }

  protected function addMediaSortButton(rex_extension_point $ep)
  {
    $list = $ep->getSubject();
    $list->addColumn($this->mediaConfig['heading'], $this->mediaConfig['heading'], 10);
    $list->setColumnFormat($this->mediaConfig['heading'], 'custom', function ($params) {
      $data_id = $params['list']->getValue('id');
      $sql = rex_sql::factory();
      $sql->setQuery('SELECT id FROM ' . $this->mediaConfig['table_name'] . ' WHERE ' . $this->mediaConfig['relation_column'] . ' = ' . $data_id . ' LIMIT 1');
      if ($sql->getRows()) {
        return self::getButton([
          'action' => 'sort-media',
          'data_id' => $data_id,
          'table_name' => $this->table_name,
          'label' => isset($this->mediaConfig['heading']) ? $this->mediaConfig['heading'] : '',
        ]);
      }
    });
  }

  public function getValue(string $value): array|null
  {
    if (!isset($this->{$value})) return null;
    return $this->{$value};
  }

  public function getAddon(): rex_addon
  {
    return $this->addon;
  }

  public static function parse(string $file, array $vars = []): string
  {
    $fragment = new rex_fragment();
    foreach ($vars as $key => $value) {
      $fragment->setVar($key, $value, false);
    }
    return $fragment->parse($file . ".php");
  }

  // public function getMediaSortConfig(): array
  // {
  //   $relation = self::getSortMediaData($table_name, $mainConfig['relation_column']);
  //   if (!isset($config['sortMedia'][$relation['table_name']])) return [];
  //   $mediaConfig = $config['sortMedia'][$relation['table_name']];
  //   $mediaConfig['table_name'] = $relation['table_name'];
  //   $mediaConfig['column'] = $relation['column'];
  //   return $mediaConfig;
  // }

  public static function getButton(array $params): string
  {
    $btn = self::parse('be-massif-dnd-sorter-button', $params);
    return $btn;
  }

  public static function getSortMediaData(string $tableName, string $column)
  {
    $sql = rex_sql::factory();
    $sql->setTable(rex_yform_manager_field::table());
    $sql->setWhere('`table_name` = :table_name AND `type_name` = :type_name AND `name` = :name', ['table_name' => $tableName, 'type_name' => 'be_manager_relation', 'name' => $column]);
    $sql->select('`table`, `field`');
    if (!$sql->getRows()) {
      return [];
    }
    return ['table_name' => $sql->getValue('table'), 'column' => $sql->getValue('field')];
  }

  // public static function getSortMediaButton(int $data_id, string $table_name, bool $reloadWindowOnClose = false): string
  // {
  //   $mediaConfig = self::getMediaSortConfig($table_name);
  //   return self::getButton([
  //     'action' => 'sort-media',
  //     'data_id' => $data_id,
  //     'table_name' => $mediaConfig['table_name'],
  //     'main_table_name' => $table_name,
  //     'label' => isset($mediaConfig['heading']) ? $mediaConfig['heading'] : '',
  //     'reloadWindowOnClose' => $reloadWindowOnClose,
  //   ]);
  // }
}
