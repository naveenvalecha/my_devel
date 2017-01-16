<?php

namespace Drupal\my_devel;

use Drupal\config_update\ConfigRevertInterface;
use Drupal\Core\Config\ConfigInstallerInterface;

/**
 * Provides helpers for performing site updates.
 */
class ConfigHelper {

  /**
   * A configuration installer.
   *
   * @var \Drupal\Core\Config\ConfigInstallerInterface
   */
  private $configInstaller;

  /**
   * A configuration reverter.
   *
   * @var \Drupal\config_update\ConfigRevertInterface
   */
  private $configReverter;

  /**
   * Creates a new ConfigHelper.
   *
   * @param \Drupal\Core\Config\ConfigInstallerInterface $config_installer
   *   A configuration installer.
   * @param \Drupal\config_update\ConfigRevertInterface $config_reverter
   *   A configuration reverter.
   */
  public function __construct(ConfigInstallerInterface $config_installer, ConfigRevertInterface $config_reverter) {
    $this->configInstaller = $config_installer;
    $this->configReverter = $config_reverter;
  }

  /**
   * Installs the default configuration for a given list of modules.
   *
   * Note: This will not update or overwrite configuration that already exists.
   *
   * @param string[] $module_list
   *   An array of module names.
   */
  public function installModuleDefaultConfig(array $module_list) {
    foreach ($module_list as $name) {
      try {
        $this->configInstaller->installDefaultConfig('module', $name);
      }
      catch (\Exception $e) {
        // An exception can be thrown if configuration already exists. Ignore.
        // @todo Catch a more specific exception, if possible.
      }
    }
  }

  /**
   * Reverts all configuration of a given list of type.
   *
   * @param string[] $type_list
   *   An array of entity type names.
   */
  public function revertAllConfigOfTypes(array $type_list) {
    foreach ($type_list as $type) {
      $ids = \Drupal::entityQuery($type)->execute();
      foreach ($ids as $id) {
        $this->configReverter->revert($type, $id);
      }
    }
  }

  /**
   * Reverts a given list of configuration items.
   *
   * @param string $type
   *   The type of configuration.
   * @param string[] $name_list
   *   An array of configuration names.
   */
  public function revertConfig($type, array $name_list) {
    foreach ($name_list as $name) {
      $this->configReverter->revert($type, $name);
    }
  }

}
