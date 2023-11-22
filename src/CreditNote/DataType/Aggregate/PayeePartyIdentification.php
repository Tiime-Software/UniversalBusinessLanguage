<?php

namespace Tiime\UniversalBusinessLanguage\CreditNote\DataType\Aggregate;

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

        $currentNode->appendChild($buyerIdentifier);

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): ?self
    {
        $partyIdentificationElements = $xpath->query(sprintf('./%s[cbc:ID[@schemeID!=\'SEPA\']]', self::XML_NODE), $currentElement);

        if (0 === $partyIdentificationElements->count()) {
            return null;
        }

        if ($partyIdentificationElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $partyIdentificationElement */
        $partyIdentificationElement = $partyIdentificationElements->item(0);

        $identifierElements = $xpath->query('./cbc:ID', $partyIdentificationElement);

        if (1 !== $identifierElements->count()) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $identifierElement */
        $identifierElement = $identifierElements->item(0);
        $value             = (string) $identifierElement->nodeValue;

        $scheme = null;

        if ($identifierElement->hasAttribute('schemeID')) {
            $scheme = InternationalCodeDesignator::tryFrom($identifierElement->getAttribute('schemeID'));

            if (!$scheme instanceof InternationalCodeDesignator) {
                throw new \Exception('Wrong schemeID');
            }
        }

        $identifier = new PayeeIdentifier($value, $scheme);

        return new self($identifier);
    }
}
