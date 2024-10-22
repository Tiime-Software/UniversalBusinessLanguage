<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\Ubl21\Invoice\Basic;

use Tiime\EN16931\Codelist\CurrencyCodeISO4217 as CurrencyCode;
use Tiime\UniversalBusinessLanguage\Ubl21\Invoice\DataType\Basic\AllowanceTotalAmount;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class AllowanceTotalAmountTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cbc:AllowanceTotalAmount currencyID="EUR">36.00</cbc:AllowanceTotalAmount>
</Invoice>
XML;

    protected const XML_INVALID_WRONG_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cbc:AllowanceTotalAmount currencyID="ER">36.0.0</cbc:AllowanceTotalAmount>
</Invoice>
XML;

    protected const XML_VALID_NO_LINE = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</Invoice>
XML;

    protected const XML_INVALID_MISSING_CURRENCY = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cbc:AllowanceTotalAmount>36.00</cbc:AllowanceTotalAmount>
</Invoice>
XML;
    protected const XML_EMPTY_NO_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cbc:AllowanceTotalAmount></cbc:AllowanceTotalAmount>
</Invoice>
XML;

    protected const XML_INVALID_MANY_LINES = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cbc:AllowanceTotalAmount currencyID="EUR">36.00</cbc:AllowanceTotalAmount>
  <cbc:AllowanceTotalAmount currencyID="EUR">36.00</cbc:AllowanceTotalAmount>
</Invoice>
XML;

    public function testCanBeCreatedFromContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_CONTENT);
        $ublObject      = AllowanceTotalAmount::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(AllowanceTotalAmount::class, $ublObject);
        $this->assertEquals(36, $ublObject->getValue()->getFormattedValueRounded());
        $this->assertEquals(CurrencyCode::tryFrom('EUR'), $ublObject->getCurrencyCode());
    }

    public function testCanBeCreatedFromNoLine(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_NO_LINE);
        $ublObject      = AllowanceTotalAmount::fromXML($this->xpath, $currentElement);
        $this->assertNull($ublObject);
    }

    public function testCannotBeCreatedFromManyLines(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MANY_LINES);
        AllowanceTotalAmount::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromWrongContent(): void
    {
        $this->expectException(\TypeError::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_WRONG_CONTENT);
        AllowanceTotalAmount::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromMissingCurrency(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MISSING_CURRENCY);
        AllowanceTotalAmount::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromNoContent(): void
    {
        $this->expectException(\TypeError::class);
        $currentElement = $this->loadXMLDocument(self::XML_EMPTY_NO_CONTENT);
        AllowanceTotalAmount::fromXML($this->xpath, $currentElement);
    }

    public function testGenerateXml(): void
    {
        $currentElement  = $this->loadXMLDocument(self::XML_VALID_CONTENT);
        $ublObject       = AllowanceTotalAmount::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyInvoiceRootDocument();
        $rootDestination->appendChild($ublObject->toXML($this->document));
        $generatedOutput = $this->formatXMLOutput();
        $this->assertStringEqualsStringIgnoringLineEndings(self::XML_VALID_CONTENT, $generatedOutput);
    }
}
