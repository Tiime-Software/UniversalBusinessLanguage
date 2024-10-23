<?php

namespace Tiime\UniversalBusinessLanguage\PeppolBIS\Invoice\DataType\Aggregate;

class ExternalReference
{
    protected const XML_NODE = 'cac:ExternalReference';

    /**
     * BT-124.
     */
    private string $uri;

    public function __construct(string $uri)
    {
        $this->uri = $uri;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);

        $currentNode->appendChild($document->createElement('cbc:URI', $this->uri));

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): ?self
    {
        $externalReferenceElements = $xpath->query(\sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $externalReferenceElements->count()) {
            return null;
        }

        if ($externalReferenceElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $externalReferenceElement */
        $externalReferenceElement = $externalReferenceElements->item(0);

        $uriElements = $xpath->query('./cbc:URI', $externalReferenceElement);

        if (1 !== $uriElements->count()) {
            throw new \Exception('Malformed');
        }

        $uri = (string) $uriElements->item(0)->nodeValue;

        return new self($uri);
    }
}
