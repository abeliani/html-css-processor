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

final class Import extends CssBlock
{
    /**
     * The space after "@import" is not decoration: without it the at-keyword
     * and the url merge into the single ident "@importurl" and the browser
     * skips the statement whole.
     */
    public function __toString(): string
    {
        return sprintf('%s %s;', $this->command, $this->properties[0] ?? '');
    }
}
