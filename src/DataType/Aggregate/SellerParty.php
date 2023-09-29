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

    private ?SellerPartyLegalEntity $partyLegalEntity;

    /**
     * BT-31-00.
     *
     * @var array<int, SellerPartyTaxScheme>
     */
    private array $partyTaxSchemes;

    /**
     * BT-28-00.
     */
    private ?PartyName $partyName;

    /**
     * BG-5-00.
     */
    private PostalAddress $postalAddress;

    /**
     * BG-6-00.
     */
    private ?Contact $contact;

    public function __construct(EndpointIdentifier $endpointID, PostalAddress $postalAddress)
    {
        $this->endpointIdentifier         = $endpointID;
        $this->postalAddress              = $postalAddress;
        $this->sellerPartyIdentifications = [];
        $this->partyLegalEntity           = null;
        $this->partyTaxSchemes            = [];
        $this->partyName                  = null;
        $this->contact                    = null;
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
        foreach ($sellerPartyIdentifications as $sellerPartyIdentification) {
            if (!$sellerPartyIdentification instanceof SellerPartyIdentification) {
                throw new \TypeError();
            }
        }

        $this->sellerPartyIdentifications = $sellerPartyIdentifications;

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

    /**
     * @return array|SellerPartyTaxScheme[]
     */
    public function getPartyTaxSchemes(): array
    {
        return $this->partyTaxSchemes;
    }

    /**
     * @param array<int, SellerPartyTaxScheme> $partyTaxSchemes
     *
     * @return $this
     */
    public function setPartyTaxSchemes(array $partyTaxSchemes): static
    {
        foreach ($partyTaxSchemes as $partyTaxScheme) {
            if (!$partyTaxScheme instanceof SellerPartyTaxScheme) {
                throw new \TypeError();
            }
        }

        $this->partyTaxSchemes = $partyTaxSchemes;

        return $this;
    }

    public function getPartyName(): ?PartyName
    {
        return $this->partyName;
    }

    public function setPartyName(?PartyName $partyName): static
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

        foreach ($this->sellerPartyIdentifications as $sellerPartyIdentification) {
            $currentNode->appendChild($sellerPartyIdentification->toXML($document));
        }

        if ($this->partyLegalEntity instanceof SellerPartyLegalEntity) {
            $currentNode->appendChild($this->partyLegalEntity->toXML($document));
        }

        foreach ($this->partyTaxSchemes as $partyTaxScheme) {
            $currentNode->appendChild($partyTaxScheme->toXML($document));
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

        $endpointId                 = EndpointIdentifier::fromXML($xpath, $partyElement);
        $sellerPartyIdentifications = SellerPartyIdentification::fromXML($xpath, $partyElement);
        $partyLegalEntity           = SellerPartyLegalEntity::fromXML($xpath, $partyElement);
        $partyTaxSchemes            = SellerPartyTaxScheme::fromXML($xpath, $partyElement);
        $partyName                  = PartyName::fromXML($xpath, $partyElement);
        $postalAddress              = PostalAddress::fromXML($xpath, $partyElement);
        $contact                    = Contact::fromXML($xpath, $partyElement);

        $party = new self($endpointId, $postalAddress);

        if (\count($sellerPartyIdentifications) > 0) {
            $party->setSellerPartyIdentifications($sellerPartyIdentifications);
        }

        if ($partyLegalEntity instanceof SellerPartyLegalEntity) {
            $party->setPartyLegalEntity($partyLegalEntity);
        }

        if (\count($partyTaxSchemes) > 0) {
            $party->setPartyTaxSchemes($partyTaxSchemes);
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
