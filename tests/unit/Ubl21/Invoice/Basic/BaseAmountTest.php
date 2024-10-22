<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\Ubl21\Invoice\Basic;

use Tiime\EN16931\Codelist\CurrencyCodeISO4217 as CurrencyCode;
use Tiime\UniversalBusinessLanguage\Ubl21\Invoice\DataType\Basic\BaseAmount;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class BaseAmountTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cbc:BaseAmount currencyID="EUR">36.01</cbc:BaseAmount>
</Invoice>
XML;

    protected const XML_INVALID_MISSING_CURRENCY = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cbc:BaseAmount>36.01</cbc:BaseAmount>
</Invoice>
XML;

    protected const XML_INVALID_AMOUNT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cbc:BaseAmount currencyID="ER">36.0.0</cbc:BaseAmount>
</Invoice>
XML;

    protected const XML_EMPTY_AMOUNT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cbc:BaseAmount></cbc:BaseAmount>
</Invoice>
XML;

    public function testCanBeCreatedFromFullContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_CONTENT);
        $ublObject      = BaseAmount::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(BaseAmount::class, $ublObject);
        $this->assertEquals(36.01, $ublObject->getValue()->getFormattedValueRounded());
        $this->assertEquals(CurrencyCode::tryFrom('EUR'), $ublObject->getCurrencyCode());
    }

    public function testCannotBeCreatedFromMissingCurrency(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MISSING_CURRENCY);
        BaseAmount::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromInvalid(): void
    {
        $this->expectException(\TypeError::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_AMOUNT);
        BaseAmount::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromEmpty(): void
    {
        $this->expectException(\TypeError::class);
        $currentElement = $this->loadXMLDocument(self::XML_EMPTY_AMOUNT);
        BaseAmount::fromXML($this->xpath, $currentElement);
    }

    public function testGenerateXml(): void
    {
        $currentElement  = $this->loadXMLDocument(self::XML_VALID_CONTENT);
        $ublObject       = BaseAmount::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyInvoiceRootDocument();
        $rootDestination->appendChild($ublObject->toXML($this->document));
        $generatedOutput = $this->formatXMLOutput();
        $this->assertStringEqualsStringIgnoringLineEndings(self::XML_VALID_CONTENT, $generatedOutput);
    }
}
