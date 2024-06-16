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

namespace Abeliani\CssJsHtmlOptimizer\Common;

use Abeliani\CssJsHtmlOptimizer\Common\Interface\ProcessableInterface;

abstract class ProcessableBlock implements ProcessableInterface
{
    public function optimize(ProcessorInterface ...$optimizers): void
    {
        foreach ($optimizers as $optimizer) {
            $this->process($this->properties, $optimizer);
        }
    }

    abstract protected function process(array &$properties, ProcessorInterface $processor): void;
}
