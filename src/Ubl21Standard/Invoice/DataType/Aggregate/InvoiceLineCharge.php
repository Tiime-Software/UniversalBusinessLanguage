<?php

declare(strict_types=1);

namespace Tiime\UniversalBusinessLanguage\Ubl21Standard\Invoice\DataType\Aggregate;

use Tiime\EN16931\Codelist\ChargeReasonCodeUNTDID7161 as ChargeReasonCode;
use Tiime\EN16931\SemanticDataType\Percentage;
use Tiime\UniversalBusinessLanguage\Ubl21Standard\Invoice\DataType\Basic\AllowanceChargeAmount;
use Tiime\UniversalBusinessLanguage\Ubl21Standard\Invoice\DataType\Basic\BaseAmount;

/**
 * BG-28.
 */
class InvoiceLineCharge
{
    protected const XML_NODE = 'cac:AllowanceCharge';

    private string $chargeIndicator;

    /**
     * BT-145.
     */
    private ?ChargeReasonCode $chargeReasonCode;

    /**
     * BT-144.
     */
    private ?string $chargeReason;

    /**
     * BT-143.
     */
    private ?Percentage $multiplierFactorNumeric;

    /**
     * BT-141.
     */
    private AllowanceChargeAmount $amount;

    /**
     * BT-142.
     */
    private ?BaseAmount $baseAmount;

    public function __construct(AllowanceChargeAmount $amount)
    {
        $this->chargeIndicator         = 'true';
        $this->chargeReasonCode        = null;
        $this->chargeReason            = null;
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

        return $currentNode;
    }

    /**
     * @return array<int,InvoiceLineCharge>
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

            $amount     = AllowanceChargeAmount::fromXML($xpath, $chargeElement);
            $baseAmount = BaseAmount::fromXML($xpath, $chargeElement);

            $charge = new self($amount);

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
