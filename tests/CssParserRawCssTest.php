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

namespace Abeliani\CssJsHtmlOptimizer\Tests;

use Abeliani\CssJsHtmlOptimizer\Css;
use Codeception\Test\Unit;

class CssParserRawCssTest extends Unit
{
    private string $css;

    protected function setUp(): void
    {
        $this->css = file_get_contents(
            codecept_data_dir('style.css')
        );

        parent::setUp();
    }

    public function testLessWeight(): void
    {
        $document = new Css\Parser\Document($this->css);

        $this->assertLessThan(strlen($this->css), strlen($document->getRaw()));
    }

    public function testClearParser(): void
    {
        $document = new Css\Parser\Document($this->css);
        $document->clear();

        $this->assertEquals(0, strlen($document->getRaw()));
    }
}