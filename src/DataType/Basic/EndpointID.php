<?php

namespace Tiime\UniversalBusinessLanguage\DataType\Basic;

use Tiime\EN16931\DataType\ElectronicAddressScheme;
use Tiime\EN16931\DataType\Identifier\ElectronicAddressIdentifier;

/**
 * BT-34.
 */
class EndpointID extends ElectronicAddressIdentifier
{
    protected const XML_NODE = 'cbc:EndpointID';

    public function __construct(string $value, ElectronicAddressScheme $scheme)
    {
        parent::__construct($value, $scheme);
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE, $this->value);

        $currentNode->setAttribute('schemeID', $this->scheme->value);

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): self
    {
        $endpointIDElements = $xpath->query(sprintf('./%s', self::XML_NODE), $currentElement);

        if (!$endpointIDElements
            || !$endpointIDElements->item(0)
            || 0 === $endpointIDElements->count()) {
            throw new \Exception('No EndpointID found');
        }

        /** @var \DOMElement $endpointElement */
        $endpointElement = $endpointIDElements->item(0);
        $value           = (string) $endpointElement->nodeValue;
        $scheme          = '' !== $endpointElement->getAttribute('schemeID') ?
            ElectronicAddressScheme::tryFrom($endpointElement->getAttribute('schemeID')) : null;

        if (!$scheme) {
            throw new \Exception('SchemeID invalid or not found');
        }

        return new self($value, $scheme);
    }
}
