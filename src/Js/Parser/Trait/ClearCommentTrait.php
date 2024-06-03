<?php

declare(strict_types=1);

namespace Abeliani\CssJsHtmlOptimizer\Js\Parser\Trait;

trait ClearCommentTrait
{
    private function clearComments(string $data): string
    {
        $inSingleQuote = $inDoubleQuote = false;
        $inBacktick = $inBlockComment = $inLineComment = false;
        $dataLength = strlen($data);

        ob_start();
        for ($i = 0; $i < $dataLength; $i++) {
            $current = $data[$i];
            $next = $data[$i + 1] ?? '';
            $prev = $data[$i - 1] ?? '';

            if ($inBlockComment) {
                if ($current === '*' && $next === '/') {
                    $inBlockComment = false;
                    $i++;
                }
                continue;
            }
            if ($inLineComment) {
                if ($current === "\n") {
                    $inLineComment = false;
                }
                continue;
            }
            if ($inSingleQuote) {
                if ($current === "'" && $prev !== '\\') {
                    $inSingleQuote = false;
                }
                print $current;
                continue;
            }
            if ($inDoubleQuote) {
                if ($current === '"' && $prev !== '\\') {
                    $inDoubleQuote = false;
                }
                print $current;
                continue;
            }
            if ($inBacktick) {
                if ($current === '`' && $prev !== '\\') {
                    $inBacktick = false;
                }
                print $current;
                continue;
            }
            if ($current === '/' && $next === '*') {
                $inBlockComment = true;
                $i++;
                continue;
            }
            if ($current === '/' && $next === '/') {
                $inLineComment = true;
                $i++;
                continue;
            }
            if ($current === "'") {
                $inSingleQuote = true;
                print $current;
                continue;
            }
            if ($current === '"') {
                $inDoubleQuote = true;
                print $current;
                continue;
            }
            if ($current === '`') {
                $inBacktick = true;
                print $current;
                continue;
            }

            print $current;
        }

        return trim(ob_get_clean());
    }
}
