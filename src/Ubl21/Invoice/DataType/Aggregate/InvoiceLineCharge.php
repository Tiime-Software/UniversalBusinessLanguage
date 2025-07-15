<?php

declare(strict_types=1);

namespace Tiime\UniversalBusinessLanguage\Ubl21\Invoice\DataType\Aggregate;

use Tiime\UniversalBusinessLanguage\Ubl21\Invoice\DataType\Basic\AllowanceChargeAmount;
use Tiime\UniversalBusinessLanguage\Ubl21\Invoice\DataType\Basic\BaseAmount;

/**
 * BG-28.
 */
class InvoiceLineCharge
{
    protected const XML_NODE = 'cac:AllowanceCharge';

    private string $chargeIndicator;

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
        $this->chargeIndicator = 'true';
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
        $currentNode->appendChild($document->createElement('cbc:ChargeIndicator', 'true'));
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
            $amount     = AllowanceChargeAmount::fromXML($xpath, $chargeElement);
            $baseAmount = BaseAmount::fromXML($xpath, $chargeElement);

            $charge = new self($amount);

            if ($baseAmount instanceof BaseAmount) {
                $charge->setBaseAmount($baseAmount);
            }
            $charges[] = $charge;
        }

        return $charges;
    }
}
