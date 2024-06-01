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

namespace Abeliani\CssJsHtmlOptimizer\Common;

use Abeliani\CssJsHtmlOptimizer\Common\Interface\OptimizerInterface;
use Abeliani\CssJsHtmlOptimizer\Common\Interface\RenderInterface;
use Abeliani\CssJsHtmlOptimizer\Html\Parser\Document;

abstract class OptimizerAbstract implements OptimizerInterface
{
    /**
     * @var Document|Document[]
     */
    protected array $documents;
    /**
     * @var ProcessorInterface[]
     */
    protected array $optimizers;
    /**
     * @var resource|false
     */
    protected mixed $resource;

    /**
     * @param DocumentAbstract|DocumentAbstract[] $documents
     * @param ProcessorInterface[] $optimizers
     *
     * @throws \Exception
     */
    public function __construct(DocumentAbstract|array $documents, array $optimizers)
    {
        if (!$this->resource = tmpfile()) {
            throw new \Exception('Failed to create tmp file');
        }

        $this->optimizers = $optimizers;
        $this->documents = !is_array($documents) ? [$documents] : $documents;
    }

    /**
     * @throws \Exception
     */
    public function do(): self
    {
        for ($i = 0; $i < count($this->documents); $i++) {
            $document = $this->documents[$i];
            $data = $document->parse();

            foreach ($this->optimizers as $optimizer) {
                $optimizer($data);
            }

            fwrite($this->resource, $data);

            if (isset($this->documents[$i + 1])) {
                fwrite($this->resource, $document->getDivider());
            }
        }

        return $this;
    }

    public function flush(RenderInterface|null $render = null): string
    {
        rewind($this->resource);

        $render?->process($this->resource);
        $result = stream_get_contents($this->resource);
        fclose($this->resource);

        return $result;
    }
}
