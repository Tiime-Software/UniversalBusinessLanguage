<?php

namespace Tiime\UniversalBusinessLanguage\DataType\Aggregate;

use Tiime\UniversalBusinessLanguage\DataType\Basic\InvoicePeriodEndDate;
use Tiime\UniversalBusinessLanguage\DataType\Basic\InvoicePeriodStartDate;

/**
 * BG-26.
 */
class InvoiceLineInvoicePeriod
{
    protected const XML_NODE = 'cac:InvoicePeriod';

    /**
     * BT-134.
     */
    private ?InvoicePeriodStartDate $startDate;

    /**
     * BT-135.
     */
    private ?InvoicePeriodEndDate $endDate;

    public function __construct()
    {
        $this->startDate = null;
        $this->endDate   = null;
    }

    public function getStartDate(): ?InvoicePeriodStartDate
    {
        return $this->startDate;
    }

    public function setStartDate(?InvoicePeriodStartDate $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?InvoicePeriodEndDate
    {
        return $this->endDate;
    }

    public function setEndDate(?InvoicePeriodEndDate $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);

        if ($this->startDate instanceof InvoicePeriodStartDate) {
            $currentNode->appendChild($this->startDate->toXML($document));
        }

        if ($this->endDate instanceof InvoicePeriodEndDate) {
            $currentNode->appendChild($this->endDate->toXML($document));
        }

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): ?self
    {
        $invoicePeriodElements = $xpath->query(sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $invoicePeriodElements->count()) {
            return null;
        }

        if (1 < $invoicePeriodElements->count()) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $invoicePeriodElement */
        $invoicePeriodElement = $invoicePeriodElements->item(0);

        $invoicePeriod = new self();

        $startDate = InvoicePeriodStartDate::fromXML($xpath, $invoicePeriodElement);
        $endDate   = InvoicePeriodEndDate::fromXML($xpath, $invoicePeriodElement);

        if ($startDate instanceof InvoicePeriodStartDate) {
            $invoicePeriod->setStartDate($startDate);
        }

        if ($endDate instanceof InvoicePeriodEndDate) {
            $invoicePeriod->setEndDate($endDate);
        }

        return $invoicePeriod;
    }
}
