<?php

declare(strict_types=1);

namespace Tiime\UniversalBusinessLanguage\PeppolBIS\CreditNote\DataType\Basic;

use Tiime\EN16931\Codelist\UnitOfMeasureCode as UnitOfMeasurement;
use Tiime\EN16931\SemanticDataType\Quantity;

class CreditedQuantity
{
    protected const XML_NODE = 'cbc:CreditedQuantity';

    /**
     * BT-129.
     */
    private Quantity $value;

    /**
     * BT-130.
     */
    private UnitOfMeasurement $unitCode;

    public function __construct(float $value, UnitOfMeasurement $unitCode)
    {
        $this->value    = new Quantity($value);
        $this->unitCode = $unitCode;
    }

    public function getQuantity(): Quantity
    {
        return $this->value;
    }

    public function getUnitCode(): UnitOfMeasurement
    {
        return $this->unitCode;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE, $this->value->getFormattedValueRounded());
        $currentNode->setAttribute('unitCode', $this->unitCode->value);

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): self
    {
        $invoicedQuantityElements = $xpath->query(\sprintf('./%s', self::XML_NODE), $currentElement);

        if (1 !== $invoicedQuantityElements->count()) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $invoicedQuantityElement */
        $invoicedQuantityElement = $invoicedQuantityElements->item(0);

        if (!is_numeric($invoicedQuantityElement->nodeValue)) {
            throw new \TypeError();
        }

        $value = (float) $invoicedQuantityElement->nodeValue;

        $unitCode = $invoicedQuantityElement->hasAttribute('unitCode') ?
            UnitOfMeasurement::tryFrom($invoicedQuantityElement->getAttribute('unitCode')) : null;

        if (!$unitCode instanceof UnitOfMeasurement) {
            throw new \Exception('Wrong unitCode');
        }

        return new self($value, $unitCode);
    }
}
