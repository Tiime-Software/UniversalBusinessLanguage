<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\PeppolBIS\Invoice\Aggregate;

use Tiime\EN16931\Codelist\CountryAlpha2Code;
use Tiime\UniversalBusinessLanguage\PeppolBIS\Invoice\DataType\Aggregate\Country;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class CountryTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:Country>
    <cbc:IdentificationCode>FR</cbc:IdentificationCode>
  </cac:Country>
</Invoice>
XML;

    protected const XML_INVALID_NO_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:Country>
  </cac:Country>
</Invoice>
XML;

    protected const XML_INVALID_WRONG_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:Country>
    <cbc:IdentificationCode>ZZ01</cbc:IdentificationCode>
  </cac:Country>
</Invoice>
XML;

    protected const XML_INVALID_MANY_CONTENTS = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:Country>
    <cbc:IdentificationCode>FR</cbc:IdentificationCode>
    <cbc:IdentificationCode>FR</cbc:IdentificationCode>
  </cac:Country>
</Invoice>
XML;

    protected const XML_INVALID_MANY_LINES = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:Country>
    <cbc:IdentificationCode>FR</cbc:IdentificationCode>
  </cac:Country>
  <cac:Country>
    <cbc:IdentificationCode>DE</cbc:IdentificationCode>
  </cac:Country>
</Invoice>
XML;

    public function testCanBeCreatedFromContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_CONTENT);
        $ublObject      = Country::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(Country::class, $ublObject);
        $this->assertInstanceOf(CountryAlpha2Code::class, $ublObject->getIdentificationCode());
        $this->assertEquals(CountryAlpha2Code::tryFrom('FR'), $ublObject->getIdentificationCode());
    }

    public function testCannotBeCreatedFromNoContent(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_NO_CONTENT);
        Country::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromWrongContent(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_WRONG_CONTENT);
        Country::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromManyContents(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MANY_CONTENTS);
        Country::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromManyLines(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MANY_LINES);
        Country::fromXML($this->xpath, $currentElement);
    }

    public function testGenerateXml(): void
    {
        $currentElement  = $this->loadXMLDocument(self::XML_VALID_CONTENT);
        $ublObject       = Country::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyInvoiceRootDocument();
        $rootDestination->appendChild($ublObject->toXML($this->document));
        $generatedOutput = $this->formatXMLOutput();
        $this->assertStringEqualsStringIgnoringLineEndings(self::XML_VALID_CONTENT, $generatedOutput);
    }
}