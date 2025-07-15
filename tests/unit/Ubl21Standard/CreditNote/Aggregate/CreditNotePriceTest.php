<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\Ubl21Standard\CreditNote\Aggregate;

use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;
use Tiime\UniversalBusinessLanguage\Ubl21Standard\CreditNote\DataType\Aggregate\Price;
use Tiime\UniversalBusinessLanguage\Ubl21Standard\CreditNote\DataType\Aggregate\PriceAllowanceCharge;
use Tiime\UniversalBusinessLanguage\Ubl21Standard\CreditNote\DataType\Basic\BaseQuantity;
use Tiime\UniversalBusinessLanguage\Ubl21Standard\CreditNote\DataType\Basic\PriceAmount;

class CreditNotePriceTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_FULL_CONTENT = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:Price>
    <cbc:PriceAmount currencyID="EUR">23.45</cbc:PriceAmount>
    <cbc:BaseQuantity unitCode="C62">1.0000</cbc:BaseQuantity>
    <cac:AllowanceCharge>
      <cbc:ChargeIndicator>false</cbc:ChargeIndicator>
      <cbc:Amount currencyID="EUR">100.00</cbc:Amount>
      <cbc:BaseAmount currencyID="EUR">123.45</cbc:BaseAmount>
    </cac:AllowanceCharge>
  </cac:Price>
</CreditNote>
XML;

    protected const XML_VALID_MINIMAL_CONTENT = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:Price>
    <cbc:PriceAmount currencyID="EUR">23.45</cbc:PriceAmount>
  </cac:Price>
</CreditNote>
XML;

    protected const XML_INVALID_NO_LINE = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</CreditNote>
XML;

    protected const XML_INVALID_MANY_CONTENTS = <<<XML
 <CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:Price>
    <cbc:PriceAmount currencyID="EUR">23.45</cbc:PriceAmount>
    <cbc:PriceAmount currencyID="EUR">23.45</cbc:PriceAmount>
  </cac:Price>
</CreditNote>
XML;

    protected const XML_INVALID_MANY_LINES = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:Price>
    <cbc:PriceAmount currencyID="EUR">23.45</cbc:PriceAmount>
  </cac:Price>
  <cac:Price>
    <cbc:PriceAmount currencyID="EUR">23.45</cbc:PriceAmount>
  </cac:Price>
</CreditNote>
XML;

    public function testCanBeCreatedFromFullContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject      = Price::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(Price::class, $ublObject);
        $this->assertInstanceOf(PriceAmount::class, $ublObject->getPriceAmount());
        $this->assertInstanceOf(BaseQuantity::class, $ublObject->getBaseQuantity());
        $this->assertInstanceOf(PriceAllowanceCharge::class, $ublObject->getAllowance());
    }

    public function testCanBeCreatedFromMinimalContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_MINIMAL_CONTENT);
        $ublObject      = Price::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(Price::class, $ublObject);
        $this->assertInstanceOf(PriceAmount::class, $ublObject->getPriceAmount());
        $this->assertNull($ublObject->getBaseQuantity());
        $this->assertNull($ublObject->getAllowance());
    }

    public function testCannotBeCreatedFromNoLine(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_NO_LINE);
        Price::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromManyContents(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MANY_CONTENTS);
        Price::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromManyLines(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MANY_LINES);
        Price::fromXML($this->xpath, $currentElement);
    }

    public function testGenerateXml(): void
    {
        $currentElement  = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject       = Price::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyCreditNoteRootDocument();
        $rootDestination->appendChild($ublObject->toXML($this->document));
        $generatedOutput = $this->formatXMLOutput();
        $this->assertStringEqualsStringIgnoringLineEndings(self::XML_VALID_FULL_CONTENT, $generatedOutput);
    }
}
