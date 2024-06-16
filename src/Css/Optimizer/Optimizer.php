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

namespace Abeliani\CssJsHtmlOptimizer\Css\Optimizer;

use Abeliani\CssJsHtmlOptimizer\Common\Interface\RenderInterface;
use Abeliani\CssJsHtmlOptimizer\Common\OptimizerAbstract;
use Abeliani\CssJsHtmlOptimizer\Css\Block\Import;
use Abeliani\CssJsHtmlOptimizer\Css\Block\Rule;
use Abeliani\CssJsHtmlOptimizer\Css\Optimizer\Processor\HexColorShorter;
use Abeliani\CssJsHtmlOptimizer\Css\Optimizer\Processor\PaddingMarginMerger;
use Abeliani\CssJsHtmlOptimizer\Css\Parser\Document;

class Optimizer extends OptimizerAbstract
{
    public function __construct(Document|array $documents, ?array $optimizers = null)
    {
        $set = $optimizers === null ? [
            new PaddingMarginMerger,
            new HexColorShorter,
        ] : $optimizers;

        parent::__construct($documents, $set);
    }

    public function do(): self
    {
        foreach ($this->documents as $document) {
            /** @var $element Rule|Import */
            foreach ($document->parse() as $element) {
                $element->optimize(...$this->optimizers);
                fwrite($this->resource, sprintf('%s', $element));
            }
        }

        return $this;
    }

    public function flush(?RenderInterface $render = null): string
    {
        rewind($this->resource);

        $render?->process($this->resource);
        $result = stream_get_contents($this->resource);
        fclose($this->resource);

        return $result;
    }
}
