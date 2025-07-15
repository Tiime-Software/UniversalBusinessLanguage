<?php

namespace Tiime\UniversalBusinessLanguage\Ubl21Standard\Invoice\DataType\Aggregate;

/**
 * BG-4.
 */
class AccountingSupplierParty
{
    protected const XML_NODE = 'cac:AccountingSupplierParty';

    private SellerParty $party;

    public function __construct(SellerParty $party)
    {
        $this->party = $party;
    }

    public function getParty(): SellerParty
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
        $accountingSupplierPartyElements = $xpath->query(\sprintf('./%s', self::XML_NODE), $currentElement);

        if (1 !== $accountingSupplierPartyElements->count()) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $accountingSupplierPartyElement */
        $accountingSupplierPartyElement = $accountingSupplierPartyElements->item(0);

        $party = SellerParty::fromXML($xpath, $accountingSupplierPartyElement);

        return new self($party);
    }
}
