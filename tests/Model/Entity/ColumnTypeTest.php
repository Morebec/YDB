<?php

namespace Tests\Morebec\YDB\Model\Entity;

use Morebec\YDB\Domain\Model\Entity\ColumnType;
use PHPUnit\Framework\TestCase;

class ColumnTypeTest extends TestCase
{

    public function testINTEGER(): void
    {
        $this->assertNotNull(ColumnType::INTEGER());
    }

    public function testFLOAT(): void
    {
        $this->assertNotNull(ColumnType::FLOAT());
    }

    public function test__callStatic(): void
    {
        $this->assertNotNull(ColumnType::__callStatic('string', null));

        $this->expectException(\InvalidArgumentException::class);
        ColumnType::__callStatic('invalid', null);
    }

    public function testSTRING(): void
    {
        $this->assertNotNull(ColumnType::STRING());
    }

    public function testARRAY(): void
    {
        $this->assertNotNull(ColumnType::ARRAY());
    }

    public function testBOOLEAN(): void
    {
        $this->assertNotNull(ColumnType::BOOLEAN());
    }
}
