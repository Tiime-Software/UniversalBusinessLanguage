<?php

namespace Tiime\UniversalBusinessLanguage\CreditNote\DataType\Aggregate;

use Tiime\UniversalBusinessLanguage\CreditNote\DataType\Basic\ActualDeliveryDate;

/**+
 * BG-13
 */
class Delivery
{
    protected const XML_NODE = 'cac:Delivery';

    /**
     * BT-72-00.
     */
    private ?ActualDeliveryDate $actualDeliveryDate;

    private ?DeliveryLocation $deliveryLocation;

    private ?DeliveryParty $deliveryParty;

    public function __construct()
    {
        $this->actualDeliveryDate = null;
        $this->deliveryLocation   = null;
        $this->deliveryParty      = null;
    }

    public function getActualDeliveryDate(): ?ActualDeliveryDate
    {
        return $this->actualDeliveryDate;
    }

    public function setActualDeliveryDate(?ActualDeliveryDate $actualDeliveryDate): static
    {
        $this->actualDeliveryDate = $actualDeliveryDate;

        return $this;
    }

    public function getDeliveryLocation(): ?DeliveryLocation
    {
        return $this->deliveryLocation;
    }

    public function setDeliveryLocation(?DeliveryLocation $deliveryLocation): static
    {
        $this->deliveryLocation = $deliveryLocation;

        return $this;
    }

    public function getDeliveryParty(): ?DeliveryParty
    {
        return $this->deliveryParty;
    }

    public function setDeliveryParty(?DeliveryParty $deliveryParty): static
    {
        $this->deliveryParty = $deliveryParty;

        return $this;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);

        if ($this->actualDeliveryDate instanceof ActualDeliveryDate) {
            $currentNode->appendChild($this->actualDeliveryDate->toXML($document));
        }

        if ($this->deliveryLocation instanceof DeliveryLocation) {
            $currentNode->appendChild($this->deliveryLocation->toXML($document));
        }

        if ($this->deliveryParty instanceof DeliveryParty) {
            $currentNode->appendChild($this->deliveryParty->toXML($document));
        }

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): ?self
    {
        $deliveryElements = $xpath->query(sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $deliveryElements->count()) {
            return null;
        }

        if ($deliveryElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $deliveryElement */
        $deliveryElement = $deliveryElements->item(0);

        $actualDeliveryDate = ActualDeliveryDate::fromXML($xpath, $deliveryElement);
        $deliveryLocation   = DeliveryLocation::fromXML($xpath, $deliveryElement);
        $deliveryParty      = DeliveryParty::fromXML($xpath, $deliveryElement);

        $delivery = new self();

        if ($actualDeliveryDate instanceof ActualDeliveryDate) {
            $delivery->setActualDeliveryDate($actualDeliveryDate);
        }

        if ($deliveryLocation instanceof DeliveryLocation) {
            $delivery->setDeliveryLocation($deliveryLocation);
        }

        if ($deliveryParty instanceof DeliveryParty) {
            $delivery->setDeliveryParty($deliveryParty);
        }

        return $delivery;
    }
}
