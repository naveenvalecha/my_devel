<?php

namespace Drupal\my_devel;

use Drupal\migrate\Plugin\MigrateIdMapInterface;
use Drupal\migrate\Plugin\MigrationInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Provides error handling for migrations.
 */
trait MigrationErrorHandlingTrait {

  /**
   * A logger instance.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * Handles a migration error.
   *
   * Logs a message and throw an exception.
   *
   * @param string $message
   *   The error message.
   * @param int $code
   *   The Exception code.
   * @param \Exception|null $previous
   *   The previous exception used for the exception chaining.
   * @param int $level
   *   The level of the error:
   *   -  MigrationInterface::MESSAGE_ERROR.
   *   -  MigrationInterface::MESSAGE_WARNING.
   *   -  MigrationInterface::MESSAGE_NOTICE.
   *   -  MigrationInterface::MESSAGE_INFORMATIONAL.
   * @param int $status
   *   The status of the item for the map table:
   *   - MigrateIdMapInterface::STATUS_IMPORTED.
   *   - MigrateIdMapInterface::STATUS_NEEDS_UPDATE.
   *   - MigrateIdMapInterface::STATUS_IGNORED.
   *   - MigrateIdMapInterface::STATUS_FAILED.
   */
  protected function handleError($message = NULL, $code = 0, \Exception $previous = NULL, $level = MigrationInterface::MESSAGE_ERROR, $status = MigrateIdMapInterface::STATUS_FAILED) {
    switch ($level) {
      case MigrationInterface::MESSAGE_WARNING:
        $log_level = LogLevel::WARNING;
        break;

      case MigrationInterface::MESSAGE_NOTICE:
        $log_level = LogLevel::NOTICE;
        break;

      case MigrationInterface::MESSAGE_INFORMATIONAL:
        $log_level = LogLevel::INFO;
        break;

      case MigrationInterface::MESSAGE_ERROR:
      default:
        $log_level = LogLevel::ERROR;
    }
    $this->getLogger()->log($log_level, $message);

  }

  /**
   * Gets the logger service.
   *
   * @return \Psr\Log\LoggerInterface
   *   A logger instance.
   */
  protected function getLogger() {
    if (!$this->logger) {
      $this->logger = \Drupal::logger($this->loggerChannel());
    }

    return $this->logger;
  }

  /**
   * Returns the logger channel name.
   *
   * @return string
   *   The logger channel name.
   */
  protected function loggerChannel() {
    return 'my_devel';
  }

  /**
   * Sets the logger service to use.
   *
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   *
   * @return $this
   */
  public function setLogger(LoggerInterface $logger) {
    $this->logger = $logger;

    return $this;
  }

}
