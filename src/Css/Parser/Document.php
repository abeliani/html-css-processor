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

namespace Abeliani\CssJsHtmlOptimizer\Css\Parser;

use Abeliani\CssJsHtmlOptimizer\Css\Parser\Trait\CheckerBlockTrait;
use Abeliani\CssJsHtmlOptimizer\Css\Parser\Trait\PrepareCssTrait;

class Document implements ParserInterface
{
    use PrepareCssTrait, CheckerBlockTrait;

    private const STARTS_WITH_AT = '@';
    private const STARTS_WITH_OPEN = '{';
    private const STARTS_WITH_CLOSE = '}';
    private const SEMICOLON = ';';

    protected string $css;
    private Element $proto;
    private ?array $document = null;

    public function __construct(string $css)
    {
        $this->proto = new Element;
        $this->css = $this->prepare($css);
    }

    public function parse(): array
    {
        if ($this->document !== null) {
            return $this->document;
        }

        $document = [];
        $length = strlen($this->css);

        for ($i = 0, $start = $braceCount = 0; $i < $length; $i++) {
            switch ($this->css[$i]) {
                case self::STARTS_WITH_OPEN:
                    if ($braceCount === 0) {
                        $selector = trim(substr($this->css, $start, $i - $start));

                        $start = $i + 1;
                    }
                    $braceCount++;
                    break;
                case self::STARTS_WITH_CLOSE:
                    $braceCount--;
                    if ($braceCount === 0 && isset($selector)) {
                        $document[] = $this->proto->createRule($selector, substr($this->css, $start, $i - $start));

                        $start = $i + 1;
                    }
                    break;
                case self::STARTS_WITH_AT:
                    if ($this->isImport($i)) {
                        $stmtEnd = strpos($this->css, self::SEMICOLON, $i);
                        [$import, $param] = explode(' ', substr($this->css, $i, $stmtEnd - $i));
                        $document[] = $this->proto->createImport($import, $param);

                        $i = $stmtEnd;
                        $start = $i + 1;
                        break;
                    } elseif ($this->isFontFace($i)) {
                        $stmtEnd = strpos($this->css, self::STARTS_WITH_CLOSE, $i);
                        $selectorStmtEnd = strpos($this->css, self::STARTS_WITH_OPEN, $i);

                        $document[] = $this->proto->createRule(
                            substr($this->css, $i, $selectorStmtEnd - $i),
                            substr($this->css, $selectorStmtEnd + 1, $stmtEnd - $selectorStmtEnd - 1)
                        );

                        $i = $stmtEnd;
                        $start = $i + 1;
                    } elseif ($this->isMedia($i) || $this->isKeyFrames($i)) {
                        for($mediaBraceCount = 0, $im = $i; ;$im++) {
                            switch ($this->css[$im]) {
                                case self::STARTS_WITH_OPEN:
                                    $mediaBraceCount++;
                                    break;
                                case self::STARTS_WITH_CLOSE:
                                    $mediaBraceCount--;
                                    if ($mediaBraceCount === 0) {
                                        $stmtEnd = strpos($this->css, self::STARTS_WITH_OPEN, $i) + 1;
                                        $subDocument = new Document(substr($this->css, $stmtEnd, $im - $stmtEnd));

                                        $document[] = $this->proto->createRule(
                                            substr($this->css, $i, $stmtEnd - $i - 1),
                                            $subDocument->parse()
                                        );

                                        break 2;
                                    }
                            }
                        }
                        $i = $im;
                        $start = $i + 1;
                        break;
                    }
            }
        }

        return $this->document = $document;
    }

    public function getRaw(): string
    {
        return $this->css;
    }

    public function clear(): void
    {
        $this->css = '';
        $this->document = null;
    }
}
