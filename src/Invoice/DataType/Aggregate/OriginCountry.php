<?php

namespace Tiime\UniversalBusinessLanguage\Invoice\DataType\Aggregate;

use Tiime\EN16931\DataType\CountryAlpha2Code;

class OriginCountry
{
    protected const XML_NODE = 'cac:OriginCountry';

    /**
     * BT-159.
     */
    private CountryAlpha2Code $identificationCode;

    public function __construct(CountryAlpha2Code $identificationCode)
    {
        $this->identificationCode = $identificationCode;
    }

    public function getIdentificationCode(): CountryAlpha2Code
    {
        return $this->identificationCode;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);

        $currentNode->appendChild($document->createElement('cbc:IdentificationCode', $this->identificationCode->value));

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): ?self
    {
        $originCountryElements = $xpath->query(sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $originCountryElements->count()) {
            return null;
        }

        if ($originCountryElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $originCountryElement */
        $originCountryElement = $originCountryElements->item(0);

        $identificationCodeElements = $xpath->query('./cbc:IdentificationCode', $originCountryElement);

        if (1 !== $identificationCodeElements->count()) {
            throw new \Exception('Malformed');
        }

        $identificationCode = CountryAlpha2Code::tryFrom((string) $identificationCodeElements->item(0)->nodeValue);

        if (null === $identificationCode) {
            throw new \Exception('Wrong country');
        }

        return new self($identificationCode);
    }
}
