<?php

namespace Tiime\UniversalBusinessLanguage\Ubl21\Invoice\DataType\Basic;

use Tiime\EN16931\SemanticDataType\Amount;

/**
 * BT-92. or BT-99.
 */
class AllowanceChargeAmount
{
    protected const XML_NODE = 'cbc:Amount';

    private Amount $value;

    public function __construct(float $value)
    {
        $this->value = new Amount($value);
    }

    public function getValue(): Amount
    {
        return $this->value;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE, $this->value->getFormattedValueRounded());

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): self
    {
        $allowanceAmountElements = $xpath->query(\sprintf('./%s', self::XML_NODE), $currentElement);

        if (1 !== $allowanceAmountElements->count()) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $allowanceAmountElement */
        $allowanceAmountElement = $allowanceAmountElements->item(0);

        if (!is_numeric($allowanceAmountElement->nodeValue)) {
            throw new \TypeError();
        }

        $value = (float) $allowanceAmountElement->nodeValue;

        return new self($value);
    }
}
