<?php

namespace Drupal\my_devel;

use GuzzleHttp\ClientInterface;

/**
 * Provides helpers for interacting with caches.
 */
class CacheHelper {

  /**
   * An HTTP client.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  private $httpClient;

  /**
   * Creates a new CacheHelper.
   *
   * @param \GuzzleHttp\ClientInterface $http_client
   *   An HTTP client.
   */
  public function __construct(ClientInterface $http_client) {
    $this->httpClient = $http_client;
  }

  /**
   * Clears all caches.
   */
  public function clearAll() {
    // Remove Drupal's error and exception handlers, which rely on a working
    // service container and other subsystems and will cause irrelevant errors.
    restore_error_handler();
    restore_exception_handler();

    drupal_flush_all_caches();
    $this->clearApc();

    // Restore Drupal's error and exception handlers.
    // @see \Drupal\Core\DrupalKernel::boot()
    set_error_handler('_drupal_error_handler');
    set_exception_handler('_drupal_exception_handler');
  }

  /**
   * Clears the APC cache.
   *
   * Resets the APC class loader. Helpful when class file changes (e.g.,
   * renames) don't seem to take effect.
   */
  public function clearApc() {
    if (function_exists('is_acquia_host') && is_acquia_host()) {
      $this->clearApcOnAcquiaHosting();
    }

    if (function_exists('apcu_clear_cache')) {
      apcu_clear_cache();
    }
    elseif (function_exists('apc_clear_cache')) {
      apc_clear_cache('user');
    }
  }

  /**
   * Clears the APC cache on Acquia hosting.
   */
  protected function clearApcOnAcquiaHosting() {
    $file = "/mnt/files/{$_ENV['AH_SITE_GROUP']}.{$_ENV['AH_SITE_ENVIRONMENT']}/files-private/sites.json";
    if (file_exists($file)) {
      $apc_token = sha1(file_get_contents($file));
      $this->httpClient->request('GET', "http://localhost:9091/sites/g/apc_rebuild.php?token={$apc_token}");
    }
  }

}
