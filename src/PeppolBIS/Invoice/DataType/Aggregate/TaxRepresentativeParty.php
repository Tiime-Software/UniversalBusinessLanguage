<?php

namespace Tiime\UniversalBusinessLanguage\PeppolBIS\Invoice\DataType\Aggregate;

/**
 * BG-11.
 */
class TaxRepresentativeParty
{
    protected const XML_NODE = 'cac:TaxRepresentativeParty';

    private TaxRepresentativePartyName $partyName;

    /**
     * BG-12-00.
     */
    private PostalAddress $postalAddress;

    /**
     * BT-63-00.
     */
    private TaxRepresentativePartyTaxScheme $partyTaxScheme;

    public function __construct(
        TaxRepresentativePartyName $partyName,
        PostalAddress $postalAddress,
        TaxRepresentativePartyTaxScheme $partyTaxScheme,
    ) {
        $this->partyName      = $partyName;
        $this->partyTaxScheme = $partyTaxScheme;
        $this->postalAddress  = $postalAddress;
    }

    public function getPartyName(): TaxRepresentativePartyName
    {
        return $this->partyName;
    }

    public function getPostalAddress(): PostalAddress
    {
        return $this->postalAddress;
    }

    public function getPartyTaxScheme(): TaxRepresentativePartyTaxScheme
    {
        return $this->partyTaxScheme;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);
        $currentNode->appendChild($this->partyName->toXML($document));
        $currentNode->appendChild($this->postalAddress->toXML($document));
        $currentNode->appendChild($this->partyTaxScheme->toXML($document));

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): ?self
    {
        $partyElements = $xpath->query(\sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $partyElements->count()) {
            return null;
        }

        if ($partyElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $partyElement */
        $partyElement = $partyElements->item(0);

        $partyName      = TaxRepresentativePartyName::fromXML($xpath, $partyElement);
        $postalAddress  = PostalAddress::fromXML($xpath, $partyElement);
        $partyTaxScheme = TaxRepresentativePartyTaxScheme::fromXML($xpath, $partyElement);

        return new self($partyName, $postalAddress, $partyTaxScheme);
    }
}
