<?php


namespace Morebec\YDB\Domain\YQL\Parser;

use Morebec\ValueObjects\BasicEnum;
use Morebec\YDB\Domain\YQL\ExpressionOperator;
use Morebec\YDB\Domain\YQL\Query\Operator;

/**
 * @method static WHITESPACE()
 * @method static EOX()
 * @method static FIND()
 * @method static ALL()
 * @method static ONE()
 * @method static IDENTIFIER()
 * @method static FROM()
 * @method static EXPR_OPERATOR_OR()
 * @method static EXPR_OPERATOR_AND()
 * @method static PAREN()
 * @method static WHERE()
 * @method static OPERATOR_EQUAL()
 * @method static OPERATOR_STRICTLY_EQUAL()
 * @method static OPERATOR_NOT_EQUAL()
 * @method static OPERATOR_STRICTLY_NOT_EQUAL()
 * @method static OPERATOR_LESS_THAN()
 * @method static OPERATOR_GREATER_THAN()
 * @method static OPERATOR_LESS_OR_EQUAL()
 * @method static OPERATOR_GREATER_OR_EQUAL()
 * @method static OPERATOR_IN()
 * @method static OPERATOR_NOT_IN()
 * @method static STRING_LITERAL()
 * @method static NUMERIC_LITERAL()
 * @method static TERM()
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
    public const OPERATOR_EQUAL = Operator::EQUAL;
    public const OPERATOR_STRICTLY_EQUAL = Operator::STRICTLY_EQUAL;

    public const OPERATOR_NOT_EQUAL = Operator::NOT_EQUAL;
    public const OPERATOR_STRICTLY_NOT_EQUAL = Operator::STRICTLY_NOT_EQUAL;

    public const OPERATOR_LESS_THAN = Operator::LESS_THAN;
    public const OPERATOR_GREATER_THAN = Operator::GREATER_THAN;

    public const OPERATOR_LESS_OR_EQUAL = Operator::LESS_OR_EQUAL;
    public const OPERATOR_GREATER_OR_EQUAL = Operator::GREATER_OR_EQUAL;

    public const OPERATOR_IN = Operator::IN;
    public const OPERATOR_NOT_IN = Operator::NOT_IN;

    public const TERM = 'TERM';

    public const NUMERIC_LITERAL = '\d+';
    public const PAREN = '\(|\)';
    public const IDENTIFIER = '\w+';
    public const STRING_LITERAL = '^((?:(?:"(?:\\"|[^"])+")|(?:\'(?:\\\'|[^\'])+\')))"';

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
