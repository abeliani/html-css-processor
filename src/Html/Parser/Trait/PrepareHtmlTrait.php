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

namespace Abeliani\CssJsHtmlOptimizer\Html\Parser\Trait;

trait PrepareHtmlTrait
{
    protected function prepare(string $html): string
    {
        if (!preg_match('~<(\w+)(?:\s[^>]*)?>.*?</\1\s*>~si', $html)) {
            return '';
        }

        return $this->clearSpaces($this->clearComments($html));
    }

    private function clearSpaces(string $html): string
    {
        return trim(preg_replace('~>\s+<~', '><', $html));
    }

    private function clearComments(string $html): string
    {
        return preg_replace('~<!--.*?-->~', '', $html);
    }
}
