<?php


namespace Morebec\YDB\YQL\Parser;


use InvalidArgumentException;

class Lexer
{
    /**
     * Pareses a string and returns an array of tokens
     * @param string $source
     * @return array
     */
    public function lex(string $source): array
    {
        $source = $this->stripComments($source);

        $tokens = [];
        $offset = 0;

        $length = strlen($source);
        while($offset < $length) {
            $token = $this->findToken($source, $offset);

            // Filter out whitespaces
            if(!$this->canDiscardToken($token)) {
                $tokens[] = $token;
            }

            $offset += strlen($token->getValue());
        }

        $tokens[] = Token::create(TokenType::EOX(), null);

        return $tokens;
    }

    private function stripComments(string $src): string
    {
       return preg_replace('%/\*\s+.*?\*/%s', '', $src); // remove /* */
    }


    /**
     * Indicates if a token can be discarded or not
     * @param Token $token
     * @return bool
     */
    private function canDiscardToken(Token $token): bool
    {
        return $token->getType()->isEqualTo(TokenType::WHITESPACE());
    }

    private function findToken(string $line, int $offset): Token
    {
        $string = substr($line, $offset);

        foreach(TokenType::getNamesAndValues() as $name => $pattern) {
            $regexPattern = "/^($pattern)/";
            if(preg_match($regexPattern, $string, $matches)) {
                $value = $matches[1];
                if($pattern == TokenType::NUMERIC_LITERAL) {
                    $value = (float)$value;
                }
                $tokenType = $pattern;
                return Token::create(new TokenType($tokenType), $value);
            }
        }


        throw new InvalidArgumentException("Unexpected token $string at $offset");
    }
}