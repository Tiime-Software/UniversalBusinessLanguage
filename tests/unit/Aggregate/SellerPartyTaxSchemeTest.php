<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\Aggregate;

use Tiime\EN16931\DataType\Identifier\VatIdentifier;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\SellerPartyTaxScheme;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\TaxScheme;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class SellerPartyTaxSchemeTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PartyTaxScheme>
    <cbc:CompanyID>FR88100000009</cbc:CompanyID>
    <cac:TaxScheme>
      <cbc:ID>VAT</cbc:ID>
    </cac:TaxScheme>
  </cac:PartyTaxScheme>
</Invoice>
XML;

    protected const XML_VALID_NO_LINE = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</Invoice>
XML;

    protected const XML_INVALID_MANY_LINES = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PartyTaxScheme>
    <cbc:CompanyID>FR88100000009</cbc:CompanyID>
    <cac:TaxScheme>
      <cbc:ID>VAT</cbc:ID>
    </cac:TaxScheme>
  </cac:PartyTaxScheme>
  <cac:PartyTaxScheme>
    <cbc:CompanyID>FR88100000009</cbc:CompanyID>
    <cac:TaxScheme>
      <cbc:ID>VAT</cbc:ID>
    </cac:TaxScheme>
  </cac:PartyTaxScheme>
  <cac:PartyTaxScheme>
    <cbc:CompanyID>FR88100000009</cbc:CompanyID>
    <cac:TaxScheme>
      <cbc:ID>VAT</cbc:ID>
    </cac:TaxScheme>
  </cac:PartyTaxScheme>
</Invoice>
XML;

    public function testCanBeCreatedFromContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_CONTENT);
        $ublObjects     = SellerPartyTaxScheme::fromXML($this->xpath, $currentElement);
        $this->assertIsArray($ublObjects);
        $this->assertCount(1, $ublObjects);
        $this->assertInstanceOf(SellerPartyTaxScheme::class, $ublObjects[0]);
        $this->assertInstanceOf(VatIdentifier::class, $ublObjects[0]->getCompanyIdentifier());
        $this->assertInstanceOf(TaxScheme::class, $ublObjects[0]->getTaxScheme());
    }

    public function testCanBeCreatedFromNoLine(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_NO_LINE);
        $ublObjects     = SellerPartyTaxScheme::fromXML($this->xpath, $currentElement);
        $this->assertIsArray($ublObjects);
        $this->assertCount(0, $ublObjects);
    }

    public function testCannotBeCreatedFromManyLines(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MANY_LINES);
        SellerPartyTaxScheme::fromXML($this->xpath, $currentElement);
    }

    public function testGenerateXml(): void
    {
        $currentElement  = $this->loadXMLDocument(self::XML_VALID_CONTENT);
        $ublObjects      = SellerPartyTaxScheme::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyRootDocument();

        foreach ($ublObjects as $ublObject) {
            $rootDestination->appendChild($ublObject->toXML($this->document));
        }
        $generatedOutput = $this->formatXMLOutput();
        $this->assertStringEqualsStringIgnoringLineEndings(self::XML_VALID_CONTENT, $generatedOutput);
    }
}
