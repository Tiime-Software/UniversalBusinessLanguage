<?php

declare(strict_types=1);

namespace Tiime\UniversalBusinessLanguage\Ubl21\Invoice\DataType\Aggregate;

use Tiime\UniversalBusinessLanguage\Ubl21\Invoice\DataType\Basic\AllowanceTotalAmount;
use Tiime\UniversalBusinessLanguage\Ubl21\Invoice\DataType\Basic\ChargeTotalAmount;
use Tiime\UniversalBusinessLanguage\Ubl21\Invoice\DataType\Basic\PayableRoundingAmount;
use Tiime\UniversalBusinessLanguage\Ubl21\Invoice\DataType\Basic\PrepaidAmount;
use Tiime\UniversalBusinessLanguage\Ubl21\Invoice\DataType\Basic\TaxExclusiveAmount;

/**
 * BG-22.
 */
class LegalMonetaryTotal
{
    protected const XML_NODE = 'cac:LegalMonetaryTotal';

    /**
     * BT-109.
     */
    private TaxExclusiveAmount $taxExclusiveAmount;

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

    public function __construct(
        TaxExclusiveAmount $taxExclusiveAmount,
    ) {
        $this->taxExclusiveAmount    = $taxExclusiveAmount;
        $this->allowanceTotalAmount  = null;
        $this->chargeTotalAmount     = null;
        $this->prepaidAmount         = null;
        $this->payableRoundingAmount = null;
    }

    public function getTaxExclusiveAmount(): TaxExclusiveAmount
    {
        return $this->taxExclusiveAmount;
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

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);

        $currentNode->appendChild($this->taxExclusiveAmount->toXML($document));

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

        $taxExclusiveAmount    = TaxExclusiveAmount::fromXML($xpath, $legalMonetaryTotalElement);
        $allowanceTotalAmount  = AllowanceTotalAmount::fromXML($xpath, $legalMonetaryTotalElement);
        $chargeTotalAmount     = ChargeTotalAmount::fromXML($xpath, $legalMonetaryTotalElement);
        $prepaidAmount         = PrepaidAmount::fromXML($xpath, $legalMonetaryTotalElement);
        $payableRoundingAmount = PayableRoundingAmount::fromXML($xpath, $legalMonetaryTotalElement);

        $legalMonetaryTotal = new self($taxExclusiveAmount);

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
