<?php


namespace Morebec\YDB\YQL\Parser;


use Morebec\YDB\Document;
use Morebec\YDB\YQL\Query\TermOperator;
use Morebec\YDB\YQL\TermNode;

class TautologyNode extends TermNode
{
    /**
     * TautologyNode constructor.
     */
    public function __construct()
    {
        // Skip parent
        // parent::__construct('', TermOperator::EQUAL(), true);
    }

    public function matchesDocument(Document $document)
    {
        return true;
    }
}