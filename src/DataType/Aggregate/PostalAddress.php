<?php

namespace Tiime\UniversalBusinessLanguage\DataType\Aggregate;

/**
 * BG-5.
 */
class PostalAddress
{
    protected const XML_NODE = 'cac:PostalAddress';

    /**
     * BT-40-00.
     */
    private Country $country;

    /**
     * BT-35.
     */
    private ?string $streetName;

    /**
     * BT-36.
     */
    private ?string $additionalStreetName;

    /**
     * BT-37.
     */
    private ?string $cityName;

    /**
     * BT-38.
     */
    private ?string $postalZone;

    /**
     * BT-39.
     */
    private ?string $countrySubentity;

    /**
     * BT-163-00.
     */
    private ?AddressLine $addressLine;

    public function __construct(Country $country)
    {
        $this->country              = $country;
        $this->streetName           = null;
        $this->additionalStreetName = null;
        $this->cityName             = null;
        $this->postalZone           = null;
        $this->countrySubentity     = null;
        $this->addressLine          = null;
    }

    public function getCountry(): Country
    {
        return $this->country;
    }

    public function getStreetName(): ?string
    {
        return $this->streetName;
    }

    public function setStreetName(?string $streetName): static
    {
        $this->streetName = $streetName;

        return $this;
    }

    public function getAdditionalStreetName(): ?string
    {
        return $this->additionalStreetName;
    }

    public function setAdditionalStreetName(?string $additionalStreetName): static
    {
        $this->additionalStreetName = $additionalStreetName;

        return $this;
    }

    public function getCityName(): ?string
    {
        return $this->cityName;
    }

    public function setCityName(?string $cityName): static
    {
        $this->cityName = $cityName;

        return $this;
    }

    public function getPostalZone(): ?string
    {
        return $this->postalZone;
    }

    public function setPostalZone(?string $postalZone): static
    {
        $this->postalZone = $postalZone;

        return $this;
    }

    public function getCountrySubentity(): ?string
    {
        return $this->countrySubentity;
    }

    public function setCountrySubentity(?string $countrySubentity): static
    {
        $this->countrySubentity = $countrySubentity;

        return $this;
    }

    public function getAddressLine(): ?AddressLine
    {
        return $this->addressLine;
    }

    public function setAddressLine(?AddressLine $addressLine): static
    {
        $this->addressLine = $addressLine;

        return $this;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);

        $currentNode->appendChild($this->country->toXML($document));

        if (\is_string($this->streetName)) {
            $currentNode->appendChild($document->createElement('cbc:StreetName', $this->streetName));
        }

        if (\is_string($this->additionalStreetName)) {
            $currentNode->appendChild($document->createElement('cbc:AdditionalStreetName', $this->additionalStreetName));
        }

        if (\is_string($this->cityName)) {
            $currentNode->appendChild($document->createElement('cbc:CityName', $this->cityName));
        }

        if (\is_string($this->postalZone)) {
            $currentNode->appendChild($document->createElement('cbc:PostalZone', $this->postalZone));
        }

        if (\is_string($this->countrySubentity)) {
            $currentNode->appendChild($document->createElement('cbc:CountrySubentity', $this->countrySubentity));
        }

        if ($this->addressLine instanceof AddressLine) {
            $currentNode->appendChild($this->addressLine->toXML($document));
        }

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): ?self
    {
        $postalAddressElements = $xpath->query(sprintf('./%s', self::XML_NODE), $currentElement);

        if (1 !== $postalAddressElements->count()) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $postalAddressItem */
        $postalAddressItem = $postalAddressElements->item(0);

        $country = Country::fromXML($xpath, $postalAddressItem);

        $postalAddress = new self($country);

        $streetNameElements = $xpath->query('./cbc:StreetName', $postalAddressItem);

        if ($streetNameElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        if (1 === $streetNameElements->count()) {
            $postalAddress->setStreetName((string) $streetNameElements->item(0)->nodeValue);
        }

        $additionalStreetNameElements = $xpath->query('./cbc:AdditionalStreetName', $postalAddressItem);

        if ($additionalStreetNameElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        if (1 === $additionalStreetNameElements->count()) {
            $postalAddress->setAdditionalStreetName((string) $additionalStreetNameElements->item(0)->nodeValue);
        }

        $cityNameElements = $xpath->query('./cbc:CityName', $postalAddressItem);

        if ($cityNameElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        if (1 === $cityNameElements->count()) {
            $postalAddress->setCityName((string) $cityNameElements->item(0)->nodeValue);
        }

        $postalZoneElements = $xpath->query('./cbc:PostalZone', $postalAddressItem);

        if ($postalZoneElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        if (1 === $postalZoneElements->count()) {
            $postalAddress->setPostalZone((string) $postalZoneElements->item(0)->nodeValue);
        }

        $addressLine = AddressLine::fromXML($xpath, $postalAddressItem);

        if ($addressLine instanceof AddressLine) {
            $postalAddress->setAddressLine($addressLine);
        }

        return $postalAddress;
    }
}
