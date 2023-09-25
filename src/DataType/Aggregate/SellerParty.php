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

    /**
     * BT-30-00.
     */
    private ?SellerPartyLegalEntity $partyLegalEntity;

    /**
     * BT-31-00.
     */
    private ?SellerPartyTaxScheme $partyTaxScheme;

    public function __construct(EndpointIdentifier $endpointID)
    {
        $this->endpointIdentifier         = $endpointID;
        $this->sellerPartyIdentifications = [];
        $this->partyLegalEntity           = null;
        $this->partyTaxScheme             = null;
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

    public function getPartyLegalEntity(): ?SellerPartyLegalEntity
    {
        return $this->partyLegalEntity;
    }

    public function setPartyLegalEntity(?SellerPartyLegalEntity $partyLegalEntity): static
    {
        $this->partyLegalEntity = $partyLegalEntity;

        return $this;
    }

    public function getPartyTaxScheme(): ?SellerPartyTaxScheme
    {
        return $this->partyTaxScheme;
    }

    public function setPartyTaxScheme(?SellerPartyTaxScheme $partyTaxScheme): static
    {
        $this->partyTaxScheme = $partyTaxScheme;

        return $this;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);

        $currentNode->appendChild($this->endpointIdentifier->toXML($document));

        if ($this->partyLegalEntity instanceof SellerPartyLegalEntity) {
            $currentNode->appendChild($this->partyLegalEntity->toXML($document));
        }

        if ($this->partyTaxScheme instanceof SellerPartyTaxScheme) {
            $currentNode->appendChild($this->partyTaxScheme->toXML($document));
        }

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): self
    {
        $partyElements = $xpath->query(sprintf('./%s', self::XML_NODE), $currentElement);

        if (1 !== $partyElements->count()) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $partyItem */
        $partyItem                  = $partyElements->item(0);
        $endpointId                 = EndpointIdentifier::fromXML($xpath, $partyItem);
        $sellerPartyIdentifications = SellerPartyIdentification::fromXML($xpath, $partyItem);
        $partyLegalEntity           = SellerPartyLegalEntity::fromXML($xpath, $partyItem);
        $partyTaxScheme             = SellerPartyTaxScheme::fromXML($xpath, $partyItem);

        $party = new self($endpointId);

        $party->setSellerPartyIdentifications($sellerPartyIdentifications);

        if ($partyLegalEntity instanceof SellerPartyLegalEntity) {
            $party->setPartyLegalEntity($partyLegalEntity);
        }

        if ($partyTaxScheme instanceof SellerPartyTaxScheme) {
            $party->setPartyTaxScheme($partyTaxScheme);
        }

        return $party;
    }
}
