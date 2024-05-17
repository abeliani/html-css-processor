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

abstract class DocumentAbstract
{
    protected ?string $data;

    public function getRaw(): string
    {
        return $this->data ?? '';
    }

    public function clear(): void
    {
        $this->data = null;
    }

    abstract public function parse(): array|string;
}