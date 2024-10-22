<?php

namespace Tiime\UniversalBusinessLanguage\PeppolBIS\CreditNote\DataType\Aggregate;

use Tiime\EN16931\DataType\Identifier\PaymentAccountIdentifier;

class PayeeFinancialAccount
{
    protected const XML_NODE = 'cac:PayeeFinancialAccount';

    /**
     * BT-84.
     */
    private PaymentAccountIdentifier $paymentAccountIdentifier;

    /**
     * BT-85.
     */
    private ?string $paymentAccountName;

    private ?FinancialInstitutionBranch $financialInstitutionBranch;

    public function __construct(PaymentAccountIdentifier $paymentAccountIdentifier)
    {
        $this->paymentAccountIdentifier   = $paymentAccountIdentifier;
        $this->paymentAccountName         = null;
        $this->financialInstitutionBranch = null;
    }

    public function getPaymentAccountIdentifier(): PaymentAccountIdentifier
    {
        return $this->paymentAccountIdentifier;
    }

    public function getPaymentAccountName(): ?string
    {
        return $this->paymentAccountName;
    }

    public function setPaymentAccountName(?string $paymentAccountName): static
    {
        $this->paymentAccountName = $paymentAccountName;

        return $this;
    }

    public function getFinancialInstitutionBranch(): ?FinancialInstitutionBranch
    {
        return $this->financialInstitutionBranch;
    }

    public function setFinancialInstitutionBranch(?FinancialInstitutionBranch $financialInstitutionBranch): static
    {
        $this->financialInstitutionBranch = $financialInstitutionBranch;

        return $this;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);

        $currentNode->appendChild($document->createElement('cbc:ID', $this->paymentAccountIdentifier->value));

        if (\is_string($this->paymentAccountName)) {
            $currentNode->appendChild($document->createElement('cbc:Name', $this->paymentAccountName));
        }

        if ($this->financialInstitutionBranch instanceof FinancialInstitutionBranch) {
            $currentNode->appendChild($this->financialInstitutionBranch->toXML($document));
        }

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): ?self
    {
        $payeeFinancialAccountElements = $xpath->query(\sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $payeeFinancialAccountElements->count()) {
            return null;
        }

        if (1 !== $payeeFinancialAccountElements->count()) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $payeeFinancialAccountElement */
        $payeeFinancialAccountElement = $payeeFinancialAccountElements->item(0);

        $paymentAccountIdentifierElements = $xpath->query('./cbc:ID', $payeeFinancialAccountElement);

        if (1 !== $paymentAccountIdentifierElements->count()) {
            throw new \Exception('Malformed');
        }

        $paymentAccountIdentifier = new PaymentAccountIdentifier(
            (string) $paymentAccountIdentifierElements->item(0)->nodeValue
        );

        $payeeFinancialAccount = new self($paymentAccountIdentifier);

        $paymentAccountNameElements = $xpath->query('./cbc:Name', $payeeFinancialAccountElement);

        if ($paymentAccountNameElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        if (1 === $paymentAccountNameElements->count()) {
            $payeeFinancialAccount->setPaymentAccountName((string) $paymentAccountNameElements->item(0)->nodeValue);
        }

        $financialInstitutionBranch = FinancialInstitutionBranch::fromXML($xpath, $payeeFinancialAccountElement);

        if ($financialInstitutionBranch instanceof FinancialInstitutionBranch) {
            $payeeFinancialAccount->setFinancialInstitutionBranch($financialInstitutionBranch);
        }

        return $payeeFinancialAccount;
    }
}
