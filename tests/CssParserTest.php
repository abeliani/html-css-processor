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

use Abeliani\CssJsHtmlOptimizer\Common\Interface\BlockInterface;
use Abeliani\CssJsHtmlOptimizer\Css;
use Abeliani\CssJsHtmlOptimizer\Css\Block\Rule;
use Codeception\Test\Unit;

class CssParserTest extends Unit
{
    public function testWrongCode(): void
    {
        $document = new Css\Parser\Document('     
                     / \__
                    (    @\____
                     /         O
                    /   (_____/
                   /_____/
        ');

        $this->assertEmpty($document->parse());
    }

    public function testCollectObjects(): void
    {
        $document = new Css\Parser\Document("body{font-family: 'Arial', sans-serif;}");

        $this->assertIsArray($document->parse());
        $this->assertNotEmpty($document->parse());
        $this->assertContainsOnlyInstancesOf(Rule::class, $document->parse());
        $this->assertContainsOnlyInstancesOf(BlockInterface::class, $document->parse());
    }

    public function testKeyframes(): void
    {
        $document = new Css\Parser\Document(
            '@keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }'
        );

        $this->assertEquals('@keyframes fadeIn', $document->parse()[0]->getCommand());
        $this->assertEquals('from{opacity:0}', $document->parse()[0]->getProperties()[0]);
        $this->assertEquals('to{opacity:1}', $document->parse()[0]->getProperties()[1]);

        $this->assertEquals('@keyframes fadeIn{from{opacity:0};to{opacity:1}}', (string) $document->parse()[0]);
        $this->assertEquals('from{opacity:0}', (string) $document->parse()[0]->getProperties()[0]);
        $this->assertEquals('to{opacity:1}', (string) $document->parse()[0]->getProperties()[1]);
    }

    public function testMedia(): void
    {
        $document = new Css\Parser\Document(
            '@media print {
                body {
                    color: black;
                    background: white;
                }
                .navbar, .footer {
                    display: none;
                }
            }'
        );

        $this->assertEquals('@media print', $document->parse()[0]->getCommand());

        $this->assertEquals('body', $document->parse()[0]->getProperties()[0]->getCommand());
        $this->assertEquals('color:black', $document->parse()[0]->getProperties()[0]->getProperties()[0]);
        $this->assertEquals('background:white', $document->parse()[0]->getProperties()[0]->getProperties()[1]);

        $this->assertEquals('.navbar,.footer', $document->parse()[0]->getProperties()[1]->getCommand());
        $this->assertEquals('display:none', $document->parse()[0]->getProperties()[1]->getProperties()[0]);

        $this->assertEquals('body{color:black;background:white}', (string) $document->parse()[0]->getProperties()[0]);
        $this->assertEquals('.navbar,.footer{display:none}', (string) $document->parse()[0]->getProperties()[1]);

        $expect = '@media print{body{color:black;background:white};.navbar,.footer{display:none}}';
        $this->assertEquals($expect, (string) $document->parse()[0]);
    }

    public function testRoot(): void
    {
        $document = new Css\Parser\Document('
                @import url("base.css");
                @import url("print.css") print;
                @import url("https://fonts.googleapis.com/css?family=Open+Sans");'
        );

        $this->assertEquals('@import', $document->parse()[0]->getCommand());
        $this->assertEquals('url("base.css")', $document->parse()[0]->getProperties()[0]);

        $this->assertEquals('@import', $document->parse()[1]->getCommand());
        $this->assertEquals('url("print.css")print', $document->parse()[1]->getProperties()[0]);

        $this->assertEquals('@import', $document->parse()[2]->getCommand());
        $url = 'url("https://fonts.googleapis.com/css?family=Open+Sans")';
        $this->assertEquals($url, $document->parse()[2]->getProperties()[0]);

    }

    public function testFontFace(): void
    {
        $document = new Css\Parser\Document(
            '@font-face {
                font-family: "MyHelvetica";
                src: local("Helvetica Neue Bold"),
                local("HelveticaNeue-Bold"),
                url("HelveticaNeue-Bold.woff2") format("woff2"),
                url("HelveticaNeue-Bold.woff") format("woff");
                font-weight: bold;
            }'
        );

        $this->assertEquals('@font-face', $document->parse()[0]->getCommand());
        $this->assertEquals('font-family:"MyHelvetica"', $document->parse()[0]->getProperties()[0]);

        $src = 'src:local("Helvetica Neue Bold"),local("HelveticaNeue-Bold"),url("HelveticaNeue-Bold.woff2")
        format("woff2"),url("HelveticaNeue-Bold.woff")format("woff")';

        $this->assertEquals(str_replace(["\r", "\n", '  '], '', $src), $document->parse()[0]->getProperties()[1]);
        $this->assertEquals('font-weight:bold', $document->parse()[0]->getProperties()[2]);
    }

    public function testRule(): void
    {
        $document = new Css\Parser\Document(
            "body {
                font-family: 'Arial', sans-serif;
                margin: 0;
                padding: 10px 3px 1px;
                background: var(--light-gray, #f4f4f4);
            }"
        );

        $this->assertEquals('body', $document->parse()[0]->getCommand());
        $this->assertEquals("font-family:'Arial',sans-serif", $document->parse()[0]->getProperties()[0]);
        $this->assertEquals('margin:0', $document->parse()[0]->getProperties()[1]);
        $this->assertEquals('padding:10px 3px 1px', $document->parse()[0]->getProperties()[2]);
        $this->assertEquals('background:var(--light-gray,#f4f4f4)', $document->parse()[0]->getProperties()[3]);
    }

    public function testClass(): void
    {
        $document = new Css\Parser\Document(
            '.container {
                --container-background: #f3f3f3
            }'
        );

        $this->assertEquals('.container', $document->parse()[0]->getCommand());
        $this->assertEquals('--container-background:#f3f3f3', $document->parse()[0]->getProperties()[0]);
    }

    public function testClassCombine(): void
    {
        $document = new Css\Parser\Document(
            '.container p {
                background: var(--container-background);
            }'
        );

        $this->assertEquals('.container p', $document->parse()[0]->getCommand());
        $this->assertEquals('background:var(--container-background)', $document->parse()[0]->getProperties()[0]);
    }

    public function testId(): void
    {
        $document = new Css\Parser\Document(
            '#main-article {
                    padding: 20px;
                    background-color: white;
                    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            }'
        );

        $this->assertEquals('#main-article', $document->parse()[0]->getCommand());
        $this->assertEquals('padding:20px', $document->parse()[0]->getProperties()[0]);
        $this->assertEquals('background-color:white', $document->parse()[0]->getProperties()[1]);
        $this->assertEquals('box-shadow:0 2px 5px rgba(0,0,0,0.1)', $document->parse()[0]->getProperties()[2]);
    }

    public function testPseudo(): void
    {
        $document = new Css\Parser\Document(
            'a:hover, a:active {
                text-decoration: underline;
            }'
        );

        $this->assertEquals('a:hover,a:active', $document->parse()[0]->getCommand());
        $this->assertEquals('text-decoration:underline', $document->parse()[0]->getProperties()[0]);
    }
}