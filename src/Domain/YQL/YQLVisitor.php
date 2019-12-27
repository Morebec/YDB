<?php


namespace Morebec\YDB\Domain\YQL;

use Morebec\YDB\Domain\YQL\Parser\Generated\Contexts\RContext;
use Morebec\YDB\Domain\YQL\Parser\Generated\YQLBaseVisitor;
class YQLVisitor extends YQLBaseVisitor
{
    function visitA(RContext $ctx)
    {
        return $this->visitChildren($ctx);
    }

    function visitB(RContext $ctx)
    {
        return $this->visitChildren($ctx);
    }
}