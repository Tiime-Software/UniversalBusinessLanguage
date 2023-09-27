<?php

namespace Tiime\UniversalBusinessLanguage\DataType\Aggregate;

use Tiime\UniversalBusinessLanguage\DataType\Basic\EndpointIdentifier;

class BuyerParty
{
    protected const XML_NODE = 'cac:Party';

    /**
     * BT-49.
     */
    private EndpointIdentifier $endpointIdentifier;

    /**
     * BT-46.
     */
    private ?BuyerPartyIdentification $buyerPartyIdentification;

    private ?BuyerPartyLegalEntity $partyLegalEntity;

    /**
     * BT-48-00.
     */
    private ?BuyerPartyTaxScheme $partyTaxScheme;

    /**
     * BT-45-00.
     */
    private ?PartyName $partyName;

    /**
     * BG-8-00.
     */
    private PostalAddress $postalAddress;

    /**
     * BG-9-00.
     */
    private ?Contact $contact;

    public function __construct(EndpointIdentifier $endpointID, PostalAddress $postalAddress)
    {
        $this->endpointIdentifier       = $endpointID;
        $this->postalAddress            = $postalAddress;
        $this->buyerPartyIdentification = null;
        $this->partyLegalEntity         = null;
        $this->partyTaxScheme           = null;
        $this->partyName                = null;
        $this->contact                  = null;
    }

    public function getEndpointIdentifier(): EndpointIdentifier
    {
        return $this->endpointIdentifier;
    }

    public function getBuyerPartyIdentification(): ?BuyerPartyIdentification
    {
        return $this->buyerPartyIdentification;
    }

    public function setBuyerPartyIdentification(?BuyerPartyIdentification $buyerPartyIdentification): static
    {
        $this->buyerPartyIdentification = $buyerPartyIdentification;

        return $this;
    }

    public function getPartyLegalEntity(): ?BuyerPartyLegalEntity
    {
        return $this->partyLegalEntity;
    }

    public function setPartyLegalEntity(?BuyerPartyLegalEntity $partyLegalEntity): static
    {
        $this->partyLegalEntity = $partyLegalEntity;

        return $this;
    }

    public function getPartyTaxScheme(): ?BuyerPartyTaxScheme
    {
        return $this->partyTaxScheme;
    }

    public function setPartyTaxScheme(?BuyerPartyTaxScheme $partyTaxScheme): static
    {
        $this->partyTaxScheme = $partyTaxScheme;

        return $this;
    }

    public function getpartyName(): ?PartyName
    {
        return $this->partyName;
    }

    public function setpartyName(?PartyName $partyName): static
    {
        $this->partyName = $partyName;

        return $this;
    }

    public function getPostalAddress(): PostalAddress
    {
        return $this->postalAddress;
    }

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function setContact(?Contact $contact): static
    {
        $this->contact = $contact;

        return $this;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);

        $currentNode->appendChild($this->endpointIdentifier->toXML($document));

        if ($this->buyerPartyIdentification instanceof BuyerPartyIdentification) {
            $currentNode->appendChild($this->buyerPartyIdentification->toXML($document));
        }

        $currentNode->appendChild($this->partyLegalEntity->toXML($document));

        if ($this->partyTaxScheme instanceof BuyerPartyTaxScheme) {
            $currentNode->appendChild($this->partyTaxScheme->toXML($document));
        }

        if ($this->partyName instanceof PartyName) {
            $currentNode->appendChild($this->partyName->toXML($document));
        }

        $currentNode->appendChild($this->postalAddress->toXML($document));

        if ($this->contact instanceof Contact) {
            $currentNode->appendChild($this->contact->toXML($document));
        }

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): self
    {
        $partyElements = $xpath->query(sprintf('./%s', self::XML_NODE), $currentElement);

        if (1 !== $partyElements->count()) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $partyElement */
        $partyElement = $partyElements->item(0);

        $endpointId               = EndpointIdentifier::fromXML($xpath, $partyElement);
        $buyerPartyIdentification = BuyerPartyIdentification::fromXML($xpath, $partyElement);
        $partyLegalEntity         = BuyerPartyLegalEntity::fromXML($xpath, $partyElement);
        $partyTaxScheme           = BuyerPartyTaxScheme::fromXML($xpath, $partyElement);
        $partyName                = PartyName::fromXML($xpath, $partyElement);
        $postalAddress            = PostalAddress::fromXML($xpath, $partyElement);
        $contact                  = Contact::fromXML($xpath, $partyElement);

        $party = new self($endpointId, $postalAddress);

        $party->setBuyerPartyIdentification($buyerPartyIdentification);

        if ($partyLegalEntity instanceof BuyerPartyLegalEntity) {
            $party->setPartyLegalEntity($partyLegalEntity);
        }

        if ($partyTaxScheme instanceof BuyerPartyTaxScheme) {
            $party->setPartyTaxScheme($partyTaxScheme);
        }

        if ($partyName instanceof PartyName) {
            $party->setpartyName($partyName);
        }

        if ($contact instanceof Contact) {
            $party->setContact($contact);
        }

        return $party;
    }
}
