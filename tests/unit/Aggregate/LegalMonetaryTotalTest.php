<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\Aggregate;

use Tiime\UniversalBusinessLanguage\DataType\Aggregate\LegalMonetaryTotal;
use Tiime\UniversalBusinessLanguage\DataType\Basic\AllowanceTotalAmount;
use Tiime\UniversalBusinessLanguage\DataType\Basic\ChargeTotalAmount;
use Tiime\UniversalBusinessLanguage\DataType\Basic\LineExtensionAmount;
use Tiime\UniversalBusinessLanguage\DataType\Basic\PayableAmount;
use Tiime\UniversalBusinessLanguage\DataType\Basic\PrepaidAmount;
use Tiime\UniversalBusinessLanguage\DataType\Basic\TaxExclusiveAmount;
use Tiime\UniversalBusinessLanguage\DataType\Basic\TaxInclusiveAmount;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class LegalMonetaryTotalTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_FULL_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:LegalMonetaryTotal>
    <cbc:LineExtensionAmount currencyID="EUR">3800.00</cbc:LineExtensionAmount>
    <cbc:TaxExclusiveAmount currencyID="EUR">3600.00</cbc:TaxExclusiveAmount>
    <cbc:TaxInclusiveAmount currencyID="EUR">4500.00</cbc:TaxInclusiveAmount>
    <cbc:AllowanceTotalAmount currencyID="EUR">200.00</cbc:AllowanceTotalAmount>
    <cbc:ChargeTotalAmount currencyID="EUR">0.00</cbc:ChargeTotalAmount>
    <cbc:PrepaidAmount currencyID="EUR">1000.00</cbc:PrepaidAmount>
    <cbc:PayableRoundingAmount currencyID="EUR">0.00</cbc:PayableRoundingAmount>
    <cbc:PayableAmount currencyID="EUR">3500.00</cbc:PayableAmount>
  </cac:LegalMonetaryTotal>
</Invoice>
XML;

    protected const XML_VALID_MINIMAL_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:LegalMonetaryTotal>
    <cbc:LineExtensionAmount currencyID="EUR">3800.00</cbc:LineExtensionAmount>
    <cbc:TaxExclusiveAmount currencyID="EUR">3600.00</cbc:TaxExclusiveAmount>
    <cbc:TaxInclusiveAmount currencyID="EUR">4500.00</cbc:TaxInclusiveAmount>
    <cbc:PayableAmount currencyID="EUR">3500.00</cbc:PayableAmount>
  </cac:LegalMonetaryTotal>
</Invoice>
XML;

    protected const XML_INVALID_NO_LINE = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</Invoice>
XML;

    protected const XML_INVALID_NO_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:LegalMonetaryTotal>
  </cac:LegalMonetaryTotal>
</Invoice>
XML;

    protected const XML_INVALID_MANY_CONTENTS = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:LegalMonetaryTotal>
    <cbc:LineExtensionAmount currencyID="EUR">3800.00</cbc:LineExtensionAmount>
    <cbc:LineExtensionAmount currencyID="EUR">3800.00</cbc:LineExtensionAmount>
    <cbc:TaxExclusiveAmount currencyID="EUR">3600.00</cbc:TaxExclusiveAmount>
    <cbc:TaxInclusiveAmount currencyID="EUR">4500.00</cbc:TaxInclusiveAmount>
    <cbc:PayableAmount currencyID="EUR">3500.00</cbc:PayableAmount>
  </cac:LegalMonetaryTotal>
</Invoice>
XML;

    protected const XML_INVALID_MANY_LINES = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:LegalMonetaryTotal>
    <cbc:LineExtensionAmount currencyID="EUR">3800.00</cbc:LineExtensionAmount>
    <cbc:TaxExclusiveAmount currencyID="EUR">3600.00</cbc:TaxExclusiveAmount>
    <cbc:TaxInclusiveAmount currencyID="EUR">4500.00</cbc:TaxInclusiveAmount>
    <cbc:PayableAmount currencyID="EUR">3500.00</cbc:PayableAmount>
  </cac:LegalMonetaryTotal>
  <cac:LegalMonetaryTotal>
    <cbc:LineExtensionAmount currencyID="EUR">3800.00</cbc:LineExtensionAmount>
    <cbc:TaxExclusiveAmount currencyID="EUR">3600.00</cbc:TaxExclusiveAmount>
    <cbc:TaxInclusiveAmount currencyID="EUR">4500.00</cbc:TaxInclusiveAmount>
    <cbc:PayableAmount currencyID="EUR">3500.00</cbc:PayableAmount>
  </cac:LegalMonetaryTotal>
</Invoice>
XML;

    public function testCanBeCreatedFromFullContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject      = LegalMonetaryTotal::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(LegalMonetaryTotal::class, $ublObject);
        $this->assertInstanceOf(ChargeTotalAmount::class, $ublObject->getChargeTotalAmount());
        $this->assertInstanceOf(AllowanceTotalAmount::class, $ublObject->getAllowanceTotalAmount());
        $this->assertInstanceOf(PayableAmount::class, $ublObject->getPayableAmount());
        $this->assertInstanceOf(PrepaidAmount::class, $ublObject->getPrepaidAmount());
        $this->assertInstanceOf(LineExtensionAmount::class, $ublObject->getLineExtensionAmount());
        $this->assertInstanceOf(TaxExclusiveAmount::class, $ublObject->getTaxExclusiveAmount());
        $this->assertInstanceOf(TaxInclusiveAmount::class, $ublObject->getTaxInclusiveAmount());
    }

    public function testCanBeCreatedFromMinimalContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_MINIMAL_CONTENT);
        $ublObject      = LegalMonetaryTotal::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(LegalMonetaryTotal::class, $ublObject);
    }

    public function testCannotBeCreatedFromNoLine(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_NO_LINE);
        LegalMonetaryTotal::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromNoContent(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_NO_CONTENT);
        LegalMonetaryTotal::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromManyContents(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MANY_CONTENTS);
        LegalMonetaryTotal::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromManyLines(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MANY_LINES);
        LegalMonetaryTotal::fromXML($this->xpath, $currentElement);
    }

    public function testGenerateXml(): void
    {
        $currentElement  = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject       = LegalMonetaryTotal::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyRootDocument();
        $rootDestination->appendChild($ublObject->toXML($this->document));
        $generatedOutput = $this->formatXMLOutput();
        $this->assertStringEqualsStringIgnoringLineEndings(self::XML_VALID_FULL_CONTENT, $generatedOutput);
    }
}
