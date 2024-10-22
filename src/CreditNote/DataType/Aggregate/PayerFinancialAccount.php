<?php

namespace Tiime\UniversalBusinessLanguage\CreditNote\DataType\Aggregate;

use Tiime\EN16931\DataType\Identifier\DebitedAccountIdentifier;

/**
 * BG-19.
 */
class PayerFinancialAccount
{
    protected const XML_NODE = 'cac:PayerFinancialAccount';

    /**
     * BT-91.
     */
    private DebitedAccountIdentifier $identifier;

    public function __construct(DebitedAccountIdentifier $identifier)
    {
        $this->identifier = $identifier;
    }

    public function getIdentifier(): DebitedAccountIdentifier
    {
        return $this->identifier;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);

        $currentNode->appendChild($document->createElement('cbc:ID', $this->identifier->value));

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): ?self
    {
        $paymentMandateElements = $xpath->query(\sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $paymentMandateElements->count()) {
            return null;
        }

        if (1 !== $paymentMandateElements->count()) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $paymentMandateElement */
        $paymentMandateElement = $paymentMandateElements->item(0);

        $identifierElements = $xpath->query('./cbc:ID', $paymentMandateElement);

        if (1 !== $identifierElements->count()) {
            throw new \Exception('Malformed');
        }

        $identifier = new DebitedAccountIdentifier((string) $identifierElements->item(0)->nodeValue);

        return new self($identifier);
    }
}
