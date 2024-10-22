<?php

namespace Tiime\UniversalBusinessLanguage\Ubl21\CreditNote\DataType\Aggregate;

/**
 * BT-11.
 */
class ContractDocumentReference
{
    protected const XML_NODE = 'cac:ContractDocumentReference';

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
        $contractDocumentReferenceElements = $xpath->query(\sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $contractDocumentReferenceElements->count()) {
            return null;
        }

        if ($contractDocumentReferenceElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $contractDocumentReferenceElement */
        $contractDocumentReferenceElement = $contractDocumentReferenceElements->item(0);

        $identifierElements = $xpath->query('./cbc:ID', $contractDocumentReferenceElement);

        if (1 !== $identifierElements->count()) {
            throw new \Exception('Malformed');
        }

        $identifier = (string) $identifierElements->item(0)->nodeValue;

        return new self($identifier);
    }
}
