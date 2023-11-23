<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\CreditNote\Aggregate;

use Tiime\UniversalBusinessLanguage\CreditNote\DataType\Aggregate\Delivery;
use Tiime\UniversalBusinessLanguage\CreditNote\DataType\Aggregate\DeliveryLocation;
use Tiime\UniversalBusinessLanguage\CreditNote\DataType\Aggregate\DeliveryParty;
use Tiime\UniversalBusinessLanguage\CreditNote\DataType\Basic\ActualDeliveryDate;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class CreditNoteDeliveryTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_FULL_CONTENT = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:Delivery>
    <cbc:ActualDeliveryDate>2017-12-01</cbc:ActualDeliveryDate>
    <cac:DeliveryLocation>
      <cbc:ID schemeID="0088">83745498753497</cbc:ID>
      <cac:Address>
        <cbc:StreetName>Delivery Street 1</cbc:StreetName>
        <cbc:AdditionalStreetName>Delivery Street 2</cbc:AdditionalStreetName>
        <cbc:CityName>Malmö</cbc:CityName>
        <cbc:PostalZone>86756</cbc:PostalZone>
        <cbc:CountrySubentity>South Region</cbc:CountrySubentity>
        <cac:AddressLine>
          <cbc:Line>C54</cbc:Line>
        </cac:AddressLine>
        <cac:Country>
          <cbc:IdentificationCode>SE</cbc:IdentificationCode>
        </cac:Country>
      </cac:Address>
    </cac:DeliveryLocation>
    <cac:DeliveryParty>
      <cac:PartyName>
        <cbc:Name>Deliver name</cbc:Name>
      </cac:PartyName>
    </cac:DeliveryParty>
  </cac:Delivery>
</CreditNote>
XML;

    protected const XML_VALID_MINIMAL_CONTENT = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:Delivery>
  </cac:Delivery>
</CreditNote>
XML;

    protected const XML_VALID_NO_LINE = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</CreditNote>
XML;

    protected const XML_INVALID_MANY_LINES = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:Delivery>
  </cac:Delivery>
  <cac:Delivery>
  </cac:Delivery>
</CreditNote>
XML;

    public function testCanBeCreatedFromFullContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject      = Delivery::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(Delivery::class, $ublObject);
        $this->assertInstanceOf(ActualDeliveryDate::class, $ublObject->getActualDeliveryDate());
        $this->assertInstanceOf(DeliveryLocation::class, $ublObject->getDeliveryLocation());
        $this->assertInstanceOf(DeliveryParty::class, $ublObject->getDeliveryParty());
    }

    public function testCanBeCreatedFromMinimalContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_MINIMAL_CONTENT);
        $ublObject      = Delivery::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(Delivery::class, $ublObject);
        $this->assertNull($ublObject->getActualDeliveryDate());
        $this->assertNull($ublObject->getDeliveryLocation());
        $this->assertNull($ublObject->getDeliveryParty());
    }

    public function testCanBeCreatedFromNoLine(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_NO_LINE);
        $ublObject      = Delivery::fromXML($this->xpath, $currentElement);
        $this->assertNull($ublObject);
    }

    public function testCannotBeCreatedFromManyLines(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MANY_LINES);
        Delivery::fromXML($this->xpath, $currentElement);
    }

    public function testGenerateXml(): void
    {
        $currentElement  = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject       = Delivery::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyCreditNoteRootDocument();
        $rootDestination->appendChild($ublObject->toXML($this->document));
        $generatedOutput = $this->formatXMLOutput();
        $this->assertStringEqualsStringIgnoringLineEndings(self::XML_VALID_FULL_CONTENT, $generatedOutput);
    }
}
