<?php

namespace Tiime\UniversalBusinessLanguage\DataType\Aggregate;

/**
 * BG-10.
 */
class PayeeParty
{
    protected const XML_NODE = 'cac:PayeeParty';

    private ?PayeePartyIdentification $partyIdentification;

    private PayeePartyName $partyName;

    private ?PayeePartyLegalEntity $partyLegalEntity;

    public function __construct(PayeePartyName $partyName)
    {
        $this->partyIdentification = null;
        $this->partyName           = $partyName;
        $this->partyLegalEntity    = null;
    }

    public function getPartyIdentification(): ?PayeePartyIdentification
    {
        return $this->partyIdentification;
    }

    public function setPartyIdentification(?PayeePartyIdentification $partyIdentification): static
    {
        $this->partyIdentification = $partyIdentification;

        return $this;
    }

    public function getPartyName(): PayeePartyName
    {
        return $this->partyName;
    }

    public function getPartyLegalEntity(): ?PayeePartyLegalEntity
    {
        return $this->partyLegalEntity;
    }

    public function setPartyLegalEntity(?PayeePartyLegalEntity $partyLegalEntity): static
    {
        $this->partyLegalEntity = $partyLegalEntity;

        return $this;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);

        if ($this->partyIdentification instanceof PayeePartyIdentification) {
            $currentNode->appendChild($this->partyIdentification->toXML($document));
        }

        $currentNode->appendChild($this->partyName->toXML($document));

        if ($this->partyLegalEntity instanceof PayeePartyLegalEntity) {
            $currentNode->appendChild($this->partyLegalEntity->toXML($document));
        }

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): ?self
    {
        $partyElements = $xpath->query(sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $partyElements->count()) {
            return null;
        }

        if ($partyElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $partyElement */
        $partyElement = $partyElements->item(0);

        $partyIdentification = PayeePartyIdentification::fromXML($xpath, $partyElement);
        $partyName           = PayeePartyName::fromXML($xpath, $partyElement);
        $partyLegalEntity    = PayeePartyLegalEntity::fromXML($xpath, $partyElement);

        $party = new self($partyName);

        if ($partyIdentification instanceof PayeePartyIdentification) {
            $party->setPartyIdentification($partyIdentification);
        }

        if ($partyLegalEntity instanceof PayeePartyLegalEntity) {
            $party->setPartyLegalEntity($partyLegalEntity);
        }

        return $party;
    }
}
