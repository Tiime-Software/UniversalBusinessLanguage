<?php

namespace Tiime\UniversalBusinessLanguage\Ubl21\Invoice\DataType\Aggregate;

use Tiime\EN16931\Codelist\InternationalCodeDesignator;
use Tiime\EN16931\DataType\Identifier\LocationIdentifier;

class DeliveryLocation
{
    protected const XML_NODE = 'cac:DeliveryLocation';

    /**
     * BT-71.
     */
    private ?LocationIdentifier $identifier;

    /**
     * BG-15.
     */
    private ?DeliveryAddress $address;

    public function __construct()
    {
        $this->identifier = null;
        $this->address    = null;
    }

    public function getIdentifier(): ?LocationIdentifier
    {
        return $this->identifier;
    }

    public function setIdentifier(?LocationIdentifier $identifier): static
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getAddress(): ?DeliveryAddress
    {
        return $this->address;
    }

    public function setAddress(?DeliveryAddress $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);

        if ($this->identifier instanceof LocationIdentifier) {
            $identifier = $document->createElement('cbc:ID', $this->identifier->value);

            if ($this->identifier->scheme instanceof InternationalCodeDesignator) {
                $identifier->setAttribute('schemeID', $this->identifier->scheme->value);
            }

            $currentNode->appendChild($identifier);
        }

        if ($this->address instanceof DeliveryAddress) {
            $currentNode->appendChild($this->address->toXML($document));
        }

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): ?self
    {
        $deliveryLocationElements = $xpath->query(\sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $deliveryLocationElements->count()) {
            return null;
        }

        if ($deliveryLocationElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $deliveryLocationElement */
        $deliveryLocationElement = $deliveryLocationElements->item(0);

        $identifierElements = $xpath->query('./cbc:ID', $deliveryLocationElement);

        if ($identifierElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        $deliveryLocation = new self();

        if (1 === $identifierElements->count()) {
            /** @var \DOMElement $identifierElement */
            $identifierElement = $identifierElements->item(0);
            $value             = (string) $identifierElement->nodeValue;

            $scheme = null;

            if ($identifierElement->hasAttribute('schemeID')) {
                $scheme = InternationalCodeDesignator::tryFrom($identifierElement->getAttribute('schemeID'));

                if (!$scheme instanceof InternationalCodeDesignator) {
                    throw new \Exception('Wrong schemeID');
                }
            }

            $identifier = new LocationIdentifier($value, $scheme);

            $deliveryLocation->setIdentifier($identifier);
        }

        $address = DeliveryAddress::fromXML($xpath, $deliveryLocationElement);

        if ($address instanceof DeliveryAddress) {
            $deliveryLocation->setAddress($address);
        }

        return $deliveryLocation;
    }
}
