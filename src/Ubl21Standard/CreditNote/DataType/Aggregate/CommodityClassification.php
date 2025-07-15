<?php

declare(strict_types=1);

namespace Tiime\UniversalBusinessLanguage\Ubl21Standard\CreditNote\DataType\Aggregate;

use Tiime\EN16931\Codelist\ItemTypeCodeUNTDID7143 as ItemTypeCode;

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
        $currentNode = $document->createElement(self::XML_NODE);

        $itemClassificationCodeElement = $document->createElement('cbc:ItemClassificationCode', $this->itemClassificationCode);

        $itemClassificationCodeElement->setAttribute('listID', $this->listIdentifier->value);

        if (\is_string($this->listVersionIdentifier)) {
            $itemClassificationCodeElement->setAttribute('listVersionID', $this->listVersionIdentifier);
        }

        $currentNode->appendChild($itemClassificationCodeElement);

        return $currentNode;
    }

    /**
     * @return array<int,CommodityClassification>
     */
    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): array
    {
        $commodityClassificationElements = $xpath->query(\sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $commodityClassificationElements->count()) {
            return [];
        }

        $commodityClassifications = [];

        /** @var \DOMElement $commodityClassificationElement */
        foreach ($commodityClassificationElements as $commodityClassificationElement) {
            $itemClassificationCodeElements = $xpath->query('./cbc:ItemClassificationCode', $commodityClassificationElement);

            if (1 !== $itemClassificationCodeElements->count()) {
                throw new \Exception('Malformed');
            }

            /** @var \DOMElement $itemClassificationCodeElement */
            $itemClassificationCodeElement = $itemClassificationCodeElements->item(0);

            $itemClassificationCode = (string) $itemClassificationCodeElement->nodeValue;

            if (!$itemClassificationCodeElement->hasAttribute('listID')) {
                throw new \Exception('Malformed');
            }

            $identifier = ItemTypeCode::tryFrom($itemClassificationCodeElement->getAttribute('listID'));

            if (!$identifier instanceof ItemTypeCode) {
                throw new \Exception('Wrong listID');
            }

            $commodityClassification = new self($itemClassificationCode, $identifier);

            if ($itemClassificationCodeElement->hasAttribute('listVersionID')) {
                $listVersionIdentifier = $itemClassificationCodeElement->getAttribute('listVersionID');

                $commodityClassification->setListVersionIdentifier($listVersionIdentifier);
            }

            $commodityClassifications[] = $commodityClassification;
        }

        return $commodityClassifications;
    }
}
