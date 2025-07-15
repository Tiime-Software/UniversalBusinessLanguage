<?php

declare(strict_types=1);

namespace Tiime\UniversalBusinessLanguage\Ubl21Standard\Invoice\DataType\Aggregate;

use Tiime\UniversalBusinessLanguage\Ubl21Standard\Invoice\DataType\Basic\TaxableAmount;
use Tiime\UniversalBusinessLanguage\Ubl21Standard\Invoice\DataType\Basic\TaxAmount;

/**
 * BG-23.
 */
class TaxSubtotal
{
    protected const XML_NODE = 'cac:TaxSubtotal';

    /**
     * BT-116.
     */
    private TaxableAmount $taxableAmount;

    /**
     * BT-117.
     */
    private TaxAmount $taxAmount;

    private SubtotalTaxCategory $taxCategory;

    public function __construct(TaxableAmount $taxableAmount, TaxAmount $taxAmount, SubtotalTaxCategory $taxCategory)
    {
        $this->taxableAmount = $taxableAmount;
        $this->taxAmount     = $taxAmount;
        $this->taxCategory   = $taxCategory;
    }

    public function getTaxableAmount(): TaxableAmount
    {
        return $this->taxableAmount;
    }

    public function getTaxAmount(): TaxAmount
    {
        return $this->taxAmount;
    }

    public function getTaxCategory(): SubtotalTaxCategory
    {
        return $this->taxCategory;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);

        $currentNode->appendChild($this->taxableAmount->toXML($document));
        $currentNode->appendChild($this->taxAmount->toXML($document));
        $currentNode->appendChild($this->taxCategory->toXML($document));

        return $currentNode;
    }

    /**
     * @return array<int, TaxSubtotal>
     */
    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): array
    {
        $taxSubtotalElements = $xpath->query(\sprintf('./%s', self::XML_NODE), $currentElement);

        $taxSubtotals = [];

        /** @var \DOMElement $taxSubtotalElement */
        foreach ($taxSubtotalElements as $taxSubtotalElement) {
            $taxableAmount = TaxableAmount::fromXML($xpath, $taxSubtotalElement);
            $taxAmount     = TaxAmount::fromXML($xpath, $taxSubtotalElement);
            $taxCategory   = SubtotalTaxCategory::fromXML($xpath, $taxSubtotalElement);

            $taxSubtotal = new self($taxableAmount, $taxAmount, $taxCategory);

            $taxSubtotals[] = $taxSubtotal;
        }

        return $taxSubtotals;
    }
}
