<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\Ubl21Standard\CreditNote\Aggregate;

use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;
use Tiime\UniversalBusinessLanguage\Ubl21Standard\CreditNote\DataType\Aggregate\AddressLine;
use Tiime\UniversalBusinessLanguage\Ubl21Standard\CreditNote\DataType\Aggregate\Country;
use Tiime\UniversalBusinessLanguage\Ubl21Standard\CreditNote\DataType\Aggregate\DeliveryAddress;

class CreditNoteDeliveryAddressTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_FULL_CONTENT = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
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
</CreditNote>
XML;

    protected const XML_VALID_MINIMAL_CONTENT = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:Address>
    <cac:Country>
      <cbc:IdentificationCode>SE</cbc:IdentificationCode>
    </cac:Country>
  </cac:Address>
</CreditNote>
XML;

    protected const XML_VALID_NO_LINE = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</CreditNote>
XML;

    protected const XML_INVALID_MANY_LINES = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:Address>
  </cac:Address>
  <cac:Address>
  </cac:Address>
</CreditNote>
XML;

    public function testCanBeCreatedFromFullContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject      = DeliveryAddress::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(DeliveryAddress::class, $ublObject);
        $this->assertEquals('Delivery Street 1', $ublObject->getStreetName());
        $this->assertEquals('Delivery Street 2', $ublObject->getAdditionalStreetName());
        $this->assertEquals('Malmö', $ublObject->getCityName());
        $this->assertEquals('86756', $ublObject->getPostalZone());
        $this->assertEquals('South Region', $ublObject->getCountrySubentity());
        $this->assertInstanceOf(AddressLine::class, $ublObject->getAddressLine());
        $this->assertInstanceOf(Country::class, $ublObject->getCountry());
    }

    public function testCanBeCreatedFromMinimalContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_MINIMAL_CONTENT);
        $ublObject      = DeliveryAddress::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(DeliveryAddress::class, $ublObject);
        $this->assertNull($ublObject->getStreetName());
        $this->assertNull($ublObject->getAdditionalStreetName());
        $this->assertNull($ublObject->getCityName());
        $this->assertNull($ublObject->getPostalZone());
        $this->assertNull($ublObject->getCountrySubentity());
        $this->assertNull($ublObject->getAddressLine());
        $this->assertInstanceOf(Country::class, $ublObject->getCountry());
    }

    public function testCanBeCreatedFromNoLine(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_NO_LINE);
        $ublObject      = DeliveryAddress::fromXML($this->xpath, $currentElement);
        $this->assertNull($ublObject);
    }

    public function testCannotBeCreatedFromManyLines(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MANY_LINES);
        DeliveryAddress::fromXML($this->xpath, $currentElement);
    }

    public function testGenerateXml(): void
    {
        $currentElement  = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject       = DeliveryAddress::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyCreditNoteRootDocument();
        $rootDestination->appendChild($ublObject->toXML($this->document));
        $generatedOutput = $this->formatXMLOutput();
        $this->assertStringEqualsStringIgnoringLineEndings(self::XML_VALID_FULL_CONTENT, $generatedOutput);
    }
}
