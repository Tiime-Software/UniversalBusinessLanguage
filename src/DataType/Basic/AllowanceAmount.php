<?php

namespace Tiime\UniversalBusinessLanguage\DataType\Basic;

use Tiime\EN16931\DataType\CurrencyCode;
use Tiime\EN16931\SemanticDataType\Amount;

/**
 * BT-92. or BT-99.
 */
class AllowanceAmount
{
    protected const XML_NODE = 'cbc:Amount';

    private Amount $amount;

    private CurrencyCode $currencyCode;

    public function __construct(Amount $amount, CurrencyCode $currencyCode)
    {
        $this->amount       = $amount;
        $this->currencyCode = $currencyCode;
    }

    public function getAmount(): Amount
    {
        return $this->amount;
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

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): self
    {
        $allowanceAmountElements = $xpath->query(sprintf('./%s', self::XML_NODE), $currentElement);

        if (1 !== $allowanceAmountElements->count()) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $allowanceAmountElement */
        $allowanceAmountElement = $allowanceAmountElements->item(0);

        if (!is_numeric($allowanceAmountElement->nodeValue)) {
            throw new \TypeError();
        }

        $amount                 = new Amount((float) $allowanceAmountElement->nodeValue);

        $currencyCode = $allowanceAmountElement->hasAttribute('currencyID') ?
            CurrencyCode::tryFrom($allowanceAmountElement->getAttribute('currencyID')) : null;

        if (!$currencyCode) {
            throw new \Exception('Invalid currency code');
        }

        return new self($amount, $currencyCode);
    }
}
