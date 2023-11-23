<?php

declare(strict_types=1);

namespace Tiime\UniversalBusinessLanguage\CreditNote\DataType\Aggregate;

use Tiime\EN16931\DataType\Reference\PurchaseOrderLineReference;

class OrderLineReference
{
    protected const XML_NODE = 'cac:OrderLineReference';

    /**
     * BT-132.
     */
    private PurchaseOrderLineReference $lineIdentifier;

    public function __construct(PurchaseOrderLineReference $lineIdentifier)
    {
        $this->lineIdentifier = $lineIdentifier;
    }

    public function getIdentifier(): PurchaseOrderLineReference
    {
        return $this->lineIdentifier;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);

        $currentNode->appendChild($document->createElement('cbc:LineID', $this->lineIdentifier->value));

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): ?self
    {
        $orderLineReferenceElements = $xpath->query(sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $orderLineReferenceElements->count()) {
            return null;
        }

        if (1 !== $orderLineReferenceElements->count()) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $orderLineReferenceElement */
        $orderLineReferenceElement = $orderLineReferenceElements->item(0);

        $identifierElements = $xpath->query('./cbc:LineID', $orderLineReferenceElement);

        if (1 !== $identifierElements->count()) {
            throw new \Exception('Malformed');
        }

        $identifier = (string) $identifierElements->item(0)->nodeValue;

        return new self(new PurchaseOrderLineReference($identifier));
    }
}
