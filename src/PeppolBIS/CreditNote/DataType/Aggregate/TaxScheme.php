<?php

namespace Tiime\UniversalBusinessLanguage\PeppolBIS\CreditNote\DataType\Aggregate;

class TaxScheme
{
    protected const XML_NODE = 'cac:TaxScheme';

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

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): self
    {
        $taxSchemeElements = $xpath->query(\sprintf('./%s', self::XML_NODE), $currentElement);

        if (1 !== $taxSchemeElements->count()) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $taxSchemeElement */
        $taxSchemeElement = $taxSchemeElements->item(0);

        $identifierElements = $xpath->query('./cbc:ID', $taxSchemeElement);

        if (1 !== $identifierElements->count()) {
            throw new \Exception('Malformed');
        }

        $identifier = (string) $identifierElements->item(0)->nodeValue;

        return new self($identifier);
    }
}
