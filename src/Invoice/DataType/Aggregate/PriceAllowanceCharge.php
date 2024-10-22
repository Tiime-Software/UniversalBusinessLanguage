<?php

declare(strict_types=1);

namespace Tiime\UniversalBusinessLanguage\Invoice\DataType\Aggregate;

use Tiime\UniversalBusinessLanguage\Invoice\DataType\Basic\AllowanceChargeAmount;
use Tiime\UniversalBusinessLanguage\Invoice\DataType\Basic\BaseAmount;

class PriceAllowanceCharge
{
    protected const XML_NODE = 'cac:AllowanceCharge';

    private string $chargeIndicator;

    /**
     * BT-147.
     */
    private AllowanceChargeAmount $amount;

    /**
     * BT-148.
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

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): ?self
    {
        $priceAllowanceChargeElements = $xpath->query(\sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $priceAllowanceChargeElements->count()) {
            return null;
        }

        if ($priceAllowanceChargeElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $priceAllowanceChargeElement */
        $priceAllowanceChargeElement = $priceAllowanceChargeElements->item(0);

        $chargeIndicatorElements = $xpath->query('./cbc:ChargeIndicator', $priceAllowanceChargeElement);

        if (1 !== $chargeIndicatorElements->count()) {
            throw new \Exception('Malformed');
        }

        $chargeIndicator = (string) $chargeIndicatorElements->item(0)->nodeValue;

        if ('false' !== $chargeIndicator) {
            throw new \Exception('Wrong charge indicator');
        }

        $amount     = AllowanceChargeAmount::fromXML($xpath, $priceAllowanceChargeElement);
        $baseAmount = BaseAmount::fromXML($xpath, $priceAllowanceChargeElement);

        $priceAllowanceCharge = new self($amount);

        if ($baseAmount instanceof BaseAmount) {
            $priceAllowanceCharge->setBaseAmount($baseAmount);
        }

        return $priceAllowanceCharge;
    }
}
