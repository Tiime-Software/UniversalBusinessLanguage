<?php

namespace Tiime\UniversalBusinessLanguage\DataType\Aggregate;

use Tiime\EN16931\DataType\Identifier\StandardItemIdentifier;
use Tiime\EN16931\DataType\InternationalCodeDesignator;

class StandardItemIdentification
{
    protected const XML_NODE = 'cac:StandardItemIdentification';

    /**
     * BT-157.
     */
    private StandardItemIdentifier $standardItemIdentifier;

    public function __construct(StandardItemIdentifier $standardItemIdentifier)
    {
        $this->standardItemIdentifier = $standardItemIdentifier;
    }

    public function getStandardItemIdentifier(): StandardItemIdentifier
    {
        return $this->standardItemIdentifier;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);

        $standardItemIdentifier = $document->createElement('cbc:ID', $this->standardItemIdentifier->value);

        if ($this->standardItemIdentifier->scheme instanceof InternationalCodeDesignator) {
            $standardItemIdentifier->setAttribute('schemeID', $this->standardItemIdentifier->scheme->value);
        }

        $currentNode->appendChild($standardItemIdentifier);

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): ?self
    {
        $standardItemIdentificationElements = $xpath->query(sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $standardItemIdentificationElements->count()) {
            return null;
        }

        if ($standardItemIdentificationElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $standardItemIdentificationElement */
        $standardItemIdentificationElement = $standardItemIdentificationElements->item(0);

        $identifierElements = $xpath->query('./cbc:ID', $standardItemIdentificationElement);

        if (1 !== $identifierElements->count()) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $identifierElement */
        $identifierElement = $identifierElements->item(0);
        $value             = (string) $identifierElement->nodeValue;

        $scheme = null;

        if ($identifierElement->hasAttribute('schemeID')) {
            $scheme = InternationalCodeDesignator::tryFrom($identifierElement->getAttribute('schemeID'));

            if (!$scheme instanceof InternationalCodeDesignator) {
                throw new \Exception('Wrong schemeID');
            }
        }

        if (null === $scheme) {
            throw new \Exception('Malformed');
        }

        $identifier = new StandardItemIdentifier($value, $scheme);

        return new self($identifier);
    }
}
