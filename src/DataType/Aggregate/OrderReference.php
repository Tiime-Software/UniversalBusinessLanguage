<?php

declare(strict_types=1);

namespace Tiime\UniversalBusinessLanguage\DataType\Aggregate;

use Tiime\EN16931\DataType\Reference\PurchaseOrderReference;
use Tiime\EN16931\DataType\Reference\SalesOrderReference;

class OrderReference
{
    protected const XML_NODE = 'cac:OrderReference';

    /**
     * BT-13.
     */
    private PurchaseOrderReference $identifier;

    /**
     * BT-14.
     */
    private ?SalesOrderReference $salesOrderIdentifier;

    public function __construct(PurchaseOrderReference $identifier)
    {
        $this->identifier           = $identifier;
        $this->salesOrderIdentifier = null;
    }

    public function getIdentifier(): PurchaseOrderReference
    {
        return $this->identifier;
    }

    public function setIdentifier(PurchaseOrderReference $identifier): static
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getSalesOrderIdentifier(): ?SalesOrderReference
    {
        return $this->salesOrderIdentifier;
    }

    public function setSalesOrderIdentifier(?SalesOrderReference $salesOrderIdentifier): static
    {
        $this->salesOrderIdentifier = $salesOrderIdentifier;

        return $this;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);

        $currentNode->appendChild($document->createElement('cbc:ID', $this->identifier->value));

        if ($this->salesOrderIdentifier instanceof SalesOrderReference) {
            $currentNode->appendChild($document->createElement('cbc:SalesOrderID', $this->salesOrderIdentifier->value));
        }

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): ?self
    {
        $orderReferenceElements = $xpath->query(sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $orderReferenceElements->count()) {
            return null;
        }

        if (1 !== $orderReferenceElements->count()) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $orderReferenceElement */
        $orderReferenceElement = $orderReferenceElements->item(0);

        $identifierElements           = $xpath->query('./cbc:ID', $orderReferenceElement);
        $salesOrderIdentifierElements = $xpath->query('./cbc:SalesOrderID', $orderReferenceElement);

        if (1 !== $identifierElements->count()) {
            throw new \Exception('Malformed');
        }

        if (1 < $salesOrderIdentifierElements->count()) {
            throw new \Exception('Malformed');
        }

        $identifier     = (string) $identifierElements->item(0)->nodeValue;
        $orderReference = new self(new PurchaseOrderReference($identifier));

        if (1 === $salesOrderIdentifierElements->count()) {
            $orderReference->setSalesOrderIdentifier(new SalesOrderReference($salesOrderIdentifierElements->item(0)->nodeValue));
        }

        return $orderReference;
    }
}
