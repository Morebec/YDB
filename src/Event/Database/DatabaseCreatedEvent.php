<?php 

namespace Morebec\YDB\Event\Database;

use Morebec\YDB\Event\DatabaseEvent;

/**
 * DatabaseCreatedEvent
 */
class DatabaseCreatedEvent extends DatabaseEvent
{
    const NAME = 'database.created';
}