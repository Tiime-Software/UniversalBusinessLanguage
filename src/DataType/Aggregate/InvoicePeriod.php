<?php

namespace Tiime\UniversalBusinessLanguage\DataType\Aggregate;

use Tiime\EN16931\DataType\DateCode2005;
use Tiime\UniversalBusinessLanguage\DataType\Basic\InvoicePeriodEndDate;
use Tiime\UniversalBusinessLanguage\DataType\Basic\InvoicePeriodStartDate;

/**
 * BG-14.
 */
class InvoicePeriod
{
    protected const XML_NODE = 'cac:InvoicePeriod';

    /**
     * BT-8.
     */
    private ?DateCode2005 $descriptionCode;

    /**
     * BT-73.
     */
    private ?InvoicePeriodStartDate $startDate;

    /**
     * BT-74.
     */
    private ?InvoicePeriodEndDate $endDate;

    public function __construct()
    {
        $this->descriptionCode = null;
        $this->startDate       = null;
        $this->endDate         = null;
    }

    public function getDescriptionCode(): ?DateCode2005
    {
        return $this->descriptionCode;
    }

    public function setDescriptionCode(?DateCode2005 $descriptionCode): static
    {
        $this->descriptionCode = $descriptionCode;

        return $this;
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

        if ($this->descriptionCode instanceof DateCode2005) {
            $currentNode->appendChild($document->createElement('cbc:DescriptionCode', $this->descriptionCode->value));
        }

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

        if ($invoicePeriodElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $invoicePeriodElement */
        $invoicePeriodElement = $invoicePeriodElements->item(0);

        $invoicePeriod = new self();

        $descriptionCodeElements = $xpath->query('./cbc:DescriptionCode', $invoicePeriodElement);
        $startDate               = InvoicePeriodStartDate::fromXML($xpath, $invoicePeriodElement);
        $endDate                 = InvoicePeriodEndDate::fromXML($xpath, $invoicePeriodElement);

        if (1 === $descriptionCodeElements->count()) {
            $descriptionCode = DateCode2005::tryFrom((string) $descriptionCodeElements->item(0)->nodeValue);

            if (null === $descriptionCode) {
                throw new \Exception('Wrong description code');
            }
            $invoicePeriod->setDescriptionCode($descriptionCode);
        }

        if ($startDate instanceof InvoicePeriodStartDate) {
            $invoicePeriod->setStartDate($startDate);
        }

        if ($endDate instanceof InvoicePeriodEndDate) {
            $invoicePeriod->setEndDate($endDate);
        }

        return $invoicePeriod;
    }
}
