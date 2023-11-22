<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\CreditNote\Aggregate;

use Tiime\UniversalBusinessLanguage\CreditNote\DataType\Aggregate\PostalAddress;
use Tiime\UniversalBusinessLanguage\CreditNote\DataType\Aggregate\TaxRepresentativeParty;
use Tiime\UniversalBusinessLanguage\CreditNote\DataType\Aggregate\TaxRepresentativePartyName;
use Tiime\UniversalBusinessLanguage\CreditNote\DataType\Aggregate\TaxRepresentativePartyTaxScheme;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class TaxRepresentativePartyTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:TaxRepresentativeParty>
    <cac:PartyName>
      <cbc:Name>SELLER TAX REP</cbc:Name>
    </cac:PartyName>
    <cac:PostalAddress>
      <cbc:StreetName>1, rue du représentant fiscal</cbc:StreetName>
      <cbc:AdditionalStreetName>Venelle du représentant fiscal</cbc:AdditionalStreetName>
      <cbc:CityName>PARIS</cbc:CityName>
      <cbc:PostalZone>75018</cbc:PostalZone>
      <cbc:CountrySubentity>Ile de France</cbc:CountrySubentity>
      <cac:AddressLine>
        <cbc:Line>Ruelle du représentant fiscal</cbc:Line>
      </cac:AddressLine>
      <cac:Country>
        <cbc:IdentificationCode>FR</cbc:IdentificationCode>
      </cac:Country>
    </cac:PostalAddress>
    <cac:PartyTaxScheme>
      <cbc:CompanyID>FR32400000006</cbc:CompanyID>
      <cac:TaxScheme>
        <cbc:ID>VAT</cbc:ID>
      </cac:TaxScheme>
    </cac:PartyTaxScheme>
  </cac:TaxRepresentativeParty>
</Invoice>
XML;

    protected const XML_VALID_NO_LINE = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</Invoice>
XML;

    public function testCanBeCreatedFromFullContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_CONTENT);
        $ublObject      = TaxRepresentativeParty::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(TaxRepresentativeParty::class, $ublObject);
        $this->assertInstanceOf(TaxRepresentativePartyName::class, $ublObject->getPartyName());
        $this->assertInstanceOf(PostalAddress::class, $ublObject->getPostalAddress());
        $this->assertInstanceOf(TaxRepresentativePartyTaxScheme::class, $ublObject->getPartyTaxScheme());
    }

    public function testCanBeCreatedFromNoLine(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_NO_LINE);
        $ublObject      = TaxRepresentativeParty::fromXML($this->xpath, $currentElement);
        $this->assertNull($ublObject);
    }

    public function testGenerateXml(): void
    {
        $currentElement  = $this->loadXMLDocument(self::XML_VALID_CONTENT);
        $ublObject       = TaxRepresentativeParty::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyRootDocument();
        $rootDestination->appendChild($ublObject->toXML($this->document));
        $generatedOutput = $this->formatXMLOutput();
        $this->assertStringEqualsStringIgnoringLineEndings(self::XML_VALID_CONTENT, $generatedOutput);
    }
}
