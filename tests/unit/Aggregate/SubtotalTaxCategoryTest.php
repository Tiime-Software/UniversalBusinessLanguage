<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\Aggregate;

use Tiime\EN16931\DataType\VatCategory;
use Tiime\EN16931\DataType\VatExoneration;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\SubtotalTaxCategory;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\TaxScheme;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class SubtotalTaxCategoryTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_FULL_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:TaxCategory>
    <cbc:ID>S</cbc:ID>
    <cbc:TaxExemptionReasonCode>VATEX-EU-79-C</cbc:TaxExemptionReasonCode>
    <cbc:TaxExemptionReason>Exempted</cbc:TaxExemptionReason>
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

    protected const XML_INVALID_MANY_CONTENTS = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:TaxCategory>
    <cbc:ID>S</cbc:ID>
    <cbc:ID>S</cbc:ID>
    <cac:TaxScheme>
      <cbc:ID>VAT</cbc:ID>
    </cac:TaxScheme>
  </cac:TaxCategory>
</Invoice>
XML;

    protected const XML_INVALID_WRONG_PERCENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:TaxCategory>
    <cbc:ID>S</cbc:ID>
    <cbc:Percent>20.20.1</cbc:Percent>
    <cac:TaxScheme>
      <cbc:ID>VAT</cbc:ID>
    </cac:TaxScheme>
  </cac:TaxCategory>
</Invoice>
XML;

    protected const XML_INVALID_WRONG_REASON_CODE = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:TaxCategory>
    <cbc:ID>S</cbc:ID>
    <cbc:TaxExemptionReasonCode>VATEX-EU-6546579-C</cbc:TaxExemptionReasonCode>
    <cac:TaxScheme>
      <cbc:ID>VAT</cbc:ID>
    </cac:TaxScheme>
  </cac:TaxCategory>
</Invoice>
XML;

    protected const XML_INVALID_WRONG_VAT_CODE = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:TaxCategory>
    <cbc:ID>654654</cbc:ID>
    <cac:TaxScheme>
      <cbc:ID>VAT</cbc:ID>
    </cac:TaxScheme>
  </cac:TaxCategory>
</Invoice>
XML;

    protected const XML_INVALID_MANY_LINES = <<<XML
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
        $ublObject      = SubtotalTaxCategory::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(SubtotalTaxCategory::class, $ublObject);
        $this->assertEquals(VatCategory::tryFrom('S'), $ublObject->getVatCategory());
        $this->assertEquals(20.2, $ublObject->getPercent()->getFormattedValueRounded());
        $this->assertEquals(VatExoneration::tryFrom('VATEX-EU-79-C'), $ublObject->getTaxExemptionReasonCode());
        $this->assertEquals('Exempted', $ublObject->getTaxExemptionReason());
        $this->assertInstanceOf(TaxScheme::class, $ublObject->getTaxScheme());
    }

    public function testCanBeCreatedFromMinimalContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_MINIMAL_CONTENT);
        $ublObject      = SubtotalTaxCategory::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(SubtotalTaxCategory::class, $ublObject);
        $this->assertEquals(VatCategory::tryFrom('S'), $ublObject->getVatCategory());
        $this->assertNull($ublObject->getPercent());
        $this->assertNull($ublObject->getTaxExemptionReasonCode());
        $this->assertNull($ublObject->getTaxExemptionReason());
        $this->assertInstanceOf(TaxScheme::class, $ublObject->getTaxScheme());
    }

    public function testCannotBeCreatedFromNoLine(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_NO_LINE);
        SubtotalTaxCategory::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromManyLines(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MANY_LINES);
        SubtotalTaxCategory::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromWrongPercent(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_WRONG_PERCENT);
        SubtotalTaxCategory::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromWrongReasonCode(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_WRONG_REASON_CODE);
        SubtotalTaxCategory::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromWrongVatCode(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_WRONG_VAT_CODE);
        SubtotalTaxCategory::fromXML($this->xpath, $currentElement);
    }

    public function testGenerateXml(): void
    {
        $currentElement  = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject       = SubtotalTaxCategory::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyRootDocument();
        $rootDestination->appendChild($ublObject->toXML($this->document));
        $generatedOutput = $this->formatXMLOutput();
        $this->assertEquals(self::XML_VALID_FULL_CONTENT, $generatedOutput);
    }
}
