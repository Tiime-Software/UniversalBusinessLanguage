<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\Aggregate;

use Tiime\UniversalBusinessLanguage\DataType\Aggregate\TaxScheme;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class TaxSchemeTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:TaxScheme>
    <cbc:ID>VAT</cbc:ID>
  </cac:TaxScheme>
</Invoice>
XML;

    protected const XML_INVALID_NO_LINE = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</Invoice>
XML;

    protected const XML_INVALID_TOO_MANY_IDENTIFIERS = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:TaxScheme>
    <cbc:ID>VAT</cbc:ID>
    <cbc:ID>VAT</cbc:ID>
  </cac:TaxScheme>
</Invoice>
XML;

    protected const XML_INVALID_NOT_ENOUGH_IDENTIFIER = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:TaxScheme>
  </cac:TaxScheme>
</Invoice>
XML;

    public function testCanBeCreatedFromFullContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_CONTENT);
        $ublObject      = TaxScheme::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(TaxScheme::class, $ublObject);
        $this->assertEquals('VAT', $ublObject->getIdentifier());
    }

    public function testCannotBeCreatedFromNoLine(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_NO_LINE);
        TaxScheme::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromTooManyIdentifiers(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_TOO_MANY_IDENTIFIERS);
        TaxScheme::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromNotEnoughIdentifier(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_NOT_ENOUGH_IDENTIFIER);
        TaxScheme::fromXML($this->xpath, $currentElement);
    }

    public function testGenerateXml(): void
    {
        $currentElement  = $this->loadXMLDocument(self::XML_VALID_CONTENT);
        $ublObject       = TaxScheme::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyRootDocument();
        $rootDestination->appendChild($ublObject->toXML($this->document));
        $generatedOutput = $this->formatXMLOutput();
        $this->assertEquals(self::XML_VALID_CONTENT, $generatedOutput);
    }
}
