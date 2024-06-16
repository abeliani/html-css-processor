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

namespace Abeliani\CssJsHtmlOptimizer\Js\Parser;

use Abeliani\CssJsHtmlOptimizer\Common\DocumentAbstract;
use Abeliani\CssJsHtmlOptimizer\Js\Parser\Trait\PrepareJsTrait;

final class Document extends DocumentAbstract
{
    use PrepareJsTrait;

    public function __construct(string $js)
    {
        parent::__construct($this->prepare($js), ';');
    }

    public function parse(): string
    {
        return $this->getRaw();
    }
}
