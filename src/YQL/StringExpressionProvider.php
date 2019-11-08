<?php 

namespace Morebec\YDB\YQL;

use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

/**
 * StringExpressionProvider
 */
class StringExpressionProvider implements ExpressionFunctionProviderInterface
{
    

    /**
     * @return ExpressionFunction[] An array of Function instances
     */
    public function getFunctions()
    {
        return [
            // lowercase
            new ExpressionFunction('lowercase', function ($str) {
                return sprintf('(is_string(%1$s) ? strtolower(%1$s) : %1$s)', $str);
            }, function ($arguments, $str) {
                if (!is_string($str)) {
                    return $str;
                }

                return strtolower($str);
            }),

            // uppercase
            new ExpressionFunction('uppercase', function ($str) {
                return sprintf('(is_string(%1$s) ? strtoupper(%1$s) : %1$s)', $str);
            }, function ($arguments, $str) {
                if (!is_string($str)) {
                    return $str;
                }

                return strtoupper($str);
            }),


            // trim
            new ExpressionFunction('trim', function ($str) {
                return sprintf('(is_string(%1$s) ? trim(%1$s) : %1$s)', $str);
            }, function ($arguments, $str) {
                if (!is_string($str)) {
                    return $str;
                }

                return trim($str);
            }),
        ];
    }
}