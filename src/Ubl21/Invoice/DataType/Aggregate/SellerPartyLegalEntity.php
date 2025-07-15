<?php

namespace Tiime\UniversalBusinessLanguage\Ubl21\Invoice\DataType\Aggregate;

use Tiime\EN16931\Codelist\InternationalCodeDesignator;
use Tiime\EN16931\DataType\Identifier\LegalRegistrationIdentifier;

class SellerPartyLegalEntity
{
    protected const XML_NODE = 'cac:PartyLegalEntity';

    /**
     * BT-30.
     */
    private ?LegalRegistrationIdentifier $companyIdentifier;

    /**
     * BT-33.
     */
    private ?string $companyLegalForm;

    public function __construct()
    {
        $this->companyIdentifier = null;
        $this->companyLegalForm  = null;
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

    public function getCompanyLegalForm(): ?string
    {
        return $this->companyLegalForm;
    }

    public function setCompanyLegalForm(?string $companyLegalForm): static
    {
        $this->companyLegalForm = $companyLegalForm;

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

        if (\is_string($this->companyLegalForm)) {
            $currentNode->appendChild($document->createElement('cbc:CompanyLegalForm', $this->companyLegalForm));
        }

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): self
    {
        $sellerPartyLegalEntityElements = $xpath->query(\sprintf('./%s', self::XML_NODE), $currentElement);

        if (1 !== $sellerPartyLegalEntityElements->count()) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $sellerPartyLegalEntityElement */
        $sellerPartyLegalEntityElement = $sellerPartyLegalEntityElements->item(0);

        $companyIdentifierElements = $xpath->query('./cbc:CompanyID', $sellerPartyLegalEntityElement);
        $companyLegalFormElements  = $xpath->query('./cbc:CompanyLegalForm', $sellerPartyLegalEntityElement);

        if ($companyIdentifierElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        if ($companyLegalFormElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        $sellerPartyLegalEntity = new self();

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

            $sellerPartyLegalEntity->setIdentifier(new LegalRegistrationIdentifier($companyIdentifier, $scheme));
        }

        if (1 === $companyLegalFormElements->count()) {
            $sellerPartyLegalEntity->setCompanyLegalForm((string) $companyLegalFormElements->item(0)->nodeValue);
        }

        return $sellerPartyLegalEntity;
    }
}
