<?php

namespace Tiime\UniversalBusinessLanguage\Ubl21\Invoice\DataType\Basic;

use Tiime\EN16931\Codelist\CurrencyCodeISO4217 as CurrencyCode;
use Tiime\EN16931\SemanticDataType\Amount;

/**
 * BT-106. or BT-131.
 */
class LineExtensionAmount
{
    protected const XML_NODE = 'cbc:LineExtensionAmount';

    private Amount $value;

    public function __construct(float $value)
    {
        $this->value              = new Amount($value);
    }

    public function getValue(): Amount
    {
        return $this->value;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        return $document->createElement(self::XML_NODE, $this->value->getFormattedValueRounded());
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): self
    {
        $lineExtensionAmountElements = $xpath->query(\sprintf('./%s', self::XML_NODE), $currentElement);

        if (1 !== $lineExtensionAmountElements->count()) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $lineExtensionAmountElement */
        $lineExtensionAmountElement = $lineExtensionAmountElements->item(0);

        if (!is_numeric($lineExtensionAmountElement->nodeValue)) {
            throw new \TypeError();
        }

        $value = (float) $lineExtensionAmountElement->nodeValue;

        return new self($value);
    }
}
