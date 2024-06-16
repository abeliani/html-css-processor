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

class HexColorShorter implements ProcessorInterface
{
    private const COLOR_PATTERN = '#%s%s%s';
    private const HEX_COLOR_PATTERN = '~#[a-f0-9]{6}~i';

    /**
     * #ffffff to #fff
     * #ffcc00 to #fc0
     */
    public function __invoke(string &$property): void
    {
        if (preg_match_all(self::HEX_COLOR_PATTERN, $property, $colors)) {
            foreach ($colors as $color) {
                $color = array_shift($color);

                if ($color[1] == $color[2] && $color[3] == $color[4] && $color[5] == $color[6]) {
                    $property = str_replace(
                        $color,
                        sprintf(self::COLOR_PATTERN, $color[1], $color[3], $color[5]), $property
                    );
                }
            }
        }
    }
}
