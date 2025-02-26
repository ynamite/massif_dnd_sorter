# DndSorter – für REDAXO yform ⬆️⬆️⬇️⬇️⬅️➡️⬅️➡️🅑🅐

Ein visuelles Drag&Drop-Addon für Redaxo CMS, das dir ermöglicht, yform-Datensätze mit Bildern in einem anpassbaren Grid zu sortieren – perfekt für Portfolio-Seiten und mehr!

![poster](https://github.com/user-attachments/assets/86b76da4-558f-46c5-b71e-888a63be2c7e)

## Features

- **Drag & Drop Sortierung:** Sortiere YForm-Datensätze und inline Relationen (ebenfalls mit Bildern) per Drag & Drop.
- **Anpassbares Grid:** Stelle je Tabelle die Anzahl der Spalten, maximale Spaltenbreite pro Element usw. ein.
- **Konfiguration über YAML:** Konfiguriere die Anzeige via package.yml (aktuell ohne Backend-Einstellungsseiten).
- **Inline-Relationen:** Unterstützt auch das Sortieren von inline Relationen mit Bildern.

[screen cap](https://github.com/user-attachments/assets/936a3c8c-2cb5-43a6-9a3a-baccf8474f69)

## Voraussetzungen

- Redaxo CMS mit installiertem YForm Addon.
- Tabellen müssen folgende Felder enthalten:
  - Eine Spalte `cols` als `tinyint` (für die Anzahl der Spalten)
  - Ein Prioritätsfeld `prio` als `int`

## Installation

1. Clone das Repository in deinen Redaxo Addon-Ordner.
2. Passe die package.yml entsprechend deinen Tabellen und Bedürfnissen an.
3. Aktiviere das Addon im Redaxo Backend.

## Konfiguration

```yaml
config:
  rex_yf_project:
    imgCol: "preview_img"
    defaultCols: 4
    maxCols: 8
    colSizeStep: 2
    labelCol: "name"
    heading: "Projekte anordnen"
    sortMedia:
      table_name: rex_yf_project_media
      relation_column: "id_project"
      imgCol: "images"
      defaultCols: 6
      maxCols: 12
      colSizeStep: 6
      heading: "Medien anordnen"
```

## Hinweise

- **Alpha-Status:** Das Addon befindet sich aktuell noch in einer frühen (Alpha-)Version.
- **Einschränkungen:** Beim Drag & Drop werden keine Offsets (column-start) unterstützt – die Elemente fließen von links nach rechts, ähnlich wie Element in einer Imagelist.
- **Anwendungsfall:** Ideal für Portfolio-Seiten, wo Kunden sowohl die Übersicht als auch innerhalb eines Portfolio-Eintrags die Bildanordnung selbst gestalten möchten.

---

Wenn du das Addon in deinem Projekt einsetzen möchtest oder Feedback hast, melde dich gerne!

Viel Spaß beim Sortieren und Anpassen deiner YForm-Datensätze!
