<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\Aggregate;

use Tiime\UniversalBusinessLanguage\Invoice\DataType\Aggregate\InvoiceLineInvoicePeriod;
use Tiime\UniversalBusinessLanguage\Invoice\DataType\Basic\EndDate;
use Tiime\UniversalBusinessLanguage\Invoice\DataType\Basic\StartDate;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class InvoiceLineInvoicePeriodTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_FULL_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:InvoicePeriod>
    <cbc:StartDate>2017-10-05</cbc:StartDate>
    <cbc:EndDate>2017-10-15</cbc:EndDate>
  </cac:InvoicePeriod>
</Invoice>
XML;

    protected const XML_VALID_MINIMAL_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:InvoicePeriod>
  </cac:InvoicePeriod>
</Invoice>
XML;

    protected const XML_VALID_NO_LINE = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</Invoice>
XML;

    protected const XML_INVALID_MANY_CONTENTS = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:InvoicePeriod>
    <cbc:StartDate>2017-10-05</cbc:StartDate>
    <cbc:StartDate>2017-10-05</cbc:StartDate>
    <cbc:EndDate>2017-10-15</cbc:EndDate>
  </cac:InvoicePeriod>
</Invoice>
XML;

    protected const XML_INVALID_MANY_LINES = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:InvoicePeriod>
    <cbc:StartDate>2017-10-05</cbc:StartDate>
    <cbc:EndDate>2017-10-15</cbc:EndDate>
  </cac:InvoicePeriod>
  <cac:InvoicePeriod>
    <cbc:StartDate>2017-10-05</cbc:StartDate>
    <cbc:EndDate>2017-10-15</cbc:EndDate>
  </cac:InvoicePeriod>
</Invoice>
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
        $rootDestination = $this->generateEmptyRootDocument();
        $rootDestination->appendChild($ublObject->toXML($this->document));
        $generatedOutput = $this->formatXMLOutput();
        $this->assertStringEqualsStringIgnoringLineEndings(self::XML_VALID_FULL_CONTENT, $generatedOutput);
    }
}
