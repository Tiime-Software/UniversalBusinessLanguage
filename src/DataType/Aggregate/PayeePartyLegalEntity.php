<?php

namespace Tiime\UniversalBusinessLanguage\DataType\Aggregate;

use Tiime\EN16931\DataType\Identifier\LegalRegistrationIdentifier;
use Tiime\EN16931\DataType\InternationalCodeDesignator;

class PayeePartyLegalEntity
{
    protected const XML_NODE = 'cac:PartyLegalEntity';

    /**
     * BT-61.
     */
    private LegalRegistrationIdentifier $identifier;

    public function __construct(LegalRegistrationIdentifier $identifier)
    {
        $this->identifier = $identifier;
    }

    public function getIdentifier(): ?LegalRegistrationIdentifier
    {
        return $this->identifier;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);

        $identifierElement = $document->createElement('cbc:CompanyID', $this->identifier->value);

        if ($this->identifier->scheme instanceof InternationalCodeDesignator) {
            $identifierElement->setAttribute('schemeID', $this->identifier->scheme->value);
        }

        $currentNode->appendChild($identifierElement);

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): ?self
    {
        $payeePartyLegalEntityElements = $xpath->query(sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $payeePartyLegalEntityElements->count()) {
            return null;
        }

        if ($payeePartyLegalEntityElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $payeePartyLegalEntityElement */
        $payeePartyLegalEntityElement = $payeePartyLegalEntityElements->item(0);

        $identifierElements = $xpath->query('./cbc:CompanyID', $payeePartyLegalEntityElement);

        if (1 !== $identifierElements->count()) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $identifierElement */
        $identifierElement = $identifierElements->item(0);
        $identifier        = (string) $identifierElement->nodeValue;

        $scheme = null;

        if ($identifierElement->hasAttribute('schemeID')) {
            $scheme = InternationalCodeDesignator::tryFrom($identifierElement->getAttribute('schemeID'));

            if (!$scheme instanceof InternationalCodeDesignator) {
                throw new \Exception('Wrong schemeID');
            }
        }

        return new self(new LegalRegistrationIdentifier($identifier, $scheme));
    }
}
