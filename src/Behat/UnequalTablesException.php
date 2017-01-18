<?php

namespace Drupal\my_devel\Behat;

use Behat\Gherkin\Node\TableNode;
use Behat\Testwork\Tester\Exception\TesterException;

/**
 * Provides an exception for displaying table inequalities.
 */
class UnequalTablesException extends \RuntimeException implements TesterException {

  const DEFAULT_MISSING_ROWS_LABEL = 'Missing rows';

  const DEFAULT_UNEXPECTED_ROWS_LABEL = 'Unexpected rows';

  /**
   * The actual table.
   *
   * @var \Behat\Gherkin\Node\TableNode
   */
  private $actual;

  /**
   * The expected table.
   *
   * @var \Behat\Gherkin\Node\TableNode
   */
  protected $expected;

  /**
   * The "missing rows" label.
   *
   * @var null|string
   */
  protected $missingRowsLabel;

  /**
   * The "unexpected rows" label.
   *
   * @var null|string
   */
  protected $unexpectedRowsLabel;

  /**
   * UnequalTablesException constructor.
   *
   * @param \Behat\Gherkin\Node\TableNode $expected
   *   The expected table.
   * @param \Behat\Gherkin\Node\TableNode $actual
   *   The actual table.
   * @param string $missing_rows_label
   *   The label for missing rows output.
   * @param string $unexpected_rows_label
   *   The label for unexpected rows output.
   */
  public function __construct(TableNode $expected, TableNode $actual, $missing_rows_label = NULL, $unexpected_rows_label = NULL) {
    $this->expected = $expected;
    $this->actual = $actual;
    $this->missingRowsLabel = $missing_rows_label;
    $this->unexpectedRowsLabel = $unexpected_rows_label;
    $message = $this->generateMessage($expected->getRows(), $actual->getRows());
    parent::__construct($message);
  }

  /**
   * Generates the exception message.
   *
   * @param array $expected_rows
   *   The expected rows.
   * @param array $actual_rows
   *   The actual rows.
   *
   * @return string
   *   The generated message.
   */
  protected function generateMessage(array $expected_rows, array $actual_rows) {
    $message = [];
    $this->addArrayDiffMessageLines($message, $actual_rows, $expected_rows, $this->getMissingRowsLabel());
    $this->addArrayDiffMessageLines($message, $expected_rows, $actual_rows, $this->getUnexpectedRowsLabel());
    return implode(PHP_EOL, $message);
  }

  /**
   * Adds message lines for array differences.
   *
   * @param array $message
   *   The message lines to add to.
   * @param array $left
   *   The left array.
   * @param array $right
   *   The right array.
   * @param string $label
   *   The label.
   */
  protected function addArrayDiffMessageLines(array &$message, array $left, array $right, $label) {
    $differences = array_filter($right, function (array $row) use ($left) {
      return !in_array($row, $left);
    });
    if ($differences) {
      $message[] = "=== ${label}: ===";
      $message[] = (new TableNode($differences))->getTableAsString();
    }
  }

  /**
   * Gets the actual table.
   *
   * @return \Behat\Gherkin\Node\TableNode
   *   The table.
   */
  public function getActual() {
    return $this->actual;
  }

  /**
   * Gets the expected table.
   *
   * @return \Behat\Gherkin\Node\TableNode
   *   The table.
   */
  public function getExpected() {
    return $this->expected;
  }

  /**
   * Gets the "missing rows" label.
   *
   * @return string
   *   The label.
   */
  public function getMissingRowsLabel() {
    return $this->missingRowsLabel ?: self::DEFAULT_MISSING_ROWS_LABEL;
  }

  /**
   * Gets the "unexpected rows" label.
   *
   * @return string
   *   The label.
   */
  public function getUnexpectedRowsLabel() {
    return $this->unexpectedRowsLabel ?: self::DEFAULT_UNEXPECTED_ROWS_LABEL;
  }

}
