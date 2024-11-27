<?php

namespace Tiime\UniversalBusinessLanguage\Ubl21\CreditNote\DataType\Aggregate;

/**
 * BT-90.
 */
class SellerPartyBankAssignedCreditorIdentification
{
    protected const XML_NODE = 'cac:PartyIdentification';

    private string $sellerIdentifier;

    public function __construct(string $sellerIdentifier)
    {
        $this->sellerIdentifier = $sellerIdentifier;
    }

    public function getSellerIdentifier(): string
    {
        return $this->sellerIdentifier;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);

        $sellerIdentifier = $document->createElement('cbc:ID', $this->sellerIdentifier);
        $sellerIdentifier->setAttribute('schemeID', 'SEPA');

        $currentNode->appendChild($sellerIdentifier);

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): array
    {
        $partyIdentificationElements = $xpath->query(\sprintf('./%s[cbc:ID[@schemeID=\'SEPA\']]', self::XML_NODE), $currentElement);

        if (0 === $partyIdentificationElements->count()) {
            return [];
        }

        $partyIdentifications = [];

        /** @var \DOMElement $partyIdentificationElement */
        foreach ($partyIdentificationElements as $partyIdentificationElement) {
            $identifierElements = $xpath->query('./cbc:ID', $partyIdentificationElement);

            if (1 !== $identifierElements->count()) {
                throw new \Exception('Malformed');
            }

            $partyIdentifications[] = new self((string) $identifierElements->item(0)->nodeValue);
        }

        return $partyIdentifications;
    }
}
