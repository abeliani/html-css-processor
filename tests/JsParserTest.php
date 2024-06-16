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

use Abeliani\CssJsHtmlOptimizer\Js;
use Codeception\Test\Unit;

class JsParserTest extends Unit
{
    public function testWrongCode(): void
    {
        $document = new Js\Parser\Document('
                     /\_/\
                    ( o.o )
                     > ^ <
        ');

        $this->assertEmpty($document->parse());
    }
}
