<?php

/**
 * This file is part of the CssJsHtmlOptimizer Project.
 *
 * @package     CssJsHtmlOptimizer
 * @author      Anatolii Belianin <belianianatoli@gmail.com>
 * @license     See LICENSE.md for license information
 * @link        https://github.com/abeliani/css-js-html-optimizer
 */

namespace Abeliani\CssJsHtmlOptimizer\Css\Block;

enum RuleEnum: string
{
    case import = '@import';
    case media = '@media';
    case keyframes = '@keyframes';
    case fontface = '@font-face';
}
