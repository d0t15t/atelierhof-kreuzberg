<?php

/**
 * One-off build for the curated frontpage (node 277). Run with drush php:script.
 */

use Drupal\Core\Entity\Entity\EntityViewDisplay;
use Drupal\Core\Entity\Entity\EntityViewMode;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\layout_builder\SectionComponent;
use Drupal\menu_link_content\Entity\MenuLinkContent;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;
use Drupal\taxonomy\Entity\Vocabulary;

// Status vocabulary.
if (!Vocabulary::load('exhibition_status')) {
  Vocabulary::create([
    'vid' => 'exhibition_status',
    'name' => 'Exhibition status',
    'description' => 'Optional label shown on featured exhibitions, e.g. Upcoming or Ongoing.',
  ])->save();
  foreach (['Upcoming', 'Ongoing'] as $weight => $name) {
    Term::create(['vid' => 'exhibition_status', 'name' => $name, 'weight' => $weight])->save();
  }
}

// Optional status on exhibitions.
if (!FieldStorageConfig::loadByName('node', 'field_status')) {
  FieldStorageConfig::create([
    'field_name' => 'field_status',
    'entity_type' => 'node',
    'type' => 'entity_reference',
    'cardinality' => 1,
    'settings' => ['target_type' => 'taxonomy_term'],
  ])->save();
  FieldConfig::create([
    'field_name' => 'field_status',
    'entity_type' => 'node',
    'bundle' => 'exhibition',
    'label' => 'Status',
    'description' => 'Optional. Shown as a label when the exhibition is featured on the frontpage.',
    'required' => FALSE,
    'settings' => [
      'handler' => 'default:taxonomy_term',
      'handler_settings' => [
        'target_bundles' => ['exhibition_status' => 'exhibition_status'],
        'auto_create' => FALSE,
      ],
    ],
  ])->save();
}

// Featured exhibitions on pages (used by the frontpage).
if (!FieldStorageConfig::loadByName('node', 'field_featured_exhibitions')) {
  FieldStorageConfig::create([
    'field_name' => 'field_featured_exhibitions',
    'entity_type' => 'node',
    'type' => 'entity_reference',
    'cardinality' => -1,
    'settings' => ['target_type' => 'node'],
  ])->save();
  FieldConfig::create([
    'field_name' => 'field_featured_exhibitions',
    'entity_type' => 'node',
    'bundle' => 'page',
    'label' => 'Featured exhibitions',
    'description' => 'Exhibitions shown on this page, in this order. Drag to reorder.',
    'settings' => [
      'handler' => 'default:node',
      'handler_settings' => [
        'target_bundles' => ['exhibition' => 'exhibition'],
        'sort' => ['field' => 'created', 'direction' => 'DESC'],
        'auto_create' => FALSE,
      ],
    ],
  ])->save();
}

$repo = \Drupal::service('entity_display.repository');

// Form widgets.
$repo->getFormDisplay('node', 'exhibition')
  ->setComponent('field_status', ['type' => 'options_select', 'weight' => -1])
  ->save();
$repo->getFormDisplay('node', 'page')
  ->setComponent('field_featured_exhibitions', [
    'type' => 'entity_reference_autocomplete',
    'weight' => 2,
    'settings' => ['match_operator' => 'CONTAINS', 'size' => 60, 'placeholder' => ''],
  ])
  ->save();

// Keep the new fields out of existing displays.
foreach (['default', 'teaser'] as $mode) {
  $display = $repo->getViewDisplay('node', 'exhibition', $mode);
  if (!$display->isNew()) {
    $display->removeComponent('field_status')->save();
  }
}
$page_display = $repo->getViewDisplay('node', 'page', 'default');
$page_display->removeComponent('field_featured_exhibitions')->save();

// "Featured" view mode: the teaser plus the status label.
if (!EntityViewMode::load('node.featured')) {
  EntityViewMode::create([
    'id' => 'node.featured',
    'label' => 'Featured',
    'targetEntityType' => 'node',
  ])->save();
}
if (!EntityViewDisplay::load('node.exhibition.featured')) {
  $teaser = EntityViewDisplay::load('node.exhibition.teaser');
  $featured = $teaser->createCopy('featured');
  $featured->setStatus(TRUE);
  $featured->setComponent('field_status', [
    'type' => 'entity_reference_label',
    'label' => 'hidden',
    'settings' => ['link' => FALSE],
    'weight' => -1,
  ]);
  $featured->save();
}

// Frontpage: pick the currently promoted exhibitions, then swap the view
// blocks for the field.
$front = Node::load(277);
if ($front->get('field_featured_exhibitions')->isEmpty()) {
  $nids = \Drupal::entityQuery('node')
    ->accessCheck(FALSE)
    ->condition('type', 'exhibition')
    ->condition('status', 1)
    ->condition('promote', 1)
    ->sort('field_dates.value', 'DESC')
    ->execute();
  $front->set('field_featured_exhibitions', array_values($nids));
}
$section = $front->get('layout_builder__layout')->getSection(0);
foreach ($section->getComponents() as $uuid => $component) {
  if (str_starts_with($component->getPluginId(), 'views_block:exhibitions_current')) {
    $section->removeComponent($uuid);
  }
}
$has_field = FALSE;
foreach ($section->getComponents() as $component) {
  $has_field = $has_field || $component->getPluginId() === 'field_block:node:page:field_featured_exhibitions';
}
if (!$has_field) {
  $section->appendComponent(new SectionComponent(\Drupal::service('uuid')->generate(), 'content', [
    'id' => 'field_block:node:page:field_featured_exhibitions',
    'label' => 'Aktuell',
    'label_display' => 'visible',
    'provider' => 'layout_builder',
    'context_mapping' => ['entity' => 'layout_builder.entity', 'view_mode' => 'view_mode'],
    'formatter' => [
      'type' => 'entity_reference_entity_view',
      'label' => 'hidden',
      'settings' => ['view_mode' => 'featured'],
      'third_party_settings' => [],
    ],
  ]));
}
$front->save();

// Menu: "Ongoing" becomes "Aktuell"; the "Upcoming" anchor has no section any more.
$link = MenuLinkContent::load(477);
$link->set('title', 'Aktuell')->set('description', 'Aktuelle Ausstellungen im Atelierhof Kreuzberg.')->save();
$link = MenuLinkContent::load(478);
$link->set('enabled', FALSE)->save();

echo "done\n";
