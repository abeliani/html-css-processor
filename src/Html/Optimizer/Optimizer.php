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

namespace Abeliani\CssJsHtmlOptimizer\Html\Optimizer;

use Abeliani\CssJsHtmlOptimizer\Common\OptimizerAbstract;
use Abeliani\CssJsHtmlOptimizer\Html\Parser\Document;

class Optimizer extends OptimizerAbstract
{
    public function __construct(Document|array $documents, ?array $optimizers = null)
    {
        $set = $optimizers === null ? [
            new Processor\UnnecessaryRemover,
            new Processor\ScriptStyleOptimizer,
        ] : $optimizers;

        parent::__construct($documents, $set);
    }
}
