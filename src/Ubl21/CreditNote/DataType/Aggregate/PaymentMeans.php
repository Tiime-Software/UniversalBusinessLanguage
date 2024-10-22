<?php

namespace Tiime\UniversalBusinessLanguage\Ubl21\CreditNote\DataType\Aggregate;

use Tiime\UniversalBusinessLanguage\Ubl21\CreditNote\DataType\Basic\PaymentDueDate;
use Tiime\UniversalBusinessLanguage\Ubl21\CreditNote\DataType\Basic\PaymentMeansNamedCode;

/**
 * BG-16.
 */
class PaymentMeans
{
    protected const XML_NODE = 'cac:PaymentMeans';

    /**
     * BT-81. & BT-82.
     */
    private PaymentMeansNamedCode $paymentMeansCode;

    /**
     * BT-83.
     */
    private ?string $paymentIdentifier;

    /**+
     * BG-18.
     */
    private ?CardAccount $cardAccount;

    /**
     * BG-17.
     */
    private ?PayeeFinancialAccount $payeeFinancialAccount;

    private ?PaymentMandate $paymentMandate;

    private ?PaymentDueDate $paymentDueDate;

    public function __construct(PaymentMeansNamedCode $paymentMeansCode)
    {
        $this->paymentMeansCode      = $paymentMeansCode;
        $this->paymentIdentifier     = null;
        $this->cardAccount           = null;
        $this->payeeFinancialAccount = null;
        $this->paymentMandate        = null;
        $this->paymentDueDate        = null;
    }

    public function getPaymentMeansCode(): PaymentMeansNamedCode
    {
        return $this->paymentMeansCode;
    }

    public function getPaymentIdentifier(): ?string
    {
        return $this->paymentIdentifier;
    }

    public function setPaymentIdentifier(?string $paymentIdentifier): static
    {
        $this->paymentIdentifier = $paymentIdentifier;

        return $this;
    }

    public function getCardAccount(): ?CardAccount
    {
        return $this->cardAccount;
    }

    public function setCardAccount(?CardAccount $cardAccount): static
    {
        $this->cardAccount = $cardAccount;

        return $this;
    }

    public function getPayeeFinancialAccount(): ?PayeeFinancialAccount
    {
        return $this->payeeFinancialAccount;
    }

    public function setPayeeFinancialAccount(?PayeeFinancialAccount $payeeFinancialAccount): static
    {
        $this->payeeFinancialAccount = $payeeFinancialAccount;

        return $this;
    }

    public function getPaymentMandate(): ?PaymentMandate
    {
        return $this->paymentMandate;
    }

    public function setPaymentMandate(?PaymentMandate $paymentMandate): static
    {
        $this->paymentMandate = $paymentMandate;

        return $this;
    }

    public function getPaymentDueDate(): ?PaymentDueDate
    {
        return $this->paymentDueDate;
    }

    public function setPaymentDueDate(?PaymentDueDate $paymentDueDate): static
    {
        $this->paymentDueDate = $paymentDueDate;

        return $this;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);

        $currentNode->appendChild($this->paymentMeansCode->toXML($document));

        if ($this->paymentDueDate instanceof PaymentDueDate) {
            $currentNode->appendChild($this->paymentDueDate->toXML($document));
        }

        if (\is_string($this->paymentIdentifier)) {
            $currentNode->appendChild($document->createElement('cbc:PaymentID', $this->paymentIdentifier));
        }

        if ($this->cardAccount instanceof CardAccount) {
            $currentNode->appendChild($this->cardAccount->toXML($document));
        }

        if ($this->payeeFinancialAccount instanceof PayeeFinancialAccount) {
            $currentNode->appendChild($this->payeeFinancialAccount->toXML($document));
        }

        if ($this->paymentMandate instanceof PaymentMandate) {
            $currentNode->appendChild($this->paymentMandate->toXML($document));
        }

        return $currentNode;
    }

    /**
     * @return array<int, PaymentMeans>
     */
    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): array
    {
        $paymentMeansElements = $xpath->query(\sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $paymentMeansElements->count()) {
            return [];
        }

        $paymentMeans = [];

        /** @var \DOMElement $paymentMeansElement */
        foreach ($paymentMeansElements as $paymentMeansElement) {
            $paymentMeansCode          = PaymentMeansNamedCode::fromXML($xpath, $paymentMeansElement);
            $paymentIdentifierElements = $xpath->query('./cbc:PaymentID', $paymentMeansElement);
            $cardAccount               = CardAccount::fromXML($xpath, $paymentMeansElement);
            $payeeFinancialAccount     = PayeeFinancialAccount::fromXML($xpath, $paymentMeansElement);
            $paymentMandate            = PaymentMandate::fromXML($xpath, $paymentMeansElement);
            $paymentDueDate            = PaymentDueDate::fromXML($xpath, $paymentMeansElement);

            if ($paymentIdentifierElements->count() > 1) {
                throw new \Exception('Malformed');
            }

            $paymentMean = new self($paymentMeansCode);

            if (1 === $paymentIdentifierElements->count()) {
                $paymentMean->setPaymentIdentifier((string) $paymentIdentifierElements->item(0)->nodeValue);
            }

            if ($cardAccount instanceof CardAccount) {
                $paymentMean->setCardAccount($cardAccount);
            }

            if ($payeeFinancialAccount instanceof PayeeFinancialAccount) {
                $paymentMean->setPayeeFinancialAccount($payeeFinancialAccount);
            }

            if ($paymentMandate instanceof PaymentMandate) {
                $paymentMean->setPaymentMandate($paymentMandate);
            }

            if ($paymentDueDate instanceof PaymentDueDate) {
                $paymentMean->setPaymentDueDate($paymentDueDate);
            }

            $paymentMeans[] = $paymentMean;
        }

        return $paymentMeans;
    }
}
