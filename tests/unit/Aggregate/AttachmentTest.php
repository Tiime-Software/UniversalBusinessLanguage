<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\Aggregate;

use Tiime\EN16931\DataType\BinaryObject;
use Tiime\EN16931\DataType\MimeCode;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\Attachment;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\ExternalReference;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class AttachmentTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_FULL_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:Attachment>
    <cbc:EmbeddedDocumentBinaryObject mimeCode="text/csv" filename="Hours-spent.csv">aHR0cHM6Ly90ZXN0LXZlZmEuZGlmaS5uby9wZXBwb2xiaXMvcG9hY2MvYmlsbGluZy8zLjAvYmlzLw==</cbc:EmbeddedDocumentBinaryObject>
    <cac:ExternalReference>
      <cbc:URI>http://www.example.com/index.html</cbc:URI>
    </cac:ExternalReference>
  </cac:Attachment>
</Invoice>
XML;

    protected const XML_VALID_MINIMAL_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:Attachment>
  </cac:Attachment>
</Invoice>
XML;

    protected const XML_VALID_NO_LINE = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</Invoice>
XML;

    protected const XML_INVALID_MANY_LINES = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:Attachment>
  </cac:Attachment>
  <cac:Attachment>
  </cac:Attachment>
</Invoice>
XML;

    public function testCanBeCreatedFromFullContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject      = Attachment::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(Attachment::class, $ublObject);
        $this->assertInstanceOf(BinaryObject::class, $ublObject->getEmbeddedDocumentBinaryObject());
        $this->assertEquals(MimeCode::tryFrom('text/csv'), $ublObject->getEmbeddedDocumentBinaryObject()->mimeCode);
        $this->assertEquals('Hours-spent.csv', $ublObject->getEmbeddedDocumentBinaryObject()->filename);
        $this->assertInstanceOf(ExternalReference::class, $ublObject->getExternalReference());
        $this->assertEquals('http://www.example.com/index.html', $ublObject->getExternalReference()->getUri());
    }

    public function testCanBeCreatedFromMinimalContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_MINIMAL_CONTENT);
        $ublObject      = Attachment::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(Attachment::class, $ublObject);
        $this->assertNull($ublObject->getEmbeddedDocumentBinaryObject());
        $this->assertNull($ublObject->getExternalReference());
    }

    public function testCanBeCreatedFromNoLine(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_NO_LINE);
        $ublObject      = Attachment::fromXML($this->xpath, $currentElement);
        $this->assertNull($ublObject);
    }

    public function testCannotBeCreatedFromManyLines(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MANY_LINES);
        Attachment::fromXML($this->xpath, $currentElement);
    }

    public function testGenerateXml(): void
    {
        $currentElement  = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject       = Attachment::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyRootDocument();
        $rootDestination->appendChild($ublObject->toXML($this->document));
        $generatedOutput = $this->formatXMLOutput();
        $this->assertStringEqualsStringIgnoringLineEndings(self::XML_VALID_FULL_CONTENT, $generatedOutput);
    }
}
