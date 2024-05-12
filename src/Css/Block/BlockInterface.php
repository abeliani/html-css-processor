<?php

/**
 * This file is part of the CssJsHtmlOptimizer Project.
 *
 * @package     CssJsHtmlOptimizer
 * @author      Anatolii Belianin <belianianatoli@gmail.com>
 * @license     See LICENSE.md for license information
 * @link        https://github.com/abeliani/css-js-html-optimizer
 */

namespace Abeliani\CssJsHtmlOptimizer\Css\Block;

use Abeliani\CssJsHtmlOptimizer\Css\Optimizer\Processor\ProcessorInterface;

interface BlockInterface extends \Stringable
{
    /**
     * @return array|BlockInterface[]
     */
    public function getProperties(): array;

    public function getCommand(): string;

    public function optimize(ProcessorInterface ...$optimizers): void;

    public function __toString(): string;
}
