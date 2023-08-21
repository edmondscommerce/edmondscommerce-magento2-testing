<?php

namespace EdmondsCommerce\Testing\Assertions;

use LogicException;
use Magento\Framework\App\ResourceConnection;
use PHPUnit\Framework\Assert;
use Zend_Db_Expr;
use Zend_Db_Select;

class DatabaseAssertions
{
    public function __construct(private readonly ResourceConnection $connection)
    {
    }

    /**
     * Checks if a single record matching criteria exists
     * Asserts that only 1 record is returned, 0 or more than 1 is considered an assertion failure
     */
    public function assertRowExists(string $table, array $fields, string $message = null): void
    {
        $resultCount = $this->queryTableCount($table, $fields);

        Assert::assertSame(
            1,
            $resultCount,
            $message ?? 'Expected 1 result, found ' . $resultCount
        );
    }

    /**
     * Asserts that 0 rows are present for the table and conditions given
     *
     * @return void
     */
    public function assertRowNotExist(string $table, array $fields, string $message = null): void
    {
        $resultCount = $this->queryTableCount($table, $fields);

        Assert::assertSame(
            0,
            $resultCount,
            $message ?? 'Expected 0 results, found ' . $resultCount
        );
    }

    public function assertTotalTableRowCount(string $table, int $expectedCount, string $message = null): void
    {
        $allRowCount = $this->queryTableCount($table, []);

        Assert::assertSame(
            $expectedCount,
            $allRowCount,
            $message ?? sprintf(
                'Incorrect record count for table %s, expected %s but found %s',
                $table,
                $expectedCount,
                $allRowCount
            )
        );
    }

    private function queryTableCount(string $table, array $fields): int
    {
        $query = $this->connection->getConnection()->select();

        $query->from($table)
              ->reset(Zend_Db_Select::COLUMNS)
              ->columns([new Zend_Db_Expr('COUNT(*)')]);

        foreach ($fields as $name => $value) {
            $query->where("$name = ?", $value);
        }

        $resultCount = $query->query()->fetchColumn();
        if (!is_numeric($resultCount)) {
            throw new LogicException('Invalid value returned from count call');
        }

        return (int)$resultCount;
    }
}