<?php

use Ynamite\DndSorter;

\rex_fragment::addDirectory($this->getPath('fragments'));

if (rex::isBackend() && rex::getUser()) {

    rex_view::addCssFile($this->getAssetsUrl('style.css'));

    $table_name = rex_request('table_name', 'string', '');

    if (!$table_name) return;

    $DndSorter = new DndSorter($table_name);
    if (!$DndSorter->getValue('table_name')) return;

    $DndSorter->addExtensions();

    rex_view::addJsFile($this->getAssetsUrl('scripts.js'));
}
