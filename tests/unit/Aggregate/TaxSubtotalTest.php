<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\Aggregate;

use Tiime\UniversalBusinessLanguage\DataType\Aggregate\SubtotalTaxCategory;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\TaxSubtotal;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\TaxTotal;
use Tiime\UniversalBusinessLanguage\DataType\Basic\TaxableAmount;
use Tiime\UniversalBusinessLanguage\DataType\Basic\TaxAmount;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class TaxSubtotalTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:TaxSubtotal>
    <cbc:TaxableAmount currencyID="EUR">180.00</cbc:TaxableAmount>
    <cbc:TaxAmount currencyID="EUR">36.00</cbc:TaxAmount>
    <cac:TaxCategory>
      <cbc:ID>S</cbc:ID>
      <cbc:Percent>20.00</cbc:Percent>
      <cac:TaxScheme>
        <cbc:ID>VAT</cbc:ID>
      </cac:TaxScheme>
    </cac:TaxCategory>
  </cac:TaxSubtotal>
</Invoice>
XML;

    protected const XML_VALID_NO_LINE = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</Invoice>
XML;

    public function testCanBeCreatedFromFullContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_CONTENT);
        $ublObjects     = TaxSubtotal::fromXML($this->xpath, $currentElement);
        $this->assertIsArray($ublObjects);
        $this->assertCount(1, $ublObjects);

        $this->assertInstanceOf(TaxSubtotal::class, $ublObjects[0]);
        $this->assertInstanceOf(TaxableAmount::class, $ublObjects[0]->getTaxableAmount());
        $this->assertInstanceOf(TaxAmount::class, $ublObjects[0]->getTaxAmount());
        $this->assertInstanceOf(SubtotalTaxCategory::class, $ublObjects[0]->getTaxCategory());
    }

    public function testCanBeCreatedFromNoLine(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_VALID_NO_LINE);
        TaxTotal::fromXML($this->xpath, $currentElement);
    }

    public function testGenerateXml(): void
    {
        $currentElement  = $this->loadXMLDocument(self::XML_VALID_CONTENT);
        $ublObjects      = TaxSubtotal::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyRootDocument();

        foreach ($ublObjects as $ublObject) {
            $rootDestination->appendChild($ublObject->toXML($this->document));
        }
        $generatedOutput = $this->formatXMLOutput();
        $this->assertEquals(self::XML_VALID_CONTENT, $generatedOutput);
    }
}
