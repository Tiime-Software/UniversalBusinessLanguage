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
        $currentNode = $document->createElement(self::XML_NODE, $this->value);

        $currentNode->setAttribute('currencyID', $this->currencyIdentifier->value);

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

        if (!is_numeric($taxableAmountElement->nodeValue)) {
            throw new \TypeError();
        }

        $value = (float) $taxableAmountElement->nodeValue;

        $currencyIdentifier = $taxableAmountElement->hasAttribute('currencyID') ?
            CurrencyCode::tryFrom($taxableAmountElement->getAttribute('currencyID')) : null;

        if (!$currencyIdentifier) {
            throw new \Exception('Invalid currency code');
        }

        return new self($value, $currencyIdentifier);
    }
}
