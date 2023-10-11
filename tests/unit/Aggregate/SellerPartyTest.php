<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\Aggregate;

use Tiime\EN16931\DataType\VatCategory;
use Tiime\EN16931\DataType\VatExoneration;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\Contact;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\PartyName;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\PostalAddress;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\SellerParty;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\SellerPartyIdentification;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\SubtotalTaxCategory;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\TaxScheme;
use Tiime\UniversalBusinessLanguage\DataType\Basic\EndpointIdentifier;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class SellerPartyTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_FULL_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:Party>
    <cbc:EndpointID schemeID="EM">VendeurCanal1.00017@100000009.ppf</cbc:EndpointID>
    <cac:PartyIdentification>
      <cbc:ID schemeID="0009">10000000900017</cbc:ID>
    </cac:PartyIdentification>
    <cac:PartyLegalEntity>
      <cbc:RegistrationName>LE FOURNISSEUR</cbc:RegistrationName>
      <cbc:CompanyLegalForm>SARL au capital de 50 000 EUR</cbc:CompanyLegalForm>
      <cbc:CompanyID schemeID="0002">100000009</cbc:CompanyID>
    </cac:PartyLegalEntity>
    <cac:PartyTaxScheme>
      <cbc:CompanyID>FR88100000009</cbc:CompanyID>
      <cac:TaxScheme>
        <cbc:ID>VAT</cbc:ID>
      </cac:TaxScheme>
    </cac:PartyTaxScheme>
    <cac:PartyName>
      <cbc:Name>SELLER TRADE NAME</cbc:Name>
    </cac:PartyName>
    <cac:PostalAddress>
      <cac:Country>
        <cbc:IdentificationCode>FR</cbc:IdentificationCode>
      </cac:Country>
      <cbc:StreetName>1, rue du fournisseur</cbc:StreetName>
      <cbc:AdditionalStreetName>Cour du fournisseur</cbc:AdditionalStreetName>
      <cbc:CityName>Quimper</cbc:CityName>
      <cbc:PostalZone>29000</cbc:PostalZone>
      <cbc:CountrySubentity>Bretagne</cbc:CountrySubentity>
      <cac:AddressLine>
        <cbc:Line>BATIMENT DU FOURNISSEUR</cbc:Line>
      </cac:AddressLine>
    </cac:PostalAddress>
    <cac:Contact>
      <cbc:Name>Contact Fournisseur</cbc:Name>
      <cbc:Telephone>01 02 03 04 05</cbc:Telephone>
      <cbc:ElectronicMail>contact@vendeur.com</cbc:ElectronicMail>
    </cac:Contact>
  </cac:Party>
</Invoice>
XML;

    protected const XML_VALID_MINIMAL_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:Party>
    <cbc:EndpointID schemeID="EM">VendeurCanal1.00017@100000009.ppf</cbc:EndpointID>
    <cac:PostalAddress>
      <cac:Country>
        <cbc:IdentificationCode>FR</cbc:IdentificationCode>
      </cac:Country>
    </cac:PostalAddress>
    <cac:PartyLegalEntity>
      <cbc:RegistrationName>LE FOURNISSEUR</cbc:RegistrationName>
      <cbc:CompanyID schemeID="0002">100000009</cbc:CompanyID>
      <cbc:CompanyLegalForm>SARL au capital de 50 000 EUR</cbc:CompanyLegalForm>
    </cac:PartyLegalEntity>
  </cac:Party>
</Invoice>
XML;

    protected const XML_INVALID_NO_LINE = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</Invoice>
XML;

    protected const XML_INVALID_TOO_MANY_TAX_CATEGORY = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</Invoice>
XML;

    public function testCanBeCreatedFromFullContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject = SellerParty::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(SellerParty::class, $ublObject);
        $this->assertInstanceOf(EndpointIdentifier::class, $ublObject->getEndpointIdentifier());
        $this->assertIsArray($ublObject->getPartyIdentifications());
        $identifiers = $ublObject->getPartyIdentifications();
        foreach($identifiers as $identifier) {
            $this->assertInstanceOf(SellerPartyIdentification::class, $identifier);
        }
        $this->assertInstanceOf(PartyName::class, $ublObject->getPartyName());
        $this->assertInstanceOf(PostalAddress::class, $ublObject->getPostalAddress());
        $this->assertInstanceOf(Contact::class, $ublObject->getContact());
    }

    public function testCanBeCreatedFromMinimalContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_MINIMAL_CONTENT);
        $ublObject = SellerParty::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(SellerParty::class, $ublObject);
        $this->assertInstanceOf(EndpointIdentifier::class, $ublObject->getEndpointIdentifier());
        $this->assertIsArray($ublObject->getPartyIdentifications());
        $identifiers = $ublObject->getPartyIdentifications();
        foreach($identifiers as $identifier) {
            $this->assertInstanceOf(SellerPartyIdentification::class, $identifier);
        }
        $this->assertNull($ublObject->getPartyName());
        $this->assertInstanceOf(PostalAddress::class, $ublObject->getPostalAddress());
        $this->assertNull($ublObject->getContact());

    }

    public function testCannotBeCreatedFromNoLine(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_NO_LINE);
        SellerParty::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromTooManyTaxTotals(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_TOO_MANY_TAX_CATEGORY);
        SellerParty::fromXML($this->xpath, $currentElement);
    }

    public function testGenerateXml(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject = SellerParty::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyRootDocument();
        $rootDestination->appendChild($ublObject->toXML($this->document));
        $generatedOutput = $this->formatXMLOutput();
        $this->assertEquals(self::XML_VALID_FULL_CONTENT, $generatedOutput);
    }
}