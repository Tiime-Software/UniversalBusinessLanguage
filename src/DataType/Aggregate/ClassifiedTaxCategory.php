<?php

declare(strict_types=1);

namespace Tiime\UniversalBusinessLanguage\DataType\Aggregate;

use Tiime\EN16931\DataType\VatCategory;
use Tiime\EN16931\SemanticDataType\Percentage;

/**
 * BG-30.
 */
class ClassifiedTaxCategory
{
    protected const XML_NODE = 'cac:ClassifiedTaxCategory';

    /**
     * BT-151.
     */
    private VatCategory $vatCategory;

    /**
     * BT-152.
     */
    private ?Percentage $percent;

    private TaxScheme $taxScheme;

    public function __construct(VatCategory $vatCategory)
    {
        $this->vatCategory = $vatCategory;
        $this->percent     = null;
        $this->taxScheme   = new TaxScheme('VAT');
    }

    public function getVatCategory(): VatCategory
    {
        return $this->vatCategory;
    }

    public function getPercent(): ?float
    {
        return $this->percent->getValueRounded();
    }

    public function setPercent(?float $percent): static
    {
        $this->percent = \is_float($percent) ? new Percentage($percent) : null;

        return $this;
    }

    public function getTaxScheme(): TaxScheme
    {
        return $this->taxScheme;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);

        $currentNode->appendChild($document->createElement('cbc:ID', $this->vatCategory->value));

        if ($this->percent instanceof Percentage) {
            $currentNode->appendChild($document->createElement('cbc:Percent', $this->percent->getFormattedValueRounded()));
        }

        $currentNode->appendChild($this->taxScheme->toXML($document));

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): self
    {
        $classifiedTaxCategoryElements = $xpath->query(sprintf('./%s', self::XML_NODE), $currentElement);

        if (1 !== $classifiedTaxCategoryElements->count()) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $classifiedTaxCategoryElement */
        $classifiedTaxCategoryElement = $classifiedTaxCategoryElements->item(0);

        $vatCategoryElements = $xpath->query('cbc:ID', $classifiedTaxCategoryElement);

        if (1 !== $vatCategoryElements->count()) {
            throw new \Exception('Malformed');
        }

        $vatCategory = VatCategory::tryFrom((string) $vatCategoryElements->item(0)->nodeValue);

        if (null === $vatCategory) {
            throw new \Exception('Invalid VAT category');
        }

        $taxScheme = TaxScheme::fromXML($xpath, $classifiedTaxCategoryElement);

        if ('VAT' !== $taxScheme->getIdentifier()) {
            throw new \Exception('Invalid tax scheme');
        }

        $classifiedTaxCategory = new self($vatCategory);

        $percentElements = $xpath->query('cbc:Percent', $classifiedTaxCategoryElement);

        if ($percentElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        $percent = (float) $percentElements->item(0)->nodeValue;

        if (1 === $percentElements->count()) {
            $classifiedTaxCategory->setPercent($percent);
        }

        return $classifiedTaxCategory;
    }
}
