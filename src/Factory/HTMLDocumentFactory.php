<?php
declare(strict_types=1);

namespace WeProvide\AsyncDeferJs\Factory;

use Gt\Dom\HTMLDocument;

/**
 * Responsible for instantiating an {@see HTMLDocument} with a given HTML string
 */
class HTMLDocumentFactory
{

    /**
     * Create a new instance of {@see HTMLDocument} with a given HTML string
     *
     * @param string $html the HTML string with which to instantiate the {@see HTMLDocument}
     * @return HTMLDocument the {@see HTMLDocument} with the given HTML string as its (parsed) contents
     */
    public function create(string $html): HTMLDocument
    {
        return new HTMLDocument($html);
    }
}
