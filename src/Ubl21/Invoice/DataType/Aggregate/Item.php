<?php

declare(strict_types=1);

namespace Tiime\UniversalBusinessLanguage\Ubl21\Invoice\DataType\Aggregate;

/**
 * BG-31.
 */
class Item
{
    protected const XML_NODE = 'cac:Item';

    /**
     * BT-153.
     */
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);
        $currentNode->appendChild($document->createElement('cbc:Name', $this->name));

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): self
    {
        $itemElements = $xpath->query(\sprintf('./%s', self::XML_NODE), $currentElement);

        if (1 !== $itemElements->count()) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $itemElement */
        $itemElement  = $itemElements->item(0);
        $nameElements = $xpath->query('./cbc:Name', $itemElement);

        if (1 !== $nameElements->count()) {
            throw new \Exception('Malformed');
        }

        return new self((string) $nameElements->item(0)->nodeValue);
    }
}
