<?php

declare(strict_types=1);

namespace Tiime\UniversalBusinessLanguage\DataType\Basic;

class IssueDateTime
{
    protected const XML_NODE        = 'cbc:IssueDate';
    protected const UBL_DATE_FORMAT = 'Y-m-d';

    /**
     * BT-2.
     */
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

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): self
    {
        $issueDateTimeElement = $xpath->query(sprintf('./%s', self::XML_NODE), $currentElement);

        if (!$issueDateTimeElement || !$issueDateTimeElement->item(0) || 1 !== $issueDateTimeElement->count()) {
            throw new \Exception('Malformed');
        }

        $dateTimeString = (string) $issueDateTimeElement->item(0)->nodeValue;

        $formattedDateTime = \DateTime::createFromFormat(self::UBL_DATE_FORMAT, $dateTimeString);

        if (!$formattedDateTime) {
            throw new \Exception('Malformed date');
        }

        $formattedDateTime->setTime(0, 0);

        return new self($formattedDateTime);
    }
}
