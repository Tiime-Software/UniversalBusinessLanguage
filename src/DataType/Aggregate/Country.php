<?php

namespace Tiime\UniversalBusinessLanguage\DataType\Aggregate;

use Tiime\EN16931\DataType\CountryAlpha2Code;

class Country
{
    protected const XML_NODE = 'cac:Country';

    /**
     * BT-40.
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

        $currentNode->appendChild($document->createElement('cbc:IdentificationCode', $this->identificationCode));

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): self
    {
        $countryElements = $xpath->query(sprintf('./%s', self::XML_NODE), $currentElement);

        if (1 !== $countryElements->count()) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $countryElement */
        $countryElement = $countryElements->item(0);

        $identificationCodeElements = $xpath->query('./cbc:IdentificationCode', $countryElement);

        if (1 !== $identificationCodeElements->count()) {
            throw new \Exception('Malformed');
        }

        $identificationCode = CountryAlpha2Code::tryFrom($identificationCodeElements->item(0)->nodeValue);

        if (null === $identificationCode) {
            throw new \Exception('Wrong country');
        }

        return new self($identificationCode);
    }
}
