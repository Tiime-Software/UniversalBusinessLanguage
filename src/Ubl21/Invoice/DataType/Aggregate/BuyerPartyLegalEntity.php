<?php

namespace Tiime\UniversalBusinessLanguage\Ubl21\Invoice\DataType\Aggregate;

use Tiime\EN16931\Codelist\InternationalCodeDesignator;
use Tiime\EN16931\DataType\Identifier\LegalRegistrationIdentifier;

class BuyerPartyLegalEntity
{
    protected const XML_NODE = 'cac:PartyLegalEntity';

    /**
     * BT-47.
     */
    private ?LegalRegistrationIdentifier $companyIdentifier;

    public function __construct()
    {
        $this->companyIdentifier = null;
    }

    public function getIdentifier(): ?LegalRegistrationIdentifier
    {
        return $this->companyIdentifier;
    }

    public function setIdentifier(?LegalRegistrationIdentifier $companyIdentifier): static
    {
        $this->companyIdentifier = $companyIdentifier;

        return $this;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);

        if ($this->companyIdentifier instanceof LegalRegistrationIdentifier) {
            $companyIdentifierElement = $document->createElement('cbc:CompanyID', $this->companyIdentifier->value);

            if ($this->companyIdentifier->scheme instanceof InternationalCodeDesignator) {
                $companyIdentifierElement->setAttribute('schemeID', $this->companyIdentifier->scheme->value);
            }

            $currentNode->appendChild($companyIdentifierElement);
        }

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): self
    {
        $buyerPartyLegalEntityElements = $xpath->query(\sprintf('./%s', self::XML_NODE), $currentElement);

        if (1 !== $buyerPartyLegalEntityElements->count()) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $buyerPartyLegalEntityElement */
        $buyerPartyLegalEntityElement = $buyerPartyLegalEntityElements->item(0);

        $companyIdentifierElements = $xpath->query('./cbc:CompanyID', $buyerPartyLegalEntityElement);

        if ($companyIdentifierElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        $buyerPartyLegalEntity = new self();

        if (1 === $companyIdentifierElements->count()) {
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

            $buyerPartyLegalEntity->setIdentifier(new LegalRegistrationIdentifier($companyIdentifier, $scheme));
        }

        return $buyerPartyLegalEntity;
    }
}
