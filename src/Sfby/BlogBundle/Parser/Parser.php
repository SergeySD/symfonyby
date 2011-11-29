<?php

namespace Sfby\BlogBundle\Parser;

use Knp\Bundle\MarkdownBundle\Parser\MarkdownParser;


class Parser extends MarkdownParser
{

    protected function _doCodeBlocks_callback($matches)
    {
        $codeblock = $matches[1];

        $codeblock = $this->outdent($codeblock);
        $codeblock = htmlspecialchars($codeblock, ENT_NOQUOTES);

        # trim leading newlines and trailing newlines
        $codeblock = preg_replace('/\A\n+|\n+\z/', '', $codeblock);

        $codeblock = "<pre class='highlight'><code>$codeblock\n</code></pre>";
        return "\n\n".$this->hashBlock($codeblock)."\n\n";
    }

}

