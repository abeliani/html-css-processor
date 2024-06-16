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

namespace Abeliani\CssJsHtmlOptimizer\Js\Optimizer;


use Abeliani\CssJsHtmlOptimizer\Common\OptimizerAbstract;
use Abeliani\CssJsHtmlOptimizer\Js\Parser\Document;

class Optimizer extends OptimizerAbstract
{
    public function __construct(Document|array $documents, ?array $optimizers = null)
    {
        $set = $optimizers === null ? [
            new Processor\FunctionNameShorter,
        ] : $optimizers;

        parent::__construct($documents, $set);
    }
}
