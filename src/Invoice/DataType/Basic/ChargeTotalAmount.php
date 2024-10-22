<?php

namespace Tiime\UniversalBusinessLanguage\Invoice\DataType\Basic;

use Tiime\EN16931\Codelist\CurrencyCodeISO4217 as CurrencyCode;
use Tiime\EN16931\SemanticDataType\Amount;

/**
 * BT-108.
 */
class ChargeTotalAmount
{
    protected const XML_NODE = 'cbc:ChargeTotalAmount';

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
        $chargeTotalAmountElements = $xpath->query(\sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $chargeTotalAmountElements->count()) {
            return null;
        }

        if ($chargeTotalAmountElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $chargeTotalAmountElement */
        $chargeTotalAmountElement = $chargeTotalAmountElements->item(0);

        if (!is_numeric($chargeTotalAmountElement->nodeValue)) {
            throw new \TypeError();
        }

        $value = (float) $chargeTotalAmountElement->nodeValue;

        $currencyIdentifier = $chargeTotalAmountElement->hasAttribute('currencyID') ?
            CurrencyCode::tryFrom($chargeTotalAmountElement->getAttribute('currencyID')) : null;

        if (!$currencyIdentifier instanceof CurrencyCode) {
            throw new \Exception('Invalid currency code');
        }

        return new self($value, $currencyIdentifier);
    }
}
