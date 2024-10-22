<?php

declare(strict_types=1);

namespace Tiime\UniversalBusinessLanguage\Ubl21\Invoice\DataType\Aggregate;

use Tiime\EN16931\Codelist\DutyTaxFeeCategoryCodeUNTDID5305 as VatCategory;
use Tiime\EN16931\Codelist\VatExemptionReasonCode;
use Tiime\EN16931\SemanticDataType\Percentage;

class SubtotalTaxCategory
{
    protected const XML_NODE = 'cac:TaxCategory';

    /**
     * BT-118.
     */
    private VatCategory $identifier;

    /**
     * BT-119.
     */
    private ?Percentage $percent;

    /**
     * BT-121.
     */
    private ?VatExemptionReasonCode $taxExemptionReasonCode;

    /**
     * BT-120.
     */
    private ?string $taxExemptionReason;

    private TaxScheme $taxScheme;

    public function __construct(VatCategory $identifier)
    {
        $this->identifier             = $identifier;
        $this->percent                = null;
        $this->taxExemptionReasonCode = null;
        $this->taxExemptionReason     = null;
        $this->taxScheme              = new TaxScheme('VAT');
    }

    public function getVatCategory(): VatCategory
    {
        return $this->identifier;
    }

    public function getTaxExemptionReasonCode(): ?VatExemptionReasonCode
    {
        return $this->taxExemptionReasonCode;
    }

    public function setTaxExemptionReasonCode(?VatExemptionReasonCode $taxExemptionReasonCode): static
    {
        $this->taxExemptionReasonCode = $taxExemptionReasonCode;

        return $this;
    }

    public function getTaxExemptionReason(): ?string
    {
        return $this->taxExemptionReason;
    }

    public function setTaxExemptionReason(?string $taxExemptionReason): static
    {
        $this->taxExemptionReason = $taxExemptionReason;

        return $this;
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
            $currentNode->appendChild(
                $document->createElement('cbc:Percent', $this->percent->getFormattedValueRounded())
            );
        }

        if ($this->taxExemptionReasonCode instanceof VatExemptionReasonCode) {
            $currentNode->appendChild(
                $document->createElement('cbc:TaxExemptionReasonCode', $this->taxExemptionReasonCode->value)
            );
        }

        if (\is_string($this->taxExemptionReason)) {
            $currentNode->appendChild(
                $document->createElement('cbc:TaxExemptionReason', $this->taxExemptionReason)
            );
        }

        $currentNode->appendChild($this->taxScheme->toXML($document));

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): self
    {
        $taxCategoryElements = $xpath->query(\sprintf('./%s', self::XML_NODE), $currentElement);

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

        $taxExemptionReasonCodeElements = $xpath->query('./cbc:TaxExemptionReasonCode', $taxCategoryElement);

        if ($taxExemptionReasonCodeElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        if (1 === $taxExemptionReasonCodeElements->count()) {
            $taxExemptionReasonCode = VatExemptionReasonCode::tryFrom((string) $taxExemptionReasonCodeElements->item(0)->nodeValue);

            if (null === $taxExemptionReasonCode) {
                throw new \Exception('Wrong exemption reason code');
            }
            $taxCategory->setTaxExemptionReasonCode($taxExemptionReasonCode);
        }

        $taxExemptionReasonElements = $xpath->query('./cbc:TaxExemptionReason', $taxCategoryElement);

        if ($taxExemptionReasonElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        if (1 === $taxExemptionReasonElements->count()) {
            $taxCategory->setTaxExemptionReason((string) $taxExemptionReasonElements->item(0)->nodeValue);
        }

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
