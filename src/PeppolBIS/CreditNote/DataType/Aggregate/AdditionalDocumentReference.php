<?php

namespace Tiime\UniversalBusinessLanguage\PeppolBIS\CreditNote\DataType\Aggregate;

use Tiime\EN16931\Codelist\ReferenceQualifierCodeUNTDID1153 as ReferenceQualifierCode;
use Tiime\EN16931\DataType\Identifier\ObjectIdentifier;

/**
 * BG-24.
 */
class AdditionalDocumentReference
{
    protected const XML_NODE = 'cac:AdditionalDocumentReference';

    /**
     * BT-18.
     */
    private ObjectIdentifier $identifier;

    /**
     * BT-18-1.
     */
    private ?string $documentTypeCode;

    /**
     * BT-123.
     */
    private ?string $documentDescription;

    private ?Attachment $attachment;

    public function __construct(ObjectIdentifier $identifier)
    {
        $this->identifier          = $identifier;
        $this->documentTypeCode    = null;
        $this->documentDescription = null;
        $this->attachment          = null;
    }

    public function getIdentifier(): ObjectIdentifier
    {
        return $this->identifier;
    }

    public function getDocumentTypeCode(): ?string
    {
        return $this->documentTypeCode;
    }

    public function setDocumentTypeCode(?string $documentTypeCode): static
    {
        $this->documentTypeCode = $documentTypeCode;

        return $this;
    }

    public function getDocumentDescription(): ?string
    {
        return $this->documentDescription;
    }

    public function setDocumentDescription(?string $documentDescription): static
    {
        $this->documentDescription = $documentDescription;

        return $this;
    }

    public function getAttachment(): ?Attachment
    {
        return $this->attachment;
    }

    public function setAttachment(?Attachment $attachment): static
    {
        $this->attachment = $attachment;

        return $this;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);

        $identifierElement = $document->createElement('cbc:ID', $this->identifier->value);

        if ($this->identifier->scheme instanceof ReferenceQualifierCode) {
            $identifierElement->setAttribute('schemeID', (string) $this->identifier->scheme->value);
        }

        $currentNode->appendChild($identifierElement);

        if (\is_string($this->documentTypeCode)) {
            $currentNode->appendChild($document->createElement('cbc:DocumentTypeCode', $this->documentTypeCode));
        }

        if (\is_string($this->documentDescription)) {
            $currentNode->appendChild($document->createElement('cbc:DocumentDescription', $this->documentDescription));
        }

        if ($this->attachment instanceof Attachment) {
            $currentNode->appendChild($this->attachment->toXML($document));
        }

        return $currentNode;
    }

    /**
     * @return array<int, AdditionalDocumentReference>
     */
    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): array
    {
        $additionalDocumentReferenceElements = $xpath->query(\sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $additionalDocumentReferenceElements->count()) {
            return [];
        }

        $additionalDocumentReferences = [];

        $invoiceCount = 0;

        /** @var \DOMElement $additionalDocumentReferenceElement */
        foreach ($additionalDocumentReferenceElements as $additionalDocumentReferenceElement) {
            $identifierElements          = $xpath->query('./cbc:ID', $additionalDocumentReferenceElement);
            $documentTypeCodeElements    = $xpath->query('./cbc:DocumentTypeCode', $additionalDocumentReferenceElement);
            $documentDescriptionElements = $xpath->query('./cbc:DocumentDescription', $additionalDocumentReferenceElement);

            if (1 !== $identifierElements->count()) {
                throw new \Exception('Malformed');
            }

            if ($documentTypeCodeElements->count() > 1) {
                throw new \Exception('Malformed');
            }

            if ($documentDescriptionElements->count() > 1) {
                throw new \Exception('Malformed');
            }

            $attachment = Attachment::fromXML($xpath, $additionalDocumentReferenceElement);

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

            $additionalDocumentReference = new self(new ObjectIdentifier($identifier, $scheme));

            if (1 === $documentTypeCodeElements->count()) {
                $documentTypeCode = (string) $documentTypeCodeElements->item(0)->nodeValue;

                if ('130' !== $documentTypeCode) {
                    throw new \Exception('Wrong TypeCode');
                }
                ++$invoiceCount;

                if ($invoiceCount > 1) {
                    throw new \Exception('PEPPOL-EN16931-R006 Only one invoiced object is allowed on document level');
                }
                $additionalDocumentReference->setDocumentTypeCode($documentTypeCode);
            }

            if (1 === $documentDescriptionElements->count()) {
                $additionalDocumentReference->setDocumentDescription((string) $documentDescriptionElements->item(0)->nodeValue);
            }

            if ($attachment instanceof Attachment) {
                $additionalDocumentReference->setAttachment($attachment);
            }

            $additionalDocumentReferences[] = $additionalDocumentReference;
        }

        return $additionalDocumentReferences;
    }
}
