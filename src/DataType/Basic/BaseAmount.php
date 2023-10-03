<?php

namespace Tiime\UniversalBusinessLanguage\DataType\Basic;

use Tiime\EN16931\DataType\CurrencyCode;
use Tiime\EN16931\SemanticDataType\Amount;

/**
 * BT-93. or BT-100.
 */
class BaseAmount
{
    protected const XML_NODE = 'cbc:BaseAmount';

    private Amount $amount;

    private CurrencyCode $currencyCode;

    public function __construct(float $value, CurrencyCode $currencyCode)
    {
        $this->amount       = new Amount($value);
        $this->currencyCode = $currencyCode;
    }

    public function getAmount(): float
    {
        return $this->amount->getValueRounded();
    }

    public function getCurrencyCode(): CurrencyCode
    {
        return $this->currencyCode;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE, $this->amount->getFormattedValueRounded());

        $currentNode->setAttribute('currencyID', $this->currencyCode->value);

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): ?self
    {
        $baseAmountElements = $xpath->query(sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $baseAmountElements->count()) {
            return null;
        }

        if ($baseAmountElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $baseAmountElement */
        $baseAmountElement = $baseAmountElements->item(0);
        $value             = (float) $baseAmountElement->nodeValue;

        if (!is_numeric($value)) {
            throw new \Exception('Invalid amount');
        }

        $currencyCode = $baseAmountElement->hasAttribute('currencyID') ?
            CurrencyCode::tryFrom($baseAmountElement->getAttribute('currencyID')) : null;

        if (!$currencyCode) {
            throw new \Exception('Invalid currency code');
        }

        return new self($value, $currencyCode);
    }
}
