<?php

namespace Tiime\UniversalBusinessLanguage\Ubl21Standard\CreditNote\DataType\Aggregate;

use Tiime\EN16931\DataType\Identifier\PaymentServiceProviderIdentifier;

class FinancialInstitutionBranch
{
    protected const XML_NODE = 'cac:FinancialInstitutionBranch';

    /**
     * BT-86.
     */
    private PaymentServiceProviderIdentifier $identifier;

    public function __construct(PaymentServiceProviderIdentifier $identifier)
    {
        $this->identifier = $identifier;
    }

    public function getIdentifier(): PaymentServiceProviderIdentifier
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
        $financialInstitutionElements = $xpath->query(\sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $financialInstitutionElements->count()) {
            return null;
        }

        if (1 !== $financialInstitutionElements->count()) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $financialInstitutionElement */
        $financialInstitutionElement = $financialInstitutionElements->item(0);

        $identifierElements = $xpath->query('./cbc:ID', $financialInstitutionElement);

        if (1 !== $identifierElements->count()) {
            throw new \Exception('Malformed');
        }

        $identifier = new PaymentServiceProviderIdentifier((string) $identifierElements->item(0)->nodeValue);

        return new self($identifier);
    }
}
