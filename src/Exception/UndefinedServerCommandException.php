<?php


namespace Morebec\YDB\Exception;

use Morebec\YDB\Server\ServerException;
use Throwable;

class UndefinedServerCommandException extends ServerException
{
    public function __construct(string $commandName, int $code = 0, Throwable $previous = null)
    {
        parent::__construct("Undefined server command {$commandName}", $code, $previous);
    }
}
