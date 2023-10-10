<?php

declare(strict_types=1);

namespace Tiime\UniversalBusinessLanguage;

use Tiime\EN16931\DataType\CurrencyCode;
use Tiime\EN16931\DataType\Identifier\InvoiceIdentifier;
use Tiime\EN16931\DataType\Identifier\SpecificationIdentifier;
use Tiime\EN16931\DataType\InvoiceTypeCode;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\AccountingCustomerParty;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\AccountingSupplierParty;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\AdditionalDocumentReference;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\Allowance;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\BillingReference;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\Charge;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\ContractDocumentReference;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\Delivery;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\DespatchDocumentReference;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\InvoiceLine;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\InvoicePeriod;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\LegalMonetaryTotal;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\OrderReference;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\OriginatorDocumentReference;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\PayeeParty;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\PaymentMeans;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\PaymentTerms;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\ProjectReference;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\ReceiptDocumentReference;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\TaxRepresentativeParty;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\TaxTotal;
use Tiime\UniversalBusinessLanguage\DataType\Basic\DueDate;
use Tiime\UniversalBusinessLanguage\DataType\Basic\IssueDate;
use Tiime\UniversalBusinessLanguage\DataType\Basic\TaxPointDate;

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
     * BT-6.
     */
    protected ?CurrencyCode $taxCurrencyCode;

    /**
     * BT-7.
     */
    private ?TaxPointDate $taxPointDate;

    /**
     * BT-8-00.
     */
    private ?InvoicePeriod $invoicePeriod;

    /**
     * BT-9-00.
     */
    private ?DueDate $dueDate;

    /**
     * BT-10.
     */
    private ?string $buyerReference;

    /**
     * BT-11-00.
     */
    private ?ProjectReference $projectReference;

    /**
     * BT-12-00.
     */
    private ?ContractDocumentReference $contractDocumentReference;

    /**
     * BT-13 et BT-14.
     */
    private ?OrderReference $orderReference;

    /**
     * BT-15-00.
     */
    private ?ReceiptDocumentReference $receiptDocumentReference;

    /**
     * BT-16-00.
     */
    private ?DespatchDocumentReference $despatchDocumentReference;

    /**
     * BT-17-00.
     */
    private ?OriginatorDocumentReference $originatorDocumentReference;

    /**
     * BT-19.
     */
    private ?string $accountingCost;

    /**
     * BG-1-00.
     * en (0,1) conformément au format UBL Peppol mais en désaccord avec les specs 2.3 (0,n) et la norme Oasis (2.x).
     */
    private ?string $note;

    /**
     * BT-23.
     * en (1,1) conformément au format UBL mais en désaccord avec les specs 2.3 (0,1).
     */
    private string $profileIdentifier;

    /**
     * BT-24.
     */
    private SpecificationIdentifier $customizationIdentifier;

    /**
     * BG-3.
     *
     * @var array<int, BillingReference>
     */
    private array $billingReferences;

    /**
     * BG-24.
     *
     * @var array<int, AdditionalDocumentReference>
     */
    private array $additionalDocumentReferences;

    /**
     * BG-4.
     */
    private AccountingSupplierParty $accountingSupplierParty;

    /**
     * BG-7.
     */
    private AccountingCustomerParty $accountingCustomerParty;

    /**
     * BG-10.
     */
    private ?PayeeParty $payeeParty;

    /**
     * BG-11.
     */
    private ?TaxRepresentativeParty $taxRepresentativeParty;

    /**
     * BG-13.
     */
    private ?Delivery $delivery;

    /**
     * BG-16.
     *
     * @var array<int, PaymentMeans>
     */
    private array $paymentMeans;

    private ?PaymentTerms $paymentTerms;

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
     * @var non-empty-array<int,InvoiceLine>
     */
    private array $invoiceLines;

    /**
     * @param non-empty-array<int,InvoiceLine> $invoiceLines
     */
    public function __construct(
        InvoiceIdentifier $identifier,
        IssueDate $issueDate,
        InvoiceTypeCode $invoiceTypeCode,
        CurrencyCode $documentCurrencyCode,
        SpecificationIdentifier $customizationIdentifier,
        string $profileIdentifier,
        AccountingSupplierParty $accountingSupplierParty,
        AccountingCustomerParty $accountingCustomerParty,
        array $taxTotals,
        LegalMonetaryTotal $legalMonetaryTotal,
        array $invoiceLines
    ) {
        if (0 === \count($taxTotals) || \count($taxTotals) > 2) {
            throw new \Exception('Malformed');
        }

        foreach ($taxTotals as $taxTotal) {
            if (!$taxTotal instanceof TaxTotal) {
                throw new \TypeError();
            }
        }

        if (0 === \count($invoiceLines)) {
            throw new \Exception('Malformed');
        }

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
        $this->profileIdentifier       = $profileIdentifier;
        $this->accountingSupplierParty = $accountingSupplierParty;
        $this->accountingCustomerParty = $accountingCustomerParty;
        $this->taxTotals               = $taxTotals;
        $this->legalMonetaryTotal      = $legalMonetaryTotal;

        $this->taxCurrencyCode              = null;
        $this->taxPointDate                 = null;
        $this->invoicePeriod                = null;
        $this->dueDate                      = null;
        $this->buyerReference               = null;
        $this->projectReference             = null;
        $this->contractDocumentReference    = null;
        $this->orderReference               = null;
        $this->receiptDocumentReference     = null;
        $this->despatchDocumentReference    = null;
        $this->originatorDocumentReference  = null;
        $this->accountingCost               = null;
        $this->note                         = null;
        $this->billingReferences            = [];
        $this->additionalDocumentReferences = [];
        $this->payeeParty                   = null;
        $this->taxRepresentativeParty       = null;
        $this->delivery                     = null;
        $this->paymentMeans                 = [];
        $this->paymentTerms                 = null;
        $this->allowances                   = [];
        $this->charges                      = [];
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

    public function getTaxCurrencyCode(): ?CurrencyCode
    {
        return $this->taxCurrencyCode;
    }

    public function setTaxCurrencyCode(?CurrencyCode $taxCurrencyCode): static
    {
        $this->taxCurrencyCode = $taxCurrencyCode;

        return $this;
    }

    public function getTaxPointDate(): ?TaxPointDate
    {
        return $this->taxPointDate;
    }

    public function setTaxPointDate(?TaxPointDate $taxPointDate): static
    {
        $this->taxPointDate = $taxPointDate;

        return $this;
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

    public function getBuyerReference(): ?string
    {
        return $this->buyerReference;
    }

    public function setBuyerReference(?string $buyerReference): static
    {
        $this->buyerReference = $buyerReference;

        return $this;
    }

    public function getProjectReference(): ?ProjectReference
    {
        return $this->projectReference;
    }

    public function setProjectReference(?ProjectReference $projectReference): static
    {
        $this->projectReference = $projectReference;

        return $this;
    }

    public function getContractDocumentReference(): ?ContractDocumentReference
    {
        return $this->contractDocumentReference;
    }

    public function setContractDocumentReference(?ContractDocumentReference $contractDocumentReference): static
    {
        $this->contractDocumentReference = $contractDocumentReference;

        return $this;
    }

    public function getOrderReference(): ?OrderReference
    {
        return $this->orderReference;
    }

    public function setOrderReference(?OrderReference $orderReference): static
    {
        $this->orderReference = $orderReference;

        return $this;
    }

    public function getReceiptDocumentReference(): ?ReceiptDocumentReference
    {
        return $this->receiptDocumentReference;
    }

    public function setReceiptDocumentReference(?ReceiptDocumentReference $receiptDocumentReference): static
    {
        $this->receiptDocumentReference = $receiptDocumentReference;

        return $this;
    }

    public function getDespatchDocumentReference(): ?DespatchDocumentReference
    {
        return $this->despatchDocumentReference;
    }

    public function setDespatchDocumentReference(?DespatchDocumentReference $despatchDocumentReference): static
    {
        $this->despatchDocumentReference = $despatchDocumentReference;

        return $this;
    }

    public function getOriginatorDocumentReference(): ?OriginatorDocumentReference
    {
        return $this->originatorDocumentReference;
    }

    public function setOriginatorDocumentReference(?OriginatorDocumentReference $originatorDocumentReference): static
    {
        $this->originatorDocumentReference = $originatorDocumentReference;

        return $this;
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

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): static
    {
        $this->note = $note;

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

    public function getProfileIdentifier(): string
    {
        return $this->profileIdentifier;
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

    /**
     * @return array|AdditionalDocumentReference[]
     */
    public function getadditionalDocumentReferences(): array
    {
        return $this->additionalDocumentReferences;
    }

    /**
     * @param array<int, AdditionalDocumentReference> $additionalDocumentReferences
     *
     * @return $this
     */
    public function setAdditionalDocumentReferences(array $additionalDocumentReferences): static
    {
        foreach ($additionalDocumentReferences as $additionalDocumentReference) {
            if (!$additionalDocumentReference instanceof AdditionalDocumentReference) {
                throw new \TypeError();
            }
        }

        $this->additionalDocumentReferences = $additionalDocumentReferences;

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

    public function getPayeeParty(): ?PayeeParty
    {
        return $this->payeeParty;
    }

    public function setPayeeParty(?PayeeParty $payeeParty): static
    {
        $this->payeeParty = $payeeParty;

        return $this;
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
     * @return array|PaymentMeans[]
     */
    public function getPaymentMeans(): array
    {
        return $this->paymentMeans;
    }

    /**
     * @param array<int, PaymentMeans> $paymentMeans
     *
     * @return $this
     */
    public function setPaymentMeans(array $paymentMeans): static
    {
        foreach ($paymentMeans as $paymentMean) {
            if (!$paymentMean instanceof PaymentMeans) {
                throw new \TypeError();
            }
        }

        $this->paymentMeans = $paymentMeans;

        return $this;
    }

    public function getPaymentTerms(): ?PaymentTerms
    {
        return $this->paymentTerms;
    }

    public function setPaymentTerms(?PaymentTerms $paymentTerms): static
    {
        $this->paymentTerms = $paymentTerms;

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

        $this->allowances = $charges;

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
            'xmlns',
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

        $root->appendChild($document->createElement('cbc:UBLVersionID', '2.1'));
        $root->appendChild($document->createElement('cbc:ID', $this->identifier->value));
        $root->appendChild($this->issueDate->toXML($document));
        $root->appendChild($document->createElement('cbc:InvoiceTypeCode', $this->invoiceTypeCode->value));
        $root->appendChild($document->createElement('cbc:DocumentCurrencyCode', $this->documentCurrencyCode->value));
        $root->appendChild($document->createElement('cbc:CustomizationID', $this->customizationIdentifier->value));
        $root->appendChild($document->createElement('cbc:ProfileID', $this->profileIdentifier));
        $root->appendChild($this->accountingSupplierParty->toXML($document));
        $root->appendChild($this->accountingCustomerParty->toXML($document));
        $root->appendChild($this->legalMonetaryTotal->toXML($document));

        if ($this->taxCurrencyCode instanceof CurrencyCode) {
            $root->appendChild($document->createElement('cbc:TaxCurrencyCode', $this->taxCurrencyCode->value));
        }

        if ($this->taxPointDate instanceof TaxPointDate) {
            $root->appendChild($this->taxPointDate->toXML($document));
        }

        if ($this->invoicePeriod instanceof InvoicePeriod) {
            $root->appendChild($this->invoicePeriod->toXML($document));
        }

        if ($this->dueDate instanceof DueDate) {
            $root->appendChild($this->dueDate->toXML($document));
        }

        if (\is_string($this->buyerReference)) {
            $root->appendChild($document->createElement('cbc:BuyerReference', $this->buyerReference));
        }

        if ($this->projectReference instanceof ProjectReference) {
            $root->appendChild($this->projectReference->toXML($document));
        }

        if ($this->contractDocumentReference instanceof ContractDocumentReference) {
            $root->appendChild($this->contractDocumentReference->toXML($document));
        }

        if ($this->orderReference instanceof OrderReference) {
            $root->appendChild($this->orderReference->toXML($document));
        }

        if ($this->receiptDocumentReference instanceof ReceiptDocumentReference) {
            $root->appendChild($this->receiptDocumentReference->toXML($document));
        }

        if ($this->despatchDocumentReference instanceof DespatchDocumentReference) {
            $root->appendChild($this->despatchDocumentReference->toXML($document));
        }

        if ($this->originatorDocumentReference instanceof OriginatorDocumentReference) {
            $root->appendChild($this->originatorDocumentReference->toXML($document));
        }

        if (\is_string($this->accountingCost)) {
            $root->appendChild($document->createElement('cbc:AccountingCost', $this->accountingCost));
        }

        if (\is_string($this->note)) {
            $root->appendChild($document->createElement('cbc:Note', $this->note));
        }

        foreach ($this->billingReferences as $billingReference) {
            $root->appendChild($billingReference->toXML($document));
        }

        foreach ($this->additionalDocumentReferences as $additionalDocumentReference) {
            $root->appendChild($additionalDocumentReference->toXML($document));
        }

        if ($this->payeeParty instanceof PayeeParty) {
            $root->appendChild($this->payeeParty->toXML($document));
        }

        if ($this->taxRepresentativeParty instanceof TaxRepresentativeParty) {
            $root->appendChild($this->taxRepresentativeParty->toXML($document));
        }

        if ($this->delivery instanceof Delivery) {
            $root->appendChild($this->delivery->toXML($document));
        }

        foreach ($this->paymentMeans as $paymentMean) {
            $root->appendChild($paymentMean->toXML($document));
        }

        if ($this->paymentTerms instanceof PaymentTerms) {
            $root->appendChild($this->paymentTerms->toXML($document));
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

        foreach ($this->invoiceLines as $invoiceLine) {
            $root->appendChild($invoiceLine->toXML($document));
        }

        return $document;
    }

    public static function fromXML(\DOMDocument $document): self
    {
        $xpath = new \DOMXPath($document);

        $universalBusinessLanguageElements = $xpath->query(sprintf('//%s', self::XML_NODE));

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

        if (1 !== $profileIdentifierElements->count()) {
            throw new \Exception('Malformed');
        }
        $profileIdentifier = (string) $profileIdentifierElements->item(0)->nodeValue;

        $taxCurrencyCodeElements      = $xpath->query('./cbc:TaxCurrencyCode', $universalBusinessLanguageElement);
        $taxPointDate                 = TaxPointDate::fromXML($xpath, $universalBusinessLanguageElement);
        $invoicePeriod                = InvoicePeriod::fromXML($xpath, $universalBusinessLanguageElement);
        $dueDate                      = DueDate::fromXML($xpath, $universalBusinessLanguageElement);
        $buyerReferenceElements       = $xpath->query('./cbc:BuyerReference', $universalBusinessLanguageElement);
        $projectReference             = ProjectReference::fromXML($xpath, $universalBusinessLanguageElement);
        $contractDocumentReference    = ContractDocumentReference::fromXML($xpath, $universalBusinessLanguageElement);
        $orderReference               = OrderReference::fromXML($xpath, $universalBusinessLanguageElement);
        $receiptDocumentReference     = ReceiptDocumentReference::fromXML($xpath, $universalBusinessLanguageElement);
        $despatchDocumentReference    = DespatchDocumentReference::fromXML($xpath, $universalBusinessLanguageElement);
        $originatorDocumentReference  = OriginatorDocumentReference::fromXML($xpath, $universalBusinessLanguageElement);
        $accountingCostElements       = $xpath->query('./cbc:AccountingCost', $universalBusinessLanguageElement);
        $noteElements                 = $xpath->query('./cbc:Note', $universalBusinessLanguageElement);
        $billingReferences            = BillingReference::fromXML($xpath, $universalBusinessLanguageElement);
        $additionalDocumentReferences = AdditionalDocumentReference::fromXML($xpath, $universalBusinessLanguageElement);
        $accountingSupplierParty      = AccountingSupplierParty::fromXML($xpath, $universalBusinessLanguageElement);
        $accountingCustomerParty      = AccountingCustomerParty::fromXML($xpath, $universalBusinessLanguageElement);
        $payeeParty                   = PayeeParty::fromXML($xpath, $universalBusinessLanguageElement);
        $taxRepresentativeParty       = TaxRepresentativeParty::fromXML($xpath, $universalBusinessLanguageElement);
        $delivery                     = Delivery::fromXML($xpath, $universalBusinessLanguageElement);
        $paymentMeans                 = PaymentMeans::fromXML($xpath, $universalBusinessLanguageElement);
        $paymentTerms                 = PaymentTerms::fromXML($xpath, $universalBusinessLanguageElement);
        $allowances                   = Allowance::fromXML($xpath, $universalBusinessLanguageElement);
        $charges                      = Charge::fromXML($xpath, $universalBusinessLanguageElement);
        $taxTotals                    = TaxTotal::fromXML($xpath, $universalBusinessLanguageElement);
        $legalMonetaryTotal           = LegalMonetaryTotal::fromXML($xpath, $universalBusinessLanguageElement);
        $invoiceLines                 = InvoiceLine::fromXML($xpath, $universalBusinessLanguageElement);

        if ($taxCurrencyCodeElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        if ($buyerReferenceElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        if ($accountingCostElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        if ($noteElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        $taxCurrencyCode = null;

        if (1 === $taxCurrencyCodeElements->count()) {
            $taxCurrencyCode = CurrencyCode::tryFrom((string) $taxCurrencyCodeElements->item(0)->nodeValue);

            if (null === $taxCurrencyCode) {
                throw new \Exception('Wrong TaxCurrencyCode');
            }
        }

        $universalBusinessLanguage = new self(
            new InvoiceIdentifier($identifier),
            $issueDate,
            $invoiceTypeCode,
            $documentCurrencyCode,
            $customizationIdentifier,
            $profileIdentifier,
            $accountingSupplierParty,
            $accountingCustomerParty,
            $taxTotals,
            $legalMonetaryTotal,
            $invoiceLines
        );

        if ($taxCurrencyCode instanceof CurrencyCode) {
            $universalBusinessLanguage->setTaxCurrencyCode($taxCurrencyCode);
        }

        if ($taxPointDate instanceof TaxPointDate) {
            $universalBusinessLanguage->setTaxPointDate($taxPointDate);
        }

        if ($invoicePeriod instanceof InvoicePeriod) {
            $universalBusinessLanguage->setInvoicePeriod($invoicePeriod);
        }

        if ($dueDate instanceof DueDate) {
            $universalBusinessLanguage->setDueDate($dueDate);
        }

        if (1 === $buyerReferenceElements->count()) {
            $universalBusinessLanguage->setBuyerReference($buyerReferenceElements->item(0)->nodeValue);
        }

        if (1 === $noteElements->count()) {
            $universalBusinessLanguage->setNote($noteElements->item(0)->nodeValue);
        }

        if ($projectReference instanceof ProjectReference) {
            $universalBusinessLanguage->setProjectReference($projectReference);
        }

        if ($contractDocumentReference instanceof ContractDocumentReference) {
            $universalBusinessLanguage->setContractDocumentReference($contractDocumentReference);
        }

        if ($orderReference instanceof OrderReference) {
            $universalBusinessLanguage->setOrderReference($orderReference);
        }

        if ($receiptDocumentReference instanceof ReceiptDocumentReference) {
            $universalBusinessLanguage->setReceiptDocumentReference($receiptDocumentReference);
        }

        if ($despatchDocumentReference instanceof DespatchDocumentReference) {
            $universalBusinessLanguage->setDespatchDocumentReference($despatchDocumentReference);
        }

        if ($originatorDocumentReference instanceof OriginatorDocumentReference) {
            $universalBusinessLanguage->setOriginatorDocumentReference($originatorDocumentReference);
        }

        if (1 === $accountingCostElements->count()) {
            $universalBusinessLanguage->setAccountingCost($accountingCostElements->item(0)->nodeValue);
        }

        if (\count($billingReferences) > 0) {
            $universalBusinessLanguage->setBillingReferences($billingReferences);
        }

        if (\count($additionalDocumentReferences) > 0) {
            $universalBusinessLanguage->setAdditionalDocumentReferences($additionalDocumentReferences);
        }

        if ($payeeParty instanceof PayeeParty) {
            $universalBusinessLanguage->setPayeeParty($payeeParty);
        }

        if ($taxRepresentativeParty instanceof TaxRepresentativeParty) {
            $universalBusinessLanguage->setTaxRepresentativeParty($taxRepresentativeParty);
        }

        if ($delivery instanceof Delivery) {
            $universalBusinessLanguage->setDelivery($delivery);
        }

        if (\count($paymentMeans) > 0) {
            $universalBusinessLanguage->setPaymentMeans($paymentMeans);
        }

        if ($paymentTerms instanceof PaymentTerms) {
            $universalBusinessLanguage->setPaymentTerms($paymentTerms);
        }

        if (\count($allowances) > 0) {
            $universalBusinessLanguage->setAllowances($allowances);
        }

        if (\count($charges) > 0) {
            $universalBusinessLanguage->setCharges($charges);
        }

        return $universalBusinessLanguage;
    }
}
