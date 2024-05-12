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

namespace Abeliani\CssJsHtmlOptimizer\Css\Optimizer;

use Abeliani\CssJsHtmlOptimizer\Css\Optimizer\Processor\HexColorShorter;
use Abeliani\CssJsHtmlOptimizer\Css\Optimizer\Processor\PaddingMarginMerger;
use Abeliani\CssJsHtmlOptimizer\Css\Parser\Document;
use Abeliani\CssJsHtmlOptimizer\Css\Render\RenderInterface;

class Optimizer implements OptimizerInterface
{
    private array $optimizer;

    /**
     * @var resource|false
     */
    private mixed $resource;

    public function __construct(private readonly Document $document)
    {
        if (!$this->resource = tmpfile()) {
            throw new \Exception('Failed to create tmp file');
        }

        $this->optimizer = [
            new PaddingMarginMerger,
            new HexColorShorter,
        ];
    }

    public function do(): self
    {
        foreach ($this->document->parse() as $element) {
            $element->optimize(...$this->optimizer);
            fwrite($this->resource, sprintf('%s', $element));
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
