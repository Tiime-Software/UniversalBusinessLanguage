<?php

namespace Tiime\UniversalBusinessLanguage\DataType\Aggregate;

class PayeePartyBACIdentification
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

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): ?self
    {
        $partyIdentificationElements = $xpath->query(sprintf('./%s', self::XML_NODE), $currentElement);

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

        /** @var \DOMElement $identifierElement */
        $identifierElement = $identifierElements->item(0);

        if (!$identifierElement->hasAttribute('schemeID')) {
            return null;
        }
        $scheme = $identifierElement->getAttribute('schemeID');

        if ('SEPA' !== $scheme) {
            return null;
        }

        return new self((string) $identifierElement->nodeValue);
    }
}
