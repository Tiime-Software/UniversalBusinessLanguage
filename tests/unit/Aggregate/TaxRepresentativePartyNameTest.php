<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\Aggregate;

use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\TaxRepresentativePartyName;

class TaxRepresentativePartyNameTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PartyName>
    <cbc:Name>SELLER TAX REP</cbc:Name>
  </cac:PartyName>
</Invoice>
XML;

    protected const XML_INVALID_NO_LINE = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</Invoice>
XML;

    protected const XML_INVALID_TOO_MANY_NAMES = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PartyName>
    <cbc:Name>SELLER TAX REP</cbc:Name>
    <cbc:Name>SELLER TAX REP</cbc:Name>
  </cac:PartyName>
</Invoice>
XML;

    protected const XML_INVALID_NOT_ENOUGH_NAME = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PartyName>
  </cac:PartyName>
</Invoice>
XML;


    public function testCanBeCreatedFromFullContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_CONTENT);
        $ublObject = TaxRepresentativePartyName::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(TaxRepresentativePartyName::class, $ublObject);
        $this->assertEquals("SELLER TAX REP", $ublObject->getName());
    }

    public function testCannotBeCreatedFromNoLine(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_NO_LINE);
        TaxRepresentativePartyName::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromTooManyIdentifiers(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_TOO_MANY_NAMES);
        TaxRepresentativePartyName::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromNotEnoughIdentifier(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_NOT_ENOUGH_NAME);
        TaxRepresentativePartyName::fromXML($this->xpath, $currentElement);
    }

    public function testGenerateXml(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_CONTENT);
        $ublObject = TaxRepresentativePartyName::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyRootDocument();
        $rootDestination->appendChild($ublObject->toXML($this->document));
        $generatedOutput = $this->formatXMLOutput();
        $this->assertEquals(self::XML_VALID_CONTENT, $generatedOutput);
    }
}