<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit;

use Tiime\UniversalBusinessLanguage\DataType\Aggregate\ExternalReference;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class UniversalBusinessLanguageTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_CONTENT = <<<XML
XML;
    protected string $xmlValidContent = "";

    protected const XML_VALID_NO_LINE = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</Invoice>
XML;

    protected const XML_INVALID_NOT_ENOUGH_URI = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:ExternalReference>
  </cac:ExternalReference>
</Invoice>
XML;

    protected const XML_INVALID_TOO_MANY_LINES = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:ExternalReference>
    <cbc:URI>http://www.example.com/index1.html</cbc:URI>
  </cac:ExternalReference>
  <cac:ExternalReference>
    <cbc:URI>http://www.example.com/index2.html</cbc:URI>
  </cac:ExternalReference>
</Invoice>
XML;

    public function setUp(): void
    {
        parent::setUp();
        $this->xmlValidContent = file_get_contents('../sample/ubl_fullcontent.xml');

    }

    public function testCanBeCreatedFromContent(): void
    {
        $currentElement = $this->loadXMLDocument($this->xmlValidContent);
        $ublObject = ExternalReference::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(ExternalReference::class, $ublObject);
        $this->assertEquals("http://www.example.com/index.html", $ublObject->getUri());
    }

    public function testCanBeCreatedFromNoLine(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_NO_LINE);
        $ublObject = ExternalReference::fromXML($this->xpath, $currentElement);
        $this->assertNull($ublObject);
    }

    public function testCannotBeCreatedFromNotEnoughLines(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_NOT_ENOUGH_URI);
        ExternalReference::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromTooManyLines(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_TOO_MANY_LINES);
        ExternalReference::fromXML($this->xpath, $currentElement);
    }

    public function testGenerateXml(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_CONTENT);
        $ublObject = ExternalReference::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyRootDocument();
        $rootDestination->appendChild($ublObject->toXML($this->document));
        $generatedOutput = $this->formatXMLOutput();
        $this->assertEquals(self::XML_VALID_CONTENT, $generatedOutput);
    }
}