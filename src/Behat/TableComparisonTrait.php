<?php

namespace Drupal\my_devel\Behat;

use Behat\Gherkin\Node\TableNode;

/**
 * Provides table comparison methods for Behat contexts.
 */
trait TableComparisonTrait {

  /**
   * Asserts that two given tables are equivalent, ignoring row order.
   *
   * @param \Behat\Gherkin\Node\TableNode $expected
   *   The expected table.
   * @param \Behat\Gherkin\Node\TableNode $actual
   *   The actual table.
   * @param string|null $missing_rows_label
   *   The "missing rows" label.
   * @param string|null $unexpected_rows_label
   *   The "unexpected rows" label.
   *
   * @throws \AssertionError
   * @throws UnequalTablesException
   */
  public function assertTableEquals(TableNode $expected, TableNode $actual, $missing_rows_label = NULL, $unexpected_rows_label = NULL) {
    $expected_sorted = $this->sortTable($expected);
    $actual_sorted = $this->sortTable($actual);
    if ($actual_sorted != $expected_sorted) {
      throw new UnequalTablesException($expected_sorted, $actual_sorted, $missing_rows_label, $unexpected_rows_label);
    }
  }

  /**
   * Sorts a given table.
   *
   * @param \Behat\Gherkin\Node\TableNode $table
   *   The table to sort.
   *
   * @return \Behat\Gherkin\Node\TableNode
   *   The sorted table.
   */
  public function sortTable(TableNode $table) {
    $raw_table = $table->getTable();
    sort($raw_table);
    return new TableNode($raw_table);
  }

}
