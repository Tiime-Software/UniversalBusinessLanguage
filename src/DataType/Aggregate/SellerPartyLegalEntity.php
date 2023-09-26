<?php

namespace Tiime\UniversalBusinessLanguage\DataType\Aggregate;

use Tiime\EN16931\DataType\Identifier\LegalRegistrationIdentifier;
use Tiime\EN16931\DataType\InternationalCodeDesignator;

class SellerPartyLegalEntity
{
    protected const XML_NODE = 'cac:PartyLegalEntity';

    /**
     * BT-27.
     */
    private string $registrationName;

    /**
     * BT-30.
     */
    private ?LegalRegistrationIdentifier $identifier;

    /**
     * BT-33.
     */
    private ?string $companyLegalForm;

    public function __construct(string $registrationName)
    {
        $this->registrationName = $registrationName;
        $this->identifier       = null;
        $this->companyLegalForm = null;
    }

    public function getRegistrationName(): string
    {
        return $this->registrationName;
    }

    public function getIdentifier(): ?LegalRegistrationIdentifier
    {
        return $this->identifier;
    }

    public function setIdentifier(?LegalRegistrationIdentifier $identifier): static
    {
        $this->identifier = $identifier;

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

        $currentNode->appendChild($document->createElement('cbc:RegistrationName', $this->registrationName));

        if (\is_string($this->companyLegalForm)) {
            $currentNode->appendChild($document->createElement('cbc:CompanyLegalForm', $this->companyLegalForm));
        }

        if ($this->identifier instanceof LegalRegistrationIdentifier) {
            $identifierElement = $document->createElement('cbc:CompanyID', $this->identifier->value);

            if ($this->identifier->scheme instanceof InternationalCodeDesignator) {
                $identifierElement->setAttribute('schemeID', $this->identifier->scheme->value);
            }

            $currentNode->appendChild($identifierElement);
        }

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): ?self
    {
        $sellerPartyLegalEntityElements = $xpath->query(sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $sellerPartyLegalEntityElements->count()) {
            return null;
        }

        if ($sellerPartyLegalEntityElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        $registrationNameElements = $xpath->query('./cbc:RegistrationName', $sellerPartyLegalEntityElements);

        if (1 !== $registrationNameElements->count()) {
            throw new \Exception('Malformed');
        }

        $registrationName = $registrationNameElements->item(0)->nodeValue;

        /** @var \DOMElement $sellerPartyLegalEntityItem */
        $sellerPartyLegalEntityItem = $sellerPartyLegalEntityElements->item(0);

        $identifierElements = $xpath->query('./cbc:CompanyID', $sellerPartyLegalEntityItem);

        if ($identifierElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        $sellerPartyLegalEntity = new self($registrationName);

        if (1 === $identifierElements->count()) {
            /** @var \DOMElement $identifierItem */
            $identifierItem = $identifierElements->item(0);
            $identifier     = (string) $identifierItem->nodeValue;

            $scheme = null;

            if ($identifierItem->hasAttribute('schemeID')) {
                $scheme = InternationalCodeDesignator::tryFrom($identifierItem->getAttribute('schemeID'));

                if (!$scheme instanceof InternationalCodeDesignator) {
                    throw new \Exception('Wrong schemeID');
                }
            }

            $sellerPartyLegalEntity->setIdentifier(new LegalRegistrationIdentifier($identifier, $scheme));
        }

        $companyLegalFormElements = $xpath->query('./cbc:CompanyLegalForm', $sellerPartyLegalEntityItem);

        if ($companyLegalFormElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        if (1 === $companyLegalFormElements->count()) {
            $sellerPartyLegalEntity->setCompanyLegalForm((string) $companyLegalFormElements->item(0)->nodeValue);
        }

        return $sellerPartyLegalEntity;
    }
}
