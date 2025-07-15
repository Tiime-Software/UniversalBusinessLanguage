<?php

declare(strict_types=1);

namespace Tiime\UniversalBusinessLanguage\Ubl21\Invoice\DataType\Aggregate;

use Tiime\UniversalBusinessLanguage\Ubl21\Invoice\DataType\Basic\AllowanceChargeAmount;
use Tiime\UniversalBusinessLanguage\Ubl21\Invoice\DataType\Basic\BaseAmount;

/**
 * BG-27.
 */
class InvoiceLineAllowance
{
    protected const XML_NODE = 'cac:AllowanceCharge';

    private string $chargeIndicator;

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
        $this->chargeIndicator = 'false';
        $this->amount          = $amount;
        $this->baseAmount      = null;
    }

    public function getChargeIndicator(): string
    {
        return $this->chargeIndicator;
    }

    public function getAmount(): AllowanceChargeAmount
    {
        return $this->amount;
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
            $amount     = AllowanceChargeAmount::fromXML($xpath, $allowanceElement);
            $baseAmount = BaseAmount::fromXML($xpath, $allowanceElement);

            $allowance = new self($amount);

            if ($baseAmount instanceof BaseAmount) {
                $allowance->setBaseAmount($baseAmount);
            }
            $allowances[] = $allowance;
        }

        return $allowances;
    }
}
