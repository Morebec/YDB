<?php

namespace Tests\Morebec\YDB\Domain\YQL\Parser;

use Morebec\YDB\Domain\YQL\Parser\Lexer;
use Morebec\YDB\Domain\YQL\Parser\TermParser;
use Morebec\YDB\Domain\YQL\Parser\YQLParser;

use Morebec\YDB\Domain\YQL\Query\Query;
use PHPUnit\Framework\TestCase;

class YQLParserTest extends TestCase
{

    public function testParse()
    {
        $termLexer = new Lexer();
        $tokens = $termLexer->lex('/* a comment */ FIND ALL FROM table WHERE (field == 100 AND field2 == 200) OR field3 == 300');

        $termLexer = new TermParser();
        $termedTokens = $termLexer->lexTerms($tokens);

        $parser = new YQLParser();
        $node = $parser->parse($termedTokens);

        $q = new Query($node);
        // The to string function of the query node adds clarifying parentheses
        $this->assertEquals('((field == 100) AND (field2 == 200)) OR (field3 == 300)', (string)$q);
    }
}
