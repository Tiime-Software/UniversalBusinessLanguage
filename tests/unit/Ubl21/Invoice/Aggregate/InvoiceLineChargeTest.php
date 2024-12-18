<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\Ubl21\Invoice\Aggregate;

use Tiime\EN16931\Codelist\ChargeReasonCodeUNTDID7161 as ChargeReasonCode;
use Tiime\EN16931\SemanticDataType\Percentage;
use Tiime\UniversalBusinessLanguage\Ubl21\Invoice\DataType\Aggregate\InvoiceLine;
use Tiime\UniversalBusinessLanguage\Ubl21\Invoice\DataType\Aggregate\InvoiceLineCharge;
use Tiime\UniversalBusinessLanguage\Ubl21\Invoice\DataType\Basic\AllowanceChargeAmount;
use Tiime\UniversalBusinessLanguage\Ubl21\Invoice\DataType\Basic\BaseAmount;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class InvoiceLineChargeTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_FULL_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:AllowanceCharge>
    <cbc:ChargeIndicator>true</cbc:ChargeIndicator>
    <cbc:AllowanceChargeReasonCode>AA</cbc:AllowanceChargeReasonCode>
    <cbc:AllowanceChargeReason>Google Ads</cbc:AllowanceChargeReason>
    <cbc:MultiplierFactorNumeric>20.00</cbc:MultiplierFactorNumeric>
    <cbc:Amount currencyID="EUR">200.00</cbc:Amount>
    <cbc:BaseAmount currencyID="EUR">1000.00</cbc:BaseAmount>
  </cac:AllowanceCharge>
</Invoice>
XML;

    protected const XML_VALID_MINIMAL_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:AllowanceCharge>
    <cbc:ChargeIndicator>true</cbc:ChargeIndicator>
    <cbc:Amount currencyID="EUR">200.00</cbc:Amount>
  </cac:AllowanceCharge>
</Invoice>
XML;

    protected const XML_VALID_NO_LINE = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</Invoice>
XML;

    protected const XML_INVALID_MANY_CONTENTS = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:AllowanceCharge>
    <cbc:ChargeIndicator>true</cbc:ChargeIndicator>
    <cbc:Amount currencyID="EUR">200.00</cbc:Amount>
    <cbc:Amount currencyID="EUR">200.00</cbc:Amount>
  </cac:AllowanceCharge>
</Invoice>
XML;

    protected const XML_VALID_MANY_LINES = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:AllowanceCharge>
    <cbc:ChargeIndicator>true</cbc:ChargeIndicator>
    <cbc:Amount currencyID="EUR">200.00</cbc:Amount>
  </cac:AllowanceCharge>
  <cac:AllowanceCharge>
    <cbc:ChargeIndicator>true</cbc:ChargeIndicator>
    <cbc:Amount currencyID="EUR">200.00</cbc:Amount>
  </cac:AllowanceCharge>
</Invoice>
XML;

    public function testCanBeCreatedFromFullContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObjects     = InvoiceLineCharge::fromXML($this->xpath, $currentElement);
        $this->assertIsArray($ublObjects);
        $this->assertCount(1, $ublObjects);
        $ublObject = $ublObjects[0];
        $this->assertInstanceOf(InvoiceLineCharge::class, $ublObject);
        $this->assertEquals('true', $ublObject->getChargeIndicator());
        $this->assertEquals('Google Ads', $ublObject->getChargeReason());
        $this->assertInstanceOf(ChargeReasonCode::class, $ublObject->getChargeReasonCode());
        $this->assertEquals(ChargeReasonCode::tryFrom('AA'), $ublObject->getChargeReasonCode());
        $this->assertInstanceOf(Percentage::class, $ublObject->getMultiplierFactorNumeric());
        $this->assertEquals('20.00', $ublObject->getMultiplierFactorNumeric()->getFormattedValueRounded());
        $this->assertInstanceOf(AllowanceChargeAmount::class, $ublObject->getAmount());
        $this->assertInstanceOf(BaseAmount::class, $ublObject->getBaseAmount());
    }

    public function testCanBeCreatedFromMinimalContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_MINIMAL_CONTENT);
        $ublObjects     = InvoiceLineCharge::fromXML($this->xpath, $currentElement);
        $this->assertIsArray($ublObjects);
        $this->assertCount(1, $ublObjects);
        $ublObject = $ublObjects[0];
        $this->assertInstanceOf(InvoiceLineCharge::class, $ublObject);
        $this->assertEquals('true', $ublObject->getChargeIndicator());
        $this->assertNull($ublObject->getChargeReason());
        $this->assertNull($ublObject->getChargeReasonCode());
        $this->assertNull($ublObject->getMultiplierFactorNumeric());
        $this->assertInstanceOf(AllowanceChargeAmount::class, $ublObject->getAmount());
        $this->assertNull($ublObject->getBaseAmount());
    }

    public function testCanBeCreatedFromNoLine(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_NO_LINE);
        $ublObjects     = InvoiceLineCharge::fromXML($this->xpath, $currentElement);
        $this->assertIsArray($ublObjects);
        $this->assertCount(0, $ublObjects);
    }

    public function testCannotBeCreatedFromManyContents(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MANY_CONTENTS);
        InvoiceLine::fromXML($this->xpath, $currentElement);
    }

    public function testCanBeCreatedFromManyLines(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_MANY_LINES);
        $ublObjects     = InvoiceLineCharge::fromXML($this->xpath, $currentElement);
        $this->assertIsArray($ublObjects);
        $this->assertCount(2, $ublObjects);
    }

    public function testGenerateXml(): void
    {
        $currentElement  = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObjects      = InvoiceLineCharge::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyInvoiceRootDocument();

        foreach ($ublObjects as $ublObject) {
            $rootDestination->appendChild($ublObject->toXML($this->document));
        }
        $generatedOutput = $this->formatXMLOutput();
        $this->assertStringEqualsStringIgnoringLineEndings(self::XML_VALID_FULL_CONTENT, $generatedOutput);
    }
}
