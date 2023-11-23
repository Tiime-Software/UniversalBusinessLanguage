<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\CreditNote\Aggregate;

use Tiime\UniversalBusinessLanguage\CreditNote\DataType\Aggregate\LegalMonetaryTotal;
use Tiime\UniversalBusinessLanguage\CreditNote\DataType\Basic\AllowanceTotalAmount;
use Tiime\UniversalBusinessLanguage\CreditNote\DataType\Basic\ChargeTotalAmount;
use Tiime\UniversalBusinessLanguage\CreditNote\DataType\Basic\LineExtensionAmount;
use Tiime\UniversalBusinessLanguage\CreditNote\DataType\Basic\PayableAmount;
use Tiime\UniversalBusinessLanguage\CreditNote\DataType\Basic\PayableRoundingAmount;
use Tiime\UniversalBusinessLanguage\CreditNote\DataType\Basic\PrepaidAmount;
use Tiime\UniversalBusinessLanguage\CreditNote\DataType\Basic\TaxExclusiveAmount;
use Tiime\UniversalBusinessLanguage\CreditNote\DataType\Basic\TaxInclusiveAmount;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class CreditNoteLegalMonetaryTotalTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_FULL_CONTENT = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
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
</CreditNote>
XML;

    protected const XML_VALID_MINIMAL_CONTENT = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:LegalMonetaryTotal>
    <cbc:LineExtensionAmount currencyID="EUR">3800.00</cbc:LineExtensionAmount>
    <cbc:TaxExclusiveAmount currencyID="EUR">3600.00</cbc:TaxExclusiveAmount>
    <cbc:TaxInclusiveAmount currencyID="EUR">4500.00</cbc:TaxInclusiveAmount>
    <cbc:PayableAmount currencyID="EUR">3500.00</cbc:PayableAmount>
  </cac:LegalMonetaryTotal>
</CreditNote>
XML;

    protected const XML_INVALID_NO_LINE = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</CreditNote>
XML;

    protected const XML_INVALID_NO_CONTENT = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:LegalMonetaryTotal>
  </cac:LegalMonetaryTotal>
</CreditNote>
XML;

    protected const XML_INVALID_MANY_CONTENTS = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:LegalMonetaryTotal>
    <cbc:LineExtensionAmount currencyID="EUR">3800.00</cbc:LineExtensionAmount>
    <cbc:LineExtensionAmount currencyID="EUR">3800.00</cbc:LineExtensionAmount>
    <cbc:TaxExclusiveAmount currencyID="EUR">3600.00</cbc:TaxExclusiveAmount>
    <cbc:TaxInclusiveAmount currencyID="EUR">4500.00</cbc:TaxInclusiveAmount>
    <cbc:PayableAmount currencyID="EUR">3500.00</cbc:PayableAmount>
  </cac:LegalMonetaryTotal>
</CreditNote>
XML;

    protected const XML_INVALID_MANY_LINES = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
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
</CreditNote>
XML;

    public function testCanBeCreatedFromFullContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject      = LegalMonetaryTotal::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(LegalMonetaryTotal::class, $ublObject);
        $this->assertInstanceOf(ChargeTotalAmount::class, $ublObject->getChargeTotalAmount());
        $this->assertInstanceOf(AllowanceTotalAmount::class, $ublObject->getAllowanceTotalAmount());
        $this->assertInstanceOf(PayableAmount::class, $ublObject->getPayableAmount());
        $this->assertInstanceOf(PayableRoundingAmount::class, $ublObject->getPayableRoundingAmount());
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
        $rootDestination = $this->generateEmptyCreditNoteRootDocument();
        $rootDestination->appendChild($ublObject->toXML($this->document));
        $generatedOutput = $this->formatXMLOutput();
        $this->assertStringEqualsStringIgnoringLineEndings(self::XML_VALID_FULL_CONTENT, $generatedOutput);
    }
}
