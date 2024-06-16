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

namespace Abeliani\CssJsHtmlOptimizer\Css\Optimizer\Processor;

use Abeliani\CssJsHtmlOptimizer\Common\ProcessorInterface;

class PaddingMarginMerger implements ProcessorInterface
{
    private const MARGIN = 'margin:';
    private const PADDING = 'padding:';

    public function __invoke(string &$property): void
    {
        if (str_starts_with($property, self::MARGIN) || str_starts_with($property, self::PADDING)) {
            [$name, $stmt] = explode(':', $property);
            $values = explode(' ', $stmt);

            switch (count($values)) {
                case 2:
                    if ($this->bothEquals($values)) {
                        $property = sprintf('%s:%s', $name, $values[0]);
                    }
                    break;
                case 4:
                    if ($this->verticalHorizontalEquals($values)) {
                        if ($this->bothEquals($values)) {
                            $property = sprintf('%s:%s', $name, $values[0]);
                        } else {
                            $property = sprintf('%s:%s %s', $name, $values[0], $values[1]);
                        }
                    }
                    break;
            }
        }
    }

    private function verticalHorizontalEquals(array $values): bool
    {
        return $values[0] == $values[2] && $values[1] == $values[3];
    }

    private function bothEquals(array $values): bool
    {
        return $values[0] == $values[1];
    }
}
