<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\Ubl21\Invoice\Basic;

use Tiime\EN16931\Codelist\ElectronicAddressSchemeCode as ElectronicAddressScheme;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;
use Tiime\UniversalBusinessLanguage\Ubl21\Invoice\DataType\Basic\EndpointIdentifier;

class EndpointIdentifierTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cbc:EndpointID schemeID="EM">VendeurCanal1.00017@100000009.ppf</cbc:EndpointID>
</Invoice>
XML;

    protected const XML_INVALID_SCHEMEID = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cbc:EndpointID schemeID="AZ42">VendeurCanal1.00017@100000009.ppf</cbc:EndpointID>
</Invoice>
XML;

    protected const XML_INVALID_MULTIPLE_CODES = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cbc:EndpointID schemeID="EM">VendeurCanal1.00017@100000009.ppf</cbc:EndpointID>
  <cbc:EndpointID schemeID="EM">VendeurCanal1.00017@100000009.ppf</cbc:EndpointID>
</Invoice>
XML;

    public function testCanBeCreatedFromContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_CONTENT);
        $ublObject      = EndpointIdentifier::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(EndpointIdentifier::class, $ublObject);
        $this->assertEquals(ElectronicAddressScheme::tryFrom('EM'), $ublObject->scheme);
        $this->assertEquals('VendeurCanal1.00017@100000009.ppf', $ublObject->value);
    }

    public function testCannotBeCreatedFromInvalidSchemeID(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_SCHEMEID);
        EndpointIdentifier::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromMultipleCodes(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MULTIPLE_CODES);
        EndpointIdentifier::fromXML($this->xpath, $currentElement);
    }

    public function testGenerateXml(): void
    {
        $currentElement  = $this->loadXMLDocument(self::XML_VALID_CONTENT);
        $ublObject       = EndpointIdentifier::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyInvoiceRootDocument();
        $rootDestination->appendChild($ublObject->toXML($this->document));
        $generatedOutput = $this->formatXMLOutput();
        $this->assertStringEqualsStringIgnoringLineEndings(self::XML_VALID_CONTENT, $generatedOutput);
    }
}
