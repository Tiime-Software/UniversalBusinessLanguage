<?php

use Tiime\UniversalBusinessLanguage\Invoice\DataType\Aggregate\Charge;
use Tiime\UniversalBusinessLanguage\Invoice\DataType\Aggregate\TaxCategory;
use Tiime\UniversalBusinessLanguage\Invoice\DataType\Basic\AllowanceChargeAmount;
use Tiime\UniversalBusinessLanguage\Invoice\DataType\Basic\BaseAmount;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class ChargeTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_FULL_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:AllowanceCharge>
    <cbc:ChargeIndicator>true</cbc:ChargeIndicator>
    <cbc:AllowanceChargeReasonCode>AA</cbc:AllowanceChargeReasonCode>
    <cbc:AllowanceChargeReason>Ads campaign</cbc:AllowanceChargeReason>
    <cbc:MultiplierFactorNumeric>20.00</cbc:MultiplierFactorNumeric>
    <cbc:Amount currencyID="EUR">200.00</cbc:Amount>
    <cbc:BaseAmount currencyID="EUR">1000.00</cbc:BaseAmount>
    <cac:TaxCategory>
      <cbc:ID>S</cbc:ID>
      <cbc:Percent>25.00</cbc:Percent>
      <cac:TaxScheme>
        <cbc:ID>VAT</cbc:ID>
      </cac:TaxScheme>
    </cac:TaxCategory>
  </cac:AllowanceCharge>
</Invoice>
XML;

    protected const XML_VALID_MINIMAL_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:AllowanceCharge>
    <cbc:ChargeIndicator>true</cbc:ChargeIndicator>
    <cbc:Amount currencyID="EUR">200.00</cbc:Amount>
    <cac:TaxCategory>
      <cbc:ID>S</cbc:ID>
      <cac:TaxScheme>
        <cbc:ID>VAT</cbc:ID>
      </cac:TaxScheme>
    </cac:TaxCategory>
  </cac:AllowanceCharge>
</Invoice>
XML;

    protected const XML_VALID_NO_LINE = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</Invoice>
XML;

    protected const XML_INVALID_MISSING_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:AllowanceCharge>
    <cbc:ChargeIndicator>true</cbc:ChargeIndicator>
    <cbc:Amount currencyID="EUR">200.00</cbc:Amount>
  </cac:AllowanceCharge>
</Invoice>
XML;

    protected const XML_INVALID_MANY_ENTRIES = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:AllowanceCharge>
    <cbc:ChargeIndicator>true</cbc:ChargeIndicator>
    <cbc:Amount currencyID="EUR">200.00</cbc:Amount>
    <cbc:Amount currencyID="EUR">200.00</cbc:Amount>
    <cac:TaxCategory>
      <cbc:ID>S</cbc:ID>
      <cac:TaxScheme>
        <cbc:ID>VAT</cbc:ID>
      </cac:TaxScheme>
    </cac:TaxCategory>
  </cac:AllowanceCharge>
</Invoice>
XML;

    protected const XML_VALID_MANY_LINES = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:AllowanceCharge>
    <cbc:ChargeIndicator>true</cbc:ChargeIndicator>
    <cbc:Amount currencyID="EUR">200.00</cbc:Amount>
    <cac:TaxCategory>
      <cbc:ID>S</cbc:ID>
      <cac:TaxScheme>
        <cbc:ID>VAT</cbc:ID>
      </cac:TaxScheme>
    </cac:TaxCategory>
  </cac:AllowanceCharge>
  <cac:AllowanceCharge>
    <cbc:ChargeIndicator>true</cbc:ChargeIndicator>
    <cbc:Amount currencyID="EUR">200.00</cbc:Amount>
    <cac:TaxCategory>
      <cbc:ID>S</cbc:ID>
      <cac:TaxScheme>
        <cbc:ID>VAT</cbc:ID>
      </cac:TaxScheme>
    </cac:TaxCategory>
  </cac:AllowanceCharge>
</Invoice>
XML;

    public function testCanBeCreatedFromFullContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObjects     = Charge::fromXML($this->xpath, $currentElement);
        $this->assertIsArray($ublObjects);
        $this->assertCount(1, $ublObjects);
        $ublObject = $ublObjects[0];
        $this->assertInstanceOf(Charge::class, $ublObject);
        $this->assertEquals('true', $ublObject->getChargeIndicator());
        $this->assertInstanceOf(ChargeReasonCode::class, $ublObject->getChargeReasonCode());
        $this->assertEquals('Ads campaign', $ublObject->getChargeReason());
        $this->assertEquals('20.00', $ublObject->getMultiplierFactorNumeric());
        $this->assertInstanceOf(AllowanceChargeAmount::class, $ublObject->getAmount());
        $this->assertInstanceOf(BaseAmount::class, $ublObject->getBaseAmount());
        $this->assertInstanceOf(TaxCategory::class, $ublObject->getTaxCategory());
    }

    public function testCanBeCreatedFromMinimalContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_MINIMAL_CONTENT);
        $ublObjects     = Charge::fromXML($this->xpath, $currentElement);
        $this->assertIsArray($ublObjects);
        $this->assertCount(1, $ublObjects);
        $this->assertInstanceOf(Charge::class, $ublObjects[0]);
        $this->assertEquals('true', $ublObjects[0]->getChargeIndicator());
        $this->assertNull($ublObjects[0]->getChargeReasonCode());
        $this->assertNull($ublObjects[0]->getChargeReason());
        $this->assertNull($ublObjects[0]->getMultiplierFactorNumeric());
        $this->assertInstanceOf(AllowanceChargeAmount::class, $ublObjects[0]->getAmount());
        $this->assertNull($ublObjects[0]->getBaseAmount());
        $this->assertInstanceOf(TaxCategory::class, $ublObjects[0]->getTaxCategory());
    }

    public function testCanBeCreatedFromNoLine(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_NO_LINE);
        $ublObjects     = Charge::fromXML($this->xpath, $currentElement);
        $this->assertIsArray($ublObjects);
        $this->assertCount(0, $ublObjects);
    }

    public function testCannotBeCreatedFromNotEnoughData(): void
    {
        $this->expectException(Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MISSING_CONTENT);
        Charge::fromXML($this->xpath, $currentElement);
    }

    public function testCanBeCreatedFromManyEntries(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_MANY_LINES);
        $ublObjects     = Charge::fromXML($this->xpath, $currentElement);
        $this->assertIsArray($ublObjects);
        $this->assertCount(2, $ublObjects);

        foreach ($ublObjects as $ublObject) {
            $this->assertInstanceOf(Charge::class, $ublObject);
        }
    }

    public function testGenerateXml(): void
    {
        $currentElement  = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObjects      = Charge::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyInvoiceRootDocument();

        foreach ($ublObjects as $ublObject) {
            $rootDestination->appendChild($ublObject->toXML($this->document));
        }
        $generatedOutput = $this->formatXMLOutput();
        $this->assertStringEqualsStringIgnoringLineEndings(self::XML_VALID_FULL_CONTENT, $generatedOutput);
    }
}
