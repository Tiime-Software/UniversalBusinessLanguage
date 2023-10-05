<?php

declare(strict_types=1);

namespace Tiime\UniversalBusinessLanguage\DataType\Aggregate;

use Tiime\UniversalBusinessLanguage\DataType\Basic\BaseQuantity;
use Tiime\UniversalBusinessLanguage\DataType\Basic\PriceAmount;

/**
 * BG-29.
 */
class Price
{
    protected const XML_NODE = 'cac:Contact';

    private PriceAmount $priceAmount;

    private ?BaseQuantity $baseQuantity;

    private ?PriceAllowanceCharge $allowanceCharge;

    public function __construct(PriceAmount $priceAmount)
    {
        $this->priceAmount     = $priceAmount;
        $this->baseQuantity    = null;
        $this->allowanceCharge = null;
    }

    public function getPriceAmount(): PriceAmount
    {
        return $this->priceAmount;
    }

    public function getBaseQuantity(): ?BaseQuantity
    {
        return $this->baseQuantity;
    }

    public function setBaseQuantity(?BaseQuantity $baseQuantity): static
    {
        $this->baseQuantity = $baseQuantity;

        return $this;
    }

    public function getAllowanceCharge(): ?PriceAllowanceCharge
    {
        return $this->allowanceCharge;
    }

    public function setAllowanceCharge(?PriceAllowanceCharge $allowanceCharge): static
    {
        $this->allowanceCharge = $allowanceCharge;

        return $this;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);

        $currentNode->appendChild($this->priceAmount->toXML($document));

        if ($this->baseQuantity instanceof BaseQuantity) {
            $currentNode->appendChild($this->baseQuantity->toXML($document));
        }

        if ($this->allowanceCharge instanceof PriceAllowanceCharge) {
            $currentNode->appendChild($this->allowanceCharge->toXML($document));
        }

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): self
    {
        $priceElements = $xpath->query(sprintf('./%s', self::XML_NODE), $currentElement);

        if (1 !== $priceElements->count()) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $priceElement */
        $priceElement = $priceElements->item(0);

        $priceAmount     = PriceAmount::fromXML($xpath, $priceElement);
        $baseQuantity    = BaseQuantity::fromXML($xpath, $priceElement);
        $allowanceCharge = PriceAllowanceCharge::fromXML($xpath, $priceElement);

        $price = new self($priceAmount);

        if ($baseQuantity instanceof BaseQuantity) {
            $price->setBaseQuantity($baseQuantity);
        }

        if ($allowanceCharge instanceof PriceAllowanceCharge) {
            $price->setAllowanceCharge($allowanceCharge);
        }

        return $price;
    }
}
