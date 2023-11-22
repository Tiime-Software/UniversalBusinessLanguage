<?php

namespace Tiime\UniversalBusinessLanguage\CreditNote\DataType\Aggregate;

class BuyersItemIdentification
{
    protected const XML_NODE = 'cac:BuyersItemIdentification';

    /**
     * BT-156.
     */
    private string $buyersItemIdentifier;

    public function __construct(string $buyersItemIdentifier)
    {
        $this->buyersItemIdentifier = $buyersItemIdentifier;
    }

    public function getBuyersItemIdentifier(): string
    {
        return $this->buyersItemIdentifier;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);

        $currentNode->appendChild($document->createElement('cbc:ID', $this->buyersItemIdentifier));

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): ?self
    {
        $buyersItemIdentificationElements = $xpath->query(sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $buyersItemIdentificationElements->count()) {
            return null;
        }

        if ($buyersItemIdentificationElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $buyersItemIdentificationElement */
        $buyersItemIdentificationElement = $buyersItemIdentificationElements->item(0);

        $buyersItemIdentifierElements = $xpath->query('./cbc:ID', $buyersItemIdentificationElement);

        if (1 !== $buyersItemIdentifierElements->count()) {
            throw new \Exception('Malformed');
        }

        $buyersItemIdentifier = (string) $buyersItemIdentifierElements->item(0)->nodeValue;

        return new self($buyersItemIdentifier);
    }
}
