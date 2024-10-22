<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\PeppolBIS\CreditNote\Aggregate;

use Tiime\UniversalBusinessLanguage\PeppolBIS\CreditNote\DataType\Aggregate\Contact;
use Tiime\UniversalBusinessLanguage\PeppolBIS\CreditNote\DataType\Aggregate\PartyName;
use Tiime\UniversalBusinessLanguage\PeppolBIS\CreditNote\DataType\Aggregate\PostalAddress;
use Tiime\UniversalBusinessLanguage\PeppolBIS\CreditNote\DataType\Aggregate\SellerParty;
use Tiime\UniversalBusinessLanguage\PeppolBIS\CreditNote\DataType\Aggregate\SellerPartyIdentification;
use Tiime\UniversalBusinessLanguage\PeppolBIS\CreditNote\DataType\Aggregate\SellerPartyLegalEntity;
use Tiime\UniversalBusinessLanguage\PeppolBIS\CreditNote\DataType\Basic\EndpointIdentifier;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class CreditNoteSellerPartyTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_FULL_CONTENT = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:Party>
    <cbc:EndpointID schemeID="EM">VendeurCanal1.00017@100000009.ppf</cbc:EndpointID>
    <cac:PartyIdentification>
      <cbc:ID schemeID="0009">10000000900017</cbc:ID>
    </cac:PartyIdentification>
    <cac:PartyName>
      <cbc:Name>SELLER TRADE NAME</cbc:Name>
    </cac:PartyName>
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
    <cac:PartyTaxScheme>
      <cbc:CompanyID>FR88100000009</cbc:CompanyID>
      <cac:TaxScheme>
        <cbc:ID>VAT</cbc:ID>
      </cac:TaxScheme>
    </cac:PartyTaxScheme>
    <cac:PartyLegalEntity>
      <cbc:RegistrationName>LE FOURNISSEUR</cbc:RegistrationName>
      <cbc:CompanyID schemeID="0002">100000009</cbc:CompanyID>
      <cbc:CompanyLegalForm>SARL au capital de 50 000 EUR</cbc:CompanyLegalForm>
    </cac:PartyLegalEntity>
    <cac:Contact>
      <cbc:Name>Contact Fournisseur</cbc:Name>
      <cbc:Telephone>01 02 03 04 05</cbc:Telephone>
      <cbc:ElectronicMail>contact@vendeur.com</cbc:ElectronicMail>
    </cac:Contact>
  </cac:Party>
</CreditNote>
XML;

    protected const XML_VALID_MINIMAL_CONTENT = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
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
</CreditNote>
XML;

    protected const XML_INVALID_NO_LINE = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</CreditNote>
XML;

    protected const XML_INVALID_MANY_CONTENTS = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</CreditNote>
XML;

    public function testCanBeCreatedFromFullContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject      = SellerParty::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(SellerParty::class, $ublObject);
        $this->assertInstanceOf(EndpointIdentifier::class, $ublObject->getEndpointIdentifier());
        $this->assertIsArray($ublObject->getPartyIdentifications());
        $identifiers = $ublObject->getPartyIdentifications();

        foreach ($identifiers as $identifier) {
            $this->assertInstanceOf(SellerPartyIdentification::class, $identifier);
        }
        $this->assertInstanceOf(PartyName::class, $ublObject->getPartyName());
        $this->assertInstanceOf(PostalAddress::class, $ublObject->getPostalAddress());
        $this->assertIsArray($ublObject->getPartyTaxSchemes());
        $this->assertCount(1, $ublObject->getPartyTaxSchemes());
        $this->assertInstanceOf(SellerPartyLegalEntity::class, $ublObject->getPartyLegalEntity());
        $this->assertInstanceOf(Contact::class, $ublObject->getContact());
    }

    public function testCanBeCreatedFromMinimalContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_MINIMAL_CONTENT);
        $ublObject      = SellerParty::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(SellerParty::class, $ublObject);
        $this->assertInstanceOf(EndpointIdentifier::class, $ublObject->getEndpointIdentifier());
        $this->assertIsArray($ublObject->getPartyIdentifications());
        $identifiers = $ublObject->getPartyIdentifications();

        foreach ($identifiers as $identifier) {
            $this->assertInstanceOf(SellerPartyIdentification::class, $identifier);
        }
        $this->assertNull($ublObject->getPartyName());
        $this->assertInstanceOf(PostalAddress::class, $ublObject->getPostalAddress());
        $this->assertIsArray($ublObject->getPartyTaxSchemes());
        $this->assertCount(0, $ublObject->getPartyTaxSchemes());
        $this->assertInstanceOf(SellerPartyLegalEntity::class, $ublObject->getPartyLegalEntity());
        $this->assertNull($ublObject->getContact());
    }

    public function testCannotBeCreatedFromNoLine(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_NO_LINE);
        SellerParty::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromManyContents(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MANY_CONTENTS);
        SellerParty::fromXML($this->xpath, $currentElement);
    }

    public function testGenerateXml(): void
    {
        $currentElement  = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject       = SellerParty::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyCreditNoteRootDocument();
        $rootDestination->appendChild($ublObject->toXML($this->document));
        $generatedOutput = $this->formatXMLOutput();
        $this->assertStringEqualsStringIgnoringLineEndings(self::XML_VALID_FULL_CONTENT, $generatedOutput);
    }
}
