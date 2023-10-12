<?php

declare(strict_types=1);

namespace Tiime\UniversalBusinessLanguage\DataType\Aggregate;

/**
 * BG-31.
 */
class Item
{
    protected const XML_NODE = 'cac:Item';

    /**
     * BT-154.
     */
    private ?string $description;

    /**
     * BT-153.
     */
    private string $name;

    /**
     * BT-156.
     */
    private ?BuyersItemIdentification $buyersItemIdentification;

    /**
     * BT-155.
     */
    private ?SellersItemIdentification $sellersItemIdentification;

    /**
     * BT-157.
     */
    private ?StandardItemIdentification $standardItemIdentification;

    private ?OriginCountry $originCountry;

    /**
     * BT-158-00.
     *
     * @var array<int, CommodityClassification>
     */
    private array $commodityClassifications;

    /**
     * BG-30.
     */
    private ClassifiedTaxCategory $classifiedTaxCategory;

    /**
     * @var array<int,AdditionalItemProperty>
     */
    private array $additionalProperties;

    public function __construct(string $name, ClassifiedTaxCategory $classifiedTaxCategory)
    {
        $this->description                = null;
        $this->name                       = $name;
        $this->buyersItemIdentification   = null;
        $this->sellersItemIdentification  = null;
        $this->standardItemIdentification = null;
        $this->originCountry              = null;
        $this->commodityClassifications   = [];
        $this->additionalProperties       = [];
        $this->classifiedTaxCategory      = $classifiedTaxCategory;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getBuyersItemIdentification(): ?BuyersItemIdentification
    {
        return $this->buyersItemIdentification;
    }

    public function setBuyersItemIdentification(?BuyersItemIdentification $buyersItemIdentification): static
    {
        $this->buyersItemIdentification = $buyersItemIdentification;

        return $this;
    }

    public function getSellersItemIdentification(): ?SellersItemIdentification
    {
        return $this->sellersItemIdentification;
    }

    public function setSellersItemIdentification(?SellersItemIdentification $sellersItemIdentification): static
    {
        $this->sellersItemIdentification = $sellersItemIdentification;

        return $this;
    }

    public function getStandardItemIdentification(): ?StandardItemIdentification
    {
        return $this->standardItemIdentification;
    }

    public function setStandardItemIdentification(?StandardItemIdentification $standardItemIdentification): static
    {
        $this->standardItemIdentification = $standardItemIdentification;

        return $this;
    }

    public function getOriginCountry(): ?OriginCountry
    {
        return $this->originCountry;
    }

    public function setOriginCountry(?OriginCountry $originCountry): static
    {
        $this->originCountry = $originCountry;

        return $this;
    }

    /**
     * @return array|CommodityClassification[]
     */
    public function getCommodityClassifications(): array
    {
        return $this->commodityClassifications;
    }

    /**
     * @param array<int,CommodityClassification> $commodityClassifications
     *
     * @return $this
     */
    public function setCommodityClassifications(array $commodityClassifications): static
    {
        foreach ($commodityClassifications as $commodityClassification) {
            if (!$commodityClassification instanceof CommodityClassification) {
                throw new \TypeError();
            }
        }

        $this->commodityClassifications = $commodityClassifications;

        return $this;
    }

    /**
     * @return AdditionalItemProperty[]
     */
    public function getAdditionalProperties(): array
    {
        return $this->additionalProperties;
    }

    /**
     * @param array<int,AdditionalItemProperty> $additionalProperties
     *
     * @return $this
     */
    public function setAdditionalProperties(array $additionalProperties): static
    {
        foreach ($additionalProperties as $additionalProperty) {
            if (!$additionalProperty instanceof AdditionalItemProperty) {
                throw new \TypeError();
            }
        }

        $this->additionalProperties = $additionalProperties;

        return $this;
    }

    public function getClassifiedTaxCategory(): ClassifiedTaxCategory
    {
        return $this->classifiedTaxCategory;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);

        if (\is_string($this->description)) {
            $currentNode->appendChild($document->createElement('cbc:Description', $this->description));
        }

        $currentNode->appendChild($document->createElement('cbc:Name', $this->name));

        if ($this->buyersItemIdentification instanceof BuyersItemIdentification) {
            $currentNode->appendChild($this->buyersItemIdentification->toXML($document));
        }

        if ($this->sellersItemIdentification instanceof SellersItemIdentification) {
            $currentNode->appendChild($this->sellersItemIdentification->toXML($document));
        }

        if ($this->standardItemIdentification instanceof StandardItemIdentification) {
            $currentNode->appendChild($this->standardItemIdentification->toXML($document));
        }

        if ($this->originCountry instanceof OriginCountry) {
            $currentNode->appendChild($this->originCountry->toXML($document));
        }

        foreach ($this->commodityClassifications as $commodityClassification) {
            $currentNode->appendChild($commodityClassification->toXML($document));
        }

        $currentNode->appendChild($this->classifiedTaxCategory->toXML($document));

        foreach ($this->additionalProperties as $additionalProperty) {
            $currentNode->appendChild($additionalProperty->toXML($document));
        }

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): self
    {
        $itemElements = $xpath->query(sprintf('./%s', self::XML_NODE), $currentElement);

        if (1 !== $itemElements->count()) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $itemElement */
        $itemElement = $itemElements->item(0);

        $descriptionElements = $xpath->query('./cbc:Description', $itemElement);

        if ($descriptionElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        $nameElements = $xpath->query('./cbc:Name', $itemElement);

        if (1 !== $nameElements->count()) {
            throw new \Exception('Malformed');
        }

        $classifiedTaxCategory = ClassifiedTaxCategory::fromXML($xpath, $itemElement);

        $item = new self((string) $nameElements->item(0)->nodeValue, $classifiedTaxCategory);

        if (1 === $descriptionElements->count()) {
            $item->setDescription((string) $descriptionElements->item(0)->nodeValue);
        }

        $buyersItemIdentification   = BuyersItemIdentification::fromXML($xpath, $itemElement);
        $sellersItemIdentification  = SellersItemIdentification::fromXML($xpath, $itemElement);
        $standardItemIdentification = StandardItemIdentification::fromXML($xpath, $itemElement);
        $originCountry              = OriginCountry::fromXML($xpath, $itemElement);
        $commodityClassifications   = CommodityClassification::fromXML($xpath, $itemElement);
        $additionalProperties       = AdditionalItemProperty::fromXML($xpath, $itemElement);

        if ($buyersItemIdentification instanceof BuyersItemIdentification) {
            $item->setBuyersItemIdentification($buyersItemIdentification);
        }

        if ($sellersItemIdentification instanceof SellersItemIdentification) {
            $item->setSellersItemIdentification($sellersItemIdentification);
        }

        if ($standardItemIdentification instanceof StandardItemIdentification) {
            $item->setStandardItemIdentification($standardItemIdentification);
        }

        if ($originCountry instanceof OriginCountry) {
            $item->setOriginCountry($originCountry);
        }

        if (\count($commodityClassifications) > 0) {
            $item->setCommodityClassifications($commodityClassifications);
        }

        if (\count($additionalProperties) > 0) {
            $item->setAdditionalProperties($additionalProperties);
        }

        return $item;
    }
}
