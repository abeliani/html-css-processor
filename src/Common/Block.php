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

use Abeliani\CssJsHtmlOptimizer\Common\Interface\BlockInterface;

abstract class Block extends ProcessableBlock implements BlockInterface
{
    protected string $command;
    protected array $properties;

    public function setCommand(string $command): self
    {
        $this->command = $command;

        return $this;
    }

    public function setProperties(array $properties): self
    {
        $this->properties = $properties;

        return $this;
    }

    public function getCommand(): string
    {
        return $this->command;
    }

    public function getProperties(): array
    {
        return $this->properties;
    }
}
