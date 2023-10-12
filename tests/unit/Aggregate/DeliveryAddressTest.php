<?php


use Tiime\EN16931\DataType\Identifier\LocationIdentifier;
use Tiime\EN16931\DataType\InternationalCodeDesignator;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\AddressLine;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\Country;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\DeliveryAddress;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\DeliveryLocation;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class DeliveryAddressTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_FULL_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:Address>
    <cac:Country>
      <cbc:IdentificationCode>SE</cbc:IdentificationCode>
    </cac:Country>
    <cbc:StreetName>Delivery Street 1</cbc:StreetName>
    <cbc:AdditionalStreetName>Delivery Street 2</cbc:AdditionalStreetName>
    <cbc:CityName>Malmö</cbc:CityName>
    <cbc:PostalZone>86756</cbc:PostalZone>
    <cbc:CountrySubentity>South Region</cbc:CountrySubentity>
    <cac:AddressLine>
      <cbc:Line>C54</cbc:Line>
    </cac:AddressLine>
  </cac:Address>
</Invoice>
XML;

    protected const XML_VALID_MINIMAL_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:Address>
    <cac:Country>
      <cbc:IdentificationCode>SE</cbc:IdentificationCode>
    </cac:Country>
  </cac:Address>
</Invoice>
XML;

    protected const XML_VALID_NO_LINE = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</Invoice>
XML;

    protected const XML_INVALID_TOO_MANY_LINES = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:DeliveryLocation>
  </cac:DeliveryLocation>
  <cac:DeliveryLocation>
  </cac:DeliveryLocation>
</Invoice>
XML;

    public function testCanBeCreatedFromFullContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject = DeliveryAddress::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(DeliveryAddress::class, $ublObject);
        $this->assertEquals("Delivery Street 1", $ublObject->getStreetName());
        $this->assertEquals("Delivery Street 2", $ublObject->getAdditionalStreetName());
        $this->assertEquals("Malmö", $ublObject->getCityName());
        $this->assertEquals("86756", $ublObject->getPostalZone());
        $this->assertEquals("South Region", $ublObject->getCountrySubentity());
        $this->assertInstanceOf(AddressLine::class, $ublObject->getAddressLine());
        $this->assertInstanceOf(Country::class, $ublObject->getCountry());
    }

    public function testCanBeCreatedFromMinimalContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_MINIMAL_CONTENT);
        $ublObject = DeliveryAddress::fromXML($this->xpath, $currentElement);
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
        $ublObject = DeliveryAddress::fromXML($this->xpath, $currentElement);
        $this->assertNull($ublObject);
    }

    public function testCannotBeCreatedFromTooManyLines(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_TOO_MANY_LINES);
        DeliveryAddress::fromXML($this->xpath, $currentElement);
    }

    public function testGenerateXml(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject = DeliveryAddress::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyRootDocument();
        $rootDestination->appendChild($ublObject->toXML($this->document));
        $generatedOutput = $this->formatXMLOutput();
        $this->assertEquals(self::XML_VALID_FULL_CONTENT, $generatedOutput);
    }
}