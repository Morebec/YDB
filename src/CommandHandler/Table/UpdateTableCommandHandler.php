<?php

namespace Morebec\YDB\CommandHandler\Table;

use Morebec\YDB\Service\Database;

/**
* UpdateTableCommandHandler
*/
class UpdateTableCommandHandler
{
    /** @var Database */
    private $database; 

    function __construct(Database $database)
    {
       $this->database = $database;
    }

    public function __invoke()
    {
        
    }
}  
