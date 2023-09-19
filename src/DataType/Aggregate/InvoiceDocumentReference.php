<?php

declare(strict_types=1);

namespace Tiime\UniversalBusinessLanguage\DataType\Aggregate;

use Tiime\EN16931\DataType\Reference\PrecedingInvoiceReference;
use Tiime\UniversalBusinessLanguage\DataType\Basic\InvoiceDocumentReferenceIssueDate;

/**
 * BG-3-0.
 */
class InvoiceDocumentReference
{
    protected const XML_NODE = 'cac:InvoiceDocumentReference';

    /**
     * BT-25.
     */
    private PrecedingInvoiceReference $issuerAssignedIdentifier;

    /**
     * BT-26-00.
     */
    private ?InvoiceDocumentReferenceIssueDate $issueDate;

    public function __construct(PrecedingInvoiceReference $issuerAssignedIdentifier)
    {
        $this->issuerAssignedIdentifier = $issuerAssignedIdentifier;
        $this->issueDate                = null;
    }

    public function getIssuerAssignedIdentifier(): PrecedingInvoiceReference
    {
        return $this->issuerAssignedIdentifier;
    }

    public function getIssueDate(): ?InvoiceDocumentReferenceIssueDate
    {
        return $this->issueDate;
    }

    public function setIssueDate(?InvoiceDocumentReferenceIssueDate $issueDate): static
    {
        $this->issueDate = $issueDate;

        return $this;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $element = $document->createElement(self::XML_NODE);

        $element->appendChild($document->createElement('cbc:ID', $this->issuerAssignedIdentifier->value));

        if ($this->issueDate instanceof InvoiceDocumentReferenceIssueDate) {
            $element->appendChild($this->issueDate->toXML($document));
        }

        return $element;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): self
    {
        $invoiceReferencedDocumentElements = $xpath->query(sprintf('./%s', self::XML_NODE), $currentElement);

        if (!$invoiceReferencedDocumentElements
            || !$invoiceReferencedDocumentElements->item(0)
            || 0 === $invoiceReferencedDocumentElements->count()) {
            throw new \Exception('No Invoice Document Reference found');
        }

        if ($invoiceReferencedDocumentElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $invoiceReferencedDocumentElement */
        $invoiceReferencedDocumentElement = $invoiceReferencedDocumentElements->item(0);

        $issuerAssignedIdentifierElements = $xpath->query('./cbc:ID', $invoiceReferencedDocumentElement);

        if (!$issuerAssignedIdentifierElements
            || !$issuerAssignedIdentifierElements->item(0)
            || 1 !== $issuerAssignedIdentifierElements->count()) {
            throw new \Exception('Malformed');
        }

        $issuerAssignedIdentifier = (string) $issuerAssignedIdentifierElements->item(0)->nodeValue;

        $invoiceReferencedDocument = new self(new PrecedingInvoiceReference($issuerAssignedIdentifier));

        $issueDate = InvoiceDocumentReferenceIssueDate::fromXML($xpath, $invoiceReferencedDocumentElement);

        if ($issueDate instanceof InvoiceDocumentReferenceIssueDate) {
            $invoiceReferencedDocument->setIssueDate($issueDate);
        }

        return $invoiceReferencedDocument;
    }
}
