<?php

namespace Tiime\UniversalBusinessLanguage\Ubl21Standard\Invoice\DataType\Aggregate;

/**
 * BG-15.
 */
class DeliveryAddress
{
    protected const XML_NODE = 'cac:Address';

    /**
     * BT-80-00.
     */
    private Country $country;

    /**
     * BT-75.
     */
    private ?string $streetName;

    /**
     * BT-76.
     */
    private ?string $additionalStreetName;

    /**
     * BT-77.
     */
    private ?string $cityName;

    /**
     * BT-78.
     */
    private ?string $postalZone;

    /**
     * BT-79.
     */
    private ?string $countrySubentity;

    /**
     * BT-165-00.
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

        $currentNode->appendChild($this->country->toXML($document));

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): ?self
    {
        $deliveryAddressElements = $xpath->query(\sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $deliveryAddressElements->count()) {
            return null;
        }

        if ($deliveryAddressElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $deliveryAddressElement */
        $deliveryAddressElement = $deliveryAddressElements->item(0);

        $country = Country::fromXML($xpath, $deliveryAddressElement);

        $deliveryAddress = new self($country);

        $streetNameElements = $xpath->query('./cbc:StreetName', $deliveryAddressElement);

        if ($streetNameElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        if (1 === $streetNameElements->count()) {
            $deliveryAddress->setStreetName((string) $streetNameElements->item(0)->nodeValue);
        }

        $additionalStreetNameElements = $xpath->query('./cbc:AdditionalStreetName', $deliveryAddressElement);

        if ($additionalStreetNameElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        if (1 === $additionalStreetNameElements->count()) {
            $deliveryAddress->setAdditionalStreetName((string) $additionalStreetNameElements->item(0)->nodeValue);
        }

        $cityNameElements = $xpath->query('./cbc:CityName', $deliveryAddressElement);

        if ($cityNameElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        if (1 === $cityNameElements->count()) {
            $deliveryAddress->setCityName((string) $cityNameElements->item(0)->nodeValue);
        }

        $postalZoneElements = $xpath->query('./cbc:PostalZone', $deliveryAddressElement);

        if ($postalZoneElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        if (1 === $postalZoneElements->count()) {
            $deliveryAddress->setPostalZone((string) $postalZoneElements->item(0)->nodeValue);
        }

        $countrySubentityElements = $xpath->query('./cbc:CountrySubentity', $deliveryAddressElement);

        if ($countrySubentityElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        if (1 === $countrySubentityElements->count()) {
            $deliveryAddress->setCountrySubentity((string) $countrySubentityElements->item(0)->nodeValue);
        }

        $addressLine = AddressLine::fromXML($xpath, $deliveryAddressElement);

        if ($addressLine instanceof AddressLine) {
            $deliveryAddress->setAddressLine($addressLine);
        }

        return $deliveryAddress;
    }
}
