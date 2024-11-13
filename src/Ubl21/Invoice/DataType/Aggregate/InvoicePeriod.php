<?php

namespace Tiime\UniversalBusinessLanguage\Ubl21\Invoice\DataType\Aggregate;

use Tiime\EN16931\Codelist\TimeReferencingCodeUNTDID2005 as DateCode2005;
use Tiime\UniversalBusinessLanguage\Ubl21\Invoice\DataType\Basic\EndDate;
use Tiime\UniversalBusinessLanguage\Ubl21\Invoice\DataType\Basic\StartDate;

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
    private ?StartDate $startDate;

    /**
     * BT-74.
     */
    private ?EndDate $endDate;

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

        if ($this->descriptionCode instanceof DateCode2005) {
            $currentNode->appendChild($document->createElement('cbc:DescriptionCode', $this->descriptionCode->value));
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

        $descriptionCodeElements = $xpath->query('./cbc:DescriptionCode', $invoicePeriodElement);
        $startDate               = StartDate::fromXML($xpath, $invoicePeriodElement);
        $endDate                 = EndDate::fromXML($xpath, $invoicePeriodElement);

        if (1 === $descriptionCodeElements->count()) {
            $descriptionCode = DateCode2005::tryFrom((string) $descriptionCodeElements->item(0)->nodeValue);

            if (null === $descriptionCode) {
                throw new \Exception('Wrong description code');
            }
            $invoicePeriod->setDescriptionCode($descriptionCode);
        }

        if ($startDate instanceof StartDate) {
            $invoicePeriod->setStartDate($startDate);
        }

        if ($endDate instanceof EndDate) {
            $invoicePeriod->setEndDate($endDate);
        }

        return $invoicePeriod;
    }
}
