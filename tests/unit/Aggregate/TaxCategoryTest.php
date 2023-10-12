<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\Aggregate;

use Tiime\EN16931\DataType\VatCategory;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\TaxCategory;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\TaxScheme;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class TaxCategoryTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_FULL_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:TaxCategory>
    <cbc:ID>S</cbc:ID>
    <cbc:Percent>20.20</cbc:Percent>
    <cac:TaxScheme>
      <cbc:ID>VAT</cbc:ID>
    </cac:TaxScheme>
  </cac:TaxCategory>
</Invoice>
XML;

    protected const XML_VALID_MINIMAL_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:TaxCategory>
    <cbc:ID>S</cbc:ID>
    <cac:TaxScheme>
      <cbc:ID>VAT</cbc:ID>
    </cac:TaxScheme>
  </cac:TaxCategory>
</Invoice>
XML;

    protected const XML_INVALID_NO_LINE = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</Invoice>
XML;

    protected const XML_INVALID_TOO_MANY_TAX_CATEGORY = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:TaxCategory>
    <cbc:ID>S</cbc:ID>
    <cac:TaxScheme>
      <cbc:ID>VAT</cbc:ID>
    </cac:TaxScheme>
  </cac:TaxCategory>
  <cac:TaxCategory>
    <cbc:ID>S</cbc:ID>
    <cac:TaxScheme>
      <cbc:ID>VAT</cbc:ID>
    </cac:TaxScheme>
  </cac:TaxCategory>
</Invoice>
XML;

    public function testCanBeCreatedFromFullContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject      = TaxCategory::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(TaxCategory::class, $ublObject);
        $this->assertEquals(VatCategory::tryFrom('S'), $ublObject->getVatCategory());
        $this->assertEquals(20.2, $ublObject->getPercent()->getFormattedValueRounded());
        $this->assertInstanceOf(TaxScheme::class, $ublObject->getTaxScheme());
    }

    public function testCanBeCreatedFromMinimalContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_MINIMAL_CONTENT);
        $ublObject      = TaxCategory::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(TaxCategory::class, $ublObject);
        $this->assertEquals(VatCategory::tryFrom('S'), $ublObject->getVatCategory());
        $this->assertNull($ublObject->getPercent());
        $this->assertInstanceOf(TaxScheme::class, $ublObject->getTaxScheme());
    }

    public function testCannotBeCreatedFromNoLine(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_NO_LINE);
        TaxCategory::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromTooManyTaxTotals(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_TOO_MANY_TAX_CATEGORY);
        TaxCategory::fromXML($this->xpath, $currentElement);
    }

    public function testGenerateXml(): void
    {
        $currentElement  = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject       = TaxCategory::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyRootDocument();
        $rootDestination->appendChild($ublObject->toXML($this->document));
        $generatedOutput = $this->formatXMLOutput();
        $this->assertEquals(self::XML_VALID_FULL_CONTENT, $generatedOutput);
    }
}
