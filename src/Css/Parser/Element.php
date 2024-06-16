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

namespace Abeliani\CssJsHtmlOptimizer\Css\Parser;

use Abeliani\CssJsHtmlOptimizer\Css\Block\Import;
use Abeliani\CssJsHtmlOptimizer\Css\Block\Rule;

class Element
{
    private ?Rule $protoRule = null;
    private ?Import $protoImport = null;

    public function createRule(string $selector, array|string $props): Rule
    {
        if ($this->protoRule === null) {
            $this->protoRule = new Rule;
        }

        return (clone $this->protoRule)
            ->setCommand($selector)
            ->setProperties(is_array($props) ? $props : $this->extractProps($props));
    }

    public function createImport(string $selector, string $props): Import
    {
        if ($this->protoImport === null) {
            $this->protoImport = new Import;
        }

        return (clone $this->protoImport)
            ->setCommand($selector)
            ->setProperties($this->extractProps($props));
    }

    private static function extractProps(string $propsStmt): array
    {
        foreach (explode(';', $propsStmt) as $propStmt) {
            if ($propStmt = trim($propStmt)) {
                $propertyList[] = $propStmt;
            }
        }

        return $propertyList ?? [];
    }
}