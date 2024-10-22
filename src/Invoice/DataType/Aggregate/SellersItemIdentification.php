<?php

namespace Tiime\UniversalBusinessLanguage\Invoice\DataType\Aggregate;

class SellersItemIdentification
{
    protected const XML_NODE = 'cac:SellersItemIdentification';

    /**
     * BT-155.
     */
    private string $sellersItemIdentifier;

    public function __construct(string $sellersItemIdentifier)
    {
        $this->sellersItemIdentifier = $sellersItemIdentifier;
    }

    public function getSellersItemIdentifier(): string
    {
        return $this->sellersItemIdentifier;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);

        $currentNode->appendChild($document->createElement('cbc:ID', $this->sellersItemIdentifier));

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): ?self
    {
        $sellersItemIdentificationElements = $xpath->query(\sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $sellersItemIdentificationElements->count()) {
            return null;
        }

        if ($sellersItemIdentificationElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $sellersItemIdentificationElement */
        $sellersItemIdentificationElement = $sellersItemIdentificationElements->item(0);

        $sellersItemIdentifierElements = $xpath->query('./cbc:ID', $sellersItemIdentificationElement);

        if (1 !== $sellersItemIdentifierElements->count()) {
            throw new \Exception('Malformed');
        }

        $sellersItemIdentifier = (string) $sellersItemIdentifierElements->item(0)->nodeValue;

        return new self($sellersItemIdentifier);
    }
}
