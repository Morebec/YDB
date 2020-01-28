<?php


namespace Morebec\YDB\YQL;


use Morebec\ValueObjects\BasicEnum;

/**
 * @method static self ONE()
 */
class Cardinality extends BasicEnum
{
    public const ALL = 'ALL';

    public const ONE = 'ONE';
}