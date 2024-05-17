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

namespace Abeliani\CssJsHtmlOptimizer\Html\Optimizer\Processor;

use Abeliani\CssJsHtmlOptimizer\Common\ProcessorInterface;
use Abeliani\CssJsHtmlOptimizer\Js\Optimizer\Optimizer as JsOptimizer;
use Abeliani\CssJsHtmlOptimizer\Css\Optimizer\Optimizer as CssOptimizer;
use Abeliani\CssJsHtmlOptimizer\Js\Parser\Document as JsDocument;
use Abeliani\CssJsHtmlOptimizer\Css\Parser\Document as CssDocument;

class ScriptStyleOptimizer implements ProcessorInterface
{
    private const STYLE_TAG = 'style';
    private const SCRIPT_TAG = 'script';
    private const REPLACE_PATTERN = '<%s>%s</%s>';
    private const STYLE_SCRIPT_PATTERN = '~<(script|style)(?![^>]*\bsrc)[^>]*>(.*?)</\1>~si';

    /**
     * @throws \Exception
     */
    public function __invoke(string &$property): void
    {
        $property = preg_replace_callback(self::STYLE_SCRIPT_PATTERN, function (array $block) {
            $tag = $block[1];
            $optimizer = match ($block[1]) {
                self::STYLE_TAG => new CssOptimizer(new CssDocument($block[2])),
                self::SCRIPT_TAG => new JsOptimizer(new JsDocument($block[2]), []),
            };

            return sprintf(self::REPLACE_PATTERN, $tag, $optimizer->do()->flush(), $tag);
        }, $property);
    }
}
