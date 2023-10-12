<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\Aggregate;

use Tiime\UniversalBusinessLanguage\DataType\Aggregate\Contact;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\PartyName;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class PayeePartyNameTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PartyName>
    <cbc:Name>Payee Name Ltd</cbc:Name>
  </cac:PartyName>
</Invoice>
XML;

    protected const XML_INVALID_MULTIPLE_NAMES = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
<cac:PartyName>
  <cbc:Name>Payee Name Ltd 1</cbc:Name>
  <cbc:Name>Payee Name Ltd 2</cbc:Name>
</cac:PartyName>
</Invoice>
XML;

    public function testCanBeCreatedFromContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_CONTENT);
        $ublObject = PartyName::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(PartyName::class, $ublObject);
        $this->assertEquals("Payee Name Ltd", $ublObject->getName());
    }


    public function testCannotBeCreatedFromOmittedLine(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MULTIPLE_NAMES);
        PartyName::fromXML($this->xpath, $currentElement);
    }

    public function testGenerateXml(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_CONTENT);
        $ublObject = PartyName::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyRootDocument();
        $rootDestination->appendChild($ublObject->toXML($this->document));
        $generatedOutput = $this->formatXMLOutput();
        $this->assertEquals(self::XML_VALID_CONTENT, $generatedOutput);
    }
}