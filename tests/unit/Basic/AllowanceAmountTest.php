<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\Basic;

use Tiime\EN16931\DataType\CurrencyCode;
use Tiime\UniversalBusinessLanguage\DataType\Basic\AllowanceAmount;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class AllowanceAmountTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_CONTENT = <<<XMLCONTENT
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cbc:Amount currencyID="EUR">36.01</cbc:Amount>
</Invoice>
XMLCONTENT;

    protected const XML_INVALID_AMOUNT = <<<XMLCONTENT
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cbc:Amount currencyID="ER">36.0.0</cbc:Amount>
</Invoice>
XMLCONTENT;

    protected const XML_EMPTY_AMOUNT = <<<XMLCONTENT
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cbc:Amount></cbc:Amount>
</Invoice>
XMLCONTENT;

    public function testCanBeCreatedFromFullContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_CONTENT);
        $ublObject = AllowanceAmount::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(AllowanceAmount::class, $ublObject);
        $this->assertEquals(36.01, $ublObject->getAmount());
        $this->assertEquals(CurrencyCode::tryFrom("EUR"), $ublObject->getCurrencyCode());
    }

    public function testCannotBeCreatedFromInvalid(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_AMOUNT);
        AllowanceAmount::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromEmpty(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_EMPTY_AMOUNT);
        AllowanceAmount::fromXML($this->xpath, $currentElement);
    }

    public function testGenerateXml(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_CONTENT);
        $ublObject = AllowanceAmount::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyRootDocument();
        $rootDestination->appendChild($ublObject->toXML($this->document));
        $generatedOutput = $this->formatXMLOutput();
        $this->assertEquals(self::XML_VALID_CONTENT, $generatedOutput);
    }
}