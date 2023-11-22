<?php

namespace Tiime\UniversalBusinessLanguage\CreditNote\DataType\Aggregate;

use Tiime\EN16931\DataType\Identifier\MandateReferenceIdentifier;

/**
 * BG-19.
 */
class PaymentMandate
{
    protected const XML_NODE = 'cac:PaymentMandate';

    /**
     * BT-89.
     */
    private ?MandateReferenceIdentifier $identifier;

    private ?PayerFinancialAccount $payerFinancialAccount;

    public function __construct()
    {
        $this->identifier            = null;
        $this->payerFinancialAccount = null;
    }

    public function getIdentifier(): ?MandateReferenceIdentifier
    {
        return $this->identifier;
    }

    public function setIdentifier(?MandateReferenceIdentifier $identifier): static
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getPayerFinancialAccount(): ?PayerFinancialAccount
    {
        return $this->payerFinancialAccount;
    }

    public function setPayerFinancialAccount(?PayerFinancialAccount $payerFinancialAccount): static
    {
        $this->payerFinancialAccount = $payerFinancialAccount;

        return $this;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);

        if ($this->identifier instanceof MandateReferenceIdentifier) {
            $currentNode->appendChild($document->createElement('cbc:ID', $this->identifier->value));
        }

        if ($this->payerFinancialAccount instanceof PayerFinancialAccount) {
            $currentNode->appendChild($this->payerFinancialAccount->toXML($document));
        }

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): ?self
    {
        $paymentMandateElements = $xpath->query(sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $paymentMandateElements->count()) {
            return null;
        }

        if (1 !== $paymentMandateElements->count()) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $paymentMandateElement */
        $paymentMandateElement = $paymentMandateElements->item(0);

        $identifierElements = $xpath->query('./cbc:ID', $paymentMandateElement);

        if ($identifierElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        $paymentMandate = new self();

        if (1 === $identifierElements->count()) {
            $paymentMandate->setIdentifier(
                new MandateReferenceIdentifier((string) $identifierElements->item(0)->nodeValue)
            );
        }

        $payerFinancialAccount = PayerFinancialAccount::fromXML($xpath, $paymentMandateElement);

        if ($payerFinancialAccount instanceof PayerFinancialAccount) {
            $paymentMandate->setPayerFinancialAccount($payerFinancialAccount);
        }

        return $paymentMandate;
    }
}
