<?php

declare(strict_types=1);

namespace Tiime\UniversalBusinessLanguage\CreditNote\DataType\Aggregate;

use Tiime\EN16931\DataType\VatCategory;
use Tiime\EN16931\SemanticDataType\Percentage;

class TaxCategory
{
    protected const XML_NODE = 'cac:TaxCategory';

    /**
     * BT-95. or BT-102.
     */
    private VatCategory $identifier;

    /**
     * BT-96. or BT-103.
     */
    private ?Percentage $percent;

    private TaxScheme $taxScheme;

    public function __construct(VatCategory $identifier)
    {
        $this->identifier = $identifier;
        $this->percent    = null;
        $this->taxScheme  = new TaxScheme('VAT');
    }

    public function getVatCategory(): VatCategory
    {
        return $this->identifier;
    }

    public function getPercent(): ?Percentage
    {
        return $this->percent;
    }

    public function setPercent(?float $value): static
    {
        $this->percent = \is_float($value) ? new Percentage($value) : null;

        return $this;
    }

    public function getTaxScheme(): TaxScheme
    {
        return $this->taxScheme;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);

        $currentNode->appendChild($document->createElement('cbc:ID', $this->identifier->value));

        if ($this->percent instanceof Percentage) {
            $currentNode->appendChild($document->createElement('cbc:Percent', $this->percent->getFormattedValueRounded()));
        }

        $currentNode->appendChild($this->taxScheme->toXML($document));

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): self
    {
        $taxCategoryElements = $xpath->query(sprintf('./%s', self::XML_NODE), $currentElement);

        if (1 !== $taxCategoryElements->count()) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $taxCategoryElement */
        $taxCategoryElement = $taxCategoryElements->item(0);

        $identifierElements = $xpath->query('./cbc:ID', $taxCategoryElement);

        if (1 !== $identifierElements->count()) {
            throw new \Exception('Malformed');
        }

        $identifier = VatCategory::tryFrom((string) $identifierElements->item(0)->nodeValue);

        if (null === $identifier) {
            throw new \Exception('Invalid VAT category');
        }

        $taxScheme = TaxScheme::fromXML($xpath, $taxCategoryElement);

        if ('VAT' !== $taxScheme->getIdentifier()) {
            throw new \Exception('Invalid tax scheme');
        }

        $taxCategory = new self($identifier);

        $percentElements = $xpath->query('./cbc:Percent', $taxCategoryElement);

        if ($percentElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        if (1 === $percentElements->count()) {
            if (!is_numeric($percentElements->item(0)->nodeValue)) {
                throw new \Exception('Malformed');
            }
            $taxCategory->setPercent((float) $percentElements->item(0)->nodeValue);
        }

        return $taxCategory;
    }
}
