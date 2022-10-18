<?php

// @codingStandardsIgnoreFile

use Drupal\Core\Installer\InstallerKernel;

$databases = [];

/**
 * Salt for one-time login links, cancel links, form tokens, etc.
 */
$settings['hash_salt'] = 'dc5vs0xcO_QgXlwZhf3N6XoNdF5jOpjdyC7jLhIUSCc7Wpi1xLlb0YtryxrgakKsltwdw7OiUQ';

/**
 * Access control for update.php script.
 */
$settings['update_free_access'] = FALSE;

/**
 * Load services definition file.
 */
$settings['container_yamls'][] = $app_root . '/' . $site_path . '/services.yml';

/**
 * The default list of directories that will be ignored by Drupal's file API.
 */
$settings['file_scan_ignore_directories'] = [
  'node_modules',
  'bower_components',
];

/**
 * The default number of entities to update in a batch process.
 */
$settings['entity_update_batch_size'] = 50;

/**
 * Entity update backup.
 */
$settings['entity_update_backup'] = TRUE;

/**
 * Node migration type.
 */
$settings['migrate_node_migrate_type_classic'] = FALSE;

/**
 * Disable all config splits by default; they'll be enabled by
 * their respective environments
 */
$config['config_split.config_split.loc']['status'] = FALSE;
$config['config_split.config_split.dev']['status'] = FALSE;
$config['config_split.config_split.uat']['status'] = FALSE;
$config['config_split.config_split.stg']['status'] = FALSE;
$config['config_split.config_split.prd']['status'] = FALSE;


/**
 * Lando environment settings
 */
if (getenv('LANDO_INFO')) {
  $lando_info = json_decode(getenv('LANDO_INFO'), TRUE);

  // Files directory paths.
  $settings['file_public_path'] = 'sites/default/files';
  $settings['file_private_path'] = 'sites/default/files/private';
  $settings['config_sync_directory'] = '/app/config/common';
  $settings['file_temp_path'] = $_ENV['TEMP'];

  // Generic hash salt for all local environments.
  $settings['hash_salt'] = 'BfHE?EG)vJPa3uikBCZWW#ATbDLijMFRZgfkyayYcZYoy>eC7QhdG7qaB4hcm4x$';

  // Allow any domains to access the site with Lando.
  $settings['trusted_host_patterns'] = [
    '^(.+)$',
  ];

  // Enable Configuration Read-only Mode (Only on Prod & UAT)
  if (PHP_SAPI !== 'cli') {
    $settings['config_readonly'] = TRUE;
  }

  // Config sync directory for Lando.
  $settings['config_sync_directory'] = '../config/common';

  // Add default config split settings for local development.
  $config['config_split.config_split.loc']['status'] = TRUE;
  $config['config_split.config_split.dev']['status'] = FALSE;
  $config['config_split.config_split.uat']['status'] = FALSE;
  $config['config_split.config_split.stg']['status'] = FALSE;
  $config['config_split.config_split.prd']['status'] = FALSE;

  $config['system.site']['uuid'] = 'f6953ac3-11b6-48d3-ab35-bb0995c5a72f';

  $databases['default']['default'] = [
    'database' => $lando_info['database']['creds']['database'],
    'username' => $lando_info['database']['creds']['user'],
    'password' => $lando_info['database']['creds']['password'],
    'prefix' => '',
    'host' => $lando_info['database']['internal_connection']['host'],
    'port' => $lando_info['database']['internal_connection']['port'],
    'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
    'driver' => 'mysql',
  ];

  // Check for PHP Memcached libraries.
  $memcache_exists = class_exists('Memcache', FALSE);
  $memcached_exists = class_exists('Memcached', FALSE);
  $memcache_module_is_present = file_exists(DRUPAL_ROOT . '/modules/contrib/memcache/memcache.services.yml');
  if ($memcache_module_is_present && ($memcache_exists || $memcached_exists)) {
    $settings['memcache']['servers'] = ['cache:11211' => 'default'];
    $settings['memcache']['bins'] = ['default' => 'default'];
    $settings['memcache']['key_prefix'] = 'site_prefix_';

    if (!InstallerKernel::installationAttempted()) {
      $settings['cache']['default'] = 'cache.backend.memcache';
    }
  }
}

/**
 * Load local development override configuration, if available.
 */
#
# if (file_exists($app_root . '/' . $site_path . '/settings.local.php')) {
#   include $app_root . '/' . $site_path . '/settings.local.php';
# }
