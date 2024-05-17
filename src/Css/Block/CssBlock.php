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

use Abeliani\CssJsHtmlOptimizer\Common\Block;
use Abeliani\CssJsHtmlOptimizer\Common\ProcessorInterface;

abstract class CssBlock extends Block
{
    protected function process(array &$properties, ProcessorInterface $processor): void
    {
        foreach ($properties as &$property) {
            if (is_subclass_of($property, self::class)) {
                $nestedProperties = $property->getProperties();
                $this->process($nestedProperties, $processor);
                continue;
            }
            $processor($property);
        }
    }
}
