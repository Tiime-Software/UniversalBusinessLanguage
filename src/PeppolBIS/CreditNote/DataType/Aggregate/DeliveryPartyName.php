<?php

namespace Tiime\UniversalBusinessLanguage\PeppolBIS\CreditNote\DataType\Aggregate;

class DeliveryPartyName
{
    protected const XML_NODE = 'cac:PartyName';

    /**
     * BT-70.
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

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): self
    {
        $payeePartyNameElements = $xpath->query(\sprintf('./%s', self::XML_NODE), $currentElement);

        if (1 !== $payeePartyNameElements->count()) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $payeePartyNameElement */
        $payeePartyNameElement = $payeePartyNameElements->item(0);

        $nameElements = $xpath->query('./cbc:Name', $payeePartyNameElement);

        if (1 !== $nameElements->count()) {
            throw new \Exception('Malformed');
        }

        $name = (string) $nameElements->item(0)->nodeValue;

        return new self($name);
    }
}
