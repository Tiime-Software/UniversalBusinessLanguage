<?php

namespace Tiime\UniversalBusinessLanguage\DataType\Aggregate;

use Tiime\EN16931\DataType\Identifier\PayeeIdentifier;
use Tiime\EN16931\DataType\InternationalCodeDesignator;

class PayeePartyIdentification
{
    protected const XML_NODE = 'cac:PartyIdentification';

    /**
     * BT-60.
     */
    private PayeeIdentifier $buyerIdentifier;

    public function __construct(PayeeIdentifier $buyerIdentifier)
    {
        $this->buyerIdentifier = $buyerIdentifier;
    }

    public function getBuyerIdentifier(): PayeeIdentifier
    {
        return $this->buyerIdentifier;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);

        $buyerIdentifier = $document->createElement('cbc:ID', $this->buyerIdentifier->value);

        if ($this->buyerIdentifier->scheme instanceof InternationalCodeDesignator) {
            $buyerIdentifier->setAttribute('schemeID', $this->buyerIdentifier->scheme->value);
        }

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): ?self
    {
        $partyIdentificationElements = $xpath->query(sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $partyIdentificationElements->count()) {
            return null;
        }

        if ($partyIdentificationElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        $identifierElements = $xpath->query('./cbc:ID', $partyIdentificationElements);

        if (1 !== $identifierElements->count()) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $identifierElement */
        $identifierElement = $identifierElements->item(0);
        $value             = (string) $identifierElement->nodeValue;
        $scheme            = $identifierElement->hasAttribute('schemeID') ?
            InternationalCodeDesignator::tryFrom($identifierElement->getAttribute('schemeID')) : null;

        $identifier = new PayeeIdentifier($value, $scheme);

        return new self($identifier);
    }
}
