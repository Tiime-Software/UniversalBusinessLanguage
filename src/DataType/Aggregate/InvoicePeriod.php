<?php

namespace Tiime\UniversalBusinessLanguage\DataType\Aggregate;

use Tiime\EN16931\DataType\DateCode2005;

class InvoicePeriod
{
    protected const XML_NODE = 'cac:InvoicePeriod';

    /**
     * BT-8.
     */
    protected ?DateCode2005 $descriptionCode;

    public function __construct()
    {
        $this->descriptionCode = null;
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

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $element = $document->createElement(self::XML_NODE);

        if ($this->descriptionCode instanceof DateCode2005) {
            $element->appendChild($document->createElement('cbc:DescriptionCode', $this->descriptionCode->value));
        }

        return $element;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): ?self
    {
        $invoicePeriodElements = $xpath->query(sprintf('./%s', self::XML_NODE), $currentElement);

        if (!$invoicePeriodElements || 0 === $invoicePeriodElements->count()) {
            return null;
        }

        if (1 < $invoicePeriodElements->count()) {
            throw new \Exception('Malformed InvoicePeriod');
        }

        /** @var \DOMElement $invoicePeriodElement */
        $invoicePeriodElement = $invoicePeriodElements->item(0);

        $invoicePeriod = new self();

        $descriptionCodeElements = $xpath->query('./cbc:DescriptionCode', $invoicePeriodElement);

        if ($descriptionCodeElements
            && $descriptionCodeElements->item(0)
            && 1 === $descriptionCodeElements->count()) {
            $descriptionCode = DateCode2005::tryFrom((string) $descriptionCodeElements->item(0)->nodeValue);
            $invoicePeriod->setDescriptionCode($descriptionCode);
        }

        return $invoicePeriod;
    }
}
