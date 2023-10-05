<?php

namespace Tiime\UniversalBusinessLanguage\DataType\Aggregate;

/**
 * BG-32.
 */
class AdditionalItemProperty
{
    protected const XML_NODE = 'cac:AdditionalItemProperty';

    /**
     * BT-160.
     */
    private string $name;

    /**
     * BT-161.
     */
    private string $value;

    public function __construct(string $name, string $value)
    {
        $this->name  = $name;
        $this->value = $value;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);

        $currentNode->appendChild($document->createElement('cbc:Name', $this->name));
        $currentNode->appendChild($document->createElement('cbc:Value', $this->value));

        return $currentNode;
    }

    /**
     * @return array<int,AdditionalItemProperty>
     */
    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): array
    {
        $additionalItemPropertyElements = $xpath->query(sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $additionalItemPropertyElements->count()) {
            return [];
        }

        $additionalItemProperties = [];

        /** @var \DOMElement $additionalItemPropertyElement */
        foreach ($additionalItemPropertyElements as $additionalItemPropertyElement) {
            $nameElements = $xpath->query('./cbc:Name', $additionalItemPropertyElement);

            if (1 !== $nameElements->count()) {
                throw new \Exception('Malformed');
            }

            $name = (string) $nameElements->item(0)->nodeValue;

            $valueElements = $xpath->query('./cbc:Value', $additionalItemPropertyElement);

            if (1 !== $valueElements->count()) {
                throw new \Exception('Malformed');
            }

            $value = (string) $valueElements->item(0)->nodeValue;

            $additionalItemProperties[] = new self($name, $value);
        }

        return $additionalItemProperties;
    }
}
