<?php


namespace Morebec\YDB\Server\Command;

/**
 * List of command return codes
 */
abstract class CommandCode
{
    /** @var int When a command encountered an error */
    public const ERROR = 0;

    /** @var int When a command has the wrong structure or values */
    public const INVALID_COMMAND = 403;

    /** @var int When a command is not found */
    public const UNDEFINED_COMMAND = 404;

    /** @var int When a command executed successfully */
    public const SUCCESS = 200;
}
