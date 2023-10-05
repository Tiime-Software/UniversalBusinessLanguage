<?php

namespace Tiime\UniversalBusinessLanguage\DataType\Basic;

use Tiime\EN16931\DataType\CurrencyCode;
use Tiime\EN16931\SemanticDataType\Amount;

/**
 * BT-146.
 */
class PriceAmount
{
    protected const XML_NODE = 'cbc:PriceAmount';

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
        $currentNode = $document->createElement(self::XML_NODE, $this->amount);

        $currentNode->setAttribute('currencyID', $this->currencyCode->value);

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): self
    {
        $priceAmountElements = $xpath->query(sprintf('./%s', self::XML_NODE), $currentElement);

        if (1 !== $priceAmountElements->count()) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $priceAmountElement */
        $priceAmountElement = $priceAmountElements->item(0);
        $value              = (float) $priceAmountElement->nodeValue;

        if (!is_numeric($value)) {
            throw new \Exception('Invalid amount amount');
        }

        $currencyCode = $priceAmountElement->hasAttribute('currencyID') ?
            CurrencyCode::tryFrom($priceAmountElement->getAttribute('currencyID')) : null;

        if (!$currencyCode) {
            throw new \Exception('Invalid currency code');
        }

        return new self($value, $currencyCode);
    }
}
