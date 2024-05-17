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

namespace Abeliani\CssJsHtmlOptimizer\Html\Optimizer\Processor;

use Abeliani\CssJsHtmlOptimizer\Common\ProcessorInterface;

class UnnecessaryRemover implements ProcessorInterface
{
    public function __invoke(string &$property): void
    {
        $property = preg_replace('~\s*type="text/(?:javascript|css)"\s*~i', '', $property);
    }
}
