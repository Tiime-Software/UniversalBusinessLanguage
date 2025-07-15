<?php

declare(strict_types=1);

namespace Tiime\UniversalBusinessLanguage\Ubl21Standard\CreditNote\DataType\Aggregate;

use Tiime\UniversalBusinessLanguage\Ubl21Standard\CreditNote\DataType\Basic\BaseQuantity;
use Tiime\UniversalBusinessLanguage\Ubl21Standard\CreditNote\DataType\Basic\PriceAmount;

/**
 * BG-29.
 */
class Price
{
    protected const XML_NODE = 'cac:Price';

    private PriceAmount $priceAmount;

    private ?BaseQuantity $baseQuantity;

    private ?PriceAllowanceCharge $allowance;

    public function __construct(PriceAmount $priceAmount)
    {
        $this->priceAmount  = $priceAmount;
        $this->baseQuantity = null;
        $this->allowance    = null;
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

    public function getAllowance(): ?PriceAllowanceCharge
    {
        return $this->allowance;
    }

    public function setAllowance(?PriceAllowanceCharge $allowance): static
    {
        $this->allowance = $allowance;

        return $this;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);

        $currentNode->appendChild($this->priceAmount->toXML($document));

        if ($this->baseQuantity instanceof BaseQuantity) {
            $currentNode->appendChild($this->baseQuantity->toXML($document));
        }

        if ($this->allowance instanceof PriceAllowanceCharge) {
            $currentNode->appendChild($this->allowance->toXML($document));
        }

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): self
    {
        $priceElements = $xpath->query(\sprintf('./%s', self::XML_NODE), $currentElement);

        if (1 !== $priceElements->count()) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $priceElement */
        $priceElement = $priceElements->item(0);

        $priceAmount  = PriceAmount::fromXML($xpath, $priceElement);
        $baseQuantity = BaseQuantity::fromXML($xpath, $priceElement);
        $allowance    = PriceAllowanceCharge::fromXML($xpath, $priceElement);

        $price = new self($priceAmount);

        if ($baseQuantity instanceof BaseQuantity) {
            $price->setBaseQuantity($baseQuantity);
        }

        if ($allowance instanceof PriceAllowanceCharge) {
            $price->setallowance($allowance);
        }

        return $price;
    }
}
