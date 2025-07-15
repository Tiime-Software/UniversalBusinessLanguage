<?php

namespace Tiime\UniversalBusinessLanguage\Ubl21Standard\Invoice\DataType\Aggregate;

class DeliveryParty
{
    protected const XML_NODE = 'cac:DeliveryParty';

    private DeliveryPartyName $partyName;

    public function __construct(DeliveryPartyName $partyName)
    {
        $this->partyName = $partyName;
    }

    public function getPartyName(): DeliveryPartyName
    {
        return $this->partyName;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);

        $currentNode->appendChild($this->partyName->toXML($document));

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): ?self
    {
        $partyElements = $xpath->query(\sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $partyElements->count()) {
            return null;
        }

        if ($partyElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $partyElement */
        $partyElement = $partyElements->item(0);

        $partyName = DeliveryPartyName::fromXML($xpath, $partyElement);

        return new self($partyName);
    }
}
