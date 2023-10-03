<?php

namespace Tiime\UniversalBusinessLanguage\DataType\Basic;

use Tiime\EN16931\DataType\CurrencyCode;
use Tiime\EN16931\SemanticDataType\Amount;

/**
 * BT-112.
 */
class TaxInclusiveAmount
{
    protected const XML_NODE = 'cbc:TaxInclusiveAmount';

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
        $taxInclusiveAmountElements = $xpath->query(sprintf('./%s', self::XML_NODE), $currentElement);

        if (1 !== $taxInclusiveAmountElements->count()) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $taxInclusiveAmountElement */
        $taxInclusiveAmountElement = $taxInclusiveAmountElements->item(0);
        $value                     = (float) $taxInclusiveAmountElement->nodeValue;

        if (!is_numeric($value)) {
            throw new \Exception('Invalid amount');
        }

        $currencyCode = $taxInclusiveAmountElement->hasAttribute('currencyID') ?
            CurrencyCode::tryFrom($taxInclusiveAmountElement->getAttribute('currencyID')) : null;

        if (!$currencyCode) {
            throw new \Exception('Invalid currency code');
        }

        return new self($value, $currencyCode);
    }
}
