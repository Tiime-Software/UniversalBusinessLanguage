<?php

namespace Tiime\UniversalBusinessLanguage\DataType\Aggregate;

use Tiime\EN16931\DataType\Identifier\SellerIdentifier;
use Tiime\EN16931\DataType\InternationalCodeDesignator;

/**
 * BT-29.
 */
class SellerPartyIdentification
{
    protected const XML_NODE = 'cac:PartyIdentification';

    private SellerIdentifier $sellerIdentifier;

    public function __construct(SellerIdentifier $sellerIdentifier)
    {
        $this->sellerIdentifier = $sellerIdentifier;
    }

    public function getBuyerIdentifier(): SellerIdentifier
    {
        return $this->sellerIdentifier;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);

        $sellerIdentifier = $document->createElement('cbc:ID', $this->sellerIdentifier->value);

        if ($this->sellerIdentifier->scheme instanceof InternationalCodeDesignator) {
            $sellerIdentifier->setAttribute('schemeID', $this->sellerIdentifier->scheme->value);
        }

        $currentNode->appendChild($sellerIdentifier);

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): array
    {
        $partyIdentificationElements = $xpath->query(sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $partyIdentificationElements->count()) {
            return [];
        }

        $partyIdentifications = [];

        /** @var \DOMElement $partyIdentificationElement */
        foreach ($partyIdentificationElements as $partyIdentificationElement) {
            $identifierElements = $xpath->query('./cbc:ID', $partyIdentificationElement);

            if (1 !== $identifierElements->count()) {
                throw new \Exception('Malformed');
            }

            /** @var \DOMElement $identifierElement */
            $identifierElement = $identifierElements->item(0);
            $value             = (string) $identifierElement->nodeValue;
            $scheme            = $identifierElement->hasAttribute('schemeID') ?
                InternationalCodeDesignator::tryFrom($identifierElement->getAttribute('schemeID')) : null;

            $identifier = new SellerIdentifier($value, $scheme);

            $partyIdentifications[] = new self($identifier);
        }

        return $partyIdentifications;
    }
}
