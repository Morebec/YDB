<?php

namespace Morebec\YDB\Domain\CommandHandler\Table;

use Morebec\YDB\Service\Database;

/**
* UpdateTableCommandHandler
*/
class UpdateTableCommandHandler
{
    /** @var Database */
    private $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function __invoke()
    {
    }
}
