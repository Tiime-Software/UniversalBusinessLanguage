<?php

namespace Tiime\UniversalBusinessLanguage\Ubl21Standard\Invoice\DataType\Aggregate;

use Tiime\EN16931\DataType\Identifier\VatIdentifier;

class TaxRepresentativePartyTaxScheme
{
    protected const XML_NODE = 'cac:PartyTaxScheme';

    /**
     * BT-63.
     */
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
        $currentNode->appendChild($document->createElement('cbc:CompanyID', $this->companyIdentifier->getValue()));
        $currentNode->appendChild($this->taxScheme->toXML($document));

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): self
    {
        $partyTaxSchemeElements = $xpath->query(\sprintf('./%s', self::XML_NODE), $currentElement);

        if (1 !== $partyTaxSchemeElements->count()) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $partyTaxSchemeElement */
        $partyTaxSchemeElement = $partyTaxSchemeElements->item(0);

        $companyIdentifierElements = $xpath->query('./cbc:CompanyID', $partyTaxSchemeElement);

        if (1 !== $companyIdentifierElements->count()) {
            throw new \Exception('Malformed');
        }

        $companyIdentifier = (string) $companyIdentifierElements->item(0)->nodeValue;
        $taxScheme         = TaxScheme::fromXML($xpath, $partyTaxSchemeElement);

        return new self(new VatIdentifier($companyIdentifier), $taxScheme);
    }
}
