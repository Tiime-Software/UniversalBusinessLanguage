<?php

namespace Tiime\UniversalBusinessLanguage\Ubl21\CreditNote\DataType\Basic;

use Tiime\EN16931\SemanticDataType\Amount;

/**
 * BT-93. or BT-100.
 */
class BaseAmount
{
    protected const XML_NODE = 'cbc:BaseAmount';

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

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): ?self
    {
        $baseAmountElements = $xpath->query(\sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $baseAmountElements->count()) {
            return null;
        }

        if ($baseAmountElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $baseAmountElement */
        $baseAmountElement = $baseAmountElements->item(0);

        if (!is_numeric($baseAmountElement->nodeValue)) {
            throw new \TypeError();
        }

        $value = (float) $baseAmountElement->nodeValue;

        return new self($value);
    }
}
