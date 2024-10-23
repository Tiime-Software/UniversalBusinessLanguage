<?php

namespace Tiime\UniversalBusinessLanguage\PeppolBIS\CreditNote\DataType\Basic;

use Tiime\EN16931\Codelist\CurrencyCodeISO4217 as CurrencyCode;
use Tiime\EN16931\SemanticDataType\Amount;

/**
 * BT-113.
 */
class PrepaidAmount
{
    protected const XML_NODE = 'cbc:PrepaidAmount';

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
        $prepaidAmountElements = $xpath->query(\sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $prepaidAmountElements->count()) {
            return null;
        }

        if ($prepaidAmountElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $prepaidAmountElement */
        $prepaidAmountElement = $prepaidAmountElements->item(0);

        if (!is_numeric($prepaidAmountElement->nodeValue)) {
            throw new \TypeError();
        }

        $value = (float) $prepaidAmountElement->nodeValue;

        $currencyIdentifier = $prepaidAmountElement->hasAttribute('currencyID') ?
            CurrencyCode::tryFrom($prepaidAmountElement->getAttribute('currencyID')) : null;

        if (!$currencyIdentifier instanceof CurrencyCode) {
            throw new \Exception('Invalid currency code');
        }

        return new self($value, $currencyIdentifier);
    }
}
