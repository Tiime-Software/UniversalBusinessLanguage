<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\Ubl21\CreditNote\Aggregate;

use Tiime\UniversalBusinessLanguage\Ubl21\CreditNote\DataType\Aggregate\AddressLine;
use Tiime\UniversalBusinessLanguage\Ubl21\CreditNote\DataType\Aggregate\Country;
use Tiime\UniversalBusinessLanguage\Ubl21\CreditNote\DataType\Aggregate\PostalAddress;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class CreditNotePostalAddressTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_FULL_CONTENT = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PostalAddress>
    <cbc:StreetName>1, rue du fournisseur</cbc:StreetName>
    <cbc:AdditionalStreetName>Cour du fournisseur</cbc:AdditionalStreetName>
    <cbc:CityName>Quimper</cbc:CityName>
    <cbc:PostalZone>29000</cbc:PostalZone>
    <cbc:CountrySubentity>Bretagne</cbc:CountrySubentity>
    <cac:AddressLine>
      <cbc:Line>BATIMENT DU FOURNISSEUR</cbc:Line>
    </cac:AddressLine>
    <cac:Country>
      <cbc:IdentificationCode>FR</cbc:IdentificationCode>
    </cac:Country>
  </cac:PostalAddress>
</CreditNote>
XML;

    protected const XML_VALID_MINIMAL_CONTENT = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PostalAddress>
    <cac:Country>
      <cbc:IdentificationCode>FR</cbc:IdentificationCode>
    </cac:Country>
  </cac:PostalAddress>
</CreditNote>
XML;

    protected const XML_INVALID_NO_COUNTRY = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PostalAddress>
  </cac:PostalAddress>
</CreditNote>
XML;

    protected const XML_INVALID_MANY_CONTENTS = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PostalAddress>
    <cbc:StreetName>1, rue du fournisseur</cbc:StreetName>
    <cbc:StreetName>Cour du fournisseur</cbc:StreetName>
    <cac:Country>
      <cbc:IdentificationCode>FR</cbc:IdentificationCode>
    </cac:Country>
  </cac:PostalAddress>
</CreditNote>
XML;

    protected const XML_INVALID_MANY_LINES = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PostalAddress>
    <cbc:StreetName>1, rue du fournisseur</cbc:StreetName>
    <cac:Country>
      <cbc:IdentificationCode>FR</cbc:IdentificationCode>
    </cac:Country>
  </cac:PostalAddress>
  <cac:PostalAddress>
    <cbc:StreetName>1, rue du fournisseur</cbc:StreetName>
    <cac:Country>
      <cbc:IdentificationCode>FR</cbc:IdentificationCode>
    </cac:Country>
  </cac:PostalAddress>
</CreditNote>
XML;

    public function testCanBeCreatedFromFullContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject      = PostalAddress::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(PostalAddress::class, $ublObject);
        $this->assertEquals('1, rue du fournisseur', $ublObject->getStreetName());
        $this->assertEquals('Cour du fournisseur', $ublObject->getAdditionalStreetName());
        $this->assertEquals('Quimper', $ublObject->getCityName());
        $this->assertEquals('29000', $ublObject->getPostalZone());
        $this->assertEquals('Bretagne', $ublObject->getCountrySubentity());
        $this->assertInstanceOf(AddressLine::class, $ublObject->getAddressLine());
        $this->assertInstanceOf(Country::class, $ublObject->getCountry());
    }

    public function testCanBeCreatedFromMinimalContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_MINIMAL_CONTENT);
        $ublObject      = PostalAddress::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(Country::class, $ublObject->getCountry());
        $this->assertNull($ublObject->getStreetName());
        $this->assertNull($ublObject->getAdditionalStreetName());
        $this->assertNull($ublObject->getCityName());
        $this->assertNull($ublObject->getPostalZone());
        $this->assertNull($ublObject->getCountrySubentity());
        $this->assertNull($ublObject->getAddressLine());
        $this->assertInstanceOf(Country::class, $ublObject->getCountry());
    }

    public function testCannotBeCreatedFromNoCountry(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_NO_COUNTRY);
        PostalAddress::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromManyContents(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MANY_CONTENTS);
        PostalAddress::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromManyLines(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MANY_LINES);
        PostalAddress::fromXML($this->xpath, $currentElement);
    }

    public function testGenerateXml(): void
    {
        $currentElement  = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject       = PostalAddress::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyCreditNoteRootDocument();
        $rootDestination->appendChild($ublObject->toXML($this->document));
        $generatedOutput = $this->formatXMLOutput();
        $this->assertStringEqualsStringIgnoringLineEndings(self::XML_VALID_FULL_CONTENT, $generatedOutput);
    }
}
