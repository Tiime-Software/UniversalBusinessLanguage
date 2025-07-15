<?php

namespace Tiime\UniversalBusinessLanguage\Ubl21Standard\CreditNote\DataType\Basic;

use Tiime\EN16931\Codelist\CurrencyCodeISO4217 as CurrencyCode;
use Tiime\EN16931\SemanticDataType\Amount;

/**
 * BT-112.
 */
class TaxInclusiveAmount
{
    protected const XML_NODE = 'cbc:TaxInclusiveAmount';

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

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): self
    {
        $taxInclusiveAmountElements = $xpath->query(\sprintf('./%s', self::XML_NODE), $currentElement);

        if (1 !== $taxInclusiveAmountElements->count()) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $taxInclusiveAmountElement */
        $taxInclusiveAmountElement = $taxInclusiveAmountElements->item(0);

        if (!is_numeric($taxInclusiveAmountElement->nodeValue)) {
            throw new \TypeError();
        }

        $value = (float) $taxInclusiveAmountElement->nodeValue;

        $currencyIdentifier = $taxInclusiveAmountElement->hasAttribute('currencyID') ?
            CurrencyCode::tryFrom($taxInclusiveAmountElement->getAttribute('currencyID')) : null;

        if (!$currencyIdentifier instanceof CurrencyCode) {
            throw new \Exception('Invalid currency code');
        }

        return new self($value, $currencyIdentifier);
    }
}
