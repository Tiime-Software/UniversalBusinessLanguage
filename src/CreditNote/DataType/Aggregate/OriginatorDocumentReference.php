<?php

namespace Tiime\UniversalBusinessLanguage\CreditNote\DataType\Aggregate;

/**
 * BT-16.
 */
class OriginatorDocumentReference
{
    protected const XML_NODE = 'cac:OriginatorDocumentReference';

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
        $originatorDocumentReferenceElements = $xpath->query(\sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $originatorDocumentReferenceElements->count()) {
            return null;
        }

        if ($originatorDocumentReferenceElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $originatorDocumentReferenceElement */
        $originatorDocumentReferenceElement = $originatorDocumentReferenceElements->item(0);

        $identifierElements = $xpath->query('./cbc:ID', $originatorDocumentReferenceElement);

        if (1 !== $identifierElements->count()) {
            throw new \Exception('Malformed');
        }

        $identifier = (string) $identifierElements->item(0)->nodeValue;

        return new self($identifier);
    }
}
