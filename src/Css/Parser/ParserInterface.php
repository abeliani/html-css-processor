<?php

/**
 * This file is part of the CssJsHtmlOptimizer Project.
 *
 * @package     CssJsHtmlOptimizer
 * @author      Anatolii Belianin <belianianatoli@gmail.com>
 * @license     See LICENSE.md for license information
 * @link        https://github.com/abeliani/css-js-html-optimizer
 */

namespace Abeliani\CssJsHtmlOptimizer\Css\Parser;

use Abeliani\CssJsHtmlOptimizer\Css\Block\BlockInterface;

interface ParserInterface
{
    /**
     * @return BlockInterface[]
     */
    public function parse(): array;

    public function getRaw(): string;

    public function clear(): void;
}