<?php

namespace Tiime\UniversalBusinessLanguage\Ubl21\Invoice\DataType\Basic;

use Tiime\EN16931\Codelist\CurrencyCodeISO4217 as CurrencyCode;
use Tiime\EN16931\SemanticDataType\Amount;

/**
 * BT-93. or BT-100.
 */
class BaseAmount
{
    protected const XML_NODE = 'cbc:BaseAmount';

    private Amount $value;

    private CurrencyCode $currencyIdentifier;

    public function __construct(float $value, CurrencyCode $currencyIdentifier)
    {
        $this->value              = new Amount($value);
        $this->currencyIdentifier = $currencyIdentifier;
    }

    public function getValue(): Amount
    {
        return $this->value;
    }

    public function getCurrencyCode(): CurrencyCode
    {
        return $this->currencyIdentifier;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE, $this->value->getFormattedValueRounded());

        $currentNode->setAttribute('currencyID', $this->currencyIdentifier->value);

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

        $currencyIdentifier = $baseAmountElement->hasAttribute('currencyID') ?
            CurrencyCode::tryFrom($baseAmountElement->getAttribute('currencyID')) : null;

        if (!$currencyIdentifier instanceof CurrencyCode) {
            throw new \Exception('Invalid currency code');
        }

        return new self($value, $currencyIdentifier);
    }
}
