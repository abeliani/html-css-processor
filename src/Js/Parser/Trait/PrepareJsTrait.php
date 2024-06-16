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

namespace Abeliani\CssJsHtmlOptimizer\Js\Parser\Trait;

trait PrepareJsTrait
{
    protected function prepare(string $data): string
    {
        $data = trim($data, " \n\r\t\v\0;");
        return $this->hasNoCode($data) ? '' : $data;
    }

    private function hasNoCode(string $data): bool
    {
        return preg_match('~\w{2,}~', $data) === 0;
    }
}
