<?php

namespace Tiime\UniversalBusinessLanguage\DataType\Aggregate;

use Tiime\EN16931\DataType\Identifier\VatIdentifier;

/**
 * BT-30. et BT-30-1.
 */
class PartyTaxScheme
{
    protected const XML_NODE = 'cac:PartyTaxScheme';

    private VatIdentifier $companyIdentifier;

    private TaxScheme $taxScheme;

    public function __construct(VatIdentifier $companyIdentifier, TaxScheme $taxScheme)
    {
        $this->companyIdentifier = $companyIdentifier;
        $this->taxScheme         = $taxScheme;
    }

    public function getCompanyIdentifier(): VatIdentifier
    {
        return $this->companyIdentifier;
    }

    public function getTaxScheme(): TaxScheme
    {
        return $this->taxScheme;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);
        $currentNode->appendChild($document->createElement('cbc:CompanyID', $this->companyIdentifier->value));
        $currentNode->appendChild($this->taxScheme->toXML($document));

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): ?self
    {
        $partyTaxSchemeElements = $xpath->query(sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $partyTaxSchemeElements->count()) {
            return null;
        }

        if ($partyTaxSchemeElements->count() > 2) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $partyTaxSchemeItem */
        $partyTaxSchemeItem = $partyTaxSchemeElements->item(0);

        $companyIdentifierElements = $xpath->query('./cbc:CompanyID', $partyTaxSchemeItem);

        if (1 !== $companyIdentifierElements->count()) {
            throw new \Exception('Malformed');
        }

        $companyIdentifier = (string) $companyIdentifierElements->item(0)->nodeValue;
        $taxScheme         = TaxScheme::fromXML($xpath, $partyTaxSchemeItem);

        return new self(new VatIdentifier($companyIdentifier), $taxScheme);
    }
}
