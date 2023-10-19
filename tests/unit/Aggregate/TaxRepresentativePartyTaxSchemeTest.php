<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\Aggregate;

use Tiime\EN16931\DataType\Identifier\VatIdentifier;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\SubtotalTaxCategory;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\TaxRepresentativePartyTaxScheme;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\TaxScheme;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class TaxRepresentativePartyTaxSchemeTest extends BaseXMLNodeTestWithHelpers
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

    protected const XML_INVALID_NO_LINE = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</Invoice>
XML;

    protected const XML_INVALID_TOO_MANY_LINES = <<<XML
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
</Invoice>
XML;

    public function testCanBeCreatedFromContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_CONTENT);
        $ublObject      = TaxRepresentativePartyTaxScheme::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(TaxRepresentativePartyTaxScheme::class, $ublObject);
        $this->assertInstanceOf(VatIdentifier::class, $ublObject->getCompanyIdentifier());
        $this->assertInstanceOf(TaxScheme::class, $ublObject->getTaxScheme());
    }

    public function testCannotBeCreatedFromNoLine(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_NO_LINE);
        SubtotalTaxCategory::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromManyLines(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_TOO_MANY_LINES);
        SubtotalTaxCategory::fromXML($this->xpath, $currentElement);
    }

    public function testGenerateXml(): void
    {
        $currentElement  = $this->loadXMLDocument(self::XML_VALID_CONTENT);
        $ublObject       = TaxRepresentativePartyTaxScheme::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyRootDocument();
        $rootDestination->appendChild($ublObject->toXML($this->document));
        $generatedOutput = $this->formatXMLOutput();
        $this->assertStringEqualsStringIgnoringLineEndings(self::XML_VALID_CONTENT, $generatedOutput);
    }
}
