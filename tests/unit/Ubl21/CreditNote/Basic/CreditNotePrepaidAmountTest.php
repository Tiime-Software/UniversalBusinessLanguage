<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\Ubl21\CreditNote\Basic;

use Tiime\EN16931\Codelist\CurrencyCodeISO4217 as CurrencyCode;
use Tiime\UniversalBusinessLanguage\Ubl21\CreditNote\DataType\Basic\PrepaidAmount;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class CreditNotePrepaidAmountTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_CONTENT = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cbc:PrepaidAmount currencyID="EUR">36.00</cbc:PrepaidAmount>
</CreditNote>
XML;

    protected const XML_VALID_EMPTY = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</CreditNote>
XML;

    protected const XML_INVALID_AMOUNT = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cbc:PrepaidAmount currencyID="ER">36.0.0</cbc:PrepaidAmount>
</CreditNote>
XML;

    protected const XML_INVALID_MISSING_CURRENCY = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cbc:PrepaidAmount>36.00</cbc:PrepaidAmount>
</CreditNote>
XML;

    protected const XML_INVALID_NO_AMOUNT = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cbc:PrepaidAmount></cbc:PrepaidAmount>
</CreditNote>
XML;

    public function testCanBeCreatedFromFullContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_CONTENT);
        $ublObject      = PrepaidAmount::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(PrepaidAmount::class, $ublObject);
        $this->assertEquals(36, $ublObject->getValue()->getFormattedValueRounded());
        $this->assertEquals(CurrencyCode::tryFrom('EUR'), $ublObject->getCurrencyCode());
    }

    public function testCannotBeCreatedFromInvalidValue(): void
    {
        $this->expectException(\TypeError::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_AMOUNT);
        PrepaidAmount::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromMissingCurrency(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MISSING_CURRENCY);
        PrepaidAmount::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromNoAmount(): void
    {
        $this->expectException(\TypeError::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_NO_AMOUNT);
        PrepaidAmount::fromXML($this->xpath, $currentElement);
    }

    public function testCanBeCreatedFromEmpty(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_EMPTY);
        $ublObject      = PrepaidAmount::fromXML($this->xpath, $currentElement);
        $this->assertNull($ublObject);
    }

    public function testGenerateXml(): void
    {
        $currentElement  = $this->loadXMLDocument(self::XML_VALID_CONTENT);
        $ublObject       = PrepaidAmount::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyCreditNoteRootDocument();
        $rootDestination->appendChild($ublObject->toXML($this->document));
        $generatedOutput = $this->formatXMLOutput();
        $this->assertStringEqualsStringIgnoringLineEndings(self::XML_VALID_CONTENT, $generatedOutput);
    }
}
