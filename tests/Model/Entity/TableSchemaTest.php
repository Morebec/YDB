<?php

namespace Tests\Morebec\YDB\Model\Entity;

use Morebec\YDB\Domain\Model\Entity\ColumnDefinition;
use Morebec\YDB\Domain\Model\Entity\ColumnType;
use Morebec\YDB\Domain\Model\Entity\TableSchema;
use PHPUnit\Framework\TestCase;

class TableSchemaTest extends TestCase
{
    public function testCreate(): void
    {
        $this->assertNotNull(TableSchema::create('a_table', []));
    }

    public function testCreateWithSpaceInTableNameThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        TableSchema::create('', []);
    }

    public function testCreateWithBlankTableNameThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        TableSchema::create('', []);
    }

    public function testCreateWithSpecialCharsInTableNameThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        TableSchema::create('Atable!#', []);
    }

    public function testGetColumnByName(): void
    {
        $expectedCol = ColumnDefinition::create('columnName', ColumnType::STRING());
        $schema = TableSchema::create('testTable', [
            $expectedCol
        ]);

        $this->assertEquals($expectedCol, $schema->getColumnByName('columnName'));
    }

    public function testGetColumns(): void
    {
        $schema = TableSchema::create('testTable', [
            ColumnDefinition::create('columnName', ColumnType::STRING()),
            ColumnDefinition::create('anotherColumn', ColumnType::STRING())
        ]);

        $this->assertNotEmpty($schema->getColumns());
    }

    public function testNonUniqueColumnNamesThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $schema = TableSchema::create('testTable', [
            ColumnDefinition::create('columnName', ColumnType::STRING()),
            ColumnDefinition::create('columnName', ColumnType::STRING())
        ]);
    }

    public function testColumnWithNameExists(): void
    {
        $schema = TableSchema::create('testTable', [
            ColumnDefinition::create('columnName', ColumnType::STRING()),
            ColumnDefinition::create('anotherColumn', ColumnType::STRING())
        ]);

        $this->assertTrue($schema->columnWithNameExists('columnName'));
        $this->assertFalse($schema->columnWithNameExists('doesNotExist'));
    }
}
