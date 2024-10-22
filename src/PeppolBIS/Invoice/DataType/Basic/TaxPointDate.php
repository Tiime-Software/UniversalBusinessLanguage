<?php

declare(strict_types=1);

namespace Tiime\UniversalBusinessLanguage\PeppolBIS\Invoice\DataType\Basic;

use Tiime\UniversalBusinessLanguage\PeppolBIS\Invoice\Utils\UniversalBusinessLanguageUtils;

/**
 * BT-7.
 */
class TaxPointDate
{
    protected const XML_NODE = 'cbc:TaxPointDate';

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
        return $document->createElement(self::XML_NODE, $this->dateTimeString->format(UniversalBusinessLanguageUtils::UBL_DATE_FORMAT));
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): ?self
    {
        $taxPointDateElements = $xpath->query(\sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $taxPointDateElements->count()) {
            return null;
        }

        if ($taxPointDateElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        $dateTimeString = (string) $taxPointDateElements->item(0)->nodeValue;

        $formattedDateTime = \DateTime::createFromFormat(UniversalBusinessLanguageUtils::UBL_DATE_FORMAT, $dateTimeString);

        if (!$formattedDateTime) {
            throw new \Exception('Malformed date');
        }

        $formattedDateTime->setTime(0, 0);

        return new self($formattedDateTime);
    }
}
