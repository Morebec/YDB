<?php


namespace Morebec\YDB\Domain\YQL;


use Morebec\ValueObjects\BasicEnum;

class Cardinality extends BasicEnum
{
    public const ALL = 'ALL';

    public const ONE = 'ONE';
}