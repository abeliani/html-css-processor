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

namespace Abeliani\CssJsHtmlOptimizer\Js\Block;

final class Func extends JsBlock
{
    public function __toString(): string
    {
        return sprintf('%s{%s}', $this->getCommand(), implode(';', $this->getProperties()));
    }
}
