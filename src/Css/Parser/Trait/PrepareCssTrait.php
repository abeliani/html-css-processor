<?php

/**
 * This file is part of the CssJsHtmlOptimizer Project.
 *
 * @package     CssJsHtmlOptimizer
 * @author      Anatolii Belianin <belianianatoli@gmail.com>
 * @license     See LICENSE.md for license information
 * @link        https://github.com/abeliani/css-html-optimizer
 */

declare(strict_types=1);

namespace Abeliani\CssJsHtmlOptimizer\Css\Parser\Trait;

trait PrepareCssTrait
{
    /**
     * A run of whitespace directly after one of these is always droppable:
     * none of them can merge with the token that follows, and none of them
     * ends a selector part, so the space cannot be a descendant combinator.
     */
    private const DROPPABLE_AFTER = ',;{}(:';

    /**
     * A run of whitespace directly before one of these is always droppable.
     *
     * "(" and ":" are deliberately absent. A space in front of them carries
     * meaning: "and (" tokenises as the single function token "and(" once the
     * space is gone, and "ul :first-child" collapses into the compound
     * selector "ul:first-child", which matches a different element.
     */
    private const DROPPABLE_BEFORE = ',;{})';

    private const WHITESPACE = [' ' => true, "\t" => true, "\n" => true, "\r" => true, "\f" => true];

    protected function prepare(string $css): string
    {
        return $this->minify($css);
    }

    /**
     * One left-to-right pass: comments are dropped, quoted values are copied
     * verbatim, and a run of whitespace survives unless both of its
     * neighbours agree it is redundant.
     *
     * Whitespace in CSS is not decoration. It is the descendant combinator
     * (".a:not(.b) .c"), it keeps a keyword from swallowing the parenthesis
     * that follows it ("@media (a) and (b)"), and it is required on both
     * sides of "+" and "-" inside calc(). Dropping it wholesale next to
     * punctuation rewrites all three: the first silently starts matching
     * other elements, the other two are thrown away by the browser without
     * a word. The decision is therefore made per position, not per character.
     */
    private function minify(string $css): string
    {
        $out = '';
        $length = strlen($css);
        $pendingSpace = false;

        for ($i = 0; $i < $length; $i++) {
            $char = $css[$i];

            // A comment produces no token at all, so ".a/*x*/.b" is ".a.b".
            // The pending-space flag is left exactly as it was found: the
            // comment neither introduces a separator nor consumes one.
            if ($char === '/' && ($css[$i + 1] ?? '') === '*') {
                $end = strpos($css, '*/', $i + 2);
                $i = $end === false ? $length : $end + 1;

                continue;
            }

            if (isset(self::WHITESPACE[$char])) {
                $pendingSpace = true;

                continue;
            }

            if ($pendingSpace) {
                $pendingSpace = false;
                $prev = $out === '' ? '' : $out[-1];

                if ($prev !== ''
                    && !str_contains(self::DROPPABLE_AFTER, $prev)
                    && !str_contains(self::DROPPABLE_BEFORE, $char)
                ) {
                    $out .= ' ';
                }
            }

            // A quoted value is data, not syntax: the space in content: ", "
            // is part of the text, and a "(" inside it opens nothing.
            if ($char === '"' || $char === "'") {
                $end = $this->stringEnd($css, $i);
                $out .= substr($css, $i, $end - $i + 1);
                $i = $end;

                continue;
            }

            // The last declaration of a block needs no ";" before the brace.
            if ($char === '}' && ($out[-1] ?? '') === ';') {
                $out = substr($out, 0, -1);
            }

            $out .= $char;
        }

        return trim($out);
    }

    /**
     * Offset of the closing quote — or of the last byte, when the string is
     * left unterminated, which is where the CSS parser ends it as well.
     */
    private function stringEnd(string $css, int $start): int
    {
        $quote = $css[$start];
        $length = strlen($css);

        for ($i = $start + 1; $i < $length; $i++) {
            if ($css[$i] === '\\') {
                $i++;

                continue;
            }

            if ($css[$i] === $quote) {
                return $i;
            }
        }

        return $length - 1;
    }
}
