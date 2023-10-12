<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\Basic;

use Tiime\EN16931\DataType\CurrencyCode;
use Tiime\UniversalBusinessLanguage\DataType\Basic\AllowanceTotalAmount;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class AllowanceTotalAmountTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cbc:AllowanceTotalAmount currencyID="EUR">36.00</cbc:AllowanceTotalAmount>
</Invoice>
XML;

    protected const XML_INVALID_AMOUNT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cbc:AllowanceTotalAmount currencyID="ER">36.0.0</cbc:AllowanceTotalAmount>
</Invoice>
XML;

    protected const XML_INVALID_MISSING_CURRENCY = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cbc:AllowanceTotalAmount>36.00</cbc:AllowanceTotalAmount>
</Invoice>

XML;


    protected const XML_EMPTY_AMOUNT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cbc:AllowanceTotalAmount></cbc:AllowanceTotalAmount>
</Invoice>
XML;

    public function testCanBeCreatedFromFullContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_CONTENT);
        $ublObject = AllowanceTotalAmount::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(AllowanceTotalAmount::class, $ublObject);
        $this->assertEquals(36, $ublObject->getValue()->getFormattedValueRounded());
        $this->assertEquals(CurrencyCode::tryFrom("EUR"), $ublObject->getCurrencyCode());
    }

    public function testCannotBeCreatedFromInvalid(): void
    {
        $this->expectException(\TypeError::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_AMOUNT);
        AllowanceTotalAmount::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromMissingCurrency(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MISSING_CURRENCY);
        AllowanceTotalAmount::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromEmpty(): void
    {
        $this->expectException(\TypeError::class);
        $currentElement = $this->loadXMLDocument(self::XML_EMPTY_AMOUNT);
        AllowanceTotalAmount::fromXML($this->xpath, $currentElement);
    }

    public function testGenerateXml(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_CONTENT);
        $ublObject = AllowanceTotalAmount::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyRootDocument();
        $rootDestination->appendChild($ublObject->toXML($this->document));
        $generatedOutput = $this->formatXMLOutput();
        $this->assertEquals(self::XML_VALID_CONTENT, $generatedOutput);
    }
}