<?php 

namespace Morebec\YDB\YQL;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * YQL
 */
class YQL extends ExpressionLanguage
{
    
    function __construct()
    {
        parent::__construct(null, $this->getProviders());
    }

    /**
     * Returns a list of language providers
     * @return array array of providers
     */
    private function getProviders(): array
    {
        return [
            new StringExpressionProvider()
        ];
    }
}