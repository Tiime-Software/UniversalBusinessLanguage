<?php

declare(strict_types=1);

namespace Tiime\UniversalBusinessLanguage\Ubl21Standard\Invoice\DataType\Aggregate;

use Tiime\UniversalBusinessLanguage\Ubl21Standard\Invoice\DataType\Basic\AllowanceTotalAmount;
use Tiime\UniversalBusinessLanguage\Ubl21Standard\Invoice\DataType\Basic\ChargeTotalAmount;
use Tiime\UniversalBusinessLanguage\Ubl21Standard\Invoice\DataType\Basic\LineExtensionAmount;
use Tiime\UniversalBusinessLanguage\Ubl21Standard\Invoice\DataType\Basic\PayableAmount;
use Tiime\UniversalBusinessLanguage\Ubl21Standard\Invoice\DataType\Basic\PayableRoundingAmount;
use Tiime\UniversalBusinessLanguage\Ubl21Standard\Invoice\DataType\Basic\PrepaidAmount;
use Tiime\UniversalBusinessLanguage\Ubl21Standard\Invoice\DataType\Basic\TaxExclusiveAmount;
use Tiime\UniversalBusinessLanguage\Ubl21Standard\Invoice\DataType\Basic\TaxInclusiveAmount;

/**
 * BG-22.
 */
class LegalMonetaryTotal
{
    protected const XML_NODE = 'cac:LegalMonetaryTotal';

    /**
     * BT-106.
     */
    private LineExtensionAmount $lineExtensionAmount;

    /**
     * BT-109.
     */
    private TaxExclusiveAmount $taxExclusiveAmount;

    /**
     * BT-112.
     */
    private TaxInclusiveAmount $taxInclusiveAmount;

    /**
     * BT-107.
     */
    private ?AllowanceTotalAmount $allowanceTotalAmount;

    /**
     * BT-108.
     */
    private ?ChargeTotalAmount $chargeTotalAmount;

    /**
     * BT-113.
     */
    private ?PrepaidAmount $prepaidAmount;

    /**
     * BT-114.
     */
    private ?PayableRoundingAmount $payableRoundingAmount;

    /**
     * BT-115.
     */
    private PayableAmount $payableAmount;

    public function __construct(
        LineExtensionAmount $lineExtensionAmount,
        TaxExclusiveAmount $taxExclusiveAmount,
        TaxInclusiveAmount $taxInclusiveAmount,
        PayableAmount $payableAmount,
    ) {
        $this->lineExtensionAmount   = $lineExtensionAmount;
        $this->taxExclusiveAmount    = $taxExclusiveAmount;
        $this->taxInclusiveAmount    = $taxInclusiveAmount;
        $this->allowanceTotalAmount  = null;
        $this->chargeTotalAmount     = null;
        $this->prepaidAmount         = null;
        $this->payableRoundingAmount = null;
        $this->payableAmount         = $payableAmount;
    }

    public function getLineExtensionAmount(): LineExtensionAmount
    {
        return $this->lineExtensionAmount;
    }

    public function getTaxExclusiveAmount(): TaxExclusiveAmount
    {
        return $this->taxExclusiveAmount;
    }

    public function getTaxInclusiveAmount(): TaxInclusiveAmount
    {
        return $this->taxInclusiveAmount;
    }

    public function getAllowanceTotalAmount(): ?AllowanceTotalAmount
    {
        return $this->allowanceTotalAmount;
    }

    public function setAllowanceTotalAmount(?AllowanceTotalAmount $allowanceTotalAmount): static
    {
        $this->allowanceTotalAmount = $allowanceTotalAmount;

        return $this;
    }

    public function getChargeTotalAmount(): ?ChargeTotalAmount
    {
        return $this->chargeTotalAmount;
    }

    public function setChargeTotalAmount(?ChargeTotalAmount $chargeTotalAmount): static
    {
        $this->chargeTotalAmount = $chargeTotalAmount;

        return $this;
    }

    public function getPrepaidAmount(): ?PrepaidAmount
    {
        return $this->prepaidAmount;
    }

    public function setPrepaidAmount(?PrepaidAmount $prepaidAmount): static
    {
        $this->prepaidAmount = $prepaidAmount;

        return $this;
    }

    public function getPayableRoundingAmount(): ?PayableRoundingAmount
    {
        return $this->payableRoundingAmount;
    }

    public function setPayableRoundingAmount(?PayableRoundingAmount $payableRoundingAmount): static
    {
        $this->payableRoundingAmount = $payableRoundingAmount;

        return $this;
    }

    public function getPayableAmount(): PayableAmount
    {
        return $this->payableAmount;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);

        $currentNode->appendChild($this->lineExtensionAmount->toXML($document));
        $currentNode->appendChild($this->taxExclusiveAmount->toXML($document));
        $currentNode->appendChild($this->taxInclusiveAmount->toXML($document));

        if ($this->allowanceTotalAmount instanceof AllowanceTotalAmount) {
            $currentNode->appendChild($this->allowanceTotalAmount->toXML($document));
        }

        if ($this->chargeTotalAmount instanceof ChargeTotalAmount) {
            $currentNode->appendChild($this->chargeTotalAmount->toXML($document));
        }

        if ($this->prepaidAmount instanceof PrepaidAmount) {
            $currentNode->appendChild($this->prepaidAmount->toXML($document));
        }

        if ($this->payableRoundingAmount instanceof PayableRoundingAmount) {
            $currentNode->appendChild($this->payableRoundingAmount->toXML($document));
        }

        $currentNode->appendChild($this->payableAmount->toXML($document));

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): self
    {
        $legalMonetaryTotalElements = $xpath->query(\sprintf('./%s', self::XML_NODE), $currentElement);

        if (1 !== $legalMonetaryTotalElements->count()) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $legalMonetaryTotalElement */
        $legalMonetaryTotalElement = $legalMonetaryTotalElements->item(0);

        $lineExtensionAmount   = LineExtensionAmount::fromXML($xpath, $legalMonetaryTotalElement);
        $taxExclusiveAmount    = TaxExclusiveAmount::fromXML($xpath, $legalMonetaryTotalElement);
        $taxInclusiveAmount    = TaxInclusiveAmount::fromXML($xpath, $legalMonetaryTotalElement);
        $allowanceTotalAmount  = AllowanceTotalAmount::fromXML($xpath, $legalMonetaryTotalElement);
        $chargeTotalAmount     = ChargeTotalAmount::fromXML($xpath, $legalMonetaryTotalElement);
        $prepaidAmount         = PrepaidAmount::fromXML($xpath, $legalMonetaryTotalElement);
        $payableRoundingAmount = PayableRoundingAmount::fromXML($xpath, $legalMonetaryTotalElement);
        $payableAmount         = PayableAmount::fromXML($xpath, $legalMonetaryTotalElement);

        $legalMonetaryTotal = new self($lineExtensionAmount, $taxExclusiveAmount, $taxInclusiveAmount, $payableAmount);

        if ($allowanceTotalAmount instanceof AllowanceTotalAmount) {
            $legalMonetaryTotal->setAllowanceTotalAmount($allowanceTotalAmount);
        }

        if ($chargeTotalAmount instanceof ChargeTotalAmount) {
            $legalMonetaryTotal->setChargeTotalAmount($chargeTotalAmount);
        }

        if ($prepaidAmount instanceof PrepaidAmount) {
            $legalMonetaryTotal->setPrepaidAmount($prepaidAmount);
        }

        if ($payableRoundingAmount instanceof PayableRoundingAmount) {
            $legalMonetaryTotal->setPayableRoundingAmount($payableRoundingAmount);
        }

        return $legalMonetaryTotal;
    }
}
