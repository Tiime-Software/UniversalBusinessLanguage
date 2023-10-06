<?php

declare(strict_types=1);

namespace Tiime\UniversalBusinessLanguage\DataType\Aggregate;

use Tiime\EN16931\DataType\AllowanceReasonCode;
use Tiime\EN16931\SemanticDataType\Percentage;
use Tiime\UniversalBusinessLanguage\DataType\Basic\AllowanceAmount;
use Tiime\UniversalBusinessLanguage\DataType\Basic\BaseAmount;

/**
 * BG-20. or BG-21.
 */
class AllowanceCharge
{
    protected const XML_NODE = 'cac:AllowanceCharge';

    private string $chargeIndicator;

    /**
     * BT-98. or BT-105.
     */
    private ?AllowanceReasonCode $allowanceChargeReasonCode;

    /**
     * BT-97 or BT-104.
     */
    private ?string $allowanceChargeReason;

    /**
     * BT-94. or BT-101.
     */
    private ?Percentage $multiplierFactorNumeric;

    /**
     * BT-92-00. or BT-99-00.
     */
    private AllowanceAmount $value;

    /**
     * BT-93-00. or BT-100-00.
     */
    private ?BaseAmount $baseAmount;

    private TaxCategory $taxCategory;

    public function __construct(AllowanceAmount $value, TaxCategory $taxCategory)
    {
        $this->chargeIndicator           = 'false';
        $this->value                     = $value;
        $this->taxCategory               = $taxCategory;
        $this->allowanceChargeReasonCode = null;
        $this->allowanceChargeReason     = null;
        $this->multiplierFactorNumeric   = null;
        $this->baseAmount                = null;
    }

    public function getChargeIndicator(): string
    {
        return $this->chargeIndicator;
    }

    public function getValue(): AllowanceAmount
    {
        return $this->value;
    }

    public function getTaxCategory(): TaxCategory
    {
        return $this->taxCategory;
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

        $currentNode->appendChild($this->value->toXML($document));

        if ($this->baseAmount instanceof BaseAmount) {
            $currentNode->appendChild($this->baseAmount->toXML($document));
        }

        $currentNode->appendChild($this->taxCategory->toXML($document));

        return $currentNode;
    }

    /**
     * @return array<int, AllowanceCharge>
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

            $value       = AllowanceAmount::fromXML($xpath, $allowanceChargeElement);
            $baseAmount  = BaseAmount::fromXML($xpath, $allowanceChargeElement);
            $taxCategory = TaxCategory::fromXML($xpath, $allowanceChargeElement);

            $allowanceCharge = new self($value, $taxCategory);

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
