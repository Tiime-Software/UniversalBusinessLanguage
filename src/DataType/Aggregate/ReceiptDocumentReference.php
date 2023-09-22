<?php

namespace Tiime\UniversalBusinessLanguage\DataType\Aggregate;

/**
 * BT-15.
 */
class ReceiptDocumentReference
{
    protected const XML_NODE = 'cac:ReceiptDocumentReference';

    private string $identifier;

    public function __construct(string $identifier)
    {
        $this->identifier = $identifier;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);

        $currentNode->appendChild($document->createElement('cbc:ID', $this->identifier));

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): ?self
    {
        $receiptDocumentReferenceElements = $xpath->query(sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $receiptDocumentReferenceElements->count()) {
            return null;
        }

        if ($receiptDocumentReferenceElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $receiptDocumentReferenceItem */
        $receiptDocumentReferenceItem = $receiptDocumentReferenceElements->item(0);

        $identifierElements = $xpath->query('./cbc:ID', $receiptDocumentReferenceItem);

        if (1 !== $identifierElements->count()) {
            throw new \Exception('Malformed');
        }

        $identifier = (string) $identifierElements->item(0)->nodeValue;

        return new self($identifier);
    }
}
