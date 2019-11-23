<?php

namespace Morebec\YDB\Enum;

use Morebec\ValueObjects\BasicEnum;

/**
 * LoggerChannel
 */
class LoggerChannel extends BasicEnum
{
    const __DEFAULT = 'default';
    const COMMAND = 'command';
    const EVENT = 'event';
}
