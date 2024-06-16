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

namespace Abeliani\CssJsHtmlOptimizer\Tests;

use Abeliani\CssJsHtmlOptimizer\Css;
use Codeception\Test\Unit;

class CssOptimizeTest extends Unit
{
    public function testMarginPaddingMerge(): void
    {
        $document = new Css\Parser\Document('body {
            margin: 5px 5px 5px 5px;
            padding: 10rem 3rem 10rem 3rem;
        }');
        $optimizer = new Css\Optimizer\Optimizer($document);

        $this->assertEquals('body{margin:5px;padding:10rem 3rem}', $optimizer->do()->flush());
    }

    public function testHexColorShorter(): void
    {
        $document = new Css\Parser\Document('body {
            color: #ffcc00;
            background-color: #ffffff;
        }');
        $optimizer = new Css\Optimizer\Optimizer($document);
        $optimized = $optimizer->do()->flush();

        $this->assertStringContainsString('#fc0', $optimized);
        $this->assertStringContainsString('#fff', $optimized);
    }
}
