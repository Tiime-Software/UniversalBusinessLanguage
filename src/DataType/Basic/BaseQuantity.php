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
    protected const XML_NODE = 'cbc:BaseQuantity';

    private Quantity $value;

    private ?UnitOfMeasurement $unitCode;

    public function __construct(float $value)
    {
        $this->value = new Quantity($value);
        $this->unitCode = null;
    }

    public function getQuantity(): Quantity
    {
        return $this->value;
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
        $currentNode = $document->createElement(self::XML_NODE, $this->value->getFormattedValueRounded());

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

        if (!is_numeric($baseQuantityElement->nodeValue)) {
            throw new \TypeError();
        }

        $value = (float) $baseQuantityElement->nodeValue;
        
        $baseQuantity = new self($value);

        $unitCode = $baseQuantityElement->hasAttribute('unitCode') ?
            UnitOfMeasurement::tryFrom($baseQuantityElement->getAttribute('unitCode')) : null;

        if ($unitCode instanceof UnitOfMeasurement) {
            $baseQuantity->setUnitCode($unitCode);
        }

        return $baseQuantity;
    }
}
