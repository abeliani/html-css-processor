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
    protected function prepare(string $css): string
    {
        return $this->clearSpaces($this->clearComments($css));
    }

    private function clearSpaces(string $css): string
    {
        return preg_replace(['~([,:;{}()])\s+~', '~\s+([,:;{}()])~', '~;}~', '~\s+~'], ['$1', '$1', '}', ' '], $css);
    }

    private function clearComments(string $css): string
    {
        return preg_replace('~/\*.*?\*/~s', '', $css);
    }
}
