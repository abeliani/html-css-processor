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

abstract class DocumentAbstract
{
    /**
     * @param string|null $data
     * @param string $divider
     */
    public function __construct(protected ?string $data, private string $divider = '')
    {
    }

    public function getRaw(): string
    {
        return $this->data ?? '';
    }

    public function clear(): void
    {
        $this->data = null;
    }

    public function getDivider(): string
    {
        return $this->divider;
    }

    abstract public function parse(): array|string;
}