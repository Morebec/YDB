<?php


namespace Morebec\YDB\YQL\Parser;

use Morebec\ValueObjects\BasicEnum;
use Morebec\YDB\YQL\ExpressionOperator;
use Morebec\YDB\YQL\Query\TermOperator;

/**
 * @method static self WHITESPACE()
 * @method static self EOX()
 * @method static self FIND()
 * @method static self ALL()
 * @method static self ONE()
 * @method static self IDENTIFIER()
 * @method static self FROM()
 * @method static self EXPR_OPERATOR_OR()
 * @method static self EXPR_OPERATOR_AND()
 * @method static self PAREN()
 * @method static self WHERE()
 * @method static self OPERATOR_EQUAL()
 * @method static self OPERATOR_STRICTLY_EQUAL()
 * @method static self OPERATOR_NOT_EQUAL()
 * @method static self OPERATOR_STRICTLY_NOT_EQUAL()
 * @method static self OPERATOR_LESS_THAN()
 * @method static self OPERATOR_GREATER_THAN()
 * @method static self OPERATOR_LESS_OR_EQUAL()
 * @method static self OPERATOR_GREATER_OR_EQUAL()
 * @method static self OPERATOR_IN()
 * @method static self OPERATOR_NOT_IN()
 * @method static self STRING_LITERAL()
 * @method static self NUMERIC_LITERAL()
 * @method static self TERM()
 */
class TokenType extends BasicEnum
{
    public const FIND = 'FIND';
    public const ALL = 'ALL';
    public const ONE = 'ONE';

    public const WHERE = 'WHERE';
    public const FROM = 'FROM';

    public const EXPR_OPERATOR_AND = ExpressionOperator::AND;
    public const EXPR_OPERATOR_OR = ExpressionOperator::OR;

    // OPERATORS ARE SYMBOLS
    public const OPERATOR_STRICTLY_EQUAL = TermOperator::EQUAL;
    public const OPERATOR_EQUAL = TermOperator::LOOSELY_EQUALS;

    public const OPERATOR_STRICTLY_NOT_EQUAL = TermOperator::NOT_EQUAL;
    public const OPERATOR_NOT_EQUAL = TermOperator::LOOSELY_NOT_EQUAL;

    public const OPERATOR_LESS_OR_EQUAL = TermOperator::LESS_OR_EQUAL;
    public const OPERATOR_GREATER_OR_EQUAL = TermOperator::GREATER_OR_EQUAL;

    public const OPERATOR_LESS_THAN = TermOperator::LESS_THAN;
    public const OPERATOR_GREATER_THAN = TermOperator::GREATER_THAN;

    public const OPERATOR_IN = TermOperator::IN;
    public const OPERATOR_NOT_IN = TermOperator::NOT_IN;

    public const TERM = 'TERM';

    public const NUMERIC_LITERAL = '\d+(\.\d+)?';

    public const PAREN = '\(|\)';

    public const IDENTIFIER = '\w+';

    public const STRING_LITERAL = "'[^'\\\\]*(?:\\\\.[^'\\\\]*)*'";

    public const WHITESPACE = '\s+';


    /**
     * @return array
     */
    public static function TERM_OPERATORS(): array
    {
        return [
            self::OPERATOR_STRICTLY_EQUAL => self::OPERATOR_STRICTLY_EQUAL(),
            self::OPERATOR_EQUAL => self::OPERATOR_EQUAL(),

            self::OPERATOR_STRICTLY_NOT_EQUAL => self::OPERATOR_STRICTLY_NOT_EQUAL(),
            self::OPERATOR_NOT_EQUAL => self::OPERATOR_NOT_EQUAL(),

            self::OPERATOR_LESS_THAN => self::OPERATOR_LESS_THAN(),
            self::OPERATOR_GREATER_THAN => self::OPERATOR_GREATER_THAN(),

            self::OPERATOR_LESS_OR_EQUAL => self::OPERATOR_LESS_OR_EQUAL(),
            self::OPERATOR_GREATER_OR_EQUAL => self::OPERATOR_GREATER_OR_EQUAL(),

            self::OPERATOR_IN => self::OPERATOR_IN(),
            self::OPERATOR_NOT_IN => self::OPERATOR_NOT_IN(),
        ];
    }

    public const EOX = 'EOX'; // END OF EXPRESSION

    public static function __callStatic($name, $arguments)
    {
        return new static(constant(sprintf('%s::%s', self::class, $name)));
    }
}
