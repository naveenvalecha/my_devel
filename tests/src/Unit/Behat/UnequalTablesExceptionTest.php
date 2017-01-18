<?php

namespace Drupal\Tests\my_devel\Unit\Behat;

use Behat\Gherkin\Node\TableNode;
use Behat\Testwork\Tester\Exception\TesterException;
use Drupal\my_devel\Behat\UnequalTablesException;

/**
 * Provides unit tests for UnequalTablesException.
 */
class UnequalTablesExceptionTest extends \PHPUnit_Framework_TestCase {

  /**
   * Tests object construction.
   */
  public function testConstruction() {
    $expected = new TableNode([['expected']]);
    $actual = new TableNode([['actual']]);

    $exception = new UnequalTablesException($expected, $actual);

    $this->assertInstanceOf(TesterException::class, $exception);
    $this->assertInstanceOf(\RuntimeException::class, $exception);
    $this->assertSame($expected, $exception->getExpected());
    $this->assertSame($actual, $exception->getActual());
  }

  /**
   * Tests object construction with non-table arguments.
   *
   * @dataProvider providerTestConstructionWithNonTableArguments
   * @expectedException \PHPUnit_Framework_Error
   */
  public function testConstructionWithNonTableArguments($expected, $actual) {
    new UnequalTablesException($expected, $actual);
  }

  /**
   * Data provider.
   */
  public function providerTestConstructionWithNonTableArguments() {
    $table = new TableNode([]);
    $non_table = '';
    return [
      [$non_table, $table],
      [$table, $non_table],
    ];
  }

  /**
   * Tests generateMessage().
   *
   * @dataProvider providerTestGenerateMessage
   */
  public function testGenerateMessage($left, $right, $expected) {
    $exception = new UnequalTablesException(
      new TableNode($left),
      new TableNode($right)
    );

    $expected = implode($expected, PHP_EOL);
    $this->assertSame($expected, $exception->getMessage());
  }

  /**
   * Data provider.
   */
  public function providerTestGenerateMessage() {
    return [
      'Missing rows' => [
        [
          ['id1', 'Label one'],
          ['id2', 'Label two'],
          ['id3', 'Label three'],
          ['id4', 'Label four'],
        ],
        [
          ['id1', 'Label one'],
          ['id2', 'Label two'],
        ],
        [
          '=== Missing rows: ===',
          '| id3 | Label three |',
          '| id4 | Label four  |',
        ],
      ],
      'Unexpected rows' => [
        [
          ['id1', 'Label one'],
          ['id2', 'Label two'],
        ],
        [
          ['id1', 'Label one'],
          ['id2', 'Label two'],
          ['id3', 'Label three'],
          ['id4', 'Label four'],
        ],
        [
          '=== Unexpected rows: ===',
          '| id3 | Label three |',
          '| id4 | Label four  |',
        ],
      ],
      'Missing and unnexpected rows' => [
        [
          ['id1', 'Label one'],
          ['id2', 'Label two'],
        ],
        [
          ['id3', 'Label three'],
          ['id4', 'Label four'],
        ],
        [
          '=== Missing rows: ===',
          '| id1 | Label one |',
          '| id2 | Label two |',
          '=== Unexpected rows: ===',
          '| id3 | Label three |',
          '| id4 | Label four  |',
        ],
      ],
    ];
  }

  /**
   * Tests generateMessage() with label arguments.
   *
   * @dataProvider providerTestGenerateMessageWithLabels
   */
  public function testGenerateMessageWithLabels(
    $left,
    $right,
    $expected_missing_rows_label,
    $missing_rows_label,
    $expected_unexpected_rows_label,
    $unexpected_rows_label,
    $expected_message_first_line
  ) {
    $exception = new UnequalTablesException(
      new TableNode($left),
      new TableNode($right),
      $missing_rows_label,
      $unexpected_rows_label
    );
    $message_first_line = strtok($exception->getMessage(), PHP_EOL);

    $this->assertSame($expected_missing_rows_label, $exception->getMissingRowsLabel());
    $this->assertSame($expected_unexpected_rows_label, $exception->getUnexpectedRowsLabel());
    $this->assertSame($expected_message_first_line, $message_first_line);
  }

  /**
   * Data provider.
   */
  public function providerTestGenerateMessageWithLabels() {
    return [
      'Missing label' => [
        [[1]],
        [],
        "They're gone!",
        "They're gone!",
        UnequalTablesException::DEFAULT_UNEXPECTED_ROWS_LABEL,
        NULL,
        "=== They're gone!: ===",
      ],
      'Unexpected label' => [
        [],
        [[1]],
        UnequalTablesException::DEFAULT_MISSING_ROWS_LABEL,
        NULL,
        'Free rows!',
        'Free rows!',
        '=== Free rows!: ===',
      ],
    ];
  }

}
