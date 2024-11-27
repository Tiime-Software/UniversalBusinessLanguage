<?php

namespace Tiime\UniversalBusinessLanguage\Ubl21\Invoice\DataType\Aggregate;

use Tiime\UniversalBusinessLanguage\Ubl21\CreditNote\DataType\Aggregate\SellerPartyBankAccountIdentification;
use Tiime\UniversalBusinessLanguage\Ubl21\Invoice\DataType\Basic\EndpointIdentifier;

class SellerParty
{
    protected const XML_NODE = 'cac:Party';

    /**
     * BT-34.
     */
    private ?EndpointIdentifier $endpointIdentifier;

    /**
     * BT-29a-00.
     *
     * @var array<int, SellerPartyIdentification>
     */
    private array $partyIdentifications;

    /**
     * BT-90-00.
     *
     * @var array<int, SellerPartyBankAccountIdentification>
     */
    private array $partyBankAccountIdentifications;

    private SellerPartyLegalEntity $partyLegalEntity;

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

    public function __construct(PostalAddress $postalAddress, SellerPartyLegalEntity $partyLegalEntity)
    {
        $this->endpointIdentifier              = null;
        $this->postalAddress                   = $postalAddress;
        $this->partyIdentifications            = [];
        $this->partyBankAccountIdentifications = [];
        $this->partyLegalEntity                = $partyLegalEntity;
        $this->partyTaxSchemes                 = [];
        $this->partyName                       = null;
        $this->contact                         = null;
    }

    public function getEndpointIdentifier(): ?EndpointIdentifier
    {
        return $this->endpointIdentifier;
    }

    public function setEndpointIdentifier(?EndpointIdentifier $endpointIdentifier): static
    {
        $this->endpointIdentifier = $endpointIdentifier;

        return $this;
    }

    /**
     * @return array|SellerPartyIdentification[]
     */
    public function getPartyIdentifications(): array
    {
        return $this->partyIdentifications;
    }

    /**
     * @param array<int, SellerPartyIdentification> $partyIdentifications
     *
     * @return $this
     */
    public function setPartyIdentifications(array $partyIdentifications): static
    {
        foreach ($partyIdentifications as $sellerPartyIdentification) {
            if (!$sellerPartyIdentification instanceof SellerPartyIdentification) {
                throw new \TypeError();
            }
        }

        $this->partyIdentifications = $partyIdentifications;

        return $this;
    }

    /**
     * @return array|SellerPartyBankAccountIdentification[]
     */
    public function getPartyBankAccountIdentifications(): array
    {
        return $this->partyBankAccountIdentifications;
    }

    /**
     * @param array<int, SellerPartyBankAccountIdentification> $partyBankAccountIdentifications
     *
     * @return $this
     */
    public function setPartyBankAccountIdentifications(array $partyBankAccountIdentifications): static
    {
        foreach ($partyBankAccountIdentifications as $partyBankAccountIdentification) {
            if (!$partyBankAccountIdentification instanceof SellerPartyBankAccountIdentification) {
                throw new \TypeError();
            }
        }

        $this->partyBankAccountIdentifications = $partyBankAccountIdentifications;

        return $this;
    }

    public function getPartyLegalEntity(): ?SellerPartyLegalEntity
    {
        return $this->partyLegalEntity;
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

        if ($this->endpointIdentifier instanceof EndpointIdentifier) {
            $currentNode->appendChild($this->endpointIdentifier->toXML($document));
        }

        foreach ($this->partyIdentifications as $sellerPartyIdentification) {
            $currentNode->appendChild($sellerPartyIdentification->toXML($document));
        }

        foreach ($this->partyBankAccountIdentifications as $partyBankAccountIdentification) {
            $currentNode->appendChild($partyBankAccountIdentification->toXML($document));
        }

        if ($this->partyName instanceof PartyName) {
            $currentNode->appendChild($this->partyName->toXML($document));
        }

        $currentNode->appendChild($this->postalAddress->toXML($document));

        foreach ($this->partyTaxSchemes as $partyTaxScheme) {
            $currentNode->appendChild($partyTaxScheme->toXML($document));
        }

        $currentNode->appendChild($this->partyLegalEntity->toXML($document));

        if ($this->contact instanceof Contact) {
            $currentNode->appendChild($this->contact->toXML($document));
        }

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): self
    {
        $partyElements = $xpath->query(\sprintf('./%s', self::XML_NODE), $currentElement);

        if (1 !== $partyElements->count()) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $partyElement */
        $partyElement = $partyElements->item(0);

        $endpointId                      = EndpointIdentifier::fromXML($xpath, $partyElement);
        $partyIdentifications            = SellerPartyIdentification::fromXML($xpath, $partyElement);
        $partyBankAccountIdentifications = SellerPartyBankAccountIdentification::fromXML($xpath, $partyElement);
        $partyLegalEntity                = SellerPartyLegalEntity::fromXML($xpath, $partyElement);
        $partyTaxSchemes                 = SellerPartyTaxScheme::fromXML($xpath, $partyElement);
        $partyName                       = PartyName::fromXML($xpath, $partyElement);
        $postalAddress                   = PostalAddress::fromXML($xpath, $partyElement);
        $contact                         = Contact::fromXML($xpath, $partyElement);

        $party = new self($postalAddress, $partyLegalEntity);

        if ($endpointId instanceof EndpointIdentifier) {
            $party->setEndpointIdentifier($endpointId);
        }

        if (\count($partyIdentifications) > 0) {
            $party->setPartyIdentifications($partyIdentifications);
        }

        if (\count($partyBankAccountIdentifications) > 0) {
            $party->setPartyBankAccountIdentifications($partyBankAccountIdentifications);
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
