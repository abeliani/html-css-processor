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

namespace Abeliani\CssJsHtmlOptimizer\Css\Block;

class Rule extends CssBlock
{
    public function __toString(): string
    {
        return sprintf('%s{%s}', $this->command, implode($this->separator(), $this->properties));
    }

    /**
     * Nested blocks (@media, @keyframes) hold rules, not declarations:
     * a ";" between them is a parse error, the browser drops every rule
     * after the first one. Declarations still need the ";".
     */
    private function separator(): string
    {
        foreach ($this->properties as $property) {
            if ($property instanceof CssBlock) {
                return '';
            }
        }

        return ';';
    }
}
