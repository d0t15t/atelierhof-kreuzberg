<?php

namespace Drupal\ak_theme\StringTranslation;

use Drupal\Core\StringTranslation\Translator\TranslatorInterface;

/**
 * Serves German for the core and contrib strings visible on the site.
 *
 * The site runs in English (its path aliases are stored as English, and
 * switching the default language would stop them resolving), but all of its
 * content is German. Rather than install locale, the few interface strings
 * that reach visitors and editors are translated here.
 */
class GermanInterfaceStrings implements TranslatorInterface {

  /**
   * English source string => German, keyed by context ('' for none).
   */
  const STRINGS = [
    '' => [
      'Home' => 'Startseite',
      'Skip to main content' => 'Direkt zum Inhalt',
      'Breadcrumb' => 'Pfadnavigation',
      'Read more' => 'Weiterlesen',
      'Read more<span class="visually-hidden"> about @title</span>' => 'Weiterlesen<span class="visually-hidden"> über @title</span>',
      'Subscribe to @title' => '@title abonnieren',
      'Page not found' => 'Seite nicht gefunden',
      'The requested page could not be found.' => 'Die angeforderte Seite konnte nicht gefunden werden.',
      'Access denied' => 'Zugriff verweigert',
      'You are not authorized to access this page.' => 'Sie haben keine Berechtigung, diese Seite aufzurufen.',
      // Pager.
      'Pagination' => 'Seitennavigation',
      'Page' => 'Seite',
      'Current page' => 'Aktuelle Seite',
      'Go to page @key' => 'Zu Seite @key',
      'First page' => 'Erste Seite',
      'Go to first page' => 'Zur ersten Seite',
      '« First' => '« Erste',
      'Previous page' => 'Vorherige Seite',
      'Go to previous page' => 'Zur vorherigen Seite',
      '‹ Previous' => '‹ Zurück',
      'Next page' => 'Nächste Seite',
      'Go to next page' => 'Zur nächsten Seite',
      'Next ›' => 'Weiter ›',
      'Last page' => 'Letzte Seite',
      'Go to last page' => 'Zur letzten Seite',
      'Last »' => 'Letzte »',
      // Tabs editors see on the front end.
      'Tabs' => 'Reiter',
      'Primary tabs' => 'Primäre Reiter',
      'Secondary tabs' => 'Sekundäre Reiter',
      'Toggle tabs as a menu' => 'Reiter als Menü umschalten',
    ],
  ];

  /**
   * {@inheritdoc}
   */
  public function getStringTranslation($langcode, $string, $context) {
    return self::STRINGS[$context][$string] ?? FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function reset() {}

}
