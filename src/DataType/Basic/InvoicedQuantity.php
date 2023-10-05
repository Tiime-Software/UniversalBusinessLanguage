<?php

declare(strict_types=1);

namespace Tiime\UniversalBusinessLanguage\DataType\Basic;

use Tiime\EN16931\DataType\UnitOfMeasurement;
use Tiime\EN16931\SemanticDataType\Quantity;

class InvoicedQuantity
{
    protected const XML_NODE = 'cbc:InvoicedQuantity';

    /**
     * BT-129.
     */
    private Quantity $quantity;

    /**
     * BT-130.
     */
    private UnitOfMeasurement $unitCode;

    public function __construct(float $quantity, UnitOfMeasurement $unitCode)
    {
        $this->quantity = new Quantity($quantity);
        $this->unitCode = $unitCode;
    }

    public function getQuantity(): float
    {
        return $this->quantity->getValueRounded();
    }

    public function getUnitCode(): UnitOfMeasurement
    {
        return $this->unitCode;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE, $this->quantity->getFormattedValueRounded());
        $currentNode->setAttribute('unitCode', $this->unitCode->value);

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): self
    {
        $invoicedQuantityElements = $xpath->query(sprintf('./%s', self::XML_NODE), $currentElement);

        if (1 !== $invoicedQuantityElements->count()) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $invoicedQuantityElement */
        $invoicedQuantityElement = $invoicedQuantityElements->item(0);

        $invoicedQuantity = $invoicedQuantityElement->nodeValue;
        $unitCode         = $invoicedQuantityElement->hasAttribute('unitCode') ?
            UnitOfMeasurement::tryFrom($invoicedQuantityElement->getAttribute('unitCode')) : null;

        if (null === $unitCode) {
            throw new \Exception('Wrong unitCode');
        }

        return new self((float) $invoicedQuantity, $unitCode);
    }
}
