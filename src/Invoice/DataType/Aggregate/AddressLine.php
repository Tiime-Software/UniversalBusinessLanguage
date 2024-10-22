<?php

namespace Tiime\UniversalBusinessLanguage\Invoice\DataType\Aggregate;

class AddressLine
{
    protected const XML_NODE = 'cac:AddressLine';

    /**
     * BT-162. or BT-163. or BT-164. or BT-165.
     */
    private string $line;

    public function __construct(string $line)
    {
        $this->line = $line;
    }

    public function getLine(): string
    {
        return $this->line;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);

        $currentNode->appendChild($document->createElement('cbc:Line', $this->line));

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): ?self
    {
        $countryElements = $xpath->query(\sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $countryElements->count()) {
            return null;
        }

        if (1 !== $countryElements->count()) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $countryElement */
        $countryElement = $countryElements->item(0);

        $lineElements = $xpath->query('./cbc:Line', $countryElement);

        if (1 !== $lineElements->count()) {
            throw new \Exception('Malformed');
        }

        return new self((string) $lineElements->item(0)->nodeValue);
    }
}
