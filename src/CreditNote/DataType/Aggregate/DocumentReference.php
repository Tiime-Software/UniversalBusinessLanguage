<?php

namespace Tiime\UniversalBusinessLanguage\CreditNote\DataType\Aggregate;

use Tiime\EN16931\Codelist\ReferenceQualifierCodeUNTDID1153 as ReferenceQualifierCode;
use Tiime\EN16931\DataType\Identifier\ObjectIdentifier;

class DocumentReference
{
    protected const XML_NODE = 'cac:DocumentReference';

    /**
     * BT-128.
     */
    private ObjectIdentifier $identifier;

    /**
     * BT-128-1.
     */
    private string $documentTypeCode;

    public function __construct(ObjectIdentifier $identifier)
    {
        $this->identifier       = $identifier;
        $this->documentTypeCode = '130';
    }

    public function getIdentifier(): ObjectIdentifier
    {
        return $this->identifier;
    }

    public function getDocumentTypeCode(): string
    {
        return $this->documentTypeCode;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);

        $identifierElement = $document->createElement('cbc:ID', $this->identifier->value);

        if ($this->identifier->scheme instanceof ReferenceQualifierCode) {
            $identifierElement->setAttribute('schemeID', (string) $this->identifier->scheme->value);
        }

        $currentNode->appendChild($identifierElement);

        $currentNode->appendChild($document->createElement('cbc:DocumentTypeCode', $this->documentTypeCode));

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): ?self
    {
        $documentReferenceElements = $xpath->query(\sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $documentReferenceElements->count()) {
            return null;
        }

        if ($documentReferenceElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $documentReferenceElement */
        $documentReferenceElement = $documentReferenceElements->item(0);

        $identifierElements       = $xpath->query('./cbc:ID', $documentReferenceElement);
        $documentTypeCodeElements = $xpath->query('./cbc:DocumentTypeCode', $documentReferenceElement);

        if (1 !== $identifierElements->count()) {
            throw new \Exception('Malformed');
        }

        if (1 !== $documentTypeCodeElements->count()) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMNode $identifierElement */
        $identifierElement = $identifierElements->item(0);

        $scheme = null;

        if ($identifierElement->hasAttribute('schemeID')) {
            $scheme = ReferenceQualifierCode::tryFrom($identifierElement->getAttribute('schemeID'));

            if (!$scheme instanceof ReferenceQualifierCode) {
                throw new \Exception('Wrong schemeID');
            }
        }

        $identifier = (string) $identifierElement->nodeValue;

        $documentTypeCode = (string) $documentTypeCodeElements->item(0)->nodeValue;

        if ('130' !== $documentTypeCode) {
            throw new \Exception('Wrong TypeCode');
        }

        return new self(new ObjectIdentifier($identifier, $scheme));
    }
}
