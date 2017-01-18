<?php

namespace Drupal\Tests\my_devel\Unit\Behat;

use Behat\Gherkin\Node\TableNode;
use Drupal\my_devel\Behat\TableComparisonTrait;
use Drupal\my_devel\Behat\UnequalTablesException;

/**
 * Provides unit tests for TableComparisonTrait.
 */
class TableComparisonTraitTest extends \PHPUnit_Framework_TestCase {

  const TABLE_REALISTIC_SORTED = [
    ['id1', 'Label one', 'First value', 'true'],
    ['id2', 'Label two', 'Second value', 'true'],
    ['id3', 'Label three', 'Third value', 'false'],
    ['id4', 'Label four', 'Fourth value', 'true'],
    ['id5', 'Label five', 'Fifth value', 'false'],
  ];

  const TABLE_REALISTIC_UNSORTED = [
    ['id4', 'Label four', 'Fourth value', 'true'],
    ['id2', 'Label two', 'Second value', 'true'],
    ['id1', 'Label one', 'First value', 'true'],
    ['id3', 'Label three', 'Third value', 'false'],
    ['id5', 'Label five', 'Fifth value', 'false'],
  ];

  const TABLE_SIMPLE_SORTED = [[1, 2], [3, 4], [5, 6]];

  const TABLE_SIMPLE_UNSORTED = [[5, 6], [1, 2], [3, 4]];

  /**
   * The system under test.
   *
   * @var TableComparisonTrait|object
   */
  protected $sut;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    $this->sut = $this->getObjectForTrait(TableComparisonTrait::class);
  }

  /**
   * Tests assertTableEquals() with equal tables.
   *
   * @dataProvider providerTestAssertTableEqualsWithEqualTables
   */
  public function testAssertTableEqualsWithEqualTables($expected, $actual) {
    $return = $this->sut->assertTableEquals(new TableNode($expected), new TableNode($actual));

    $this->assertNull($return);
  }

  /**
   * Data provider.
   */
  public function providerTestAssertTableEqualsWithEqualTables() {
    return [
      'Identical with one single value row' => [[[1]], [[1]]],
      'Identical with one multi-value row' => [[[1, 2]], [[1, 2]]],
      'Identical with multiple multi-value rows' => [
        self::TABLE_SIMPLE_SORTED,
        self::TABLE_SIMPLE_SORTED,
      ],
      'Equal with rows in differing orders' => [
        self::TABLE_REALISTIC_SORTED,
        self::TABLE_REALISTIC_UNSORTED,
      ],
    ];
  }

  /**
   * Tests assertTableEquals() with non-table arguments.
   *
   * @dataProvider providerTestAssertTableEqualsWithNonTableArguments
   * @expectedException \PHPUnit_Framework_Error
   */
  public function testAssertTableEqualsWithNonTableArguments($expected, $actual) {
    $this->sut->assertTableEquals($expected, $actual);
  }

  /**
   * Data provider.
   */
  public function providerTestAssertTableEqualsWithNonTableArguments() {
    $table = new TableNode([]);
    $non_table = '';
    return [
      [$non_table, $table],
      [$table, $non_table],
    ];
  }

  /**
   * Tests assertTableEquals with unequal tables.
   *
   * @dataProvider providerTestAssertTableEqualsWithUnequalTables
   */
  public function testAssertTableEqualsWithUnequalTables($expected, $actual) {
    $expected = new TableNode($expected);
    $actual = new TableNode($actual);
    try {
      $this->sut->assertTableEquals($expected, $actual);
      $this->fail('Failed to throw exception.');
    }
    catch (UnequalTablesException $e) {
      $this->assertEquals($expected, $e->getExpected());
      $this->assertEquals($actual, $e->getActual());
    }
  }

  /**
   * Data provider.
   */
  public function providerTestAssertTableEqualsWithUnequalTables() {
    return [
      'Simple difference' => [[[]], [[1]]],
      'Duplicate row on one side' => [
        [[1, 2], [3, 4], [3, 4], [5, 6]],
        [[1, 2], [3, 4], [5, 6]],
      ],
    ];
  }

  /**
   * Tests assertTableEquals() with label arguments.
   */
  public function testAssertTableEqualsWithLabels() {
    $missing_rows_label = "They're gone!";
    $unexpected_rows_label = 'Free rows!';
    try {
      $this->sut->assertTableEquals(new TableNode([[1]]), new TableNode([[2]]), $missing_rows_label, $unexpected_rows_label);
    }
    catch (UnequalTablesException $e) {
      $this->assertSame($missing_rows_label, $e->getMissingRowsLabel());
      $this->assertSame($unexpected_rows_label, $e->getUnexpectedRowsLabel());
    }
  }

  /**
   * Tests sortTable().
   *
   * @dataProvider providerTestSortTable
   */
  public function testSortTable($raw_table, $expected) {
    $table = new TableNode($raw_table);

    $actual = $this->sut->sortTable($table);

    $this->assertSame($expected, $actual->getTable());
  }

  /**
   * Data provider.
   */
  public function providerTestSortTable() {
    return [
      'Simple table' => [
        self::TABLE_SIMPLE_UNSORTED,
        self::TABLE_SIMPLE_SORTED,
      ],
      'Realistic table' => [
        self::TABLE_REALISTIC_UNSORTED,
        self::TABLE_REALISTIC_SORTED,
      ],
    ];
  }

}
