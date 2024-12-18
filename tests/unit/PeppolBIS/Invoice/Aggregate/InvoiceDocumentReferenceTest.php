<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\PeppolBIS\Invoice\Aggregate;

use Tiime\EN16931\DataType\Reference\PrecedingInvoiceReference;
use Tiime\UniversalBusinessLanguage\PeppolBIS\Invoice\DataType\Aggregate\InvoiceDocumentReference;
use Tiime\UniversalBusinessLanguage\PeppolBIS\Invoice\DataType\Basic\InvoiceDocumentReferenceIssueDate;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class InvoiceDocumentReferenceTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_FULL_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:InvoiceDocumentReference>
    <cbc:ID>inv123</cbc:ID>
    <cbc:IssueDate>2017-09-15</cbc:IssueDate>
  </cac:InvoiceDocumentReference>
</Invoice>
XML;

    protected const XML_VALID_MINIMAL_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:InvoiceDocumentReference>
    <cbc:ID>inv123</cbc:ID>
  </cac:InvoiceDocumentReference>
</Invoice>
XML;

    protected const XML_INVALID_NO_LINE = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</Invoice>
XML;

    protected const XML_INVALID_MANY_LINES = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:InvoiceDocumentReference>
    <cbc:ID>inv123</cbc:ID>
  </cac:InvoiceDocumentReference>
  <cac:InvoiceDocumentReference>
    <cbc:ID>inv456</cbc:ID>
  </cac:InvoiceDocumentReference>
</Invoice>
XML;

    public function testCanBeCreatedFromFullContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject      = InvoiceDocumentReference::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(InvoiceDocumentReference::class, $ublObject);
        $this->assertInstanceOf(InvoiceDocumentReferenceIssueDate::class, $ublObject->getIssueDate());
        $this->assertInstanceOf(PrecedingInvoiceReference::class, $ublObject->getIssuerAssignedIdentifier());
    }

    public function testCanBeCreatedFromMinimalContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_MINIMAL_CONTENT);
        $ublObject      = InvoiceDocumentReference::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(InvoiceDocumentReference::class, $ublObject);
        $this->assertNull($ublObject->getIssueDate());
        $this->assertInstanceOf(PrecedingInvoiceReference::class, $ublObject->getIssuerAssignedIdentifier());
    }

    public function testCannotBeCreatedFromNoLine(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_NO_LINE);
        InvoiceDocumentReference::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromManyLines(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MANY_LINES);
        InvoiceDocumentReference::fromXML($this->xpath, $currentElement);
    }

    public function testGenerateXml(): void
    {
        $currentElement  = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject       = InvoiceDocumentReference::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyInvoiceRootDocument();
        $rootDestination->appendChild($ublObject->toXML($this->document));
        $generatedOutput = $this->formatXMLOutput();
        $this->assertStringEqualsStringIgnoringLineEndings(self::XML_VALID_FULL_CONTENT, $generatedOutput);
    }
}
