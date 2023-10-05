<?php

declare(strict_types=1);

namespace Tiime\UniversalBusinessLanguage\DataType\Aggregate;

use Tiime\EN16931\DataType\ItemTypeCode;

class CommodityClassification
{
    protected const XML_NODE = 'cac:CommodityClassification';

    /**
     * BT-158.
     */
    private string $itemClassificationCode;

    /**
     * BT-158-1.
     */
    private ItemTypeCode $listIdentifier;

    /**
     * BT-158-2.
     */
    private ?string $listVersionIdentifier;

    public function __construct(string $itemClassificationCode, ItemTypeCode $listIdentifier)
    {
        $this->itemClassificationCode = $itemClassificationCode;
        $this->listIdentifier         = $listIdentifier;
        $this->listVersionIdentifier  = null;
    }

    public function getItemClassificationCode(): string
    {
        return $this->itemClassificationCode;
    }

    public function getListIdentifier(): ItemTypeCode
    {
        return $this->listIdentifier;
    }

    public function getListVersionIdentifier(): ?string
    {
        return $this->listVersionIdentifier;
    }

    public function setListVersionIdentifier(?string $listVersionIdentifier): static
    {
        $this->listVersionIdentifier = $listVersionIdentifier;

        return $this;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE, $this->itemClassificationCode);
        $currentNode->setAttribute('listID', $this->listIdentifier->value);

        if (\is_string($this->listVersionIdentifier)) {
            $currentNode->setAttribute('listVersionID', $this->listVersionIdentifier);
        }

        return $currentNode;
    }

    /**
     * @return array<int,CommodityClassification>
     */
    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): array
    {
        $commodityClassificationElements = $xpath->query(sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $commodityClassificationElements->count()) {
            return [];
        }

        $commodityClassifications = [];

        /** @var \DOMElement $commodityClassificationElement */
        foreach ($commodityClassificationElements as $commodityClassificationElement) {
            $itemClassificationCode = (string) $commodityClassificationElement->nodeValue;

            $identifier = ItemTypeCode::tryFrom($commodityClassificationElement->getAttribute('listID'));

            if (!$identifier instanceof ItemTypeCode) {
                throw new \Exception('Wrong listID');
            }

            $commodityClassification = new self($itemClassificationCode, $identifier);

            if ($commodityClassificationElement->hasAttribute('listVersionID')) {
                $listVersionIdentifier = $commodityClassificationElement->getAttribute('listVersionID');

                $commodityClassification->setListVersionIdentifier($listVersionIdentifier);
            }

            $commodityClassifications[] = $commodityClassification;
        }

        return $commodityClassifications;
    }
}
