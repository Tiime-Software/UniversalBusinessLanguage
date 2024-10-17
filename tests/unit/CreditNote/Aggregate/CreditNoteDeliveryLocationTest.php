<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\CreditNote\Aggregate;

use Tiime\EN16931\Codelist\InternationalCodeDesignator;
use Tiime\EN16931\DataType\Identifier\LocationIdentifier;
use Tiime\UniversalBusinessLanguage\CreditNote\DataType\Aggregate\DeliveryAddress;
use Tiime\UniversalBusinessLanguage\CreditNote\DataType\Aggregate\DeliveryLocation;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class CreditNoteDeliveryLocationTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_FULL_CONTENT = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
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
</CreditNote>
XML;

    protected const XML_VALID_MINIMAL_CONTENT = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:DeliveryLocation>
  </cac:DeliveryLocation>
</CreditNote>
XML;

    protected const XML_VALID_NO_LINE = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</CreditNote>
XML;

    protected const XML_INVALID_MANY_LINES = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:DeliveryLocation>
  </cac:DeliveryLocation>
  <cac:DeliveryLocation>
  </cac:DeliveryLocation>
</CreditNote>
XML;

    public function testCanBeCreatedFromFullContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject      = DeliveryLocation::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(DeliveryLocation::class, $ublObject);
        $this->assertInstanceOf(LocationIdentifier::class, $ublObject->getIdentifier());
        $this->assertEquals('83745498753497', $ublObject->getIdentifier()->value);
        $this->assertEquals(InternationalCodeDesignator::tryFrom('0088'), $ublObject->getIdentifier()->scheme);
        $this->assertInstanceOf(DeliveryAddress::class, $ublObject->getAddress());
    }

    public function testCanBeCreatedFromMinimalContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_MINIMAL_CONTENT);
        $ublObject      = DeliveryLocation::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(DeliveryLocation::class, $ublObject);
        $this->assertNull($ublObject->getIdentifier());
        $this->assertNull($ublObject->getAddress());
    }

    public function testCanBeCreatedFromNoLine(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_NO_LINE);
        $ublObject      = DeliveryLocation::fromXML($this->xpath, $currentElement);
        $this->assertNull($ublObject);
    }

    public function testCannotBeCreatedFromManyLines(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MANY_LINES);
        DeliveryLocation::fromXML($this->xpath, $currentElement);
    }

    public function testGenerateXml(): void
    {
        $currentElement  = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject       = DeliveryLocation::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyCreditNoteRootDocument();
        $rootDestination->appendChild($ublObject->toXML($this->document));
        $generatedOutput = $this->formatXMLOutput();
        $this->assertStringEqualsStringIgnoringLineEndings(self::XML_VALID_FULL_CONTENT, $generatedOutput);
    }
}
