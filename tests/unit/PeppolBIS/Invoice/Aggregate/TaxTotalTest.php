<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\PeppolBIS\Invoice\Aggregate;

use Tiime\UniversalBusinessLanguage\PeppolBIS\Invoice\DataType\Aggregate\TaxSubtotal;
use Tiime\UniversalBusinessLanguage\PeppolBIS\Invoice\DataType\Aggregate\TaxTotal;
use Tiime\UniversalBusinessLanguage\PeppolBIS\Invoice\DataType\Basic\TaxAmount;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class TaxTotalTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_FULL_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:TaxTotal>
    <cbc:TaxAmount currencyID="EUR">36.00</cbc:TaxAmount>
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
  </cac:TaxTotal>
  <cac:TaxTotal>
    <cbc:TaxAmount currencyID="GBP">31.16</cbc:TaxAmount>
  </cac:TaxTotal>
</Invoice>
XML;

    protected const XML_VALID_MINIMAL_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:TaxTotal>
    <cbc:TaxAmount currencyID="EUR">36.00</cbc:TaxAmount>
  </cac:TaxTotal>
</Invoice>
XML;

    protected const XML_INVALID_NO_LINE = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</Invoice>
XML;

    protected const XML_INVALID_MANY_LINES = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:TaxTotal>
    <cbc:TaxAmount currencyID="EUR">36.00</cbc:TaxAmount>
  </cac:TaxTotal>
  <cac:TaxTotal>
    <cbc:TaxAmount currencyID="EUR">36.00</cbc:TaxAmount>
  </cac:TaxTotal>
    <cac:TaxTotal>
    <cbc:TaxAmount currencyID="EUR">36.00</cbc:TaxAmount>
  </cac:TaxTotal>
</Invoice>
XML;

    public function testCanBeCreatedFromFullContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObjects     = TaxTotal::fromXML($this->xpath, $currentElement);
        $this->assertIsArray($ublObjects);
        $this->assertCount(2, $ublObjects);
        $this->assertInstanceOf(TaxTotal::class, $ublObjects[0]);
        $this->assertInstanceOf(TaxAmount::class, $ublObjects[0]->getTaxAmount());
        $this->assertIsArray($ublObjects[0]->getTaxSubtotals());
        $this->assertCount(1, $ublObjects[0]->getTaxSubtotals());

        foreach ($ublObjects[0]->getTaxSubtotals() as $elem) {
            $this->assertInstanceOf(TaxSubtotal::class, $elem);
        }

        $this->assertInstanceOf(TaxTotal::class, $ublObjects[1]);
        $this->assertInstanceOf(TaxAmount::class, $ublObjects[1]->getTaxAmount());
        $this->assertCount(0, $ublObjects[1]->getTaxSubtotals());
    }

    public function testCanBeCreatedFromMinimalContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_MINIMAL_CONTENT);
        $ublObjects     = TaxTotal::fromXML($this->xpath, $currentElement);
        $this->assertIsArray($ublObjects);
        $this->assertCount(1, $ublObjects);
        $this->assertInstanceOf(TaxTotal::class, $ublObjects[0]);
        $this->assertInstanceOf(TaxAmount::class, $ublObjects[0]->getTaxAmount());
        $this->assertIsArray($ublObjects[0]->getTaxSubtotals());
        $this->assertCount(0, $ublObjects[0]->getTaxSubtotals());
    }

    public function testCannotBeCreatedFromNoLine(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_NO_LINE);
        TaxTotal::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromManyLines(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MANY_LINES);
        TaxTotal::fromXML($this->xpath, $currentElement);
    }

    public function testGenerateXml(): void
    {
        $currentElement  = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObjects      = TaxTotal::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyInvoiceRootDocument();

        foreach ($ublObjects as $ublObject) {
            $rootDestination->appendChild($ublObject->toXML($this->document));
        }
        $generatedOutput = $this->formatXMLOutput();
        $this->assertStringEqualsStringIgnoringLineEndings(self::XML_VALID_FULL_CONTENT, $generatedOutput);
    }
}
