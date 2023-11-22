<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\CreditNote\Aggregate;

use Tiime\UniversalBusinessLanguage\CreditNote\DataType\Aggregate\CardAccount;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class CardAccountTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_FULL_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:CardAccount>
    <cbc:PrimaryAccountNumberID>1234</cbc:PrimaryAccountNumberID>
    <cbc:NetworkID>NA</cbc:NetworkID>
    <cbc:HolderName>John Doe</cbc:HolderName>
  </cac:CardAccount>
</Invoice>
XML;

    protected const XML_VALID_MINIMAL_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:CardAccount>
    <cbc:PrimaryAccountNumberID>1234</cbc:PrimaryAccountNumberID>
    <cbc:NetworkID>NA</cbc:NetworkID>
  </cac:CardAccount>
</Invoice>
XML;

    protected const XML_VALID_NO_LINE = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</Invoice>
XML;

    protected const XML_INVALID_MANY_CONTENTS = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:CardAccount>
    <cbc:PrimaryAccountNumberID>1234</cbc:PrimaryAccountNumberID>
    <cbc:PrimaryAccountNumberID>1234</cbc:PrimaryAccountNumberID>
    <cbc:NetworkID>NA</cbc:NetworkID>
    <cbc:HolderName>John Doe</cbc:HolderName>
  </cac:CardAccount>
</Invoice>
XML;
    protected const XML_INVALID_MANY_LINES = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:CardAccount>
    <cbc:PrimaryAccountNumberID>1234</cbc:PrimaryAccountNumberID>
    <cbc:NetworkID>NA</cbc:NetworkID>
  </cac:CardAccount>
    <cac:CardAccount>
    <cbc:PrimaryAccountNumberID>1234</cbc:PrimaryAccountNumberID>
    <cbc:NetworkID>NA</cbc:NetworkID>
  </cac:CardAccount>
</Invoice>
XML;

    public function testCanBeCreatedFromFullContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject      = CardAccount::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(CardAccount::class, $ublObject);
        $this->assertEquals('1234', $ublObject->getPrimaryAccountNumberIdentifier());
        $this->assertEquals('NA', $ublObject->getNetworkIdentifier());
        $this->assertEquals('John Doe', $ublObject->getHolderName());
    }

    public function testCanBeCreatedFromMinimalContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_MINIMAL_CONTENT);
        $ublObject      = CardAccount::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(CardAccount::class, $ublObject);
        $this->assertEquals('1234', $ublObject->getPrimaryAccountNumberIdentifier());
        $this->assertEquals('NA', $ublObject->getNetworkIdentifier());
        $this->assertNull($ublObject->getHolderName());
    }

    public function testCanBeCreatedFromNoLine(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_NO_LINE);
        $ublObject      = CardAccount::fromXML($this->xpath, $currentElement);
        $this->assertNull($ublObject);
    }

    public function testCannotBeCreatedFromMultipleContents(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MANY_CONTENTS);
        CardAccount::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromMultipleLines(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MANY_LINES);
        CardAccount::fromXML($this->xpath, $currentElement);
    }

    public function testGenerateXml(): void
    {
        $currentElement  = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject       = CardAccount::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyRootDocument();
        $rootDestination->appendChild($ublObject->toXML($this->document));
        $generatedOutput = $this->formatXMLOutput();
        $this->assertStringEqualsStringIgnoringLineEndings(self::XML_VALID_FULL_CONTENT, $generatedOutput);
    }
}
