<?php

namespace Morebec\YDB\Event\Database;

use Morebec\YDB\Event\DatabaseEvent;

/**
 * DatabaseDeletedEvent
 */
class DatabaseDeletedEvent extends DatabaseEvent
{
    const NAME = 'database.deleted';
}
