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

trait PrepareJsTrait
{
    use ClearCommentTrait;

    protected function prepare(string $data): string
    {
        $data = $this->clearSpaces($this->clearComments($data));
        return $this->hasNoCode($data) ? '' : $data;
    }

    private function clearSpaces(string $data): string
    {
        $operators = ['/', '*', '=', '|', '-', '+', '<', '>', '!', '~'];
        $length = strlen($data);
        $apostrophe = 0;
        $singleQuote = 0;
        $doubleQuote = 0;

        ob_start();
        for ($i = 0; ; $i++) {
            if ($i >= $length) {
                break;
            }
            $curr = $data[$i];
            $next = $data[$i + 1] ?? '';
            $prev = $data[$i - 1] ?? '';

            // content inside quotes is not processing
            if ($prev !== '\\') {
                if ($curr === '`') {
                    if ($apostrophe) {
                        $apostrophe--;
                    } else {
                        $apostrophe++;
                    }
                }
                if ($curr === "'") {
                    if ($singleQuote) {
                        $singleQuote--;
                    } else {
                        $singleQuote++;
                    }
                }
                if ($curr === '"') {
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
            if ($curr === ' ') {
                [$char, $offset] = $this->firstCharAfterSpaces($data, $i);
                if (in_array($char, $operators)) {
                    $i = $offset;
                    continue;
                }
                if ($prev === ' ' || $prev === ',') {
                    continue;
                }
                if (preg_match('~[^a-z]~i', $prev)) {
                    continue;
                }
                if ($next === '(' || $next === ')' || $next === '{') {
                    continue;
                }
            }
            if (in_array($curr, $operators) && preg_match('~\s~', $next)) {
                $pair = $this->firstCharAfterSpaces($data, $i);
                $i = $pair[1];
                print $curr;
                continue;
            }

            if ($curr === "\r" || $curr === "\n") {
                [$char, $offset] = $this->firstCharAfterSpaces($data, $i);
                if ($char === 't' && (substr($data, $offset + 1, 4) === 'this')) {
                    if ($this->firstCharBeforeSpaces($data, $i) === ')') {
                        $i = $offset;
                        print $curr;
                    }
                }
                continue;
            }

            if ($next === "\r" || $next === "\n") {
                if ($curr === ' ') {
                    continue;
                }
                if (preg_match('~[a-z0-9\]]~i', $curr)) {
                    [$char, $offset] = $this->firstCharAfterSpaces($data, $i);

                    if ($char === '}' || $char === ')' || $char === ']') {
                        $i = $offset + 1;
                        print sprintf('%s%s', $curr, $char);
                    } else {
                        print sprintf('%s;', $curr);
                    }

                    continue;
                }
                if ($curr === '}') {
                    [$char, $offset] = $this->firstCharAfterSpaces($data, $i);

                    if ($char === 'f' && (substr($data, $offset + 1, 8)) === 'function') {
                        print sprintf('%s;', $curr);
                        continue;
                    }
                }
            }

            if ($curr === ';') {
                if (!$next) {
                    break;
                }
                if ($nextBrakes = strpos($data, '}', $i)) {
                    if (!trim(substr($data, $i + 1, $nextBrakes - ($i + 1)))) {
                       $i = $nextBrakes;
                        print '}';
                        continue;
                    }
                }
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

        if (preg_match('~\S~', substr($data, $currPos + 1), $m, PREG_OFFSET_CAPTURE)) {
            $result[0] = $m[0][0];
            $result[1] = $currPos + (int) $m[0][1];
        }

        return $result;
    }

    private function firstCharBeforeSpaces(string $data, int $currPos): string
    {
        preg_match('~\S(?=\s*)~', substr($data, $currPos - 1), $m);
        return $m[0] ?? '';
    }
}