<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\Basic;

use Tiime\UniversalBusinessLanguage\DataType\Basic\Note;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class NoteTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_FULL_CONTENT = <<<XMLCONTENT
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cbc:Note>#PMD#Localized content</cbc:Note>
</Invoice>
XMLCONTENT;

    protected const XML_VALID_MINIMAL_CONTENT = <<<XMLCONTENT
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</Invoice>
XMLCONTENT;

    protected const XML_INVALID_MULTIPLE_NOTES = <<<XMLCONTENT
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cbc:Note>#PMD#Localized content</cbc:Note>
  <cbc:Note>#AAB#Localized content</cbc:Note>
  <cbc:Note>#ABL#Localized content</cbc:Note>
  <cbc:Note>#AAI#Localized content</cbc:Note>
</Invoice>
XMLCONTENT;


    public function testCanBeCreatedFromCompleteValid(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject = Note::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(Note::class, $ublObject);
        $this->assertEquals("#PMD#Localized content", $ublObject->getContent());
    }

    public function testCanBeCreatedFromMinimalContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_MINIMAL_CONTENT);
        $ublObject = Note::fromXML($this->xpath, $currentElement);
        $this->assertNull($ublObject);
    }

    /**
     * Note: this test should be valid
     * According to Oasis-open's UBL2.1 XSD and PPF specs, cbc:Note cardinality is 0..n
     * Peppol specs is the only one who states cbc:Note cardinality is 0..1
     */
    public function testCannotBeCreatedFromMultipleNotes(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MULTIPLE_NOTES);
        $this->expectException(\Exception::class);
        $ublObject = Note::fromXML($this->xpath, $currentElement);
    }

    public function testGenerateXml(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject = Note::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyRootDocument();
        $rootDestination->appendChild($ublObject->toXML($this->document));
        $generatedOutput = $this->formatXMLOutput();
        $this->assertEquals(self::XML_VALID_FULL_CONTENT, $generatedOutput);
    }
}