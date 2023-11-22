<?php

namespace Tiime\UniversalBusinessLanguage\Invoice\DataType\Aggregate;

use Tiime\EN16931\DataType\Identifier\LegalRegistrationIdentifier;
use Tiime\EN16931\DataType\InternationalCodeDesignator;

class PayeePartyLegalEntity
{
    protected const XML_NODE = 'cac:PartyLegalEntity';

    /**
     * BT-61.
     */
    private LegalRegistrationIdentifier $companyIdentifier;

    public function __construct(LegalRegistrationIdentifier $companyIdentifier)
    {
        $this->companyIdentifier = $companyIdentifier;
    }

    public function getIdentifier(): ?LegalRegistrationIdentifier
    {
        return $this->companyIdentifier;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);

        $companyIdentifierElement = $document->createElement('cbc:CompanyID', $this->companyIdentifier->value);

        if ($this->companyIdentifier->scheme instanceof InternationalCodeDesignator) {
            $companyIdentifierElement->setAttribute('schemeID', $this->companyIdentifier->scheme->value);
        }

        $currentNode->appendChild($companyIdentifierElement);

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

        $companyIdentifierElements = $xpath->query('./cbc:CompanyID', $payeePartyLegalEntityElement);

        if (1 !== $companyIdentifierElements->count()) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $companyIdentifierElement */
        $companyIdentifierElement = $companyIdentifierElements->item(0);
        $companyIdentifier        = (string) $companyIdentifierElement->nodeValue;

        $scheme = null;

        if ($companyIdentifierElement->hasAttribute('schemeID')) {
            $scheme = InternationalCodeDesignator::tryFrom($companyIdentifierElement->getAttribute('schemeID'));

            if (!$scheme instanceof InternationalCodeDesignator) {
                throw new \Exception('Wrong schemeID');
            }
        }

        return new self(new LegalRegistrationIdentifier($companyIdentifier, $scheme));
    }
}
