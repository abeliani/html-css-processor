<?php

/**
 * This file is part of the CssJsHtmlOptimizer Project.
 *
 * @package     CssJsHtmlOptimizer
 * @author      Anatolii Belianin <belianianatoli@gmail.com>
 * @license     See LICENSE.md for license information
 * @link        https://github.com/abeliani/css-js-html-optimizer
 */

declare(strict_types=1);

namespace Abeliani\CssJsHtmlOptimizer\Js\Parser\Trait;

use Abeliani\CssJsHtmlOptimizer\Js\Parser\JSToken;

trait PrepareJsTrait
{
    use ClearCommentTrait;

    protected function prepare(string $data): string
    {
        $data = $this->clearSpaces($this->clearComments($data));
        $data = rtrim($data, JSToken::SEMICOLON);
        return $this->hasNoCode($data) ? JSToken::NS : $data;
    }

    private function clearSpaces(string $data): string
    {
        $length = strlen($data);
        $apostrophe = $singleQuote = $doubleQuote = 0;
        $operators = ['/', '*', '=', '|', '-', '+', '<', '>', '!', '~'];

        ob_start();
        for ($i = 0; $length > $i; $i++) {
            $curr = $data[$i];
            $next = $data[$i + 1] ?? JSToken::NS;
            $prev = $data[$i - 1] ?? JSToken::NS;

            if ($curr === JSToken::F_SLASH) {
                try {
                    if (!$regexpClose = strpos($data, JSToken::F_SLASH, $i + 1)) {
                        print $curr;
                        continue;
                    }
                    $regexp = substr($data, $i, $regexpClose - $i + 1);
                    preg_match($regexp, JSToken::NS);

                    $i = $regexpClose;
                    print $regexp;
                    continue;
                } catch (\Exception) {
                    continue;
                }
            }
            // content inside quotes is not processing
            if ($prev !== JSToken::B_SLASH) {
                if ($curr === JSToken::B_QUOTE) {
                    if ($apostrophe) {
                        $apostrophe--;
                    } else {
                        $apostrophe++;
                    }
                }
                if ($curr === JSToken::QUOTE) {
                    if ($singleQuote) {
                        $singleQuote--;
                    } else {
                        $singleQuote++;
                    }
                }
                if ($curr === JSToken::D_QUOTE) {
                    if ($doubleQuote) {
                        $doubleQuote--;
                    } else {
                        $doubleQuote++;
                    }
                }
            }
            if ($apostrophe || $singleQuote || $doubleQuote) {
                print $curr;
                continue;
            }

            if ($curr === JSToken::S) {
                continue;
            }

            if ($curr === JSToken::O_CUR_BRACKET && ($next === JSToken::RL || $next === JSToken::NL)) {
                $i++;
                print $curr;
                continue;
            }

            if ($curr === JSToken::SEMICOLON) {
                [$char, $offset] = $this->firstCharAfterSpaces($data, $i);

                if (!$char) {
                    break;
                }

                if ($char === JSToken::C_CUR_BRACKET) {
                    $i = $offset;
                    print $char;
                    continue;
                }

                $i = $offset - 1;
                print $curr;
                continue;
            }

            if ($curr === JSToken::RL || $curr === JSToken::NL) {
                if ($prev === JSToken::COMMA
                    || $prev === JSToken::O_CUR_BRACKET
                    || $prev === JSToken::SEMICOLON
                    || in_array($prev, $operators)
                ) {
                    continue;
                }
                if ($next === JSToken::C_CUR_BRACKET || in_array($next, $operators)) {
                    continue;
                }

                [$char, $i] = $this->firstCharAfterSpaces($data, $i);

                if ($char === JSToken::C_CUR_BRACKET) {
                    print $char;
                    continue;
                }

                if (($prev === JSToken::C_CUBE_BRACKET && $char === JSToken::C_CUBE_BRACKET)
                    || in_array($char, $operators)
                ) {
                    print $char;
                    continue;
                }

                print $curr;
                $curr = $char;
            }

            if ($curr === JSToken::F
                && ((substr($data, $i, 9)) === JSToken::WITH_SPACE_FUNCTION
                    || (substr($data, $i, 9)) === JSToken::WITH_BRACKET_FUNCTION)
            ) {
                $openBrakePos = strpos($data, JSToken::O_PARENTHESES, $i);
                $funcDeclareStmt = preg_replace(
                    JSToken::PATT_D_SPACES,
                    JSToken::S,
                    substr($data, $i, $openBrakePos - $i)
                );

                $i = $openBrakePos;
                print sprintf(JSToken::S_PATT_CONCAT, trim($funcDeclareStmt), JSToken::O_PARENTHESES);
                continue;
            }

            if ($curr === JSToken::V || $curr === JSToken::L) {
                $name = trim(substr($data, $i, 4));

                if ($name === JSToken::VAR || $name === JSToken::LET) {
                    $equalPos = strpos($data, JSToken::EQUAL, $i);
                    $varStmt = preg_replace(
                        JSToken::PATT_SPACES,
                        JSToken::S,
                        substr($data, $i, $equalPos - $i)
                    );

                    $i = $equalPos;
                    print sprintf(JSToken::S_PATT_EQUALS, trim($varStmt));
                    continue;
                }
            }

            if ($curr === JSToken::C) {
                $name = trim(substr($data, $i, 5));

                if ($name === JSToken::CONST) {
                    $equalPos = strpos($data, JSToken::EQUAL, $i);
                    $constStmt = preg_replace(
                        JSToken::PATT_SPACES,
                        JSToken::S,
                        substr($data, $i, $equalPos - $i)
                    );

                    $i = $equalPos;
                    print sprintf(JSToken::S_PATT_EQUALS, trim($constStmt));
                    continue;
                }
            }

            if ($curr === JSToken::R && (substr($data, $i, 7)) === JSToken::WITH_SPACE_RETURN) {
                $pair = $this->firstCharAfterSpaces($data, $i + 6);

                $i = $pair[1] - 1;
                print JSToken::WITH_SPACE_RETURN;
                continue;
            }

            if ($curr === JSToken::E) {
                $stmt = substr($data, $i, 5);
                if ($stmt === JSToken::WITH_SPACE_ELSE) {
                    [$char, $offset] = $this->firstCharAfterSpaces($data, $i + 4);

                    if ($char === JSToken::I || $char === JSToken::O_CUR_BRACKET) {

                        print preg_replace(JSToken::PATT_D_SPACES, JSToken::NS, substr($data, $i, $offset - $i));
                        $i = $offset - 1;
                    }
                    continue;
                }
            }

            if ($curr === JSToken::I) {
                $stmt = substr($data, $i, 3);
                if ($stmt !== JSToken::WITH_SPACE_IF && $stmt !== JSToken::WITH_BRACKET_IF) {
                    print $curr;
                    continue;
                }

                $i = strpos($data, JSToken::O_PARENTHESES, $i) - 1;
                print JSToken::IF;
                continue;
            }

            print $curr;
        }

        return ob_get_clean();
    }

    private function hasNoCode(string $data): bool
    {
        return preg_match('~\w{2,}~', $data) === 0;
    }

    private function firstCharAfterSpaces(string $data, int $currPos): \SplFixedArray
    {
        $result = new \SplFixedArray(2);

        if (preg_match(JSToken::PATT_NON_SPACE, substr($data, $currPos + 1), $m, PREG_OFFSET_CAPTURE)) {
            $result[0] = $m[0][0];
            $result[1] = $currPos + (int) $m[0][1] + 1;
        }

        return $result;
    }
}
