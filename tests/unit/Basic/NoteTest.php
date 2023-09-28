<?php

namespace TiimePDP\UniversalBusinessLanguage\Tests\unit\Basic;

use Tiime\UniversalBusinessLanguage\DataType\Basic\Note;
use TiimePDP\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class NoteTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_FULL_CONTENT = <<<XMLCONTENT
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
    <cbc:Note>#PMD#Localized content</cbc:Note>
</Invoice>
XMLCONTENT;

    public function testCanBeCreatedFromCompleteValid(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject = Note::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(Note::class, $ublObject);
        $this->assertEquals("#PMD#Localized content", $ublObject->getContent());
        // $this->assertInstanceOf(InvoiceNoteCode::class, $ublObject->getSubjectCode());
        // $this->assertEquals("PMD", $ublObject->getSubjectCode()->value);
    }
}