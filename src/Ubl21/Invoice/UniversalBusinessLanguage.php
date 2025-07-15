<?php

declare(strict_types=1);

namespace Tiime\UniversalBusinessLanguage\Ubl21\Invoice;

use Tiime\EN16931\Codelist\CurrencyCodeISO4217 as CurrencyCode;
use Tiime\EN16931\DataType\Identifier\InvoiceIdentifier;
use Tiime\EN16931\DataType\Identifier\SpecificationIdentifier;
use Tiime\UniversalBusinessLanguage\Ubl21\Invoice\DataType\Aggregate\AccountingCustomerParty;
use Tiime\UniversalBusinessLanguage\Ubl21\Invoice\DataType\Aggregate\AccountingSupplierParty;
use Tiime\UniversalBusinessLanguage\Ubl21\Invoice\DataType\Aggregate\Allowance;
use Tiime\UniversalBusinessLanguage\Ubl21\Invoice\DataType\Aggregate\BillingReference;
use Tiime\UniversalBusinessLanguage\Ubl21\Invoice\DataType\Aggregate\Charge;
use Tiime\UniversalBusinessLanguage\Ubl21\Invoice\DataType\Aggregate\Delivery;
use Tiime\UniversalBusinessLanguage\Ubl21\Invoice\DataType\Aggregate\InvoiceLine;
use Tiime\UniversalBusinessLanguage\Ubl21\Invoice\DataType\Aggregate\InvoicePeriod;
use Tiime\UniversalBusinessLanguage\Ubl21\Invoice\DataType\Aggregate\LegalMonetaryTotal;
use Tiime\UniversalBusinessLanguage\Ubl21\Invoice\DataType\Aggregate\TaxRepresentativeParty;
use Tiime\UniversalBusinessLanguage\Ubl21\Invoice\DataType\Aggregate\TaxTotal;
use Tiime\UniversalBusinessLanguage\Ubl21\Invoice\DataType\Basic\DueDate;
use Tiime\UniversalBusinessLanguage\Ubl21\Invoice\DataType\Basic\IssueDate;
use Tiime\UniversalBusinessLanguage\Ubl21\Invoice\DataType\Basic\Note;
use Tiime\UniversalBusinessLanguage\Ubl21\Invoice\DataType\InvoiceTypeCode;
use Tiime\UniversalBusinessLanguage\UniversalBusinessLanguageInterface;

class UniversalBusinessLanguage implements UniversalBusinessLanguageInterface
{
    private const XML_NODE = 'ubl:Invoice';

    /**
     * BT-1.
     */
    private InvoiceIdentifier $identifier;

    /**
     * BT-2-00.
     */
    private IssueDate $issueDate;

    /**
     * BT-3.
     */
    private InvoiceTypeCode $invoiceTypeCode;

    /**
     * BT-5.
     */
    private CurrencyCode $documentCurrencyCode;

    /**
     * BT-8-00.
     */
    private ?InvoicePeriod $invoicePeriod;

    /**
     * BT-9-00.
     */
    private ?DueDate $dueDate;

    /**
     * BG-1-00.
     *
     * @var array<int, Note>
     */
    private array $notes;

    /**
     * BT-23.
     */
    private ?string $profileIdentifier;

    /**
     * BT-24.
     */
    private SpecificationIdentifier $customizationIdentifier;

    /**
     * Pas de BT
     * 0..1, n'apparait pas dans les specs ni Peppol mais dans la norme UBL 2.1 à 2.3.
     */
    private ?string $ublVersionIdentifier;

    /**
     * BG-3.
     *
     * @var array<int, BillingReference>
     */
    private array $billingReferences;

    /**
     * BG-4.
     */
    private AccountingSupplierParty $accountingSupplierParty;

    /**
     * BG-7.
     */
    private AccountingCustomerParty $accountingCustomerParty;

    /**
     * BG-11.
     */
    private ?TaxRepresentativeParty $taxRepresentativeParty;

    /**
     * BG-13.
     */
    private ?Delivery $delivery;

    /**
     * BG-20.
     *
     * @var array<int, Allowance>
     */
    private array $allowances;

    /**
     * BG-21.
     *
     * @var array<int, Charge>
     */
    private array $charges;

    /**
     * @var array<int, TaxTotal>
     *                           (1..2)
     */
    private array $taxTotals;

    /**
     * BG-22.
     */
    private LegalMonetaryTotal $legalMonetaryTotal;

    /**
     * BG-25.
     *
     * @var InvoiceLine
     */
    private array $invoiceLines;

    /**
     * @param InvoiceLine $invoiceLines
     */
    public function __construct(
        InvoiceIdentifier $identifier,
        IssueDate $issueDate,
        InvoiceTypeCode $invoiceTypeCode,
        CurrencyCode $documentCurrencyCode,
        SpecificationIdentifier $customizationIdentifier,
        AccountingSupplierParty $accountingSupplierParty,
        AccountingCustomerParty $accountingCustomerParty,
        array $taxTotals,
        LegalMonetaryTotal $legalMonetaryTotal,
        array $invoiceLines,
    ) {
        if (0 === \count($taxTotals) || \count($taxTotals) > 2) {
            throw new \Exception('Malformed');
        }

        foreach ($taxTotals as $taxTotal) {
            if (!$taxTotal instanceof TaxTotal) {
                throw new \TypeError();
            }
        }

        /*        Pas de lignes de facture OBLIGATOIRE au DEMARRAGE, les remettre lors de l'implémentation CIBLE
                if (0 === \count($invoiceLines)) {
                    throw new \Exception('Malformed');
                }
        */
        foreach ($invoiceLines as $invoiceLine) {
            if (!$invoiceLine instanceof InvoiceLine) {
                throw new \TypeError();
            }
        }

        $this->invoiceLines            = $invoiceLines;
        $this->identifier              = $identifier;
        $this->issueDate               = $issueDate;
        $this->invoiceTypeCode         = $invoiceTypeCode;
        $this->documentCurrencyCode    = $documentCurrencyCode;
        $this->customizationIdentifier = $customizationIdentifier;
        $this->accountingSupplierParty = $accountingSupplierParty;
        $this->accountingCustomerParty = $accountingCustomerParty;
        $this->taxTotals               = $taxTotals;
        $this->legalMonetaryTotal      = $legalMonetaryTotal;

        $this->profileIdentifier      = null;
        $this->invoicePeriod          = null;
        $this->dueDate                = null;
        $this->notes                  = [];
        $this->billingReferences      = [];
        $this->taxRepresentativeParty = null;
        $this->delivery               = null;
        $this->allowances             = [];
        $this->charges                = [];
        $this->ublVersionIdentifier   = null;
    }

    public function getIdentifier(): InvoiceIdentifier
    {
        return $this->identifier;
    }

    public function getIssueDate(): IssueDate
    {
        return $this->issueDate;
    }

    public function getInvoiceTypeCode(): InvoiceTypeCode
    {
        return $this->invoiceTypeCode;
    }

    public function getDocumentCurrencyCode(): CurrencyCode
    {
        return $this->documentCurrencyCode;
    }

    public function getDueDate(): ?DueDate
    {
        return $this->dueDate;
    }

    public function setDueDate(?DueDate $dueDate): static
    {
        $this->dueDate = $dueDate;

        return $this;
    }

    public function getCustomizationID(): SpecificationIdentifier
    {
        return $this->customizationIdentifier;
    }

    public function getUblVersionIdentifier(): ?string
    {
        return $this->ublVersionIdentifier;
    }

    public function setUblVersionIdentifier(string $ublVersionIdentifier): static
    {
        $this->ublVersionIdentifier = $ublVersionIdentifier;

        return $this;
    }

    /**
     * @return array|Note[]
     */
    public function getNotes(): array
    {
        return $this->notes;
    }

    /**
     * @param array<int, Note> $notes
     *
     * @return $this
     */
    public function setNotes(array $notes): static
    {
        foreach ($notes as $note) {
            if (!$note instanceof Note) {
                throw new \TypeError();
            }
        }

        $this->notes = $notes;

        return $this;
    }

    public function getInvoicePeriod(): ?InvoicePeriod
    {
        return $this->invoicePeriod;
    }

    public function setInvoicePeriod(?InvoicePeriod $invoicePeriod): static
    {
        $this->invoicePeriod = $invoicePeriod;

        return $this;
    }

    public function getProfileIdentifier(): ?string
    {
        return $this->profileIdentifier;
    }

    public function setProfileIdentifier(?string $profileIdentifier): static
    {
        $this->profileIdentifier = $profileIdentifier;

        return $this;
    }

    /**
     * @return array|BillingReference[]
     */
    public function getBillingReferences(): array
    {
        return $this->billingReferences;
    }

    /**
     * @param array<int, BillingReference> $billingReferences
     *
     * @return $this
     */
    public function setBillingReferences(array $billingReferences): static
    {
        foreach ($billingReferences as $billingReference) {
            if (!$billingReference instanceof BillingReference) {
                throw new \TypeError();
            }
        }

        $this->billingReferences = $billingReferences;

        return $this;
    }

    public function getAccountingSupplierParty(): AccountingSupplierParty
    {
        return $this->accountingSupplierParty;
    }

    public function getAccountingCustomerParty(): AccountingCustomerParty
    {
        return $this->accountingCustomerParty;
    }

    public function getTaxRepresentativeParty(): ?TaxRepresentativeParty
    {
        return $this->taxRepresentativeParty;
    }

    public function setTaxRepresentativeParty(?TaxRepresentativeParty $taxRepresentativeParty): static
    {
        $this->taxRepresentativeParty = $taxRepresentativeParty;

        return $this;
    }

    public function getDelivery(): ?Delivery
    {
        return $this->delivery;
    }

    public function setDelivery(?Delivery $delivery): static
    {
        $this->delivery = $delivery;

        return $this;
    }

    /**
     * @return array|Allowance[]
     */
    public function getAllowances(): array
    {
        return $this->allowances;
    }

    /**
     * @param array<int, Allowance> $allowances
     *
     * @return $this
     */
    public function setAllowances(array $allowances): static
    {
        foreach ($allowances as $allowance) {
            if (!$allowance instanceof Allowance) {
                throw new \TypeError();
            }
        }

        $this->allowances = $allowances;

        return $this;
    }

    /**
     * @return array|Charge[]
     */
    public function getCharges(): array
    {
        return $this->charges;
    }

    /**
     * @param array<int, Charge> $charges
     *
     * @return $this
     */
    public function setCharges(array $charges): static
    {
        foreach ($charges as $charge) {
            if (!$charge instanceof Charge) {
                throw new \TypeError();
            }
        }

        $this->charges = $charges;

        return $this;
    }

    /**
     * @return array|TaxTotal[]
     */
    public function getTaxTotals(): array
    {
        return $this->taxTotals;
    }

    public function getLegalMonetaryTotal(): LegalMonetaryTotal
    {
        return $this->legalMonetaryTotal;
    }

    /**
     * @return InvoiceLine[]
     */
    public function getInvoiceLines(): array
    {
        return $this->invoiceLines;
    }

    public function toXML(): \DOMDocument
    {
        $document = new \DOMDocument('1.0', 'UTF-8');

        $universalBusinessLanguage = $document->createElement(self::XML_NODE);
        $universalBusinessLanguage->setAttribute(
            'xmlns:ubl',
            'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2'
        );
        $universalBusinessLanguage->setAttribute(
            'xmlns:cac',
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2'
        );
        $universalBusinessLanguage->setAttribute(
            'xmlns:cbc',
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2'
        );

        $root = $document->appendChild($universalBusinessLanguage);

        if (\is_string($this->ublVersionIdentifier)) {
            $root->appendChild($document->createElement('cbc:UBLVersionID', $this->ublVersionIdentifier));
        }
        $root->appendChild($document->createElement('cbc:CustomizationID', $this->customizationIdentifier->value));

        if (\is_string($this->profileIdentifier)) {
            $root->appendChild($document->createElement('cbc:ProfileID', $this->profileIdentifier));
        }
        $root->appendChild($document->createElement('cbc:ID', $this->identifier->value));
        $root->appendChild($this->issueDate->toXML($document));

        if ($this->dueDate instanceof DueDate) {
            $root->appendChild($this->dueDate->toXML($document));
        }
        $root->appendChild($document->createElement('cbc:InvoiceTypeCode', $this->invoiceTypeCode->value));

        foreach ($this->notes as $note) {
            $root->appendChild($note->toXML($document));
        }

        $root->appendChild($document->createElement('cbc:DocumentCurrencyCode', $this->documentCurrencyCode->value));

        if ($this->invoicePeriod instanceof InvoicePeriod) {
            $root->appendChild($this->invoicePeriod->toXML($document));
        }

        foreach ($this->billingReferences as $billingReference) {
            $root->appendChild($billingReference->toXML($document));
        }

        $root->appendChild($this->accountingSupplierParty->toXML($document));
        $root->appendChild($this->accountingCustomerParty->toXML($document));

        if ($this->taxRepresentativeParty instanceof TaxRepresentativeParty) {
            $root->appendChild($this->taxRepresentativeParty->toXML($document));
        }

        if ($this->delivery instanceof Delivery) {
            $root->appendChild($this->delivery->toXML($document));
        }

        foreach ($this->allowances as $allowance) {
            $root->appendChild($allowance->toXML($document));
        }

        foreach ($this->charges as $charge) {
            $root->appendChild($charge->toXML($document));
        }

        foreach ($this->taxTotals as $taxTotal) {
            $root->appendChild($taxTotal->toXML($document));
        }
        $root->appendChild($this->legalMonetaryTotal->toXML($document));

        foreach ($this->invoiceLines as $invoiceLine) {
            $root->appendChild($invoiceLine->toXML($document));
        }

        return $document;
    }

    public static function fromXML(\DOMDocument $document): self
    {
        $xpath = new \DOMXPath($document);
        $xpath->registerNamespace('ubl', 'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2');

        $universalBusinessLanguageElements = $xpath->query(\sprintf('//%s', self::XML_NODE));

        if (!$universalBusinessLanguageElements || 1 !== $universalBusinessLanguageElements->count()) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $universalBusinessLanguageElement */
        $universalBusinessLanguageElement = $universalBusinessLanguageElements->item(0);

        $identifierElements = $xpath->query('./cbc:ID', $universalBusinessLanguageElement);

        if (1 !== $identifierElements->count()) {
            throw new \Exception('Malformed');
        }

        $identifier = (string) $identifierElements->item(0)->nodeValue;

        $issueDate = IssueDate::fromXML($xpath, $universalBusinessLanguageElement);

        $invoiceTypeCodeElements = $xpath->query('./cbc:InvoiceTypeCode', $universalBusinessLanguageElement);

        if (1 !== $invoiceTypeCodeElements->count()) {
            throw new \Exception('Malformed');
        }
        $invoiceTypeCode = InvoiceTypeCode::tryFrom((string) $invoiceTypeCodeElements->item(0)->nodeValue);

        if (null === $invoiceTypeCode) {
            throw new \Exception('Wrong type code');
        }

        $documentCurrencyCodeElements = $xpath->query('./cbc:DocumentCurrencyCode', $universalBusinessLanguageElement);

        if (!$documentCurrencyCodeElements || 1 !== $documentCurrencyCodeElements->count()) {
            throw new \Exception('Malformed');
        }
        $documentCurrencyCode = CurrencyCode::tryFrom((string) $documentCurrencyCodeElements->item(0)->nodeValue);

        if (null === $documentCurrencyCode) {
            throw new \Exception('Wrong currency code');
        }

        $customizationIdentifierElements = $xpath->query('./cbc:CustomizationID', $universalBusinessLanguageElement);

        if (1 !== $customizationIdentifierElements->count()) {
            throw new \Exception('Malformed');
        }
        $customizationIdentifier = new SpecificationIdentifier((string) $customizationIdentifierElements->item(0)->nodeValue);

        $profileIdentifierElements = $xpath->query('./cbc:ProfileID', $universalBusinessLanguageElement);

        if ($profileIdentifierElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        if (1 === $profileIdentifierElements->count()) {
            $profileIdentifier = (string) $profileIdentifierElements->item(0)->nodeValue;
        }

        $invoicePeriod                = InvoicePeriod::fromXML($xpath, $universalBusinessLanguageElement);
        $dueDate                      = DueDate::fromXML($xpath, $universalBusinessLanguageElement);
        $notes                        = Note::fromXML($xpath, $universalBusinessLanguageElement);
        $billingReferences            = BillingReference::fromXML($xpath, $universalBusinessLanguageElement);
        $accountingSupplierParty      = AccountingSupplierParty::fromXML($xpath, $universalBusinessLanguageElement);
        $accountingCustomerParty      = AccountingCustomerParty::fromXML($xpath, $universalBusinessLanguageElement);
        $taxRepresentativeParty       = TaxRepresentativeParty::fromXML($xpath, $universalBusinessLanguageElement);
        $delivery                     = Delivery::fromXML($xpath, $universalBusinessLanguageElement);
        $ublVersionIdentifierElements = $xpath->query('./cbc:UBLVersionID', $universalBusinessLanguageElement);

        $allowanceChargeElements = $xpath->query('./cac:AllowanceCharge', $universalBusinessLanguageElement);

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

        $allowances         = Allowance::fromXML($xpath, $universalBusinessLanguageElement);
        $charges            = Charge::fromXML($xpath, $universalBusinessLanguageElement);
        $taxTotals          = TaxTotal::fromXML($xpath, $universalBusinessLanguageElement);
        $legalMonetaryTotal = LegalMonetaryTotal::fromXML($xpath, $universalBusinessLanguageElement);
        $invoiceLines       = InvoiceLine::fromXML($xpath, $universalBusinessLanguageElement);

        if ($ublVersionIdentifierElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        $universalBusinessLanguage = new self(
            new InvoiceIdentifier($identifier),
            $issueDate,
            $invoiceTypeCode,
            $documentCurrencyCode,
            $customizationIdentifier,
            $accountingSupplierParty,
            $accountingCustomerParty,
            $taxTotals,
            $legalMonetaryTotal,
            $invoiceLines
        );

        if (isset($profileIdentifier)) {
            $universalBusinessLanguage->setProfileIdentifier($profileIdentifier);
        }

        if ($invoicePeriod instanceof InvoicePeriod) {
            $universalBusinessLanguage->setInvoicePeriod($invoicePeriod);
        }

        if ($dueDate instanceof DueDate) {
            $universalBusinessLanguage->setDueDate($dueDate);
        }

        if (\count($notes) > 0) {
            $universalBusinessLanguage->setNotes($notes);
        }

        if (\count($billingReferences) > 0) {
            $universalBusinessLanguage->setBillingReferences($billingReferences);
        }

        if ($taxRepresentativeParty instanceof TaxRepresentativeParty) {
            $universalBusinessLanguage->setTaxRepresentativeParty($taxRepresentativeParty);
        }

        if ($delivery instanceof Delivery) {
            $universalBusinessLanguage->setDelivery($delivery);
        }

        if (\count($allowances) > 0) {
            $universalBusinessLanguage->setAllowances($allowances);
        }

        if (\count($charges) > 0) {
            $universalBusinessLanguage->setCharges($charges);
        }

        if (1 === $ublVersionIdentifierElements->count()) {
            $universalBusinessLanguage->setUblVersionIdentifier($ublVersionIdentifierElements->item(0)->nodeValue);
        }

        return $universalBusinessLanguage;
    }
}
