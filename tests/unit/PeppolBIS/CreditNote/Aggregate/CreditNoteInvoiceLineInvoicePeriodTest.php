<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\PeppolBIS\CreditNote\Aggregate;

use Tiime\UniversalBusinessLanguage\PeppolBIS\CreditNote\DataType\Aggregate\InvoiceLineInvoicePeriod;
use Tiime\UniversalBusinessLanguage\PeppolBIS\CreditNote\DataType\Basic\EndDate;
use Tiime\UniversalBusinessLanguage\PeppolBIS\CreditNote\DataType\Basic\StartDate;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class CreditNoteInvoiceLineInvoicePeriodTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_FULL_CONTENT = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:InvoicePeriod>
    <cbc:StartDate>2017-10-05</cbc:StartDate>
    <cbc:EndDate>2017-10-15</cbc:EndDate>
  </cac:InvoicePeriod>
</CreditNote>
XML;

    protected const XML_VALID_MINIMAL_CONTENT = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:InvoicePeriod>
  </cac:InvoicePeriod>
</CreditNote>
XML;

    protected const XML_VALID_NO_LINE = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</CreditNote>
XML;

    protected const XML_INVALID_MANY_CONTENTS = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:InvoicePeriod>
    <cbc:StartDate>2017-10-05</cbc:StartDate>
    <cbc:StartDate>2017-10-05</cbc:StartDate>
    <cbc:EndDate>2017-10-15</cbc:EndDate>
  </cac:InvoicePeriod>
</CreditNote>
XML;

    protected const XML_INVALID_MANY_LINES = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:InvoicePeriod>
    <cbc:StartDate>2017-10-05</cbc:StartDate>
    <cbc:EndDate>2017-10-15</cbc:EndDate>
  </cac:InvoicePeriod>
  <cac:InvoicePeriod>
    <cbc:StartDate>2017-10-05</cbc:StartDate>
    <cbc:EndDate>2017-10-15</cbc:EndDate>
  </cac:InvoicePeriod>
</CreditNote>
XML;

    public function testCanBeCreatedFromFullContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject      = InvoiceLineInvoicePeriod::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(InvoiceLineInvoicePeriod::class, $ublObject);
        $this->assertInstanceOf(StartDate::class, $ublObject->getStartDate());
        $this->assertInstanceOf(EndDate::class, $ublObject->getEndDate());
    }

    public function testCanBeCreatedFromMinimalContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_MINIMAL_CONTENT);
        $ublObject      = InvoiceLineInvoicePeriod::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(InvoiceLineInvoicePeriod::class, $ublObject);
        $this->assertNull($ublObject->getStartDate());
        $this->assertNull($ublObject->getEndDate());
    }

    public function testCanBeCreatedFromNoLine(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_NO_LINE);
        $ublObject      = InvoiceLineInvoicePeriod::fromXML($this->xpath, $currentElement);
        $this->assertNull($ublObject);
    }

    public function testCannotBeCreatedFromManyContents(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MANY_CONTENTS);
        InvoiceLineInvoicePeriod::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromManyLines(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MANY_LINES);
        InvoiceLineInvoicePeriod::fromXML($this->xpath, $currentElement);
    }

    public function testGenerateXml(): void
    {
        $currentElement  = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject       = InvoiceLineInvoicePeriod::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyCreditNoteRootDocument();
        $rootDestination->appendChild($ublObject->toXML($this->document));
        $generatedOutput = $this->formatXMLOutput();
        $this->assertStringEqualsStringIgnoringLineEndings(self::XML_VALID_FULL_CONTENT, $generatedOutput);
    }
}
