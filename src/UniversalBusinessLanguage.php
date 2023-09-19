<?php

declare(strict_types=1);

namespace Tiime\UniversalBusinessLanguage;

use Tiime\EN16931\DataType\CurrencyCode;
use Tiime\EN16931\DataType\Identifier\InvoiceIdentifier;
use Tiime\EN16931\DataType\Identifier\SpecificationIdentifier;
use Tiime\EN16931\DataType\InvoiceTypeCode;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\BillingReference;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\InvoicePeriod;
use Tiime\UniversalBusinessLanguage\DataType\Basic\DueDate;
use Tiime\UniversalBusinessLanguage\DataType\Basic\IssueDate;
use Tiime\UniversalBusinessLanguage\DataType\Basic\Note;

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
     * BT-14.
     */
    private ?InvoicePeriod $invoicePeriod;

    /**
     * BT-9-00.
     */
    private ?DueDate $dueDate;

    /**
     * BG-1-00.
     * en (0,1) conformément au format UBL mais en désaccord avec les specs 2.3 (0,n).
     */
    private ?Note $note;

    /**
     * BT-23.
     */
    private ?string $profileID;

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

    public function __construct(
        InvoiceIdentifier $identifier,
        IssueDate $issueDate,
        InvoiceTypeCode $invoiceTypeCode,
        CurrencyCode $documentCurrencyCode,
        SpecificationIdentifier $customizationID
    ) {
        $this->identifier           = $identifier;
        $this->issueDate            = $issueDate;
        $this->invoiceTypeCode      = $invoiceTypeCode;
        $this->documentCurrencyCode = $documentCurrencyCode;
        $this->customizationID      = $customizationID;
        $this->dueDate              = null;
        $this->invoicePeriod        = null;
        $this->note                 = null;
        $this->profileID            = null;
        $this->billingReferences    = [];
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

    public function getCustomizationID(): SpecificationIdentifier
    {
        return $this->customizationID;
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

    public function getProfileID(): ?string
    {
        return $this->profileID;
    }

    public function setProfileID(?string $profileID): static
    {
        $this->profileID = $profileID;

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

        if ($this->dueDate instanceof DueDate) {
            $root->appendChild($this->dueDate->toXML($document));
        }

        if ($this->invoicePeriod instanceof InvoicePeriod) {
            $root->appendChild($this->invoicePeriod->toXML($document));
        }

        if ($this->note instanceof Note) {
            $root->appendChild($this->note->toXML($document));
        }

        if ($this->profileID) {
            $root->appendChild($document->createElement('cbc:ProfileID', $this->profileID));
        }

        foreach ($this->billingReferences as $billingReference) {
            $root->appendChild($billingReference->toXML($document));
        }

        return $document;
    }

    public static function fromXML(\DOMDocument $document): self
    {
        $xpath = new \DOMXPath($document);

        $universalBusinessLanguageElements = $xpath->query(sprintf('//%s', self::XML_NODE));

        if (!$universalBusinessLanguageElements
            || !$universalBusinessLanguageElements->item(0)
            || 1 !== $universalBusinessLanguageElements->count()) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $universalBusinessLanguageElement */
        $universalBusinessLanguageElement = $universalBusinessLanguageElements->item(0);

        $identifierElements = $xpath->query('./cbc:ID', $universalBusinessLanguageElement);

        if (!$identifierElements || !$identifierElements->item(0)) {
            throw new \Exception('ID not found');
        }
        $identifier = (string) $identifierElements->item(0)->nodeValue;

        $issueDate = IssueDate::fromXML($xpath, $universalBusinessLanguageElement);

        $typeCodeElements = $xpath->query('./cbc:InvoiceTypeCode', $universalBusinessLanguageElement);

        if (!$typeCodeElements || !$typeCodeElements->item(0)) {
            throw new \Exception('Type Code not found');
        }
        $typeCode = InvoiceTypeCode::tryFrom((string) $typeCodeElements->item(0)->nodeValue);

        if (null === $typeCode) {
            throw new \Exception('Wrong type code');
        }

        $documentCurrencyCodeElements = $xpath->query('./cbc:DocumentCurrencyCode', $universalBusinessLanguageElement);

        if (!$documentCurrencyCodeElements || !$documentCurrencyCodeElements->item(0)) {
            throw new \Exception('Currency Code not found');
        }
        $documentCurrencyCode = CurrencyCode::tryFrom((string) $documentCurrencyCodeElements->item(0)->nodeValue);

        if (null === $documentCurrencyCode) {
            throw new \Exception('Wrong currency code');
        }

        $customizationIDElements = $xpath->query('./cbc:CustomizationID', $universalBusinessLanguageElement);

        if (!$customizationIDElements || !$customizationIDElements->item(0)) {
            throw new \Exception('Customization ID (BT-24) not found');
        }
        $customizationID = new SpecificationIdentifier((string) $customizationIDElements->item(0)->nodeValue);

        $universalBusinessLanguage = new self(new InvoiceIdentifier($identifier), $issueDate, $typeCode, $documentCurrencyCode, $customizationID);

        $dueDate = DueDate::fromXML($xpath, $universalBusinessLanguageElement);

        if ($dueDate instanceof DueDate) {
            $universalBusinessLanguage->setDueDate($dueDate);
        }

        $invoicePeriod = InvoicePeriod::fromXML($xpath, $universalBusinessLanguageElement);

        if ($invoicePeriod instanceof InvoicePeriod) {
            $universalBusinessLanguage->setInvoicePeriod($invoicePeriod);
        }

        $note = Note::fromXML($xpath, $universalBusinessLanguageElement);

        if ($note instanceof Note) {
            $universalBusinessLanguage->setNote($note);
        }

        $profileIDElements = $xpath->query('./cbc:ProfileID', $universalBusinessLanguageElement);

        if ($profileIDElements && $profileIDElements->item(0)) {
            $universalBusinessLanguage->setProfileID((string) $profileIDElements->item(0)->nodeValue);
        }

        $billingReferences = BillingReference::fromXML($xpath, $universalBusinessLanguageElement);

        if (\count($billingReferences) > 0) {
            $universalBusinessLanguage->setBillingReferences($billingReferences);
        }

        return $universalBusinessLanguage;
    }
}
