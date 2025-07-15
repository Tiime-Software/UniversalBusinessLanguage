<?php

declare(strict_types=1);

namespace Tiime\UniversalBusinessLanguage\Ubl21\Invoice\DataType\Aggregate;

use Tiime\EN16931\DataType\Identifier\InvoiceLineIdentifier;
use Tiime\UniversalBusinessLanguage\Ubl21\Invoice\DataType\Basic\InvoicedQuantity;

/**
 * BG-25.
 */
class InvoiceLine
{
    protected const XML_NODE = 'cac:InvoiceLine';

    /**
     * BT-126.
     */
    private InvoiceLineIdentifier $invoiceLineIdentifier;

    /**
     * BT-127.
     */
    private ?string $note;

    /**
     * BT-129 & BT-130.
     */
    private InvoicedQuantity $invoicedQuantity;

    /**
     * BG-26.
     */
    private ?InvoiceLineInvoicePeriod $invoicePeriod;

    /**
     * @var array<int,InvoiceLineAllowance>
     */
    private array $allowances;

    /**
     * @var array<int,InvoiceLineCharge>
     */
    private array $charges;

    /**
     * BG-31.
     */
    private Item $item;

    /**
     * BG-29.
     */
    private Price $price;

    public function __construct(
        InvoiceLineIdentifier $invoiceLineIdentifier,
        InvoicedQuantity $invoicedQuantity,
        Item $item,
        Price $price,
    ) {
        $this->invoiceLineIdentifier = $invoiceLineIdentifier;
        $this->note                  = null;
        $this->invoicedQuantity      = $invoicedQuantity;
        $this->invoicePeriod         = null;
        $this->allowances            = [];
        $this->charges               = [];
        $this->item                  = $item;
        $this->price                 = $price;
    }

    public function getInvoiceLineIdentifier(): ?InvoiceLineIdentifier
    {
        return $this->invoiceLineIdentifier;
    }

    public function setInvoiceLineIdentifier(InvoiceLineIdentifier $invoiceLineIdentifier): static
    {
        $this->invoiceLineIdentifier = $invoiceLineIdentifier;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): static
    {
        $this->note = $note;

        return $this;
    }

    public function getInvoicedQuantity(): InvoicedQuantity
    {
        return $this->invoicedQuantity;
    }

    public function getInvoicePeriod(): ?InvoiceLineInvoicePeriod
    {
        return $this->invoicePeriod;
    }

    public function setInvoicePeriod(?InvoiceLineInvoicePeriod $invoicePeriod): static
    {
        $this->invoicePeriod = $invoicePeriod;

        return $this;
    }

    /**
     * @return array|InvoiceLineAllowance[]
     */
    public function getAllowances(): array
    {
        return $this->allowances;
    }

    /**
     * @param array<int, InvoiceLineAllowance> $allowances
     */
    public function setAllowances(array $allowances): static
    {
        foreach ($allowances as $allowance) {
            if (!$allowance instanceof InvoiceLineAllowance) {
                throw new \TypeError();
            }
        }

        $this->allowances = $allowances;

        return $this;
    }

    /**
     * @return array|InvoiceLineCharge[]
     */
    public function getCharges(): array
    {
        return $this->charges;
    }

    /**
     * @param array<int, InvoiceLineCharge> $charges
     */
    public function setCharges(array $charges): static
    {
        foreach ($charges as $charge) {
            if (!$charge instanceof InvoiceLineCharge) {
                throw new \TypeError();
            }
        }

        $this->charges = $charges;

        return $this;
    }

    public function getItem(): Item
    {
        return $this->item;
    }

    public function getPrice(): Price
    {
        return $this->price;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);

        $currentNode->appendChild($document->createElement('cbc:ID', $this->invoiceLineIdentifier->value));

        if (\is_string($this->note)) {
            $currentNode->appendChild($document->createElement('cbc:Note', $this->note));
        }

        $currentNode->appendChild($this->invoicedQuantity->toXML($document));

        if ($this->invoicePeriod instanceof InvoiceLineInvoicePeriod) {
            $currentNode->appendChild($this->invoicePeriod->toXML($document));
        }

        foreach ($this->allowances as $allowance) {
            $currentNode->appendChild($allowance->toXML($document));
        }

        foreach ($this->charges as $charge) {
            $currentNode->appendChild($charge->toXML($document));
        }

        $currentNode->appendChild($this->item->toXML($document));
        $currentNode->appendChild($this->price->toXML($document));

        return $currentNode;
    }

    /**
     * @return non-empty-array<int, InvoiceLine>
     */
    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): array
    {
        $invoiceLineElements = $xpath->query(\sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $invoiceLineElements->count()) {
            throw new \Exception('Malformed');
        }

        $invoiceLines = [];

        /** @var \DOMElement $invoiceLineElement */
        foreach ($invoiceLineElements as $invoiceLineElement) {
            $invoiceLineIdentifierElements = $xpath->query('./cbc:ID', $invoiceLineElement);

            if (1 !== $invoiceLineIdentifierElements->count()) {
                throw new \Exception('Malformed');
            }

            $invoiceLineIdentifier = (string) $invoiceLineIdentifierElements->item(0)->nodeValue;

            $invoicedQuantity = InvoicedQuantity::fromXML($xpath, $invoiceLineElement);
            $invoicePeriod    = InvoiceLineInvoicePeriod::fromXML($xpath, $invoiceLineElement);

            $allowanceChargeElements = $xpath->query('./cac:AllowanceCharge', $invoiceLineElement);

            /** @var \DOMElement $allowanceChargeElement */
            foreach ($allowanceChargeElements as $allowanceChargeElement) {
                $chargeIndicatorElements = $xpath->query('./cbc:ChargeIndicator', $allowanceChargeElement);

                if (1 !== $chargeIndicatorElements->count()) {
                    throw new \Exception('Malformed');
                }
                $chargeIndicator = (string) $chargeIndicatorElements->item(0)->nodeValue;

                if ('true' !== $chargeIndicator && 'false' !== $chargeIndicator) {
                    throw new \Exception('Wrong charge indicator');
                }
            }

            $allowances = InvoiceLineAllowance::fromXML($xpath, $invoiceLineElement);
            $charges    = InvoiceLineCharge::fromXML($xpath, $invoiceLineElement);
            $item       = Item::fromXML($xpath, $invoiceLineElement);
            $price      = Price::fromXML($xpath, $invoiceLineElement);

            $invoiceLine = new self(
                new InvoiceLineIdentifier($invoiceLineIdentifier),
                $invoicedQuantity,
                $item,
                $price
            );

            $noteElements = $xpath->query('./cbc:Note', $invoiceLineElement);

            if ($noteElements->count() > 1) {
                throw new \Exception('Malformed');
            }

            if (1 === $noteElements->count()) {
                $invoiceLine->setNote((string) $noteElements->item(0)->nodeValue);
            }

            if ($invoicePeriod instanceof InvoiceLineInvoicePeriod) {
                $invoiceLine->setInvoicePeriod($invoicePeriod);
            }

            if (\count($allowances) > 0) {
                $invoiceLine->setAllowances($allowances);
            }

            if (\count($charges) > 0) {
                $invoiceLine->setCharges($charges);
            }

            $invoiceLines[] = $invoiceLine;
        }

        return $invoiceLines;
    }
}
