<?php

namespace Tiime\UniversalBusinessLanguage\Ubl21Standard\Invoice\DataType\Aggregate;

class PayeePartyBankAssignedCreditorIdentification
{
    protected const XML_NODE = 'cac:PartyIdentification';

    /**
     * BT-90.
     */
    private string $bankAssignedCreditorIdentifier;

    public function __construct(string $bankAssignedCreditorIdentifier)
    {
        $this->bankAssignedCreditorIdentifier = $bankAssignedCreditorIdentifier;
    }

    public function getBankAssignedCreditorIdentifier(): string
    {
        return $this->bankAssignedCreditorIdentifier;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);

        $bankAssignedCreditorIdentifier = $document->createElement('cbc:ID', $this->bankAssignedCreditorIdentifier);
        $bankAssignedCreditorIdentifier->setAttribute('schemeID', 'SEPA');

        $currentNode->appendChild($bankAssignedCreditorIdentifier);

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): ?self
    {
        $partyIdentificationElements = $xpath->query(\sprintf('./%s[cbc:ID[@schemeID=\'SEPA\']]', self::XML_NODE), $currentElement);

        if (0 === $partyIdentificationElements->count()) {
            return null;
        }

        if ($partyIdentificationElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $partyIdentificationElement */
        $partyIdentificationElement = $partyIdentificationElements->item(0);

        $identifierElements = $xpath->query('./cbc:ID', $partyIdentificationElement);

        if (1 !== $identifierElements->count()) {
            throw new \Exception('Malformed');
        }

        return new self((string) $identifierElements->item(0)->nodeValue);
    }
}
