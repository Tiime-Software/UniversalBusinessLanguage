<?php

declare(strict_types=1);

namespace Tiime\UniversalBusinessLanguage\DataType\Aggregate;

class BillingReference
{
    protected const XML_NODE = 'cac:BillingReference';

    private InvoiceDocumentReference $invoiceDocumentReference;

    public function __construct(InvoiceDocumentReference $invoiceDocumentReference)
    {
        $this->invoiceDocumentReference = $invoiceDocumentReference;
    }

    public function getInvoiceDocumentReference(): InvoiceDocumentReference
    {
        return $this->invoiceDocumentReference;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);

        $currentNode->appendChild($this->invoiceDocumentReference->toXML($document));

        return $currentNode;
    }

    /**
     * @return array<int, BillingReference>
     */
    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): array
    {
        $billingReferenceElements = $xpath->query(sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $billingReferenceElements->count()) {
            return [];
        }

        $billingReferences = [];

        /** @var \DOMElement $billingReferenceElement */
        foreach ($billingReferenceElements as $billingReferenceElement) {
            $invoiceDocumentReference = InvoiceDocumentReference::fromXML($xpath, $billingReferenceElement);

            $billingReference = new self($invoiceDocumentReference);

            $billingReferences[] = $billingReference;
        }

        return $billingReferences;
    }
}
