<?php

declare(strict_types=1);

namespace Tiime\UniversalBusinessLanguage\CreditNote\DataType\Aggregate;

use Tiime\EN16931\DataType\Identifier\InvoiceLineIdentifier;
use Tiime\UniversalBusinessLanguage\CreditNote\DataType\Basic\CreditedQuantity;
use Tiime\UniversalBusinessLanguage\CreditNote\DataType\Basic\LineExtensionAmount;

/**
 * BG-25.
 */
class CreditNoteLine
{
    protected const XML_NODE = 'cac:CreditNoteLine';

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
    private CreditedQuantity $creditedQuantity;

    /**
     * BT-131.
     */
    private LineExtensionAmount $lineExtensionAmount;

    /**
     * BT-133.
     */
    private ?string $accountingCost;

    /**
     * BG-26.
     */
    private ?InvoiceLineInvoicePeriod $invoicePeriod;

    /**
     * BT-132.
     */
    private ?OrderLineReference $orderLineReference;

    /**
     * BT-128. & BT-128-1.
     */
    private ?DocumentReference $documentReference;

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
        CreditedQuantity $invoicedQuantity,
        LineExtensionAmount $lineExtensionAmount,
        Item $item,
        Price $price,
    ) {
        $this->invoiceLineIdentifier = $invoiceLineIdentifier;
        $this->note                  = null;
        $this->creditedQuantity      = $invoicedQuantity;
        $this->lineExtensionAmount   = $lineExtensionAmount;
        $this->accountingCost        = null;
        $this->invoicePeriod         = null;
        $this->orderLineReference    = null;
        $this->documentReference     = null;
        $this->allowances            = [];
        $this->charges               = [];
        $this->item                  = $item;
        $this->price                 = $price;
    }

    public function getInvoiceLineIdentifier(): InvoiceLineIdentifier
    {
        return $this->invoiceLineIdentifier;
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

    public function getCreditedQuantity(): CreditedQuantity
    {
        return $this->creditedQuantity;
    }

    public function getLineExtensionAmount(): LineExtensionAmount
    {
        return $this->lineExtensionAmount;
    }

    public function getAccountingCost(): ?string
    {
        return $this->accountingCost;
    }

    public function setAccountingCost(?string $accountingCost): static
    {
        $this->accountingCost = $accountingCost;

        return $this;
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

    public function getOrderLineReference(): ?OrderLineReference
    {
        return $this->orderLineReference;
    }

    public function setOrderLineReference(?OrderLineReference $orderLineReference): static
    {
        $this->orderLineReference = $orderLineReference;

        return $this;
    }

    public function getDocumentReference(): ?DocumentReference
    {
        return $this->documentReference;
    }

    public function setDocumentReference(?DocumentReference $documentReference): static
    {
        $this->documentReference = $documentReference;

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

        $currentNode->appendChild($this->creditedQuantity->toXML($document));
        $currentNode->appendChild($this->lineExtensionAmount->toXML($document));

        if (\is_string($this->accountingCost)) {
            $currentNode->appendChild($document->createElement('cbc:AccountingCost', $this->accountingCost));
        }

        if ($this->invoicePeriod instanceof InvoiceLineInvoicePeriod) {
            $currentNode->appendChild($this->invoicePeriod->toXML($document));
        }

        if ($this->orderLineReference instanceof OrderLineReference) {
            $currentNode->appendChild($this->orderLineReference->toXML($document));
        }

        if ($this->documentReference instanceof DocumentReference) {
            $currentNode->appendChild($this->documentReference->toXML($document));
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
     * @return non-empty-array<int, CreditNoteLine>
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

            $invoicedQuantity    = CreditedQuantity::fromXML($xpath, $invoiceLineElement);
            $lineExtensionAmount = LineExtensionAmount::fromXML($xpath, $invoiceLineElement);
            $invoicePeriod       = InvoiceLineInvoicePeriod::fromXML($xpath, $invoiceLineElement);
            $orderLineReference  = OrderLineReference::fromXML($xpath, $invoiceLineElement);
            $documentReference   = DocumentReference::fromXML($xpath, $invoiceLineElement);

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
                $lineExtensionAmount,
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

            $accountingCostElements = $xpath->query('./cbc:AccountingCost', $invoiceLineElement);

            if ($accountingCostElements->count() > 1) {
                throw new \Exception('Malformed');
            }

            if (1 === $accountingCostElements->count()) {
                $invoiceLine->setAccountingCost((string) $accountingCostElements->item(0)->nodeValue);
            }

            if ($invoicePeriod instanceof InvoiceLineInvoicePeriod) {
                $invoiceLine->setInvoicePeriod($invoicePeriod);
            }

            if ($orderLineReference instanceof OrderLineReference) {
                $invoiceLine->setOrderLineReference($orderLineReference);
            }

            if ($documentReference instanceof DocumentReference) {
                $invoiceLine->setDocumentReference($documentReference);
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
