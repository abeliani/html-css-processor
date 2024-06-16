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

namespace Abeliani\CssJsHtmlOptimizer\Html\Parser;

use Abeliani\CssJsHtmlOptimizer\Common\DocumentAbstract;
use Abeliani\CssJsHtmlOptimizer\Html\Parser\Trait\PrepareHtmlTrait;

class Document extends DocumentAbstract
{
    use PrepareHtmlTrait;

    public function __construct(string $html)
    {
        parent::__construct($this->prepare($html));
    }

    public function parse(): string
    {
        return $this->getRaw();
    }
}
