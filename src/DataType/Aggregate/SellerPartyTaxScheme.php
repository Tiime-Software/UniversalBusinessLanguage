<?php

namespace Tiime\UniversalBusinessLanguage\DataType\Aggregate;

use Tiime\EN16931\DataType\Identifier\VatIdentifier;

class SellerPartyTaxScheme
{
    protected const XML_NODE = 'cac:PartyTaxScheme';

    /**
     * BT-31.
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

    /**
     * @return array<int, SellerPartyTaxScheme>
     */
    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): array
    {
        $partyTaxSchemeElements = $xpath->query(sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $partyTaxSchemeElements->count()) {
            return [];
        }

        if ($partyTaxSchemeElements->count() > 2) {
            throw new \Exception('Malformed');
        }

        $partyTaxSchemes = [];

        /** @var \DOMElement $partyTaxSchemeElement */
        foreach ($partyTaxSchemeElements as $partyTaxSchemeElement) {
            $identifierElements = $xpath->query('./cbc:CompanyID', $partyTaxSchemeElement);

            if (1 !== $identifierElements->count()) {
                throw new \Exception('Malformed');
            }

            $identifier = new VatIdentifier((string) $identifierElements->item(0)->nodeValue);

            $taxScheme = TaxScheme::fromXML($xpath, $partyTaxSchemeElement);

            $partyTaxSchemes[] = new self($identifier, $taxScheme);
        }

        return $partyTaxSchemes;
    }
}
