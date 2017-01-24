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
   * Converts a given list (a one-dimensional array) to a table.
   *
   * @param array $list
   *   The list to convert.
   *
   * @return \Behat\Gherkin\Node\TableNode
   *   The table.
   *
   * @throws \AssertionError
   */
  public function getTableFromList(array $list) {
    assert(!$this->isArrayMultidimensional($list), 'List must be a one-dimensional array.');

    array_walk($list, function (&$item) {
      $item = [$item];
    });
    return new TableNode($list);
  }

  /**
   * Determines whether a given array is multidimensional or not.
   *
   * @param array $array
   *   The array.
   *
   * @return bool
   *   TRUE if the given array is multidimensional or FALSE if not.
   */
  public function isArrayMultidimensional(array $array) {
    return count($array) !== count($array, COUNT_RECURSIVE);
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
