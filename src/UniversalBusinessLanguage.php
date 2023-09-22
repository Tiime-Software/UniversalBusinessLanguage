<?php

declare(strict_types=1);

namespace Tiime\UniversalBusinessLanguage;

use Tiime\EN16931\DataType\CurrencyCode;
use Tiime\EN16931\DataType\Identifier\InvoiceIdentifier;
use Tiime\EN16931\DataType\Identifier\SpecificationIdentifier;
use Tiime\EN16931\DataType\InvoiceTypeCode;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\AdditionalDocumentReference;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\BillingReference;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\ContractDocumentReference;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\DespatchDocumentReference;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\InvoicePeriod;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\OrderReference;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\OriginatorDocumentReference;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\ProjectReference;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\ReceiptDocumentReference;
use Tiime\UniversalBusinessLanguage\DataType\Basic\DueDate;
use Tiime\UniversalBusinessLanguage\DataType\Basic\IssueDate;
use Tiime\UniversalBusinessLanguage\DataType\Basic\Note;
use Tiime\UniversalBusinessLanguage\DataType\Basic\TaxPointDate;

class UniversalBusinessLanguage implements UniversalBusinessLanguageInterface
{
    private const XML_NODE = 'Invoice';

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
     * Pas dans les specs 2.3.
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
     * en (0,1) conformément au format UBL mais en désaccord avec les specs 2.3 (0,n).
     */
    private ?Note $note;

    /**
     * BT-23.
     * en (1,1) conformément au format UBL mais en désaccord avec les specs 2.3 (0,1).
     */
    private string $profileIdentifier;

    /**
     * BT-24.
     */
    private SpecificationIdentifier $customizationID;

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

    public function __construct(
        InvoiceIdentifier $identifier,
        IssueDate $issueDate,
        InvoiceTypeCode $invoiceTypeCode,
        CurrencyCode $documentCurrencyCode,
        SpecificationIdentifier $customizationID,
        string $profileIdentifier
    ) {
        $this->identifier           = $identifier;
        $this->issueDate            = $issueDate;
        $this->invoiceTypeCode      = $invoiceTypeCode;
        $this->documentCurrencyCode = $documentCurrencyCode;
        $this->customizationID      = $customizationID;
        $this->profileIdentifier    = $profileIdentifier;

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

    public function setTaxCurrencyCode(?CurrencyCode $taxCurrencyCode): void
    {
        $this->taxCurrencyCode = $taxCurrencyCode;
    }

    public function getTaxPointDate(): ?TaxPointDate
    {
        return $this->taxPointDate;
    }

    public function setTaxPointDate(?TaxPointDate $taxPointDate): void
    {
        $this->taxPointDate = $taxPointDate;
    }

    public function getDueDate(): ?DueDate
    {
        return $this->dueDate;
    }

    public function getCustomizationID(): SpecificationIdentifier
    {
        return $this->customizationID;
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

    public function setProjectReference(?ProjectReference $projectReference): void
    {
        $this->projectReference = $projectReference;
    }

    public function getContractDocumentReference(): ?ContractDocumentReference
    {
        return $this->contractDocumentReference;
    }

    public function setContractDocumentReference(?ContractDocumentReference $contractDocumentReference): void
    {
        $this->contractDocumentReference = $contractDocumentReference;
    }

    public function getOrderReference(): ?OrderReference
    {
        return $this->orderReference;
    }

    public function setOrderReference(?OrderReference $orderReference): void
    {
        $this->orderReference = $orderReference;
    }

    public function getReceiptDocumentReference(): ?ReceiptDocumentReference
    {
        return $this->receiptDocumentReference;
    }

    public function setReceiptDocumentReference(?ReceiptDocumentReference $receiptDocumentReference): void
    {
        $this->receiptDocumentReference = $receiptDocumentReference;
    }

    public function getDespatchDocumentReference(): ?DespatchDocumentReference
    {
        return $this->despatchDocumentReference;
    }

    public function setDespatchDocumentReference(?DespatchDocumentReference $despatchDocumentReference): void
    {
        $this->despatchDocumentReference = $despatchDocumentReference;
    }

    public function getOriginatorDocumentReference(): ?OriginatorDocumentReference
    {
        return $this->originatorDocumentReference;
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

    public function setOriginatorDocumentReference(?OriginatorDocumentReference $originatorDocumentReference): void
    {
        $this->originatorDocumentReference = $originatorDocumentReference;
    }

    public function getNote(): ?Note
    {
        return $this->note;
    }

    public function setNote(?Note $note): static
    {
        $this->note = $note;

        return $this;
    }

    public function setDueDate(?DueDate $dueDate): static
    {
        $this->dueDate = $dueDate;

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
        $tmpBillingReference = [];

        foreach ($billingReferences as $billingReference) {
            if (!$billingReference instanceof BillingReference) {
                throw new \TypeError();
            }

            $tmpBillingReference[] = $billingReference;
        }

        $this->billingReferences = $tmpBillingReference;

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
        $tmpAdditionalDocumentReference = [];

        foreach ($additionalDocumentReferences as $additionalDocumentReference) {
            if (!$additionalDocumentReference instanceof AdditionalDocumentReference) {
                throw new \TypeError();
            }

            $tmpAdditionalDocumentReference[] = $additionalDocumentReference;
        }

        $this->additionalDocumentReferences = $tmpAdditionalDocumentReference;

        return $this;
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
        $root->appendChild($document->createElement('cbc:CustomizationID', $this->customizationID->value));
        $root->appendChild($document->createElement('cbc:ProfileID', $this->profileIdentifier));

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

        if ($this->note instanceof Note) {
            $root->appendChild($this->note->toXML($document));
        }

        foreach ($this->billingReferences as $billingReference) {
            $root->appendChild($billingReference->toXML($document));
        }

        foreach ($this->additionalDocumentReferences as $additionalDocumentReference) {
            $root->appendChild($additionalDocumentReference->toXML($document));
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

        if (!$identifierElements || 1 !== $identifierElements->count()) {
            throw new \Exception('Malformed');
        }

        $identifier = (string) $identifierElements->item(0)->nodeValue;

        $issueDate = IssueDate::fromXML($xpath, $universalBusinessLanguageElement);

        $typeCodeElements = $xpath->query('./cbc:InvoiceTypeCode', $universalBusinessLanguageElement);

        if (!$typeCodeElements || 1 !== $typeCodeElements->count()) {
            throw new \Exception('Malformed');
        }
        $typeCode = InvoiceTypeCode::tryFrom((string) $typeCodeElements->item(0)->nodeValue);

        if (null === $typeCode) {
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

        $customizationIDElements = $xpath->query('./cbc:CustomizationID', $universalBusinessLanguageElement);

        if (!$customizationIDElements || 1 !== $customizationIDElements->count()) {
            throw new \Exception('Malformed');
        }
        $customizationID = new SpecificationIdentifier((string) $customizationIDElements->item(0)->nodeValue);

        $profileIdentifierElements = $xpath->query('./cbc:ProfileID', $universalBusinessLanguageElement);

        if (!$profileIdentifierElements || 1 !== $profileIdentifierElements->count()) {
            throw new \Exception('Malformed');
        }
        $profileIdentifier = (string) $profileIdentifierElements->item(0)->nodeValue;

        $taxCurrencyCodeElements      = $xpath->query('cbc:TaxCurrencyCode', $universalBusinessLanguageElement);
        $taxPointDate                 = TaxPointDate::fromXML($xpath, $universalBusinessLanguageElement);
        $invoicePeriod                = InvoicePeriod::fromXML($xpath, $universalBusinessLanguageElement);
        $dueDate                      = DueDate::fromXML($xpath, $universalBusinessLanguageElement);
        $buyerReferenceElements       = $xpath->query('cbc:BuyerReference', $universalBusinessLanguageElement);
        $projectReference             = ProjectReference::fromXML($xpath, $universalBusinessLanguageElement);
        $contractDocumentReference    = ContractDocumentReference::fromXML($xpath, $universalBusinessLanguageElement);
        $orderReference               = OrderReference::fromXML($xpath, $universalBusinessLanguageElement);
        $receiptDocumentReference     = ReceiptDocumentReference::fromXML($xpath, $universalBusinessLanguageElement);
        $despatchDocumentReference    = DespatchDocumentReference::fromXML($xpath, $universalBusinessLanguageElement);
        $originatorDocumentReference  = OriginatorDocumentReference::fromXML($xpath, $universalBusinessLanguageElement);
        $accountingCostElements       = $xpath->query('cbc:AccountingCost', $universalBusinessLanguageElement);
        $note                         = Note::fromXML($xpath, $universalBusinessLanguageElement);
        $billingReferences            = BillingReference::fromXML($xpath, $universalBusinessLanguageElement);
        $additionalDocumentReferences = AdditionalDocumentReference::fromXML($xpath, $universalBusinessLanguageElement);

        if ($taxCurrencyCodeElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        if ($buyerReferenceElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        if ($accountingCostElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        $taxCurrencyCode = null;

        if (1 === $taxCurrencyCodeElements->count()) {
            $taxCurrencyCode = CurrencyCode::tryFrom($taxCurrencyCodeElements->item(0)->nodeValue);

            if (null === $taxCurrencyCode) {
                throw new \Exception('Wrong TaxCurrencyCode');
            }
        }

        $universalBusinessLanguage = new self(new InvoiceIdentifier($identifier), $issueDate, $typeCode, $documentCurrencyCode, $customizationID, $profileIdentifier);

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

        if ($note instanceof Note) {
            $universalBusinessLanguage->setNote($note);
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

        return $universalBusinessLanguage;
    }
}
