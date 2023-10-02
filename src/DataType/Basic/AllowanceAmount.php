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
        $allowanceAmountElements = $xpath->query(sprintf('./%s', self::XML_NODE), $currentElement);

        if (1 !== $allowanceAmountElements->count()) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $allowanceAmountElement */
        $allowanceAmountElement = $allowanceAmountElements->item(0);
        $value                  = (float) $allowanceAmountElement->nodeValue;

        if (!is_numeric($value)) {
            throw new \Exception('Invalid amount amount');
        }

        $currencyCode = $allowanceAmountElement->hasAttribute('currencyID') ?
            CurrencyCode::tryFrom($allowanceAmountElement->getAttribute('currencyID')) : null;

        if (!$currencyCode) {
            throw new \Exception('Invalid currency code');
        }

        return new self($value, $currencyCode);
    }
}
