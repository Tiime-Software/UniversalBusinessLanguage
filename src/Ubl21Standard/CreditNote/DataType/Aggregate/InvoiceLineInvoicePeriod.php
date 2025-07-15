<?php

namespace Tiime\UniversalBusinessLanguage\Ubl21Standard\CreditNote\DataType\Aggregate;

use Tiime\UniversalBusinessLanguage\Ubl21Standard\CreditNote\DataType\Basic\EndDate;
use Tiime\UniversalBusinessLanguage\Ubl21Standard\CreditNote\DataType\Basic\StartDate;

/**
 * BG-26.
 */
class InvoiceLineInvoicePeriod
{
    protected const XML_NODE = 'cac:InvoicePeriod';

    /**
     * BT-134.
     */
    private ?StartDate $startDate;

    /**
     * BT-135.
     */
    private ?EndDate $endDate;

    public function __construct()
    {
        $this->startDate = null;
        $this->endDate   = null;
    }

    public function getStartDate(): ?StartDate
    {
        return $this->startDate;
    }

    public function setStartDate(?StartDate $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?EndDate
    {
        return $this->endDate;
    }

    public function setEndDate(?EndDate $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);

        if ($this->startDate instanceof StartDate) {
            $currentNode->appendChild($this->startDate->toXML($document));
        }

        if ($this->endDate instanceof EndDate) {
            $currentNode->appendChild($this->endDate->toXML($document));
        }

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): ?self
    {
        $invoicePeriodElements = $xpath->query(\sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $invoicePeriodElements->count()) {
            return null;
        }

        if ($invoicePeriodElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $invoicePeriodElement */
        $invoicePeriodElement = $invoicePeriodElements->item(0);

        $invoicePeriod = new self();

        $startDate = StartDate::fromXML($xpath, $invoicePeriodElement);
        $endDate   = EndDate::fromXML($xpath, $invoicePeriodElement);

        if ($startDate instanceof StartDate) {
            $invoicePeriod->setStartDate($startDate);
        }

        if ($endDate instanceof EndDate) {
            $invoicePeriod->setEndDate($endDate);
        }

        return $invoicePeriod;
    }
}
