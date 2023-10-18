<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\Aggregate;

use Tiime\UniversalBusinessLanguage\DataType\Aggregate\BuyerParty;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\BuyerPartyIdentification;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\BuyerPartyLegalEntity;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\BuyerPartyTaxScheme;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\Contact;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\PartyName;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\PostalAddress;
use Tiime\UniversalBusinessLanguage\DataType\Basic\EndpointIdentifier;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class BuyerPartyTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_FULL_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:Party>
    <cbc:EndpointID schemeID="0192">987654321</cbc:EndpointID>
    <cac:PartyIdentification>
      <cbc:ID schemeID="0088">SE8765456787</cbc:ID>
    </cac:PartyIdentification>
    <cac:PartyName>
      <cbc:Name>Buyer Trading Name</cbc:Name>
    </cac:PartyName>
    <cac:PostalAddress>
      <cbc:StreetName>Hovudgatan 32</cbc:StreetName>
      <cbc:AdditionalStreetName>Po box 43</cbc:AdditionalStreetName>
      <cbc:CityName>Stockholm</cbc:CityName>
      <cbc:PostalZone>34567</cbc:PostalZone>
      <cbc:CountrySubentity>Region A</cbc:CountrySubentity>
      <cac:AddressLine>
        <cbc:Line>Building F2</cbc:Line>
      </cac:AddressLine>
      <cac:Country>
        <cbc:IdentificationCode>SE</cbc:IdentificationCode>
      </cac:Country>
    </cac:PostalAddress>
    <cac:PartyTaxScheme>
      <cbc:CompanyID>SE8765456787</cbc:CompanyID>
      <cac:TaxScheme>
        <cbc:ID>VAT</cbc:ID>
      </cac:TaxScheme>
    </cac:PartyTaxScheme>
    <cac:PartyLegalEntity>
      <cbc:RegistrationName>Buyer Full Name AS</cbc:RegistrationName>
      <cbc:CompanyID schemeID="0007">5560104525</cbc:CompanyID>
    </cac:PartyLegalEntity>
    <cac:Contact>
      <cbc:Name>Jens Jensen</cbc:Name>
      <cbc:Telephone>876 654 321</cbc:Telephone>
      <cbc:ElectronicMail>jens.j@buyer.se</cbc:ElectronicMail>
    </cac:Contact>
  </cac:Party>
</Invoice>
XML;

    protected const XML_VALID_MINIMAL_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:Party>
    <cbc:EndpointID schemeID="0192">987654321</cbc:EndpointID>
    <cac:PostalAddress>
      <cac:Country>
        <cbc:IdentificationCode>SE</cbc:IdentificationCode>
      </cac:Country>
    </cac:PostalAddress>
    <cac:PartyLegalEntity>
      <cbc:RegistrationName>Buyer Full Name AS</cbc:RegistrationName>
    </cac:PartyLegalEntity>
  </cac:Party>
</Invoice>
XML;

    protected const XML_INVALID_NO_LINE = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</Invoice>
XML;

    protected const XML_INVALID_NOT_ENOUGH_DATA = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
    <cac:Party>
      <cbc:EndpointID schemeID="0192">987654321</cbc:EndpointID>
    </cac:Party>
</Invoice>
XML;

    protected const XML_INVALID_MANY_ENTRIES = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:Party>
    <cbc:EndpointID schemeID="0192">987654321</cbc:EndpointID>
    <cbc:EndpointID schemeID="0193">987654322</cbc:EndpointID>
    <cac:PostalAddress>
      <cac:Country>
        <cbc:IdentificationCode>SE</cbc:IdentificationCode>
      </cac:Country>
    </cac:PostalAddress>
    <cac:PartyLegalEntity>
      <cbc:RegistrationName>Buyer Full Name AS</cbc:RegistrationName>
    </cac:PartyLegalEntity>
  </cac:Party>
</Invoice>
XML;

    protected const XML_INVALID_MANY_LINES = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:Party>
    <cbc:EndpointID schemeID="0192">987654321</cbc:EndpointID>
    <cac:PostalAddress>
      <cac:Country>
        <cbc:IdentificationCode>SE</cbc:IdentificationCode>
      </cac:Country>
    </cac:PostalAddress>
    <cac:PartyLegalEntity>
      <cbc:RegistrationName>Buyer Full Name AS</cbc:RegistrationName>
    </cac:PartyLegalEntity>
  </cac:Party>
  <cac:Party>
    <cbc:EndpointID schemeID="0192">987654321</cbc:EndpointID>
    <cac:PostalAddress>
      <cac:Country>
        <cbc:IdentificationCode>SE</cbc:IdentificationCode>
      </cac:Country>
    </cac:PostalAddress>
    <cac:PartyLegalEntity>
      <cbc:RegistrationName>Buyer Full Name AS</cbc:RegistrationName>
    </cac:PartyLegalEntity>
  </cac:Party>
</Invoice>
XML;

    public function testCanBeCreatedFromFullContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject      = BuyerParty::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(BuyerParty::class, $ublObject);
        $this->assertInstanceOf(EndpointIdentifier::class, $ublObject->getEndpointIdentifier());
        $this->assertInstanceOf(BuyerPartyIdentification::class, $ublObject->getPartyIdentification());
        $this->assertInstanceOf(BuyerPartyLegalEntity::class, $ublObject->getPartyLegalEntity());
        $this->assertInstanceOf(BuyerPartyTaxScheme::class, $ublObject->getPartyTaxScheme());
        $this->assertInstanceOf(PartyName::class, $ublObject->getPartyName());
        $this->assertInstanceOf(PostalAddress::class, $ublObject->getPostalAddress());
        $this->assertInstanceOf(Contact::class, $ublObject->getContact());
    }

    public function testCanBeCreatedFromMinimalContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_MINIMAL_CONTENT);
        $ublObject      = BuyerParty::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(BuyerParty::class, $ublObject);
        $this->assertInstanceOf(EndpointIdentifier::class, $ublObject->getEndpointIdentifier());
        $this->assertNull($ublObject->getPartyIdentification());
        $this->assertInstanceOf(BuyerPartyLegalEntity::class, $ublObject->getPartyLegalEntity());
        $this->assertNull($ublObject->getPartyTaxScheme());
        $this->assertNull($ublObject->getPartyName());
        $this->assertInstanceOf(PostalAddress::class, $ublObject->getPostalAddress());
        $this->assertNull($ublObject->getContact());
    }

    public function testCannotBeCreatedFromNoLine(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_NO_LINE);
        BuyerParty::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromNotEnoughData(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_NOT_ENOUGH_DATA);
        BuyerParty::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromManyEntries(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MANY_LINES);
        BuyerParty::fromXML($this->xpath, $currentElement);
    }

    public function testGenerateXml(): void
    {
        $currentElement  = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject       = BuyerParty::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyRootDocument();
        $rootDestination->appendChild($ublObject->toXML($this->document));
        $generatedOutput = $this->formatXMLOutput();
        $this->assertEquals(self::XML_VALID_FULL_CONTENT, $generatedOutput);
    }
}
