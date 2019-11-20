<?php 

namespace Morebec\YDB;

use Morebec\YDB\Service\Engine;

/**
 * The Database connection is the main entry point
 * tot the library for end users
 */
class DatabaseConnection
{
    /** @var Engine engine */
    private $engine;

    function __construct(Engine $engine)
    {
        $this->engine = $engine;
    }
}
