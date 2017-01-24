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
   * Tests getTableFromList().
   */
  public function testGetTableFromList() {
    $list = [1, 2, 3];
    $expected = new TableNode([[1], [2], [3]]);

    $actual = $this->sut->getTableFromList($list);

    $this->assertEquals($expected, $actual);
  }

  /**
   * Tests getTableFromList() with a non-array argument.
   *
   * @dataProvider providerNonArrayArguments
   * @expectedException \PHPUnit_Framework_Error
   */
  public function testGetTableFromListWithNonArrayArgument($argument) {
    $this->sut->getTableFromList($argument);
  }

  /**
   * Tests getTableFromList() with a multidimensional array argument.
   *
   * @expectedException \AssertionError
   * @expectedExceptionMessage List must be a one-dimensional array.
   */
  public function testGetTableFromListWithMultidimensionalArrayArgument() {
    $this->sut->getTableFromList([[1, 2, 3], [4, 5, 6]]);
  }

  /**
   * Tests isArrayMultidimensional().
   *
   * @dataProvider providerTestIsArrayMultidimensional
   */
  public function testIsArrayMultidimensional($expected, $array) {
    $actual = $this->sut->isArrayMultidimensional($array);

    $this->assertSame($expected, $actual);
  }

  /**
   * Data provider.
   */
  public function providerTestIsArrayMultidimensional() {
    return [
      [FALSE, [1]],
      [FALSE, [1, 2, 3]],
      [TRUE, [[1, 2]]],
      [TRUE, [[1, 2], [3, 4]]],
    ];
  }

  /**
   * Tests getTableFromList() with a non-array argument.
   *
   * @dataProvider providerNonArrayArguments
   * @expectedException \PHPUnit_Framework_Error
   */
  public function testIsArrayMultidimensionalWithNonArrayArgument($argument) {
    $this->sut->isArrayMultidimensional($argument);
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

  /**
   * Reusable data provider for non-array arguments.
   */
  public function providerNonArrayArguments() {
    return [
      ['string'],
      [12345],
      [123.45],
      [TRUE],
      [NULL],
    ];
  }

}
