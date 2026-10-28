<?php

// phpcs:ignoreFile

/**
 * @file
 * Drupal settings for atelierhof-kreuzberg.com.
 *
 * Environment-specific values (database credentials, trusted hosts) belong in
 * settings.local.php, which is not committed. On ddev, settings.ddev.php
 * supplies the database connection.
 */

$databases = [];

// Kept from the former ibase multisite, where the salt was the site name.
// Changing it invalidates sessions, one-time links and image style tokens.
$settings['hash_salt'] = 'ak';

$settings['config_sync_directory'] = '../config/sync';
$settings['file_private_path'] = '../private';
$settings['file_temp_path'] = '../tmp';

$settings['update_free_access'] = FALSE;
$settings['allow_authorize_operations'] = FALSE;
$settings['rebuild_access'] = FALSE;
$settings['file_scan_ignore_directories'] = ['node_modules', 'bower_components'];
$settings['entity_update_batch_size'] = 50;
$settings['entity_update_backup'] = TRUE;
$settings['migrate_node_migrate_type_classic'] = FALSE;

$settings['trusted_host_patterns'] = [
  '^(www\.)?atelierhof-kreuzberg\.com$',
];

// Automatically generated include for settings managed by ddev.
if (getenv('IS_DDEV_PROJECT') == 'true' && file_exists(__DIR__ . '/settings.ddev.php')) {
  include __DIR__ . '/settings.ddev.php';
}

if (getenv('IS_DDEV_PROJECT') == 'true') {
  $settings['trusted_host_patterns'][] = '\.ddev\.site$';
  $settings['container_yamls'][] = $app_root . '/sites/development.services.yml';
  $config['system.performance']['css']['preprocess'] = FALSE;
  $config['system.performance']['js']['preprocess'] = FALSE;
  $settings['cache']['bins']['render'] = 'cache.backend.null';
  $settings['cache']['bins']['dynamic_page_cache'] = 'cache.backend.null';
  $settings['cache']['bins']['page'] = 'cache.backend.null';
  $settings['extension_discovery_scan_tests'] = FALSE;
  $config['system.logging']['error_level'] = 'verbose';
  $settings['config_exclude_modules'] = ['devel', 'stage_file_proxy'];
  $config['stage_file_proxy.settings']['origin'] = 'https://atelierhof-kreuzberg.com';
}

if (file_exists(__DIR__ . '/settings.local.php')) {
  include __DIR__ . '/settings.local.php';
}

// Drupal 11 recommends READ COMMITTED for MySQL/MariaDB.
if (!empty($databases['default']['default'])) {
  $databases['default']['default']['init_commands']['isolation_level'] = 'SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED';
}
