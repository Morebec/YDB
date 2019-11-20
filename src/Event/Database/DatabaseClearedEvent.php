<?php 

namespace Morebec\YDB\Event\Database;

use Morebec\YDB\Event\DatabaseEvent;

/**
 * DatabaseClearedEvent
 */
class DatabaseClearedEvent extends DatabaseEvent
{
    const NAME = 'database.cleared';
}
