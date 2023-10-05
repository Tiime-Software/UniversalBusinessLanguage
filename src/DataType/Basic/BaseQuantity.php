<?php

declare(strict_types=1);

namespace Tiime\UniversalBusinessLanguage\DataType\Basic;

use Tiime\EN16931\DataType\UnitOfMeasurement;
use Tiime\EN16931\SemanticDataType\Quantity;

/**
 * BT-149.
 */
class BaseQuantity
{
    protected const XML_NODE = 'cbc:InvoicedQuantity';

    private Quantity $quantity;

    private ?UnitOfMeasurement $unitCode;

    public function __construct(float $quantity)
    {
        $this->quantity = new Quantity($quantity);
        $this->unitCode = null;
    }

    public function getQuantity(): float
    {
        return $this->quantity->getValueRounded();
    }

    public function getUnitCode(): ?UnitOfMeasurement
    {
        return $this->unitCode;
    }

    public function setUnitCode(?UnitOfMeasurement $unitCode): static
    {
        $this->unitCode = $unitCode;

        return $this;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE, $this->quantity->getFormattedValueRounded());

        if ($this->unitCode instanceof UnitOfMeasurement) {
            $currentNode->setAttribute('unitCode', $this->unitCode->value);
        }

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): ?self
    {
        $baseQuantityElements = $xpath->query(sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $baseQuantityElements->count()) {
            return null;
        }

        /** @var \DOMElement $baseQuantityElement */
        $baseQuantityElement = $baseQuantityElements->item(0);

        $baseQuantity = $baseQuantityElement->nodeValue;

        $baseQuantity = new self((float) $baseQuantity);

        $unitCode = $baseQuantityElement->hasAttribute('unitCode') ?
            UnitOfMeasurement::tryFrom($baseQuantityElement->getAttribute('unitCode')) : null;

        if ($unitCode instanceof UnitOfMeasurement) {
            $baseQuantity->setUnitCode($unitCode);
        }

        return $baseQuantity;
    }
}
