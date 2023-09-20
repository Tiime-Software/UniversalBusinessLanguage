<?php

namespace Tiime\UniversalBusinessLanguage\DataType\Aggregate;

use Tiime\UniversalBusinessLanguage\DataType\Basic\EndpointIdentifier;

class SellerParty
{
    protected const XML_NODE = 'cac:Party';

    /**
     * BT-34. (warning: pas dans les specs 2.3  mais obligatoire dans la norme UBL).
     */
    private EndpointIdentifier $endpointIdentifier;

    /**
     * BT-29a-00.
     *
     * @var array<int, SellerPartyIdentification>
     */
    private array $sellerPartyIdentifications;

    public function __construct(EndpointIdentifier $endpointID)
    {
        $this->endpointIdentifier         = $endpointID;
        $this->sellerPartyIdentifications = [];
    }

    public function getEndpointIdentifier(): EndpointIdentifier
    {
        return $this->endpointIdentifier;
    }

    /**
     * @return array|SellerPartyIdentification[]
     */
    public function getSellerPartyIdentifications(): array
    {
        return $this->sellerPartyIdentifications;
    }

    /**
     * @param array<int, SellerPartyIdentification> $sellerPartyIdentifications
     *
     * @return $this
     */
    public function setSellerPartyIdentifications(array $sellerPartyIdentifications): static
    {
        $tmpSellerPartyIdentification = [];

        foreach ($sellerPartyIdentifications as $sellerPartyIdentification) {
            if (!$sellerPartyIdentification instanceof SellerPartyIdentification) {
                throw new \TypeError();
            }

            $tmpSellerPartyIdentification[] = $sellerPartyIdentification;
        }

        $this->sellerPartyIdentifications = $tmpSellerPartyIdentification;

        return $this;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);

        $currentNode->appendChild($this->endpointIdentifier->toXML($document));

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): self
    {
        $partyElements = $xpath->query(sprintf('./%s', self::XML_NODE), $currentElement);

        if (!$partyElements || 1 !== $partyElements->count()) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $partyElement */
        $partyElement = $partyElements->item(0);

        $endpointId = EndpointIdentifier::fromXML($xpath, $partyElement);

        $sellerPartyIdentifications = SellerPartyIdentification::fromXML($xpath, $partyElement);

        $party = new self($endpointId);

        $party->setSellerPartyIdentifications($sellerPartyIdentifications);

        return $party;
    }
}
