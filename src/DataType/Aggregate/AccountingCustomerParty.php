<?php

namespace Tiime\UniversalBusinessLanguage\DataType\Aggregate;

class AccountingCustomerParty
{
    protected const XML_NODE = 'cac:AccountingCustomerParty';

    private BuyerParty $party;

    public function __construct(BuyerParty $party)
    {
        $this->party = $party;
    }

    public function getParty(): BuyerParty
    {
        return $this->party;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);

        $currentNode->appendChild($this->party->toXML($document));

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): self
    {
        $accountingCustomerPartyElements = $xpath->query(sprintf('./%s', self::XML_NODE), $currentElement);

        if (1 !== $accountingCustomerPartyElements->count()) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $accountingCustomerPartyElement */
        $accountingCustomerPartyElement = $accountingCustomerPartyElements->item(0);

        $party = BuyerParty::fromXML($xpath, $accountingCustomerPartyElement);

        return new self($party);
    }
}
