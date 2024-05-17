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

use Codeception\Test\Unit;
use Abeliani\CssJsHtmlOptimizer\Html;

class HtmlParseTest extends Unit
{
    public function testWrongCode(): void
    {
        $document = new Html\Parser\Document('     
                      (\__/)
                      (>'.'<)
                      (")_(")
        ');

        $this->assertEmpty($document->parse());
    }

    public function testCompress(): void
    {
        $document = new Html\Parser\Document(
                '<section>
                    <h2>Main content</h2> <p>bla bla bla.</p>
                </section>'
        );

        $stmt = '<section><h2>Main content</h2><p>bla bla bla.</p></section>';
        $this->assertEquals($stmt, $document->parse());
    }
}
