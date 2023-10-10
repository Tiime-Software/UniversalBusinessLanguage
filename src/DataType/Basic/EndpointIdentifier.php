<?php

namespace Tiime\UniversalBusinessLanguage\DataType\Basic;

use Tiime\EN16931\DataType\ElectronicAddressScheme;
use Tiime\EN16931\DataType\Identifier\ElectronicAddressIdentifier;

/**
 * BT-34. or BT-49.
 */
class EndpointIdentifier extends ElectronicAddressIdentifier
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
        $endpointIdentifierElements = $xpath->query(sprintf('./%s', self::XML_NODE), $currentElement);

        if (1 !== $endpointIdentifierElements->count()) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $endpointIdentifierElement */
        $endpointIdentifierElement = $endpointIdentifierElements->item(0);
        $value                     = (string) $endpointIdentifierElement->nodeValue;
        $scheme                    = $endpointIdentifierElement->hasAttribute('schemeID') ?
            ElectronicAddressScheme::tryFrom($endpointIdentifierElement->getAttribute('schemeID')) : null;

        if (!$scheme instanceof ElectronicAddressScheme) {
            throw new \Exception('Invalid schemeID');
        }

        return new self($value, $scheme);
    }
}
