<?php

namespace Tiime\UniversalBusinessLanguage\CreditNote\DataType\Basic;

use Tiime\EN16931\DataType\CurrencyCode;
use Tiime\EN16931\SemanticDataType\Amount;

/**
 * BT-107.
 */
class AllowanceTotalAmount
{
    protected const XML_NODE = 'cbc:AllowanceTotalAmount';

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
        $allowanceTotalAmountElements = $xpath->query(sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $allowanceTotalAmountElements->count()) {
            return null;
        }

        if ($allowanceTotalAmountElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $allowanceTotalAmountElement */
        $allowanceTotalAmountElement = $allowanceTotalAmountElements->item(0);

        if (!is_numeric($allowanceTotalAmountElement->nodeValue)) {
            throw new \TypeError();
        }

        $value = (float) $allowanceTotalAmountElement->nodeValue;

        $currencyIdentifier = $allowanceTotalAmountElement->hasAttribute('currencyID') ?
            CurrencyCode::tryFrom($allowanceTotalAmountElement->getAttribute('currencyID')) : null;

        if (!$currencyIdentifier instanceof CurrencyCode) {
            throw new \Exception('Invalid currency code');
        }

        return new self($value, $currencyIdentifier);
    }
}
