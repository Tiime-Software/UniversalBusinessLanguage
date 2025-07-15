<?php

namespace Tiime\UniversalBusinessLanguage\Ubl21Standard\Invoice\DataType\Aggregate;

class PaymentTerms
{
    protected const XML_NODE = 'cac:PaymentTerms';

    /**
     * BT-20.
     */
    private string $note;

    public function __construct(string $note)
    {
        $this->note = $note;
    }

    public function getNote(): string
    {
        return $this->note;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);

        $currentNode->appendChild($document->createElement('cbc:Note', $this->note));

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): ?self
    {
        $paymentTermsElements = $xpath->query(\sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $paymentTermsElements->count()) {
            return null;
        }

        if (1 !== $paymentTermsElements->count()) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $paymentTermsElement */
        $paymentTermsElement = $paymentTermsElements->item(0);

        $noteElements = $xpath->query('./cbc:Note', $paymentTermsElement);

        if (1 !== $noteElements->count()) {
            throw new \Exception('Malformed');
        }

        return new self((string) $noteElements->item(0)->nodeValue);
    }
}
