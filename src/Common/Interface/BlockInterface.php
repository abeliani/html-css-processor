<?php

/**
 * This file is part of the CssJsHtmlOptimizer Project.
 *
 * @package     CssJsHtmlOptimizer
 * @author      Anatolii Belianin <belianianatoli@gmail.com>
 * @license     See LICENSE.md for license information
 * @link        https://github.com/abeliani/css-html-optimizer
 */

namespace Abeliani\CssJsHtmlOptimizer\Common\Interface;

interface BlockInterface extends \Stringable
{
    /**
     * @return BlockInterface[]
     */
    public function getProperties(): array;

    public function getCommand(): string;

    public function __toString(): string;
}
