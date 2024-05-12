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

namespace Abeliani\CssJsHtmlOptimizer\Css\Block;

use Abeliani\CssJsHtmlOptimizer\Css\Optimizer\Processor\ProcessorInterface;

abstract class Block implements BlockInterface
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

    public function optimize(ProcessorInterface ...$optimizers): void
    {
        foreach ($optimizers as $optimizer) {
            $this->process($this->properties, $optimizer);
        }
    }

    private function process(array &$properties, ProcessorInterface $processor): void
    {
        foreach ($properties as &$property) {
            if (is_subclass_of($property, self::class)) {
                $nestedProperties = $property->getProperties();
                $this->process($nestedProperties, $processor);
                continue;
            }
            $processor($this->command, $property);
        }
    }

    abstract function __toString(): string;
}
