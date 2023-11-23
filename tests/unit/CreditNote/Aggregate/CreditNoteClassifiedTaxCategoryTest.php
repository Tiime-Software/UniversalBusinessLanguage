<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\CreditNote\Aggregate;

use Tiime\EN16931\DataType\VatCategory;
use Tiime\EN16931\SemanticDataType\Percentage;
use Tiime\UniversalBusinessLanguage\CreditNote\DataType\Aggregate\ClassifiedTaxCategory;
use Tiime\UniversalBusinessLanguage\CreditNote\DataType\Aggregate\TaxScheme;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class CreditNoteClassifiedTaxCategoryTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_FULL_CONTENT = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:ClassifiedTaxCategory>
    <cbc:ID>S</cbc:ID>
    <cbc:Percent>25.00</cbc:Percent>
    <cac:TaxScheme>
      <cbc:ID>VAT</cbc:ID>
    </cac:TaxScheme>
  </cac:ClassifiedTaxCategory>
</CreditNote>
XML;

    protected const XML_VALID_MINIMAL_CONTENT = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:ClassifiedTaxCategory>
    <cbc:ID>S</cbc:ID>
    <cac:TaxScheme>
      <cbc:ID>VAT</cbc:ID>
    </cac:TaxScheme>
  </cac:ClassifiedTaxCategory>
</CreditNote>
XML;

    protected const XML_INVALID_NO_LINE = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</CreditNote>
XML;

    protected const XML_INVALID_MANY_LINES = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:ClassifiedTaxCategory>
    <cbc:ID>S</cbc:ID>
    <cac:TaxScheme>
      <cbc:ID>VAT</cbc:ID>
    </cac:TaxScheme>
  </cac:ClassifiedTaxCategory>
  <cac:ClassifiedTaxCategory>
    <cbc:ID>S</cbc:ID>
    <cac:TaxScheme>
      <cbc:ID>VAT</cbc:ID>
    </cac:TaxScheme>
  </cac:ClassifiedTaxCategory>
</CreditNote>
XML;

    public function testCanBeCreatedFromFullContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject      = ClassifiedTaxCategory::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(ClassifiedTaxCategory::class, $ublObject);
        $this->assertInstanceOf(TaxScheme::class, $ublObject->getTaxScheme());
        $this->assertInstanceOf(Percentage::class, $ublObject->getPercent());
        $this->assertEquals('25.00', $ublObject->getPercent()->getFormattedValueRounded());
        $this->assertInstanceOf(VatCategory::class, $ublObject->getVatCategory());
        $this->assertEquals(VatCategory::tryFrom('S'), $ublObject->getVatCategory());
    }

    public function testCanBeCreatedFromMinimalContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_MINIMAL_CONTENT);
        $ublObject      = ClassifiedTaxCategory::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(ClassifiedTaxCategory::class, $ublObject);
        $this->assertInstanceOf(TaxScheme::class, $ublObject->getTaxScheme());
        $this->assertNull($ublObject->getPercent());
        $this->assertInstanceOf(VatCategory::class, $ublObject->getVatCategory());
        $this->assertEquals(VatCategory::tryFrom('S'), $ublObject->getVatCategory());
    }

    public function testCannotBeCreatedFromNoLine(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_NO_LINE);
        ClassifiedTaxCategory::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromManyLines(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MANY_LINES);
        ClassifiedTaxCategory::fromXML($this->xpath, $currentElement);
    }

    public function testGenerateXml(): void
    {
        $currentElement  = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject       = ClassifiedTaxCategory::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyCreditNoteRootDocument();
        $rootDestination->appendChild($ublObject->toXML($this->document));
        $generatedOutput = $this->formatXMLOutput();
        $this->assertStringEqualsStringIgnoringLineEndings(self::XML_VALID_FULL_CONTENT, $generatedOutput);
    }
}
