<?php


namespace Morebec\YDB\Domain\Model\Repository;

use Morebec\YDB\Domain\Model\Entity\Database;

interface DatabaseRepositoryInterface
{
    public function add(Database $database): void;

    public function remove(Database $database): void;
}