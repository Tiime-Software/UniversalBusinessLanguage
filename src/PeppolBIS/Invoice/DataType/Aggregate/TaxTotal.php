<?php

declare(strict_types=1);

namespace Tiime\UniversalBusinessLanguage\PeppolBIS\Invoice\DataType\Aggregate;

use Tiime\UniversalBusinessLanguage\PeppolBIS\Invoice\DataType\Basic\TaxAmount;

class TaxTotal
{
    protected const XML_NODE = 'cac:TaxTotal';

    /**
     * BT-110.
     */
    private TaxAmount $taxAmount;

    /**
     * BG-23.
     *
     * @var array<int, TaxSubtotal>
     */
    private array $taxSubtotals;

    public function __construct(TaxAmount $taxAmount)
    {
        $this->taxAmount    = $taxAmount;
        $this->taxSubtotals = [];
    }

    public function getTaxAmount(): TaxAmount
    {
        return $this->taxAmount;
    }

    /**
     * @return array|TaxSubtotal[]
     */
    public function getTaxSubtotals(): array
    {
        return $this->taxSubtotals;
    }

    /**
     * @param array<int, TaxSubtotal> $taxSubtotals
     *
     * @return $this
     */
    public function setTaxSubtotals(array $taxSubtotals): static
    {
        foreach ($taxSubtotals as $taxSubtotal) {
            if (!$taxSubtotal instanceof TaxSubtotal) {
                throw new \TypeError();
            }
        }

        $this->taxSubtotals = $taxSubtotals;

        return $this;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);

        $currentNode->appendChild($this->taxAmount->toXML($document));

        foreach ($this->taxSubtotals as $taxSubtotal) {
            $currentNode->appendChild($taxSubtotal->toXML($document));
        }

        return $currentNode;
    }

    /**
     * @return array<int, TaxTotal>
     */
    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): array
    {
        $taxTotalElements = $xpath->query(\sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $taxTotalElements->count() || $taxTotalElements->count() > 2) {
            throw new \Exception('Malformed');
        }

        $taxTotals = [];

        /** @var \DOMElement $taxTotalElement */
        foreach ($taxTotalElements as $taxTotalElement) {
            $value = TaxAmount::fromXML($xpath, $taxTotalElement);

            $taxTotal = new self($value);

            $taxSubtotals = TaxSubtotal::fromXML($xpath, $taxTotalElement);

            if (\count($taxSubtotals) > 0) {
                $taxTotal->setTaxSubtotals($taxSubtotals);
            }

            $taxTotals[] = $taxTotal;
        }

        return $taxTotals;
    }
}
