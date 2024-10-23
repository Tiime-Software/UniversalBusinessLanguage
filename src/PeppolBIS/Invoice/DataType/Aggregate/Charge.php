<?php

declare(strict_types=1);

namespace Tiime\UniversalBusinessLanguage\PeppolBIS\Invoice\DataType\Aggregate;

use Tiime\EN16931\Codelist\ChargeReasonCodeUNTDID7161 as ChargeReasonCode;
use Tiime\EN16931\SemanticDataType\Percentage;
use Tiime\UniversalBusinessLanguage\PeppolBIS\Invoice\DataType\Basic\AllowanceChargeAmount;
use Tiime\UniversalBusinessLanguage\PeppolBIS\Invoice\DataType\Basic\BaseAmount;

/**
 * BG-21.
 */
class Charge
{
    protected const XML_NODE = 'cac:AllowanceCharge';

    private string $chargeIndicator;

    /**
     * BT-105.
     */
    private ?ChargeReasonCode $chargeReasonCode;

    /**
     * BT-104.
     */
    private ?string $chargeReason;

    /**
     * BT-101.
     */
    private ?Percentage $multiplierFactorNumeric;

    /**
     * BT-99-00.
     */
    private AllowanceChargeAmount $amount;

    /**
     * BT-100-00.
     */
    private ?BaseAmount $baseAmount;

    private TaxCategory $taxCategory;

    public function __construct(AllowanceChargeAmount $amount, TaxCategory $taxCategory)
    {
        $this->chargeIndicator         = 'true';
        $this->amount                  = $amount;
        $this->taxCategory             = $taxCategory;
        $this->chargeReasonCode        = null;
        $this->chargeReason            = null;
        $this->multiplierFactorNumeric = null;
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

    public function getTaxCategory(): TaxCategory
    {
        return $this->taxCategory;
    }

    public function getChargeReasonCode(): ?ChargeReasonCode
    {
        return $this->chargeReasonCode;
    }

    public function setChargeReasonCode(?ChargeReasonCode $chargeReasonCode): static
    {
        $this->chargeReasonCode = $chargeReasonCode;

        return $this;
    }

    public function getChargeReason(): ?string
    {
        return $this->chargeReason;
    }

    public function setChargeReason(?string $chargeReason): static
    {
        $this->chargeReason = $chargeReason;

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

        $currentNode->appendChild($document->createElement('cbc:ChargeIndicator', 'true'));

        if ($this->chargeReasonCode instanceof ChargeReasonCode) {
            $currentNode->appendChild(
                $document->createElement(
                    'cbc:AllowanceChargeReasonCode', $this->chargeReasonCode->value
                )
            );
        }

        if (\is_string($this->chargeReason)) {
            $currentNode->appendChild(
                $document->createElement(
                    'cbc:AllowanceChargeReason', $this->chargeReason
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

        $currentNode->appendChild($this->taxCategory->toXML($document));

        return $currentNode;
    }

    /**
     * @return array<int, Charge>
     */
    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): array
    {
        $chargeElements = $xpath->query(\sprintf('./%s[cbc:ChargeIndicator[text() = \'true\']]', self::XML_NODE), $currentElement);

        if (0 === $chargeElements->count()) {
            return [];
        }

        $charges = [];

        /** @var \DOMElement $chargeElement */
        foreach ($chargeElements as $chargeElement) {
            $chargeReasonCodeElements = $xpath->query('./cbc:AllowanceChargeReasonCode', $chargeElement);

            if ($chargeReasonCodeElements->count() > 1) {
                throw new \Exception('Malformed');
            }

            $chargeReasonElements = $xpath->query('./cbc:AllowanceChargeReason', $chargeElement);

            if ($chargeReasonElements->count() > 1) {
                throw new \Exception('Malformed');
            }

            $multiplierFactorNumericElements = $xpath->query('./cbc:MultiplierFactorNumeric', $chargeElement);

            if ($multiplierFactorNumericElements->count() > 1) {
                throw new \Exception('Malformed');
            }

            $amount      = AllowanceChargeAmount::fromXML($xpath, $chargeElement);
            $baseAmount  = BaseAmount::fromXML($xpath, $chargeElement);
            $taxCategory = TaxCategory::fromXML($xpath, $chargeElement);

            $charge = new self($amount, $taxCategory);

            if (1 === $chargeReasonCodeElements->count()) {
                $chargeReasonCode = ChargeReasonCode::tryFrom(
                    (string) $chargeReasonCodeElements->item(0)->nodeValue
                );

                if (null === $chargeReasonCode) {
                    throw new \Exception('Wrong charge reason code');
                }
                $charge->setChargeReasonCode($chargeReasonCode);
            }

            if (1 === $chargeReasonElements->count()) {
                $chargeReason = (string) $chargeReasonElements->item(0)->nodeValue;
                $charge->setChargeReason($chargeReason);
            }

            if (1 === $multiplierFactorNumericElements->count()) {
                $charge->setMultiplierFactorNumeric(
                    (float) $multiplierFactorNumericElements->item(0)->nodeValue
                );
            }

            if ($baseAmount instanceof BaseAmount) {
                $charge->setBaseAmount($baseAmount);
            }

            $charges[] = $charge;
        }

        return $charges;
    }
}
