<?php

namespace Tiime\UniversalBusinessLanguage\DataType\Basic;

use Tiime\EN16931\DataType\CurrencyCode;
use Tiime\EN16931\SemanticDataType\Amount;

/**
 * BT-114.
 */
class PayableRoundingAmount
{
    protected const XML_NODE = 'cbc:PayableRoundingAmount';

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
        $payableRoundingAmountElements = $xpath->query(sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $payableRoundingAmountElements->count()) {
            return null;
        }

        if ($payableRoundingAmountElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $payableRoundingAmountElement */
        $payableRoundingAmountElement = $payableRoundingAmountElements->item(0);
        $value                        = (float) $payableRoundingAmountElement->nodeValue;

        if (!is_numeric($value)) {
            throw new \Exception('Invalid amount');
        }

        $currencyCode = $payableRoundingAmountElement->hasAttribute('currencyID') ?
            CurrencyCode::tryFrom($payableRoundingAmountElement->getAttribute('currencyID')) : null;

        if (!$currencyCode) {
            throw new \Exception('Invalid currency code');
        }

        return new self($value, $currencyCode);
    }
}
