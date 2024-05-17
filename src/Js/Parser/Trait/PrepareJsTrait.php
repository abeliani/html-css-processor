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
    protected function prepare(string $data): string
    {
        $data = $this->clearSpaces($this->clearComments($data));

        if (!$this->hasNoCode($data)) {
            return '';
        }

        return $this->clearUnnecessarySymbols($data);
    }

    private function clearUnnecessarySymbols(string $data): string
    {
        return preg_replace([ '~;?(})~', '~;*$~'], ['$1', ''], $data);
    }

    private function clearSpaces(string $data): string
    {
        return preg_replace(['~\s+~', '~\s*([{}();])\s*~', '~\s*(===|==|=|\*|-|\|\|)\s*~'], [' ', '$1', '$1'], $data);
    }

    private function clearComments(string $data): string
    {
        return trim(preg_replace('~/\*[\s\S]*?\*/|([^:]|^)//.*$~', '', $data));
    }

    private function hasNoCode(string $data): bool
    {
        return preg_match('~\w{2,}~', $data) > 0;
    }
}