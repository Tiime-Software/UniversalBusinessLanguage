<?php

declare(strict_types=1);

namespace Tiime\UniversalBusinessLanguage\CreditNote\DataType\Aggregate;

use Tiime\EN16931\Codelist\AllowanceReasonCodeUNTDID5189 as AllowanceReasonCode;
use Tiime\EN16931\SemanticDataType\Percentage;
use Tiime\UniversalBusinessLanguage\CreditNote\DataType\Basic\AllowanceChargeAmount;
use Tiime\UniversalBusinessLanguage\CreditNote\DataType\Basic\BaseAmount;

/**
 * BG-27.
 */
class InvoiceLineAllowance
{
    protected const XML_NODE = 'cac:AllowanceCharge';

    private string $chargeIndicator;

    /**
     * BT-140.
     */
    private ?AllowanceReasonCode $allowanceReasonCode;

    /**
     * BT-19.
     */
    private ?string $allowanceReason;

    /**
     * BT-138.
     */
    private ?Percentage $multiplierFactorNumeric;

    /**
     * BT-136.
     */
    private AllowanceChargeAmount $amount;

    /**
     * BT-137.
     */
    private ?BaseAmount $baseAmount;

    public function __construct(AllowanceChargeAmount $amount)
    {
        $this->chargeIndicator         = 'false';
        $this->allowanceReasonCode     = null;
        $this->allowanceReason         = null;
        $this->multiplierFactorNumeric = null;
        $this->amount                  = $amount;
        $this->baseAmount              = null;
    }

    public function getChargeIndicator(): string
    {
        return $this->chargeIndicator;
    }

    public function getAmount(): AllowanceChargeAmount
    {
        return $this->amount;
    }

    public function getAllowanceReasonCode(): ?AllowanceReasonCode
    {
        return $this->allowanceReasonCode;
    }

    public function setAllowanceReasonCode(?AllowanceReasonCode $allowanceReasonCode): static
    {
        $this->allowanceReasonCode = $allowanceReasonCode;

        return $this;
    }

    public function getAllowanceReason(): ?string
    {
        return $this->allowanceReason;
    }

    public function setAllowanceReason(?string $allowanceReason): static
    {
        $this->allowanceReason = $allowanceReason;

        return $this;
    }

    public function getMultiplierFactorNumeric(): ?Percentage
    {
        return $this->multiplierFactorNumeric;
    }

    public function setMultiplierFactorNumeric(?float $value): static
    {
        $this->multiplierFactorNumeric = \is_float($value) ? new Percentage($value) : null;

        return $this;
    }

    public function getBaseAmount(): ?BaseAmount
    {
        return $this->baseAmount;
    }

    public function setBaseAmount(?BaseAmount $baseAmount): static
    {
        $this->baseAmount = $baseAmount;

        return $this;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);

        $currentNode->appendChild($document->createElement('cbc:ChargeIndicator', 'false'));

        if ($this->allowanceReasonCode instanceof AllowanceReasonCode) {
            $currentNode->appendChild(
                $document->createElement(
                    'cbc:AllowanceChargeReasonCode', $this->allowanceReasonCode->value
                )
            );
        }

        if (\is_string($this->allowanceReason)) {
            $currentNode->appendChild(
                $document->createElement(
                    'cbc:AllowanceChargeReason', $this->allowanceReason
                )
            );
        }

        if ($this->multiplierFactorNumeric instanceof Percentage) {
            $currentNode->appendChild(
                $document->createElement(
                    'cbc:MultiplierFactorNumeric', $this->multiplierFactorNumeric->getFormattedValueRounded()
                )
            );
        }

        $currentNode->appendChild($this->amount->toXML($document));

        if ($this->baseAmount instanceof BaseAmount) {
            $currentNode->appendChild($this->baseAmount->toXML($document));
        }

        return $currentNode;
    }

    /**
     * @return array<int,InvoiceLineAllowance>
     */
    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): array
    {
        $allowanceElements = $xpath->query(\sprintf('./%s[cbc:ChargeIndicator[text() = \'false\']]', self::XML_NODE), $currentElement);

        if (0 === $allowanceElements->count()) {
            return [];
        }

        $allowances = [];

        /** @var \DOMElement $allowanceElement */
        foreach ($allowanceElements as $allowanceElement) {
            $allowanceReasonCodeElements = $xpath->query('./cbc:AllowanceChargeReasonCode', $allowanceElement);

            if ($allowanceReasonCodeElements->count() > 1) {
                throw new \Exception('Malformed');
            }

            $allowanceReasonElements = $xpath->query('./cbc:AllowanceChargeReason', $allowanceElement);

            if ($allowanceReasonElements->count() > 1) {
                throw new \Exception('Malformed');
            }

            $multiplierFactorNumericElements = $xpath->query('./cbc:MultiplierFactorNumeric', $allowanceElement);

            if ($multiplierFactorNumericElements->count() > 1) {
                throw new \Exception('Malformed');
            }

            $amount     = AllowanceChargeAmount::fromXML($xpath, $allowanceElement);
            $baseAmount = BaseAmount::fromXML($xpath, $allowanceElement);

            $allowance = new self($amount);

            if (1 === $allowanceReasonCodeElements->count()) {
                $allowanceReasonCode = AllowanceReasonCode::tryFrom(
                    (string) $allowanceReasonCodeElements->item(0)->nodeValue
                );

                if (null === $allowanceReasonCode) {
                    throw new \Exception('Wrong allowance reason code');
                }

                $allowance->setAllowanceReasonCode($allowanceReasonCode);
            }

            if (1 === $allowanceReasonElements->count()) {
                $allowanceReason = (string) $allowanceReasonElements->item(0)->nodeValue;
                $allowance->setAllowanceReason($allowanceReason);
            }

            if (1 === $multiplierFactorNumericElements->count()) {
                $allowance->setMultiplierFactorNumeric(
                    (float) $multiplierFactorNumericElements->item(0)->nodeValue
                );
            }

            if ($baseAmount instanceof BaseAmount) {
                $allowance->setBaseAmount($baseAmount);
            }

            $allowances[] = $allowance;
        }

        return $allowances;
    }
}
