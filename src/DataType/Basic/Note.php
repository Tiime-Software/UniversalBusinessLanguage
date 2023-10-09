<?php

declare(strict_types=1);

namespace Tiime\UniversalBusinessLanguage\DataType\Basic;

/**
 * BG-1.
 */
class Note
{
    protected const XML_NODE = 'cbc:Note';

    /**
     * BT-22.
     */
    private string $content;

    public function __construct(string $content)
    {
        $this->content = $content;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE, $this->content);

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): ?self
    {
        $noteElements = $xpath->query(sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $noteElements->count()) {
            return null;
        }

        if ($noteElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        return new self((string) $noteElements->item(0)->nodeValue);
    }
}
