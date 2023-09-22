<?php

declare(strict_types=1);

namespace Tiime\UniversalBusinessLanguage\DataType\Basic;

/**
 * BT-73.
 */
class InvoicePeriodStartDate
{
    protected const XML_NODE        = 'cbc:StartDate';
    protected const UBL_DATE_FORMAT = 'Y-m-d';

    private \DateTimeInterface $dateTimeString;

    public function __construct(\DateTimeInterface $dateTimeString)
    {
        $this->dateTimeString = $dateTimeString;
    }

    public function getDateTimeString(): \DateTimeInterface
    {
        return $this->dateTimeString;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        return $document->createElement(self::XML_NODE, $this->dateTimeString->format(self::UBL_DATE_FORMAT));
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): ?self
    {
        $dueDateElements = $xpath->query(sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $dueDateElements->count()) {
            return null;
        }

        if ($dueDateElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        $dateTimeString = (string) $dueDateElements->item(0)->nodeValue;

        $formattedDateTime = \DateTime::createFromFormat(self::UBL_DATE_FORMAT, $dateTimeString);

        if (!$formattedDateTime) {
            throw new \Exception('Malformed date');
        }

        $formattedDateTime->setTime(0, 0);

        return new self($formattedDateTime);
    }
}
