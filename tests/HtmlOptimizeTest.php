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

use Abeliani\CssJsHtmlOptimizer\Html;
use Codeception\Test\Unit;

class HtmlOptimizeTest extends Unit
{
    private string $html;

    protected function setUp(): void
    {
        $this->html = file_get_contents(
            codecept_data_dir('index.html')
        );

        parent::setUp();
    }

    public function testRemoveUnnecessary(): void
    {
        $document = new Html\Parser\Document('
                <style type="text/css">body { margin: 0; }</style>
                <script type="text/javascript">alert("Hi!")</script>
        ');
        $optimized = (new Html\Optimizer\Optimizer($document))->do()->flush();

        $this->assertStringContainsString('<style>', $optimized);
        $this->assertStringContainsString('<script>', $optimized);
        $this->assertStringNotContainsString('type="text/css"', $optimized);
        $this->assertStringNotContainsString('type="text/javascript"', $optimized);
    }

    public function testOptimizeInlineScript(): void
    {
        $document = new Html\Parser\Document($this->html);
        $o = (new Html\Optimizer\Optimizer($document))->do()->flush();

        $this->assertStringContainsString('longName(', $o);
        $this->assertStringContainsString('function longName(text)', $o);
        $this->assertStringContainsString("<script>function longName(text){alert(text)}longName('Hi!')</script>", $o);
    }

    public function testOptimizeInlineStyle(): void
    {
        $document = new Html\Parser\Document($this->html);
        $optimized = (new Html\Optimizer\Optimizer($document))->do()->flush();

        $this->assertStringNotContainsString('margin: 1px 1px 1px 1px', $optimized);
        $this->assertStringContainsString('<style>body{margin:1px}</style>', $optimized);
    }

    public function testMergeDocuments(): void
    {
        $d1 = new Html\Parser\Document($this->html);
        $d2 = new Html\Parser\Document('<menu>some</menu>');
        $optimized = (new Html\Optimizer\Optimizer([$d1, $d2]))->do()->flush();

        $this->assertStringContainsString('<!DOCTYPE html>', $optimized);
        $this->assertStringContainsString('<menu>some</menu>', $optimized);
    }
}
