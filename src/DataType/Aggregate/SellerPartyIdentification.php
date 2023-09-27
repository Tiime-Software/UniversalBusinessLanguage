<?php

namespace Tiime\UniversalBusinessLanguage\DataType\Aggregate;

use Tiime\EN16931\DataType\Identifier\SellerIdentifier;
use Tiime\EN16931\DataType\InternationalCodeDesignator;

/**
 * BT-29.
 */
class SellerPartyIdentification extends SellerIdentifier
{
    protected const XML_NODE = 'cac:PartyIdentification';

    public function __construct(string $value, InternationalCodeDesignator $scheme)
    {
        parent::__construct($value, $scheme);
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE, $this->value);

        if (!$this->scheme) {
            throw new \Exception('No scheme found');
        }

        $currentNode->setAttribute('schemeID', $this->scheme->value);

        return $currentNode;
    }

    /**
     * @return array<int, SellerPartyIdentification>
     */
    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): array
    {
        $partyIdentifications = $xpath->query(sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $partyIdentifications->count()) {
            return [];
        }

        $sellerGlobalIdentifiers = [];

        /** @var \DOMElement $partyIdentification */
        foreach ($partyIdentifications as $partyIdentification) {
            $sellerGlobalIdentifier = (string) $partyIdentification->nodeValue;
            $scheme                 = $partyIdentification->hasAttribute('schemeID') ?
                InternationalCodeDesignator::tryFrom($partyIdentification->getAttribute('schemeID')) : null;

            if (null === $scheme) {
                throw new \Exception('Wrong schemeID');
            }

            $sellerGlobalIdentifiers[] = new self($sellerGlobalIdentifier, $scheme);
        }

        return $sellerGlobalIdentifiers;
    }
}
