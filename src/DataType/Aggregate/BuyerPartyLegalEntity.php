<?php

namespace Tiime\UniversalBusinessLanguage\DataType\Aggregate;

use Tiime\EN16931\DataType\Identifier\LegalRegistrationIdentifier;
use Tiime\EN16931\DataType\InternationalCodeDesignator;

class BuyerPartyLegalEntity
{
    protected const XML_NODE = 'cac:PartyLegalEntity';

    /**
     * BT-44.
     */
    private string $registrationName;

    /**
     * BT-47.
     */
    private ?LegalRegistrationIdentifier $identifier;

    public function __construct(string $registrationName)
    {
        $this->registrationName = $registrationName;
        $this->identifier       = null;
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

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);

        $currentNode->appendChild($document->createElement('cbc:RegistrationName', $this->registrationName));

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
        $buyerPartyLegalEntityElements = $xpath->query(sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $buyerPartyLegalEntityElements->count()) {
            return null;
        }

        if ($buyerPartyLegalEntityElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        $registrationNameElements = $xpath->query('./cbc:RegistrationName', $buyerPartyLegalEntityElements);

        if (1 !== $registrationNameElements->count()) {
            throw new \Exception('Malformed');
        }

        $registrationName = $registrationNameElements->item(0)->nodeValue;

        /** @var \DOMElement $buyerPartyLegalEntityElement */
        $buyerPartyLegalEntityElement = $buyerPartyLegalEntityElements->item(0);

        $identifierElements = $xpath->query('./cbc:CompanyID', $buyerPartyLegalEntityElement);

        if ($identifierElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        $buyerPartyLegalEntity = new self($registrationName);

        if (1 === $identifierElements->count()) {
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

            $buyerPartyLegalEntity->setIdentifier(new LegalRegistrationIdentifier($identifier, $scheme));
        }

        return $buyerPartyLegalEntity;
    }
}
