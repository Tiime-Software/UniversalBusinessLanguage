<?php

declare(strict_types=1);

namespace Tiime\UniversalBusinessLanguage;

use Tiime\EN16931\DataType\Identifier\InvoiceIdentifier;
use Tiime\EN16931\DataType\InvoiceTypeCode;
use Tiime\UniversalBusinessLanguage\DataType\Basic\IssueDateTime;

class UniversalBusinessLanguage implements UniversalBusinessLanguageInterface
{
    protected const XML_NODE = 'Invoice';

    /**
     * BT-1.
     */
    private InvoiceIdentifier $identifier;

    /**
     * BT-2-00.
     */
    private IssueDateTime $issueDate;

    /**
     * BT-3.
     */
    private InvoiceTypeCode $invoiceTypeCode;

    public function __construct(
        InvoiceIdentifier $identifier,
        IssueDateTime $issueDate,
        InvoiceTypeCode $invoiceTypeCode
    ) {
        $this->identifier      = $identifier;
        $this->issueDate       = $issueDate;
        $this->invoiceTypeCode = $invoiceTypeCode;
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
        if (!$identifierElements || !$identifierElements->item(0)){
            throw new \Exception('ID not found');
        }
        $identifier         = (string) $identifierElements->item(0)->nodeValue;

        $issueDate = IssueDateTime::fromXML($xpath, $universalBusinessLanguageElement);

        $typeCodeElements = $xpath->query('./cbc:InvoiceTypeCode', $universalBusinessLanguageElement);
        if (!$typeCodeElements || !$typeCodeElements->item(0)){
            throw new \Exception('Type Code not found');
        }
        $typeCode         = InvoiceTypeCode::tryFrom((string) $typeCodeElements->item(0)->nodeValue);

        if (null === $typeCode) {
            throw new \Exception('Wrong currency code');
        }

        return new self(new InvoiceIdentifier($identifier), $issueDate, $typeCode);
    }
}
