<?php

declare(strict_types=1);

namespace Tiime\UniversalBusinessLanguage\DataType\Aggregate;

use Tiime\EN16931\DataType\AllowanceReasonCode;
use Tiime\EN16931\SemanticDataType\Percentage;
use Tiime\UniversalBusinessLanguage\DataType\Basic\AllowanceAmount;
use Tiime\UniversalBusinessLanguage\DataType\Basic\BaseAmount;

/**
 * BG-27. or BG-28.
 */
class InvoiceLineAllowanceCharge
{
    protected const XML_NODE = 'cac:AllowanceCharge';

    private string $chargeIndicator;

    /**
     * BT-140. or BT-145.
     */
    private ?AllowanceReasonCode $allowanceChargeReasonCode;

    /**
     * BT-19 or BT-144.
     */
    private ?string $allowanceChargeReason;

    /**
     * BT-138. or BT-143.
     */
    private ?Percentage $multiplierFactorNumeric;

    /**
     * BT-136. or BT-141.
     */
    private AllowanceAmount $amount;

    /**
     * BT-137. or BT-142.
     */
    private ?BaseAmount $baseAmount;

    public function __construct(AllowanceAmount $amount)
    {
        $this->chargeIndicator           = 'false';
        $this->allowanceChargeReasonCode = null;
        $this->allowanceChargeReason     = null;
        $this->multiplierFactorNumeric   = null;
        $this->amount                    = $amount;
        $this->baseAmount                = null;
    }

    public function getChargeIndicator(): string
    {
        return $this->chargeIndicator;
    }

    public function getAmount(): AllowanceAmount
    {
        return $this->amount;
    }

    public function getAllowanceChargeReasonCode(): ?AllowanceReasonCode
    {
        return $this->allowanceChargeReasonCode;
    }

    public function setAllowanceChargeReasonCode(?AllowanceReasonCode $allowanceChargeReasonCode): void
    {
        $this->allowanceChargeReasonCode = $allowanceChargeReasonCode;
    }

    public function getAllowanceChargeReason(): ?string
    {
        return $this->allowanceChargeReason;
    }

    public function setAllowanceChargeReason(?string $allowanceChargeReason): static
    {
        $this->allowanceChargeReason = $allowanceChargeReason;

        return $this;
    }

    public function getMultiplierFactorNumeric(): ?float
    {
        return $this->multiplierFactorNumeric?->getValueRounded();
    }

    public function setMultiplierFactorNumeric(?float $multiplierFactorNumeric): static
    {
        $this->multiplierFactorNumeric = \is_float($multiplierFactorNumeric) ? new Percentage($multiplierFactorNumeric) : null;

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

        if ($this->allowanceChargeReasonCode instanceof AllowanceReasonCode) {
            $currentNode->appendChild(
                $document->createElement(
                    'cbc:AllowanceChargeReasonCode', $this->allowanceChargeReasonCode->value
                )
            );
        }

        if (\is_string($this->allowanceChargeReason)) {
            $currentNode->appendChild(
                $document->createElement(
                    'cbc:AllowanceChargeReason', $this->allowanceChargeReason
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
     * @return array<int,InvoiceLineAllowanceCharge>
     */
    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): array
    {
        $allowanceChargeElements = $xpath->query(sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $allowanceChargeElements->count()) {
            return [];
        }

        $allowanceCharges = [];

        /** @var \DOMElement $allowanceChargeElement */
        foreach ($allowanceChargeElements as $allowanceChargeElement) {
            $chargeIndicatorElements = $xpath->query('cbc:ChargeIndicator', $allowanceChargeElement);

            if (1 !== $chargeIndicatorElements->count()) {
                throw new \Exception('Malformed');
            }

            $chargeIndicator = (string) $chargeIndicatorElements->item(0)->nodeValue;

            if ('false' !== $chargeIndicator) {
                throw new \Exception('Malformed');
            }

            $allowanceChargeReasonCodeElements = $xpath->query('cbc:AllowanceChargeReasonCode', $allowanceChargeElement);

            if ($allowanceChargeReasonCodeElements->count() > 1) {
                throw new \Exception('Malformed');
            }

            $allowanceChargeReasonElements = $xpath->query('cbc:AllowanceChargeReason', $allowanceChargeElement);

            if ($allowanceChargeReasonElements->count() > 1) {
                throw new \Exception('Malformed');
            }

            $multiplierFactorNumericElements = $xpath->query('cbc:MultiplierFactorNumeric', $allowanceChargeElement);

            if ($multiplierFactorNumericElements->count() > 1) {
                throw new \Exception('Malformed');
            }

            $amount     = AllowanceAmount::fromXML($xpath, $allowanceChargeElement);
            $baseAmount = BaseAmount::fromXML($xpath, $allowanceChargeElement);

            $allowanceCharge = new self($amount);

            if (1 === $allowanceChargeReasonCodeElements->count()) {
                $allowanceChargeReasonCode = AllowanceReasonCode::tryFrom(
                    (string) $allowanceChargeReasonCodeElements->item(0)->nodeValue
                );

                if (null === $allowanceChargeReasonCode) {
                    throw new \Exception('Wrong charge reason code');
                }
                $allowanceCharge->setAllowanceChargeReasonCode($allowanceChargeReasonCode);
            }

            if (1 === $allowanceChargeReasonElements->count()) {
                $allowanceChargeReason = (string) $allowanceChargeReasonElements->item(0)->nodeValue;
                $allowanceCharge->setAllowanceChargeReason($allowanceChargeReason);
            }

            if (1 === $multiplierFactorNumericElements->count()) {
                $allowanceCharge->setMultiplierFactorNumeric(
                    (float) $multiplierFactorNumericElements->item(0)->nodeValue
                );
            }

            if ($baseAmount instanceof BaseAmount) {
                $allowanceCharge->setBaseAmount($baseAmount);
            }

            $allowanceCharges[] = $allowanceCharge;
        }

        return $allowanceCharges;
    }
}
