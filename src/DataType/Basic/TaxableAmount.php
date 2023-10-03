<?php

namespace Tiime\UniversalBusinessLanguage\DataType\Basic;

use Tiime\EN16931\DataType\CurrencyCode;
use Tiime\EN16931\SemanticDataType\Amount;

/**
 * BT-116.
 */
class TaxableAmount
{
    protected const XML_NODE = 'cbc:TaxableAmount';

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
        $taxableAmountElements = $xpath->query(sprintf('./%s', self::XML_NODE), $currentElement);

        if (1 !== $taxableAmountElements->count()) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $taxableAmountElement */
        $taxableAmountElement = $taxableAmountElements->item(0);
        $value                = (float) $taxableAmountElement->nodeValue;

        if (!is_numeric($value)) {
            throw new \Exception('Invalid amount amount');
        }

        $currencyCode = $taxableAmountElement->hasAttribute('currencyID') ?
            CurrencyCode::tryFrom($taxableAmountElement->getAttribute('currencyID')) : null;

        if (!$currencyCode) {
            throw new \Exception('Invalid currency code');
        }

        return new self($value, $currencyCode);
    }
}
