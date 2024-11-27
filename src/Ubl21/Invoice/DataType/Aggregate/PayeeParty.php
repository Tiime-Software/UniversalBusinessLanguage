<?php

namespace Tiime\UniversalBusinessLanguage\Ubl21\Invoice\DataType\Aggregate;

/**
 * BG-10.
 */
class PayeeParty
{
    protected const XML_NODE = 'cac:PayeeParty';

    private ?PayeePartyIdentification $partyIdentification;

    private ?PayeePartyBankAssignedCreditorIdentification $partyBankAssignedCreditorIdentification;

    private PayeePartyName $partyName;

    private ?PayeePartyLegalEntity $partyLegalEntity;

    public function __construct(
        PayeePartyName $partyName,
        ?PayeePartyIdentification $partyIdentification = null,
        ?PayeePartyBankAssignedCreditorIdentification $partyBankAssignedCreditorIdentification = null)
    {
        if ($partyIdentification instanceof PayeePartyIdentification && $partyBankAssignedCreditorIdentification instanceof PayeePartyBankAssignedCreditorIdentification) {
            throw new \Exception('Malformed');
        }
        $this->partyIdentification                     = $partyIdentification;
        $this->partyBankAssignedCreditorIdentification = $partyBankAssignedCreditorIdentification;
        $this->partyName                               = $partyName;
        $this->partyLegalEntity                        = null;
    }

    public function getPartyIdentification(): ?PayeePartyIdentification
    {
        return $this->partyIdentification;
    }

    public function getPartyBankAssignedCreditorIdentification(): ?PayeePartyBankAssignedCreditorIdentification
    {
        return $this->partyBankAssignedCreditorIdentification;
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

        if ($this->partyBankAssignedCreditorIdentification instanceof PayeePartyBankAssignedCreditorIdentification) {
            $currentNode->appendChild($this->partyBankAssignedCreditorIdentification->toXML($document));
        }

        $currentNode->appendChild($this->partyName->toXML($document));

        if ($this->partyLegalEntity instanceof PayeePartyLegalEntity) {
            $currentNode->appendChild($this->partyLegalEntity->toXML($document));
        }

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

        $partyBankAssignedCreditorIdentification = PayeePartyBankAssignedCreditorIdentification::fromXML($xpath, $partyElement);
        $partyIdentification                     = PayeePartyIdentification::fromXML($xpath, $partyElement);
        $partyName                               = PayeePartyName::fromXML($xpath, $partyElement);
        $partyLegalEntity                        = PayeePartyLegalEntity::fromXML($xpath, $partyElement);

        $party = new self($partyName, $partyIdentification, $partyBankAssignedCreditorIdentification);

        if ($partyLegalEntity instanceof PayeePartyLegalEntity) {
            $party->setPartyLegalEntity($partyLegalEntity);
        }

        return $party;
    }
}
