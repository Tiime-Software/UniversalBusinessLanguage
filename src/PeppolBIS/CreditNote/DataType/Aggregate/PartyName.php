<?php

namespace Tiime\UniversalBusinessLanguage\PeppolBIS\CreditNote\DataType\Aggregate;

class PartyName
{
    protected const XML_NODE = 'cac:PartyName';

    /**
     * BT-28. or BT-45.
     */
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);

        $currentNode->appendChild($document->createElement('cbc:Name', $this->name));

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): ?self
    {
        $partyNameElements = $xpath->query(\sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $partyNameElements->count()) {
            return null;
        }

        if ($partyNameElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $partyNameElement */
        $partyNameElement = $partyNameElements->item(0);

        $nameElements = $xpath->query('./cbc:Name', $partyNameElement);

        if (1 !== $nameElements->count()) {
            throw new \Exception('Malformed');
        }

        $name = (string) $nameElements->item(0)->nodeValue;

        return new self($name);
    }
}
